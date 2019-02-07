<?php
	session_start();
	include("db_conn.php");
	if(isset($_POST['go'])){
		$sql = "SELECT salt FROM inspectors WHERE username LIKE \"".mysqli_real_escape_string($conn, $_POST['username'])."\"";
		$res = mysqli_query($conn, $sql) or die(mysqli_error($conn)."<br/>\n$sql");
		$row = mysqli_fetch_array($res);
		if($row === NULL){
			$msg = "Incorrect username or password";
		}else{
			$salt = $row[0];
			$sql = "SELECT * FROM inspectors WHERE username LIKE \"".mysqli_real_escape_string($conn, $_POST['username'])."\" AND password LIKE \"".mysqli_real_escape_string($conn, hash("sha256", $salt.$_POST['password']))."\"";
			$res = mysqli_query($conn, $sql) or die(mysqli_error($conn)."<br/>\n$sql");
			$row = mysqli_fetch_assoc($res);
			if($row === NULL){
				$msg = "Incorrect username or password.";
			}else{
				$_SESSION['user_id'] = $row['inspector_id'];
				$_SESSION['is_admin'] = $row['is_admin'];
				header("Location: /index.php");
			}
		}
	}
?>
<html>
	<head>
		<meta charset="UTF-8"/>
		<title>FRC Inspection System</title>
	</head>
	<body>
		<p>
			<form action=# method=POST>
				<label for=username>Username</label><input type=text name=username id=username />
				<label for=username>Password</label><input type=password name=password id=password />
				<?php if(isset($msg)) echo("<span id=msg>$msg</span>"); ?>
				<input type=submit name=go value="Log In"/>
			</form>
		</p>
	</body>
</html>