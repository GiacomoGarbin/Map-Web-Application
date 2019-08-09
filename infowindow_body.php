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

	$sql .= queryConstraints();
	
	//var_dump($sql);
	
	$result = mysqli_query($con, $sql);
	
	$ip_addresses = array();
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$ip_addresses[] = $row["ip_address"];
	}
	
	//var_dump($ip_addresses);
	
	
	/* ip address informations */
	
	$sql = "SELECT * FROM ip_table";
	
	//if ($_GET["ip_address"] != "") {
	if (isset($_GET["ip_address"])) {
		$sql .= " WHERE ip_address = '".$_GET["ip_address"]."'";
	} else {
	
		$items = array();

		foreach ($ip_addresses as $value) {
			$items[] = "'".$value."'";
		}
	
		$sql .= " WHERE ip_address IN (".implode(", ", $items).")";
	}
	
	//var_dump($sql);
	
	$result = mysqli_query($con, $sql);
	
	$informations = array();
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$informations[] = $row;
	}
	
	//var_dump($informations);
	
	/* informations table */

	$table = '<table id="information"><tbody>';	
	
	if (count($informations) == 1) {
		
		$keys = array_keys($informations[0]);
		
		while ($key = array_shift($keys)) {
		
			if ($key == "ip_address" || $key == "coordinates")
				continue;
				
			$value = $informations[0][$key];
					
			if ($key == "as_code") {
				$table .= '<tr><td class="label">'.str_replace("_", " ", $key).':</td><td class="value" colspan=3>'.$value.'</td></tr>';
			} else {
				$table .= '<tr><td class="label">'.str_replace("_", " ", $key).':</td><td class="value">'.$value.'</td>';
				
				$key = array_shift($keys);
				$value = $informations[0][$key];
				
				$table .= '<td class="label">'.str_replace("_", " ", $key).':</td><td class="value">'.$value.'</td></tr>';
			}
		}
		
	} else {
		
		$keys = array_keys($informations[0]);
		
		while ($key = array_shift($keys)) {
		
			if ($key == "ip_address" || $key == "coordinates")
				continue;
			
			$column = array_column($informations, $key);
			$column = array_unique($column);
			
			$value = (count($column) == 1) ? $informations[0][$key] : "*** multiple values ***";
					
			if ($key == "as_code") {
				$table .= '<tr><td class="label">'.str_replace("_", " ", $key).':</td><td class="value" colspan=3>'.$value.'</td></tr>';
			} else {
				$table .= '<tr><td class="label">'.str_replace("_", " ", $key).':</td><td class="value">'.$value.'</td>';
				
				$key = array_shift($keys);
				
				$column = array_column($informations, $key);
				$column = array_unique($column);
		
				$value = (count($column) == 1) ? $informations[0][$key] : "*** multiple values ***";
				
				$table .= '<td class="label">'.str_replace("_", " ", $key).':</td><td class="value">'.$value.'</td></tr>';
			}
		}
		
	}

	$table .= '</tbody></table>';
	
	echo '<h3>IP ADDRESS INFORMATIONS</h3>';
	//echo '<div class="container">'.$table.'</div>';
	echo $table;
	
	
	/* sessions */
	
	$sql = "SELECT * FROM session_table";

	$sql .= queryConstraints();
	
	$sql .= " ORDER BY session_start";
		
	//var_dump($sql);
	
	$result = mysqli_query($con, $sql);
	
	$sessions = array();
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$sessions[] = $row;
	}
	
	//var_dump($sessions);
	
	
	/* session table */
	
	$keys = array_keys($sessions[0]);
	$flag = (count(array_unique(array_column($sessions, "ip_address"))) == 1) || isset($_GET["ip_address"]);
	
	$table = '<table id="infowindowSessions"><thead><tr>';
	
	foreach ($keys as $key) {

  	if ($key == "jsessionid")
			continue;
	
		if (($key == "ip_address" && $flag) || $key == "session_start" || $key == "session_end" || $key == "coordinates")
			continue;
		
		if ($key == "session_start_string" || $key == "session_end_string") {
			$table .= '<th class="'.$key.'">'.str_replace("_", " ", str_replace("_string", "", $key)).'</th>';
			continue;
		}
		
		$table .= '<th class="'.$key.'">'.str_replace("_", " ", $key).'</th>';
	}
	
	$table .= '</tr></thead><tbody>';
	
	foreach ($sessions as $session) {
		$table .= '<tr>';
		
		foreach ($keys as $key) {

    	if ($key == "jsessionid")
			  continue;
	
			if (($key == "ip_address" && $flag) || $key == "session_start" || $key == "session_end" || $key == "coordinates")
				continue;
				
			if ($key == "ip_address") {
				$ip_address = explode(".", $session[$key]);
	
				$ip_address = array_map(function ($byte) {
					return '<span class="byte">'.$byte.'</span>';
				}, $ip_address);
	
				$ip_address = implode('<span class="point">.</span>', $ip_address);
	
				//var_dump($ip_address);
				
				$table .= '<td class="'.$key.'">'.$ip_address.'</td>';
				continue;
			}
				
			if ($key == "verbalization" || $key == "holiday") {
				$table .= '<td class="'.$key.'">'.(($session[$key] == 1) ? "true" : "false").'</td>';
				continue;
			}

			/*
			if ($key == "ua_id") {
				$table .= '<td class="'.$key.'">'.(!isset($session[$key]) ? "" : "[".$session['ua_id']."] ".$session['ua_string']).'</td>';
				continue;
			}
			*/
	
			$table .= '<td class="'.$key.'">'.$session[$key].'</td>';
		}
		
		$table .= '</tr>';
	}

	$table .= '</tbody></table>';
	
	echo '<h3>SESSIONS ('.count($sessions).')</h3>';
	echo '<div class="container">'.$table.'</div>';
	
	
	/* users */
	
	$users = array_column($sessions, "user_id");
	$users = array_unique($users);
	
	// ordinare gli user_id prima (o eventualmente dopo) la query
	
	//var_dump($users);

	
	$sql = "SELECT user_id, role_id, department_id, structured FROM user_table";

	//$sql .= queryConstraints();

	$sql .= " WHERE user_id IN ('".implode("', '", $users)."')";
	
	$sql .= " ORDER BY user_id";
		
	//var_dump($sql);

	$result = mysqli_query($con, $sql);
	
	$users = array();
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$users[] = $row;
	}
	
	//var_dump($users);


	/* user table */

	$roles = array(
    "1" => "Professore Ordinario",
    "2" => "Co.Co.Co.",
    "3" => "Professore Associato",
    "4" => "Cat. EP - Area Tecnica, Tecnico-Scientifica ed Elaborazione Dati",
    "5" => "Ricercatore Universitario",
    "6" => "Cat. D - Area Socio-Sanitaria",
    "7" => "Collaboratore Esperto Linguistico",
    "8" => "Cat. D - Area Tecnica, Tecnico-Scientifica ed Elaborazione Dati",
    "9" => "Cat. C - Area Tecnica, Tecnico-Scientifica ed Elaborazione Dati",
    "10" => "Cat. EP - Area Medico-Odontoiatrica e Socio-Sanitaria",
    "11" => "Libero Professionista",
    "12" => "Assegnista di Ricerca",
    "13" => "Ricercatore a Tempo Determinato",
    "14" => "Personale Esterno",
    "15" => "Cat. D - Area Amministrativa-Gestionale",
    "16" => "Cat. C - Area Amministrativa",
    "17" => "Dottorato di Ricerca",
    "18" => "Lavoratore Autonomo"
	);

	$departments = array(
    "000530" => "Unità di Staff Servizio di Prevenzione e Protezione",
    "000555" => "Ufficio Industrial Liaison Office e Placement",
    "008000" => "Ex Facoltà di Psicologia",
    "009000" => "Ex Facoltà di Scienze della Formazione",
    "010000" => "Ex Scuola Superiore di Lingue Moderne per Interpreti e Traduttori",
    "010690" => "Centro Linguistico di Ateneo",
    "011000" => "Ex Facoltà di Ingegneria",
    "014467" => "Dipartimento di Fisica",
    "014468" => "Dipartimento di Scienze Politiche e Sociali",
    "014472" => "Dipartimento di Scienze Economiche, Aziendali, Matematiche e Statistiche",
    "015000" => "Ex Facoltà di Medicina e Chirurgia",
    "017074" => "Dipartimento Universitario Clinico di Scienze Mediche, Chirurgiche e della Salute",
    "017078" => "Dipartimento di Scienze Giuridiche, del Linguaggio, dell'Interpretazione e della Traduzione",
    "017080" => "Dipartimento di Matematica e Geoscienze",
    "017081" => "Dipartimento di Studi Umanistici",
    "023000" => "Dipartimento di Matematica e Informatica",
    "028654" => "Segreteria Didattica DF",
    "028663" => "Segreteria Amministrativa DSCF",
    "029000" => "Dipartimento di Scienze Chimiche e Farmaceutiche",
    "042000" => "Dipartimento di Ingegneria e Architettura",
    "056000" => "Dipartimento di Italianistica Linguistica Comunicazione Spettacolo",
    "084000" => "Dipartimento di Scienze della Vita"
	);
	
	$keys = array_keys($users[0]);

	$table = '<table id="infowindowUsers"><thead><tr>';
	
	foreach ($keys as $key) {
		
		if ($key == "role_id" || $key == "department_id") {
			$table .= '<th class="'.$key.'">'.str_replace("_", " ", $key).'</th>';
			$table .= '<th class="'.str_replace("_id", "_string", $key).'">'.str_replace("_id", " string", $key).'</th>';
			continue;
		}
		
		$table .= '<th class="'.$key.'">'.str_replace("_", " ", $key).'</th>';
	}
	
	$table .= '</tr></thead><tbody>';
	
	foreach ($users as $user) {
		$table .= '<tr>';
		
		foreach ($user as $key => $value) {
	
			if ($key == "role_id") {
				$table .= '<td class="'.$key.'">'.(!isset($value) ? "" : $value).'</td>';
				$table .= '<td class="'.str_replace("_id", "_string", $key).'">'.(!isset($value) ? "" : $roles[$value]).'</td>';
				continue;
			}

			if ($key == "department_id") {
				$table .= '<td class="'.$key.'">'.(!isset($value) ? "" : $value).'</td>';
				$table .= '<td class="'.str_replace("_id", "_string", $key).'">'.(!isset($value) ? "" : $departments[$value]).'</td>';
				continue;
			}
			
			if ($key == "structured") {
				$table .= '<td class="'.$key.'">'.(!isset($value) ? "" : (($value == 1) ? "true" : "false")).'</td>';
				continue;
			}
	
			$table .= '<td class="'.$key.'">'.$value.'</td>';
		}
		
		$table .= '</tr>';
	}

	$table .= '</tbody></table>';
	
	echo '<h3>USERS ('.count($users).')</h3>';
	echo '<div class="container">'.$table.'</div>';
	
	mysqli_close($con);
?>
