<?php 
namespace Model;

class Usuario extends ActiveRecord {
  protected static $tabla = "usuarios";
  protected static $columnasDB = ["id", "nombre", "email", "password", "token", "confirmado"];

  public $id;
  public $nombre;
  public $email;
  public $password;
  public $password2;
  public $password_actual;
  public $password_nuevo;
  public $token;
  public $confirmado;

  public function __construct($args = []) {
    $this->id = $args["id"] ?? null;
    $this->nombre = $args["nombre"] ?? null;
    $this->email = $args["email"] ?? null;
    $this->password = $args["password"] ?? null;
    $this->password2 = $args["password2"] ?? null;
    $this->password_actual = $args["password_actual"] ?? null;
    $this->password_nuevo = $args["password_nuevo"] ?? null;
    $this->token = $args["token"] ?? null;
    $this->confirmado = $args["confirmado"] ?? 0;
  }

  /** Valida la entrada de datos en el login
   * @return array 
   */
  public function validarLogin() : array {
    if(!$this->email) {
      self::$alertas["error"][] = "El email es obigatorio";
    }
    if(!$this->password) {
      self::$alertas["error"][] = "El password el obligatorio";
    }

    return self::$alertas;
  }

  /** Valida la creación de nuevas cuentas de usuario
   * @return array
   */
  public function validarNuevaCuenta() : array {
    if(!$this->nombre) {
      self::$alertas["error"][] = "El nombre del Usuario es Obligatorio";
    }
    if(!$this->email) {
      self::$alertas["error"][] = "El email del Usuario es Obligatorio";
    }
    if(!$this->password) {
      self::$alertas["error"][] = "El password del Usuario es Obligatorio";
    }
    if(strlen($this->password) < 8) {
      self::$alertas["error"][] = "El password debe contener al menos 8 caracteres";
    }
    if($this->password != $this->password2) {
      self::$alertas["error"][] = "Los password no coinciden";
    }

    return self::$alertas;
  }

  /** Hashea el password del usuario
   * @return void
   */
  public function hashPassword() : void {
    $this->password = password_hash($this->password, PASSWORD_BCRYPT);
  }

  /** Crea un token único para uso del usuario
   * @return void 
   */
  public function createToken() : void {
    $this->token = md5(uniqid());
  }
  
  /** Valida el campo de email en el proceso de reestablecimiento de password
   * @return array 
   */
  public function validarEmail() : array {
    if(!$this->email) {
      self::$alertas["error"][] = "El email es obligatorio";
      if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
        self::$alertas["error"][] = "Email no valido";
      }
    }
    return self::$alertas;
  }

  /** Valida el reestablecimiento de un password
   * @return array
   */
  public function validarPassword() : array {
    if(!$this->password) {
      self::$alertas["error"][] = "El password del Usuario es Obligatorio";
    }
    if(strlen($this->password) < 8) {
      self::$alertas["error"][] = "El password debe contener al menos 8 caracteres";
    }

    return self::$alertas;
  }

  public function validarPerfil() : array {
    if(!$this->nombre) {
      self::$alertas["error"][] = "El nombre es obligatorio";
    }
    if(!$this->email) {
      self::$alertas["error"][] = "El email es obligatorio";
    }

    return self::$alertas;
  }

  public function nuevo_password() : array {
    if(!$this->password_actual) {
      self::$alertas["error"][] = "El password no puede ir vacio";
    }
    if(!$this->password_nuevo) {
      self::$alertas["error"][] = "Debes colocar el nuevo password";
    }
    if(strlen($this->password_nuevo) < 8) {
      self::$alertas["error"][] = "El password debe contener al menos 8 caracteres";
    }

    return self::$alertas;
  }

  public function comprobar_password() : bool {
    return password_verify($this->password_actual, $this->password);
  }
}
?>