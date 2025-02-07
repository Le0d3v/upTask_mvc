<?php include_once __DIR__ . "/header.php"?>
<div class="contenedor-sm">
  <div class="contenedor-nueva-tarea">
    <button type="button" class="agregar-tarea" id="agregar-tarea">&#43; Nueva Tarea</button>
  </div>
  <a href="/dashboard" class="enlace">
  &#8592; Volver a Proyectos
  </a>
  <div id="filtros" class="filtros">
    <div class="filtros-inputs">
      <h2>Filtros: </h2>
      <div class="campo">
        <label for="todas">Todas</label>
        <input type="radio" name="filtro" id="todas" value="" checked>
      </div>
      <div class="campo">
        <label for="completadas">Completadas</label>
        <input type="radio" name="filtro" id="completadas" value="1">
      </div>
      <div class="campo">
        <label for="pendientes">Pendientes</label>
        <input type="radio" name="filtro" id="pendientes" value="0">
      </div>
    </div>
  </div>
  <ul id="listado-tareas" class="listado-tareas">
    
  </ul>
</div>
<?php include_once __DIR__ . "/footer.php";
$script .= "
  <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
  <script src='/build/js/tareas.js'></script>";
?>
