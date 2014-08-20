<?php

if($_POST) {
    
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
	if(!isset($_POST["userID"]) )
	{
		$output = json_encode(array('type'=>'error', 'text' => 'Input fields are empty!'));
		die($output);
	}

	$id = $_POST["userID"]; // We get the darn user ID here

    require 'config/db-connect.php';        //databse connect

    //$query = "SELECT user_phone FROM user WHERE user_id = $id "; //get the selected users phone based on user id
    //while ($row = $query->fetch_assoc()) {$userPhone = $row['user_phone'];} //store in variable $userPhone

    ///////////////////////
    ///DISPLAY MESSAGES//// 
    ///////////////////////
    if($result = $con->query("  SELECT user.user_fname, messages.content, DATE_FORMAT(messages.timestamp,'%b/%d/%y at %h:%i%p') as timestamp 
                                FROM messages 
                                INNER JOIN user
                                ON user.user_id = messages.sender
                                WHERE sender = '$id' OR recipient = '$id' 
                                ORDER BY timestamp ASC")){
        if($result->num_rows){                          //if the query has a result, then dislpay data
            while($rows = $result->fetch_assoc()){      //loop through result and display mesages
                $userName = $rows['user_fname'];
                $content = $rows['content'];
                $timestamp = $rows['timestamp'];
                echo $timestamp.':     '.$userName.': <strong>'.$content, '</strong></br></br>';
            }  
        }
    }
    
}
?>