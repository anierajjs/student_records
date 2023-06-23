<?php

	// connect to the database
	$conn = mysqli_connect('localhost', 'root', '', 'student_records');

	// check connection
	if(!$conn){
		echo 'Connection error: '. mysqli_connect_error();
	}

?>
