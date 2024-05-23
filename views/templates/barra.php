<div class="barra">
    <p>Hola: <?php echo $nombre ?? ''?> </p>
    <a href="/logout" class="btn-azul">Cerrar Sesión</a>
    <a href="/inicio-ingles" class="btn-verde">Cambiar Lenguaje</a>
</div>

<div class="barra">
    <p class="aumento-size">A+</p>
    <p class="disminuye-size">A-</p>
    <img class="dark-mode-boton" src="/build/img/dark-mode.svg">
</div>

<?php if(isset($_SESSION['admin'])){ ?>
    <div class="barra-servicios">
        <a href="/admin" class="btn-azul">Ver Citas</a>
        <a href="/servicios" class="btn-azul">Ver servicios</a>
        <a href="/servicios/crear" class="btn-azul">Nuevo Servicio</a>
    </div>
<?php } ?> 
