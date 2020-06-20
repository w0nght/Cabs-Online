# Cabs-Online
A we-based taxi booking system using PHP and MySQL

## System info
To use the function of booking a taxi in CabsOnline, user need to be a register customer of the system.
### For customers
New customer can register via the register page, existing customer can login via the login page.
After successful login to the system, users can then book a taxi by filling in a booking form. Any booking needs to be at least 40 minutes.
The system will genarate a unique booking reference number which will be showed on a confirmation message, as well as a confirmation email. 
When finished, users can log out of the system via a log out button on the top menu bar.
### For admins
Admins can access the system on the login page with the pre-set login credential (see below).
After successful login, admins can see a list of those unassigned booking requests ordered by timestamp, from the current time to within 3 hours period. 
Admins can also assign a taix for those booking requests, by simply entering the reference number and click the update button. 
When finished, admins can log out of the system via a log out button on the top menu bar.

PS: You need to create some bookings in order to test this system, login credential can be found on showCustomer.php if you forgot the password.


#### Admins login credential
email: admin
password: admin
login via login.php


#### List of all the files in the system
1. login.php <br>
To handle the authentication of users and admin. 
Admin password and email is listed above. With the correct "email" and password will redirect an admin to the admin page (admin.php).
Select LogOut button from admin/booking page will redirect users back to this page.
Select register button will redirect users to the register page.
2. register.php
register.php is to handle the registation of users.
Select login button will redirect users to the login page.
3. booking.php
booking.php is 
4. admin.php
admin.php is designed for administration purpose.
Need to login via login page, only able to access this page with a successful login credential
4. showCustomer.php
showCustomer.php is a debug page.
Retrieved table(s) from database for convenience.
5. commands.txt
commands.txt listed MySQL commands used to create tables.
6. checkList.txt
checkList.txt listed all the tasks.
