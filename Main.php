<?php include "../inc/dbinfo.inc"; ?>
<html>
<body bgcolor="#FFEFD5">
<center><h1 style="background-color:Orange;">Want to Post Your Art??</h1>

<?php

   /* Connect to MySQL and select the database. */
   $connection = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD);
   
   if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();
   
   $database = mysqli_select_db($connection,DB_DATABASE);
   
  
   /* Ensure that the user_details table exists.*/
   Verifyuser_credsTable($connection,DB_DATABASE);
   
   function Verifyuser_credsTable($connection,$dbName) {
   if(TableExistsMain("user_details", $connection,DB_DATABASE))
   {
    
   $username = htmlentities($_POST['Username']);
   $password = htmlentities($_POST['Password']);
   	
	if (strlen($username) || strlen($password)) {

	$result = mysqli_query($connection, "SELECT * FROM user_details"); 
    $var = True;
	while($query_data = mysqli_fetch_row($result)) {
	if ($query_data[3] == $username  && $query_data[4] == $password )
	{
	session_start();
    $_SESSION['firstname'] = $query_data[1];
	$_SESSION['lastname'] = $query_data[2];	
	$_SESSION['username'] = $query_data[3];	
	header('Location: http://www.salonibox.com/Features.php');
	$var = False;
	exit();
    }
	}   
     
	if ($var == True)
	{
	echo("<html>");
    echo("<center>");	
    echo("<br>");
	echo("<br>");
	echo("<h1>User Account Is Absent!!</h1>");
	echo("<h2>Please Create An Account!!</h2>");
	echo("</center>");
	echo("</html>");
	exit();
	}
																
	}

    }
	else
	{ if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
	  exit();
	}	
}
  function TableExistsMain($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);
  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;
  else
  return false;
  }

?>

<img src="https://s3-us-west-1.amazonaws.com/projreqfiles2017/Art.jpg" height="400" width="1500">
</img>
<center><h1 style="background-color:Orange;">Login</h1></br>
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <b>Username :</b><input type="text" name="Username" maxlength="20" size="15" />
  <br></br>
  <b>Password :</b><input type="password" name="Password" maxlength="20" size="15" />
  <br></br>
  <input type="submit" value="Submit">
</form>
<a href="Createaccnt.php"><h2 style="color:Black;">New User?</h2></a>
</body>
</html>
