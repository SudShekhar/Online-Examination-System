<?php
include("include/exam.php");
global $database;
global $session;
global $exam;
$totalquestions = 2;
function debug_to_console( $data ) {
    if ( is_array( $data ) )
        {
        	$output = "aray";
        	//$output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
    	}
    else
        $output = "<script>console.log( 'single " . $data . "' );</script>";
    echo $output;
}

$_SESSION['dne'] += $_SESSION['for'];
for ($i=0; $i < count($_SESSION['currentqarray']); $i++) { 
	$exam->chkans($_SESSION['currentqarray'][$i],$_POST[$_SESSION['currentqarray'][$i]])."\n";
}
if($_SESSION['totalQuestions'] == $_SESSION['dne']){
	$_SESSION['for'] *=2;
	$exam->endexam();
	unset($_SESSION['topicid']);
	header("location: results.php?result=latest&user=".$session->username);
}
else{
	header("location:exam.php");
}





?>