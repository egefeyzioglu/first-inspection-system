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
		<a href="/">Home</a>
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
				), array(
				        'id' => "starting_configuration",
					'title' => "starting_configuration_title",
					'explanation' => "starting_configuration_explanation",
					'rule_no' => "starting_configuration_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				), array(
					'id' => "starting_volume",
					'title' => "starting_volume_title",
					'explanation' => "starting_volume_explanation",
					'rule_no' => "starting_volume_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				), array(
					'id' => "frame_perimeter",
					'title' => "frame_perimeter_title",
					'explanation' => "frame_perimeter_explanation",
					'rule_no' => "frame_perimeter_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "playing_configuration",
					'title' => "playing_configuration_title",
					'explanation' => "playing_configuration_explanation",
					'rule_no' => "playing_configuration_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "standard_bumpers",
					'title' => "standard_bumpers_title",
					'explanation' => "standard_bumpers_explanation",
					'rule_no' => "standard_bumpers_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "standard_bumpers_a",
					'title' => "standard_bumpers_a_title",
					'explanation' => "standard_bumpers_a_explanation",
					'rule_no' => "standard_bumpers_a_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "standard_bumpers_b",
					'title' => "standard_bumpers_b_title",
					'explanation' => "standard_bumpers_b_explanation",
					'rule_no' => "standard_bumpers_b_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "standard_bumpers_c",
					'title' => "standard_bumpers_c_title",
					'explanation' => "standard_bumpers_c_explanation",
					'rule_no' => "standard_bumpers_c_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "standard_bumpers_d",
					'title' => "standard_bumpers_d_title",
					'explanation' => "standard_bumpers_d_explanation",
					'rule_no' => "standard_bumpers_d_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "standard_bumpers_e",
					'title' => "standard_bumpers_e_title",
					'explanation' => "standard_bumpers_e_explanation",
					'rule_no' => "standard_bumpers_e_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "standard_bumpers_f",
					'title' => "standard_bumpers_f_title",
					'explanation' => "standard_bumpers_f_explanation",
					'rule_no' => "standard_bumpers_f_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "standard_bumpers_g",
					'title' => "standard_bumpers_g_title",
					'explanation' => "standard_bumpers_g_explanation",
					'rule_no' => "standard_bumpers_g_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "standard_bumpers_h",
					'title' => "standard_bumpers_h_title",
					'explanation' => "standard_bumpers_h_explanation",
					'rule_no' => "standard_bumpers_h_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "standard_bumpers_i",
					'title' => "standard_bumpers_i_title",
					'explanation' => "standard_bumpers_i_explanation",
					'rule_no' => "standard_bumpers_i_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "standard_bumpers_j",
					'title' => "standard_bumpers_j_title",
					'explanation' => "standard_bumpers_j_explanation",
					'rule_no' => "standard_bumpers_j_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "standard_bumpers_k",
					'title' => "standard_bumpers_k_title",
					'explanation' => "standard_bumpers_k_explanation",
					'rule_no' => "standard_bumpers_k_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "standard_bumpers_l",
					'title' => "standard_bumpers_l_title",
					'explanation' => "standard_bumpers_l_explanation",
					'rule_no' => "standard_bumpers_l_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				)
			),
			'mechanical_title' => array(
				array(
					'id' => "bom_cost",
					'title' => "bom_cost_title",
					'explanation' => "bom_cost_explanation",
					'rule_no' => "bom_cost_rule_no",
					'data_entry_field_ids' => array(),    
					'data_entry_field_titles' => array()
				), 
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
				), array(
					'id' => "energy_storage",
					'title' => "energy_storage_title",
					'explanation' => "energy_storage_explanation",
					'rule_no' => "energy_storage_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "damage_robots",
					'title' => "damage_robots_title",
					'explanation' => "damage_robots_explanation",
					'rule_no' => "damage_robots_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "damage_field",
					'title' => "damage_field_title",
					'explanation' => "damage_field_explanation",
					'rule_no' => "damage_field_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "decorations",
					'title' => "decorations_title",
					'explanation' => "decorations_explanation",
					'rule_no' => "decorations_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "end_game",
					'title' => "end_game_title",
					'explanation' => "end_game_explanation",
					'rule_no' => "end_game_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),
			),
			'electrical_title' => array(
				array(
					'id' => "components",
					'title' => "components_title",
					'explanation' => "components_explanation",
					'rule_no' => "components_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "battery",
					'title' => "battery_title",
					'explanation' => "battery_explanation",
					'rule_no' => "battery_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "other_batteries",
					'title' => "other_batteries_title",
					'explanation' => "other_batteries_explanation",
					'rule_no' => "other_batteries_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "visibility",
					'title' => "visibility_title",
					'explanation' => "visibility_explanation",
					'rule_no' => "visibility_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				), array(
				        'id' => "breaker",
					'title' => "breaker_title",
					'explanation' => "breaker_explanation",
					'rule_no' => "breaker_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "pd_breakers",
					'title' => "pd_breakers_title",
					'explanation' => "pd_breakers_explanation",
					'rule_no' => "pd_breakers_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "robot_radio",
					'title' => "robot_radio_title",
					'explanation' => "robot_radio_explanation",
					'rule_no' => "robot_radio_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "can_bus",
					'title' => "can_bus_title",
					'explanation' => "can_bus_explanation",
					'rule_no' => "can_bus_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "robo_rio",
					'title' => "robo_rio_title",
					'explanation' => "robo_rio_explanation",
					'rule_no' => "robo_rio_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "wire_breaker_size",
					'title' => "wire_breaker_size_title",
					'explanation' => "wire_breaker_size_explanation",
					'rule_no' => "wire_breaker_size_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "wire_size_a",
					'title' => "wire_size_a_title",
					'explanation' => "wire_size_a_explanation",
					'rule_no' => "wire_size_a_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "wire_size_b",
					'title' => "wire_size_b_title",
					'explanation' => "wire_size_b_explanation",
					'rule_no' => "wire_size_b_rule_no",
					'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
				),array(
					'id' => "wire_size_c",
					'title' => "wire_size_c_title",
					'explanation' => "wire_size_c_explanation",
					'rule_no' => "wire_size_c_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "wire_size_d",
					'title' => "wire_size_d_title",
					'explanation' => "wire_size_d_explanation",
					'rule_no' => "wire_size_d_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "wire_colors",
					'title' => "wire_colors_title",
					'explanation' => "wire_colors_explanation",
					'rule_no' => "wire_colors_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array(),
				),array(
					'id' => "copper_wire",
					'title' => "copper_wire_title",
					'explanation' => "copper_wire_explanation",
					'rule_no' => "copper_wire_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "wire_wago",
					'title' => "wire_wago_title",
					'explanation' => "wire_wago_explanation",
					'rule_no' => "wire_wago_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "motors",
					'title' => "motors_title",
					'explanation' => "motors_explanation",
					'rule_no' => "motors_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "actuators",
					'title' => "actuators_title",
					'explanation' => "actuators_explanation",
					'rule_no' => "actuators_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "actuator_power",
					'title' => "actuator_power_title",
					'explanation' => "actuator_power_explanation",
					'rule_no' => "actuator_power_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "actuator_control",
					'title' => "actuator_control_title",
					'explanation' => "actuator_control_explanation",
					'rule_no' => "actuator_control_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "additional_electronics",
					'title' => "additional_electronics_title",
					'explanation' => "additional_electronics_explanation",
					'rule_no' => "additional_electronics_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "pcm",
					'title' => "pcm_title",
					'explanation' => "pcm_explanation",
					'rule_no' => "pcm_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "spike_fuse",
					'title' => "spike_fuse_title",
					'explanation' => "spike_fuse_explanation",
					'rule_no' => "spike_fuse_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				),array(
					'id' => "isolated_frame",
					'title' => "isolated_frame_title",
					'explanation' => "isolated_frame_explanation",
					'rule_no' => "isolated_frame_rule_no",
					'data_entry_field_ids' => array(),
					'data_entry_field_titles' => array()
				)
			),
			'pneumatic_system_title' => array(
					array(
						'id' => "modifications",
						'title' => "modifications_title",
						'explanation' => "modifications_explanation",
						'rule_no' => "modifications_rule_no",
						'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
					),array(
						'id' => "compressor",
						'title' => "compressor_title",
						'explanation' => "compressor_explanation",
						'rule_no' => "compressor_rule_no",
						'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
					),array(
						'id' => "compressor_power",
						'title' => "compressor_power_title",
						'explanation' => "compressor_power_explanation",
						'rule_no' => "compressor_power_rule_no",
						'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
					),array(
						'id' => "compressor_control",
						'title' => "compressor_control_title",
						'explanation' => "compressor_control_explanation",
						'rule_no' => "compressor_control_rule_no",
						'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
					),array(
						'id' => "vpv",
						'title' => "vpv_title",
						'explanation' => "vpv_explanation",
						'rule_no' => "vpv_rule_no",
						'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
					),array(
						'id' => "tubing",
						'title' => "tubing_title",
						'explanation' => "tubing_explanation",
						'rule_no' => "tubing_rule_no",
						'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
					),array(
						'id' => "gauges",
						'title' => "gauges_title",
						'explanation' => "gauges_explanation",
						'rule_no' => "gauges_rule_no",
						'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
					),array(
						'id' => "pressure",
						'title' => "pressure_title",
						'explanation' => "pressure_explanation",
						'rule_no' => "pressure_rule_no",
						'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
					),array(
						'id' => "valve_control",
						'title' => "valve_control_title",
						'explanation' => "valve_control_explanation",
						'rule_no' => "valve_control_rule_no",
						'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
						),
						
				),
				'power_check_title' => array(
					array(
						'id' => "wireless",
						'title' => "wireless_title",
						'explanation' => "wireless_explanation",
						'rule_no' => "wireless_rule_no",
						'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
					),array(
						'id' => "pneumatics_operation",
						'title' => "pneumatics_operation_title",
						'explanation' => "pneumatics_operation_explanation",
						'rule_no' => "pneumatics_operation_rule_no",
						'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
					),array(
						'id' => "compressor_automatic",
						'title' => "compressor_automatic_title",
						'explanation' => "compressor_automatic_explanation",
						'rule_no' => "compressor_automatic_rule_no",
						'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
					),array(
						'id' => "main_pressure",
						'title' => "main_pressure_title",
						'explanation' => "main_pressure_explanation",
						'rule_no' => "main_pressure_rule_no",
						'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
					),array(
						'id' => "relief_valve",
						'title' => "relief_valve_title",
						'explanation' => "relief_valve_explanation",
						'rule_no' => "relief_valve_rule_no",
						'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
					),array(
						'id' => "pressure_regulator",
						'title' => "pressure_regulator_title",
						'explanation' => "pressure_regulator_explanation",
						'rule_no' => "pressure_regulator_rule_no",
						'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
					),array(
						'id' => "signal_light",
						'title' => "signal_light_title",
						'explanation' => "signal_light_explanation",
						'rule_no' => "signal_light_rule_no",
						'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
					),array(
						'id' => "teamnumber_ds",
						'title' => "teamnumber_ds_title",
						'explanation' => "teamnumber_ds_explanation",
						'rule_no' => "teamnumber_ds_rule_no",
						'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
					),array(
						'id' => "software_versions",
						'title' => "software_versions_title",
						'explanation' => "software_versions_explanation",
						'rule_no' => "software_versions_rule_no",
						'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
					),array(
						'id' => "power_off",
						'title' => "power_off_title",
						'explanation' => "power_off_explanation",
						'rule_no' => "power_off_rule_no",
						'data_entry_field_ids' => array(),
						'data_entry_field_titles' => array()
					),array(
						'id' => "driver_console",
						'title' => "driver_console_title",
						'explanation' => "driver_console_explanation",
						'rule_no' => "driver_console_rule_no",
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
			'starting_configuration_title' => array('en' => "Starting Configuration"),
			'starting_configuration_explanation' => array('en' => "Parts may not extend beyond the vertical projection of the FRAME PERIMETER."),
			'starting_configuration_rule_no' => array('en' => "R02"),
			'starting_volume_title' => array('en' => "Starting Volume"),
			'starting_volume_explanation' => array('en' => "FRAME PERIMETER Not greater than 120in. (~304 cm) and may not be taller than 4ft. (~121 cm)"),
			'starting_volume_rule_no' => array('en' => "R03"),
			'standard_bumpers_title' => array('en' => "Standard Bumpers"),
			'standard_bumpers_explanation' => array('en' => "must follow all specifications in Sec. 10.5, BUMPER Rules."),
			'standard_bumpers_a_title' => array('en' => ""),
			'standard_bumpers_a_explanation' => array('en' => "Bumpers must provide protection for at least 6” (~16cm) on both sides of all outside corners. (Wood within ¼” of corner)"),
			'standard_bumpers_a_rule_no' => array('en' => "R24"),
			'standard_bumpers_b_title' => array('en' => ""),
			'standard_bumpers_b_explanation' => array('en' => "Hard bumper parts defined by bumper backing, may not extend >1” (~25mm) beyond robot frame."),
			'standard_bumpers_b_rule_no' => array('en' => "R31-B"),
			'standard_bumpers_c_title' => array('en' => ""),
			'standard_bumpers_c_explanation' => array('en' => "No bumper segment may be unsupported by robot frame for a length greater than 8” (~20cm). Gaps must be less than or
equal to ¼” (~6mm)"),
			'standard_bumpers_c_rule_no' => array('en' => "R33"),
			'standard_bumpers_d_title' => array('en' => ""),
			'standard_bumpers_d_explanation' => array('en' => "Bumpers must be supported by at least ½” (~13mm) of Robot Frame at each end (< ¼” (~6mm) gap OK)"),
			'standard_bumpers_d_rule_no' => array('en' => "R33"),
			'standard_bumpers_e_title' => array('en' => ""),
			'standard_bumpers_e_explanation' => array('en' => "Corners must be filled with pool noodle such that no “hard parts” are exposed."),
			'standard_bumpers_e_rule_no' => array('en' => "R32 & Fig. 10-7"),
			'standard_bumpers_f_title' => array('en' => ""),
			'standard_bumpers_f_explanation' => array('en' => "Must use ¾” (~19mm) thick x 5” (+/- ½”) (~127 mm ± 12.7 mm) tall plywood or solid robust wood backing with no
extraneous holes that may affect structural integrity. (clearance pockets and/or access holes are acceptable)."),
			'standard_bumpers_f_rule_no' => array('en' => "R31-A"),
			'standard_bumpers_g_title' => array('en' => ""),
			'standard_bumpers_g_explanation' => array('en' => "Must use a pair of vertically-stacked 2.5” pool noodles. Pool noodles may be any shape cross section, solid or hollow, but
both must be identical in shape and density."),
			'standard_bumpers_g_rule_no' => array('en' => "R31-C"),
			'standard_bumpers_h_title' => array('en' => ""),
			'standard_bumpers_h_explanation' => array('en' => "Must use a durable fabric cover for the noodles secured as in Fig 10-6 cross section."),
			'standard_bumpers_h_rule_no' => array('en' => "R31.D."),
			'standard_bumpers_i_title' => array('en' => ""),
			'standard_bumpers_i_explanation' => array('en' => "Must be able to display red or blue to match alliance color."),
			'standard_bumpers_i_rule_no' => array('en' => "R28"),
			'standard_bumpers_j_title' => array('en' => ""),
			'standard_bumpers_j_explanation' => array('en' => "Team number displayed with Arabic Font, min. font 4” (~11cm) tall x ½”(~13mm) stroke, in white or outlined in white
with a minimum 1/16in. (~2mm) outline and be easily read when walking around the perimeter of the robot. No logos
may be used for numerals. First Logos similar to 2019 KOP are OK."),
			'standard_bumpers_j_rule_no' => array('en' => "R28 & R29"),
			'standard_bumpers_k_title' => array('en' => ""),
			'standard_bumpers_k_explanation' => array('en' => "Must be securely mounted when attached and be easily removable for inspection."),
			'standard_bumpers_k_rule_no' => array('en' => "R31 G & R27"),
			'standard_bumpers_l_title' => array('en' => ""),
			'standard_bumpers_l_explanation' => array('en' => "When on flat floor, bumpers must reside entirely between the floor and 7-1/2” (~19cm) above floor (evaluated when
sitting flat on floor) and may not be articulated."),
			'standard_bumpers_l_rule_no' => array('en' => "R25 & R26"),
			'frame_perimeter_title' => array('en' => "FRAME PERIMETER"),
			'frame_perimeter_explanation' => array('en' => "Frame must be non-articulated."),
			'frame_perimeter_rule_no' => array('en' => "R01"),
			'playing configuration_title' => array('en' => "Playing Configuration"),
			'playing configuration_explanation' => array('en' => "Robot may not extend beyond the FRAME PERIMETER by more than 30” (~76 cm)."),
			'playing configuration_rule_no' => array('en' => "R04"),
			'sharp_edges_title' => array('en' => "No Sharp Edges or Protrusions that pose a hazard for participants, robots, arena, or field"),
			'mechanical_title' => array('en' => "Mechanical"),
			'prohibited_materials_title' => array('en' => "No Prohibited Materials"),
			'prohibited_materials_explanation' => array('en' => "eg. sound, lasers, noxious or toxic gases or inhalable particles or chemicals"),
			'energy_storage_title' => array('en' => "No Unsafe Energy Storage Devices"),
			'energy_storage_explanation' => array('en' => "carefully consider safety of stored energy or pneumatic systems"),
			'energy_storage_rule_no' => array('en' => "R09"),
			'damage_robots_title' => array('en' => "No Risk of Damage to Other Robots"),
			'damage_robots_explanation' => array('en' => "e.g. damaging, entangling, upending or adhering"),
			'damage_robots_rule_no' => array('en' => "G19, G20 & G09"),
			'damage_field_title' => array('en' => "No Risk of Damage to Field"),
			'damage_field_explanation' => array('en' => "e.g. metal cleats on traction devices or sharp points on frame."),
			'damage_field_rule_no' => array('en' => "G15 & R07"),
			'decorations_title' => array('en' => "Decorations"),
			'decorations_explanation' => array('en' => "Cannot interfere with other robots’ electronics or sensors, be in spirit of “Gracious Professionalism”."),
			'decorations_rule_no' => array('en' => "R09"),
			'bom_cost_title' => array('en' => "BOM Cost"),
			'bom_cost_explanation' => array('en' => "Team must present worksheet with total cost <= $5500, and no single component > $500."),
			'bom_cost_rule_no' => array('en' => "R12 thru R14"),
			'end_game_title' => array('en' => "End Game"),
			'end_game_explanation' => array('en' => "Game Objects can be removed from robot and robot from field without power."),
			'end_game_rule_no' => array('en' => "R10"),
			'electrical_title' => array('en' => "Electrical"),
			'components_title' => array('en' => "Components"),
			'components_explanation' => array('en' => "None may be modified, except for motor mounting and output shaft, motor wires may be trimmed, window
motor locking pins may be removed, and certain devices may be repaired with parts identical to the originals. PDP fuses
may be replaced with identical fuses only. Servos may be modified per manufacturer’s instructions."),
			'components_rule_no' => array('en' => "R35, R73"),
			'battery_title' => array('en' => "Battery"),
			'battery_explanation' => array('en' => "A single 12 volt, 17-18 AH robot battery (or listed equivalent), securely fastened inside robot."),
			'battery_rule_no' => array('en' => "R39, R43, R44"),
			'other_batteries_title' => array('en' => "Other Batteries"),
			'other_batteries_explanation' => array('en' => "Integral to COTS computing device or camera or COTS USB < 100Wh (20,000mAh at 5V) and 2.5Amp
max output per port used for COTS computing device and accessories only."),
			'other_batteries_rule_no' => array('en' => "R40"),
			'visibility_title' => array('en' => "Visibility"),
			'visibility_explanation' => array('en' => "The single PDP and PDP breakers must be easily visible for inspection."),
			'visibility_rule_no' => array('en' => "R51"),
			'breaker_title' => array('en' => "Main Breaker Accessibility"),
			'breaker_explanation' => array('en' => "the single 120A main breaker must be readily accessible with labeling preferred."),
			'breaker_rule_no' => array('en' => "R50"),
			'pd_breakers_title' => array('en' => "Allowable PD Breakers"),
			'pd_breakers_explanation' => array('en' => "Only VB3-A, MX5-A or MX5-L Series, Snap-Action breakers may be inserted in the PD"),
			'pd_breakers_rule_no' => array('en' => "R57"),
			'robot_radio_title' => array('en' => "Robot Radio"),
			'robot_radio_explanation' => array('en' => "A single OpenMesh OM5P-AN or OM5P-AC radio must be powered via a VRM +12 volt, 2 amp output.
The VRM must connect to the dedicated +12 volt output on the PDP. Radio LEDs are easily visible."),
			'robot_radio_rule_no' => array('en' => "R54, R55, R65"),
			'can_bus_title' => array('en' => "CAN BUS"),
			'can_bus_explanation' => array('en' => "The RoboRio and PDP must be connected via CAN wiring even if no other CAN devices are used."),
			'can_bus_rule_no' => array('en' => "R79"),
			'robo_rio_title' => array('en' => "RoboRio Power"),
			'robo_rio_explanation' => array('en' => "Only the RoboRio must be connected to dedicated power terminals on PDP."),
			'robo_rio_rule_no' => array('en' => "R53"),
			'wire_breaker_size_title' => array('en' => "Wire Size Minimum and Breaker Size"),
			'wire_breaker_size_explanation' => array('en' => "obey the wiring size conventions."),
			'wire_size_a_title' => array('en' => ""),
			'wire_size_a_explanation' => array('en' => "All wire from battery to main breaker to PDP must be min 6 AWG (7 SWG or 16mm2) wire"),
			'wire_size_a_rule_no' => array('en' => "R47 & Fig. 10-9"),
			'wire_size_b_title' => array('en' => ""),
			'wire_size_b_explanation' => array('en' => "40 amp breakers must have min 12 AWG (13 SWG or 4 mm2) wire"),
			'wire_size_b_rule_no' => array('en' => "R60"),
			'wire_size_c_title' => array('en' => ""),
			'wire_size_c_explanation' => array('en' => "30 amp breakers must have min 14 AWG (16 SWG or 2.5 mm2) wire"),
			'wire_size_c_rule_no' => array('en' => "R60"),
			'wire_size_d_title' => array('en' => ""),
			'wire_size_d_explanation' => array('en' => "20 amp breakers must have min #18 AWG (18 SWG or 1 mm2) wire"),
			'wire_size_d_rule_no' => array('en' => "R60"),
			'wire_colors_title' => array('en' => "Wire Colors"),
			'wire_colors_explanation' => array('en' => "All power wire must be color coded - red, white, brown, yellow, or black w/stripe for +24, +12, +5 VDC
supply (positive) wires and black or blue for common (negative) for supply return wires"),
			'wire_colors_rule_no' => array('en' => "R62"),
			'copper_wire_title' => array('en' => "Copper Wire Only"),
			'copper_wire_explanation' => array('en' => "All wire used on robot must be copper wire, stranded preferred. (Signal wire excluded)"),
			'copper_wire_rule_no' => array('en' => "R60"),
			'wire_wago_title' => array('en' => "1 Wire Per WAGO"),
			'wire_wago_explanation' => array('en' => "Only 1 wire may be inserted in each WAGO terminal, splices and/or terminal blocks, may be used to
distribute power to multiple branch circuits but all wires in the splice are subject to the Wire Size rules <R56>
____ Motors –Only motors listed per table 10-1"),
			'wire_wago_rule_no' => array('en' => "R56"),
			'motors_title' => array('en' => "Motors"),
			'motors_explanation' => array('en' => "Only motors listed per table 10-1"),
			'motors_rule_no' => array('en' => "R34"),
			'actuators_title' => array('en' => "Actuators"),
			'actuators_explanation' => array('en' => "Electrical solenoid actuators, max. 1 in. stroke and no greater than 10 watts@12V continuous duty,"),
			'actuators_rule_no' => array('en' => "R34"),
			'actuator_power_title' => array('en' => "Motor/Actuator Power"),
			'actuator_power_explanation' => array('en' => "Each motor controller may have one motor connected to the load terminals with exceptions in
Table 10-2, (R37), and single specified motors may be connected to Spike or Automation Direct Relay (however multiple
pneumatic valves may be driven by a single Spike). CIMs and specified other motors must be fed by speed controllers
only. Two PWM controllers can be connected by a PWM “Y” cable."),
			'actuator_power_rule_no' => array('en' => "R36, R37 & Table 10-2"),
			'actuator_control_title' => array('en' => "Motor/Actuator Control"),
			'actuator_control_explanation' => array('en' => "Motors/actuators must be controlled by legal motor controllers and driven directly by PWM
signals from RoboRio or through legal MXP board or by CAN bus."),
			'actuator_control_rule_no' => array('en' => "R77-R80"), 
			'additional_electronics_title' => array('en' => "Custom Circuits, Sensors and Additional Electronics"),
			'additional_electronics_explanation' => array('en' => "cannot directly control speed controllers, relays, actuators or
servos. Custom Circuits may not produce voltage exceeding 24V."),
			'additional_electronics_rule_no' => array('en' => "R52 & R63"),
			'pcm_title' => array('en' => "Pneumatic Control Module (PCM)"),
			'pcm_explanation' => array('en' => "PCM modules must be connected to RoboRio via CAN bus"),
			'pcm_rule_no' => array('en' => "R78"),
			'spike_fuse_title' => array('en' => "Spike Fuse"),
			'spike_fuse_explanation' => array('en' => "Spike must have 20 amp fuse installed. When used for compressor control only, the Spike fuse may be
replaced with 20 amp, snap action, breaker (recommended)."),
			'spike_fuse_rule_no' => array('en' => "R73.D"),
			'isolated_frame_title' => array('en' => "Isolated Frame"),
			'isolated_frame_explanation' => array('en' => "Frame must be electrically isolated from battery, RoboRio must be insulated from frame. (>3k Ohm
between either PD battery post and chassis)"),
			'isolated_frame_rule_no' => array('en' => "R49"),
			'pneumatic_system_title' => array('en' => "Pneumatic System using one on-board compressor (n/a for robots that do not use pneumatics)"),
			'modifications_title' => array('en' => "No Modifications"),
			'modifications_explanation' => array('en' => "No Modifications"),
			'modifications_rule_no' => array('en' => "No Modifications"),
			'compressor_title' => array('en' => "Compressor"),
			'compressor_explanation' => array('en' => "Compressor"),
			'compressor_rule_no' => array('en' => "Compressor"),
			'compressor_power_title' => array('en' => "Compressor Power"),
			'compressor_power_explanation' => array('en' => "Compressor Power"),
			'compressor_power_rule_no' => array('en' => "Compressor Power"),
			'compressor_control_title' => array('en' => "Compressor Control"),
			'compressor_control_explanation' => array('en' => "Compressor Control"),
			'compressor_control_rule_no' => array('en' => "Compressor Control"),
			'vpv_title' => array('en' => "Vent Plug Valve"),
			'vpv_explanation' => array('en' => "Vent Plug Valve"),
			'vpv_rule_no' => array('en' => "Vent Plug Valve"),
			'tubing_title' => array('en' => "Tubing"),
			'tubing_explanation' => array('en' => "Tubing"),
			'tubing_rule_no' => array('en' => "Tubing"),
			'gauges_title' => array('en' => "Gauges"),
			'gauges_explanation' => array('en' => "Gauges"),
			'gauges_rule_no' => array('en' => "Gauges"),
			'pressure_title' => array('en' => "Pressure Rating"),
			'pressure_explanation' => array('en' => "Pressure Rating"),
			'pressure_rule_no' => array('en' => "Pressure Rating"),
			'valve_control_title' => array('en' => "Valve Control"),
			'valve_control_explanation' => array('en' => "Valve Control"),
			'valve_control_rule_no' => array('en' => "Valve Control"),
			'power_check_title' => array('en' => 'Power On Check (Driver Station must be tethered to the Robot)'),
			'wireless_title' => array('en' => "Unauthorized Wireless Communication"),
			'wireless_explanation' => array('en' => "no wireless communication to/from ROBOT or OPERATOR CONSOLE
without prior FIRST written permission. No radios allowed on the OPERATOR CONSOLE or in the pit"),
			'wireless_rule_no' => array('en' => "R70, R99"),
			'pneumatics_operation_title' => array('en' => "Confirm Pneumatics Operation"),
			'pneumatics_operation_explanation' => array('en' => "With no pressure in system, compressor should start when robot is enabled."),
			'compressor_automatic_title' => array('en' => "Compressor should stop automatically"),
			'compressor_automatic_explanation' => array('en' => "at ~120 psi under RoboRio control."),
			'compressor_automatic_rule_no' => array('en' => "R87"),
			'main_pressure_title' => array('en' => "Check that Main Pressure"),
			'main_pressure_explanation' => array('en' => "<= 120 psi <R87> and Working Pressure <= 60 psi"),
			'main_pressure_rule_no' => array('en' => "R87 & R88"),
			'relief_valve_title' => array('en' => "Compressor Relief Valve"),
			'relief_valve_explanation' => array('en' => "set to 125 psi, attached to (or through legal fittings) compressor outlet port."),
			'relief_valve_rule_no' => array('en' => "R91"),
			'pressure_regulator_title' => array('en' => "Relieving Pressure Regulator"),
			'pressure_regulator_explanation' => array('en' => "Set to <= 60 psi, providing all working pressure."),
			'pressure_regulator_rule_no' => array('en' => "R88"),
			'signal_light_title' => array('en' => "Robot Signal Light(s)"),
			'signal_light_explanation' => array('en' => "The Robot Signal Light (two max.) from the KOP must be visible from 3’ in front of the robot,
and be plugged into the RSL port on RoboRio. Confirm that the RSL flashes in sync with RoboRio."),
			'signal_light_rule_no' => array('en' => "R72"),
			'teamnumber_ds_title' => array('en' => "Verify Team Number on DS"),
			'teamnumber_ds_explanation' => array('en' => "team has programmed the OpenMesh Wireless Bridge at kiosk for this event."),
			'teamnumber_ds_rule_no' => array('en' => "R68"),
			'software_versions_title' => array('en' => "Software Versions"),
			'software_versions_explanation' => array('en' => "The RoboRio image (FRC_2019_v12 or later) and DS (19.0 or later) must be loaded"),
			'software_versions_rule_no' => array('en' => "R64 & R95"),
			'power_off_title' => array('en' => "Power Off"),
			'power_off_explanation' => array('en' => "Disable robot and open Main Breaker to remove power from the robot, confirm all LEDs are off, actuate
pneumatic vent plug valve and confirm that all pressure is vented to atmosphere and all gauges read 0 psi pressure."),
			'driver_console_title' => array('en' => "Driver Console is less than 60” x 14” x 6’6” above floor (approx.)."),
			'driver_console_explanation' => array('en' => "May have hook and loop hook side attached to
secure to Driver’s Station shelf."),
			'driver_console_rule_no' => array('en' => "R98"),
							
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
