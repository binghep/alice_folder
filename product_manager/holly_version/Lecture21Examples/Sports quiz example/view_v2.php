<?php


class View
{
    private $model;  // variable to hold an instance of the model class
    private $controller;  //variable to hold an instance of the controller class
 
    // constuctor stores pointers to the local model and controller variables
	// to the instances of the instances of the $controller and $model passed into iterator_apply
	// as parameters when it is instantiated
    public function __construct($controller,$model) {
        $this->controller = $controller;
        $this->model = $model;
    }
    
	// $data contains the tstring (evaluation of user section)
	// value that is set in the model when requested by the controller.
	// the view just accesses tstring, and data holds an HTML that includes the
	// value in tstring
    public function output(){
        $data = "<p>" . $this->model->tstring ."</p>";
        require_once('template_v2.php');
    }
}

?>
