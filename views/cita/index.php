<h1 class="nombre-página">Citas</h1>
<p class="descripcion-pagina">A continuación, elige tus servicios</p>

<?php include_once __DIR__  . '/../templates/barra.php';?>

<div id="app">
    <nav class="tabs">
        <button type="button" data-paso='1'>Servicios</button>
        <button type="button" data-paso='2'>Datos Cita</button>
        <button type="button" data-paso='3'>Resumen</button>
    </nav>
    <div id="paso-1" class="seccion">
        <h2>Servicios</h2>
        <p class="text-center">Elige tus servicios a continuación</p>
        <div id="servicios" class="listado-servicios">

        </div>
    </div>
    <div id="paso-2" class="seccion">
        <h2>Tus datos y cita</h2>
        <p class="text-center">Coloca tus datos y fechas de tu cita</p>
        <form class="formulario">
            <div class="campo">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" placeholder="Inserta tu nombre" value="<?php echo $nombre ?>" disabled>
            </div> <!--Cierre del Div -->
            <div class="campo">
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
            </div> <!--Cierre del Div -->
            <div class="campo">
                <label for="hora">Hora:</label>
                <input type="time" id="hora">
            </div> <!--Cierre del Div -->
            <input type="hidden" id="id" value="<?php echo $id;?>">
        </form>
    </div>
    <div id="paso-3" class="seccion contenido-resumen">
        <h2>Resumen de la actividad</h2>
        <p class="text-center">Verifíca que la información sea correcta</p>
    </div>

    <div class="paginacion">
        <button id="anterior" class="btn-azul">&laquo; Anterior</button>
        <button id="siguiente" class="btn-azul">Siguiente &raquo;</button>
    </div>
</div>

<?php 
    $script = "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script src='build/js/darkmode.js'></script> 
    <script src='build/js/app.js'></script>";
?>