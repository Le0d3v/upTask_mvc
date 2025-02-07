<?php include_once __DIR__ . "/header.php"?>
<?php include_once __DIR__ . "/breadcrumbs.php"?>
<div class="contenedor-sm">
  <?php include_once __DIR__ . "/../templates/alertas.php"?>
  <form action="/crear-proyecto" method="post" class="formulario">
    <?php include_once __DIR__ . "/formulario-proyecto.php"?>
    <input type="submit" value="Crear Proyecto">
  </form>
</div>
<?php include_once __DIR__ . "/footer.php"?>