<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController{
    public static function login(Router $router) {
        $alertas = [];
        $auth = new Usuario;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();

            if(empty($alertas)) {
                // Comprobar que exista el usuario
                $usuario = Usuario::find('email', $auth->email);
                
                if($usuario) {
                    // Verificar el password
                    if ($usuario->comprobarPasswordAndVerificado($auth->password)) {
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //Redireccionamiento
                        if ($usuario->admin === '1') {
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header("Location: /admin");
                        } else {
                            header('Location: /cita');
                        } 

                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no Encontrado');
                }
            }


        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/login',[
            'alertas' => $alertas,
            'usuario' => $auth
        ]);
    }
    public static function logout(Router $router) {
        session_start();
        $_SESSION = [];
        header('Location: /');
    }    
    public static function olvide_password(Router $router) {
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();
            
            if (empty($alertas)) {
                # verificar que el email exista
                $usuario = Usuario::find('email',  $auth->email);
                if ($usuario && $usuario->confirmado === '1') {
                    //Generar un token
                    $usuario->crearToken();
                    $usuario->guardar();
                    //Enviar Email
                    $email= new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarInstrucciones();

                    //Alerta  de Exito
                    Usuario::setAlerta('exito', 'Restaure su contraseña vía E-Mail');
                } else {
                    Usuario::setAlerta('error', 'Usuario no confirmado o no existente');
                    
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }
    public static function recuperar_password(Router $router) {
        $alertas = [];
        $error = FALSE;
        $token = s($_GET['token']);
        //Buscar usuario por su token 
        $usuario = Usuario::find('token', $token);
        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token no válido');
            $error = TRUE;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Leer el nuevo password y guardarlo
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();
            if (empty($alertas)) {
                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();
                if ($resultado) {
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error'   => $error
        ]);
    }   
    public static function crear(Router $router) {
        $usuario = new Usuario;

        //Alertas vacias
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarCuenta();

            //Revisar que alertas este vacío
            if (empty($alertas)) {
                //Verificar que el usurario no este registrado
                $resultado = $usuario->validarUsuarioExistente();

                if ($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    //Hashear las password
                    $usuario->hashPassword();

                    //Generar un token único
                    $usuario->crearToken();

                    //Enviar el token
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarToken();

                    //Crear el usuario
                    $resultado = $usuario->guardar();
                    if ($resultado) {
                        header('Location: /mensaje');
                    }
                }
                
            }
        }
        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas

        ]);
    }

    public static function mensaje(Router $router) {
        $router->render('auth/mensaje', [

        ]);
    }

    public static function confirmar_cuenta(Router $router) {
        $alertas =[];
        $token = s($_GET['token']);
        $usuario = Usuario::find('token', $token);
        if (empty($usuario)) {
            //Mostrar mensaje error
            Usuario::setAlerta('error', 'Token no válido');

        } else{
            //Mostrar mensaje éxito
            $usuario->confirmado = '1';
            $usuario->token = null;
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta confirmada correctamente');

        }
        $alertas = Usuario::getAlertas();
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}
