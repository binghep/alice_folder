<?php
class Model {
    public $text;
    
    public function __construct() {
        $this->text = 'Hello world!';
    }        
}

class View {
    private $model;
    private $controller;
    
    public function __construct(Controller $controller, Model $model) {
        $this->controller = $controller;
        $this->model = $model;
    }
    
    public function output() {
        return '<a href="HelloMVC_v2.php?action=textClicked">' . $this->model->text . '</a>';
    }
}

class Controller {
    private $model;

    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function textClicked() {
        $this->model->text = 'Text Updated';
    }
}

//initiate the triad
if (!isset($model) {
$model = new Model();
//The controller and the view typically share the model
$controller = new Controller($model);
$view = new View($controller, $model);
}
if (isset($_GET['action'])) $controller->{$_GET['action']}();
echo $view->output();

?>