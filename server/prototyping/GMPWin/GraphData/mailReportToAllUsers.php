<?php

/*
	 * Copyright (c) 2015, CEDEP France,
 	 * Authors: Albert A. Angehrn, Marco Luccini, Pradeep Kumar Mittal
         * All rights reserved.
	 * Redistribution and use in source and binary forms, with or without modification, 
	 * are permitted provided that the following conditions are met:
	 *
	 *  * Redistributions of source code must retain the above copyright notice, 
	 *    this list of conditions and the following disclaimer. 
	 *  * Redistributions in binary form must reproduce the above copyright notice, 
	 *    this list of conditions and the following disclaimer in the documentation
	 *    and/or other materials provided with the distribution. 
	 *  * Neither the name of the COLLAGE Group nor the names of its 
	 *    contributors may be used to endorse or promote products derived from this 
	 *    software without specific prior written permission. 
	 *
	 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
	 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	 * DISCLAIMED. IN NO EVENT SHALL CONSORTIUM BOARD COLLAGE Group BE LIABLE FOR ANY
	 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
	 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

?>


<?php
// the next 2 lines to prevent caching
session_start();
header("Cache-control: private");
header("Content-type: text/html;charset=UTF-8");
include "conntube.php";
$period = isset($_REQUEST['period']) ? $_REQUEST['period'] : '';
//error_log($period);
$query ="select * from peoplenodes ";    	  

$rs				=	mysql_query ($query);
//$nameStr="";

//$retstring=  $retstring ."<html>\n<body>\n";  //error_log($retstring);
//$retstring=  $retstring ."<html>\n<body>\n";  //error_log($retstring);
//$retstring=  $retstring ."<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\">\n";
//$retstring= $retstring ." <Report>";

$retstring=  "<html>\n<body>\n";  //error_log($retstring);
// general details	
	//$retstring= $retstring ." </Users>";
//total no. of accesses
if($period=='')
$query ="select Id from useractions where (Datetime > '$LRT' and action='Login')";    	  
else
$query ="select Id from useractions where (Datetime > TIMESTAMPADD(DAY,'$period',now()) and action='Login')";    	  
$rs				=	mysql_query ($query);
$no_of_accesses= mysql_num_rows($rs);
$retstring= $retstring ."<h2>General Activity</h2>\n";//error_log($retstring);
$retstring=$retstring ." <h4>total number of accesses</h4>\n";
$retstring=$retstring ." <p>".$no_of_accesses."  </p>\n";

//no. of new users 
if($period=='')
$query ="select name from peoplenodes where jointime > '$LRT'";    	  
else
$query ="select name from peoplenodes where jointime > TIMESTAMPADD(DAY,'$period',now())";    	  
$rs				=	mysql_query ($query);
$nameStr="";
while ($row = mysql_fetch_array($rs))
{
    if($nameStr=="")
    {
       $nameStr=$row['name'];
    } 
    else
	{
	  $nameStr=$nameStr."</p>".$row['name']."<p>";
	}
}
$no_of_new_users= mysql_num_rows($rs);
$retstring=$retstring ." <h4>total number of new users</h4>\n";
$retstring=$retstring ." <p>".$no_of_new_users."  </p>\n";
$retstring=$retstring ." <h4>new users</h4>\n";
$retstring=$retstring ." <p>".$nameStr."  </p>\n";

//no. of new videos
if($period=='')
$query ="select name from videonodes where submittime > '$LRT'";    	  
else
$query ="select name from videonodes where submittime > TIMESTAMPADD(DAY,'$period',now())";    	  
$rs				=	mysql_query ($query);
$no_of_new_videos= mysql_num_rows($rs);
$nameStr="";
while ($row = mysql_fetch_array($rs))
{
    if($nameStr=="")
    {
       $nameStr=$row['name'];
    } 
    else
	{
	  $nameStr=$nameStr."</p>".$row['name']."<p>";
	}
}
$retstring=$retstring ." <h4>number of new videos</h4>\n";
$retstring=$retstring ." <p>".$no_of_new_videos."  </p>\n";

$retstring=$retstring ." <h4>new videos</h4>\n";
$retstring=$retstring ." <p>".$nameStr."  </p>\n";

//no. of times game was played
if($period=='')
$query ="select gameid from g3_gamelog where datetime >'$LRT'";    	  
else
$query ="select gameid from g3_gamelog where datetime >TIMESTAMPADD(DAY,'$period',now())";    	  
$rs				=	mysql_query ($query);
$no_of_times_game_played= mysql_num_rows($rs);
$retstring=$retstring ." <h4>number of times the game was played</h4>\n";
$retstring=$retstring ." <p>".$no_of_times_game_played."  </p>\n";
//no. of new connections created 
if($period=='')
$query ="select id from edges where ( creationtime > '$LRT' and name='Knows')";    	  
else
$query ="select id from edges where ( creationtime > TIMESTAMPADD(DAY,'$period',now()) and name='Knows')";    	  
$rs				=	mysql_query ($query);
$no_of_new_connections= mysql_num_rows($rs);
$retstring=$retstring ." <h4>number of new connections</h4>\n";
$retstring=$retstring ." <p>".$no_of_new_connections."  </p>\n";

//no. of new comments made 
if($period=='')
$query ="select * from commentstable where (datetime > '$LRT')";    	  
else
$query ="select * from commentstable where (datetime > TIMESTAMPADD(DAY,'$period',now()))";    	  
$rs				=	mysql_query ($query);
$no_of_new_comments= mysql_num_rows($rs);
$retstring=$retstring ." <h4>number of new comments</h4>\n";
$retstring=$retstring ." <p>".$no_of_new_comments."  </p>\n";

//top 3 users who logged in most number of times
$c1=0;
$c2=0;
$c3=0;
$p1="";
$p2="";
$p3="";
$query = "select id from peoplenodes";
$rs				=	mysql_query ($query);
while ($row = mysql_fetch_array($rs))
{
    $person=$row['id'];
	if($period=='')
	$query1 ="select Id from useractions where (Datetime > '$LRT' and action='Login' and TakenBy='$person')";    	  
    else
	$query1 ="select Id from useractions where (Datetime > TIMESTAMPADD(DAY,'$period',now()) and action='Login' and TakenBy='$person')";    	  
    $rs1	=	mysql_query ($query1);
    $count = mysql_num_rows($rs1);
	if($count>$c1)
	{
	    $c3=$c2;
		$p3=$p2;
	    $c2=$c1;
		$p2=$p1;
	    $c1=$count;
		$p1=$person;
	}
	else if($count > $c2)
	{
	    $c3=$c2;
        $p3=$p2;
        $c2=$count;
        $p2=$person;		
	}
	else if($count > $c3)
	{
	    $c3=$count;
		$p3=$person;
	}
}
$top3users = $p1."</p><p>".$p2."</p><p>".$p3;
$retstring=$retstring ." <h4>top three users</h4>\n";
$retstring=$retstring ." <p>".$top3users."  </p>\n";

//no. of different videos seen
if($period=='')
$query ="select id from videonodes where lastseen >  '$LRT'";    	  
else
$query ="select id from videonodes where lastseen >  TIMESTAMPADD(DAY,'$period',now())";    	  
$rs				=	mysql_query ($query);
$no_of_diff_videos_seen= mysql_num_rows($rs);
$retstring=$retstring ." <h4>number of different videos seen</h4>\n";
$retstring=$retstring ." <p>".$no_of_diff_videos_seen."  </p>\n";

//top 3 videos seen
//v - videoId c-Counts
$c1=0;
$c2=0;
$c3=0;
$v1="";
$v2="";
$v3="";
$query = "SELECT id FROM videonodes";
$rs				=	mysql_query ($query);
while ($row = mysql_fetch_array($rs))
{
    $videoId=$row['id'];
	if($period=='')
	$query1 ="select Id from useractions where (Datetime > '$LRT' and action='WatchVideo' and TakenOn='$videoId')";    	  
    else
	$query1 ="select Id from useractions where (Datetime > TIMESTAMPADD(DAY,'$period',now()) and action='WatchVideo' and TakenOn='$videoId')";    	  
    $rs1	=	mysql_query ($query1);
    $count = mysql_num_rows($rs1);	
	if($count>$c1)
	{
	    $c3=$c2;
		$v3=$v2;
	    $c2=$c1;
		$v2=$v1;
	    $c1=$count;
		$v1=$videoId;
	}
	else if($count > $c2)
	{
	    $c3=$c2;
        $v3=$v2;
        $c2=$count;
        $v2=$videoId;		
	}
	else if($count > $c3)
	{
	    $c3=$count;
		$v3=$videoId;
	}
}
// $top3videosIds = $v1."\n".$v2."\n".$v3;
// $retstring=$retstring ." <topThreeVideosIds>".$top3videosIds."  </topThreeVideosIds>";
$query = "SELECT name FROM videonodes where id='$v1'";
$rs				=	mysql_query ($query);
while ($row = mysql_fetch_array($rs))
{
  $p1=$row['name'];
}
$query = "SELECT name FROM videonodes where id='$v2'";
$rs				=	mysql_query ($query);
while ($row = mysql_fetch_array($rs))
{
  $p2=$row['name'];
}
$query = "SELECT name FROM videonodes where id='$v3'";
$rs				=	mysql_query ($query);
while ($row = mysql_fetch_array($rs))
{
  $p3=$row['name'];
}
$top3videos = $p1."".$p2."</p><p>".$p3."</p>";
$retstring=$retstring ." <h4>top three videos</h4>\n";
$retstring=$retstring ." <p>".$top3videos."  </p>\n";

//no of times rating of videos had been done
if($period=='')
$query ="select Id from useractions where (Datetime > '$LRT' and action='Rated')";    	  
else
$query ="select Id from useractions where (Datetime > TIMESTAMPADD(DAY,'$period',now()) and action='Rated')";    	  
$rs				=	mysql_query ($query);
$no_of_ratings= mysql_num_rows($rs);
$retstring=$retstring ." <h4>number of ratings</h4>\n";
$retstring=$retstring ." <p>".$no_of_ratings."  </p>\n";

//no of videostarts logged in useracitons
if($period=='')
$query ="select Id from useractions where (Datetime > '$LRT' and action='VideoStart')";    	  
else
$query ="select Id from useractions where (Datetime > TIMESTAMPADD(DAY,'$period',now()) and action='VideoStart')";    	  
$rs				=	mysql_query ($query);
$no_of_video_starts= mysql_num_rows($rs);
$retstring=$retstring ." <h4>number of video starts</h4>\n";
$retstring=$retstring ." <p>".$no_of_video_starts."  </p>\n";

//no of videoends logged in useracitons
if($period=='')
$query ="select Id from useractions where (Datetime > '$LRT' and action='VideoEnd')";    	  
else
$query ="select Id from useractions where (Datetime > TIMESTAMPADD(DAY,'$period',now()) and action='VideoEnd')";    	  
$rs				=	mysql_query ($query);
$no_of_video_ends= mysql_num_rows($rs);
$retstring=$retstring ." <h4>number of video ends</h4>\n";
$retstring=$retstring ." <p>".$no_of_video_ends."  </p>\n";

//total no. of profileviews 
if($period=='')
$query ="select Id from useractions where (Datetime > '$LRT' and action='ProfileView')";    	  
else
$query ="select Id from useractions where (Datetime > TIMESTAMPADD(DAY,'$period',now()) and action='ProfileView')";    	  
$rs				=	mysql_query ($query);
$no_of_profileviews= mysql_num_rows($rs);
$retstring=$retstring ." <h4>total number of Profile Views</h4>\n";
$retstring=$retstring ." <p>".$no_of_profileviews."  </p>\n";

//new discussion topics
if($period=='')
$query="select * from topics where (time > '$LRT')";
else
$query="select * from topics where (time > TIMESTAMPADD(DAY,'$period',now()))";
$rs				=	mysql_query ($query);
$nameStr="";
while ($row = mysql_fetch_array($rs))
{
    if($nameStr=="")
    {
       $nameStr=$row['name'];
    } 
    else
	{
	  $nameStr=$nameStr."</p>".$row['name']."<p>";
	}
}
$retstring=$retstring ." <h4>New Discussion Topics</h4>\n";
$retstring=$retstring ." <p>".$nameStr."  </p>\n";

//new tags
if($period=='')
$query="select * from tagnodes where (submittime > '$LRT')";
else
$query="select * from tagnodes where (submittime > TIMESTAMPADD(DAY,'$period',now()))";
$rs				=	mysql_query ($query);
$nameStr="";
while ($row = mysql_fetch_array($rs))
{
    if($nameStr=="")
    {
       $nameStr=$row['name'];
    } 
    else
	{
	  $nameStr=$nameStr."</p>".$row['name']."<p>";
	}
}
$retstring=$retstring ." <h4>New Tags</h4>\n";
$retstring=$retstring ." <p>".$nameStr."  </p>\n";

//new interests
if($period=='')
$query="select * from controlpanel where (submittime > '$LRT')";
else
$query="select * from controlpanel where (submittime > TIMESTAMPADD(DAY,'$period',now()))";
$rs				=	mysql_query ($query);
$nameStr="";
while ($row = mysql_fetch_array($rs))
{
    if($nameStr=="")
    {
       $nameStr=$row['name'];
    } 
    else
	{
	  $nameStr=$nameStr."</p>".$row['name']."<p>";
	}
}
$retstring=$retstring ." <h4>New Interests</h4>\n";
$retstring=$retstring ." <p>".$nameStr."  </p>\n";


$genData = $retstring;  // to store the genral report data in one seperate variable


//$retstring= $retstring ."<h2>User Specific Data</h2>\n";//error_log($retstring);

while ($row = mysql_fetch_array($rs))
{

	$retstring = "";  // not mix the restring for other users

	$userid=$row['id'];
	//$retstring= $retstring ." <User>";
	$retstring= $retstring ."<h3>".$userid."</h3>\n";//error_log($retstring);
	if($period=='')
		$LRT=$row['LRT'];
	//else
	//$GRT=TIMESTAMPADD(DAY,'$period',now());
	//your videos
	//update the LRT
	mysql_query ("UPDATE peoplenodes SET LRT=current_timestamp() where id='$userid'");
	$retstring= $retstring ."<h2>Activity Related to Your Specific Interests</h2>\n";//error_log($retstring);
	//$query="select * from videonodes where submittedby=\'$row['submittedby']\'";
	$query="select * from videonodes where submittedby='$userid'";
	$rs1=mysql_query($query);
	$retstring= $retstring ."<table border=\"1\">\n<tr>\n<th>Your Videos</th>\n<th>Rating</th>\n<th># Views Total</th>\n<th># Views New</th>\n<th># Comments Total</th>\n<th># Comments New</th>\n<th># Discussions Total</th>\n<th># Discussions New</th>\n</tr>\n";
	//error_log($retstring);
	while($row1 = mysql_fetch_array($rs1))
	{
		$videoid=$row1['id'];
		$retstring= $retstring ."<tr>\n<td>".$row1['name']."</td>\n";//error_log($retstring);
		$retstring= $retstring ." <td>".$row1['grandrating']."</td>\n";//error_log($retstring);
		//$retstring= $retstring ." <views>";
		$retstring= $retstring ." <td>".$row1['timesseen']."</td>\n";
		if($period=='')
		$query="select * from edges where name='Has seen' AND toID='$videoid' AND creationtime > '$LRT'";
		else
		$query="select * from edges where name='Has seen' AND toID='$videoid' AND creationtime > TIMESTAMPADD(DAY,'$period',now())";
		$newrows=mysql_query($query);
		$newno=mysql_num_rows($newrows);
		$retstring= $retstring ." <td>".$newno."</td>\n";//error_log($retstring);
		//$retstring= $retstring ." </views>";
		//$retstring= $retstring ." <comments>";
		$query="select * from commentstable where videoid='$videoid'";
		$totalrows=mysql_query($query);
		$totalno=mysql_num_rows($totalrows);
		$retstring= $retstring ." <td>".$totalno."</td>\n";//error_log($retstring);
		if($period=='')
		$query="select * from commentstable where videoid='$videoid' AND datetime > '$LRT'";
		else
		$query="select * from commentstable where videoid='$videoid' AND datetime > TIMESTAMPADD(DAY,'$period',now())";
		$newrows=mysql_query($query);
		$newno=mysql_num_rows($newrows);
		$retstring= $retstring ." <td>".$newno."</td>\n";//error_log($retstring);
		//$retstring= $retstring ." </comments>";
		//$retstring= $retstring ." <discussions>";
		$query="select * from topicmessages as t where t.topicid IN (select topicid from topics where videoid = '$videoid')";
		$totalrows=mysql_query($query);
		$totalno=mysql_num_rows($totalrows);
		$retstring= $retstring ." <td>".$totalno."</td>\n";//error_log($retstring);
		if($period=='')
		$query="select * from topicmessages as t where t.topicid IN (select topicid from topics where videoid = '$videoid') AND time > '$LRT'";
		else
		$query="select * from topicmessages as t where t.topicid IN (select topicid from topics where videoid = '$videoid') AND time > TIMESTAMPADD(DAY,'$period',now())";
		$newrows=mysql_query($query);
		$newno=mysql_num_rows($newrows);
		$retstring= $retstring ." <td>".$newno."</td>\n";//error_log($retstring);
		//$retstring= $retstring ." </discussions>";
		//$retstring= $retstring ." </video>";
		$retstring= $retstring ."</tr>\n";//error_log($retstring);
	}
	//$retstring= $retstring ."</tr>\n";//error_log($retstring);
	$retstring= $retstring ."</table>\n";//error_log($retstring);
	//$retstring= $retstring ." </yourvideos>";
	
	
	
	//your profile
	$retstring= $retstring ."<h3>Your Profile</h3>\n";
	$retstring= $retstring ."<table border=\"1\">\n<tr>\n<th># Views Total</th>\n<th># Views New</th>\n<th># Comments Total</th>\n<th># Comments New</th>\n<th>Your Game Score</th>\n<th>Best Game Score</th>\n</tr>\n<tr>\n";
	$query="select * from useractions where TakenOn='$userid' AND action='ProfileView'";
	$totalrows=mysql_query($query);
	$totalno=mysql_num_rows($totalrows);
		$retstring= $retstring ." <td>".$totalno."</td>\n";
		if($period=='')
		$query="select * from useractions where TakenOn='$userid' AND action='ProfileView' AND Datetime > '$LRT'";
		else
		$query="select * from useractions where TakenOn='$userid' AND action='ProfileView' AND  Datetime > TIMESTAMPADD(DAY,'$period',now())";
		$newrows=mysql_query($query);
		$newno=mysql_num_rows($newrows);
		$retstring= $retstring ." <td>".$newno."</td>\n";
	
	//$retstring= $retstring ." </views>";
	//$retstring= $retstring ." <comments>";
	$query="select * from scrapstable where userid='$userid'";
	$totalrows=mysql_query($query);
	$totalno=mysql_num_rows($totalrows);
		$retstring= $retstring ." <td>".$totalno."</td>\n";
		if($period=='')
		$query="select * from scrapstable where userid='$userid' AND datetime > '$LRT'";
		else
		$query="select * from scrapstable where userid='$userid' AND  datetime > TIMESTAMPADD(DAY,'$period',now())";
		$newrows=mysql_query($query);
		$newno=mysql_num_rows($newrows);
		$retstring= $retstring ." <td>".$newno."</td>\n";
	//$retstring= $retstring ." </comments>";
	//$retstring= $retstring ." </yourprofile>";
	//$retstring= $retstring ." <gamescore>";
	$retstring= $retstring ." <td>".$row['grandscore']."</td>\n";
	$best=mysql_query("select * from peoplenodes ORDER BY grandscore DESC");
	$bestscore = mysql_fetch_array($best);
	$retstring= $retstring ." <td>".$bestscore['grandscore']."</td>\n";
	//$retstring= $retstring ." </gamescore>";
	$retstring= $retstring ."</tr>\n";//error_log($retstring);
	$retstring= $retstring ."</table>\n";//error_log($retstring);
	//$retstring= $retstring ." </User>";
	
	//name of people who connected to you
	if($period=='')
	$query="select * from edges where name = 'Knows' AND (fromID='$userid' OR toID='$userid') AND creationtime > '$LRT'";
	else
	$query="select * from edges where name = 'Knows' AND (fromID='$userid' OR toID='$userid') AND creationtime > TIMESTAMPADD(DAY,'$period',now())";
	$rs2=mysql_query($query);
	$retstring= $retstring ."<h2>New people who have connected to you</h2>\n";//error_log($retstring);
	$retstring= $retstring ." <ol>\n";
	while($row2 = mysql_fetch_array($rs2))
	{
		if($row2['fromID']==$userid)
			$name=mysql_query("select name from peoplenodes where id='".$row2['toID']."'");
		else
			$name=mysql_query("select name from peoplenodes where id='".$row2['fromID']."'");
		$name_know = mysql_fetch_array($name);
		$retstring= $retstring ." <li>".$name_know['name']."</li>\n";
	}
	$retstring= $retstring ." </ol>\n";
	//your ongoing discussions
	
		$query="select * from topicmessages where userid='$userid'";
	
	$rs3=mysql_query($query);
	$retstring= $retstring ."<table border=\"1\">\n<tr>\n<th>Your Ongoing Discussions</th>\n<th>#New Responses</th>\n</tr>\n";
	while($row3 = mysql_fetch_array($rs3))
	{
		$retstring= $retstring ." <tr>\n";
		$query=mysql_query("select * from topics where topicid='".$row3['topicid']."'");
		$name_topic = mysql_fetch_array($query);
		$retstring= $retstring ." <td>".$name_topic['name']."</td>\n";
		if($period=='')
		$query=mysql_query("select * from topicmessages where topicid='".$row3['topicid']."' AND time > '$LRT'");
		else
		$query=mysql_query("select * from topicmessages where topicid='".$row3['topicid']."' AND time > TIMESTAMPADD(DAY,'$period',now())");
		//$totalrows=mysql_query($query);
		$totalno=mysql_num_rows($query);
		$retstring= $retstring ." <td>".$totalno."</td>\n";
		$retstring= $retstring ." </tr>\n";
	}
	$retstring= $retstring ."</table>\n";
	
	
	//videos you have commented
	$query="select * from commentstable where authorid='$userid'";
	$rs4=mysql_query($query);
	$retstring= $retstring ."<table border=\"1\">\n<tr>\n<th>Videos You have Commented</th>\n<th>#New Comments</th>\n</tr>\n";
	while($row4 = mysql_fetch_array($rs4))
	{
		$retstring= $retstring ." <tr>\n";
		$query=mysql_query("select * from videonodes where id='".$row4['videoid']."'");
		$name_video = mysql_fetch_array($query);
		$retstring= $retstring ." <td>".$name_video['name']."</td>\n";
		if($period=='')
		$query=mysql_query("select * from commentstable where videoid='".$row4['videoid']."' AND datetime > '$LRT'");
		else
		$query=mysql_query("select * from commentstable where videoid='".$row4['videoid']."' AND datetime > TIMESTAMPADD(DAY,'$period',now())");
		//$totalrows=mysql_query($query);
		$totalno=mysql_num_rows($query);
		$retstring= $retstring ." <td>".$totalno."</td>\n";
		$retstring= $retstring ."</tr>\n";
	}
	$retstring= $retstring ."</table>\n";
	
   //  $userData 

    $message = "Hello";

	
	}  // end of while loop for fetcing users reports



$retstring=$retstring."</body></html>";//error_log($retstring);
echo $retstring;
//error_log($retstring);
	

//sending mail code 

//$retstring = "<html><body>Hello <b>world</b> </body></html>";

//$to      = 'krishnamahesh678@gmail.com'.', '.'krishna.katepalli@insead.edu';
$to      = 'pkmittal82@gmail.com';

$subject = 'Daily Report of TENTube Activity';
$message = $retstring;

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

$headers .= 'From: CALTTubes@insead.edu' . "\r\n" .
    'Reply-To: pkmittal82@gmail.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

$val=mail($to, $subject, $message, $headers);

if($val==true)
  echo "Success";
else if($val==false)
  echo "Failure"; 



?>

