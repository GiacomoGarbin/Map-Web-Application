<?php
	function queryConstraints() {

		$query = array();

		if (isset($_GET["ip_address"])) {
			$query[] = "ip_address = '".$_GET["ip_address"]."'";
		}
		
		$startDate = "SUBSTR(session_start, 1, 10)";
		$endDate = "SUBSTR(session_end, 1, 10)";
		$minDate = "'".$_GET["minDate"]."'";
		$maxDate = "'".$_GET["maxDate"]."'";
		
		//$query[] = (($_GET["IncExcDate"] == "include") ? "(SUBSTR(session_start, 1, 10) >= '".$_GET["minDate"]."' AND SUBSTR(session_end, 1, 10) <= '".$_GET["maxDate"]."')" : "(SUBSTR(session_start, 1, 10) > '".$_GET["maxDate"]."' OR SUBSTR(session_end, 1, 10) < '".$_GET["minDate"]."')");
		/*
		$query[] = "(".(($_GET["IncExcDate"] == "include")
			? "(SUBSTR(session_start, 1, 10) >= '".$_GET["minDate"]."' AND SUBSTR(session_end, 1, 10) <= '".$_GET["maxDate"]."')"
			: "(SUBSTR(session_start, 1, 10) > '".$_GET["maxDate"]."' OR SUBSTR(session_end, 1, 10) < '".$_GET["minDate"]."')"
		).")";
		*/

		$query[] = ($_GET["IncExcDate"] == "include")
			? "(SUBSTR(session_start, 1, 10) >= '".$_GET["minDate"]."' AND SUBSTR(session_end, 1, 10) <= '".$_GET["maxDate"]."')"
			: "(SUBSTR(session_start, 1, 10) > '".$_GET["maxDate"]."' OR SUBSTR(session_end, 1, 10) < '".$_GET["minDate"]."')";

		if (isset($_GET["IncExcTime"])) {
			$startTime = "SUBSTR(session_start, 12, 16)";
			$endTime = "SUBSTR(session_end, 12, 16)";
			$minTime = "'".$_GET["minTime"]."'";
			$maxTime = "'".$_GET["maxTime"]."'";
		
			//$query[] = (($_GET["IncExcTime"] == "include") ? "(".$startDate." == ".$endDate.") AND (".$startTime." >= ".$minTime." AND ".$endTime." <= ".$maxTime.")" : "((".$startDate." == ".$endDate.") AND (".$startTime." > ".$maxTime." OR ".$endTime." < ".$minTime.")) OR ((".$startDate." != ".$endDate.") AND (".$startTime." > ".$maxTime." AND ".$endTime." < ".$minTime."))" );
			/*
			$query[] = "(".(($_GET["IncExcTime"] == "include")
				? "(".$startDate." = ".$endDate.") AND (".$startTime." >= ".$minTime." AND ".$endTime." <= ".$maxTime.")"
				: "((".$startDate." = ".$endDate.") AND (".$startTime." > ".$maxTime." OR ".$endTime." < ".$minTime.")) OR ((".$startDate." != ".$endDate.") AND (".$startTime." > ".$maxTime." AND ".$endTime." < ".$minTime."))"
			).")";
			*/

			$query[] = ($_GET["IncExcTime"] == "include")
				? "((".$startDate." = ".$endDate.") AND (".$startTime." >= ".$minTime." AND ".$endTime." <= ".$maxTime."))"
				: "(((".$startDate." = ".$endDate.") AND (".$startTime." > ".$maxTime." OR ".$endTime." < ".$minTime.")) OR ((".$startDate." != ".$endDate.") AND (".$startTime." > ".$maxTime." AND ".$endTime." < ".$minTime.")))";
		}

		if (isset($_GET["coordinates"])) {
			$query[] = "coordinates = '".$_GET["coordinates"]."'";
		}

		if ($_GET["verbalization"] != "") {
			$query[] = "verbalization = ".$_GET["verbalization"];
		}

		if ($_GET["holiday"] != "") {
			$query[] = "holiday = ".$_GET["holiday"];
		}

		if (isset($_GET["IncExcUsers"])) {

			$users = explode(" ", $_GET["users"]);
			//var_dump($users);
		
			$query[] = "user_id".($_GET["IncExcUsers"] == "include" ? "" : " NOT")." IN ('".implode("', '", $users)."')";
		}
		
		return " WHERE ".implode(" AND ", $query);
	}
?>
