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
	
	if(isset($_POST['add_go'])){
		$salt = random_bytes(10);
		$hashed_password = hash("sha256", $salt.$_POST['password']);
		
		$sql = "INSERT INTO inspectors(inspector_id, name, lastname, username, salt, password, team_affiliation, is_admin) VALUES (0, \"".mysqli_real_escape_string($conn, $_POST['name'])."\", \"".mysqli_real_escape_string($conn, $_POST['lname'])."\", \"".mysqli_real_escape_string($conn, $_POST['username'])."\", \"$salt\", \"$hashed_password\", ".(mysqli_real_escape_string($conn, $_POST['team_affiliation']) == "" ? "NULL" : "\"".mysqli_real_escape_string($conn, $_POST['team_affiliation'])."\"").", \"".($_POST['is_admin'] == "on" ? "1" : "0")."\")";
		mysqli_query($conn, $sql) or die(mysqli_error($conn)."<br/>\n$sql");
	}
	
	if(isset($_POST['del_no'])){
		$sql = "DELETE FROM inspectors WHERE inspector_id=".mysqli_real_escape_string($conn, $_POST['del_no']);
		mysqli_query($conn, $sql) or die(mysqli_error($conn)."<br/>\n$sql");
	}
?>
<html>
	<head>
		<meta charset="UTF-8"/>
		<title>FRC Inspection System</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
	</head>
	<body>
		<a href="/">Home</a>
		<table>
			<h2>Manage Users</h2>
			<tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Username</th><th>Team Affiliation</th><th>Is Admin</th></tr>
			<?php 
				$sql = "SELECT * FROM inspectors";
				$res = mysqli_query($conn, $sql);
				while($row = mysqli_fetch_assoc($res)){
					echo("<tr><td>$row[inspector_id]</td><td>$row[name]</td><td>$row[lastname]</td><td>$row[username]</td><td>".(!$row['team_affiliation'] ? "None" : $row['team_affiliation'])."</td><td>".($row['is_admin'] == 1 ? "Yes" : "No")."</tr>");
				}
			?>
		</table>
		<form action=# method=POST>
			<h2>Add User</h2>
			<table>
				<tr><td><label for=name>First Name</label></td><td><input type=text name=name></td></tr>
				<tr><td><label for=lname>Last Name</label></td><td><input type=text name=lname></td></tr>
				<tr><td><label for=username>Username</label></td><td><input type=text name=username></td></tr>
				<tr><td><label for=password>Password</label></td><td><input type=password name=password></td></tr>
				<tr><td><label for=team_affiliation>Team Affiliation</label></td><td><input type=text name=team_affiliation></td></tr>
				<tr><td><label for=is_admin>Is Admin</label></td><td><input type=checkbox name=is_admin></td></tr>
				<tr><td colspan=2><input type=Submit name=add_go></td></tr>
			</table>
		</form>
		<form action=# method=POST>
			<h2>Delete User</h2>
			<label for=del_no>User to Delete (Enter ID)</label><input type=number name=del_no id=del_no /><br/>
			<button type=button onclick="if(confirm('Are you sure to delete this user? You will not be able to undo this')){$(this).parent().submit();}">Delete</button>
		</form>
		<form action=# method=POST><input type=submit name=logoff value="Log Out"></form>
	</body>
</html>