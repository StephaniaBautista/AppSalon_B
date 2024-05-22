<h1 class="nombre-pagina">Olvidé mi password</h1>
<p class="descripcion-pagina">Llena el siguiente formulario para restablecer tu password</p>
<?php include_once __DIR__ . '/../templates/alertas.php'; ?>

<form action="/olvide-password" class="formulario" method="POST">
    <div class="campo">
        <label for="email">E-Mail:</label>
        <input type="email" id="email" name="email" placeholder="Inserta tu E-Mail">
    </div> <!--Fin div -->
    <input type="submit" value="Recuperar cuenta" class="btn-azul">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes cuenta? ¡Crea una!</a>
</div>