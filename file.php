<?php
  
if($_POST) {
    $fname = "";
	$surname = "";
    $email_add = "";
	$subject = "Application Form Submission";
    $email_body = "";
      
    if(isset($_POST['fname'])) {
        $fname = filter_var($_POST['fname'], FILTER_SANITIZE_STRING);
        $email_body .= "First Name: ".$fname;
    }
 
	if(isset($_POST['surname'])) {
        $surname = filter_var($_POST['surname'], FILTER_SANITIZE_STRING);
        $email_body .= $surname."\n";
    }
	
    if(isset($_POST['email_add'])) {
        $email_add= str_replace(array("\r", "\n", "%0a", "%0d"), '', $_POST['email_add']);
        $email_add = filter_var($email_add, FILTER_VALIDATE_EMAIL);
        $email_body .= "Email Address: ".$email_add."\n";
    }
 
	if(isset($_FILES['application_form'])) {
		$tmp_name    = $_FILES['application_form']['tmp_name']; // get the temporary file name of the file on the server
		$name        = $_FILES['application_form']['name'];  // get the name of the file
		$size        = $_FILES['application_form']['size'];  // get size of the file for size validation
		$type        = $_FILES['application_form']['type'];  // get type of the file
		$error       = $_FILES['application_form']['error']; // get the error (if any)
		
		$handle = fopen($tmp_name, "r");  // set the file handle only for reading the file
		$content = fread($handle, $size); // reading the file
		fclose($handle);                  // close upon completion

		$encoded_content = chunk_split(base64_encode($content));
		$boundary = md5("random"); // define boundary with a md5 hashed value
		
		$email_body .= "--$boundary\r\n";
		$email_body .="Content-Type: application/octet-stream; name=".basename($name)."\r\n";
		$email_body .="Content-Disposition: attachment; filename=".$name."; filesize=".$size."\r\n";
		$email_body .="Content-Transfer-Encoding: base64\r\n";
		$email_body .= $encoded_content; // Attaching the encoded file with email
	}
	
    $recipient = "test@test.com";

    
    $headers = 'From: ' . $fname ." ". $surname . "\r\n" . 'Reply-To: ' . $email_add . "\r\n" . 'X-Mailer: PHP/' . phpversion();
      
    if(mail($recipient, $subject, $email_body, $headers)) {
        echo "<p>Thank you for contacting us, $fname $surname. You will get a reply within 24 hours.</p>";
    } else {
        echo "<p>We are sorry ". $fname . " " . $surname .", but the email did not go through.</p>";
    }
      
} 
else {
    echo '<p>Something went wrong</p>';
}


?>