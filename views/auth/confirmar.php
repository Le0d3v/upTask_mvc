<div class="contenedor confirmar">
  <?php include_once __DIR__ . "/../templates/nombre-sitio.php"?>
  <div class="contenedor-sm">
    <?php include_once __DIR__ . "/../templates/alertas.php"?>
    <div class="acciones">
      <?php 
        if(!$error) {?>
          <a href="/" class="boton">Iniciar Sesión</a>
        <?php 
        } else { ?>
          <a href="/">Volver al Inicio</a>
          <?php 
        }
      ?>
    </div>
  </div>
</div>
