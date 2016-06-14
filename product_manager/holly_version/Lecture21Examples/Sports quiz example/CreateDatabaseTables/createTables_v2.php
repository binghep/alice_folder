<?php
$con= mysqli_connect('egon.cs.umn.edu','F14CS4131U110','14322','F14CS4131U110','3307');

// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

// Create table
$sql="CREATE TABLE mySport(id INT NOT NULL AUTO_INCREMENT,
      name VARCHAR(20), 
      sport VARCHAR(20),
      PRIMARY KEY (id));";

// Execute query
if (mysqli_query($con,$sql))
  {
  echo "<h1> Table mySport created successfully </h1>";
  }
else
  {
  echo "<h1> Error creating table: <h1>" . mysqli_error($con);
  }

mysqli_close($con);

?> 
