<h1 class="nombre-pagina">Login</h1>
<p class="descripcion-pagina">Inicia sesión con tus datos</p>

<?php include_once __DIR__ . '/../templates/alertas.php'; ?>

<form class="formulario" method="POST" action="/">
    <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" placeholder="Inserta tu E-Mail" name="email" value="<?php echo s($usuario->email) ?>">
    </div> <!--- Cierre campo -->
    <div class="campo">
        <label for="password">password</label>
        <input type="password" id="password" placeholder="Inserta tu password" name="password">
    </div> <!--- Cierre campo -->
    <input type="submit" class="btn-azul" value="Iniciar Sesión">

</form>

<div class="acciones">
    <a href="/crear-cuenta">¿Aún no tienes cuenta? ¡Crea una!</a>
    <a href="/olvide-password">¿Has olvidado tu password?</a>
</div>