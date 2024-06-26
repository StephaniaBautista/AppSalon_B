﻿# AppSalon_B
 #Documentacion App Salon
Una pagina que permite reservar citas para una estetica
- Se muestran los servicios disponibles
- Se muestran las fechas y horarios en las que se puede hacer la reservación
-Permite crear un usuario



###Classes
#####El código define dos clases en PHP: Email y PHPMailer. La clase Email se utiliza para encapsular la información del usuario y el token de confirmación o recuperación, mientras que la clase PHPMailer (una biblioteca externa) se utiliza para enviar correos electrónicos.

Clase Email:

Propiedades:
$email: Almacena la dirección de correo electrónico del usuario.
$nombre: Almacena el nombre del usuario.
$token: Almacena el token de confirmación o recuperación.
Métodos:
enviarToken(): Envía un correo electrónico de confirmación al usuario con un enlace para verificar su cuenta.
enviarInstrucciones(): Envía un correo electrónico de recuperación de contraseña al usuario con un enlace para restablecer su contraseña.
Función enviarToken():

Crea una instancia de la clase PHPMailer.
Configura las opciones de SMTP: host, autenticación, puerto, usuario y contraseña.
Establece la dirección de correo electrónico del remitente y el destinatario, así como el asunto del correo electrónico.
Crea el contenido HTML del correo electrónico, incluyendo un saludo personalizado, un enlace de confirmación y un mensaje final.
Configura el cuerpo del mensaje con el contenido HTML creado.
Envía el correo electrónico utilizando el método send().
Función enviarInstrucciones():

Crea una instancia de la clase PHPMailer.
Configura las opciones de SMTP (igual que en enviarToken()).
Establece la dirección de correo electrónico del remitente y el destinatario, así como el asunto del correo electrónico.
Crea el contenido HTML del correo electrónico, incluyendo un saludo personalizado, un enlace para restablecer la contraseña y un mensaje final.
Configura el cuerpo del mensaje con el contenido HTML creado.
Envía el correo electrónico utilizando el método send().

```<?php 

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email{
    public $email;
    public $nombre;
    public $token;
    public function __construct($email, $nombre, $token) {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarToken() {
        // Crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject = 'Confirma tu cuenta';

        //Set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->email .  "</strong> Has Creado tu cuenta en App Salón, solo debes confirmarla presionando el siguiente enlace</p>";
        $contenido .= "<p>Presiona aquí: <a href='". $_ENV['APP_URL'] ."/confirmar-cuenta?token=" . $this->token . "'>Confirmar Cuenta</a>";        
        $contenido .= "<p>Si tu no solicitaste este cambio, puedes ignorar el mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        //Enviar el mail
        $mail->send();
    }

    public function enviarInstrucciones() {
        // Crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];


        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject = 'Restaura tu contraseña';

        //Set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->email .  "</strong> Para restaurar tu cuenta dentro de App Salón,
        solo debes confirmarla presionando el siguiente enlace</p>";
        $contenido .= "<p>Presiona aquí: <a href='". $_ENV['APP_URL'] . "/recuperar-password?token=" . $this->token . "'>Recuperar contraseña</a>";        
        $contenido .= "<p>Si tu no solicitaste este cambio, puedes ignorar el mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        //Enviar el mail
        $mail->send();
    }
}
```
#Controllers
###Controllers
#####API Controller
El código define un controlador API llamado APIController con tres métodos: index(), guardar() y eliminar(). Estos métodos se encargan de manejar las solicitudes HTTP para la gestión de citas y servicios.

Método index():

Función: Recupera y devuelve una lista de todos los servicios disponibles.
Operación HTTP: GET
Ruta: /api/servicios (asumiendo)
Respuesta: JSON con la lista de servicios
Método guardar():

Función: Guarda una nueva cita y asocia los servicios seleccionados.
Operación HTTP: POST
Ruta: /api/citas (asumiendo)
Parámetros:
$_POST['fecha']: Fecha de la cita.
$_POST['hora']: Hora de la cita.
$_POST['cliente']: Nombre del cliente.
$_POST['servicios']: Lista de IDs de servicios separados por comas.
Respuesta: JSON con el resultado de la operación (éxito o error) y el ID de la cita guardada.
Método eliminar():

Función: Elimina una cita existente.
Operación HTTP: POST
Ruta: /api/citas/{id} (asumiendo)
Parámetro:
$_POST['id']: ID de la cita a eliminar.
Respuesta: Redirección a la página referida (usando header('Location:')).
```
<?php

namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

class APIController  {
    public static function index() {
        $servicios = Servicio::all();
        echo json_encode($servicios);
    }

    public static function guardar() {

        //Almacena la cita y devuleve el ID
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();

        $id = $resultado['id'];

        //Almacena la cita y el Servicio
        $idServicios = explode(",", $_POST['servicios']);

        //Almacena cada uno de los servicios con el id de la cita
        foreach ($idServicios as $idServicio) {
            $args = [
                'citaid' => $id,
                'servicioid' => $idServicio
            ];
            $citaServicio = new CitaServicio($args);
            $citaServicio->guardar();
        }
        echo json_encode(['resultado' => $resultado]);
    }

    public static function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cita = Cita::find_id($_POST['id']);
            $cita->eliminar();
            header('Location:' . $_SERVER['HTTP_REFERER']);
        }
    }
}
```
###Controllers
#####Admin Controller

```
<?php

namespace Controllers;
use Model\AdminCita;
use MVC\Router;

class AdminController {
    public static function index(Router $router) {
        session_start();
        isAdmin();
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $fechas = explode('-', $fecha);

        if (!checkdate($fechas[1], $fechas[2], $fechas[0])) {
            header('Location: /404');
        }

        //Consultar la base de datos
        $consulta = "SELECT citas.id, citas.hora, CONCAT( usuarios.nombre, ' ', usuarios.apellido) as cliente,";
        $consulta .= " usuarios.email, usuarios.telefono, servicios.nombre as servicio, servicios.precio  ";
        $consulta .= " FROM citas  ";
        $consulta .= " LEFT OUTER JOIN usuarios ";
        $consulta .= " ON citas.usuarioId=usuarios.id  ";
        $consulta .= " LEFT OUTER JOIN citas_servicios ";
        $consulta .= " ON citas_servicios.citaId=citas.id ";
        $consulta .= " LEFT OUTER JOIN servicios ";
        $consulta .= " ON servicios.id=citas_servicios.servicioId ";
        $consulta .= " WHERE fecha =  '$fecha' ";

        $citas = AdminCita::SQL($consulta);

        $router->render('admin/index',[
            'nombre'=> $_SESSION['nombre'],
            'citas' => $citas,
            'fecha' => $fecha
        ]);
    }
}
```

###Controllers
#####Cita Controller
El código define un controlador llamado CitaController con dos métodos: index() y index_ingles(). Ambos métodos se encargan de mostrar una vista relacionada a las citas, pero con la posibilidad de tener contenido en diferentes idiomas.

Funciones de los métodos:
index() y index_ingles():
Inician la sesión con session_start().
Llaman a la función isAuth() para verificar si el usuario está autenticado. Si no lo está, se redirige al usuario a la página de inicio o login.
Renderizan la vista correspondiente:
index() renderiza la vista cita/index. Se asume que esta vista contiene el contenido en el idioma por defecto.
index_ingles() renderiza la vista cita/index_ingles. Se asume que esta vista contiene el contenido en inglés.
Ambas vistas reciben las variables nombre (obtenido de la sesión) y id (también obtenido de la sesión) para su uso en la presentación.
Soporte multi-idioma:

Este código implementa un enfoque básico para el soporte multi-idioma. Se definen dos vistas separadas (cita/index y cita/index_ingles) para el contenido en diferentes idiomas.

```
?php

namespace Controllers;

use MVC\Router;

class CitaController {
    public static function index(Router $router) {
        session_start();
        isAuth();
        $router->render('cita/index', [
            'nombre'=> $_SESSION['nombre'],
            'id' => $_SESSION['id']
        ]);
    }
    public static function index_ingles(Router $router) {
        session_start();
        isAuth();
        $router->render('cita/index_ingles', [
            'nombre'=> $_SESSION['nombre'],
            'id' => $_SESSION['id']
        ]);
    }
}
```
###Controllers
#####Login Controller
El código define un controlador llamado LoginController que maneja diversas acciones relacionadas con el login, registro, recuperación de contraseña y confirmación de cuenta de usuarios.

Explicación de los métodos:

login(Router $router):
Procesa el formulario de login.
Valida los datos ingresados por el usuario.
Busca el usuario por su correo electrónico.
Verifica la contraseña ingresada.
Si el login es correcto, inicia la sesión y redirecciona al usuario según su rol (administrador o cliente).
Si hay errores de validación o la cuenta no existe, muestra los mensajes de alerta correspondientes en la vista de login.
logout(Router $router):
Cierra la sesión y redirecciona al usuario a la página principal.
olvide_password(Router $router):
Procesa el formulario para solicitar el restablecimiento de contraseña.
Valida el correo electrónico ingresado.
Busca el usuario por su correo electrónico.
Verifica si el usuario existe y está confirmado.
Si el usuario existe y está confirmado, genera un token de recuperación, lo guarda en la base de datos y envía un correo electrónico con instrucciones para restablecer la contraseña.
Muestra mensajes de alerta según el resultado de la operación.
recuperar_password(Router $router):
Procesa el formulario para restablecer la contraseña.
Valida el token proporcionado en la URL.
Busca el usuario por su token.
Si el token es válido, permite ingresar la nueva contraseña.
Valida la nueva contraseña ingresada.
Si la contraseña es válida, actualiza la contraseña del usuario, elimina el token y redirecciona al usuario al login.
Muestra mensajes de alerta según el resultado de la operación.
crear(Router $router):
Muestra el formulario de registro de usuario.
Procesa el formulario de registro.
Sincroniza los datos del formulario con el objeto usuario.
Valida los datos ingresados para el registro.
Verifica si el correo electrónico ingresado ya está registrado.
Si la validación es correcta, genera un token de confirmación, hashea la contraseña y guarda el usuario en la base de datos.
Envía un correo electrónico con el token de confirmación.
Si hay errores de validación, muestra los mensajes de alerta correspondientes en la vista de registro.
mensaje(Router $router):
Muestra una vista genérica que podría utilizarse para mostrar un mensaje de éxito después del registro.
confirmar_cuenta(Router $router):
Procesa la confirmación de la cuenta a través del token enviado por correo electrónico.
Valida el token proporcionado en la URL.
Busca el usuario por su token.
Si el token es válido, marca la cuenta como confirmada, elimina el token y guarda los cambios en la base de datos.
Muestra un mensaje de éxito o error según el resultado de la confirmación.

```
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
```
###Controllers
#####Servicio Controller
El código define un controlador llamado ServicioController que maneja el CRUD (Crear, Leer, Actualizar, Eliminar) de los servicios ofrecidos en la aplicación.

Explicación de los métodos:

index(Router $router):
Obtiene todos los servicios de la base de datos usando el método all() de la clase Servicio.
Verifica si el usuario tiene permisos de administrador con la función isAdmin().
Muestra la vista services/index con la lista de servicios y el nombre del usuario en sesión.
crear(Router $router):
Crea una nueva instancia del modelo Servicio.
Inicializa un arreglo vacío para almacenar las alertas de validación.
Si el método de solicitud es POST, sincroniza los datos del formulario con el objeto servicio y valida los datos con el método validar().
Si la validación es exitosa (no hay alertas), guarda el servicio en la base de datos y redirecciona a la lista de servicios.
Muestra la vista services/crear con el formulario para crear un servicio, el nombre del usuario en sesión y cualquier alerta de validación generada.
actualizar(Router $router):
Verifica si el ID del servicio proporcionado en la URL es numérico.
Busca el servicio por su ID utilizando el método find_id() de la clase Servicio.
Inicializa un arreglo vacío para almacenar las alertas de validación.
Si el método de solicitud es POST, sincroniza los datos del formulario con el objeto servicio existente y valida los datos con el método validar().
Si la validación es exitosa (no hay alertas), guarda los cambios del servicio en la base de datos y redirecciona a la lista de servicios.
Muestra la vista services/actualizar con el formulario para actualizar un servicio específico, el nombre del usuario en sesión y cualquier alerta de validación generada.
eliminar(Router $router):
Este método solo se ejecuta si el método de solicitud es POST.
Busca el servicio por su ID utilizando el método find_id() de la clase Servicio.
Elimina el servicio de la base de datos utilizando el método eliminar().
Redirecciona al usuario a la página referida utilizando el header Location: $_SERVER['HTTP_REFERER'].
```
<?php 

namespace Controllers;

use Model\Servicio;
use MVC\Router;

class ServicioController {
    public static function index(Router $router) {
        session_start();
        isAdmin();

        $servicios = Servicio::all();
        $router->render('services/index', [
            'nombre' => $_SESSION['nombre'],
            'servicios' => $servicios
        ]);
    }

    public static function crear(Router $router) {
        session_start();
        isAdmin();
        $servicio = new Servicio;
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $servicio->sincronizar($_POST);
            $alertas = $servicio->validar();

            if (empty($alertas)) {
                $servicio->guardar();
                header('Location: /servicios');
            }
            
        }
        $router->render('services/crear', [
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicio,
            'alertas' => $alertas
        ]);
    }

    public static function actualizar(Router $router) {
        session_start();
        isAdmin();
        $id = is_numeric($_GET['id']);
        if (!$id) return;
        $servicio = Servicio::find_id($_GET['id']);
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $servicio->sincronizar($_POST);
            $alertas = $servicio->validar();
            if(empty($alertas)){
                $servicio->guardar();
                header('Location: /servicios');
            }
        }
        $router->render('services/actualizar', [
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicio,
            'alertas' => $alertas
        ]);
    }

    public static function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cita = Servicio::find_id($_POST['id']);
            $cita->eliminar();
            header('Location:' . $_SERVER['HTTP_REFERER']);
        }
    }
}
```
#Includes
###Includes
####app
El código se encarga de configurar el entorno inicial para la aplicación.

Carga del Autoloader de Composer:

La línea require __DIR__ . '/../vendor/autoload.php'; incluye el archivo autoloader generado por Composer. Este archivo permite cargar automáticamente las librerías instaladas con Composer.
Carga de variables de entorno:

Se utiliza la librería Dotenv para cargar variables de entorno desde un archivo .env.
La línea $dotenv = Dotenv::createImmutable(__DIR__); crea una instancia de la clase Dotenv para el directorio actual.
La línea $dotenv->safeLoad(); carga las variables de entorno de forma segura, evitando exponer variables sensibles accidentalmente.
Carga de archivos adicionales:

Se incluyen dos archivos adicionales:
funciones.php: Probablemente contenga funciones reutilizables a lo largo de la aplicación.
database.php: Posiblemente se encarga de establecer la conexión a la base de datos.
Configuración de ActiveRecord:

Se está utilizando una librería como aramsy/activerecord para el manejo de la base de datos.
La línea ActiveRecord::setDB($db); establece la conexión a la base de datos en el ORM (Object-Relational Mapper) de ActiveRecord. La variable $db presumiblemente se define en el archivo database.php.
```
<?php 

// Conectarnos a la base de datos

use Dotenv\Dotenv;
use Model\ActiveRecord;
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
require 'funciones.php';
require 'database.php';

ActiveRecord::setDB($db);
```
###Includes
#####data base

El código establece la conexión a la base de datos MySQL utilizando la extensión mysqli de PHP.

- Conexión:

mysqli_connect(..., ...): Esta función se encarga de establecer la conexión a la base de datos MySQL. Recibe cuatro parámetros:

$_ENV['DB_HOST']: Obtiene el valor de la variable de entorno DB_HOST que debe contener la dirección del servidor de la base de datos.
$_ENV['DB_USER']: Obtiene el valor de la variable de entorno DB_USER que debe contener el nombre de usuario para la base de datos.
$_ENV['DB_PASS']: Obtiene el valor de la variable de entorno DB_PASS que debe contener la contraseña del usuario para la base de datos.
$_ENV['DB_NAME']: Obtiene el valor de la variable de entorno DB_NAME que debe contener el nombre de la base de datos a la que se desea conectar.
$db->set_charset('utf8'): Configura la codificación de caracteres de la conexión a UTF-8 para evitar problemas con caracteres especiales.

- Manejo de errores:

if (!$db) { ... }: Se verifica si la conexión a la base de datos se realizó con éxito.
Si la conexión falla (!$db), se imprime un mensaje de error con información de depuración:
mysqli_connect_errno(): Proporciona el código de error de la conexión.
mysqli_connect_error(): Proporciona una descripción textual del error de conexión.
La ejecución del script se detiene con la instrucción exit;.
En general, el código implementa una conexión segura a la base de datos utilizando variables de entorno para almacenar las credenciales y un manejo básico de errores.

```
<?php

$db = mysqli_connect(
    $_ENV['DB_HOST'],
    $_ENV['DB_USER'], 
    $_ENV['DB_PASS'], 
    $_ENV['DB_NAME']
);

$db->set_charset('utf8');

if (!$db) {
    echo "Error: No se pudo conectar a MySQL.";
    echo "errno de depuración: " . mysqli_connect_errno();
    echo "error de depuración: " . mysqli_connect_error();
    exit;
}
```
###Includes
#####funciones
El código define varias funciones útiles para una aplicación web:

- debuguear($variable):

Esta función imprime el contenido de una variable en un formato legible (pre-formatted) utilizando var_dump().
La función finaliza la ejecución del script con exit; después de mostrar la información.
Suele utilizarse para depurar código e inspeccionar el valor de variables durante el desarrollo.
- s($html):

Esta función escapa caracteres especiales en una cadena HTML utilizando htmlspecialchars().
Esto ayuda a prevenir la inyección de código HTML malicioso en la aplicación.
La función devuelve la cadena HTML sanitizada.

- ultimo(string $actual, string $proximo):

Esta función compara dos cadenas y devuelve true si la cadena actual es diferente a la próxima cadena.
En caso de ser iguales, devuelve false.
Se podría simplificar la lógica a return $actual !== $proximo;.

- isAuth():

Esta función verifica si el usuario está autenticado.
Comprueba si la variable de sesión $_SESSION['login'] está definida.
Si no está definida, redirecciona al usuario a la página principal (/).
Esta función no valida el valor de la variable de sesión, solo su existencia.

- isAdmin():

Esta función verifica si el usuario tiene permisos de administrador.
Comprueba si la variable de sesión $_SESSION['admin'] está definida.
Si no está definida, redirecciona al usuario a la página principal (/).
Similar a isAuth(), esta función asume que el valor de la variable de sesión indica el rol de administrador.
```
<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

function ultimo(string $actual, string $proximo) : bool {
    if ($actual !== $proximo) {
        return true;
    } else {
        return false;
    }
}

//Funcion que revisa que el usuario este autenticado
function isAuth() :void {
    if(!isset($_SESSION['login'])){
        header('Location: /');
    }
}

function isAdmin() : void{
    if (!isset($_SESSION['admin'])) {
        header('Location: /');
    }
}
```
#Models
###Models
#####Active Record
El código define una clase ActiveRecord que implementa una capa de abstracción de base de datos (Data Access Layer - DAL) para facilitar el manejo de registros en una aplicación web PHP.


- Conexión a la base de datos:
Permite establecer la conexión a la base de datos mediante el método setDB($database).
Utiliza la variable de clase $db para almacenar la conexión y ejecutar consultas.
- Manejo de alertas y mensajes:
Define un arreglo $alertas para almacenar mensajes de alerta y errores.
Proporciona métodos setAlerta($tipo, $mensaje) para agregar alertas y getAlertas() para obtenerlas.
- Validación de datos:
Define un método abstracto validar() que debe ser implementado por las clases que heredan de ActiveRecord para realizar validaciones específicas de cada modelo.
- Consultas SQL:
Implementa el método consultarSQL($query) para ejecutar consultas SQL arbitrarias y obtener los resultados como un array de objetos.
- Creación de objetos a partir de registros:
El método crearObjeto($registro) crea una instancia de la clase correspondiente a partir de un registro de la base de datos.
- Sanitización de datos:
Define métodos atributos(), sanitizarAtributos() para obtener y sanitizar los atributos de un objeto antes de guardarlos en la base de datos.
- Sincronización de datos:
El método sincronizar($args=[]) sincroniza los datos de un objeto con los valores proporcionados en un array.
- Métodos CRUD (Crear, Leer, Actualizar, Eliminar):
Implementa métodos CRUD genéricos para:
guardar(): Guarda un nuevo registro o actualiza uno existente.
all(): Obtiene todos los registros de la tabla.
find_id($id): Busca un registro por su ID.
find($cosa, $valor): Busca un registro por un campo y un valor específico.
SQL($query): Ejecuta una consulta SQL arbitraria.
get($limite): Obtiene un número limitado de registros.
crear(): Crea un nuevo registro en la base de datos.
actualizar(): Actualiza un registro existente en la base de datos.
eliminar(): Elimina un registro de la base de datos.
```
<?php
namespace Model;
class ActiveRecord {

    // Base DE DATOS
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];

    // Alertas y Mensajes
    protected static $alertas = [];
    
    // Definir la conexión a la BD - includes/database.php
    public static function setDB($database) {
        self::$db = $database;
    }

    public static function setAlerta($tipo, $mensaje) {
        static::$alertas[$tipo][] = $mensaje;
    }

    // Validación
    public static function getAlertas() {
        return static::$alertas;
    }

    public function validar() {
        static::$alertas = [];
        return static::$alertas;
    }

    // Consulta SQL para crear un objeto en Memoria
    public static function consultarSQL($query) {
        // Consultar la base de datos
        $resultado = self::$db->query($query);

        // Iterar los resultados
        $array = [];
        while($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }

        // liberar la memoria
        $resultado->free();

        // retornar los resultados
        return $array;
    }

    // Crea el objeto en memoria que es igual al de la BD
    protected static function crearObjeto($registro) {
        $objeto = new static;

        foreach($registro as $key => $value ) {
            if(property_exists( $objeto, $key  )) {
                $objeto->$key = $value;
            }
        }

        return $objeto;
    }

    // Identificar y unir los atributos de la BD
    public function atributos() {
        $atributos = [];
        foreach(static::$columnasDB as $columna) {
            if($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    // Sanitizar los datos antes de guardarlos en la BD
    public function sanitizarAtributos() {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach($atributos as $key => $value ) {
            $sanitizado[$key] = self::$db->escape_string($value);
        }
        return $sanitizado;
    }

    // Sincroniza BD con Objetos en memoria
    public function sincronizar($args=[]) { 
        foreach($args as $key => $value) {
          if(property_exists($this, $key) && !is_null($value)) {
            $this->$key = $value;
          }
        }
    }

    // Registros - CRUD
    public function guardar() {
        $resultado = '';
        if(!is_null($this->id)) {
            // actualizar
            $resultado = $this->actualizar();
        } else {
            // Creando un nuevo registro
            $resultado = $this->crear();
        }
        return $resultado;
    }

    // Todos los registros
    public static function all() {
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Busca un registro por su id
    public static function find_id($id) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE id = $id";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }

    //Busca un registro en general
    public static function find($cosa, $valor) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE $cosa = '$valor'";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }
    //Consulta Plana de SQL (Utiliza cuando los metodos del moldelo no son suficiente)
    public static function SQL($query) {
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Obtener Registros con cierta cantidad
    public static function get($limite) {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT $limite";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }

    // crea un nuevo registro
    public function crear() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Insertar en la base de datos
        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES ('";
        $query .= join("', '", array_values($atributos));
        $query .= " ') ";

        //return json_encode(['query' => $query]);

        // Resultado de la consulta
        $resultado = self::$db->query($query);
        return [
           'resultado' =>  $resultado,
           'id' => self::$db->insert_id
        ];
    }

    // Actualizar el registro
    public function actualizar() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Iterar para ir agregando cada campo de la BD
        $valores = [];
        foreach($atributos as $key => $value) {
            $valores[] = "{$key}='{$value}'";
        }

        // Consulta SQL
        $query = "UPDATE " . static::$tabla ." SET ";
        $query .=  join(', ', $valores );
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1 "; 

        // Actualizar BD
        $resultado = self::$db->query($query);
        return $resultado;
    }

    // Eliminar un Registro por su ID
    public function eliminar() {
        $query = "DELETE FROM "  . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);
        return $resultado;
    }
}
```

###Models
#####Admin Cita

El código define una clase AdminCita que hereda de la clase ActiveRecord analizada anteriormente. Esta clase representa el modelo de datos para las citas de servicio administrativas.

- Heredando funcionalidades:

La clase AdminCita hereda todos los métodos y propiedades de la clase padre ActiveRecord.
Esto le proporciona funcionalidades como conexión a la base de datos, consultas CRUD, manejo de alertas y sanitización de datos.
Atributos y Propiedades:

La clase define la tabla asociada (citas_servicios) y las columnas que maneja (id, hora, cliente, email, telefono, servicio, precio) a través de las propiedades estáticas $tabla y $columnasDB.
Se definen propiedades públicas para cada columna de la tabla, permitiendo acceso y modificación a sus valores.
El constructor (__construct($args=[])) inicializa las propiedades del objeto con valores por defecto o los proporcionados en el argumento $args.


```<?php

namespace Model;

class AdminCita extends ActiveRecord {
    protected static $tabla = 'citas_servicios';
    protected static $columnasDB = ['id', 'hora', 'cliente', 'email', 'telefono', 'servicio', 'precio'];

    public $id;
    public $hora;
    public $cliente;
    public $email;
    public $telefono;
    public $servicio;
    public $precio;

    public function __construct($args=[]){
        $this->id = $args['id'] ?? null;
        $this->hora = $args['hora'] ?? '';
        $this->cliente = $args['cliente'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->servicio = $args['servicio'] ?? '';
        $this->precio = $args['precio'] ?? '';
    }
}
```

###Models
#####Cita
El código define una clase Cita que hereda de la clase ActiveRecord analizada anteriormente. Esta clase representa el modelo de datos para las citas de usuarios.

La clase Cita hereda todas las funcionalidades de ActiveRecord al igual que AdminCita.
Define la tabla asociada (citas) y las columnas que maneja (id, fecha, hora, usuarioid).
Posee propiedades públicas para cada columna y un constructor para inicializarlas.
Diferencias con AdminCita:

La tabla asociada y las columnas que maneja son diferentes, reflejando la información específica de citas de usuarios.
No incluye propiedades para datos como cliente, email, telefono, y precio que sí están presentes en AdminCita.
En general, la clase Cita es un modelo específico para citas de usuarios que hereda la funcionalidad de ActiveRecord para interactuar con la base de datos.
```
<?php

namespace Model;

class Cita extends ActiveRecord {
    //Base de datos 
    protected static $tabla = 'citas';
    protected static $columnasDB = ['id', 'fecha', 'hora', 'usuarioid'];

    public $id;
    public $fecha;
    public $hora;
    public $usuarioid;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->fecha = $args['fecha'] ?? '';
        $this->hora = $args['hora'] ?? '';
        $this->usuarioid = $args['usuarioid'] ?? '';
    }
}
```
###Model
#####Cita Servicio
El código proporcionado define una clase CitaServicio que hereda de la clase ActiveRecord analizada anteriormente. Esta clase representa la relación entre citas y servicios, funcionando como una tabla puente en la base de datos.

Relación muchos a muchos:

La tabla citas_servicios típicamente se utiliza para modelar una relación de muchos a muchos entre citas y servicios.
Una cita puede tener varios servicios asociados y un servicio puede estar en varias citas.
La clase CitaServicio representa un registro en esta tabla de relación.
Atributos y Propiedades:

La clase define la tabla (citas_servicios) y sus columnas (id, citaid, servicioid).
Posee propiedades públicas para cada columna (id, citaid, servicioid).
El constructor (__construct($args = [])) inicializa las propiedades del objeto con valores por defecto o los proporcionados en el argumento $args.
```
<?php

namespace Model;

class CitaServicio extends ActiveRecord{
    protected static $tabla = 'citas_servicios';
    protected static $columnasDB = ['id', 'citaid', 'servicioid'];

    public $id;
    public $citaid;
    public $servicioid;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->citaid = $args['citaid'] ?? '';
        $this->servicioid = $args['servicioid'] ?? '';
    }
}
```
###Models
#####Servicio
El código proporcionado define una clase Servicio que hereda de la clase ActiveRecord analizada anteriormente. Esta clase representa el modelo de datos para los servicios ofrecidos por la aplicación.

- Heredando funcionalidades:

La clase Servicio hereda todos los métodos y propiedades de la clase padre ActiveRecord.
Esto le proporciona funcionalidades como conexión a la base de datos, consultas CRUD, manejo de alertas y sanitización de datos.
Atributos y Propiedades:

La clase define la tabla asociada (servicios) y las columnas que maneja (id, nombre, precio) a través de las propiedades estáticas $tabla y $columnasDB.
Se definen propiedades públicas para cada columna de la tabla, permitiendo acceso y modificación a sus valores.
El constructor (__construct($args=[])) inicializa las propiedades del objeto con valores por defecto o los proporcionados en el argumento $args.
Método validar():

La clase implementa un método validar() que verifica si los atributos nombre y precio están presentes y son válidos.
Si el nombre está vacío o es un número, se agrega un mensaje de error al array self::$alertas.
Si el precio está vacío, se agrega otro mensaje de error al array self::$alertas.
El método devuelve el array self::$alertas con los mensajes de error encontrados.
```
<?php

namespace Model;

class Servicio extends ActiveRecord {
    //Bae de datos
    protected static $tabla = 'servicios';
    protected static $columnasDB =  ['id', 'nombre', 'precio'];

    public $id;
    public $nombre;
    public $precio;

    public function __construct($args = []){
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->precio = $args['precio'] ?? '';
    }

    public function validar() {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre del servicio es obligatorio';
        }
        if (!$this->precio) {
            self::$alertas['error'][] = 'El precio del servicio es obligatorio';
        }

        if (is_numeric($this->nombre)) {
            self::$alertas['error'][] = 'El precio del servicio debe ser un número';
        }
        return self::$alertas;
    }
}
```

###Models
#####Usuario
El código proporcionado define una clase Usuario que hereda de la clase ActiveRecord analizada anteriormente. Esta clase representa el modelo de datos para los usuarios del sistema.

Heredando funcionalidades:

La clase Usuario hereda todos los métodos y propiedades de la clase padre ActiveRecord.
Esto le proporciona funcionalidades como conexión a la base de datos, consultas CRUD, manejo de alertas y sanitización de datos.
Atributos y Propiedades:

La clase define la tabla asociada (usuarios) y las columnas que maneja (id, nombre, apellido, email, password, telefono, admin, confirmado, token) a través de las propiedades estáticas $tabla y $columnasDB.
Se definen propiedades públicas para cada columna de la tabla, permitiendo acceso y modificación a sus valores.
El constructor (__construct($args=[])) inicializa las propiedades del objeto con valores por defecto o los proporcionados en el argumento $args.
Métodos específicos de la clase Usuario:

validarCuenta():
Este método valida los datos de un usuario al crear una nueva cuenta.
Comprueba que el nombre, apellido, email, password y telefono sean válidos.
Agrega mensajes de error al array self::$alertas si encuentra datos inválidos.
validarLogin():
Este método valida los datos de un usuario al iniciar sesión.
Comprueba que el email y el password sean válidos.
Agrega mensajes de error al array self::$alertas si encuentra datos inválidos.
validarUsuarioExistente():
Este método verifica si un usuario con un email específico ya está registrado en la base de datos.
Realiza una consulta SQL para buscar el usuario por email.
Si el usuario existe, agrega un mensaje de error al array self::$alertas.
validarEmail():
Este método valida el formato del email de un usuario.
Comprueba que el email no esté vacío y que tenga un formato válido usando filter_var().
Agrega mensajes de error al array self::$alertas si el email es inválido.
validarPassword():
Este método valida la longitud del password de un usuario.
Comprueba que el password no esté vacío y que tenga una longitud mayor o igual a 8 caracteres.
Agrega mensajes de error al array self::$alertas si el password es inválido.
- hashPassword():
Este método encripta el password de un usuario utilizando la función - - -- - - password_hash().
Almacena el password encriptado en la propiedad $this->password.
- crearToken():
Este método genera un token único para un usuario utilizando la función - - - uniqid().
Almacena el token generado en la propiedad $this->token.
comprobarPasswordAndVerificado($password):
Este método verifica si el password proporcionado coincide con el password del usuario y si el usuario está verificado.
Utiliza la función password_verify() para comparar las contraseñas.
Si el password coincide y el usuario está verificado, devuelve true.
En caso contrario, agrega un mensaje de error al array self::$alertas.
```
<?php
namespace Model;
class Usuario extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 'admin', 'confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? '0';
        $this->confirmado = $args['confirmado'] ?? '0';
        $this->token = $args['token'] ?? '';
    }

    // Metodo para las alertas de la creación de la cuenta
    public function validarCuenta() {
        if(strlen($this->nombre) < 3){
            self::$alertas['error'][] = 'El nombre debe ser valido';
        }
        if(strlen($this->apellido) < 3){
            self::$alertas['error'][] = 'El apellido es obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El E-mail es obligatorio';
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = "El E-mail no es válido";
        }
        if(strlen($this->password) < 8){
            self::$alertas['error'][] = 'El password es obligatorio que sea mayor a 8 carácteres';
        }
        if(strlen($this->telefono) !== 10){
            self::$alertas['error'][] = 'El telefono debe ser de 11 digitos';
        }
        return self::$alertas;
    }

    public function validarLogin() {
        if (!$this->email) {
            self::$alertas['error'][] = 'El E-Mail es incorrecto';

        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = "El E-mail no es válido";
        }
        if(!$this->password){
            self::$alertas['error'][] = 'La contraseña es requerida o debe ser valida';
        }
        return self::$alertas;
    }

    public function validarUsuarioExistente() {
        $query = " SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";
        $resultado = self::$db->query($query);

        if ($resultado->num_rows) {
            self::$alertas['error'][] = 'Este Usuario ya esta está registrado';

        }
        return $resultado;
    }

    public function validarEmail() {
        if (!$this->email) {
            self::$alertas['error'][] = 'El E-Mail es incorrecto';

        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = "El E-mail no es válido";
        }

        return self::$alertas;
    }

    public function validarPassword() {
        if(strlen($this->password) < 8){
            self::$alertas['error'][] = 'El password es obligatorio que sea mayor a 8 carácteres';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'El password es obligatorio';

        }
        return self::$alertas;
    }

    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }
    public function crearToken() {
        $this->token = uniqid();
    }
    public function comprobarPasswordAndVerificado($password)
    {
        $resultado = password_verify($password, $this->password);
        if (!$resultado || !$this->confirmado) {
            self::$alertas['error'][] = 'Password incorrecta o usuario no verificado';
        } else {
            return true;
        }
    }
}
```
#Public
#####index
Enrutamiento de la aplicación web:

Utiliza la librería MVC\Router para mapear URLs a métodos en controladores.
Define rutas para:
Inicio de sesión y cierre de sesión (/, /logout).
Recuperación de contraseña (/olvide-password, /recuperar-password).
Creación de cuenta (/crear-cuenta).
Confirmación de cuenta (/confirmar-cuenta, /mensaje).
Área privada (citas y administración) (/cita, /admin).
API de citas (/api/servicios, /api/citas, /api/eliminar).
Servicios (/servicios, /servicios/crear, /servicios/actualizar, /servicios/eliminar).
Inicio en inglés (/inicio-ingles).
Comprueba y valida las rutas, asignándoles las funciones del controlador correspondiente.
```
<?php 

require_once __DIR__ . '/../includes/app.php';

use Controllers\APIController;
use Controllers\CitaController;
use Controllers\LoginController;
use Controllers\AdminController;
use Controllers\ServicioController;
use MVC\Router;

$router = new Router();

//Iniciar sesion
$router->get('/', [LoginController::class, 'login']);
$router->post('/', [LoginController::class, 'login']);

//Cerrar sesión
$router->get('/logout', [LoginController::class, 'logout']);

//Recuperar Password
$router->get('/olvide-password', [LoginController::class, 'olvide_password']);
$router->post('/olvide-password', [LoginController::class, 'olvide_password']);
$router->get('/recuperar-password', [LoginController::class, 'recuperar_password']);
$router->post('/recuperar-password', [LoginController::class, 'recuperar_password']);

//Crear cuenta
$router->get('/crear-cuenta', [LoginController::class, 'crear']);
$router->post('/crear-cuenta', [LoginController::class, 'crear']);

//Confirmar cuenta
$router->get('/confirmar-cuenta', [LoginController::class, 'confirmar_cuenta']);
$router->get('/mensaje', [LoginController::class, 'mensaje']);

// AREA PRIVADA
$router->get('/cita', [CitaController::class, 'index']);
$router->get('/admin', [AdminController::class, 'index']);

//API de Citas
$router->get('/api/servicios', [APIController::class, 'index']);
$router->post('/api/citas', [APIController::class, 'guardar']);
$router->post('/api/eliminar', [APIController::class, 'eliminar']);

//API de Servicios
$router->get('/servicios', [ServicioController::class, 'index']);

$router->get('/servicios/crear', [ServicioController::class, 'crear']);
$router->post('/servicios/crear', [ServicioController::class, 'crear']);

$router->get('/servicios/actualizar', [ServicioController::class, 'actualizar']);
$router->post('/servicios/actualizar', [ServicioController::class, 'actualizar'])
;
$router->post('/servicios/eliminar', [ServicioController::class, 'eliminar']);

//Inicio Ingles
$router->get('/inicio-ingles', [CitaController::class, 'index_ingles']);
$router->get('/admin', [AdminController::class, 'index']);


// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
```

#SRC
#####App
- Variables y constantes:

paso: Almacena el paso actual del proceso de reserva (1, 2 o 3).
pasoIncial: Constante que define el paso inicial (1).
pasoFinal: Constante que define el paso final (3).
cita: Objeto que almacena la información de la cita (id, nombre, fecha, hora, servicios).

-  Funciones principales:

iniciarApp():
Ejecuta las funciones para inicializar la aplicación:
mostrarSeccion(): Muestra la sección correspondiente al paso actual.
tabs(): Agrega funcionalidad a las pestañas para cambiar de paso.
botonesPaginador(): Habilita/deshabilita los botones de "Siguiente" y "Anterior".
paginaSiguiente(): Maneja el clic en el botón "Siguiente".
paginaAnterior(): Maneja el clic en el botón "Anterior".
consultarAPI(): Consulta los servicios disponibles desde la API.
idCliente(): Obtiene el ID del cliente del formulario.
nombreCliente(): Obtiene el nombre del cliente del formulario.
selccionarFecha(): Valida e ingresa la fecha de la cita en el objeto cita.
seleccionarHora(): Valida e ingresa la hora de la cita en el objeto cita.
mostrarResumen(): Muestra el resumen de la cita en la última sección.
eliminarClase(nombreClase):
Elimina la clase nombreClase del elemento seleccionado.
mostrarSeccion():
Muestra la sección correspondiente al paso actual y oculta las demás.
Agrega la clase actual a la pestaña del paso actual.
tabs():
Agrega un evento click a cada pestaña para cambiar de paso.
botonesPaginador():
Habilita/deshabilita los botones de "Siguiente" y "Anterior" según el paso actual.
Muestra un mensaje de alerta si se intenta avanzar más allá del paso final.
paginaSiguiente():
Incrementa el valor de la variable paso.
Llama a mostrarSeccion() para actualizar la vista.
Llama a botonesPaginador() para actualizar el estado de los botones.
paginaAnterior():
Decrementa el valor de la variable paso.
Llama a mostrarSeccion() para actualizar la vista.
Llama a botonesPaginador() para actualizar el estado de los botones.
consultarAPI():
Realiza una petición GET a la API /api/servicios para obtener los servicios disponibles.
Muestra los servicios en la sección correspondiente.
mostrarServicios(servicios):
Recorre la lista de servicios y crea elementos HTML para cada uno.
Agrega los elementos HTML a la sección correspondiente.
seleccionarServicio(servicio):
Agrega o elimina el servicio seleccionado del objeto cita.
Agrega o elimina la clase `
```
let paso = 1;
const pasoIncial = 1;
const pasoFinal = 3;

const cita = {
  id: "",
  nombre: "",
  fecha: "",
  hora: "",
  servicios: [],
};

document.addEventListener("DOMContentLoaded", function () {
  iniciarApp();
});

function iniciarApp() {
  mostrarSeccion(); //Muestra y oculta las secciones
  tabs(); //Cambia la sección cuando se muestren dos tabs
  botonesPaginador(); // Agrega o quita los botones del paginador
  paginaSiguiente();
  paginaAnterior();

  consultarAPI(); //Consulta el Json para los datos de la DB

  idCliente();
  nombreCliente(); //Trae el nombre del cliente al objeto de cita.
  selccionarFecha(); //Añade la fecha de la cita al objeto de cita.
  seleccionarHora(); //Añande la hora de la cita al objeto de cita.

  mostrarResumen(); //Muestra el resumen de la cita
}

function eliminarClase(nombreClase) {
  const eliminarAlgo = document.querySelector(`.${nombreClase}`);
  if (eliminarAlgo) {
    eliminarAlgo.classList.remove(`${nombreClase}`);
  }
}

function mostrarSeccion() {
  //Ocultar la seccion que tenga la clase de mostrar
  eliminarClase("mostrar");

  //Seleccionar la sección con el paso
  const pasoSelector = `#paso-${paso}`;
  const seccion = document.querySelector(pasoSelector);
  seccion.classList.add("mostrar");

  //Cambiar el color cuando el tab no es donde se está
  eliminarClase("actual");

  //Cambiar el color del tab
  const tab = document.querySelector(`[data-paso="${paso}"]`);
  tab.classList.add("actual");
}

function tabs() {
  const botones = document.querySelectorAll(".tabs button");
  botones.forEach((boton) => {
    boton.addEventListener("click", function (e) {
      paso = parseInt(e.target.dataset.paso);
      mostrarSeccion();
      botonesPaginador();
    });
  });
}

function botonesPaginador() {
  const paginaSiguiente = document.querySelector("#siguiente");
  const paginaAnterior = document.querySelector("#anterior");

  if (paso === 1) {
    paginaAnterior.classList.add("ocultar");
    paginaSiguiente.classList.remove("ocultar");
  } else if (paso === 3) {
    paginaAnterior.classList.remove("ocultar");
    paginaSiguiente.classList.add("ocultar");
    mostrarResumen();
  } else {
    paginaAnterior.classList.remove("ocultar");
    paginaSiguiente.classList.remove("ocultar");
  }

  mostrarSeccion();
}

function paginaSiguiente() {
  const paginaSiguiente = document.querySelector("#siguiente");
  paginaSiguiente.addEventListener("click", function () {
    if (paso >= pasoFinal) return;
    paso++;
    botonesPaginador();
  });
}

function paginaAnterior() {
  const paginaAnterior = document.querySelector("#anterior");
  paginaAnterior.addEventListener("click", function () {
    if (paso <= pasoIncial) return;
    paso--;
    botonesPaginador();
  });
}

async function consultarAPI() {
  try {
    const url = `${location.origin}/api/servicios`;
    const resultado = await fetch(url);
    const servicios = await resultado.json();
    mostrarServicios(servicios);
  } catch (error) {}
}

function mostrarServicios(servicios) {
  servicios.forEach((servicio) => {
    const { id, nombre, precio } = servicio;

    //Crear el nombre del servicio
    const nombreServicio = document.createElement("P");
    nombreServicio.classList.add("nombre-servicio");
    nombreServicio.textContent = nombre;

    //Crear el precio del servicio
    const precioServicio = document.createElement("P");
    precioServicio.classList.add("precio-servicio");
    precioServicio.textContent = `$${precio}`;

    //Crear el contenedor del servicio
    const divServicio = document.createElement("DIV");
    divServicio.classList.add("servicio");
    divServicio.dataset.idServicio = id;
    divServicio.onclick = function () {
      seleccionarServicio(servicio);
    };

    divServicio.appendChild(nombreServicio);
    divServicio.appendChild(precioServicio);

    document.querySelector("#servicios").appendChild(divServicio);
  });
}

function seleccionarServicio(servicio) {
  const { id } = servicio;
  const { servicios } = cita;

  //Indentifica al elemento que se le da click
  const servicioDiv = document.querySelector(`[data-id-servicio="${id}"]`);

  //Comprobar si un servicio ya fue agregado
  if (servicios.some((agregado) => agregado.id === id)) {
    //Eliminar el objeto
    cita.servicios = servicios.filter((agregado) => agregado.id !== id);
    eliminarClase("seleccionado");
  } else {
    //Agregar el objeto
    cita.servicios = [...servicios, servicio];
    servicioDiv.classList.add("seleccionado");
  }
}

function idCliente() {
  cita.id = document.querySelector("#id").value;
}

function nombreCliente() {
  cita.nombre = document.querySelector("#nombre").value;
}

function selccionarFecha() {
  const inputfecha = document.querySelector("#fecha");
  inputfecha.addEventListener("input", function (e) {
    const dia = new Date(e.target.value).getUTCDay();
    if ([6, 0].includes(dia)) {
      e.target.value = "";
      mostrarAlerta("Fines de semana no permitidos", "error", ".formulario");
    } else {
      cita.fecha = e.target.value;
    }
  });
}

function seleccionarHora() {
  const inputhora = document.querySelector("#hora");
  inputhora.addEventListener("input", function (e) {
    const horaCita = e.target.value;
    const hora = horaCita.split(":")[0];
    if (hora < 10 || hora > 18) {
      e.target.value = "";
      mostrarAlerta("Hora no valida", "error", ".formulario");
    } else {
      cita.hora = e.target.value;
    }
  });
}

function mostrarAlerta(mensaje, tipo, lugar, desaparece = true) {
  //Evitar que salga muchas veces la alerta
  const alertaPrevia = document.querySelector(".alerta");
  if (alertaPrevia) {
    alertaPrevia.remove();
  }

  //Crea el div para la alerta y elige el tipo de alerta
  const alerta = document.createElement("DIV");
  alerta.classList.add("alerta");
  alerta.classList.add(tipo);

  //Crea el parrafo e inserta el mensaje en el div de alerta
  const mensajeAlerta = document.createElement("P");
  mensajeAlerta.textContent = mensaje;
  alerta.appendChild(mensajeAlerta);

  //Agrega la alerta en el formulario
  const formulario = document.querySelector(lugar);
  formulario.appendChild(alerta);

  //Evita que la alerta se mantenga más de 3 segundos
  if (desaparece) {
    setTimeout(() => {
      alerta.remove();
    }, 3000);
  }
}

function mostrarResumen() {
  const resumen = document.querySelector(".contenido-resumen");

  //Limpiar el contenido del resumuen
  while (resumen.firstChild) {
    resumen.removeChild(resumen.firstChild);
  }
  if (Object.values(cita).includes("") || cita.servicios.length === 0) {
    mostrarAlerta(
      "Faltan  datos por completar",
      "error",
      ".contenido-resumen",
      false
    );
    return;
  }

  //Formatear el div de resumen
  const { nombre, fecha, hora, servicios } = cita;

  const nombreCliente = document.createElement("P");
  nombreCliente.innerHTML = `<span>Nombre: </span>${nombre}`;

  //Formatear la fecha en español
  const fechaObj = new Date(fecha);
  const mes = fechaObj.getMonth();
  const dia = fechaObj.getDate() + 2;
  const year = fechaObj.getFullYear();

  const fechaUTC = new Date(Date.UTC(year, mes, dia));
  const opcionesFecha = {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  };
  const fechaFormateada = fechaUTC.toLocaleDateString("es-MX", opcionesFecha);

  const fechaCita = document.createElement("P");
  fechaCita.innerHTML = `<span>Fecha: </span>${fechaFormateada}`;

  const horaCita = document.createElement("P");
  horaCita.innerHTML = `<span>Hora: </span>${hora} horas`;

  // Boton para Crear una cita
  const botonReservar = document.createElement("BUTTON");
  botonReservar.classList.add("btn-azul");
  botonReservar.textContent = "Reservar Cita";
  botonReservar.onclick = reservarCita;

  //Heading para servicios en resumen
  const headingServicios = document.createElement("H3");
  headingServicios.textContent = "Resumen de servicios";

  resumen.appendChild(headingServicios);

  //Heading para servicios en resumen
  const headingUsuario = document.createElement("H3");
  headingUsuario.textContent = "Resumen de los datos de la cita";

  resumen.appendChild(headingUsuario);

  resumen.appendChild(nombreCliente);
  resumen.appendChild(fechaCita);
  resumen.appendChild(horaCita);

  //Iterando en los servicios
  servicios.forEach((servicio) => {
    const { id, precio, nombre } = servicio;
    const contenedorServicio = document.createElement("DIV");
    contenedorServicio.classList.add("contenedor-servicio");

    const textoServicio = document.createElement("P");
    textoServicio.textContent = nombre;

    const precioServicio = document.createElement("P");
    precioServicio.innerHTML = `<span>Precio: </span> $${precio}`;

    contenedorServicio.appendChild(textoServicio);
    contenedorServicio.appendChild(precioServicio);

    resumen.appendChild(contenedorServicio);
  });

  resumen.appendChild(botonReservar);
}

async function reservarCita() {
  const { nombre, fecha, hora, servicios, id } = cita;

  const idServicios = servicios.map((servicio) => servicio.id); //A diferencia de For Each, Map lo hace solo por coincidencias, servicio.id selecciona solo los id

  const datos = new FormData();
  datos.append("fecha", fecha);
  datos.append("hora", hora);
  datos.append("usuarioid", id);
  datos.append("servicios", idServicios);

  try {
    //Peticion hacia la API
    const url = `${location.origin}/api/citas`;
    const respuesta = await fetch(url, {
      method: "POST",
      body: datos,
    });

    const resultado = await respuesta.json();
    if (resultado.resultado.resultado) {
      Swal.fire({
        icon: "success",
        title: "Cita Creada",
        text: "Tu cita fue creada correctamente",
        button: "OK",
      }).then(() => {
        setTimeout(() => {
          window.location.reload();
        }, 3000);
      });
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Algo no salio bien!",
    });
  }
}
```

###src
####Buscador
- Carga del documento y función iniciarApp():

document.addEventListener("DOMContentLoaded", function () {...});:
Este código espera a que la página HTML haya terminado de cargarse completamente.
Una vez cargada, ejecuta la función iniciarApp() para inicializar las funcionalidades del script.
function iniciarApp() { ... }:
Esta función contiene las instrucciones principales para el funcionamiento del script.
En este caso, llama a dos funciones:
seleccionarFecha(): Maneja la selección de una fecha.
confirmDelete(): Maneja la confirmación de una eliminación.

- Selección de fecha (seleccionarFecha()):

const fechaInput = document.querySelector("#fecha");:
Obtiene el elemento HTML con el ID "fecha", que se presume es un campo de entrada de fecha.
fechaInput.addEventListener("input", function (e) { ... });:
Agrega un detector de eventos al campo de fecha.
Este evento se dispara cada vez que el usuario introduce un valor de fecha.
const fechaSeleccionada = e.target.value;:
Dentro del detector de eventos, se obtiene el valor de la fecha seleccionada.
window.location = \?fecha=${fechaSeleccionada}`;`:
Redirecciona al usuario a la misma página web, pero con un parámetro de consulta fecha que contiene la fecha seleccionada.

- Confirmación de eliminación (confirmDelete()):

function confirmDelete(event) { ... }:
Esta función recibe un objeto de evento como parámetro (probablemente proveniente del clic en un botón de envío).
event.preventDefault();:
Previene el comportamiento predeterminado del envío del formulario.
Swal.fire({ ... });:
Utiliza la biblioteca Swal.fire para mostrar una ventana emergente de confirmación.
La ventana emergente pregunta al usuario "¿Estás seguro de que deseas eliminar?" con un icono de advertencia y dos botones: "Sí, eliminar" y "Cancelar".
if (result.isConfirmed) { ... }:
Si el usuario confirma la eliminación, la función envía el formulario con el ID "formEliminar".
```
document.addEventListener("DOMContentLoaded", function () {
  iniciarApp();
});

function iniciarApp() { 
  seleccionarFecha();
  confirmDelete();
}

function seleccionarFecha() {
  const fechaInput = document.querySelector("#fecha");
  fechaInput.addEventListener("input", function (e) {
    const fechaSeleccionada = e.target.value;
    window.location = `?fecha=${fechaSeleccionada}`;
  });
}

function confirmDelete(event) {
  event.preventDefault(); // Previne el envío del formulario inmediatamente
  Swal.fire({
    title: "Confirmación",
    text: "¿Estás seguro de que deseas eliminar este registro/cita?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById("formEliminar").submit();
    }
  });
}
```
#Views
##Router
- Almacenamiento de rutas:

La clase Router tiene dos arreglos:
getRoutes: Almacena rutas que se manejan con solicitudes GET (generalmente para mostrar información).
postRoutes: Almacena rutas que se manejan con solicitudes POST (generalmente para enviar formularios).
- Definición de rutas:

get($url, $fn): Agrega una ruta para solicitudes GET. El primer argumento es la URL y el segundo es la función que se ejecuta para esa ruta.
post($url, $fn): Similar a get, pero para solicitudes POST.
- Comprobación de rutas (comprobarRutas()):

Esta función determina la ruta actual y el método de solicitud (GET o POST).
Busca la función correspondiente en los arreglos getRoutes o postRoutes según el método.
(Sección comentada): El código comentado define rutas protegidas y verifica la autenticación del usuario (presumiblemente usando sesiones).
```
<?php

namespace MVC;

class Router
{
    public array $getRoutes = [];
    public array $postRoutes = [];

    public function get($url, $fn)
    {
        $this->getRoutes[$url] = $fn;
    }

    public function post($url, $fn)
    {
        $this->postRoutes[$url] = $fn;
    }

    public function comprobarRutas()
    {
        
        // Proteger Rutas...
        session_start();

        // Arreglo de rutas protegidas...
        // $rutas_protegidas = ['/admin', '/propiedades/crear', '/propiedades/actualizar', '/propiedades/eliminar', '/vendedores/crear', '/vendedores/actualizar', '/vendedores/eliminar'];

        // $auth = $_SESSION['login'] ?? null;

        $currentUrl = strtok($_SERVER['REQUEST_URI'], '?') ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            $fn = $this->getRoutes[$currentUrl] ?? null;
        } else {
            $fn = $this->postRoutes[$currentUrl] ?? null;
        }
```

###Views
####Composer
- Información del proyecto:

name: El nombre del proyecto (ingri/app-salon-php-mvc-sass).
description: Una breve descripción del proyecto (Creando un POO).
type: Indica que es un proyecto completo (project).
- Autocarga con PSR-4:

La sección autoload define cómo Composer carga automáticamente las clases del proyecto utilizando el estándar PSR-4.
psr-4: Indica que se usa el estándar PSR-4 para la autocarga.
El objeto define un mapeo entre espacios de nombres (namespaces) y directorios:
MVC\\: Las clases del núcleo MVC se encuentran en el directorio raíz del proyecto.
Controllers\\: Los controladores de la aplicación se encuentran en la carpeta controllers.
Model\\: Los modelos de la aplicación se encuentran en la carpeta models.
Classes\\: Otras clases genéricas del proyecto se encuentran en la carpeta classes.
- Autores:

La sección authors lista a los autores del proyecto.
- Dependencias:

La sección require define las librerías externas que necesita el proyecto:
intervention/image: Librería para manipular imágenes.
phpmailer/phpmailer: Librería para enviar correos electrónicos.
vlucas/phpdotenv: Librería para cargar variables de entorno desde un archivo .env.
```
{
"name": "ingri/app-salon-php-mvc-sass",
"description": "Creando un POO",
"type": "project",
"autoload": {
    "psr-4": {
        "MVC\\": "./",
        "Controllers\\": "./controllers",
        "Model\\": "./models",
        "Classes\\": "./classes"
    }
},

"authors": [
{
    "name": "Stephania",
    "email": "correo@correo.com"
}
],
"require": {
    "intervention/image": "^2.7",
    "phpmailer/phpmailer": "^6.9",
        "vlucas/phpdotenv": "^5.6"
}
}
```
###Views
#####Composer lock
- Información del proyecto:

name: El nombre del proyecto, en este caso "ingri/app-salon-php-mvc-sass".
description: Una breve descripción del proyecto, "Creando un POO".
type: Indica el tipo de proyecto, en este caso "project" (proyecto completo).
-  Autocarga de clases con PSR-4:

La sección autoload define cómo Composer carga automáticamente las clases del proyecto utilizando el estándar PSR-4.
psr-4: Indica que se utiliza el estándar PSR-4 para la autocarga.
El objeto define un mapeo entre espacios de nombres (namespaces) y directorios:
MVC\\: Las clases del núcleo MVC se encuentran en la raíz del proyecto.
Controllers\\: Los controladores de la aplicación se encuentran en la carpeta controllers.
Model\\: Los modelos de la aplicación se encuentran en la carpeta models.
Classes\\: Otras clases genéricas del proyecto se encuentran en la carpeta classes.
- Autores:

La sección authors enumera a los autores del proyecto, en este caso solo uno con su nombre y correo electrónico.
-  Dependencias:

La sección require define las bibliotecas externas que necesita el proyecto:
intervention/image: Biblioteca para manipular imágenes.
phpmailer/phpmailer: Biblioteca para enviar correos electrónicos.
vlucas/phpdotenv: Biblioteca para cargar variables de entorno desde un archivo .env.
```
{
    "_readme": [
        "This file locks the dependencies of your project to a known state",
        "Read more about it at https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies",
        "This file is @generated automatically"
    ],
    "content-hash": "247676722bbc716f4aeac0251c8b55c3",
    "packages": [
        {
            "name": "graham-campbell/result-type",
            "version": "v1.1.2",
            "source": {
                "type": "git",
                "url": "https://github.com/GrahamCampbell/Result-Type.git",
                "reference": "fbd48bce38f73f8a4ec8583362e732e4095e5862"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/GrahamCampbell/Result-Type/zipball/fbd48bce38f73f8a4ec8583362e732e4095e5862",
                "reference": "fbd48bce38f73f8a4ec8583362e732e4095e5862",
                "shasum": ""
            },
            "require": {
                "php": "^7.2.5 || ^8.0",
                "phpoption/phpoption": "^1.9.2"
            },
            "require-dev": {
                "phpunit/phpunit": "^8.5.34 || ^9.6.13 || ^10.4.2"
            },
            "type": "library",
            "autoload": {
                "psr-4": {
                    "GrahamCampbell\\ResultType\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Graham Campbell",
                    "email": "hello@gjcampbell.co.uk",
                    "homepage": "https://github.com/GrahamCampbell"
                }
            ],
            "description": "An Implementation Of The Result Type",
            "keywords": [
                "Graham Campbell",
                "GrahamCampbell",
                "Result Type",
                "Result-Type",
                "result"
            ],
            "support": {
                "issues": "https://github.com/GrahamCampbell/Result-Type/issues",
                "source": "https://github.com/GrahamCampbell/Result-Type/tree/v1.1.2"
            },
            "funding": [
                {
                    "url": "https://github.com/GrahamCampbell",
                    "type": "github"
                },
                {
                    "url": "https://tidelift.com/funding/github/packagist/graham-campbell/result-type",
                    "type": "tidelift"
                }
            ],
            "time": "2023-11-12T22:16:48+00:00"
        },
        {
            "name": "guzzlehttp/psr7",
            "version": "2.6.2",
            "source": {
                "type": "git",
                "url": "https://github.com/guzzle/psr7.git",
                "reference": "45b30f99ac27b5ca93cb4831afe16285f57b8221"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/guzzle/psr7/zipball/45b30f99ac27b5ca93cb4831afe16285f57b8221",
                "reference": "45b30f99ac27b5ca93cb4831afe16285f57b8221",
                "shasum": ""
            },
            "require": {
                "php": "^7.2.5 || ^8.0",
                "psr/http-factory": "^1.0",
                "psr/http-message": "^1.1 || ^2.0",
                "ralouphie/getallheaders": "^3.0"
            },
            "provide": {
                "psr/http-factory-implementation": "1.0",
                "psr/http-message-implementation": "1.0"
            },
            "require-dev": {
                "bamarni/composer-bin-plugin": "^1.8.2",
                "http-interop/http-factory-tests": "^0.9",
                "phpunit/phpunit": "^8.5.36 || ^9.6.15"
            },
            "suggest": {
                "laminas/laminas-httphandlerrunner": "Emit PSR-7 responses"
            },
            "type": "library",
            "extra": {
                "bamarni-bin": {
                    "bin-links": true,
                    "forward-command": false
                }
            },
            "autoload": {
                "psr-4": {
                    "GuzzleHttp\\Psr7\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Graham Campbell",
                    "email": "hello@gjcampbell.co.uk",
                    "homepage": "https://github.com/GrahamCampbell"
                },
                {
                    "name": "Michael Dowling",
                    "email": "mtdowling@gmail.com",
                    "homepage": "https://github.com/mtdowling"
                },
                {
                    "name": "George Mponos",
                    "email": "gmponos@gmail.com",
                    "homepage": "https://github.com/gmponos"
                },
                {
                    "name": "Tobias Nyholm",
                    "email": "tobias.nyholm@gmail.com",
                    "homepage": "https://github.com/Nyholm"
                },
                {
                    "name": "Márk Sági-Kazár",
                    "email": "mark.sagikazar@gmail.com",
                    "homepage": "https://github.com/sagikazarmark"
                },
                {
                    "name": "Tobias Schultze",
                    "email": "webmaster@tubo-world.de",
                    "homepage": "https://github.com/Tobion"
                },
                {
                    "name": "Márk Sági-Kazár",
                    "email": "mark.sagikazar@gmail.com",
                    "homepage": "https://sagikazarmark.hu"
                }
            ],
            "description": "PSR-7 message implementation that also provides common utility methods",
            "keywords": [
                "http",
                "message",
                "psr-7",
                "request",
                "response",
                "stream",
                "uri",
                "url"
            ],
            "support": {
                "issues": "https://github.com/guzzle/psr7/issues",
                "source": "https://github.com/guzzle/psr7/tree/2.6.2"
            },
            "funding": [
                {
                    "url": "https://github.com/GrahamCampbell",
                    "type": "github"
                },
                {
                    "url": "https://github.com/Nyholm",
                    "type": "github"
                },
                {
                    "url": "https://tidelift.com/funding/github/packagist/guzzlehttp/psr7",
                    "type": "tidelift"
                }
            ],
            "time": "2023-12-03T20:05:35+00:00"
        },
        {
            "name": "intervention/image",
            "version": "2.7.2",
            "source": {
                "type": "git",
                "url": "https://github.com/Intervention/image.git",
                "reference": "04be355f8d6734c826045d02a1079ad658322dad"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/Intervention/image/zipball/04be355f8d6734c826045d02a1079ad658322dad",
                "reference": "04be355f8d6734c826045d02a1079ad658322dad",
                "shasum": ""
            },
            "require": {
                "ext-fileinfo": "*",
                "guzzlehttp/psr7": "~1.1 || ^2.0",
                "php": ">=5.4.0"
            },
            "require-dev": {
                "mockery/mockery": "~0.9.2",
                "phpunit/phpunit": "^4.8 || ^5.7 || ^7.5.15"
            },
            "suggest": {
                "ext-gd": "to use GD library based image processing.",
                "ext-imagick": "to use Imagick based image processing.",
                "intervention/imagecache": "Caching extension for the Intervention Image library"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "2.4-dev"
                },
                "laravel": {
                    "providers": [
                        "Intervention\\Image\\ImageServiceProvider"
                    ],
                    "aliases": {
                        "Image": "Intervention\\Image\\Facades\\Image"
                    }
                }
            },
            "autoload": {
                "psr-4": {
                    "Intervention\\Image\\": "src/Intervention/Image"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Oliver Vogel",
                    "email": "oliver@intervention.io",
                    "homepage": "https://intervention.io/"
                }
            ],
            "description": "Image handling and manipulation library with support for Laravel integration",
            "homepage": "http://image.intervention.io/",
            "keywords": [
                "gd",
                "image",
                "imagick",
                "laravel",
                "thumbnail",
                "watermark"
            ],
            "support": {
                "issues": "https://github.com/Intervention/image/issues",
                "source": "https://github.com/Intervention/image/tree/2.7.2"
            },
            "funding": [
                {
                    "url": "https://paypal.me/interventionio",
                    "type": "custom"
                },
                {
                    "url": "https://github.com/Intervention",
                    "type": "github"
                }
            ],
            "time": "2022-05-21T17:30:32+00:00"
        },
        {
            "name": "phpmailer/phpmailer",
            "version": "v6.9.1",
            "source": {
                "type": "git",
                "url": "https://github.com/PHPMailer/PHPMailer.git",
                "reference": "039de174cd9c17a8389754d3b877a2ed22743e18"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/PHPMailer/PHPMailer/zipball/039de174cd9c17a8389754d3b877a2ed22743e18",
                "reference": "039de174cd9c17a8389754d3b877a2ed22743e18",
                "shasum": ""
            },
            "require": {
                "ext-ctype": "*",
                "ext-filter": "*",
                "ext-hash": "*",
                "php": ">=5.5.0"
            },
            "require-dev": {
                "dealerdirect/phpcodesniffer-composer-installer": "^1.0",
                "doctrine/annotations": "^1.2.6 || ^1.13.3",
                "php-parallel-lint/php-console-highlighter": "^1.0.0",
                "php-parallel-lint/php-parallel-lint": "^1.3.2",
                "phpcompatibility/php-compatibility": "^9.3.5",
                "roave/security-advisories": "dev-latest",
                "squizlabs/php_codesniffer": "^3.7.2",
                "yoast/phpunit-polyfills": "^1.0.4"
            },
            "suggest": {
                "decomplexity/SendOauth2": "Adapter for using XOAUTH2 authentication",
                "ext-mbstring": "Needed to send email in multibyte encoding charset or decode encoded addresses",
                "ext-openssl": "Needed for secure SMTP sending and DKIM signing",
                "greew/oauth2-azure-provider": "Needed for Microsoft Azure XOAUTH2 authentication",
                "hayageek/oauth2-yahoo": "Needed for Yahoo XOAUTH2 authentication",
                "league/oauth2-google": "Needed for Google XOAUTH2 authentication",
                "psr/log": "For optional PSR-3 debug logging",
                "symfony/polyfill-mbstring": "To support UTF-8 if the Mbstring PHP extension is not enabled (^1.2)",
                "thenetworg/oauth2-azure": "Needed for Microsoft XOAUTH2 authentication"
            },
            "type": "library",
            "autoload": {
                "psr-4": {
                    "PHPMailer\\PHPMailer\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "LGPL-2.1-only"
            ],
            "authors": [
                {
                    "name": "Marcus Bointon",
                    "email": "phpmailer@synchromedia.co.uk"
                },
                {
                    "name": "Jim Jagielski",
                    "email": "jimjag@gmail.com"
                },
                {
                    "name": "Andy Prevost",
                    "email": "codeworxtech@users.sourceforge.net"
                },
                {
                    "name": "Brent R. Matzelle"
                }
            ],
            "description": "PHPMailer is a full-featured email creation and transfer class for PHP",
            "support": {
                "issues": "https://github.com/PHPMailer/PHPMailer/issues",
                "source": "https://github.com/PHPMailer/PHPMailer/tree/v6.9.1"
            },
            "funding": [
                {
                    "url": "https://github.com/Synchro",
                    "type": "github"
                }
            ],
            "time": "2023-11-25T22:23:28+00:00"
        },
        {
            "name": "phpoption/phpoption",
            "version": "1.9.2",
            "source": {
                "type": "git",
                "url": "https://github.com/schmittjoh/php-option.git",
                "reference": "80735db690fe4fc5c76dfa7f9b770634285fa820"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/schmittjoh/php-option/zipball/80735db690fe4fc5c76dfa7f9b770634285fa820",
                "reference": "80735db690fe4fc5c76dfa7f9b770634285fa820",
                "shasum": ""
            },
            "require": {
                "php": "^7.2.5 || ^8.0"
            },
            "require-dev": {
                "bamarni/composer-bin-plugin": "^1.8.2",
                "phpunit/phpunit": "^8.5.34 || ^9.6.13 || ^10.4.2"
            },
            "type": "library",
            "extra": {
                "bamarni-bin": {
                    "bin-links": true,
                    "forward-command": true
                },
                "branch-alias": {
                    "dev-master": "1.9-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "PhpOption\\": "src/PhpOption/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "Apache-2.0"
            ],
            "authors": [
                {
                    "name": "Johannes M. Schmitt",
                    "email": "schmittjoh@gmail.com",
                    "homepage": "https://github.com/schmittjoh"
                },
                {
                    "name": "Graham Campbell",
                    "email": "hello@gjcampbell.co.uk",
                    "homepage": "https://github.com/GrahamCampbell"
                }
            ],
            "description": "Option Type for PHP",
            "keywords": [
                "language",
                "option",
                "php",
                "type"
            ],
            "support": {
                "issues": "https://github.com/schmittjoh/php-option/issues",
                "source": "https://github.com/schmittjoh/php-option/tree/1.9.2"
            },
            "funding": [
                {
                    "url": "https://github.com/GrahamCampbell",
                    "type": "github"
                },
                {
                    "url": "https://tidelift.com/funding/github/packagist/phpoption/phpoption",
                    "type": "tidelift"
                }
            ],
            "time": "2023-11-12T21:59:55+00:00"
        },
        {
            "name": "psr/http-factory",
            "version": "1.1.0",
            "source": {
                "type": "git",
                "url": "https://github.com/php-fig/http-factory.git",
                "reference": "2b4765fddfe3b508ac62f829e852b1501d3f6e8a"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/php-fig/http-factory/zipball/2b4765fddfe3b508ac62f829e852b1501d3f6e8a",
                "reference": "2b4765fddfe3b508ac62f829e852b1501d3f6e8a",
                "shasum": ""
            },
            "require": {
                "php": ">=7.1",
                "psr/http-message": "^1.0 || ^2.0"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "1.0.x-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "Psr\\Http\\Message\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "PHP-FIG",
                    "homepage": "https://www.php-fig.org/"
                }
            ],
            "description": "PSR-17: Common interfaces for PSR-7 HTTP message factories",
            "keywords": [
                "factory",
                "http",
                "message",
                "psr",
                "psr-17",
                "psr-7",
                "request",
                "response"
            ],
            "support": {
                "source": "https://github.com/php-fig/http-factory"
            },
            "time": "2024-04-15T12:06:14+00:00"
        },
        {
            "name": "psr/http-message",
            "version": "2.0",
            "source": {
                "type": "git",
                "url": "https://github.com/php-fig/http-message.git",
                "reference": "402d35bcb92c70c026d1a6a9883f06b2ead23d71"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/php-fig/http-message/zipball/402d35bcb92c70c026d1a6a9883f06b2ead23d71",
                "reference": "402d35bcb92c70c026d1a6a9883f06b2ead23d71",
                "shasum": ""
            },
            "require": {
                "php": "^7.2 || ^8.0"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "2.0.x-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "Psr\\Http\\Message\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "PHP-FIG",
                    "homepage": "https://www.php-fig.org/"
                }
            ],
            "description": "Common interface for HTTP messages",
            "homepage": "https://github.com/php-fig/http-message",
            "keywords": [
                "http",
                "http-message",
                "psr",
                "psr-7",
                "request",
                "response"
            ],
            "support": {
                "source": "https://github.com/php-fig/http-message/tree/2.0"
            },
            "time": "2023-04-04T09:54:51+00:00"
        },
        {
            "name": "ralouphie/getallheaders",
            "version": "3.0.3",
            "source": {
                "type": "git",
                "url": "https://github.com/ralouphie/getallheaders.git",
                "reference": "120b605dfeb996808c31b6477290a714d356e822"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/ralouphie/getallheaders/zipball/120b605dfeb996808c31b6477290a714d356e822",
                "reference": "120b605dfeb996808c31b6477290a714d356e822",
                "shasum": ""
            },
            "require": {
                "php": ">=5.6"
            },
            "require-dev": {
                "php-coveralls/php-coveralls": "^2.1",
                "phpunit/phpunit": "^5 || ^6.5"
            },
            "type": "library",
            "autoload": {
                "files": [
                    "src/getallheaders.php"
                ]
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Ralph Khattar",
                    "email": "ralph.khattar@gmail.com"
                }
            ],
            "description": "A polyfill for getallheaders.",
            "support": {
                "issues": "https://github.com/ralouphie/getallheaders/issues",
                "source": "https://github.com/ralouphie/getallheaders/tree/develop"
            },
            "time": "2019-03-08T08:55:37+00:00"
        },
        {
            "name": "symfony/polyfill-ctype",
            "version": "v1.29.0",
            "source": {
                "type": "git",
                "url": "https://github.com/symfony/polyfill-ctype.git",
                "reference": "ef4d7e442ca910c4764bce785146269b30cb5fc4"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/symfony/polyfill-ctype/zipball/ef4d7e442ca910c4764bce785146269b30cb5fc4",
                "reference": "ef4d7e442ca910c4764bce785146269b30cb5fc4",
                "shasum": ""
            },
            "require": {
                "php": ">=7.1"
            },
            "provide": {
                "ext-ctype": "*"
            },
            "suggest": {
                "ext-ctype": "For best performance"
            },
            "type": "library",
            "extra": {
                "thanks": {
                    "name": "symfony/polyfill",
                    "url": "https://github.com/symfony/polyfill"
                }
            },
            "autoload": {
                "files": [
                    "bootstrap.php"
                ],
                "psr-4": {
                    "Symfony\\Polyfill\\Ctype\\": ""
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Gert de Pagter",
                    "email": "BackEndTea@gmail.com"
                },
                {
                    "name": "Symfony Community",
                    "homepage": "https://symfony.com/contributors"
                }
            ],
            "description": "Symfony polyfill for ctype functions",
            "homepage": "https://symfony.com",
            "keywords": [
                "compatibility",
                "ctype",
                "polyfill",
                "portable"
            ],
            "support": {
                "source": "https://github.com/symfony/polyfill-ctype/tree/v1.29.0"
            },
            "funding": [
                {
                    "url": "https://symfony.com/sponsor",
                    "type": "custom"
                },
                {
                    "url": "https://github.com/fabpot",
                    "type": "github"
                },
                {
                    "url": "https://tidelift.com/funding/github/packagist/symfony/symfony",
                    "type": "tidelift"
                }
            ],
            "time": "2024-01-29T20:11:03+00:00"
        },
        {
            "name": "symfony/polyfill-mbstring",
            "version": "v1.29.0",
            "source": {
                "type": "git",
                "url": "https://github.com/symfony/polyfill-mbstring.git",
                "reference": "9773676c8a1bb1f8d4340a62efe641cf76eda7ec"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/symfony/polyfill-mbstring/zipball/9773676c8a1bb1f8d4340a62efe641cf76eda7ec",
                "reference": "9773676c8a1bb1f8d4340a62efe641cf76eda7ec",
                "shasum": ""
            },
            "require": {
                "php": ">=7.1"
            },
            "provide": {
                "ext-mbstring": "*"
            },
            "suggest": {
                "ext-mbstring": "For best performance"
            },
            "type": "library",
            "extra": {
                "thanks": {
                    "name": "symfony/polyfill",
                    "url": "https://github.com/symfony/polyfill"
                }
            },
            "autoload": {
                "files": [
                    "bootstrap.php"
                ],
                "psr-4": {
                    "Symfony\\Polyfill\\Mbstring\\": ""
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Nicolas Grekas",
                    "email": "p@tchwork.com"
                },
                {
                    "name": "Symfony Community",
                    "homepage": "https://symfony.com/contributors"
                }
            ],
            "description": "Symfony polyfill for the Mbstring extension",
            "homepage": "https://symfony.com",
            "keywords": [
                "compatibility",
                "mbstring",
                "polyfill",
                "portable",
                "shim"
            ],
            "support": {
                "source": "https://github.com/symfony/polyfill-mbstring/tree/v1.29.0"
            },
            "funding": [
                {
                    "url": "https://symfony.com/sponsor",
                    "type": "custom"
                },
                {
                    "url": "https://github.com/fabpot",
                    "type": "github"
                },
                {
                    "url": "https://tidelift.com/funding/github/packagist/symfony/symfony",
                    "type": "tidelift"
                }
            ],
            "time": "2024-01-29T20:11:03+00:00"
        },
        {
            "name": "symfony/polyfill-php80",
            "version": "v1.29.0",
            "source": {
                "type": "git",
                "url": "https://github.com/symfony/polyfill-php80.git",
                "reference": "87b68208d5c1188808dd7839ee1e6c8ec3b02f1b"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/symfony/polyfill-php80/zipball/87b68208d5c1188808dd7839ee1e6c8ec3b02f1b",
                "reference": "87b68208d5c1188808dd7839ee1e6c8ec3b02f1b",
                "shasum": ""
            },
            "require": {
                "php": ">=7.1"
            },
            "type": "library",
            "extra": {
                "thanks": {
                    "name": "symfony/polyfill",
                    "url": "https://github.com/symfony/polyfill"
                }
            },
            "autoload": {
                "files": [
                    "bootstrap.php"
                ],
                "psr-4": {
                    "Symfony\\Polyfill\\Php80\\": ""
                },
                "classmap": [
                    "Resources/stubs"
                ]
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Ion Bazan",
                    "email": "ion.bazan@gmail.com"
                },
                {
                    "name": "Nicolas Grekas",
                    "email": "p@tchwork.com"
                },
                {
                    "name": "Symfony Community",
                    "homepage": "https://symfony.com/contributors"
                }
            ],
            "description": "Symfony polyfill backporting some PHP 8.0+ features to lower PHP versions",
            "homepage": "https://symfony.com",
            "keywords": [
                "compatibility",
                "polyfill",
                "portable",
                "shim"
            ],
            "support": {
                "source": "https://github.com/symfony/polyfill-php80/tree/v1.29.0"
            },
            "funding": [
                {
                    "url": "https://symfony.com/sponsor",
                    "type": "custom"
                },
                {
                    "url": "https://github.com/fabpot",
                    "type": "github"
                },
                {
                    "url": "https://tidelift.com/funding/github/packagist/symfony/symfony",
                    "type": "tidelift"
                }
            ],
            "time": "2024-01-29T20:11:03+00:00"
        },
        {
            "name": "vlucas/phpdotenv",
            "version": "v5.6.0",
            "source": {
                "type": "git",
                "url": "https://github.com/vlucas/phpdotenv.git",
                "reference": "2cf9fb6054c2bb1d59d1f3817706ecdb9d2934c4"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/vlucas/phpdotenv/zipball/2cf9fb6054c2bb1d59d1f3817706ecdb9d2934c4",
                "reference": "2cf9fb6054c2bb1d59d1f3817706ecdb9d2934c4",
                "shasum": ""
            },
            "require": {
                "ext-pcre": "*",
                "graham-campbell/result-type": "^1.1.2",
                "php": "^7.2.5 || ^8.0",
                "phpoption/phpoption": "^1.9.2",
                "symfony/polyfill-ctype": "^1.24",
                "symfony/polyfill-mbstring": "^1.24",
                "symfony/polyfill-php80": "^1.24"
            },
            "require-dev": {
                "bamarni/composer-bin-plugin": "^1.8.2",
                "ext-filter": "*",
                "phpunit/phpunit": "^8.5.34 || ^9.6.13 || ^10.4.2"
            },
            "suggest": {
                "ext-filter": "Required to use the boolean validator."
            },
            "type": "library",
            "extra": {
                "bamarni-bin": {
                    "bin-links": true,
                    "forward-command": true
                },
                "branch-alias": {
                    "dev-master": "5.6-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "Dotenv\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "BSD-3-Clause"
            ],
            "authors": [
                {
                    "name": "Graham Campbell",
                    "email": "hello@gjcampbell.co.uk",
                    "homepage": "https://github.com/GrahamCampbell"
                },
                {
                    "name": "Vance Lucas",
                    "email": "vance@vancelucas.com",
                    "homepage": "https://github.com/vlucas"
                }
            ],
            "description": "Loads environment variables from `.env` to `getenv()`, `$_ENV` and `$_SERVER` automagically.",
            "keywords": [
                "dotenv",
                "env",
                "environment"
            ],
            "support": {
                "issues": "https://github.com/vlucas/phpdotenv/issues",
                "source": "https://github.com/vlucas/phpdotenv/tree/v5.6.0"
            },
            "funding": [
                {
                    "url": "https://github.com/GrahamCampbell",
                    "type": "github"
                },
                {
                    "url": "https://tidelift.com/funding/github/packagist/vlucas/phpdotenv",
                    "type": "tidelift"
                }
            ],
            "time": "2023-11-12T22:43:29+00:00"
        }
    ],
    "packages-dev": [],
    "aliases": [],
    "minimum-stability": "stable",
    "stability-flags": [],
    "prefer-stable": false,
    "prefer-lowest": false,
    "platform": [],
    "platform-dev": [],
    "plugin-api-version": "2.6.0"
}
```
###Views
####Gulpfile
- Dependencias:

Importa librerías para compilar Sass (sass, gulp-sass), prefijado automático de CSS (autoprefixer, gulp-postcss), optimización de código CSS (cssnano, gulp-postcss), minificado de JavaScript (terser, gulp-terser-js), optimización de imágenes (imagemin, gulp-imagemin), conversión a WebP (webp, gulp-webp) y otras utilidades (sourcemaps, concat, rename, notify, cache, clean).
- Rutas de archivos:

Define rutas para archivos SCSS (paths.scss), JavaScript (paths.js) e imágenes (paths.imagenes).
- Tareas:

css: Compila SCSS a CSS, añade prefijos automáticos, opcionalmente minifica y genera mapas de origen.
javascript: Minifica archivos JavaScript y genera mapas de origen.
imagenes: Optimiza imágenes usando cache y notifica al finalizar.
versionWebp: Crea versiones WebP de las imágenes originales.
watchArchivos: Observa cambios en los archivos y ejecuta las tareas correspondientes.
- Exportaciones:

Exporta las tareas css y watchArchivos para ser usadas individualmente.
Exporta dos tareas por defecto:
default: Ejecuta todas las tareas en paralelo (compilación, minificación, optimización) e inicia el observado de archivos.
build: Similar a default pero sin observar cambios.

```
const { src, dest, watch, series, parallel } = require("gulp");
const sass = require("gulp-sass")(require("sass"));
const autoprefixer = require("autoprefixer");
const postcss = require("gulp-postcss");
const sourcemaps = require("gulp-sourcemaps");
const cssnano = require("cssnano");
const concat = require("gulp-concat");
const terser = require("gulp-terser-js");
const rename = require("gulp-rename");
const imagemin = require("gulp-imagemin"); // Minificar imagenes
const notify = require("gulp-notify");
const cache = require("gulp-cache");
const clean = require("gulp-clean");
const webp = require("gulp-webp");

const paths = {
  scss: "src/scss/**/*.scss",
  js: "src/js/**/*.js",
  imagenes: "src/img/**/*",
};

// css es una función que se puede llamar automaticamente
function css() {
  return (
    src(paths.scss)
      .pipe(sourcemaps.init())
      .pipe(sass())
      .pipe(postcss([autoprefixer(), cssnano()]))
      // .pipe(postcss([autoprefixer()]))
      .pipe(sourcemaps.write("."))
      .pipe(dest("public/build/css"))
  );
}

function javascript() {
  return src(paths.js)
    .pipe(terser())
    .pipe(sourcemaps.write("."))
    .pipe(dest("public/build/js"));
}

function imagenes() {
  return src(paths.imagenes)
    .pipe(cache(imagemin({ optimizationLevel: 3 })))
    .pipe(dest("public/build/img"))
    .pipe(notify({ message: "Imagen Completada" }));
}

function versionWebp() {
  return src(paths.imagenes)
    .pipe(webp())
    .pipe(dest("public/build/img"))
    .pipe(notify({ message: "Imagen Completada" }));
}

function watchArchivos() {
  watch(paths.scss, css);
  watch(paths.js, javascript);
  watch(paths.imagenes, imagenes);
  watch(paths.imagenes, versionWebp);
}

exports.css = css;
exports.watchArchivos = watchArchivos;
exports.default = parallel(
  css,
  javascript,
  imagenes,
  versionWebp,
  watchArchivos
);
exports.build = parallel(css, javascript, imagenes, versionWebp);
```

###Views
####Package
- Contiene la información de la aplicación, como el nombre, la descripción, los permisos necesarios y los componentes de la aplicación.-
-  Componentes de la aplicación:
- Formulario de registro: Un formulario donde los usuarios pueden crear una cuenta y proporcionar información sobre ellos mismos, como su nombre, ubicación, preferencias de corte de cabello y disponibilidad.
Búsqueda de barberos: Una función de búsqueda que permite a los usuarios encontrar barberos cercanos a ellos en función de sus preferencias.
Perfiles de barberos: Páginas de perfil que muestran información sobre los barberos, como sus fotos, calificaciones, disponibilidad y servicios ofrecidos.
Sistema de citas: Un sistema que permite a los usuarios reservar citas con los barberos.
Sistema de mensajería: Un sistema de mensajería que permite a los usuarios comunicarse con los barberos antes y después de las citas.
- Integración con GitHub Actions:

- GitHub Actions se puede utilizar para automatizar tareas como enviar notificaciones por correo electrónico a los usuarios cuando se confirme una cita o para enviar recordatorios a los usuarios sobre sus próximas citas.

- Implementación de la aplicación:
La aplicación se puede implementar en una plataforma de alojamiento web como Vercel o Netlify.



###End
