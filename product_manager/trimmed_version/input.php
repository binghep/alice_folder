
<?php

  // Start the session
  session_start();
  
  $error_message = '';
  
  if (! empty($_POST)) {
    if (isset($_POST['clear'])) {
      /* clear events in session */
      unset($_SESSION['locations']);
      
      /* redirect the user to the calendar page */
      header('Location: calendar.php');
      exit();
    }
  
	/* do input validation */
	/* you insert the code to do input validation here!!!! */

	$event_day=$_POST['event_day'];
	$event_name=$_POST['event_name'];
	$event_start=$_POST['event_start'];
	$event_end=$_POST['event_end'];
	$event_loc=$_POST['event_loc'];

  	//if the _POST array exist and not equal to false, which means someone requested a post action by pressing the submit or clear button on the form on this page.

	//empty($variable) returns true if it doesn't exist or if its value equals false. 
	if ( empty($event_name) ) {
        $error_message .= 'Event Name is a required field.'; 
	}
	if(empty($event_start) ) {
        $error_message .= '<br /> Start Time is a required field.'; 
	}
	if(empty($event_end) ) {
        $error_message.= '<br /> End Time is a required field.'; 
	}
	if(empty($event_loc) ) {
        $error_message.= '<br /> Location is a required field.'; 
	}
	
  
    /* if loading this page for the first time, set _SESSION['locations'] array */	
	//isset(var) returns true if var exists and has value other than NULL.
      if (! isset($_SESSION['locations'])) {
        $_SESSION['locations']['Mon']='';
        $_SESSION['locations']['Tue']='';
        $_SESSION['locations']['Wed']='';
        $_SESSION['locations']['Thu']='';
        $_SESSION['locations']['Fri']='';
      }
      //echo $error_message;
	//passed input validation, append an element to the _SESSION['locations']['Mon'] array + redirect to calendar.php
      if (empty($error_message)){//Fact: empty string equals false.      
      		$_SESSION['locations'][$_POST['event_day']][] = array('event_name'=>$_POST['event_name'], 'event_start'=>$_POST['event_start'], 'event_end'=>$_POST['event_end'], 'event_loc'=>$_POST['event_loc']);

      		/* redirect browser */
      		header('Location: calendar.php');
      		/*Make sure that the code below does not get executed when we redirect.*/
		      exit();
      }
}
 ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Calendar Input</title>
    <link href="Style.css" type="text/css" rel="stylesheet">
</head>

<body class="body_TiledCartoonBricks">
   <!--ice cream image header-->
    <img src="S_strawberry-ice-cream.png" width="60"  alt="ice cream picture">

   <!--nav bar-->
   <div class ="container">
        <nav>
            <span class="link_w_background">
                <a href="calendar.php">Calendar</a>
            </span>

            <span class="link_w_background">
                <a href="input.php">Input New Events</a>
            </span>

        </nav>
    
  	 <div style="color:red"><?php echo $error_message;?></div>
    
     <form class="silver" method="post" action="input.php" >
        <p>
   	 <label for="event_start">Event Name:</label>
   	 <input type="text" name="event_name">
   	 </p>
   	 <p>
   	 <label for="event_day">Event Day:</label>
   	 <select name="event_day">
    	   <option value="Mon">Mon</option>
    	   <option value="Tue">Tue</option>
    	   <option value="Wed">Wed</option>
   	   <option value="Thu">Thu</option>
    	   <option value="Fri">Fri</option>
  	 </select>
   	 </p>
   	 <p>
   	 <label for="event_start">Start Time:</label>
   	 <input type="time" name="event_start">
   	 </p>
   	 <p>
   	 <label for="event_end">End Time:</label>
   	 <input type="time" name="event_end">
   	 </p>
   	 <p>
  	 <label for="event_loc">Location:</label>
   	 <input type="text" name="event_loc">
   	 </p>
   	 <p>
   	 <button type="submit">Submit</button> &nbsp;
   	 <button type="submit" name="clear">Clear Calendar</button>
   	 </p>
   </form>
 </div>
</body>

</html>
