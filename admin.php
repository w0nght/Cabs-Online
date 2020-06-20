<!-- admin.php is for admin to assign cabs & check the updates
    = Created by =
    Hei Tung Wong
    #101664795
    Tuesday 2:30pm lab
    ==============
 -->
 <?php
	session_start(); 

	// logout button will destroy the session
	// the session variables will be set to unset
	// user will be redirect to the login page after logging out 
	if (isset($_GET['logout'])) { 
			session_destroy(); 
			unset($_SESSION['email']); 
			header("location: login.php"); 
  }
  $submitErr = "";
  /* TODO:
  1. List all booking requests within 3 hours pick-up time
  2. order by time
  3. Able to search for specific ref number
  4. get current time, current time +3 hrs
  5. update button to change status from unassigned to assigned
  6. display confirmation informaion
  7. if no matches display error msg
*/
?> 
<!DOCTYPE html>
<HTML XMLns="http://www.w3.org/1999/xHTML"> 
<head> 
	<meta charset="utf-8">
	<title>CabsOnline Booking</title>
	<style type="text/css">
  /* body{background-color:#585A5C;} */
	.logout {text-align: right; padding-top:5px;}
  a {border-color: #00d278; border-style: dotted; padding:5px;}
  a:hover {color: #00d278;}
  input[type=submit] {margin: 2px 2px; border-radius: 8px;font-size: 18px;}
  table {border: 4px solid #00d278; width: 90%; text-align: center; border-collapse: collapse;}
  th {border: 1px solid #555555; padding: 5px 2px;}
	.errorMsg{color:red; font-weight:bold;}
	</style>
</head>
	<body>
    <div class="logout"><a href="admin.php?logout='1'">Logout</a></div>
    <H1>Admin page of CabsOnline</H1>
    <form action="" method="post">
      <p>1. Click below button to search for all unassigned booking requests with a pick-up time within 3 hours.</p>
      <input type="submit" value="List All" name="btnList" /><br/>
      <?php
        // date and time was set up in a 24 hour format
  			// i.e. 2020-04-09 00:24:21
        $today = date('Y-m-d H:i:s');
        $availableTime = date('Y-m-d H:i:s', strtotime('+3 hours'));
        
        if (isset($_POST['btnList'])) {
          $DBConnect = @mysqli_connect("feenix-mariadb.swin.edu.au", "s101664795","250394", "s101664795_db")
            Or die ("<p>Unable to connect to the database server.</p>". "<p>Error code ". mysqli_connect_errno().": ". mysqli_connect_error()). "</p>";
    
          // set up the SQL query string
          // use concat to combine date and time into timestamp as we stored them separately
          // compared timestamp 
          $SQLstring = "select b.booking_ref, c.customer_name, b.passenger_name, b.contact_num, b.unit_num, b.street_num, b.street_name, b.suburb, b.destination, 
          concat(b.pick_date, ' ', b.pick_time) as timestamp, b.status 
          FROM customer c, booking b
          where c.email_address = b.email_address
          and concat(b.pick_date, ' ', b.pick_time)<'".$availableTime."' 
          and concat(b.pick_date, ' ', b.pick_time)>'".$today."' 
          and b.status = 'unassigned'
          order by timestamp ";
          $queryResult = @mysqli_query($DBConnect, $SQLstring)
          Or die ("<p>Unable to query the $TableName table.</p>"."<p>Error code ". mysqli_errno($DBConnect). ": ".mysqli_error($DBConnect)). "</p>";

          // print customer table on html page
          echo "<br/><table width='100%' border='1'>";
          echo "<th>reference #</th>
                <th>customer name</th>
                <th>passenger name</th>
                <th>passenger contact no.</th>
                <th>pick-up address</th>
                <th>desstination suburb</th>
                <th>pick-time</th>";

          $row = mysqli_fetch_row($queryResult);
  
          while ($row) {
            // get rid of the "/" when unit no. is null
            if ($row[4] == 0) $row[4] = "";
            else $row[4] = "$row[4]/";
            
            // reformat the date to ----> 15 Mar 18:07
            // was -----> 2020-04-09 00:24:21
            $date = date_create($row[9]);
            $newDate = date_format($date, 'd M H:i');

            echo "<tr><td>{$row[0]}</td>";
            echo "<td>{$row[1]}</td>";
            echo "<td>{$row[2]}</td>";
            echo "<td>{$row[3]}</td>";
            echo "<td>{$row[4]}{$row[5]} {$row[6]}, {$row[7]}</td>";
            echo "<td>{$row[8]}</td>";
            echo "<td> $newDate</td></tr>";
            $row = mysqli_fetch_row($queryResult);
          }
          echo "</table><br/>";
        }
      ?>
      <p>2. Input a reference number below and click "update" button to assign a taxi to that request</p>
      <?php
        $refno = "";
        if (isset($_POST['btnUpdate'])) {
          if (empty($_POST['refno'])) {
            $submitErr = "Empty input";
          } elseif (!is_numeric($_POST["refno"])) {
            $submitErr = "Only integer allowed";
          } else {
            $refno = $_POST['refno'];
            $DBConnect = @mysqli_connect("feenix-mariadb.swin.edu.au", "s101664795","250394", "s101664795_db")
              Or die ("<p>Unable to connect to the database server.</p>". "<p>Error code ". mysqli_connect_errno().": ". mysqli_connect_error()). "</p>";
            // query to check if such reference number existed
            $SQLrefCheck = "SELECT * from booking WHERE booking_ref = '".$refno."' ";
            $refCheckResult = @mysqli_query($DBConnect, $SQLrefCheck)
            Or die ("<p>Unable to query the booking table.</p>"."<p>Error code ". mysqli_errno($DBConnect). ": ".mysqli_error($DBConnect)). "</p>";
      
            // if number of rows > 0, ref no. exists
            if (mysqli_num_rows($refCheckResult)>0) {
              // update query, set the status from unassigned to assigned
              $SQLupdate = " UPDATE booking SET status='assigned' WHERE booking_ref = '".$refno."' ";
              $updateResult = @mysqli_query($DBConnect, $SQLupdate)
               Or die ("<p>Unable to query the booking table.</p>"."<p>Error code ". mysqli_errno($DBConnect). ": ".mysqli_error($DBConnect)). "</p>";
              // alert msg
              echo "<script>alert('The booking request ref#$refno has been properly assigned!');</script>";
            } else {
              // ref not exist
              $submitErr = "reference number does not exist";
            }
            // Connection close
            mysqli_close($DBConnect);
          }
        }
      ?>
      <label for="">Reference number: <input type="text" name="refno" id="refno" />
			</label><span class="errorMsg"> <?php echo $submitErr;?></span>
      <input type="submit" value="Update" name="btnUpdate" />
    </form>
  </body>
</HTML>