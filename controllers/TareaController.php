<?php 
namespace Controllers;

use Model\Proyecto;
use Model\Tarea;

class TareaController {
  public static function index() {
    // Ontener url del proyecto actual
    $proyectoId = s($_GET["proyecto"]);
    if(!$proyectoId) header("Location: /dashboard"); // Redirección en caso de no encontrar el proyecto
    
    $proyecto = Proyecto::where("url", $proyectoId); // Obtener el proyecto actual
    session_start();

    // Redirección en caso de no encontrar un proyecto o de que el proyecto no sea del usuario
    if(!$proyecto || $proyecto->propietarioId != $_SESSION["id"]) { 
      header("Location: /404");
    }

    $tareas = Tarea::belongsTo("proyectoId", $proyecto->id); // Obtener todas las tareas relacionadas al proyecto actual

    echo json_encode(["tareas" => $tareas]); // Imprimir las tareas en formato JSON
  }

  public static function crear() {
    if($_SERVER["REQUEST_METHOD"] === "POST") {
      session_start();
      $proyecto = Proyecto::where("url", $_POST["proyectoId"]);

      if(!$proyecto || $proyecto->propietarioId !== $_SESSION["id"]) {
        $respuesta = [
          "tipo" => "error",
          "mensaje" => "Hubo un error al agregar la tarea"
        ];
        echo json_encode($respuesta);
        return;
      } 
      
      // Instanciar y crear la tarea
      $tarea = new Tarea($_POST);
      $tarea->proyectoId = $proyecto->id;
      $resultado = $tarea->guardar();

      $respuesta = [
        "resultado" => "true",
        "tipo" => "exito",
        "id" => $resultado["id"],
        "mensaje" => "Tarea creada correctamente",
        "proyectoId" => $proyecto->id
      ];
      echo json_encode($respuesta);
    }
  }

  public static function actualizar() {
    if($_SERVER["REQUEST_METHOD"] === "POST") {
      $proyecto = Proyecto::where("url", $_POST["proyectoId"]);
      session_start();
      
      if(!$proyecto || $proyecto->propietarioId !== $_SESSION["id"]) {
        $respuesta = [
          "tipo" => "error",
          "mensaje" => "Hubo un error al actualizar la tarea"
        ];
        echo json_encode($respuesta);
        return;
      } 

      $tarea = new Tarea($_POST);
      $tarea->proyectoId = $proyecto->id;
      $resultado = $tarea->guardar();

      if($resultado) {
        $respuesta = [
          "resultado" => "true",
          "tipo" => "exito",
          "id" => $tarea->id,
          "proyectoId" => $proyecto->id,
          "mensaje" => "Actualizado Correctamente"
        ];
        echo json_encode(["respuesta" => $respuesta]);
      }

    }
  }

  public static function eliminar() {
    if($_SERVER["REQUEST_METHOD"] === "POST") {
      $proyecto = Proyecto::where("url", $_POST["proyectoId"]);
      session_start();
      
      if(!$proyecto || $proyecto->propietarioId !== $_SESSION["id"]) {
        $respuesta = [
          "tipo" => "error",
          "mensaje" => "Hubo un error al actualizar la tarea"
        ];
        echo json_encode($respuesta);
        return;
      } 

      $tarea = new Tarea($_POST);
      $resultado = $tarea->eliminar();

      if($resultado) {
        $resultado = [
          "resultado" => $resultado,
          "mensaje" => "Eliminado Correctamente"
        ];
      }
      echo json_encode($resultado);
    }
  }
}
