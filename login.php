<!-- login.php is for existing user to log in to the book page
    = Created by =
    Hei Tung Wong
    #101664795
    Tuesday 2:30pm lab
    ==============
 -->
<!DOCTYPE html>
<HTML XMLns="http://www.w3.org/1999/xHTML"> 
<head> 
	<meta charset="utf-8">
	<title>Login to CabsOnline</title>
	<style type="text/css">
	.redirect {padding-top:20px;}
  a {border-color: #00d278; border-style: dotted; padding:12px;}
	a:hover {color: #00d278;}
  input[type=submit] {margin: 2px 2px; border-radius: 8px; font-size: 18px;}
	.errorMsg {color:red; font-weight:bold;}
	</style>
</head>
	<?php 
		/* TODO: check
		 1. all inputs
		 2. if is an existing user - email & pw matches with db
		 3. if non existed user - error msg
		 4. if is an admin - correct pw
		*/

		// Starting the session, necessary for using session variables 
		session_start(); 

		// define variables as empty values
		$emailErr = $submitErr = "";
		$adminPW = "admin";
		$password = $email = "";

		// Validate posted inputs
		if ($_SERVER["REQUEST_METHOD"] == "POST") {		
			if (empty($_POST["email"])) {
				$emailErr = "Email is requried";
			} elseif ($_POST["email"] == "admin") {	// if 'admin' is the email
				adminCheck($adminPW);		// check if the password is correct too
			} elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
				// check if the email is well formed
				$emailErr = "Invalid email address";
			} elseif (!check_email_exist($_POST['email'])) {
				$submitErr = "This email is not registered yet! Please check again!";
			} else {
				// customer exists
				echo "<script>console.log('Customer existed!');</script>";
				$email = $_POST['email'];
				$password = $_POST['password'];
				// authenication
				if (!auth($email, $password)){
					$submitErr = "Login failed. Please double check your inputs!";
				}
			}
		}

		// check if the password is 'admin'
		function adminCheck($adminPW) {
			// if yes redirect admin to the admin page
			if ($_POST['password'] == $adminPW) {
				header('Location: admin.php'); 
			} else {
				$submitErr = "Invailed login.";
			}
		}

		// check if the email & password match with the db
		// if correct redirect user to booking page
		function auth($email, $password) {
			// connect to db
			$DBConnect = @mysqli_connect("feenix-mariadb.swin.edu.au", "s101664795","250394", "s101664795_db")
			Or die ("<p>Unable to connect to the database server.</p>". "<p>Error code ". mysqli_connect_errno().": ". mysqli_connect_error()). "</p>";
			
			// Query to fetch email from customer table
			$SQLstring = "SELECT * FROM customer WHERE email_address = '".$email."' and password = '".$password."' ";
			
			// Execute the query, store the result set
			$queryResult = @mysqli_query($DBConnect, $SQLstring)
			Or die ("<p>Unable to query the customer table.</p>"."<p>Error code ". mysqli_errno($DBConnect). ": ".mysqli_error($DBConnect)). "</p>";

			// if number of rows > 0, pw matches the email
			if (mysqli_num_rows($queryResult)>0) {
				mysqli_free_result($queryResult);
				
				// store the email in session variable
				$_SESSION['email'] = $email;
				// redirect user to booking.php, carry info
				header('Location: booking.php'); 
				exit();
				return true;
			} else {
				return false;
			}
		}

		// check if the email already existed in the db
		// if no, no allow to log in
		function check_email_exist($emailCheck) {
			$DBConnect = @mysqli_connect("feenix-mariadb.swin.edu.au", "s101664795","250394", "s101664795_db")
			Or die ("<p>Unable to connect to the database server.</p>". "<p>Error code ". mysqli_connect_errno().": ". mysqli_connect_error()). "</p>";
			
			$SQLstring = " SELECT email_address FROM customer WHERE email_address = '".$emailCheck."' ";
			
			$queryResult = @mysqli_query($DBConnect, $SQLstring)
			Or die ("<p>Unable to query the customer table.</p>"."<p>Error code ". mysqli_errno($DBConnect). ": ".mysqli_error($DBConnect)). "</p>";

			// if number of rows > 0, customer exists
			if (mysqli_num_rows($queryResult)>0) {
				// $row = mysqli_num_rows($queryResult);
				// clost the result
				mysqli_free_result($queryResult);
				return true;
			} else {
				// customer not exist
				return false;
			}
			// Connection close
			mysqli_close($DBConnect);
		}
	?>

	<body>
    <H1>Login to CabsOnline</H1>
		<form 
			method="post"
			action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
		>
			<label for="">Email: <input type="text" name="email">
				<span class="errorMsg"> <?php echo $emailErr;?></span>
			</label><br><br>
			<label for="">Password: <input type="password" name="password" id="password">
			</label><br><br>
			<label for=""><input type="checkbox" onclick="showPw('password')">Show Password</label>
			<br><br>
			<input type="submit" value="Login" />
				<span class="errorMsg"> <?php echo $submitErr;?></span>
				<br/>
		</form>
		<div class="redirect">
			<h3>New member?</h3><a href="register.php">Register here</a>
		</div>
		<script>
		function showPw(id) {
			var x = document.getElementById(id);
			if (x.type === 'password') {
				x.type = "text";
			} else {
				x.type = 'password';
			}
		}
	</script>
  </body>
</HTML>