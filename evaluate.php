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
$id = $_SESSION['currentqarray'][$_SESSION['index']];
$exam->chkans($id,$_POST[$id]);

$_SESSION['currentqarray'][$_SESSION['index']] = -1;
if($_SESSION['totalQuestions'] == $_SESSION['dne']){
	$_SESSION['for'] = $_SESSION['dne'];
	$exam->endexam();
	$examvars = array("isWritingExam","subid","topicid","marks","corcount");
    foreach($examvars as $toCheck){
        if (isset($_SESSION[$toCheck]))
          unset($_SESSION[$toCheck]);
      }
	header("location: results.php?result=latest&user=".$session->username);
}
else{
    $_SESSION['currentqkey']=0;
	header("location:exam.php");
}





?>