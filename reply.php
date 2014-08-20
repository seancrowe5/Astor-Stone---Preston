<?php

/// SEND INITIAL TEXT MESSAGE, INSERT INTO DATABASE, AND EXIT WITH SUCCESS
require "twilio/Services/Twilio.php"; //twilio library
require 'config/twilio-connect.php';    //twilio credentials...not sure if this will work
require 'config/db-connect.php';        //databse connect

if($_POST)
{
    
    $twilioNumber = '+16307967918';         //assign the twilio number that OTP will be sending from
    
    //check if its an ajax request, exit if not
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
	
		//exit script outputting json data
		$output = json_encode(
		array(
			'type'=>'error', 
			'text' => 'Request must come from Ajax'
		));
		
		die($output);
    } 
	
	//check $_POST vars are set, exit if any missing
	if(!isset($_POST["otpReply"]) || !isset($_POST["userPhone"]) )
	{
		$output = json_encode(array('type'=>'error', 'text' => 'Input field is empty!'));
		die($output);
	}
    
	//Sanitize input data using PHP filter_var().
	$otp_Reply        = filter_var($_POST["otpReply"], FILTER_SANITIZE_STRING);
    $user_Phone        = filter_var($_POST["userPhone"], FILTER_SANITIZE_STRING);
	

   
    $otp_Reply = str_replace('&#39;',"'",$otp_Reply);

    $sms = $client->account->messages->sendMessage($twilioNumber, $user_Phone, $otp_Reply);
 
    //insert message into database
    //get user id from phone....make this a method you stupidhead sean
    $result = $con->query("SELECT user_id FROM user WHERE user_phone = $user_Phone "); //get the selected users phone based on user id
    while ($rows = $result->fetch_assoc()) {$userID = $rows['user_id'];} //store in variable $userPhone 
    
    $otpReply = $con->real_escape_string($otp_Reply); //escape for special chars
    $query = "INSERT INTO messages(sender,recipient, content) VALUES ('$twilioNumber','$userID','$otpReply')"; //sender is twil number for now...
    mysqli_query($con,$query);
    

    if (!$sms) {
        $output = json_encode(array('type'=>'error', 'text' => 'Could not send text message!'));
		die($output);
    }
    
    else{
		$output = json_encode(array('type'=>'message', 'text' => "Message sent!"));
		die($output);
	}

}
?>

