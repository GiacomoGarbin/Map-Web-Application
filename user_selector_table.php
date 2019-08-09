<?php
	//var_dump($_GET);
	
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
	
	$sql = "SELECT * FROM user_table ORDER BY user_id";
		
	//var_dump($sql);
	
	$result = mysqli_query($con, $sql);
	
	$users = array();
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$users[] = $row;
	}
	
	//var_dump($users);


	/* user selector table */
	
	$table = '<table id="userSelectorTable">';

	$table .= '<thead><tr>';

	$table .= '<th class="rowNumber">'.count($users).'</th>';
	$table .= '<th class="checkAll"><input id="checkSelectAll" type="checkbox" name="checkSelectAll" checked></th>';

	//var_dump(array_keys($users[0]));

	foreach ($users[0] as $key => $value) {

		if ($key == "user_id") {
			$table .= '<th class="'.$key.' sortAsc">'.preg_replace("/_\w+/", "", $key).'</th>';
			continue;
		}

		if ($key == "department_id" || $key == "structured") {
			$table .= '<th class="'.$key.'">'.substr($key, 0, 3).'.</th>';
			continue;
		}

		if ($key == "verbalization") {
			$table .= '<th class="'.$key.'">verb.</th>';
			$table .= '<th class="'.$key.'_pct">verb. %</th>';
			continue;
		}

		if ($key == "italy" || $key == "fvg" || $key == "holiday") {
			$table .= '<th class="'.$key.'">'.$key.'</th>';
			$table .= '<th class="'.$key.'_pct">'.$key.' %</th>';
			continue;
		}

		if ($key == "pair_ip_ua") {
			$table .= '<th class="'.$key.'">(ip, ua)</th>';
			continue;
		}

		if ($key == "country") {
			$table .= '<th class="'.$key.'">state</th>';
			continue;
		}

		if ($key == "mean_duration") {
			$table .= '<th class="'.$key.'">&mu; time</th>';
			continue;
		}
		
		$table .= '<th class="'.$key.'">'.preg_replace("/_\w+/", "", $key).'</th>';
	}

	$table .= '</tr></thead>';

	$table .= '<tbody class="userAlertRadioOr">';

	$j = 1;
	
	foreach ($users as $user) {
	
		$table .= '<tr class="checked">';
		
		$table .= '<td class="rowNumber">'.$j++.'</td>';
		$table .= '<td class="checkUser"><input type="checkbox" name="'.$user["user_id"].'" checked></td>';

		foreach ($user as $key => $value) {

			if ($key == "structured") {
				$table .= '<td class="'.$key.'">'.(!(isset($value)) ? "" : (($value == 1) ? "true" : "false")).'</td>';
				continue;
			}

			/*
			if ($key == "italy" || $key == "fvg" || $key == "holiday") {
				$table .= '<td class="'.$key.'">'.( !isset($value) ? "" : $value." (".number_format($value / $user["verbalization"] * 100, 2)." %)" ).'</td>';
				continue;
			}
			*/

			if ($key == "verbalization") {
				$table .= '<td class="'.$key.'">'.$value.'</td>';
				$table .= '<td class="'.$key.'_pct">'.(number_format($value / $user["session"] * 100, 2)." %").'</td>';
				continue;
			}

			if ($key == "italy" || $key == "fvg" || $key == "holiday") {
				$table .= '<td class="'.$key.'">'.$value.'</td>';
				$table .= '<td class="'.$key.'_pct">'.( !isset($value) ? "" : number_format($value / $user["verbalization"] * 100, 2)." %" ).'</td>';
				continue;
			}

			if ($key == "mean_duration") {
				$table .= '<td class="'.$key.'">'.( !isset($value) ? "" : number_format($value, 2) ).'</td>';
				continue;
			}
		
			$table .= '<td class="'.$key.'">'.$value.'</td>';
		}
		
		$table .= '</tr>';
	}

	$table .= '</tbody></table>';
	
	echo $table;
	

	mysqli_close($con);
?>
