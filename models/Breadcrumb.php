<?php 
namespace Model;

class breadcrumb {
  private $breadcrumbs = [];

    public function add($texto, $url) {
        $this->breadcrumbs[] = ['label' => $texto, 'url' => $url];
    }

    public function getBreadcrumbs() {
        return $this->breadcrumbs;
    }
}
?>