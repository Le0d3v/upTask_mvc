<?php include_once __DIR__ . "/header.php"?>
<div class="contenedor-sm">
  <?php include_once __DIR__ . "/../templates/alertas.php"?>
  <a href="/perfil" class="enlace">Volver al perfil</a>
  <form action="/cambiar-password" class="formulario" method="post">
    <div class="campo">
      <label for="nombre">Password Actual</label>
      <input type="password" placeholder="Tu password actual" value="" name="password_actual">
    </div>
    <div class="campo">
      <label for="email">Password Nuevo</label>
      <input type="password" placeholder="Tu nuevo password" value="" name="password_nuevo">
    </div>
    <input type="submit" value="Cambiar Password">
  </form>
</div>
<?php include_once __DIR__ . "/footer.php"?>