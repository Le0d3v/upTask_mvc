<?php 
namespace Model;

class Proyecto extends ActiveRecord {
  protected static $tabla = "proyectos";
  protected static $columnasDB = ["id", "proyecto", "url", "propietarioId"];

  public $id;
  public $proyecto;
  public $url;
  public $propietarioId;

  public function __construct($args = []) {
    $this->id = $args["id"] ?? null;
    $this->proyecto = $args["proyecto"] ?? null;
    $this->url = $args["url"] ?? null;
    $this->propietarioId = $args["propietarioId"] ?? null;
  }

  public function validarProyecto() : array {
    if(!$this->proyecto) {
      self::$alertas["error"][] = "Debes colocar un nombre al proyecto";
    }

    return self::$alertas;
  }
}
?>