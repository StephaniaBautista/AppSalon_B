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