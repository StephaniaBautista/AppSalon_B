<h1 class="nombre-pagina">Crear cuenta</h1>
<p class="descripcion-pagina">Llena el siguiente formulario para crear una cuenta</p>

<?php 
    include_once __DIR__ . "/../templates/alertas.php";
?>

<form action="/crear-cuenta" class="formulario" method="POST">
    <div class="campo">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" placeholder="Inserta tu nombre" value="<?php echo s($usuario->nombre) ?>">
    </div> <!--Fin div -->
    <div class="campo">
        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" placeholder="Inserta tu apellido" value="<?php echo s($usuario->apellido) ?>">
    </div> <!--Fin div -->
    <div class="campo">
        <label for="telefono">telefono:</label>
        <input type="tel" id="telefono" name="telefono" placeholder="Inserta tu telefono" value="<?php echo s($usuario->telefono) ?>">
    </div> <!--Fin div -->
    <div class="campo">
        <label for="email">E-Mail:</label>
        <input type="email" id="email" name="email" placeholder="Inserta tu E-Mail"  value="<?php echo s($usuario->email) ?>">
    </div> <!--Fin div -->
    <div class="campo">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Crea tu password">
    </div> <!--Fin div -->
    <input type="submit" value="Crear Cuenta" class="btn-azul">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
    <a href="/olvide-password">¿Has olvidado tu password?</a>
</div>