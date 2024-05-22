<h1 class="nombre-pagina">Servicios Crear</h1>
<p class="descripcion-pagina">Administracion de Servicios</p>
<?php require_once __DIR__ . '/../templates/barra.php'; ?>
<?php require_once __DIR__ . '/../templates/alertas.php'; ?>

<form action="/servicios/crear" method="POST" class="formulario">
    <?php include_once __DIR__ . '/formulario.php' ?>

    <input type="submit" class="btn-azul" value="Guardar Servicio">
</form>