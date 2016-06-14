<?php 


//include the MVC objects
include('model_v2.php');
include('view_v2.php');
include('controller_v2.php');


//instantiate the mvc objects
$model = new Model();
$controller = new Controller($model);
$view = new View($controller, $model); // so view will display the template when it is instantiated
 

// get the option set in the global GET array, controller works with model to get the data 
// see the clicked function in the controller
if (isset($_GET['option'])) {
    $controller->clicked($_GET['option']); 
}




// once the data is obtained, call the output function in view to display the data obtained
echo $view->output();




?>
