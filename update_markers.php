<?php
	include 'utility.php';
	
	$keys = array("username", "password", "database");
	
	$file = fopen("credentials.txt", "r") or die("unable to open file");

	while(!feof($file)) {
		$credentials[array_shift($keys)] = chop(fgets($file));
	}
	
	fclose($file);
	
	//var_dump($credentials);
	
	$con = mysqli_connect("localhost", $credentials["username"], $credentials["password"], $credentials["database"]);
	
	if (!$con) {
		die("could not connect: ".mysqli_error($con));
	}

	mysqli_select_db($con, $credentials["database"]);
	
	$sql = "SELECT coordinates, COUNT(DISTINCT ip_address) AS 'ip_address', COUNT(session_id) AS 'session_id', COUNT(DISTINCT user_id) AS 'user_id' FROM session_table";
	
	if (count($_GET) != 0) {
	
		$sql .= queryConstraints();
	}
	
	$sql .= " GROUP BY coordinates";
		
	//var_dump($sql);
	
	$result = mysqli_query($con, $sql);
	
	$coordinates = array();
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$coordinates[] = $row;
	}
	
	//var_dump($coordinates);
	
	echo json_encode($coordinates);
	

	mysqli_close($con);
?>
