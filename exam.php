<?php
include("include/exam.php");
global $database;
global $session;
global $exam;
if(!$session->logged_in)
include("login.php");
else if(!isset($_POST['subjectid']) && !isset($_POST['topicid']) && !(isset($_SESSION['subid']) && isset($_SESSION['topicid']) && isset($_SESSION['marks'])))
  include("disp.php");
else if(isset($_POST['subjectid']) && isset($_POST['topicid']) || (isset($_SESSION['subid']) && isset($_SESSION['topicid']) && isset($_SESSION['marks'])))
{
if(isset($_POST['subjectid']) && isset($_POST['topicid']))  {
  $subid=$_POST['subjectid'];
  $topicid=$_POST['topicid'];
  $marks=1;
  $_SESSION['subid'] = $subid;
  $_SESSION['topicid'] = $topicid;
  $_SESSION['marks'] = 1;
  $val=$exam->startExam($topicid,$marks,$_POST['rounds']);
  $_SESSION['val'] = $val;
  $count=1;
}
else{
  $subid=$_SESSION['subid'];
  $topicid = $_SESSION['topicid'];
  $marks=$_SESSION['marks'];
  $val = $_SESSION['val'];
  $count = 1;
  $marks=1;
}

$style = <<< EOF
<style>
#clock{width:95px;height:40px;margin:3px 0px 0px 3px;}
#hint{width:95px;height:40px;margin:3px 0px 0px 3px;}
.right{float:right;padding-bottom: 5em; width: 20%;}
.left{
  float: left;
  width:80%;
  padding-bottom: 5em;
  height:400px;
  overflow: scroll;
}
.fancy_button {
 display: block;
 background: #62869b;
 width: 200px;
 height: 50px;
 text-align: center;
 padding: 30px 0 0 0;
 font: 1.2em/12px Verdana, Arial, Helvetica, sans-serif;
 color: #fff;
 text-decoration: none;
 -webkit-border-radius: 15px;
 -khtml-border-radius: 15px;
 -moz-border-radius: 15px;
 border-radius: 15px;
 }

.right table tbody tr{background-color: #4B2C0C;}
.qs{background-color: #D08437;}
\
#footer{margin-top: 20px;}
</style>
EOF;

$tme = $_SESSION['for'];
$script = <<< EOF
<script>
\$(function(){
\$('#clock').countdown({until: '+{$tme}m',format:'MS',onExpiry:comp});
function comp(){\$("#answers").submit();}
\$("#sub").click(function(){
\$.post(".php",{qid:\$("#qid").val(),ans:\$("input:checked").val()},function(data){\$("#content").html(data);});
});

\$("#hint").click(function(){
  \$("#data").show();
  \$("#havecheated").val(1);
});
});


</script>
EOF;
$title = "Talent Hunt";
$head = <<< EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<title>{$title}</title>
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
<link rel="stylesheet" href="css/jquery.countdown.css">
<script src="js/jquery.js"></script>
<script src="js/jquery.countdown.js"></script>
{$script}
{$style}
<meta http-equiv="content-type"
content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" type="text/css" href="css/footerframe.css" />
</head>
EOF;
$topicName = $database->gettopicname($_SESSION['top_id']);
if($val){
echo $head;

$lft="";
do
{
  $qdetails = $exam->loadmyQuestion($_SESSION['difficulty']);
  $id = $qdetails[1];
  $qtxt = $qdetails[0];
  $hnt = $exam->givehint();
  $allqids[]=$id;
  $answers=$exam->loadans();
  $allanswers[] = $answers;
  $lft.="(Q".$count.") ".$qtxt."<br>";
  $i = 'a';
  foreach($answers as $answer)
   {
    $lft.= $i.")&nbsp;".$answer['value'].'<br>';
    $i++;
  }
  $lft.="<br />";
$exam->nextQ();
$count++;
$_SESSION['done']++;
}
while(!$exam->isEndOFExam());

$frm = "<form action=\"evaluate.php\" method=\"post\" id=\"answers\">
    <table>
      <thead>
        <td></td>
        <td>A</td>
        <td>B</td>
        <td>C</td>
        <td>D</td>
      </thead>";
for($i=0;$i<count($allanswers);$i++){
  $ind = $i+1;
  $frm.="<tr><td class=\"qs\">".$ind."</td>";
  foreach ($allanswers[$i] as $answer) {
     $frm.="<td><input type=\"radio\" name=".$allqids[$i]." value=".$answer['key']."></td>";
  }
  $frm.="</tr>";
}
    $frm.="
    <input type=\"hidden\" id=\"havecheated\" name=\"havecheated\" value=\"0\">
    </table>
    <input type=\"submit\" value=\"go\"> 
  </form>";

$bodyup = <<< EOF
<body>
<div id="wrapper">
<div id="header">
<h1 class="logo">{$title}</h1>
<span class="strapline">Topic: {$topicName}</span>
</div>
<div id="content">
<div class="left"> 
{$lft}
</div>
 <div class="right">
{$frm}
 </div>
</div>
</div>
<div><button id="hint" type="button" class="fancy button" >Hint</button> <p id="data" style="display:none;">{$hnt}</p></div>
<div id="footer">
<div id="clock"></div>

</div>
</body>
</html>
EOF;
echo $bodyup;
}
else
{ 
echo "Sorry, there are not as many questions as you asked for";
}
}
?>
