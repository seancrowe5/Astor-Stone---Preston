<?php
	echo '<?xml version="1.0" encoding="UTF-8" ?>';
    require 'config/db-connect.php';        //databse connect

    $user_phone = $_REQUEST['From'];
	$message = $_REQUEST['Body'];
    $twilioPhone = $_REQUEST['To'];

    $result = $con->query("SELECT user_id FROM user WHERE user_phone = $user_phone "); //get user id from phone
    while ($row = $result->fetch_assoc()) {$user_id = $row['user_id'];}

    $message = $con->real_escape_string($message);
    $query = "INSERT INTO messages(content, sender, recipient) VALUES ('$message','$user_id', $twilioPhone)";
    mysqli_query($con,$query);
    echo '<Response>';
    echo '</Response>';
?>