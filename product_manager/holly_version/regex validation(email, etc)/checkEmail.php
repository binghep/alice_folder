<!DOCTYPE html>


<html>
   <head>
      <meta charset="utf-8">
      <title>checkEmail</title>
   </head>
   <body>
      <?php
         if ( isset( $_POST[ "email" ] ) ) 
         {
            $email = $_POST[ "email" ];

            // the regular expression below is from http://www.regular-expressions.info/tutorial.html
            if ( preg_match("/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i", $email) )
               print( "<p>$email seems to be a valid email address.</p>" );
            else
               print( "<p>$email is not a valid email address.</p>" );
         } // end if
         else 
         {
            print( "Enter an email address <form method = \"post\">
               <p><input type = \"text\" name = \"email\">
                  <input type = \"submit\" name = \"Submit\"></p>
               </form>" );
         } // end else
      ?><!-- end PHP script -->
   </body> 
</html>

