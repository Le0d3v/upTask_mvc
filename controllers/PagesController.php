<?php
namespace Controllers;

use MVC\Router;

Class PagesController {
  public static function error(Router $router) {
    $router->render("templates/error", [
      "titulo" => "Error 404",
    ]);
  }
}