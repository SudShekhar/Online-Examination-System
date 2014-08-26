<?php
include('session.php');
class exam
{
  function startExam($topic_id,$marks,$total) //starts exam taking topic id as input
  {
   global $database;
   $_SESSION['isWritingExam'] =true;
   $_SESSION['currentqkey'] =0; //question index
   $_SESSION['for'] =$marks; //questions per round
   $_SESSION['totalQuestions'] =$total; //will count total questions
   $_SESSION['top_id'] =$topic_id;
   $_SESSION['time'] =time();
   $_SESSION['isEndOfExam'] =false;
   $_SESSION['corcount'] =0;
   $_SESSION['difficulty'] = 0;
   $_SESSION['amCheat']=0;
   $_SESSION['end'] = 2*$marks-1;
   $_SESSION['start'] = 0;
   $_SESSION['index'] = (int)(($_SESSION['start'] +$_SESSION['end'])/2);
   
   if($database->isdreqs($topic_id,$_SESSION['totalQuestions']))
   {
    $_SESSION['currentqarray']=$database->getranqarray($topic_id,$_SESSION['totalQuestions']);
    $_SESSION['end']= count($_SESSION['currentqarray'])-1;
    $_SESSION['tot'] = $_SESSION['end'];
    $_SESSION['index'] = (int)(($_SESSION['start'] +$_SESSION['end'])/2);
    return true;
   }
   else
   {
    return false;
   }
  } 
  
  function loadmyQuestion($difficulty=0) //loads current question
  { 
  global $database;
  while($_SESSION['currentqarray'][$_SESSION['index']] == -1 && $_SESSION['index'] <= $_SESSION['tot'])
    $_SESSION['index']++;
  if($_SESSION['index'] > $_SESSION['tot'] )
  {
    $_SESSION['index']=0;
    while($_SESSION['currentqarray'][$_SESSION['index']] == -1 && $_SESSION['index'] <= $_SESSION['tot'])
      $_SESSION['index']++;
  }
  if($_SESSION['index'] > $_SESSION['tot'])
    {
      alert("Sorry we are facing some issues");
      header("location:index.php");
    }

  $res=$database->query('select q_text from questions where q_id='.$_SESSION['currentqarray'][$_SESSION['index']]);
  
  $row=mysql_fetch_array($res);
  return array($row[0],$_SESSION['currentqarray'][$_SESSION['index']]);
  }

  function nextQ()
  {
    $_SESSION['currentqkey']++;
    if($_SESSION['currentqkey'] >= $_SESSION['for'])
      $_SESSION['isEndOfExam']=true;
  }
  function loadans() //returns row[0] option1,row[1] option2....row[3] option4
  {
   global $database;

   $res=$database->query('select q_op1,q_op2,q_op3,q_op4 from questions where q_id='.$_SESSION['currentqarray'][$_SESSION['index']]);
   $row=mysql_fetch_array($res);
   for($i=0;$i<4;$i++)
   $ar[]=array('key'=>$i+1,'value'=>$row[$i]);
   shuffle($ar);
   return $ar;
  }
  function givehint(){
    global $database;
    $res = $database->query('select qhint from questions where q_id='.$_SESSION['currentqarray'][$_SESSION['index']]);
    $row = mysql_fetch_array($res);
    return $row[0];
  }
  function chkans($qid,$ansid,$hnt) //checks the ans is correct or wrong
  {
   global $database;
    if($hnt==1)
      $_SESSION['amCheat']=1;

    
    if($database->iscor($qid,$ansid)) 
    {
    $_SESSION['corcount']++; 
    $_SESSION['difficulty']++;
    $_SESSION['difficulty'] = min($_SESSION['difficulty'],2);
    $_SESSION['start'] = $_SESSION['index']+1;
    $_SESSION['start'] = min($_SESSION['start'],$_SESSION['tot']);
    $_SESSION['index'] = (int)(($_SESSION['start'] +$_SESSION['end'])/2);

    return true;
    }
    else{
      $_SESSION['difficulty']--;
      $_SESSION['difficulty'] = max($_SESSION['difficulty'],0);
      $_SESSION['end'] = $_SESSION['index']-1;
      $_SESSION['end'] = max($_SESSION['end'],0);
      $_SESSION['index'] = (int)(($_SESSION['start'] +$_SESSION['end'])/2);
      return false;
    }
  }
  function endexam()
  {
    global $database;
    global $session;
    $_SESSION['isWritingExam']=false;
    $res=$database->query("INSERT INTO exam_res (`exam_id`, `result`, `for`, `timestamp`, `top_id`,`cheat`, `username`) VALUES (NULL, '".$_SESSION['corcount']."', '".$_SESSION['totalQuestions']."', '".$_SESSION['time']."', '".$_SESSION['top_id']."', '".$_SESSION['amCheat']."','".$session->username."')");
    $res2= $database->query("UPDATE users SET `score`=`score`+'".$_SESSION['corcount']."'");
    unset($_SESSION['currentqarray']);    
    unset($_SESSION['corcount']);
    unset($_SESSION['currentqkey']);
    if(!$res)
      echo "error submiting";  
    if(!$res)
      echo "Score not updated";    
  }
  function isEndOfExam()
  {
  return $_SESSION['isEndOfExam'];
  }
}
$exam = new exam;
?>