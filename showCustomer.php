<!-- showCustomer.php is for debug purposes
    = Created by =
    Hei Tung Wong
    #101664795
    Tuesday 2:30pm lab
    ==============
 -->
<html>
<body>
<?php 
  $DBConnect = @mysqli_connect("feenix-mariadb.swin.edu.au", "s101664795","250394", "s101664795_db")
  Or die ("<p>Unable to connect to the database server.</p>". "<p>Error code ". mysqli_connect_errno().": ". mysqli_connect_error()). "</p>";

  // set up the SQL query string - we will retrieve the whole record that matches the name
  // get all in customer table from db
  $SQLstring = "select * from customer";  
  $queryResult = @mysqli_query($DBConnect, $SQLstring)
  Or die ("<p>Unable to query the $TableName table.</p>"."<p>Error code ". mysqli_errno($DBConnect). ": ".mysqli_error($DBConnect)). "</p>";

  // print customer table on html page
  echo "<h3>customer table</h3>";
  echo "<table width='100%' border='1'>";
  echo "<th>email_address</th>
        <th>customer_name</th>
        <th>password</th>
        <th>phone_number</th>";

  $row = mysqli_fetch_row($queryResult);
  
  while ($row) {
    echo "<tr><td>{$row[0]}</td>";
    echo "<td>{$row[1]}</td>";
    echo "<td>{$row[2]}</td>";
    echo "<td>{$row[3]}</td></tr>";
    $row = mysqli_fetch_row($queryResult);
  }
  echo "</table><br/><br/><br/>";

  // close the connection
  mysqli_close($DBConnect);

?>
<?php 
  $DBConnect = @mysqli_connect("feenix-mariadb.swin.edu.au", "s101664795","250394", "s101664795_db")
  Or die ("<p>Unable to connect to the database server.</p>". "<p>Error code ". mysqli_connect_errno().": ". mysqli_connect_error()). "</p>";

  // set up the SQL query string
  $SQLstring = "select b.booking_ref, c.customer_name, b.passenger_name, b.contact_num, b.unit_num, b.street_num, b.street_name, b.suburb, b.destination, 
  concat(b.pick_date, ' ', b.pick_time) as timestamp, b.status 
  FROM customer c, booking b
  where c.email_address = b.email_address
  order by b.booking_ref";
  $queryResult = @mysqli_query($DBConnect, $SQLstring)
  Or die ("<p>Unable to query the $TableName table.</p>"."<p>Error code ". mysqli_errno($DBConnect). ": ".mysqli_error($DBConnect)). "</p>";

  // print customer table on html page
  echo "<h3>booking table</h3>";
  echo "<p>ordered by ref no.</p>";
  echo "<table width='100%' border='1'>";
  echo "<th>ref</th>
        <th>customer_name</th>
        <th>passenger_name</th>
        <th>contact no.</th>
        <th>pick-up address</th>
        <th>desstination suburb</th>
        <th>pick-time</th>
        <th>status</th>";

  $row = mysqli_fetch_row($queryResult);
  
  while ($row) {
    // 15 Mar 18:07
    if ($row[4] == 0) $row[4] = "";
    else $row[4] = "$row[4]/";
    echo "<tr><td>{$row[0]}</td>";
    echo "<td>{$row[1]}</td>";
    echo "<td>{$row[2]}</td>";
    echo "<td>{$row[3]}</td>";
    echo "<td>{$row[4]}{$row[5]} {$row[6]}, {$row[7]}</td>";
    echo "<td>{$row[8]}</td>";
    echo "<td>{$row[9]}</td>";
    echo "<td>{$row[10]}</td></tr>";
    $row = mysqli_fetch_row($queryResult);
  }
  echo "</table><br/>";

  // close the connection
  mysqli_close($DBConnect);

?>
</body>
</html>