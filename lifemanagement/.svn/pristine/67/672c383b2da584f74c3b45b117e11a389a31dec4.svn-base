<?php session_start();



$host="localhost"; // Host name 
$username="root"; // Mysql username 
$password=""; // Mysql password 
$db_name="nutrition"; // Database name 
$tbl_name="members"; // Table name 

// Connect to server and select databse.
mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");


$account = $_SESSION['myusername'];

 
$verify="select * from members where username = '{$account}'";
  
 $result=mysql_query($verify);

// Mysql_num_row is counting table row

 $count=mysql_num_rows($result);



    if($count > 0)
    
      {
      
        header("location: ./Nutrition102.html");
        
      }
      
       else
       
      {
      
         header("location: ./NotLoggedIn.html");
         
         
      }
      
      


?> 
