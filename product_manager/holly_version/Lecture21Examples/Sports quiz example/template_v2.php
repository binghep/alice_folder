<!DOCTYPE html>
<html>
 <head>
  <meta charset='charset=utf-8'>
  <title>Gopher Sports Hero Quiz</title>
  <script>
  // checks to make sure an option has been selected
      function check(n){
	var x = document.getElementsByName(n)
	for( var i=0; i< x.length; i++)
		if (x[i].type=='radio' && x[i].checked)
			return true;
	alert("No choice has been made");
	return false;
      } 
  </script>
 </head>
 <body>

  <div>
     <h1><font color="#0033CC"> Sports </font></h1>
          <p>
	       Choose Famous Gopher Alumni to find out what sport they played
          </p>

          <form method="get" action ="mvc_v2.php"  onSubmit="return check('option');">
              <input type="radio" name="option" value="McHale" />McHale</br>
              <input type="radio" name="option" value="Giel" />Giel</br>
              <input type="radio" name="option" value="Molitor" />Molitor</br>
              <input type="radio" name="option" value="Bonin" />Bonin</br>
              <input type="submit" value="Submit" />
          </form>
  </div>
  <br>
  <br>
  <div>
      <h3> Sport They Played:</h1>
      <h3><font color= #FF0000><?php echo $data; ?></font></h1>
  </div>
 </body>
</html>
