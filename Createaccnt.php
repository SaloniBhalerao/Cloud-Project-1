<?php include "../inc/dbinfo.inc"; ?>
<html>
<body bgcolor="#FFEFD5">
<center><h1 style="background-color:Orange;">Create Account</h1>

<?php
  /* Connect to MySQL and select the database. */
  $connection = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD);
  if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

  $database = mysqli_select_db($connection,DB_DATABASE);

  /* Ensure that the user_details table exists.*/
  Verifyuser_detailsTable($connection,DB_DATABASE);
  
  /* Ensure that the file_details table exists.*/
  Verifyfile_detailsTable($connection,DB_DATABASE);

  /*Check whether the table exists and, if not, create it.*/
  function Verifyuser_detailsTable($connection,$dbName) {
  if(!TableExists("user_details", $connection,DB_DATABASE))
  {
  $query = "CREATE TABLE `user_details` (
         `ID` int(11) NOT NULL AUTO_INCREMENT,
	     `Firstname` varchar(20) DEFAULT NULL,
         `Lastname` varchar(20) DEFAULT NULL,
         `Username` varchar(20) DEFAULT NULL,
         `Password` varchar(20) DEFAULT NULL,
         PRIMARY KEY (`Username`),
         UNIQUE KEY `ID_UNIQUE` (`ID`)
       ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";

  if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
  }
  }
  
  /*Check whether the table exists and, if not, create it.*/
  function Verifyfile_detailsTable($connection,$dbName) {
  if(!TableExists("file_details", $connection,DB_DATABASE))
  {
  $query = "CREATE TABLE `file_details` (
         `ID` int(11) NOT NULL AUTO_INCREMENT,
	     `Firstname` varchar(20) NULL,
         `Lastname` varchar(20) NULL,
         `Username` varchar(20) NOT NULL,
         `Fileuploadtime` varchar(50) NULL,
         `Fileupdatetime` varchar(50) NULL,
         `Filedescription` varchar(45) DEFAULT NULL,
         PRIMARY KEY (`Username`, `Filedescription`),
         UNIQUE KEY `ID_UNIQUE` (`ID`)
       ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";

  if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
  }
  }
  
  

  /* Check for the existence of a table. */
  function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);
  
  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;
  else
  return false;
  }

   /*If input fields are populated, add a row to the User_details table. */
   $firstname = htmlentities($_POST['Firstname']);
   $lastname = htmlentities($_POST['Lastname']);
   $username = htmlentities($_POST['Username']);
   $password = htmlentities($_POST['Password']);

   if (strlen($firstname) || strlen($lastname) || strlen($username) || strlen($password) ) {
   AddUser($connection, $firstname, $lastname, $username, $password);
  }

?>

  <img src="https://s3-us-west-1.amazonaws.com/projreqfiles2017/Acc_Img.jpg" alt="test image" height="400" width="500">
  </img>
  <h1 style="background-color:Orange;"></h1>
  <form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <b>Firstname:</b><input type="text" name="Firstname" maxlength="20" size="15" />
  <br></br>
  <b>Lastname:</b><input type="text" name="Lastname" maxlength="20" size="15" />
  <br></br>
  <b>Username:</b><input type="text" name="Username" maxlength="20" size="15" />
  <br></br>
  <b>Password:</b><input type="password" name="Password" maxlength="20" size="15" />
  <br></br>
  <input type="submit"  value="Create Account" />
  </form>
  </center>
  <a href="Main.php"><center><h5 style="color:Black;">Back To MainPage</h5></center></a>
  
  <!-- Clean up. -->
  <?php
  mysqli_free_result($result);
  mysqli_close($connection);
  ?>
  </body>
  </html>


<?php
   /*Add an User to the table.*/
   function AddUser($connection, $fname, $lname, $uname, $pwd) {
   $result = mysqli_query($connection, "SELECT * FROM user_details"); 

   while($query_data = mysqli_fetch_row($result)) {
   if ($query_data[3] == $uname)
   {
	 echo("<html>");
	 echo("<center>");	 
	 echo("<h1>User Already Exists!!</h1>");
	 echo("</center>");
	 echo("</html>");
     exit();
   }	   
}   
	   
   $f = mysqli_real_escape_string($connection, $fname);
   $l = mysqli_real_escape_string($connection, $lname);
   $u = mysqli_real_escape_string($connection, $uname);
   $p = mysqli_real_escape_string($connection, $pwd);
   
   $query = "INSERT INTO `user_details` (`Firstname`,`Lastname`,`Username`,`Password`) VALUES ('$f','$l','$u','$p');";

   if(!mysqli_query($connection, $query)) echo("<p>Error adding employee data.</p>");
}
?>


