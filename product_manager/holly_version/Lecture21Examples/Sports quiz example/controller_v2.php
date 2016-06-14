<?php
class Controller  // create the controller class
{
    private $model; // 
    
 
    public function __construct($model){ // model is stored locally in this class when instantiated
        $this->model = $model;
    }


    public function clicked($option) {  // is a parameter containing the user selection from the view
	                                    // so it it one of the sports figures selected 
        $this->model->selection = $option;

        $this->model->selectDB();// function in the model, takes option (stored in selection) and queries database
        
    }
    
 
}

?>
