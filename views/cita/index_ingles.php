<h1 class="nombre-página">Dates</h1>
<p class="descripcion-pagina">Next, please choose your services</p>

<?php include_once __DIR__  . '/../templates/barra_ingles.php';?>

<div id="app">
    <nav class="tabs">
        <button type="button" data-paso='1'>Services</button>
        <button type="button" data-paso='2'>Date Information</button>
        <button type="button" data-paso='3'>Resume</button>
    </nav>
    <div id="paso-1" class="seccion">
        <h2>Services</h2>
        <p class="text-center">Next Choose your services</p>
        <div id="servicios" class="listado-servicios">

        </div>
    </div>
    <div id="paso-2" class="seccion">
        <h2>Your Date and Information</h2>
        <p class="text-center">Set Here your information and date of your date</p>
        <form class="formulario">
            <div class="campo">
                <label for="nombre">Name:</label>
                <input type="text" id="nombre" placeholder="Inserta tu nombre" value="<?php echo $nombre ?>" disabled>
            </div> <!--Cierre del Div -->
            <div class="campo">
                <label for="fecha">Date:</label>
                <input type="date" id="fecha" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
            </div> <!--Cierre del Div -->
            <div class="campo">
                <label for="hora">Hour:</label>
                <input type="time" id="hora">
            </div> <!--Cierre del Div -->
            <input type="hidden" id="id" value="<?php echo $id;?>">
        </form>
    </div>
    <div id="paso-3" class="seccion contenido-resumen">
        <h2>Activity Resume</h2>
        <p class="text-center">Plese verify that the information is correct</p>
    </div>

    <div class="paginacion">
        <button id="anterior" class="btn-azul">&laquo; Last</button>
        <button id="siguiente" class="btn-azul">Next &raquo;</button>
    </div>
</div>

<?php 
    $script = "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script src='build/js/darkmode.js'></script> 
    <script src='build/js/app.js'></script>";
?>