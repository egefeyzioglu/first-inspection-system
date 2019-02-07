<html>
<body>

<?php
		error_reporting(0);
		session_start();
		$_SESSION['user_id'] = 1234;
		
		$team = 9999;
		
		$setup_ini = parse_ini_file("/settings.ini", true);
		
		$db_ini = $setup_ini['db'];
		$server_ini = $setup_ini['server'];
		
		$conn = mysqli_connect($db_ini['host'], $db_ini['user'], $db_ini['password'], $db_ini['db_name']) or die("Connection to database failed.");
		if(isset($_GET['id']) && isset($_GET['value'])){
			$sql = "SELECT * FROM inspected_items WHERE team_number = ".mysqli_real_escape_string($conn, $_GET['team'])." AND item_id LIKE \"".mysqli_real_escape_string($conn, $_GET['id'])."\"";
			$res = mysqli_query($conn, $sql);
			if(mysqli_fetch_assoc($res)){
				$sql = "UPDATE inspected_items SET value = \"".mysqli_real_escape_string($conn, $_GET['value'])."\" WHERE team_number = ".mysqli_real_escape_string($conn, $_GET[team])." AND item_id LIKE \"".mysqli_real_escape_string($conn, $_GET[id])."\"";
				$res = mysqli_query($conn, $sql) or die(mysqli_error($conn)."<br/>\n$sql");
			}else{
				$sql = "INSERT INTO inspected_items(inspected_item_id, item_id, value, inspector_id, team_number) VALUES(0, \"".mysqli_real_escape_string($conn, $_GET[id])."\", \"$_GET[value]\", $_SESSION[user_id], ".mysqli_real_escape_string($conn, $_GET[team]).")";
				$res = mysqli_query($conn, $sql) or die(mysqli_error($conn)."<br/>\n$sql");
			}
		}
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
	
	function toggleChecked(id){
		var url = "<?php echo($url); ?>?id="+encodeURI(id)+"&value="+encodeURI($("#" + id)[0].checked)+"&team="+<?php echo($team); ?>;
		//$('#lol').html(url);
		$.get( url , function( data ) {});
	}
	
	function updateDataEntry(id){
		debugger;
		var url = "<?php echo($url); ?>?id="+encodeURI(id.substring(1,id.legth))+"&value="+encodeURI($(id)[0].value)+"&team="+<?php echo($team); ?>;;
		//$('#lol').html(url);
		$.get( url , function( data ) {});
	}
</script>
<?php
	error_reporting(E_ERROR);
	
	$current_locale = "en";
	
	$items = array(
		'initial_inspection_title' => array(
			array(
				'id' => "robot_weight",
				'title' => "robot_weight_title",
				'explanation' => "robot_weight_explanation",
				'rule_no' => "R05",
				'data_entry_field_ids' => array("robot_weight_field"),
				'data_entry_field_titles' => array("robot_weight_field")
			),array(
				'id' => "bumper_weight",
				'title' => "bumper_weight_title",
				'explanation' => "bumper_weight_explanation",
				'rule_no' => "R29",
				'data_entry_field_ids' => array("blue_bumper_weight_field", "red_bumper_weight_field"),
				'data_entry_field_titles' => array("blue_bumper_weight_field", "red_bumper_weight_field")
			)
		),
		'mechanical_title' => array(
			array(
				'id' => "sharp_edges",
				'title' => "sharp_edges_title",
				'explanation' => "",
				'rule_no' => "R06 R07",
				'data_entry_field_ids' => array(),
				'data_entry_field_titles' => array()
			),
			array(
				'id' => "prohibited_materials",
				'title' => "prohibited_materials_title",
				'explanation' => "prohibited_materials_explanation",
				'rule_no' => "R08",
				'data_entry_field_ids' => array(),
				'data_entry_field_titles' => array()
			)
		)
	);
	
	$strings = array(
		'initial_inspection_title' => array('en' => "Initial Inspection"),
		'robot_weight_title' => array('en' => "Robot Weight"),
		'robot_weight_explanation' => array('en' => "Must be equal to or lower than 120 lbs (~54.4311 kg)"),
		'robot_weight_field' => array('en' => "Robot Weight"),
		'bumper_weight_title' => array('en' => "Bumper Weight"),
		'bumper_weight_explanation' => array('en' => "Must be equal to or lower than 20 lbs (~9.07185 kg)"),
		'blue_bumper_weight_field' => array('en' => "Blue Bumper Weight"),
		'red_bumper_weight_field' => array('en' => "Red Bumper Weight"),
		'sharp_edges_title' => array('en' => "No Sharp Edges or Protrusions that pose a hazard for participants, robots, arena, or field"),
		'mechanical_title' => array('en' => "Mechanical"),
		'prohibited_materials_title' => array('en' => "No Prohibited Materials"),
		'prohibited_materials_explanation' => array('en' => "eg. sound, lasers, noxious or toxic gases or inhalable particles or chemicals")
	);
	
	function get_string($string_id, $locale, $strings){
		$get_string_value = $strings[$string_id][$locale];
		if($get_string_value == null)
			return $string_id;
		else
			return $get_string_value;
	}
	
	echo("<table>\n");
	
	
	$sql = "SELECT * FROM inspected_items WHERE team_number=".mysqli_real_escape_string($conn, $team);
	$res = mysqli_query($conn, $sql) or die(mysqli_error($conn)."<br/>\n$sql");
	$values_of_items = array();
	while($row = mysqli_fetch_assoc($res)){
		$values_of_items[$row['item_id']] = $row['value'];
	}
	
	foreach($items as $section_title=>$section){
		echo("\t<tr><th colspan=4>".get_string($section_title, $current_locale, $strings)."</th></tr>\n");
		foreach($section as $item){
			$data_inputs = "";
			foreach($item['data_entry_field_titles'] as $index => $value){
				$item_value = $values_of_items[$value];
				$data_inputs .= "<label for='".$item['data_entry_field_ids'][$index]."'>".get_string($value, $current_locale, $strings)."</label>
				
				<input oninput='updateDataEntry(\"#".$item['data_entry_field_ids'][$index]."\")' name='".$item['data_entry_field_ids'][$index]."' id='".$item['data_entry_field_ids'][$index]."' value=$item_value>";
			}
			echo("\n<tr><td><input id=$item[id] onclick=\"toggleChecked('$item[id]');\" type=checkbox name=$item[id] ".($values_of_items[$item['id']] == "true" ? "checked" : "")."></td><td".($data_inputs == "" ? " colspan=3" : "")."><b>".get_string($item['title'], $current_locale, $strings)."</b> ".get_string($item['explanation'], $current_locale, $strings)."&nbsp;&nbsp;&lt;".$item['rule_no']."&gt;</td><td>$data_inputs</td>");
		}
	}
?>
<span id=lol></span>
</body>
</html>