<?php include_once __DIR__ . "/header.php"?>
<div class="contenedor-sm">
  <?php include_once __DIR__ . "/../templates/alertas.php"?>
  <a href="/cambiar-password" class="enlace">Cambiar Password</a>
  <form action="/perfil" class="formulario" method="post">
    <div class="campo">
      <label for="nombre">Nombre</label>
      <input type="text" placeholder="Tu nombre" value="<?= $usuario->nombre?>" name="nombre">
    </div>
    <div class="campo">
      <label for="email">Email</label>
      <input type="email" placeholder="Tu email" value="<?= $usuario->email?>" name="email">
    </div>
    <input type="submit" value="Editar Datos de Perfil">
  </form>
</div>
<?php include_once __DIR__ . "/footer.php"?>