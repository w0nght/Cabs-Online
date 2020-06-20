<!-- booking.php is for customer to book a cab
    = Created by =
    Hei Tung Wong
    #101664795
    Tuesday 2:30pm lab
    ==============
 -->
 <?php
	session_start(); 
		
	// if the session variable is empty
	// user will redirect to the login page
	if (!isset($_SESSION['email'])) { 
		header('location: login.php'); 
	}
		
	// logout button will destroy the session
	// the session variables will be set to unset
	// user will be redirect to the login page after logging out 
	if (isset($_GET['logout'])) { 
			session_destroy(); 
			unset($_SESSION['email']); 
			header("location: login.php"); 
	} 
?> 
<!DOCTYPE html>
<HTML XMLns="http://www.w3.org/1999/xHTML"> 
<head> 
	<meta charset="utf-8">
	<title>CabsOnline Booking</title>
	<style type="text/css">
	.logout {text-align: right; padding-top:5px;}
  a {border-color: #00d278; border-style: dotted; padding:5px;}
	a:hover {color: #00d278;}
	.form_address {padding-left: 120px; padding-top:20px;}
  input[type=submit] {margin: 2px 2px; border-radius: 8px; font-size: 18px;}
	.errorMsg{color:red; font-weight:bold;}
	</style>
</head>
  <?php
    /* TODO:
      1. All input items except unit number must be provided
			2. pickup date/time must be AT LEAST 40 min after current booking date/time; otherwise error message
			3. form wont be able to submit & insert to db if above points don't match
      4. after generate unique booking ref no.;
        display book date/time
        status - unassigned
		*/
		$passengerErr = $contactNumErr = $streetNumErr = $streetNameErr = $suburbErr = $destinationErr = $pickDateErr = $pickTimeErr = "";
		$passenger = $contactNum = $unitNum = $streetNum = $streetName = $suburb = $destination = $pickDate = $pickTime = $status = "";

		// Validate posted inputs
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			// set up the current date and time in 24 hour format
			// i.e. 2020-04-09 00:24:21
			$today = date('Y-m-d H:i:s');
			$currentDate = date('Y-m-d');
			$currentTime = date('H:i');
			$availableTime = date('H:i', strtotime('+40 minutes'));

			if (empty($_POST["passenger"])) {
				$passengerErr = "Passenger name is requried";
			} elseif (!preg_match("/^[a-zA-Z ]*$/", $_POST["passenger"])) {
				$passengerErr = "Only letters and white space allowed";
			}	else {
				$passenger = $_POST["passenger"];
			}

			if (empty($_POST["contact"])) {
				$contactNumErr = "Contact number is requried";
			} elseif (!is_numeric($_POST["contact"])) {
				$contactNumErr = "Phone number must be 10 digits integer";
			} else {
				$contactNum = $_POST["contact"];
			}

			if (empty($_POST['unitNum'])) {
				$unitNum = "NULL";
			} else {
				$unitNum = $_POST['unitNum'];
			}

			if (empty($_POST["streetNum"])) {
				$streetNumErr = "Street number is requried";
			} elseif (!is_numeric($_POST["streetNum"])) {
				$streetNumErr = "Only integer allowed";
			}	else {
				$streetNum = $_POST["streetNum"];
			}

			if (empty($_POST["streetName"])) {
				$streetNameErr = "Street name is requried";
			} elseif (!preg_match("/^[a-zA-Z ]*$/", $_POST["streetName"])) {
				$streetNameErr = "Only letters and white space allowed";
			}	else {
				$streetName = $_POST["streetName"];
			}

			if (empty($_POST["suburb"])) {
				$suburbErr = "Suburb is requried";
			} elseif (!preg_match("/^[a-zA-Z ]*$/", $_POST["suburb"])) {
				$suburbErr = "Only letters and white space allowed";
			}	else {
				$suburb = $_POST["suburb"];
			}

			if (empty($_POST["destination"])) {
				$destinationErr = "Destination is requried";
			} elseif (!preg_match("/^[a-zA-Z ]*$/", $_POST["destination"])) {
				$destinationErr = "Only letters and white space allowed";
			}	else {
				$destination = $_POST["destination"];
			}

			if (empty($_POST["pickDate"])) {
				$pickDateErr = "Pick up date is requried";
			} if (empty($_POST["pickTime"])) {
				$pickTimeErr = "Pick up time is requried";
			} if ($_POST["pickDate"] < $currentDate) {
				$pickDateErr = "Cannot be a passed date";
			} elseif ($_POST["pickTime"] < $currentTime) {
				$pickTimeErr = "Cannot be a passed time";
			} if ($_POST["pickDate"] == $currentDate && $_POST["pickTime"] < $availableTime) {
				$pickTimeErr = "Please allow at least 40 minutes for us to prepare";
			} else {
				$pickDate = $_POST["pickDate"];
				$pickTime = $_POST["pickTime"];
			}

			// store informations to db if things we want are not empty
			if (!empty($passenger) && !empty($contactNum) && !empty($streetNum) && !empty($streetName) && !empty($suburb) && !empty($destination) && !empty($pickDate) && !empty($pickTime) && !empty($currentDate) && !empty($currentTime)) {
				// connect to db
				$DBConnect = @mysqli_connect("feenix-mariadb.swin.edu.au", "s101664795","250394", "s101664795_db")
				Or die ("<p>Unable to connect to the database server.</p>". "<p>Error code ". mysqli_connect_errno().": ". mysqli_connect_error()). "</p>";
				
				// insert everything into booking table
				$SQLinsert = "INSERT INTO booking (email_address, passenger_name, contact_num, unit_num, street_num, street_name, suburb, destination, pick_date, pick_time, book_date, book_time, status) VALUES ('".$_SESSION['email']."', '$passenger', '$contactNum', '$unitNum', '$streetNum', '$streetName', '$suburb', '$destination', '$pickDate', '$pickTime', '$currentDate', '$currentTime', 'unassigned')";
				
				// Execute the query, store the result set
				$queryResult = @mysqli_query($DBConnect, $SQLinsert)
				Or die ("<p>Unable to query the booking table.</p>"."<p>Error code ". mysqli_errno($DBConnect). ": ".mysqli_error($DBConnect)). "</p>";

				// get auto generated id
				$id = @mysqli_insert_id($DBConnect);
				// use that id to get information for confirmation msg
				$SQLgetbyid = "select b.booking_ref, c.customer_name, c.email_address, b.pick_date, b.pick_time
				from customer c, booking b
				where c.email_address = b.email_address
				and booking_ref = '".$id."' ";
				$queryResult2 = @mysqli_query($DBConnect, $SQLgetbyid)
					Or die ("<p>Unable to query tables.</p>"."<p>Error code ". mysqli_errno($DBConnect). ": ".mysqli_error($DBConnect)). "</p>";
				$row = mysqli_fetch_row($queryResult2);
				
				// alert confirmation msg
				echo "<script>alert('Thank you $row[1]! Your booking reference number is $row[0]. We will pick up the passengers in front of your provided address at $row[4] on $row[3].');</script>";

				// set and send confirmation email
				$to_email_address = $row[2];
				$subject = "Your booking request with CabsOnline!";
				$message = "Dear $row[1], Thanks for booking with CabsOnline! Your booking reference number is $row[0]. We will pick up the passengers in front of your provided address at $row[4] on $row[3].";
				$header = "From: booking@cabsonline.com.au";
				mail($to_email_address,$subject,$message, $header, "-r 101664795@student.swin.edu.au");

				// close the database connection
				mysqli_close($DBConnect);
			}
		}
  ?>
	<body>
		<?php if(isset($_SESSION['email'])) : ?>
			<div class="logout"><p>You logged in under <strong><?php echo $_SESSION['email'];?></strong></p>
			<p>Not you? <a href="booking.php?logout='1'">Logout</a></p></div>
		<?php endif ?>
    <H1>Booking a cab</H1>
		<p>Please fill the fields below to book a taxi</p>
		<p><span class="errorMsg">* required field</span></p><br/>
		<form 
			method="post"
			action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
		>
			<label for="">Passenger name: <input type="text" name="passenger">
				<span class="errorMsg"> *<?php echo $passengerErr;?></span>
			</label><br><br>
			<label for="">Contact phone of the passenger: <input type="text" name="contact" maxlength="10" >
				<span class="errorMsg"> *<?php echo $contactNumErr;?></span>
			</label><br><br>
			<label for="">Pick up address: 
				<div class="form_address">
					<label for="">Unit number: </label>
					<input type="text" name="unitNum"><br><br>
					<label for="">Street number: </label>
					<input type="text" name="streetNum" >
					<span class="errorMsg"> *<?php echo $streetNumErr;?></span><br><br>
					<label for="">Street name: </label>
					<input type="text" name="streetName" >
					<span class="errorMsg"> *<?php echo $streetNameErr;?></span><br><br>
					<label for="">Suburb: </label>
					<input type="text" name="suburb" >
					<span class="errorMsg"> *<?php echo $suburbErr;?></span>
				</label><br><br>
				<label for="">Destination suburb: <input type="text" name="destination" >
					<span class="errorMsg"> *<?php echo $destinationErr;?></span>
				</label><br><br>
			</div>
			<label for="">Pick up date: 
				<input type="date" id="start" name="pickDate"
       min="2020-04-01" max="2040-12-31">
				<span class="errorMsg"> *<?php echo $pickDateErr;?></span>
			</label><br><br>
			<label for="">Pick up time: <input type="time" name="pickTime" >
				<span class="errorMsg"> *<?php echo $pickTimeErr;?></span>
			</label><br><br>
			<input type="submit" value="Book" /> <br/>
		</form>
  </body>
</HTML>