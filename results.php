<?php
include("include/session.php");

?>
<script>
function saveData(data) {
  ndata = {}
  ndata['id'] = data;
 $.ajax({
                url : 'http://localhost/temp/Online-Examination-System/include/database.php',
                type : 'POST',
                async : true,
                data : ndata,
                success : function(response){
                        location.reload();
                },
                error : function(response){
                        console.log('Failed to insert data');
                }
        }); 
 }
 function accept(data) {
  var apologize = confirm('Do you accept you have cheated?');
  alert(apologize);
  alert(data);
 }
</script>
<html>
<head>
<title>Talent Hunt-results</title>
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
<script src="js/jquery.js"></script>
<style>
#data tr{background-color:#BC7E7E;color:#301A1A;}
#data td{font-size:19px;}
#data{width:500px;padding-left:3px;}
a img {border:none;}
</style>
</head>
<body>
<?php
global $database;
global $session;
if(isset($_GET['admin']))
{
 if($session->isAdmin()){
   include("selectexam.html.php");
 }
}
if(isset($_POST['topic']))
{
  echo "<table align='center' border='1'>";
  $rows=$database->getreport($_POST['topic']);
  $count=1;
  foreach ($rows as $row) {
     echo "<tr><td>".$count++."</td><td>".$row['username']."</td><td>".$row['result']."</td></tr>";
  }
  echo "</table>";
}
if(isset($_GET['id']))
{
 $examid=$_GET['id'];
  if(!$database->valid($examid))
  {
  echo "<font color=red><b>not a valid exam</b></font>";
  exit();
  }
 $result=$database->getresults($examid);
 echo "<table>";
 echo "<tr ><td colspan=2 align=\"center\"><b>Result</b></td></tr>";
 echo "<tr><td colspan=2 align=\"center\"><image src=\"imageloader.php?num=".$result['got']."&den=".$result['for']."\"></td></tr>";
 echo "<tr><td><b>Marks obtained</b></td><td>".$result['got']."</td></tr>";
 echo "<tr><td><b>Conducted marks</b></td><td>".$result['for']."</td></tr>";
 echo "<tr><td><b>Topic :</b></td><td>".$result['topic']."</td></tr>";
 echo "<tr><td><b>Date</b></td><td>".$result['date']."</td></tr>";
 echo "<tr><td><b>Time</b></td><td>".$result['time']."</td></tr>";
 echo "</table>";
 echo "<a href=\"?user=".$result['username'].'" title="Go back"><img src="images/back.png"></a>';
}
else if(isset($_GET['user']))
{
 if($_GET['result']=='latest')
 {
  $result=$database->getresults($database->latestExamID($_GET['user']));
  echo "<table>";
  echo "<tr ><td colspan=2 align=\"center\"><b>Result</b></td></tr>";
  echo "<tr><td colspan=2 align=\"center\"><image src=\"imageloader.php?num=".$result['got']."&den=".$result['for']."\"></td></tr>";
  echo "<tr><td><b>Marks obtained</b></td><td>".$result['got']."</td></tr>"; 
  echo "<tr><td><b>Conducted marks</b></td><td>".$result['for']."</td></tr>";
  echo "<tr><td><b>Topic :</b></td><td>".$result['topic']."</td></tr>";
  echo "<tr><td><b>Date</b></td><td>".$result['date']."</td></tr>";
  echo "<tr><td><b>Time</b></td><td>".$result['time']."</td></tr>";
  echo "</table>";
  echo "<a href=\"?user=".$result['username'].'" title="Go back"><img src="images/back.png"></a>';
 }
 else{
  $username=$_GET['user'];
   if($database->noresults(($username)))
   {
    echo "<b><font color=red size=6>".$username."</font></b> hasn't attended exams until now";
    exit();
   }
  $exams=$database->getExams($username);
  echo "<h1>Exams list(latest to oldest)</h1>";
  echo "<table cellpadding=5 cellspacing=0 id=data>"; 
  echo "<tr id=\"tag\"><td ><b>Topic</b></td><td><b>Date</b></td><td><b>Result</b></td><td><b>User</b></td><td><b>Flag</b></td><td><b>Flagged?</b></td></tr>";
  foreach($exams as $exam)
  echo "<tr><td>".$exam['topic']."</td><td>".$exam['date']."</td><td><a href=\"?id=".$exam['id']."\">view results</a></td><td>".$exam['name'].
       "</td>" .(($exam['flag']==0 && $exam['name']!= $username)?"<td><a href='#' onclick=\"javascript:saveData(".$exam['id'].");\">Flag</a>":"
        <td><input type='submit' name='Flag' value='Flagged/NA' disabled>"). "</td><td>" .(($exam['name']== $username && $exam['flag']==1)?"<a href='#' onclick=\"javascript:accept(".$exam['id'].");\">You are flagged</a>":"
        <input type='submit' name='Flag' value='NA' disabled>")."</td></tr>";
  echo "</table>";
  echo "<a href=\"./\" title=\"Go back\"><img src=\"images/back.png\"></a>";
 }
}
?>
