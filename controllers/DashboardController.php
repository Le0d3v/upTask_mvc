<?php 
namespace Controllers;

use Model\Breadcrumb;
use MVC\Router;
use Model\Usuario;
use Model\Proyecto;

class DashboardController {
  public static function index(Router $router) {
    session_start();
    isAuth();

    $breadcrumb = new Breadcrumb();
    $breadcrumb->add("UpTask", "/dashboard");
    $breadcrumb->add("Proyectos", "/dashboard");

    $proyectos = Proyecto::belongsTo("propietarioId", $_SESSION["id"]);
    
    $router->render("dashboard/index", [
      "titulo" => "Proyectos",
      "proyectos" => $proyectos,
      "enlace" => "project",
      "breadcrumbs" => $breadcrumb->getBreadcrumbs()
    ]);
  }

  public static function crear_proyecto(Router $router) {
    session_start();
    isAuth();

    $breadcrumb = new Breadcrumb();
    $breadcrumb->add("UpTask", "/dashboard");
    $breadcrumb->add("Proyectos", "/dashboard");
    $breadcrumb->add("Crear Proyecto", "/crear-proyecto");

    $alertas = [];

    if($_SERVER["REQUEST_METHOD"] === "POST") {
      $proyecto = new Proyecto($_POST);
      
      // Validar
      $alertas = $proyecto->validarProyecto();

      if(empty($alertas)) { 
        $proyecto->url = md5(uniqid()); // Generar URL 
        $proyecto->propietarioId = $_SESSION["id"]; // Asignar un propietario
        $proyecto->guardar();

        header("Location: /proyecto?proyecto=" . $proyecto->url);
      }
    }

    $router->render("dashboard/crear-proyecto", [
      "titulo" => "Crear Proyecto",
      "alertas" => $alertas,
      "breadcrumbs" => $breadcrumb->getBreadcrumbs()
    ]);
  }

  public static function perfil(Router $router) {
    session_start();
    isAuth();

    $breadcrumb = new Breadcrumb();
    $breadcrumb->add("UpTask", "/dashboard");
    $breadcrumb->add("Perfil", "/perfil");
    
    $alertas = [];
    $usuario = Usuario::find($_SESSION["id"]);
    
    if($_SERVER["REQUEST_METHOD"] === "POST") {
      $usuario->sincronizar($_POST);
      $alertas = $usuario->validarPerfil();

      if(empty($alertas)) {
        // Verificar si el email ya existe en otro usuario
        $existeUsuario = Usuario::where("email", $usuario->email);
        
        if($existeUsuario && $existeUsuario->id !== $usuario->id) {
          Usuario::setAlerta("error", "El email ya existe en otra cuenta");
          $alertas = Usuario::getAlertas();
        } else {
          $usuario->guardar();
          // Generar una alerta
          Usuario::setAlerta("exito", "Guardado correctamente");
          $alertas = Usuario::getAlertas();
          $_SESSION["nombre"] = $usuario->nombre; // Modificar el nombre en la barra 
        }

      }
    }

    $router->render("dashboard/perfil", [
      "titulo" => "Perfil", 
      "usuario" => $usuario, 
      "alertas" => $alertas,
      "enlace" => "profile",
      "breadcrumbs" => $breadcrumb->getBreadcrumbs()
    ]);
  }

  public static function proyecto(Router $router) {
    session_start();
    isAuth();

    $url = s($_GET["proyecto"]);
    if(!$url) header("Location: /dashboard"); // Redireccion

    // Revisar que quien revisa el proyecto es quein lo creó
    $proyecto = Proyecto::where("url", $url);

    if($proyecto->propietarioId !== $_SESSION["id"]) {
      header("Location: /dashboard"); // Redirección en caso de ser otro usuario
    }

    $breadcrumb = new Breadcrumb();
    $breadcrumb->add("UpTask", "/dashboard");
    $breadcrumb->add("Proyectos", "/dashboard");
    $breadcrumb->add($proyecto->proyecto, "/proyecto?proyecto=" . $proyecto->url);

    $router->render("dashboard/proyecto", [
      "titulo" => $proyecto->proyecto,
      "enlace" => "project",
      "breadcrumbs" => $breadcrumb->getBreadcrumbs()
      
    ]);
  }

  public static function cambiar_password(Router $router ) {
    session_start();
    isAuth();

    $breadcrumb = new Breadcrumb();
    $breadcrumb->add("UpTask", "/dashboard");
    $breadcrumb->add("Perfil", "/perfil");
    $breadcrumb->add("Cambiar Password", "/cambiar-password");
    
    $alertas = [];

    if($_SERVER["REQUEST_METHOD"] === "POST"){
      $usuario = Usuario::find($_SESSION["id"]);
      $usuario->sincronizar($_POST);
      $alertas = $usuario->nuevo_password();
      
      if(empty($alertas)) {
        $resultado = $usuario->comprobar_password();
        if($resultado) {
          $usuario->password = $usuario->password_nuevo; // Asignar el nuevo password
          
          // Eliminar propiedades no nesearias
          unset($usuario->password_actual);
          unset($usuario->password_nuevo);

          $usuario->hashPassword(); // Hashear el nuevo password

          $resultado = $usuario->guardar(); // Almacenar el nuevo password

          if($resultado) {
            // Mostrar mensaje de error
            Usuario::setAlerta("exito", "Password guardado correctamente");
            $alertas = Usuario::getAlertas();
          }
        } else  {
          // Mostrar mensaje de error
          Usuario::setAlerta("error", "El Password es incorrecto");
          $alertas = Usuario::getAlertas();
        }
      }
    }

    $router->render("/dashboard/cambiar-password", [
      "titulo" => "Cambiar Password", 
      "alertas" => $alertas,
      "enlace" => "profile",
      "breadcrumbs" => $breadcrumb->getBreadcrumbs()
    ]);
  }
}
?>