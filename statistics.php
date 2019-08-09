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
	
	
	/* statistics */
	
	$sql = "SELECT COUNT(DISTINCT coordinates) AS 'coordinates', COUNT(DISTINCT ip_address) AS 'ip_address', COUNT(session_id) AS 'session_id', COUNT(DISTINCT user_id) AS 'user_id' FROM session_table";
	
	if (count($_GET) != 0) {
		/*
		$query = array();
		
		$startDate = "SUBSTR(session_start, 1, 10)";
		$endDate = "SUBSTR(session_end, 1, 10)";
		$minDate = "'".$_GET["minDate"]."'";
		$maxDate = "'".$_GET["maxDate"]."'";

		$query[] = ($_GET["IncExcDate"] == "include")
			? "(SUBSTR(session_start, 1, 10) >= '".$_GET["minDate"]."' AND SUBSTR(session_end, 1, 10) <= '".$_GET["maxDate"]."')"
			: "(SUBSTR(session_start, 1, 10) > '".$_GET["maxDate"]."' OR SUBSTR(session_end, 1, 10) < '".$_GET["minDate"]."')";

		if (isset($_GET["IncExcTime"])) {
			$startTime = "SUBSTR(session_start, 12, 16)";
			$endTime = "SUBSTR(session_end, 12, 16)";
			$minTime = "'".$_GET["minTime"]."'";
			$maxTime = "'".$_GET["maxTime"]."'";

			$query[] = ($_GET["IncExcTime"] == "include")
				? "((".$startDate." = ".$endDate.") AND (".$startTime." >= ".$minTime." AND ".$endTime." <= ".$maxTime."))"
				: "(((".$startDate." = ".$endDate.") AND (".$startTime." > ".$maxTime." OR ".$endTime." < ".$minTime.")) OR ((".$startDate." != ".$endDate.") AND (".$startTime." > ".$maxTime." AND ".$endTime." < ".$minTime.")))";
		}

		if ($_GET["verbalization"] != "") {
			$query[] = "verbalization = ".$_GET["verbalization"];
		}

		if ($_GET["holiday"] != "") {
			$query[] = "holiday = ".$_GET["holiday"];
		}
		
		$sql .= " WHERE ".implode(" AND ", $query);
		*/
		$sql .= queryConstraints();
	}
		
	//var_dump($sql);
	
	$result = mysqli_query($con, $sql);
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$statistics = $row;
	}
	
	//var_dump($statistics);
	
	echo json_encode($statistics);
	

	mysqli_close($con);
?>
