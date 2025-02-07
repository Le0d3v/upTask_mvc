<aside class="sidebar">
  <div class="contenedor-sidebar">
    <h2>UpTask</h1>
    <div class="cerrar-menu">
      <img src="/build/img/cerrar.svg" alt="imagen-cerrar-menu" id="cerrar-menu">
    </div>
  </div>
  <nav class="sidebar-nav">
    <a class="<?php echo ($enlace === "project") ? "activo" : "";?>" href="/dashboard">Proyectos</a>
    <a class="<?php echo ($titulo === "Crear Proyecto") ? "activo" : ""; ?>" href="/crear-proyecto">Crear Proyecto</a>
    <a class="<?php echo ($enlace === "profile") ? "activo" : "";?>" href="/perfil">Perfil</a>
  </nav>

  <div class="cerrar-sesion-mobile">
    <a href="/logout" class="cerrar-sesion">Cerrar Sesión</a>
  </div>
</aside>