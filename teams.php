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
	
	if(isset($_POST['add_team'])){
		$sql = "INSERT INTO teams(team_number, team_mentour, team_captain, team_name) VALUES(\"".mysqli_real_escape_string($conn, $_POST['team_number'])."\", \"".mysqli_real_escape_string($conn, $_POST['team_mentour'])."\", \"".mysqli_real_escape_string($conn, $_POST['team_captain'])."\", \"".mysqli_real_escape_string($conn, $_POST['team_name'])."\")";
		if(!mysqli_query($conn, $sql)){
			if(mysqli_errno($conn) == 1062) $msg = "Duplicate team number!"; else die(mysqli_error($conn)."<br/>\n".mysqli_errno($conn)."$sql");
		}else{
			$sql = "INSERT INTO inspections(inspection_id, team_number, assigned_inspector, inspection_completed, captain_signature, mentour_signature, initial_inspection_signature, reinspection_signature, final_inspection_signature) VALUES(0, \"".mysqli_real_escape_string($conn, $_POST['team_number'])."\", -1, NULL, \"\", \"\", \"\", \"\", \"\")";
			mysqli_query($conn, $sql) or die(mysqli_error($conn)."<br/>\n$sql");
		}
	}
	
	if(isset($_POST['delete_number'])){
		$sql = "DELETE FROM teams WHERE team_number=".mysqli_real_escape_string($conn, $_POST['delete_number']);
		mysqli_query($conn, $sql) or die(mysqli_error($conn)."<br/>\n$sql");
		
		$sql = "DELETE FROM inspections WHERE team_number=".mysqli_real_escape_string($conn, $_POST['delete_number']);
		mysqli_query($conn, $sql) or die(mysqli_error($conn)."<br/>\n$sql");
	}
	
	$sql = "SELECT * FROM teams, inspections WHERE inspections.team_number = teams.team_number";
	$res = mysqli_query($conn, $sql);
	
	while($row = mysqli_fetch_assoc($res)){
		$teams[$row['team_number']] = $row;
	}
	
	foreach($teams as $team){
		if(isset($_POST[$team['team_number']."_go"])){
			$sql = "UPDATE teams SET team_name=\"".mysqli_real_escape_string($conn, $_POST['name'])."\", team_captain=\"".mysqli_real_escape_string($conn, $_POST['captain'])."\", team_mentour=\"".mysqli_real_escape_string($conn, $_POST['mentour'])."\" WHERE team_number=".mysqli_real_escape_string($conn, $team['team_number']);
			mysqli_query($conn, $sql) or die(mysqli_error($conn)."<br/>\n$sql");
		}
	}
?>
<html>
	<head>
		<meta charset="UTF-8"/>
		<title>FRC Inspection System</title>
	</head>
	<body>
		<a href="/">Home</a>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
		<table>
			<tr><th>Team Number</th><th>Team Name</th><th>Team Captain</th><th>Team Mentour</th></tr>
			<?php
				$sql = "SELECT * FROM teams, inspections WHERE inspections.team_number = teams.team_number";
				$res = mysqli_query($conn, $sql);
				while($row = mysqli_fetch_assoc($res)){
					echo("<tr><form action=# method=POST><td>$row[team_number]</td><td><input name=name value=\"$row[team_name]\"/></td>
					<td><input name=captain value=\"$row[team_captain]\"</td><td><input name=mentour value=\"$row[team_mentour]\"/></td><td><input type=Submit name=$row[team_number]"."_go value=Save></td></form></tr>");
				}
			?>
		</table>
		<b>Add Team</b>
		<form action=# method=POST>
			<table>
				<tr><th>Team Number</th><th>Team Name</th><th>Team Captain</th><th>Team Mentour</th></tr>
				<tr><td><input name=team_number /></td><td><input name=team_name /></td><td><input name=team_captain /></td><td><input name=team_mentour /></td><td colspan=4><input type=Submit name=add_team /></td></tr>
				<?php if(isset($msg)) echo("<tr><td colspan=4>$msg</td></tr>"); ?>
			</table>
		</form>
		<form action=# method=POST>
			<label for=delete_number>Delete Team</label><input type=text name=delete_number id=delete_number />
			<button type=button onclick="if(confirm('Are you sure you want to delete? You cannot undo this.'))$(this).parent().submit();">DELETE</button>
		</form>
		<form action=# method=POST><input type=submit name=logoff value="Log Out"></form>
	</body>
</html>