<?php
/**
 * CoffeeCup Flash Form Builder: Database Management Example
 *
 * @author Jeff Welch <jw@coffeecup.com>
 * @version 1.0
 * @package CC_FB
 */

   // EDIT YOUR DATABASE FIELDS HERE
   //-------------------------------
   $database_host = '127.0.0.1';
   $database_port = '3306';
   $database_username = 'root';
   $database_password = 'biteme';
   $database_name = 'lifemanagement';
   
   // DO NOT EDIT ANYTHING BELOW THIS LINE
   //-------------------------------------
   // Error reporting should be disabled in favor of
   // our customer error messages.
   error_reporting(0);
      
   // Let's make sure they are running PHP version 4.3.0 or greater. 
   if(!version_compare(PHP_VERSION, '4.3.0', '>='))
   {
      printMessage('Invalid PHP Version',
         "We're sorry but this script requires PHP version 4.3.0 " .
            'or greater.  Please contact your server administrator.');
   }   
      
   // Let's make sure they actually have mysql loaded.
   if(!extension_loaded('mysql'))
   {
      printMessage('Unable to use MySQL',
         "We're sorry but you must have the MySQL extensions loaded " .
            'in your PHP configuration in order to use this script. ' .
            'Please contact your server administrator.');     
   }
   
   // Take care of magic quotes.
   if(get_magic_quotes_gpc()) 
   {
      $_POST = array_map('stripslashes', $_POST);
      $_GET = array_map('stripslashes', $_GET);
   }      
   
   session_start();
   
   // Lets make sure we can start the session.
   if(!isset($_GET['start_session']) && !isset($_SESSION['logged_in']))
   {
      $_SESSION['logged_in'] = false;
      die(header("Location: {$_SERVER['SCRIPT_NAME']}?start_session"));
   }
   elseif(isset($_GET['start_session']))
   {
      if(!isset($_SESSION['logged_in']))
      {
         $error_message = "\n        <p class=\"error\">You must have " . 
            "cookies enable to use this script.</p>\n";      
      }
      else
      {      
         die(header("Location: {$_SERVER['SCRIPT_NAME']}"));
      }
   }  
   
   // If $_SESSION['logged_in'] isn't set, allow the user to login
   if($_SESSION['logged_in'] !== true)
   {
      if(isset($_POST['login']))
      {
         if($_POST['username'] == $database_username &&
            $_POST['password'] == $database_password)
         {
            $_SESSION['logged_in'] = true;
            die(header("Location: {$_SERVER['REQUEST_URI']}"));
         }
         else
         {
            $error_message = "\n        <p class=\"error\">Invalid username/" . 
               "password combination</p>\n";
            $username = htmlentities($_POST['username'], ENT_QUOTES);
         }
      }
      
      printMessage('Please Login',
         <<<EOHTML
         
    <!-- Start Login Form -->         
    <form method="post" action="" id="loginform">
    
      <fieldset>  
        
        <legend>Login Credentials</legend>
        $error_message
        <label for="username"><span>Username:</span>
           <input type="text" name="username" id="username" 
              value="$username" size="28" tabindex="1" />
        </label>

        <label for="password"><span>Password:</span>         
           <input type="password" name="password" id="password" 
              size="28" tabindex="2" />
        </label>
        
      </fieldset>

      <div>
         <input name="login" id="login" tabindex="3" 
            value="Login" type="submit" />
      </div>

    </form>
    <!-- End Login Form -->

EOHTML
         , false);
   }   
   
   // Let's make sure we can connect to their database.
   if(!($link = mysql_connect("$database_host:$database_port",
      $database_username, $database_password)))
   {
      printMessage('Unable to Connect to Database Server.',
         "We're sorry but we were unable to connect to your database " .
            'server. Please be sure you have entered your database ' .
            'settings correctly.');         
   }
   // Let's make sure we can select their database.
   if(!mysql_select_db($database_name, $link)) 
   {
      printMessage('Unable to select Database.',
         "We're sorry but we were unable to select your database. " .
            'Please be sure that you have the proper permissions to ' .
            'select it.  If you are still experiencing trouble, ' .
            'please contact your server administrator.');    
   }
   // Download the file if the user requested it.
   if($_GET['action'] == 'download' && trim($_GET['file']) != '')
   {
      // Make sure we can select.
      if(!($results = mysql_query('SELECT `uploaded_file` FROM ' .
         ' `form_results` WHERE `uploaded_file_name` = "' .
            mysql_real_escape_string($_GET['file'], $link) . 
            '" LIMIT 1', $link)))
      {
         printMessage('Unable to Query Database.',
            "We're sorry but we were unable to query your database " .
               'table. Please be sure that you have the proper ' .
               'permissions to select from the form_results ' .
               'table. If you are still experiencing trouble, ' .
               'please contact your server administrator.');           
      }
      
      // If the file doesn't exist, let the user know.
      if(mysql_num_rows($results) == 0)
      {
         printMessage('Unknown File.',
            "We're sorry but the file you have requested does not exist.");         
      }
      else
      {
         $row = mysql_fetch_assoc($results);
         
         header("Content-length: " . strlen($row['uploaded_file']));
         header("Content-type: application/octet-stream");
         header("Content-Disposition: attachment; filename=" . $_GET['file']);   
         die($row['uploaded_file']);
      }
   }
   // Make sure we can show columns.
   if(!($results = mysql_query('SHOW COLUMNS FROM `form_results`', 
      $link)))
   {
      printMessage('Unable to Query Database.',
         "We're sorry but we were unable to query your database " .
            'table. Please be sure that you have the proper ' .
            'permissions to select from the form_results ' .
             'table. If you are still experiencing trouble, ' .
            'please contact your server administrator.');           
   }
   
   // Get the field names.
   while($row = mysql_fetch_assoc($results))
   {
      if($row['Field'] == 'uploaded_file')
      {
         $columns[] = 'filesize';      
         $sql .= 'length(`uploaded_file`) AS filesize,';
      }
	   elseif($row['Field'] != 'id')
	   {
         $columns[] = $row['Field'];
         $sql .= "`{$row['Field']}`,";
      }
   }

   // Get the form results.
   $results = mysql_query('SELECT ' . substr($sql, 0, -1) . 
      ' FROM `form_results`', $link);
      
   printMessage("Form Results", getTable($results, $columns), false);
   
   /**
    * Creates a results table.
    * 
    * @param resource $results the results resource
    * @param array $columns the database columns
    * @return string
    */   
   function getTable($results, $columns)
   {      
      $table = "<table>\n      <tr>";
      foreach($columns as $column)
      {
         $table .= "\n        <th>" . 
            htmlentities($column, ENT_QUOTES) . '</th>';
      }
      $table .= "\n      </tr>";
      
      while($row = mysql_fetch_assoc($results))
      {
         $table .= "\n      <tr class=\"" . (++$i % 2 != 0 ? 'odd' : 'even') . "\">";
         foreach($row as $key => $value)
         {
            $table .= "\n        <td>";
            if($value == '')
            {
               $table .= '&nbsp;';
            }
            else
            {
               if($key == 'uploaded_file_name')
               {
                  $table .= 
                     '<a href="?action=download&amp;file=' . 
                     htmlentities(urlencode($value), ENT_QUOTES) .
                     '">' . htmlentities($value, ENT_QUOTES) . '</a>';
               } 
               elseif($key == 'filesize')
               {
                  $table .= htmlentities(humanSize($value), ENT_QUOTES);               
               }
               else
               {
                  $table .= htmlentities($value, ENT_QUOTES);
               }
            }
            $table .= '</td>';
         }
         $table .= "\n      </tr>";
      }
      return $table . "\n    </table>";
   }
   
   /**
    * Gets the human-readable size for a size in bytes.
    * 
    * @param int $size the size in bytes
    * @return string
    */   
   function humanSize($size)
   {
      if(($human_size = $size / 1048576) > 1) 
      { 
         return round($human_size, 1) . ' MB'; 
      }
      elseif(($human_size = $size / 1024) > 1) 
      {
         return round($human_size, 1) . ' KB'; 
      }
      else 
      {
         return round($size, 1) . ' Bytes';
      }
   }

   /**
    * Prints a message to the screen.
    *
    * NOTE: This function stops execution of the script.
    * 
    * @param string $title the title of the page
    * @param string $message the message to print to the screen
    * @param boolean $html_encode whether or not to encode the message
    */
   function printMessage($title = null, $message = null, $html_encode = true)
   {
      // Html-encode if necessary
      if($html_encode)
      {
         $message = '<p>' . htmlentities($message, ENT_QUOTES) . '</p>';
      }   
   
      // If the user has provided a title, format it for HTML
      if($title !== null)
      {
         $title = htmlentities($title, ENT_QUOTES);      
         $page_title = "$title - ";      
         $title = "<h1>$title</h1>";
      }
           
      die( <<<EOHTML
<?xml version="1.0" encoding="utf-8"?>      
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">      
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>{$page_title}CoffeeCup Form Builder Manager</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="robots" content="noindex,nofollow" />
  <style type="text/css">
   <!--
    table 
    {
       border-collapse: collapse;  
    }
    
    th
    {
       background-color: #d3dce3;    
    }
    
    th, td
    {
       padding:    .5em;
       border:     1px solid #fff;    
       text-align: left;
    } 
    
    tr.odd
    {
       background-color: #e5e5e5;
    }
    
    tr.even
    {
       background-color: #d5d5d5;
    }
    
    tr:hover
    {
       background-color: #cfc;
    }
    
    #loginform
    {
       padding-top: 10px;
       margin-top:  -10px;
       border-top:  1px #ccc solid;
    }
    
    #loginform p.error
    {
       padding:          1em;
       margin-top:       0;       
       color:            #600;
       border:           1px solid #600;
       background-color: #fee;
    }
    
    #loginform fieldset
    {
       border: 0;
    }
    
    #loginform fieldset label span
    {
       display: block;
    }
    
    #loginform fieldset input
    {
       display: block;
       margin:  .5em 0 1em;
    }
    
    #loginform fieldset legend
    {
       display: none;
    }
   -->
  </style>  
</head>

<body>
  <div id="wrapper">
    $title
    $message
  </div>
</body>

</html>      
EOHTML
      );
   }
?>