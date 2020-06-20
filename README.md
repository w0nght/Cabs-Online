# Cabs-Online
A we-based taxi booking system using PHP and MySQL

## System info
To use the function of booking a taxi in CabsOnline, user need to be a register customer of the system.
### For customers
New customer can register via the register page, existing customer can login via the login page. <br>
After successful login to the system, users can then book a taxi by filling in a booking form. Any booking needs to be at least 40 minutes. <br>
The system will genarate a unique booking reference number which will be showed on a confirmation message, as well as a confirmation email.  <br>
When finished, users can log out of the system via a log out button on the top menu bar.
### For admins
Admins can access the system on the login page with the pre-set login credential (see below). <br>
After successful login, admins can see a list of those unassigned booking requests ordered by timestamp, from the current time to within 3 hours period.  <br>
Admins can also assign a taix for those booking requests, by simply entering the reference number and click the update button.  <br>
When finished, admins can log out of the system via a log out button on the top menu bar. <br>

PS: You need to create some bookings in order to test this system, login credential can be found on showCustomer.php if you forgot the password.


#### Admins login credential
email: admin
password: admin
login via login.php


#### List of all the files in the system
1. login.php <br>
To handle the authentication of users and admins.  <br>
With the correct admins credential, admins will be redirected to the admin page. <br>
Select LogOut button from admin/booking page will redirect users back to this page. <br>
Select register button will redirect users to the register page. <br>
2. register.php
To handle the registation of users. <br>
Select login button will redirect users to the login page. <br>
3. booking.php
To handle the taxi booking form. <br>
4. admin.php
Designed for administration purpose. <br>
To handle taxi booking requests that need to be assigned as soon as possible.  <br>
To assign taxi for a particular booking request.  <br>
4. showCustomer.php
For debug purpose only. <br>
Retrieved table(s) from database for convenience. <br>
5. commands.txt
List of MySQL commands used to create tables.
