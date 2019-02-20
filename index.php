<?php
	session_start();
	if(isset($_POST['logoff'])){
		$_SESSION['user_id'] = null;
		session_destroy();
	}
	if(!isset($_SESSION['user_id'])){
		header("Location: /login.php");
	}
	
	include("db_conn.php");
?>
<html>
	<head>
		<meta charset="UTF-8"/>
		<title>FRC Inspection System</title>
	</head>
	<body>
		<p>
			<form action=inspect.php method=GET>
				<h1>Robot Inspector</h1>
				<label for=team>Select Team</label>
				<select name=team id=team>
				<?php
					$sql = "SELECT team_number FROM inspections".($_SESSION['is_admin'] == "1" ? "" : " WHERE assigned_inspector = $_SESSION[user_id]");
					$res = mysqli_query($conn, $sql);
					while($row = mysqli_fetch_assoc($res)){
						echo("<option value=$row[team_number]>$row[team_number]</option>");
					}
				?>
				</select>
				<input type=submit value=Inspect>
			</form>
<?php
			if($_SESSION['is_admin']) echo("
			<h1>Lead Robot Inspector / Inspection Manager</h1>
			<a href=assign.php>Assign Inspectors</a><br/>
			<a href=teams.php>Manage Teams</a>
		</p>"); ?>
		<form action=# method=POST><input type=submit name=logoff value="Log Out"></form>
	</body>
</html>