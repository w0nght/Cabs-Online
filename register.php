<!-- register.php is for customer to create an account
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
	<title>Register to CabsOnline</title>
	<style type="text/css">
	.redirect {padding-top:20px;}
  a {border-color: #00d278; border-style: dotted; padding:12px;}
	a:hover {color: #00d278;}
  input[type=submit] {margin: 2px 2px; border-radius: 8px;font-size: 18px;}
	.errorMsg {color:red; font-weight:bold;}
	</style>
</head> 
	<body>
		<?php
			// Starting the session, necessary for using session variables 
			session_start();

			/* TODO: new customer, check
			* 1. all inputs
			* 2. password & confirmed password match
			* 3. email is unique - check against database customer table
			*/

			// define variables as empty values
			$nameErr = $passwordErr = $emailErr = $phoneErr = $submitErr = "";
			$name = $password = $password_con = $email = $phone = "";

			// Validate the posted inputs
			if ($_SERVER["REQUEST_METHOD"] == "POST") {		
				if (empty($_POST["name"])) {
					$nameErr = "Name is required";
				} elseif (!preg_match("/^[a-zA-Z ]*$/", $_POST["name"])) {
					$nameErr = "Only letters and white space allowed";
				}	else {
					$name = $_POST["name"];
				}
				
				if (empty($_POST["password"]) || empty($_POST["password_con"])) {
					$passwordErr = "Password is required";
				} elseif ($_POST["password"] != $_POST["password_con"]) {
					$passwordErr = "Passwords need to be matched";
				} else { // only store the password if matched
					$password = $_POST["password"];
					$password_con = $_POST["password_con"];
				}

				if (empty($_POST["email"])) {
					$emailErr = "Email is requried";
				} elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) { // check if the email is well formed
					$emailErr = "Invalid email address";
				} elseif (!check_email_exist($_POST['email'])) {
					// if it does not existed, store the email
					$email = $_POST['email'];
				} else {
					$submitErr = "Email already existed!";
				}
				
				if (empty($_POST["phone"])) {
					$phoneErr = "Phone number is required";
				} elseif (!is_numeric($_POST["phone"])) {
					$phoneErr = "Phone number must be 10 digits integer";
				} else {
					$phone = $_POST["phone"];
				}

				if (!empty($name) && !empty($password) && !empty($email) && !empty($phone)) {
					create_user($name, $password, $email, $phone);
				}
			}

			// check if the email existed in the db
			function check_email_exist($emailCheck) {
				// connect to db
				$DBConnect = @mysqli_connect("feenix-mariadb.swin.edu.au", "s101664795","250394", "s101664795_db")
				Or die ("<p>Unable to connect to the database server.</p>". "<p>Error code ". mysqli_connect_errno().": ". mysqli_connect_error()). "</p>";
				
				// Query to fetch email from customer table
				$SQLstring = " SELECT email_address FROM customer WHERE email_address = '".$emailCheck."' ";
				
				// Execute the query, store the result set
				$queryResult = @mysqli_query($DBConnect, $SQLstring)
				Or die ("<p>Unable to query the customer table.</p>"."<p>Error code ". mysqli_errno($DBConnect). ": ".mysqli_error($DBConnect)). "</p>";

				// if number of rows > 0, customer exists
				if (mysqli_num_rows($queryResult)>0) {
					$row = mysqli_num_rows($queryResult);
					echo "<script>console.log('Email already existed, with the row of ', $row);</script>";
					// clost the result
					mysqli_free_result($queryResult);
					return true;
				} else {
					$row = mysqli_num_rows($queryResult);
					echo "<script>console.log('Email free to use with the row of ', $row);</script>";
					return false;
				}
				// Connection close
				mysqli_close($DBConnect);
			}

			// store the user information to db & send user to booking page
			function create_user($name, $password, $email, $phone) {
				$DBConnect = @mysqli_connect("feenix-mariadb.swin.edu.au", "s101664795","250394", "s101664795_db")
				Or die ("<p>Unable to connect to the database server.</p>". "<p>Error code ". mysqli_connect_errno().": ". mysqli_connect_error()). "</p>";
				
				$SQLstring2 = "INSERT into customer (email_address, customer_name, password, phone_number) VALUES ('$email', '$name', '$password', '$phone')";

				$queryResultInsert = @mysqli_query($DBConnect, $SQLstring2)
				Or die ("<p>Unable to query the $TableName table.</p>"."<p>Error code ". mysqli_errno($DBConnect). ": ".mysqli_error($DBConnect)). "</p>";
		
				$affactedrow = mysqli_affected_rows($DBConnect);
				echo "<script>console.log('Success!', $affactedrow, ' Row inserted.');</script>";
				echo "<script>alert('You are now registered! Taking you to the booking session...');</script>";

				// store the email in session variable
				$_SESSION['email'] = $email;
				// redirect user to booking.php, carry info
				header('Location: booking.php'); 

				// close the database connection
				mysqli_close($DBConnect);
			}
		?>
		<H1>Register to CabsOnline</H1>
		<p>Please fill the fields below to complete your registration</p>
		<p><span class="errorMsg">* required field</span></p><br/>
		<form 
			method="post"
			action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
		>
			<label for="">Name: <input type="text" name="name"  autofocus>
				<span class="errorMsg">* <?php echo $nameErr;?></span>
			</label><br><br>
			<label for="">Password: <input type="password" name="password" >
				<span class="errorMsg">* <?php echo $passwordErr;?></span>
			</label><br><br>
			<label for="">Confirm password: <input type="password" name="password_con" >
				<span class="errorMsg">* <?php echo $passwordErr;?></span>
			</label><br><br>
			<label for="">Email: <input type="text" name="email" >
				<span class="errorMsg">* <?php echo $emailErr;?></span>
			</label><br><br>
			<label for="">Phone: <input type="text" name="phone" maxlength="10" >
				<span class="errorMsg">* <?php echo $phoneErr;?></span>
			</label><br><br>
			<input type="submit" value="Register" />
				<span class="errorMsg"> <?php echo $submitErr;?></span>
				<br/>
		</form>
		<div class="redirect"><h3>Already register?</h3><a href="login.php">Login here</a></div>
	</body>
</HTML>