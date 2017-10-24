<?php include "../inc/dbinfo.inc"; ?>
<?php

use Aws\S3\Exception\S3Exception;
 
require 'app/start.php';


    if(isset($_FILES['file'])){
     
	$file = $_FILES['file'];
 
    // Details of the file
    $file_name = $file['name'];
    $tmp_file_name = $file['tmp_name'];
 
    $extensin = explode('.', $file_name);
	$extensin = strtolower(end($extensin));
	
	// Temporary file details
    $key = md5(uniqid());
    $tmp_filename = "{$key}.{$extensin}";
    $tmp_filepath = "files/{$tmp_filename}";
 
    // Move the file to the destination
    move_uploaded_file($tmp_file_name, $tmp_filepath);
 
    try { 
            $s3->putObject([
            'Bucket' => $config['s3']['bucket'],
            'Key' => "Uploads/{$file_name}",
            'Body' =>  fopen($tmp_filepath, 'rb'),
            'ACL' => 'public-read'
        ]);
 
        // Remove the file from the folder
        unlink($tmp_filepath);
 
    } 
	
	//If exception is generated catch it.
	catch (S3Exception $e) {
    die("Error in uploading the file");
         
    }
	
	$connection = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD);
    if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

    $database = mysqli_select_db($connection,DB_DATABASE);
		
    date_default_timezone_set('America/Los_Angeles');
	$date = date('m/d/Y h:i:s a', time());
	session_start();
	
	$f = mysqli_real_escape_string($connection, $_SESSION['firstname']);
	$l = mysqli_real_escape_string($connection, $_SESSION['lastname']);
	$u = mysqli_real_escape_string($connection,	$_SESSION['username']);
    $d = mysqli_real_escape_string($connection, $date);
    $fi = mysqli_real_escape_string($connection, $file_name);
	
	
	$result = mysqli_query($connection, "SELECT * FROM file_details where username='$u' and filedescription='$file_name'");
	 
	if(mysqli_num_rows($result)==0) { 
		$query = "INSERT INTO `file_details` (`Firstname`,`Lastname`,`Username`,`Fileuploadtime`,`Fileupdatetime`,`Filedescription`) VALUES ('$f','$l','$u','$d','$d','$fi');";
	} else if (mysqli_num_rows($result)==1) {
		$date_update = date('m/d/Y h:i:s a', time());
		$du = mysqli_real_escape_string($connection, $date_update);
		$query = "UPDATE `file_details` SET Fileupdatetime = '$du' where username='$u' and filedescription='$file_name';";
	} else {
		echo("<p>Error adding file details.</p>");
	}		 
    if(!mysqli_query($connection, $query)) echo("<p>Error adding employee data.</p>");
}

?>


<?php
          
		     require 'app/start.php';      
				
				if (isset($_POST['delete'])) {
                    $s3->deleteObject([ 
                    'Bucket' => $config['s3']['bucket'],
                    'Key' => $_POST['Deletekey']
                    ]);
					
				}	
			$connection = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD);
            if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();
            $database = mysqli_select_db($connection,DB_DATABASE);
				
			$fi = mysqli_real_escape_string($connection,$_POST['Deletekey']);
			$query = "Delete FROM file_details where Filedescription='$fi';";
			$result = mysqli_query($connection, $query);					
?>

<!DOCTYPE html>
<html lang="en">
<H1>
<meta charset="UTF-8">
<style>
.button {
    background-color: Orange;
    border-radius: 12px;
    color: black;
    padding: 14px 40px;
    text-align: center;
    text-decoration: underline;
    display: inline-block;
    font-size: 20px;
    margin: 4px 2px;
    cursor: pointer;
}
</style>
</H1>
<body bgcolor="#FFEFD5">
<center> <h1 style="background-color:Orange;">Upload your Art</h1></center>
<center><img src="https://s3-us-west-1.amazonaws.com/projreqfiles2017/Art_1.jpg" alt="test image" height="230" width="710"></center>
<center>
<h4>Want To Upload Your Art?</h4>
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="post" enctype="multipart/form-data">
<input type="file"      name="file">
<input type="submit" value="Upload Your Art">
</form>
</center></body>
</html>

<?php

   $connection = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD);
   if (mysqli_connect_errno()) echo "Failed to connect to MySQL: ".mysqli_connect_error();
   session_start();
   $database = mysqli_select_db($connection,DB_DATABASE);
   $u = mysqli_real_escape_string($connection,	$_SESSION['username']);
  
   $result = mysqli_query($connection, "SELECT * FROM file_details where username='$u';");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Listings</title>
	<style>
    table, th, td {
    border: 1px solid black;
    }
    </style>
    </head>
    <body>
	<br></br>
	<center>
        <table cellspacing="10">
		<thead>
		<tr>		
		    <th>First Name</th>
            <th>Last Name</th>
            <th>Upload Time</th>
            <th>Update Time</th>			
		    <th>Your Art</th>
			<th>View</th>
			<th>Download</th>			
        </tr>
		</thead>
		<tbody>		
		<?php while($query_data = mysqli_fetch_row($result)) { ?>
		<tr>
		<td><?php echo $query_data[1]; ?></td>
		<td><?php echo $query_data[2]; ?></td>
		<td><?php echo $query_data[4]; ?></td>
		<td><?php echo $query_data[5]; ?></td>
		<td><?php echo $query_data[6]; ?></td>
		<td><a href="<?php echo $s3->getObjectUrl($config['s3']['bucket'], "Uploads/{$query_data[6]}");?>"> View Files </a></td>
		<td><a href="<?php echo $s3->getObjectUrl($config['s3']['bucket'], "Uploads/{$query_data[6]}");?>" download=" <?php "Uploads/{$query_data[6]}"; ?>" >Download Files</a></td>
		</tr>
		<?php } ?>
		<tbody>
		</table>
	</center>
    </body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Delete</title>
</head>
<body>
<center>
<h4>Want To Delete Your Art?</h4>
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>"  method="post">
<input type="text" name="Deletekey">
<input type="submit" name="delete" value="Delete Your Art">
</form>
<center>
</body>
</html>
	
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Delete</title>
</head>
<body>	
<h4>Want To Update Your Art?</h4>
<center>
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="post" enctype="multipart/form-data">
<input type="file"      name="file">
<input type="submit" value="Update Your Art">
</form>
</center></body>
</html>
	
<!DOCTYPE html>
<html lang="en">
<body>
<center>
<a href="Logout.php"><h4 style="color:Black;">Logout</h4></a>
</center></body>
</html>

