<?php session_start();
/**
 
 * @license http://www.coffeecup.com/legal/eula.html  
 * @author Jeff Welch <jw@coffeecup.com>
 * @version 4.0
 * @package CC_FB
 */
  
   // Error reporting should be disabled in favor of
   // our customer error messages.
   error_reporting(0);
   
   /**
    * The version of CoffeeCup Flash Form Builder that
    * generated this script.
    */
   define('CC_FB_VERSION', '7.1');
   /**
    * The release date of the version of CoffeeCup Flash Form
    * Builder that generated this script.
    */
   define('CC_FB_LAST_UPDATED', '08/31/2007');
   
   /**
    * The version of this script.
    */
   define('CC_FB_SCRIPT_VERSION', '4.0');
   /**
    * The release date of this script.
    */
   define('CC_FB_SCRIPT_LAST_UPDATED', '04/20/2008');
   
   /**
    * The required PHP version for this script.
    */
   define('CC_FB_PHP_VERSION', '4.3.0');
   
   /**
    * Will the owner of this form be emailed the 
    * form data
    */
   define('CC_FB_DO_EMAIL',false);       
   /**
    * To default To address.
    */   
   define('CC_FB_TO_EMAIL', 'keladar@hotmail.com');
   /**
    * The default CC address.
    */   
   define('CC_FB_CC_EMAIL', ''); 
   /**
    * The default BCC address.
    */   
   define('CC_FB_BCC_EMAIL', '');
   
   /**
    * If we should send a message back to the user.
    */     
   define('CC_FB_AUTO_REPLY', false);
   /**
    * The subject of the message to be sent to the user.
    */  
   define('CC_FB_AUTO_REPLY_SUBJECT', '');   
   /**
    * If we should include the form results 
    * in the message we send to the user.
    */  
   define('CC_FB_AUTO_REPLY_FORM_RESULTS', false);
   /**
    * The position of the auto-reply message
    * in the email.
    */  
   define('CC_FB_AUTO_REPLY_POSITION', 'bottom');
   
   /**
    * The page to redirect to after the form is submitted.
    */  
   define('CC_FB_RESULTS_REDIRECT', 'http://127.0.0.1/TaskManagement.html');
   
   /**
    * The address of the database where the form results 
    * will be saved.
    */
   define('CC_FB_DB_ADDRESS', '127.0.0.1');
   /**
    * The port number of the database where the form results 
    * will be saved.
    */
   define('CC_FB_DB_PORT', '3306');     
   /**
    * The username for the database where the form results 
    *  will be saved.
    */
   define('CC_FB_DB_USERNAME', 'root');
   /**
    * The password for the database where the form results
    * will be saved.
    */
   define('CC_FB_DB_PASSWORD', 'biteme');
   /**
    * The name of the database where the form results
    * will be saved.
    */
   define('CC_FB_DB_NAME', 'lifemanagement');

   /**
    * The file to log the form results to if necessary.
    */   
   define('CC_FB_SAVE_FILE', '[FILENAME]');
   
   /**
    * The filetypes that are acceptable for file uploads.
    */
   define('CC_FB_ACCEPTABLE_FILE_TYPES', 'txt|gif|jpg|jpeg|zip|doc|png|pdf|rtf');
   /**
    * The directory where files are uploaded
    */
   define('CC_FB_UPLOADS_DIRECTORY', 'files');
   /**
    * The extension that gets added to file uploads
    */
   define('CC_FB_UPLOADS_EXTENSION', '_fbu');   
   /**
    * Will we save the file uploads to the server
    */   
	define('CC_FB_ATTACHMENT_SAVETOSERVER',false); 
   /**
    * Will we save the file uploads to the db
    */   
   define('CC_FB_ATTACHMENT_SAVETODB',false);
   /**
    * Will we send the file upload as an attachment
    */   
   define('CC_FB_ATTACHMENT_ADDTOEMAIL',false);
   /**
    * Sendmail Message EOL's
    */   
   define('CC_FB_SENDMAIL_EOL',"\r\n");
   
   
   
   checkuser();
   
   

   // Makes sure that the user is using the required version
   // of PHP as specified by {@link CC_FB_PHP_VERSION}.
   if(!version_compare(PHP_VERSION, CC_FB_PHP_VERSION, '>='))
   {
      printMessage('Invalid PHP Version',
         "We're sorry but CoffeeCup Form Builder requires PHP version " .
            CC_FB_PHP_VERSION . ' or greater.  Please contact your server ' .
            'administrator.');
   }
   // Strip slashes if the server has magic quotes enabled.
   if(get_magic_quotes_gpc()) 
   {
      $_POST = array_map("stripslashes", $_POST);
   }
   // John will need to fix this in the swf file.
   foreach($_POST as $key => $value)
   {
      $_POST[str_replace('_', ' ', $key)] = $value;
   }   
   
   // If the '$_FILES['Filedata']' is populated, process the
   // file upload.
   if(isset($_FILES['Filedata']))
   {
      processFileUpload();
   }
   // If the '$_POST' superglobal array is populated,
   // process the form results.
   elseif(is_array($_POST) && count($_POST) > 0)
   {
      processMailForm();
   }
   // If all else fails, print out a blank page with version
   // numbers and release dates.
   printMessage();


   /**
    * Process the mail form results.
    *
    * This method is in charge of processing the mail form which
    * is posted from the CoffeeCup Flash Form Builder SWF.  This
    * process includes:
    * 
    * - Retrieving the preferences from the included CoffeeCup Flash
    *   Form Builder XML preferences file.
    * - Formats output for file output as well as for an email to
    *   the form user and the form owner as necesarry.
    * - Writes output to a file and sends it to the form user and
    *   the form owner as necessary.
    * - Writes form results to a database if necesarry.
    */
   function processMailForm()
   {
      fixUploadedFileName();
      $preferences = getPreferences();

      foreach($preferences['form_fields'] as $key => $value)
      {
         if(trim($_POST[$key]) != '')
         {
            $email_response .= "$key: {$_POST[$key]}" .
            CC_FB_SENDMAIL_EOL . CC_FB_SENDMAIL_EOL;
            $form_response .= "$key: {$_POST[$key]}<br/>\n";
            $txt_file .= "$key: {$_POST[$key]}|";
         }
      }
      
      // If a file was uploaded, add the appropriate data to the response
      // fields
      if($_POST['Uploaded_File'] != "")
      {
         $email_response .= "Uploaded File: {$_POST['Uploaded_File']}" .
            CC_FB_SENDMAIL_EOL . CC_FB_SENDMAIL_EOL;
         $form_response .= "    Uploaded File: {$_POST['Uploaded_File']}" . 
            "<br/>\n";
         $txt_file .= "Uploaded File: {$_POST['Uploaded_File']}|";           
      }
      
     // sendResponseEmails($email_response, $preferences);
     // writeResponseToFile($txt_file);
      writeResponseToDatabase($preferences);
      
      // Make sure we delete the file from the server if the user doesn't
      // want it
      if(!CC_FB_ATTACHMENT_SAVETOSERVER && $_POST['Uploaded_File'] != '')
      {
         @unlink(CC_FB_UPLOADS_DIRECTORY . "/{$_POST['Uploaded_File']}");
      }      
      
      printResponsePage($form_response, $preferences);
   }


   /**
    * Send response emails to the appropriate recipients.
    *
    * Sends an email to the scripts owner as well as the end-user
    * if appropriate.  If the sending of mail fails, an error
    * message will be printed out to the screen.
    * 
    * @param string $email_response the default contents to mail to the user.
    * @param array $preferences the CoffeeCup Flash Form Builder Preferences.
    */      
   function sendResponseEmails($email_response, $preferences)
   {      
      // If the program is unregistered, add the unregistered message.
      if($_POST['unreg'])
      {
         $unreg = "------------------------" . 
            CC_FB_SENDMAIL_EOL . CC_FB_SENDMAIL_EOL . 
            "This Form was sent to you using CoffeeCup Form Builder." . 
            CC_FB_SENDMAIL_EOL . "Please tell a " .
               "friend about us: http://www.coffeecup.com/form-builder/"; 
      }    
      
      // Set up the CC field if necessary
      if(CC_FB_CC_EMAIL != '')
      {
         $cc = 'Cc: ' . CC_FB_CC_EMAIL . CC_FB_SENDMAIL_EOL;
      }
      
      // Set up the BCC field if necessary 
      if(CC_FB_BCC_EMAIL != '')
      {
         $bcc = 'Bcc: ' . CC_FB_BCC_EMAIL . CC_FB_SENDMAIL_EOL;
      }
      
      // Use the alternative email if one is provided
      $mail_to = ($_POST['_ALT_EMAIL'] != '' ? $_POST['_ALT_EMAIL'] : 
         CC_FB_TO_EMAIL);
         
      // Set a default subject if one is not provided provided
      $subject = ($_POST['subject'] != '' ? $_POST['subject'] : 
         'Form Submission');   
                  
      // Set up the default mail headers   
      $headers = 'MIME-Version: 1.0' . CC_FB_SENDMAIL_EOL .
         'Content-Type: text/plain; charset=utf-8' . CC_FB_SENDMAIL_EOL .
         'Content-Transfer-Encoding: 7bit' . CC_FB_SENDMAIL_EOL;             
      
      // Set up the default owner message
      $form_owner_msg =  
         'Here is the information submitted to ' . 
         "{$_SERVER['SERVER_NAME']}{$_SERVER['PHP_SELF']} from " .
         "{$_SERVER['REMOTE_ADDR']} on " . date("l, F dS, Y \a\\t g:i a") . 
         "." . CC_FB_SENDMAIL_EOL . "------------------------" . 
         CC_FB_SENDMAIL_EOL . "$email_response$unreg" . 
         CC_FB_SENDMAIL_EOL. CC_FB_SENDMAIL_EOL;  
            
      // Add the uploaded file as an attachment if the user has
      // request we do so
      if(CC_FB_ATTACHMENT_ADDTOEMAIL && $_POST['Uploaded_File'] != '')
      {
         if(!($contents = 
            file_get_contents(CC_FB_UPLOADS_DIRECTORY . 
               "/{$_POST['Uploaded_File']}")))
         {
            printMessage('Unable To Open Attachment File',"We're sorry but "  .
               'we were unable to open your uploaded file to attatch it for ' .
               'email. Please be sure that you have the proper permissions.');
         }
         
         $attachment = chunk_split(base64_encode($contents));
    
         // Setup the unique mime boundary
         $mime_boundary = md5(time());                 
    
         // Set up the form owner mail headers   
         $form_owner_headers = 'MIME-Version: 1.0' . CC_FB_SENDMAIL_EOL .
            'Content-Type: multipart/mixed; ' .
            "boundary=\"$mime_boundary\"" .
             CC_FB_SENDMAIL_EOL;                        
         
         // Set up the new form owner message
         $form_owner_msg = 
            CC_FB_SENDMAIL_EOL .
            "--$mime_boundary" . CC_FB_SENDMAIL_EOL .
            'Content-Type: text/plain; charset=utf-8' . CC_FB_SENDMAIL_EOL .
            'Content-Transfer-Encoding: 7bit' .
            CC_FB_SENDMAIL_EOL. CC_FB_SENDMAIL_EOL .  
            $form_owner_msg .
            "--$mime_boundary" . CC_FB_SENDMAIL_EOL .          
            'Content-Type: application/octet-stream ' .
            "name=\"{$_POST['Uploaded_File']}\"" . CC_FB_SENDMAIL_EOL . 
            "Content-Transfer-Encoding: base64" . CC_FB_SENDMAIL_EOL . 
            "Content-Description: {$_POST['Uploaded_File']}" . 
            CC_FB_SENDMAIL_EOL .  
            "Content-Disposition: attachment; " .
            "filename=\"{$_POST['Uploaded_File']}\"" . 
            CC_FB_SENDMAIL_EOL . CC_FB_SENDMAIL_EOL  .
            "$attachment" . CC_FB_SENDMAIL_EOL. CC_FB_SENDMAIL_EOL;             
            "--$mime_boundary--" .
            CC_FB_SENDMAIL_EOL . CC_FB_SENDMAIL_EOL;                  
      }
      else
      {
         $form_owner_headers = $headers;
      }
   
      // If we collected the end-user's email
      if($_POST['eM'])
      {      
         // Send a message to the form's owner with the end-user's email
         // as the reply-to address.
         if(CC_FB_DO_EMAIL && !(mail($mail_to,$subject,
            $form_owner_msg,
            "Reply-To: {$_POST['eM']}" . CC_FB_SENDMAIL_EOL .
            "Return-Path: {$_POST['eM']}" . CC_FB_SENDMAIL_EOL .           
            "From: {$_POST['eM']}" . CC_FB_SENDMAIL_EOL .
            "$cc$bcc" .
            'Message-ID: <' . time() . "-{$_POST['eM']}>" . CC_FB_SENDMAIL_EOL .
            'X-Mailer: PHP v' . phpversion() . CC_FB_SENDMAIL_EOL .                  
            $form_owner_headers)))
         {
            printMessage('Unable To Send E-Mail',
               "We're sorry but we were unable to send your e-mail. " .
                  'If you are sure that you entered all your email ' .
                  'addresses properly, you should contact your server ' .
                  'administrator.');         
         }
         
         // If necesarry, send a message to the end-user as well.
         if(CC_FB_AUTO_REPLY)
         {
            $auto_reply_msg = '';
         
            if(CC_FB_AUTO_REPLY_FORM_RESULTS)
            {
               $form_user_msg = $email_response;
               
               if(CC_FB_AUTO_REPLY_POSITION == 'top')
               {
                  $form_user_msg = $auto_reply_msg . 
                     CC_FB_SENDMAIL_EOL . CC_FB_SENDMAIL_EOL .
                     $form_user_msg;
               }
               else
               {
                  $form_user_msg .= $auto_reply_msg . 
                     CC_FB_SENDMAIL_EOL . CC_FB_SENDMAIL_EOL;
               }                
            }
            else
            {
               $form_user_msg = $auto_reply_msg;
            }

            mail($_POST['eM'],CC_FB_AUTO_REPLY_SUBJECT,
               "$form_user_msg$unreg",
               "Reply-To: $mail_to" . CC_FB_SENDMAIL_EOL .
               "Return-Path: $mail_to" . CC_FB_SENDMAIL_EOL .                         
               "From: $mail_to" . CC_FB_SENDMAIL_EOL .
               'Message-ID: <' . time() . "-$mail_to>" . 
               CC_FB_SENDMAIL_EOL .
               'X-Mailer: PHP v' . phpversion() . CC_FB_SENDMAIL_EOL .                     
               $headers);         
         }
      }
      // Send a message to the form's owner.
      elseif(CC_FB_DO_EMAIL && !(mail($mail_to,$subject,
         $form_owner_msg,
            'From: CoffeeCup Flash Form Builder ' .
            "<formbuilder@{$_SERVER['SERVER_NAME']}>" . CC_FB_SENDMAIL_EOL .
            "$cc$bcc" .
            'Message-ID: <' . time() . 
            "-formbuilder@{$_SERVER['SERVER_NAME']}>" . CC_FB_SENDMAIL_EOL .
            'X-Mailer: PHP v' . phpversion() . CC_FB_SENDMAIL_EOL .                
            $form_owner_headers)))
	   {
         printMessage('Unable To Send E-Mail',
            "We're sorry but we were unable to send your e-mail. " .
               'If you are sure that you entered all your email ' .
               'addresses properly, you should contact your server ' .
               'administrator.');      	       
	   }   
   }
   

   /**
    * Gets the real name of the file that was uploaded.
    *
    * Since the file upload occurs in a different request,
    * this method helps us resolve what the name of the 
    * uploaded file was in case it was renamed.
    */ 
   function fixUploadedFileName()
   {
      if($_POST['Uploaded_File'] != '')
      {
         $extension = substr($_POST['Uploaded_File'], 
            strrpos($_POST['Uploaded_File'], '.'));
         $basename = basename($_POST['Uploaded_File'], $extension);
         
         while(file_exists(CC_FB_UPLOADS_DIRECTORY . "/$basename". 
            CC_FB_UPLOADS_EXTENSION . "$i$extension"))
         {
            $new_upload_name = "$basename". CC_FB_UPLOADS_EXTENSION . 
            "$i$extension";
            $i++;            
         }
         
      }
      $_POST['Uploaded_File'] = $new_upload_name;
   }
   
   

   
   function checkuser()
    {
    	
     mysql_connect(CC_FB_DB_ADDRESS . ':' . CC_FB_DB_PORT,
     CC_FB_DB_USERNAME, CC_FB_DB_PASSWORD)or die("cannot connect");
    	
     mysql_select_db(CC_FB_DB_NAME)or die("cannot select DB");
    	
    $account = $_SESSION['myusername'];
 
	$verify="select * from members where username = '{$account}'";
  
 	$result=mysql_query($verify);

	// Mysql_num_row is counting table row

 	$count=mysql_num_rows($result);

    if(!($count > 0)) 
      {
      
  		
  printhtmlMessage('Please Login',
     <<<EOHTML
         
    <!-- Start Login Form -->  
    
    
			 <form action="../checklogin.php" class="login" method="post"><b>User
				Name</b>&nbsp;<input name="myusername" ><br>
				<br>
				<b>Password</b>&nbsp;&nbsp;&nbsp;<input name="mypassword" type="password" ><br>
				<br>
				<button name="send" type="submit">Login</button>
			 </form>
		
    
    <!-- End Login Form -->

EOHTML
         , false);
      	
      		
			
    //    header("location: ../Index.html");
      
      }	
    
    
    }
   
   
   
   /**
    * Write form response to a database.
    *
    * Writes the form response to the database specified at 'CC_FB_DB_ADDRESS'
    * if appropriate.  If the database doesn't it exist, the form_results
    * table doesn't exist or if the form_results table doesn't comply with
    * the structure of the current form then the database will be restructured
    * accordingly.
    * 
    * @param array $preferences the CoffeeCup Flash Form Builder Preferences.
    */       
   function writeResponseToDatabase($preferences)
   {
      // If the CC_FB_DB_ADDRESS constant has been populated, then
      // the user wants to write their data to a database.
      if(CC_FB_DB_ADDRESS != '[ADDRESS]')   
      {
         // First and foremost, lets make sure they have the mysql extension
         // loaded.
         if(!extension_loaded('mysql')) 
         {
            printMessage('Unable to use MySQL',
               "We're sorry but you must have the MySQL extensions loaded " .
                  'in your PHP configuration in order to save your form '.
                  'results to a MySQL database. Please contact your ' .
                  'server administrator.');  	       
         }
         // Secondly, lets make sure we can connect to their database.
         elseif(!($link = 
            mysql_connect(CC_FB_DB_ADDRESS . ':' . CC_FB_DB_PORT, 
               CC_FB_DB_USERNAME, CC_FB_DB_PASSWORD)))
         {
            printMessage('Unable to Connect to Database Server.',
               "We're sorry but we were unable to connect to your database " .
                  'server. Please be sure you have entered your database ' .
                  'settings correctly.');         
         }
        
                 
         // If all went well, lets attempt to write the form results to
         // the database.
         foreach($preferences['form_fields'] as $field_name => $field)
         {
            $query .= "`$field_name` = " . 
               mysqlEscape($_POST[$field_name], $link) . ',';
         }
         
         
       		 // Add member id to query
	    		// Set User = Session User
		// Code Block   if not logged in, redirect to login

		$user = $_SESSION['myusername'];
         $query .= '`memberid` = ' .
                mysqlEscape($user, $link). ',';
                  
         
         // Add the uploaded file to the query if necessary
         
         if(!mysql_query('INSERT INTO `tasks` SET ' . 
            $query . "`created_at` = NOW()", $link))
         {
            printMessage('Unable to Insert Into Database Table.', 
               "We're sorry but we were unable to insert the form results " . 
                  'into your database table. Please be sure that you have ' .
                  'the proper permissions to insert data into the ' .
                  'tasks table. If you are still experiencing ' .
                  'trouble, please contact your server administrator.');                
         }
      }
   }


   /**
    * Archives an old `tasks` table.
    *
    * Renames a form results table to tasks_old or 
    * tasks_old with a numerical value on the end of it 
    * if appropriate.
    * 
    * @param resource $link a database resource  
    */     
   


   /**
    * Escapes a value for MySQL.
    *
    * Prepares a value to be used safely in a MySQL query.  If the value is 
    * numeric, it is returned.  If the value is a string, it is quoted and
    * escaped using the mysql_real_escape_string function.
    * 
    * @param mixed $value the value to be escaped
    * @param resource $link a database resource  
    * @return mixed $value the escaped value   
    */     
   function mysqlEscape($value, $link)
   {
      return ("'" . mysql_real_escape_string($value, $link) . "'");
   }


   /**
    * Checks if the columns from a table match the the structure
    * of the fields from a form.
    * 
    * @param array $form_fields the structure from the form
    * @param array $table_fields the structure from the table
    * @return boolean $value, true if the structures are the same,
    * false if the structures are not.
    */      
  
   /**
    * Create a MySQL table from the form structure.
    *
    * Uses the structure of the form, pulled from the XML preferences
    * file to create a database table to store the form results.
    * 
    * @param resource $form_fields the structure of the form    
    * @param resource $link a database resource  
    */      
  
   
   
   /**
    * Write form response to a log file.
    *
    * Writes the form response to the log file specified at 'CC_FB_SAVE_FILE'
    * if appropriate.  If the file writing fails, an error message will be 
    * printed out to the screen.
    * 
    * @param string $txt_file the response to write to the log file.
    */      
   function writeResponseToFile($txt_file)
   {
      $txt_file = "{$_SERVER['SERVER_NAME']}{$_SERVER['PHP_SELF']}|" . 
         date("Y-m-d H:i:s") . "|{$_SERVER['REMOTE_ADDR']}|$txt_file\n";

      // If a log file location has been set
      if(CC_FB_SAVE_FILE != '[FILENAME]')
      {
         if($handle = fopen(CC_FB_SAVE_FILE, 'a'))
         {
            if(fwrite($handle, $txt_file) === false)
            {
               printMessage('Unable To Write To File',
                  "We're sorry but we were unable to write to ".CC_FB_SAVE_FILE.
                     '. Please contact your server administrator to be sure ' . 
                     'that you have the proper permissions.');            
            }
            fclose($handle);
         }
         else
         {
            printMessage('Unable To Open File',
               "We're sorry but we were unable to open " . CC_FB_SAVE_FILE .
                  '. Please contact your server administrator to be sure ' . 
                  'that you have the proper permissions.');
         }
      }   
   }


   /**
    * Prints the HTML-formatted, form response page for the end-user.
    *
    * Writes the form response to an HTML-formatted page for the end-user
    * or redirects the user to a thank you page if specified.
    * 
    * @param string $form_response the response to write to the page.
    * @param array $preferences the CoffeeCup Flash Form Builder Preferences.
    */    
   function printResponsePage($form_response, $preferences)
   {
      // Redirect to a thank you page if the user has created one.
      if(CC_FB_RESULTS_REDIRECT != '[RESULTSREDIRECT]')
      {
         die(header('Location: ' . CC_FB_RESULTS_REDIRECT));
      }
      // Otherwise create a thank you page.
      else
      {      
         $results_msg = '[RESULTSMSG]';
         die(str_replace('$form_results', $form_response, 
            $results_msg));
      }    
   }        


   /**
    * Returns the CoffeeCup Flash Form Builder Preferences.
    *
    * Opens the CoffeeeCup Flash Form Builder XML preferences file
    * and retrieves the preferences and form fields from it.  If
    * the preferences file is not found or can not be opened, an
    * error message is printed to the screen.
    * 
    * @return array $preferences an array of preferences specified
    * in the CoffeeCup Flash Form Builder XML preferences file.
    */
   function getPreferences()
   {
      if(!($contents = file_get_contents($_POST['xmlfile'])))
      {
         printMessage('Unable To Open XML File',"We're sorry but we were "  .
            'unable to locate your XML file.  Please be sure that the \'' .
               "{$_POST['xmlfile']}' is on your server in the same directory " .
               'as your other form builder files.');
      }
      
      // Strips out all the XML nodes from the preferences file.
      preg_match_all('/<([a-z]+?)\s+(.*?)>/is', $contents, $nodes);
      
      foreach($nodes[1] as $node_key => $node_value)
      {
         // Skip over item, hidden, button and label nodes, as we're not 
         // interested in them.
         if($node_value != 'item' && $node_value != 'hidden' && 
            $node_value != 'submitbutton' && $node_value != 'browsebutton' &&
            $node_value != 'label' && $node_value != 'resetbutton')
         {
            $node_array = array();
         
            // For each node, we will strip out all of the attributes
            preg_match_all('/([a-z0-9]+?)="(.*?)"/is', 
               $nodes[2][$node_key], $attributes);
            foreach($attributes[2] as $attribute_key => $attribute_value)
            {
               $node_array[$attributes[1][$attribute_key]] = 
                  html_entity_decode($attribute_value);
            }
         
            // If the node has an attribute called 'name', it is a form field.
            if(isset($node_array['name']))
            {    
               $name = $node_array['name'] . ($node_array['label'] != '' ?
                  " - {$node_array['label']}" : '');
               $preferences['form_fields'][$name] = $node_array;
               $preferences['form_fields'][$name]['type'] = $node_value;
            }
            // If the node type is 'form', it is the form preferences
            elseif($node_value == 'form')
            {
               $preferences['form_preferences'] = $node_array;
            }
            // otherwise just dump everything into a general array depending
            // on its node type.
            else
            {
               $preferences[$node_value][] = $node_array;            
            }
         } 
      }
      
      return $preferences;      
   }


   /**
    * Uploads a user-submitted file.
    *
    * Attempts to upload a user-submitted file specified in 
    * '$_FILES['Filedata']' to the 'CC_FB_UPLOADS_DIRECTORY' directory.  If the
    * file already exists, append a numeric value to the end of
    * the file name.
    */
   function processFileUpload()
   {
	   if(!ini_get('file_uploads'))
	   {
         printMessage('File Uploads Disabled',
            "We're sorry but we were unable to upload your file because " .
               'your do not have file uploads enabled.  Please contact' .
               'your server administrator.');		
	   }
	
      // Make sure we have a directory to store the file uploads
      if(!is_dir(CC_FB_UPLOADS_DIRECTORY) && 
         !mkdir(CC_FB_UPLOADS_DIRECTORY,0755))
      {
         printMessage('Directory Creation Failed',
            "We're sorry but we were unable to create a directory for " .
               'your file uploads.  Please contact your server administrator.');       
      }	
      // Make sure the file upload is of an acceptable file type
      if(CC_FB_ACCEPTABLE_FILE_TYPES != "" &&
         !preg_match('/\.('.CC_FB_ACCEPTABLE_FILE_TYPES.')$/is', 
         $_FILES['Filedata']['name']))
      {
         printMessage('Invalid File Type',
            "We're sorry but we were unable to upload your file because " .
               'the file type is not acceptable.');          
      }
      
      // Seperate the file's basename and extension so that
      // we can append numeric values on the end of the basename
      // if the file already exists.
      $extension = substr($_FILES['Filedata']['name'], 
         strrpos($_FILES['Filedata']['name'], '.'));
      $basename = basename($_FILES['Filedata']['name'], $extension);
      
      // Append number values on the end of the file name
      // if the file already exists
      while(file_exists(CC_FB_UPLOADS_DIRECTORY . "/$basename" . 
         CC_FB_UPLOADS_EXTENSION . "$i$extension"))
      {
         $i++;
      }
      
      if(!move_uploaded_file($_FILES['Filedata']['tmp_name'],
         CC_FB_UPLOADS_DIRECTORY . "/$basename". CC_FB_UPLOADS_EXTENSION . 
         "$i$extension"))
      {
         printMessage('File Upload Failed',
            "We're sorry but we were unable to upload your file.  Please " .
               'contact your server administrator.');       
      }
      chmod(CC_FB_UPLOADS_DIRECTORY . "/$basename$i$extension", 0777);
   }

   
    function printhtmlMessage($title = null, $message = null, $html_encode = true)
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
  <title>{$page_title}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="robots" content="noindex,nofollow" />
  <LINK REL=StyleSheet HREF="../my.css" TYPE="text/css" MEDIA=screen>
  
</head>


<body>
 <div id="wrapper"
 <div id="content"
 $title
 <div id="login">
    $message
  </div>
  </div>
  </div>
</body>

</html>      
EOHTML
      );
   }
   
   

   /**
    * Prints a message to the screen.
    *
    * Prints an HTML-formatted message to the screen that also contains
    * the current PHP version number the server is running, the current
    * version number and release date of this script as well as the 
    * current version number and release date of the version of CoffeeCup 
    * Flash Form Builder that generated this script.
    *
    * NOTE: This function stops execution of the script.
    * 
    * @param string $title the title of the page
    * @param string $message the message to print to the screen
    */
   function printMessage($title = null, $message = null)
   {
      // If the user has provided a title, format it for HTML
      if($title !== null)
      {
         $title = htmlentities($title, ENT_QUOTES);
         $page_title = "$title - ";      
         $title = "<h1>$title</h1>";
      }
      
      // If the user has provided a message, formit it for HTML
      if($message !== null)
      {
         $message = '<p>' . htmlentities($message, ENT_QUOTES) . '</p>';
      }
      
      die( <<<EOHTML
<?xml version="1.0" encoding="utf-8"?>      
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">      
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>{$page_title}CoffeeCup Form Builder</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="robots" content="noindex,nofollow" />
  <style type="text/css">
   <!--
    div#script_info
    {
       border-top: 1px solid #666;
       font-size:  .85em;
    }
   -->
  </style>
</head>

<body>
  $title
  $message
  <div id="script_info">
    <p>
      PHP Version: 
EOHTML
      . PHP_VERSION . '
    </p>
    <p>
     Sendmail Path: ' . ini_get('sendmail_path') . '<br />
     Sendmail From: ' . ini_get('sendmail_from') . '<br />
     SMTP: ' . ini_get('SMTP') . '<br />
     SMTP Port: ' . ini_get('smtp_port') . '
    </p>
    <p>
     MySQL: ' . (extension_loaded('mysql') ? 'Installed' : 'Not Installed') . '
    </p>
    <p>
      File Uploads: ' . (ini_get('file_uploads') ? 'On' : 'Off') . '<br />
      File Uploads Max Size: ' . ini_get('upload_max_filesize') . '<br />
      Post Max Size: ' . ini_get('post_max_size') . '</p>
    <p>
      Software Version: ' . CC_FB_VERSION . '<br />
      Software Last Updated: ' . CC_FB_LAST_UPDATED . '
    </p>
    <p>
      Script Version: ' . CC_FB_SCRIPT_VERSION . '<br />
      Script Last Updated: ' . CC_FB_SCRIPT_LAST_UPDATED  . '
    </p>' .
      <<<EOHTML

  </div>
</body>

</html>      
EOHTML
      );
   }