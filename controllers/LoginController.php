<?php 
namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
  /** Funcionamiento de la zona del login
   * @return void 
   */
  public static function login(Router $router) : void {
    $alertas = [];
    if($_SERVER["REQUEST_METHOD"] === "POST") {
      $auth = new Usuario($_POST);
      $alertas = $auth->validarLogin();

      if(empty($alertas)) {
        $usuario = Usuario::where("email", $auth->email);

        if(!$usuario || !$usuario->confirmado) {
          Usuario::setAlerta("error", "El usuario no existe o no está confirmado");
        } else { // El usuaio existe y está confirmado
          if(password_verify($_POST["password"], $usuario->password)) {
            session_start();
            $_SESSION["id"] = $usuario->id;
            $_SESSION["nombre"] = $usuario->nombre;
            $_SESSION["email"] = $usuario->email;
            $_SESSION["login"] = true;
            
            header("Location: /dashboard");
          } else {
            Usuario::setAlerta("error", "Datos de acceso incorrectos o cuenta no confirmada");
          }
        }
      }
    }

    $alertas = Usuario::getAlertas();

    $router->render("auth/login", [
      "titulo" => "Login",
      "alertas" => $alertas
    ]);
  }

  /** Cierre de Sesión 
   * @return void
  */
  public static function logout() : void {
    session_start();
    $_SESSION = [];
    header("Location: /");
  }

  /** Crear nuevas cuentas
   * @return  void 
   */
  public static function crear(Router $router) : void {
    $usuario = new Usuario;
    $alertas = [];

    if($_SERVER["REQUEST_METHOD"] === "POST") {
      $usuario->sincronizar($_POST);
      $alertas = $usuario->validarNuevaCuenta();
      
      // Validación de campos
      if(!empty($alertas)) {
        $existeUsuario = Usuario::where("email", $usuario->email);
        if($existeUsuario) { // Revisar si un usuario ya existe
          Usuario::setAlerta("error", "El email ya existe en otra cuenta");
          $alertas = Usuario::getAlertas();
        }
      } else {
        $usuario->hashPassword(); // Hashear password

        unset($usuario->password2); // Eliminar el segundo password del objeto

        $usuario->createToken(); // Crea token

        $resultado = $usuario->guardar(); // Guardar en BD
        
        if($resultado) {
          $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
          $email->enviarConfirmacion();
          header("Location: /mensaje");
        }
      }
    }

    $router->render("auth/crear", [
      "titulo" => "Crear Cuenta",
      "usuario" => $usuario,
      "alertas" => $alertas
    ]);
  }

  /** procedimiento de reestablecimiento de password 
   * @return void
  */
  public static function olvide(Router $router) : void {
    $alertas = [];
    if($_SERVER["REQUEST_METHOD"] === "POST") {
      $usuario = new Usuario($_POST);
      $alertas = $usuario->validarEmail(); // Validar la entrada de email
      
      if(empty($alertas)) {
        $usuario = Usuario::where("email", $usuario->email);
        
        if($usuario && $usuario->confirmado) { // Usuario encontrado y confirmado
          $usuario->createToken();
          unset($usuario->password2);
          $usuario->guardar();
          $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
          $email->enviarInstrucciones();

          Usuario::setAlerta("exito", "Instrucciones enviadas a tu email");
        } else {
          Usuario::setAlerta("error", "El usuario no existe o no está confirmado");
        }
      }
    }

    $alertas = Usuario::getAlertas();

    $router->render("auth/olvide", [
      "titulo" => "Olvidé mi Password",
      "alertas" => $alertas
    ]);
  }

  /** Reestablecimiento de password
   * @return void
   */
  public static function reestablecer(Router $router) : void {
    $token = s($_GET["token"]);
    $mostrar = true;
    
    if(!$token) { // Validar existencia de token 
      header("Location: /");
    }
    
    $usuario = Usuario::where("token", $token);
    
    if(empty($usuario)) {
      Usuario::setAlerta("error", "Token no valido");
      $mostrar = false;
    }

    if($_SERVER["REQUEST_METHOD"] === "POST") {
      // Añadir el nuevo password
      $usuario->sincronizar($_POST);

      // Validar password
      $alertas = $usuario->validarPassword();

      if(empty($alertas)) {
        $usuario->hashPassword();
        $usuario->token = null;
        $resultado = $usuario->guardar();

        if($resultado) {
          header("Location: /?resultado=1");
        }
      }
    }

    $alertas = Usuario::getAlertas();

    $router->render("auth/reestablecer", [
      "titulo" => "Reestablece tu Password",
      "alertas" => $alertas,
      "mostrar" => $mostrar
    ]);
  }

  /** Mensaje de confirmación */
  public static function mensaje(Router $router) {
    $router->render("auth/mensaje", [
      "titulo" => "Cuenta creada correctamente"
    ]);
  }

  /** Confirmación de cuenta
   * @return void
   */
  public static function confirmar(Router $router) : void {
    $error = false;
    $token = s($_GET["token"]); // Obtener el token

    if(!$token) {
      header("Location: /"); // Redirección en caso de no existir un token
    }

    $usuario = Usuario::where("token", $token); // Encontrar al usiario

    if(empty($usuario)) {
      // Mostrar alerta en caso de que no exista el usuario
      Usuario::setAlerta("error", "Token no valido");
      $error = true;
    } else {
      // Confirmar la cuenta y ajustar campos
      $usuario->confirmado = 1;
      $usuario->token = null;
      unset($usuario->password2);
      $usuario->guardar();
      Usuario::setAlerta("exito", "Cuenta comprobada correctamente"); // Mensaje de exito
    }

    $alertas = Usuario::getAlertas();
    $router->render("auth/confirmar", [
      "titulo" => "Confirmar cuenta UpTask",
      "alertas" => $alertas,
      "error" => $error
    ]);
  }
}
?>