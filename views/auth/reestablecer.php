<div class="contenedor reestablecer">
  <?php include_once __DIR__ . "/../templates/nombre-sitio.php"?>
  <div class="contenedor-sm">
    <p class="descripcion-pagina">Coloca tu nuevo Password</p>
    <?php include_once __DIR__ . "/../templates/alertas.php"?>
    <?php if($mostrar) {?>
      <form method="post" class="formulario">
        <div class="campo">
          <label for="password">Password</label> 
          <input type="password" id="password" placeholder="Tu password" name="password">
        </div>
        <input type="submit" class="boton" value="Reestablecer Password">
      </form>
      <?php 
      } 
    ?>
    <div class="acciones">
      <a href="/">¿Ta tienes una cuenta? Iniciar Sesión</a>
      <a href="/crear">¿Aun no tienes una cuenta? Crear Una</a>
    </div>
  </div>
</div>
