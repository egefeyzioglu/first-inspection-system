<?php
	session_start();
	if(isset($_POST['logoff'])){
		$_SESSION['user_id'] = null;
		session_destroy();
		header("Location: index.php");
		die();
	}
	if(!isset($_SESSION['user_id'])){
		header("Location: /login.php");
		die();
	}
	if(!$_SESSION['is_admin']){
		header("Location: index.php");
		die();
	}
	include("db_conn.php");
?>
<html>
	<head>
		<meta charset="UTF-8"/>
		<title>FRC Inspection System</title>
	</head>
	<body>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
		<script>
			$(function(){
				$('input.assign_radio').change(
					function(){
						var radios = $('input.assign_radio:checked');
						for(var radio_index = 0; radio_index < radios.length; radio_index++){
							var radio = radios[radio_index];
							if(radio.getAttribute("value") == "-1"){
								radio.parentElement.parentElement.setAttribute("style", "background-color:red");
							}else{
								radio.parentElement.parentElement.setAttribute("style", "");
							}
						}}
				)
				$('input.assign_radio').change();
			});
		</script>
		<form action=# method=POST>
		<table>
			<tr><th colspan=<?php $res = mysqli_query($conn, "SELECT COUNT(*) FROM inspectors"); echo(mysqli_fetch_array($res)[0] + 1); ?>>Inspectors</th></tr>
			<tr><td></td><?php $res = mysqli_query($conn, "SELECT * FROM inspectors"); while($row = mysqli_fetch_assoc($res)){$inspectors[$row['inspector_id']] = $row; echo("<td>$row[name] $row[lastname]</td>");} ?><td>Unassigned</td></tr>
<?php
				
				$inspectors[-1] = array("inspector_id" => -1, "team_affiliation" => "");
				$sql = "SELECT * FROM teams, inspections WHERE teams.team_number = inspections.team_number";
				$res = mysqli_query($conn, $sql) or die(mysqli_error($conn)."<br/>\n$sql");
				while($row = mysqli_fetch_assoc($res)){
					$teams[$row['team_number']] = $row;
				}
				
				if(isset($_POST['save'])){
					foreach($teams as $team){
						$sql = "UPDATE inspections SET assigned_inspector=".$_POST[$team['team_number']]." WHERE team_number=".$team['team_number'];
						mysqli_query($conn, $sql);
					}
				}
				
				$sql = "SELECT * FROM teams, inspections WHERE teams.team_number = inspections.team_number";
				$res = mysqli_query($conn, $sql) or die(mysqli_error($conn)."<br/>\n$sql");
				while($row = mysqli_fetch_assoc($res)){
					$teams[$row['team_number']] = $row;
				}
				
				foreach($teams as $team){
					echo("\t\t\t<tr>\n\t\t\t\t<td>$team[team_number]</td>\n");
					foreach($inspectors as $inspector){
						echo("\t\t\t\t<td".($team['team_number'] == $inspector["team_affiliation"] ? " style=\"background-color:gray;\" ":"")."><input type=radio class=assign_radio name=$team[team_number] value=$inspector[inspector_id] ".($team['assigned_inspector'] == $inspector['inspector_id'] ? " checked ":"")."/></td>\n");
					}
					echo("\t\t\t</tr>\n");
				}
?>
		<tr><td><input colspan=5 type=Submit name=save></td></tr>
		</table>
		</form>
		<form action=# method=POST><input type=submit name=logoff value="Log Out"></form>
	</body>
</html>