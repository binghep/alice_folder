<?php
class Model
{
      // NOTE - the model assumes that you have already created and populated a mySQL database

    public $tstring; // the string that will contain the evaluation corresponding to the user selection
    // con contains the connection object returned from a successful call to connect to the mySQL database
	public $con; // the object holding the connection to the database
    public $selection;  // Selection obtained from the user via the view - (but obtained from the controller)

    // class construction
    public function __construct(){

    } 

     public function selectDB() {

	    // set this instance of the Model's connection to the MySQL database connection
        $this->con = mysqli_connect('egon','C4131F13U4','1250','C4131F13U4','3307');
		$this->con= mysqli_connect('egon.cs.umn.edu','F14CS4131U110','14322','F14CS4131U110','3307');
        // result returned form query for value corresponding to user selection (this->selection)
		$result = mysqli_query($this->con,"SELECT * FROM mySport WHERE name ='". $this->selection ."'");
        //map the the selection (record from the database- an object) stored in $result
		// to an indexable array
		$row = mysqli_fetch_array($result);

		// set tstring to the value stored in the array index (key) 'evaluation"
		// indicies to the array name are the same as the fieldnames in the database
        $this->tstring = $row['sport'];

    }
}

?>
