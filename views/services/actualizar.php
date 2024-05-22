<h1 class="nombre-pagina">Servicios Actualizar</h1>
<p class="descripcion-pagina">Administracion de Servicios</p>
<?php require_once __DIR__ . '/../templates/barra.php'; ?>
<?php require_once __DIR__ . '/../templates/alertas.php'; ?>

<form method="POST" class="formulario">
    <?php include_once __DIR__ . '/formulario.php' ?>

    <input type="submit" class="btn-azul" value="Actualizar Servicio">
</form>