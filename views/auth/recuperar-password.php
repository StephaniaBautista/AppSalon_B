<h1 class="nombre-pagina">Recupear mi password</h1>
<p class="descripcion-pagina">Llena el siguiente formulario para restablecer tu password</p>
<?php include_once __DIR__ . '/../templates/alertas.php'; ?>
<?php if($error) return; ?>

<form class="formulario" method="POST">
    <div class="campo">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Inserta tu nueva password">
    </div> <!--Fin div -->
    <input type="submit" value="Recuperar password" class="btn-azul">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes cuenta? ¡Crea una!</a>
</div>