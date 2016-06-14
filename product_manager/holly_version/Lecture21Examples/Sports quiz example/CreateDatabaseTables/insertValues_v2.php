<?php
$con= mysqli_connect('egon.cs.umn.edu','F14CS4131U110','14322','F14CS4131U110','3307');
// Check connection
if (mysqli_connect_errno())
  {
  echo 'Failed to connect to MySQL:' . mysqli_connect_error();
  }

mysqli_query($con,"INSERT INTO mySport (name, sport) VALUES ('McHale', 'Basketball')");

mysqli_query($con,"INSERT INTO mySport (name, sport)  VALUES ('Giel', 'Football')");

mysqli_query($con,"INSERT INTO mySport (name, sport) VALUES ('Molitor', 'Baseball')");

mysqli_query($con,"INSERT INTO mySport (name, sport)VALUES ('Bonin', 'Hockey')");

mysqli_close($con);


echo '<h1> 
Successfully Inserted Values into the Table </h1>'
?> 
