<html>
<head>
	<?php
			error_reporting(0);
			session_start();
			if(isset($_POST['logoff'])){
				$_SESSION['user_id'] = null;
				session_destroy();
			}
			if(!isset($_SESSION['user_id'])){
				header("Location: /login.php");
			}
			
			$team = $_GET['team'];
			
			$setup_ini = parse_ini_file("/settings.ini", true);
			
			$server_ini = $setup_ini['server'];
			
			include("db_conn.php");
			
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
			
			if(isset($_POST['submit_captain'])){
				$sql = "UPDATE inspections SET `captain_signature`=\"".mysqli_real_escape_string($conn,$_POST['signature'])."\" WHERE team_number=".mysqli_real_escape_string($conn, $_GET['team']);
				mysqli_query($conn, $sql) or die(mysqli_error($conn)."<br>\n$sql");
			}
			if(isset($_POST['submit_mentour'])){
				$sql = "UPDATE inspections SET `mentour_signature`=\"".mysqli_real_escape_string($conn,$_POST['signature'])."\" WHERE team_number=".mysqli_real_escape_string($conn, $_GET['team']);
				mysqli_query($conn, $sql);
			}
			if(isset($_POST['submit_initial'])){
				$sql = "UPDATE inspections SET `initial_inspection_signature`=\"".mysqli_real_escape_string($conn,$_POST['signature'])."\" WHERE team_number=".mysqli_real_escape_string($conn, $_GET['team']);
				mysqli_query($conn, $sql);
			}
			if(isset($_POST['submit_reinspection'])){
				$sql = "UPDATE inspections SET `reinspection_signature`=\"".mysqli_real_escape_string($conn,$_POST['signature'])."\" WHERE team_number=".mysqli_real_escape_string($conn, $_GET['team']);
				mysqli_query($conn, $sql);
			}
			if(isset($_POST['final_inspection_captain'])){
				$sql = "UPDATE inspections SET `final_inspection_signature`=\"".mysqli_real_escape_string($conn,$_POST['signature'])."\" WHERE team_number=".mysqli_real_escape_string($conn, $_GET['team']);
				mysqli_query($conn, $sql);
			}
			
			$signatures = array("mentour", "captain", "initial_inspection", "reinspection", "final_inspection");
			
			foreach($signatures as $signature){
				$sql = "SELECT $signature"."_signature FROM inspections WHERE team_number = $_GET[team]";
				$res = mysqli_query($conn, $sql);
				$row = mysqli_fetch_array($res);
				${$signature."_url"} = $row[0];
			}
	?>
	<style>
		.hide{
			display:none;
		}
	</style>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
	<script>
		
		var mentourSignaturePad;
		var captainSignaturePad;
		var initialInspectionSignaturePad;
		var reinspectionSignaturePad;
		var finalInspectionSignaturePad;
		
		$(function(){
				mentourSignaturePad = new SignaturePad($('#mentour_signature_pad')[0]);
				mentourSignaturePad.onEnd = function(){$('#mentour_signature_input').val(mentourSignaturePad.toDataURL());}
				mentourSignaturePad.fromDataURL("<?php echo($mentour_url); ?>");
				
				captainSignaturePad = new SignaturePad($('#captain_signature_pad')[0]);
				captainSignaturePad.onEnd = function(){$('#captain_signature_input').val(captainSignaturePad.toDataURL());}
				captainSignaturePad.fromDataURL("<?php echo($captain_url); ?>");
				
				initialInspectionSignaturePad = new SignaturePad($('#initial_inspection_signature_pad')[0]);
				initialInspectionSignaturePad.onEnd = function(){$('#initial_inspection_signature_input').val(initialInspectionSignaturePad.toDataURL());}
				initialInspectionSignaturePad.fromDataURL("<?php echo($initial_inspection_url); ?>");
				
				reinspectionSignaturePad = new SignaturePad($('#reinspection_signature_pad')[0]);
				reinspectionSignaturePad.onEnd = function(){$('#reinspection_signature_input').val(reinspectionSignaturePad.toDataURL());}
				reinspectionSignaturePad.fromDataURL("<?php echo($reinspection_url); ?>");
				
				finalInspectionSignaturePad = new SignaturePad($('#final_inspection_signature_pad')[0]);
				finalInspectionSignaturePad.onEnd = function(){$('#final_inspection_signature_input').val(finalInspectionSignaturePad.toDataURL());}
				finalInspectionSignaturePad.fromDataURL("<?php echo($final_inspection_url); ?>");
		});
		
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
</head>
<body>
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
			'prohibited_materials_explanation' => array('en' => "eg. sound, lasers, noxious or toxic gases or inhalable particles or chemicals"),
			'clear_button_text' => array('en' => "Clear Signature"),
			'mentour_signature_text' => array('en' => "Mentour Signature"),
			'captain_signature_text' => array('en' => "Captain Signature"),
			'initial_inspection_signature_text' => array('en' => "Initial Inspection Signature"),
			'reinspection_signature_text' => array('en' => "Reinspection Signature"),
			'final_inspection_signature_text' => array('en' => "Final Inspection Signature"),
			'team_compliance_statement_text'  => array(
													'en' => "We, the Team Mentor and Team Captain, attest by our signing below, that our team’s robot was built after the 2019 Kickoff on January 5, 2019 and in accordance with all of the 2019 FRC rules, including all Fabrication Schedule rules. We have conducted our own inspection and determined that our robot satisfies all of the 2019 FRC rules for robot design."
												)
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
					
					<input oninput='updateDataEntry(\"#".$item['data_entry_field_ids'][$index]."\")' name='".$item['data_entry_field_ids'][$index]."' id='".$item['data_entry_field_ids'][$index]."' value=\"$item_value\">";
				}
				echo("\n<tr><td><input id=$item[id] onclick=\"toggleChecked('$item[id]');\" type=checkbox name=$item[id] ".($values_of_items[$item['id']] == "true" ? "checked" : "")."></td><td".($data_inputs == "" ? " colspan=3" : "")."><b>".get_string($item['title'], $current_locale, $strings)."</b> ".get_string($item['explanation'], $current_locale, $strings)."&nbsp;&nbsp;&lt;".$item['rule_no']."&gt;</td><td>$data_inputs</td>");
			}
		}
		
		echo("</table>");
	?>
	<a href=# onclick="this.showing = !this.showing; if(this.showing) $('#mentour_signature_form').show(); else $('#mentour_signature_form').hide();"><?php echo(get_string("mentour_signature_text", $current_locale, $strings)); ?></a><br/>
	<form id=mentour_signature_form action=# method=POST class=hide>
		<p><?php echo(get_string("team_compliance_statement_text", $current_locale, $strings)); ?></p>
		<canvas id=mentour_signature_pad class=signature_pad></canvas>
		<input type=hidden name=signature id=mentour_signature_input>
		<input type=Submit name=submit_mentour>
		<button type=button onclick="mentourSignaturePad.clear();mentourSignaturePad.onEnd();">
			<?php echo(get_string("clear_button_text", $current_locale, $strings)); ?>
		</button>
	</form>

	<a href=# onclick="this.showing = !this.showing; if(this.showing) $('#captain_signature_form').show(); else $('#captain_signature_form').hide();"><?php echo(get_string("captain_signature_text", $current_locale, $strings)); ?></a><br/>
	<form id=captain_signature_form action=# method=POST class=hide>
		<p><?php echo(get_string("team_compliance_statement_text", $current_locale, $strings)); ?></p>
		<canvas id=captain_signature_pad class=signature_pad></canvas>
		<input type=hidden name=signature id=captain_signature_input>
		<input type=Submit name=submit_captain>
		<button type=button onclick="captainSignaturePad.clear();captainSignaturePad.onEnd();">
			<?php echo(get_string("clear_button_text", $current_locale, $strings)); ?>
		</button>
	</form>

	<a href=# onclick="this.showing = !this.showing; if(this.showing) $('#initial_inspection_signature_form').show(); else $('#initial_inspection_signature_form').hide();"><?php echo(get_string("initial_inspection_signature_text", $current_locale, $strings)); ?></a><br/>
	<form id=initial_inspection_signature_form action=# method=POST class=hide>
		<canvas id=initial_inspection_signature_pad class=signature_pad></canvas>
		<input type=hidden name=signature id=initial_inspection_signature_input>
		<input type=Submit name=submit_initial>
		<button type=button onclick="initialInspectionSignaturePad.clear();initialInspectionSignaturePad.onEnd();">
			<?php echo(get_string("clear_button_text", $current_locale, $strings)); ?>
		</button>
	</form>

	<a href=# onclick="this.showing = !this.showing; if(this.showing) $('#reinspection_signature_form').show(); else $('#reinspection_signature_form').hide();"><?php echo(get_string("reinspection_signature_text", $current_locale, $strings)); ?></a><br/>
	<form id=reinspection_signature_form action=# method=POST class=hide>
		<canvas id=reinspection_signature_pad class=signature_pad></canvas>
		<input type=hidden name=signature id=reinspection_signature_input>
		<input type=Submit name=submit_reinspection>
		<button type=button onclick="reinspectionSignaturePad.clear();reinspectionSignaturePad.onEnd();">
			<?php echo(get_string("clear_button_text", $current_locale, $strings)); ?>
		</button>
	</form>

	<a href=# onclick="this.showing = !this.showing; if(this.showing) $('#final_inspection_signature_form').show(); else $('#final_inspection_signature_form').hide();"><?php echo(get_string("final_inspection_signature_text", $current_locale, $strings)); ?></a><br/>
	<form id=final_inspection_signature_form action=# method=POST class=hide>
		<canvas id=final_inspection_signature_pad class=signature_pad></canvas>
		<input type=hidden name=signature id=final_inspection_signature_input>
		<input type=Submit name=submit_final>
		<button type=button onclick="finalInspectionSignaturePad.clear();finalInspectionSignaturePad.onEnd();">
			<?php echo(get_string("clear_button_text", $current_locale, $strings)); ?>
		</button>
	</form>
	
	<form action=# method=POST><input type=submit name=logoff value="Log Out"></form>
</body>
</html>