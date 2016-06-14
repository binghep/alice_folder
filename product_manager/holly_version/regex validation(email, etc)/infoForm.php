<!DOCTYPE html>


<!-- Process information sent from infoform.html. -->
<html>
   <head>
      <meta charset = "utf-8">
      <title>Form Validation</title>
      <style type = "text/css">
         p       { margin: 0px; }
         .error  { color: red }
         p.head  { font-weight: bold; margin-top: 10px; }
      </style>
   </head>
   <body>
      <?php
         extract( $_POST );  // gets all the info in the post into php variables
		                     // that match the names in the form

         // determine whether phone number is valid and print
         // an error message if not
         if (!preg_match( "/^\([0-9]{3}\) [0-9]{3}-[0-9]{4}$/", $phone))
         {
            print( "<p class = 'error'>Invalid phone number</p>
               <p>A valid phone number must be in the form 
               (555) 555-5555</p><p>Click the Back button, 
               enter a valid phone number and resubmit.</p>
               <p>Thank You.</p>" );
            die( "</body></html>" ); // terminate script execution
         }
      ?><!-- end PHP script -->
      <p>Hi <?php print( "$fname" ); ?>. Thank you for completing the 
         survey. You have been added to the <?php print( "$book " ); ?>
         mailing list.</p>
      <p class = "head">The following information has been saved 
         in our database:</p>
      <p>Name: <?php print( "$fname $lname" ); ?></p>
      <p>Email: <?php print( "$email" ); ?></p>
      <p>Phone: <?php print( "$phone" ); ?></p>
      <p>OS: <?php print( "$sport" ); ?></p>
      <p class = "head">This is only a sample form.       
         You have not been added to a mailing list.</p>   
   </body>
</html>

