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
	
	
	/* ip addresses */
	
	$sql = "SELECT DISTINCT ip_address FROM session_table";

	/*
	$query = array();
	
	$startDate = "SUBSTR(session_start, 1, 10)";
	$endDate = "SUBSTR(session_end, 1, 10)";
	$minDate = "'".$_GET["minDate"]."'";
	$maxDate = "'".$_GET["maxDate"]."'";
		
	//$query[] = (($_GET["IncExcDate"] == "include") ? "(SUBSTR(session_start, 1, 10) >= '".$_GET["minDate"]."' AND SUBSTR(session_end, 1, 10) <= '".$_GET["maxDate"]."')" : "(SUBSTR(session_start, 1, 10) > '".$_GET["maxDate"]."' OR SUBSTR(session_end, 1, 10) < '".$_GET["minDate"]."')");
	$query[] = "(".(($_GET["IncExcDate"] == "include")
		? "(SUBSTR(session_start, 1, 10) >= '".$_GET["minDate"]."' AND SUBSTR(session_end, 1, 10) <= '".$_GET["maxDate"]."')"
		: "(SUBSTR(session_start, 1, 10) > '".$_GET["maxDate"]."' OR SUBSTR(session_end, 1, 10) < '".$_GET["minDate"]."')"
	).")";

	if (isset($_GET["IncExcTime"])) {
		$startTime = "SUBSTR(session_start, 12, 16)";
		$endTime = "SUBSTR(session_end, 12, 16)";
		$minTime = "'".$_GET["minTime"]."'";
		$maxTime = "'".$_GET["maxTime"]."'";
		
		//$query[] = (($_GET["IncExcTime"] == "include") ? "(".$startDate." == ".$endDate.") AND (".$startTime." >= ".$minTime." AND ".$endTime." <= ".$maxTime.")" : "((".$startDate." == ".$endDate.") AND (".$startTime." > ".$maxTime." OR ".$endTime." < ".$minTime.")) OR ((".$startDate." != ".$endDate.") AND (".$startTime." > ".$maxTime." AND ".$endTime." < ".$minTime."))" );
		$query[] = "(".(($_GET["IncExcTime"] == "include")
			? "(".$startDate." = ".$endDate.") AND (".$startTime." >= ".$minTime." AND ".$endTime." <= ".$maxTime.")"
			: "((".$startDate." = ".$endDate.") AND (".$startTime." > ".$maxTime." OR ".$endTime." < ".$minTime.")) OR ((".$startDate." != ".$endDate.") AND (".$startTime." > ".$maxTime." AND ".$endTime." < ".$minTime."))"
		).")";
	}
	
	$query[] = "coordinates = '".$_GET["coordinates"]."'";
	
	if ($_GET["verbalization"] != "") {
		$query[] = "verbalization = ".$_GET["verbalization"];
	}
	
	$sql .= " WHERE ".implode(" AND ", $query);
	*/

	$sql .= queryConstraints();
	
	//var_dump($sql);
	
	$result = mysqli_query($con, $sql);
	
	$ip_addresses = array();
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$ip_addresses[] = $row["ip_address"];
	}
	
	//var_dump($ip_addresses);
	
	
	// ordinare gli indirizzi ip
	
	
	/* ip addresses table */
	
	$table = '<table id="ip_address"><tbody>';
	
	if (($n = count($ip_addresses)) == 1) {
	
		$ip_address = explode(".", $ip_addresses[0]);
		
		$ip_address = array_map(function ($byte) {
			return '<span class="byte">'.$byte.'</span>';
		}, $ip_address);
		
		$ip_address = implode('<span class="point">.</span>', $ip_address);
		
		//var_dump($ip_address);
	
		$table .= '<tr>'.
			//'<td class="selected">'.$ip_addresses[0].'</td>'.
			'<td class="selected">'.$ip_address.'</td>'.
			'<td class="space" colspan=5 style="width: '.(5 * 16.666).'%;"></td>'.
		'</tr>';
	} else {
	
		for ($i = 0; $i < intval(($n + 1) / 6) ; $i++) {

			$table .= '<tr>';

			for ($j = -1; $j <= 4; $j++) {
				if ($i == 0 && $j == -1)
					$table .= '<td class="selected">any</td>';
				else {
					//$table .= '<td>'.$ip_addresses[6 * $i + $j].'</td>';
					$ip_address = explode(".", $ip_addresses[6 * $i + $j]);
	
					$ip_address = array_map(function ($byte) {
						return '<span class="byte">'.$byte.'</span>';
					}, $ip_address);
	
					$ip_address = implode('<span class="point">.</span>', $ip_address);
	
					//var_dump($ip_address);
					
					$table .= '<td>'.$ip_address.'</td>';
				}
			}

			$table .= '</tr>';
		}

		if (($n + 1) % 6 != 0) {
		
				$table .= '<tr>';

				if (($n + 1) < 6) {
					$table .= '<td class="selected">any</td>';

					for ($i = $n - $n % 6; $i < $n; $i++) {
						//$table .= '<td>'.$ip_addresses[$i].'</td>';
						$ip_address = explode(".", $ip_addresses[$i]);
	
						$ip_address = array_map(function ($byte) {
							return '<span class="byte">'.$byte.'</span>';
						}, $ip_address);
	
						$ip_address = implode('<span class="point">.</span>', $ip_address);
	
						//var_dump($ip_address);
					
						$table .= '<td>'.$ip_address.'</td>';
					}

				} else {
					for ($i = $n - ($n + 1) % 6; $i < $n; $i++) {
						//$table .= '<td>'.$ip_addresses[$i].'</td>';
						$ip_address = explode(".", $ip_addresses[$i]);
	
						$ip_address = array_map(function ($byte) {
							return '<span class="byte">'.$byte.'</span>';
						}, $ip_address);
	
						$ip_address = implode('<span class="point">.</span>', $ip_address);
	
						//var_dump($ip_address);
					
						$table .= '<td>'.$ip_address.'</td>';
					}
				}

				$table .= '<td class="space" colspan='.(6 - ($n + 1) % 6).' style="width: '.((6 - ($n + 1) % 6) * 16.666).'%;"></td>';
				$table .= '</tr>';
		}
	}
	
	$table .= '</tbody></table>';
	
	echo '<h3>IP ADDRESSES ('.count($ip_addresses).')</h3>';
	echo $table;
	
	
	mysqli_close($con);
?>
