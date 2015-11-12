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
include "conntube.php";
session_start();
ini_set('session.gc_maxlifetime', 1000);
header("Cache-control: private");
header("Content-type: text/html;charset=UTF-8");

global $full_tubename ;
$tubename=$full_tubename;
$period = isset($_REQUEST['period']) ? $_REQUEST['period'] : '';
$LRT=time();
$val='mail';
$retstring='';
$query ="select * from peoplenodes";   
$rsu				=	mysql_query ($query);
$LRT=time();
$val='mail';

$whole='';
while ($rowu = mysql_fetch_array($rsu))
{
	$retstring = "";  // not mix the restring for other users
	$userid=$rowu['id'];
	$emailid=$rowu['emailid'];
	$retstring= $retstring ."<h4> email report for ".$userid."</h4>\n";
	if($period=='')
	{
		$LRT=$rowu['LRT'];
		$date = new DateTime($LRT);
		$RF=$rowu['RF'];
		if($RF==0)
		continue;
		//error_log($RF);
		//error_log($date->format("d-m-Y"));
		$tomorrow = mktime(date("G"),Date("i"),date("s"),date("m"),date("d")+$RF+1,date("Y"));
		$tomorrow=new DateTime(date("Y/m/d", $tomorrow));
		//date_add($date, new DateInterval("P".-$RF.'D'));
		//error_log($tomorrow->format("d-m-Y"));
		if($tomorrow <= $date)
		continue;
		}
	else
	{
	$LRT = mktime(0,0,0,date("m"),date("d")+$period,date("Y"));
	$LRT=new DateTime(date("Y/m/d", $LRT));
	$LRT=$LRT->format("Y-m-d H:i:s");
	//error_log(time());
	//error_log($period);
	}
	//your videos
	//update the LRT
	if($period=='')
	{
	mysql_query ("UPDATE peoplenodes SET LRT=current_timestamp() where id='$userid'");
	}
	$retstring= $retstring ."<h2>Activity Related to Your Videos</h2>\n";
	$query="select * from videonodes where submittedby='$userid'";
	$rs1=mysql_query($query);
	$i=0;
	if(mysql_num_rows($rs1)!=0)
	{
		while($row1 = mysql_fetch_array($rs1))
	{
		$videoid=$row1['id'];
		
		$query="select * from edges where name='Has seen' AND toID='$videoid' AND creationtime > '$LRT'";
		$newrowsviews=mysql_query($query);
		$newnoviews=mysql_num_rows($newrowsviews);
		
		//$retstring= $retstring ." </views>";
		//$retstring= $retstring ." <comments>";
		$query="select * from commentstable where videoid='$videoid'";
		$totalrowscomments=mysql_query($query);
		$totalnocomments=mysql_num_rows($totalrowscomments);
		
		if($period=='')
		$query="select * from commentstable where videoid='$videoid' AND datetime > '$LRT'";
		
		$newrowscomments=mysql_query($query);
		$newnocomments=mysql_num_rows($newrowscomments);
		
		$query="select * from topicmessages as t where t.topicid IN (select topicid from topics where videoid = '$videoid')";
		$totalrowsdiscussions=mysql_query($query);
		$totalnodiscussions=mysql_num_rows($totalrowsdiscussions);
		//$retstring= $retstring ." <td>".$totalno."</td>\n";
		$query="select * from topicmessages as t where t.topicid IN (select topicid from topics where videoid = '$videoid') AND time > '$LRT'";
		$newrowsdiscussions=mysql_query($query);
		$newnodiscussions=mysql_num_rows($newrowsdiscussions);
		
		//related material
		//new external links
		$query="select * from useractions where action like 'Link%' AND TakenOn='$videoid' AND Datetime > '$LRT' order by Datetime asc";
		$rs5=mysql_query($query);
		$newlinks=0;
		//$gone=0;
		while($row5 = mysql_fetch_array($rs5))
		{
		if($row5['action']=='LinkNew')
		{
		$query=mysql_query("select * from useractions where action = 'LinkRemoved' AND TakenOn='$videoid' AND Datetime > '$LRT'AND TakenBy='".$row5['TakenBy']."'");
		if(mysql_num_rows($query)==0)
		$newlinks=$newlinks +1;
		}
		
		
		
		}
		//new doc links
		$query="select * from useractions where action like 'DocLink%' AND TakenOn='$videoid' AND Datetime > '$LRT' order by Datetime asc";
		$rs5=mysql_query($query);
		$newdoclinks=0;
		//$gone=0;
		while($row5 = mysql_fetch_array($rs5))
		{
		if($row5['action']=='DocLinkNew')
		{
		$query=mysql_query("select * from useractions where action = 'DocLinkRemoved' AND TakenOn='$videoid' AND Datetime > '$LRT'AND TakenBy='".$row5['TakenBy']."'");
		if(mysql_num_rows($query)==0)
		$newdoclinks=$newdoclinks +1;
		}
		}
		$relatedlinksarray=explode("\r",$row1['externalLinks']);
		$total=sizeof($relatedlinksarray);
		if($row1['externalLinks']=='')
		$total=0;
		$relateddoclinksarray=explode("|",$row1['docLinks']);
		$total=$total+sizeof($relateddoclinksarray);
		if($row1['docLinks']=='')
		$total=$total-1;
		
		
		
		if($row1['grandrating']!=0 || $row1['timesseen']!=0 || $newnoviews!=0 || $totalnocomments!=0 || $newnocomments!=0 || $newnodiscussions!=0 || ($newlinks+$newdoclinks)!=0)
		{
		$i=$i+1;
		if($i==1)
		$retstring= $retstring ."<table border=\"1\">\n<tr>\n<th>Your Videos</th>\n<th>Rating</th>\n<th># Views Total</th>\n<th># Views New</th>\n<th># Comments Total</th>\n<th># Comments New</th>\n<th># Discussions New</th>\n<th># Related Material New</th></tr>\n";
		$retstring= $retstring ."<tr>\n<td>".$row1['name']."</td>\n";
		$retstring= $retstring ." <td>".$row1['grandrating']."</td>\n";
		$retstring= $retstring ." <td>".$row1['timesseen']."</td>\n";
		$retstring= $retstring ." <td>".$newnoviews."</td>\n";
		$retstring= $retstring ." <td>".$totalnocomments."</td>\n";
		$retstring= $retstring ." <td>".$newnocomments."</td>\n";
		$retstring= $retstring ." <td>".$newnodiscussions."</td>\n";
		//$retstring= $retstring ." <td>".$total."</td>\n";
		$retstring= $retstring ." <td>".($newlinks+$newdoclinks)."</td>\n";
		$retstring= $retstring ."</tr>\n";
		}
	}
	if($i!=0)
	$retstring= $retstring ."</table>\n";
	else
	$retstring= $retstring ."<p>none</p>";
	}
	else
	$retstring= $retstring ."<p>none</p>";
	
	//new videos related to your videos
	$query="select * from edges where name = 'Is Related To' AND (fromID IN (select id from videonodes where submittedby='$userid') OR toID IN (select id from videonodes where submittedby='$userid')) AND creationtime > '$LRT'";
	$rs2=mysql_query($query);
	$retstring= $retstring ."<h2>New Videos Related to Your Videos</h2>\n";//error_log($retstring);
	if(mysql_num_rows($rs2)!=0)
	{
	$retstring= $retstring ." <ol>\n";
	while($row2 = mysql_fetch_array($rs2))
	{
		echo('here!');
		//if($row2['fromID']==$userid)
		//	$name=mysql_query("select name from videonodes where id='".$row2['toID']."'");
		//else
			$name=mysql_query("select name from videonodes where (id='".$row2['fromID']."' OR id='".$row2['toID']."') AND submittedby!='$userid'");
		$name_related = mysql_fetch_array($name);
		if($name_related!='')
		$retstring= $retstring ." <li>".$name_related['name']."</li>\n";
	}
	$retstring= $retstring ." </ol>\n";
	}
	else
	$retstring= $retstring ."<p>none</p>";
	
	//your profile
	$i=0;
	$retstring= $retstring ."<h2>Your Profile</h2>\n";
	$retstring= $retstring ."<table border=\"1\">\n<tr>\n<th># Views Total</th>\n<th># Views New</th>\n<th># Comments Total</th>\n<th># Comments New</th>\n<th>Your Game Score</th>\n<th>Best Game Score</th>\n</tr>\n<tr>\n";
	$query="select * from useractions where TakenOn='$userid' AND action='ProfileView'";
	$totalrows=mysql_query($query);
	$totalno=mysql_num_rows($totalrows);
		$retstring= $retstring ." <td>".$totalno."</td>\n";
		$query="select * from useractions where TakenOn='$userid' AND action='ProfileView' AND Datetime > '$LRT'";
		$newrows=mysql_query($query);
		$newno=mysql_num_rows($newrows);
		$retstring= $retstring ." <td>".$newno."</td>\n";
	$query="select * from scrapstable where userid='$userid'";
	$totalrows=mysql_query($query);
	$totalno=mysql_num_rows($totalrows);
		$retstring= $retstring ." <td>".$totalno."</td>\n";
		$query="select * from scrapstable where userid='$userid' AND datetime > '$LRT'";
		$newrows=mysql_query($query);
		$newno=mysql_num_rows($newrows);
		$retstring= $retstring ." <td>".$newno."</td>\n";
	$retstring= $retstring ." <td>".$rowu['grandscore']."</td>\n";
	$best=mysql_query("select * from peoplenodes ORDER BY grandscore DESC");
	$bestscore = mysql_fetch_array($best);
	$retstring= $retstring ." <td>".$bestscore['grandscore']."</td>\n";
	$retstring= $retstring ."</tr>\n";//error_log($retstring);
	$retstring= $retstring ."</table>\n";
	//name of people who connected to you
	$query="select * from edges where name = 'Knows' AND (fromID='$userid' OR toID='$userid') AND creationtime > '$LRT'";
	$rs2=mysql_query($query);
	$retstring= $retstring ."<h2>New people who have connected to you</h2>\n";//error_log($retstring);
	if(mysql_num_rows($rs2)!=0)
	{
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
	}
	else
	$retstring= $retstring ."<p>none</p>";
	//your ongoing discussions
	$retstring= $retstring ."<h2>New Responses to Your Ongoing Discussions</h2>\n";
		$query="select * from topicmessages where userid='$userid'";
		$rs3=mysql_query($query);
	if(mysql_num_rows($rs3)!=0)
	{
		while($row3 = mysql_fetch_array($rs3))
	{
		
		$query=mysql_query("select * from topics where topicid='".$row3['topicid']."'");
		$name_topic = mysql_fetch_array($query);
		
	$query=mysql_query("select * from topicmessages where topicid='".$row3['topicid']."' AND time > '$LRT'");
	$totalno=mysql_num_rows($query);
	if($totalno!=0)
	{
		$i=$i+1;
		if($i==1)
		$retstring= $retstring ."<table border=\"1\">\n<tr>\n<th>Your Ongoing Discussions</th>\n<th>#New Responses</th>\n</tr>\n";
		$retstring= $retstring ." <tr>\n";
		$retstring= $retstring ." <td>".$name_topic['name']."</td>\n";
		$retstring= $retstring ." <td>".$totalno."</td>\n";
		$retstring= $retstring ." </tr>\n";
		}
	}
	if($i!=0)
	$retstring= $retstring ."</table>\n";
	else
	$retstring= $retstring ."<p>none</p>";
	}
	else
	$retstring= $retstring ."<p>none</p>";
	
	//videos you have commented
	$i=0;
	$query="select * from commentstable where authorid='$userid' group by videoid";
	$retstring= $retstring ."<h2>New Responses to Videos You Have Commented</h2>\n";
	$rs4=mysql_query($query);
	if(mysql_num_rows($rs4)!=0)
	{
		while($row4 = mysql_fetch_array($rs4))
	{
		
		$query=mysql_query("select * from videonodes where id='".$row4['videoid']."'");
		//error_log($row4['videoid']);
		$name_video = mysql_fetch_array($query);
		if($name_video['name']=='')
		{
		continue;
		}
		
		//error_log($name_video['name']);
		$query=mysql_query("select * from commentstable where videoid='".$row4['videoid']."' AND datetime > '$LRT'");
		$totalno=mysql_num_rows($query);
		if($totalno!=0)
		{
		$i=$i+1;
		if($i==1)
		$retstring= $retstring ."<table border=\"1\">\n<tr>\n<th>Videos You have Commented</th>\n<th>#New Comments</th>\n</tr>\n";
		$retstring= $retstring ." <tr>\n";
		$retstring= $retstring ." <td>".$name_video['name']."</td>\n";
		$retstring= $retstring ." <td>".$totalno."</td>\n";
		$retstring= $retstring ."</tr>\n";
		}
	}
	if($i!=0)
	$retstring= $retstring ."</table>\n";
	else
	$retstring= $retstring ."<p>none</p>";
	}
	else
	$retstring= $retstring ."<p>none</p>";

	//changes to your groups
	$i=0;
	$query="select * from grpmaindata where author='$userid'";
	$retstring= $retstring ."<h2>Changes To Your Groups</h2>\n";
	$rs4=mysql_query($query);
	if(mysql_num_rows($rs4)!=0)
	{
		while($row4 = mysql_fetch_array($rs4))
	{
		
		//$query=mysql_query("select * from videonodes where id='".$row4['videoid']."'");
		//error_log($row4['videoid']);
		//$name_video = mysql_fetch_array($query);
		//if($name_video['name']=='')
		//{
		//continue;
		//}
		
		//error_log($name_video['name']);
		//$query=mysql_query("select * from commentstable where videoid='".$row4['videoid']."' AND datetime > '$LRT'");
		//$totalno=mysql_num_rows($query);
		$id=$row4['gid'];
		$memberlist=explode(';',$row4['memberlist']);
		$query="select * from useractions where action like 'Member%' AND TakenOn='$id' AND Datetime > '$LRT' order by Datetime asc";
		$rs5=mysql_query($query);
		$new=0;
		$gone=0;
		while($row5 = mysql_fetch_array($rs5))
		{
		if($row5['action']=='MemberNew')
		{
		$query=mysql_query("select * from useractions where action = 'MemberDeparted' AND TakenOn='$id' AND Datetime > '$LRT'AND TakenBy='".$row5['TakenBy']."'");
		if(mysql_num_rows($query)==0)
		$new=$new +1;
		}
		else if($row5['action']=='MemberDeparted')
		$query=mysql_query("select * from useractions where action = 'MemberNew' AND TakenOn='$id' AND Datetime > '$LRT'AND TakenBy='".$row5['TakenBy']."'");
		if(mysql_num_rows($query)==0)
		$gone=$gone +1;
		;
		
		}
		if($totalno!=0)
		{
		$i=$i+1;
		if($i==1)
		$retstring= $retstring ."<table border=\"1\">\n<tr>\n<th>Group</th>\n<th># Members</th>\n<th># New</th>\n<th># Departures</th>\n</tr>\n";
		$retstring= $retstring ." <tr>\n";
		$retstring= $retstring ." <td>".$row4['name']."</td>\n";
		$retstring= $retstring ." <td>".(sizeof($memberlist))."</td>\n";
		$retstring= $retstring ." <td>".($new)."</td>\n";
		$retstring= $retstring ." <td>".($gone)."</td>\n";
		$retstring= $retstring ."</tr>\n";
		}
	}
	if($i!=0)
	$retstring= $retstring ."</table>\n";
	else
	$retstring= $retstring ."<p>none</p>";
	}
	else
	$retstring= $retstring ."<p>none</p>";
// General details	
	//total no. of accesses
/*
	$query ="select Id from useractions where (Datetime > '$LRT' and action='Login')";    	  
$rs				=	mysql_query ($query);
$no_of_accesses= mysql_num_rows($rs);

$retstring=$retstring ." <h4>Total number of accesses</h4>\n";
$retstring=$retstring ." <p>".$no_of_accesses."  </p>\n";
*/
$retstring= $retstring ."<h2>General Activity</h2>\n";
//no. of new users 
$query ="select name from peoplenodes where jointime > '$LRT'";    	  
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
/*$retstring=$retstring ." <h4>Total number of new users</h4>\n";
$retstring=$retstring ." <p>".$no_of_new_users."  </p>\n";
*/
$retstring=$retstring ." <h4>New Members</h4>\n";
if($nameStr!='')
$retstring=$retstring ." <p>".$nameStr."  </p>\n";
else
$retstring=$retstring ." <p>none</p>\n";

//no. of new videos
$query ="select name from videonodes where submittime > '$LRT'";    	  
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
/*$retstring=$retstring ." <h4>Number of new videos</h4>\n";
$retstring=$retstring ." <p>".$no_of_new_videos."  </p>\n";

*/
$retstring=$retstring ." <h4>New Videos</h4>\n";
if($nameStr!='')
$retstring=$retstring ." <p>".$nameStr."  </p>\n";
else
$retstring=$retstring ." <p>none</p>\n";

//no. of times game was played
/*
$query ="select gameid from g3_gamelog where datetime >'$LRT'";    	  
$rs				=	mysql_query ($query);
$no_of_times_game_played= mysql_num_rows($rs);
$retstring=$retstring ." <h4>Number of times the game was played</h4>\n";
$retstring=$retstring ." <p>".$no_of_times_game_played."  </p>\n";
//no. of new connections created 
$query ="select id from edges where ( creationtime > '$LRT' and name='Knows')";    	  
$rs				=	mysql_query ($query);
$no_of_new_connections= mysql_num_rows($rs);
$retstring=$retstring ." <h4>Number of new connections</h4>\n";
$retstring=$retstring ." <p>".$no_of_new_connections."  </p>\n";
*/
//no. of new comments made 
/*$query ="select * from commentstable where (datetime > '$LRT')";    	  
$rs				=	mysql_query ($query);
$no_of_new_comments= mysql_num_rows($rs);
$retstring=$retstring ." <h4>Number of new comments</h4>\n";
$retstring=$retstring ." <p>".$no_of_new_comments."  </p>\n";
*/
//no. of different videos seen
/*$query ="select id from videonodes where lastseen >  '$LRT'";    	  
$rs				=	mysql_query ($query);
$no_of_diff_videos_seen= mysql_num_rows($rs);
$retstring=$retstring ." <h4>Number of different videos seen</h4>\n";
$retstring=$retstring ." <p>".$no_of_diff_videos_seen."  </p>\n";
*/
//total no. of profileviews 
/*
$query ="select Id from useractions where (Datetime > '$LRT' and action='ProfileView')";    	  
$rs				=	mysql_query ($query);
$no_of_profileviews= mysql_num_rows($rs);
$retstring=$retstring ." <h4>Total number of Profile Views</h4>\n";
$retstring=$retstring ." <p>".$no_of_profileviews."  </p>\n";
*/
//new discussion topics
$query="select * from topics where (time > '$LRT')";
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
if($nameStr!='')
$retstring=$retstring ." <p>".$nameStr."  </p>\n";
else
$retstring=$retstring ." <p>none</p>\n";

//new tags
$query="select * from tagnodes where (submittime > '$LRT')";
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
if($nameStr!='')
$retstring=$retstring ." <p>".$nameStr."  </p>\n";
else
$retstring=$retstring ." <p>none</p>\n";

//new interests
$query="select * from controlpanel where (submittime > '$LRT')";
$rs				=	mysql_query ($query);
$nameStr="";
while ($row = mysql_fetch_array($rs))
{
    if($nameStr=="")
    {
       $nameStr=$row['interests'];
    } 
    else
	{
	  $nameStr=$nameStr."</p>".$row['interests']."<p>";
	}
}
$retstring=$retstring ." <h4>New Interests</h4>\n";
if($nameStr!='')
$retstring=$retstring ." <p>".$nameStr."  </p>\n";
else
$retstring=$retstring ." <p>none</p>\n";
 $note="\nNote: For modifying the frequency with which the reports are sent to you  please login to ".$tubename.". Click on \"change settings\" in your profile to change the Report Frequency.";
 $message = "<html>\n <body>\n".$retstring .$note."</html>\n</body>\n";
$whole=$whole.$message;
 echo $message;





//sending mail code 

$to      = $emailid;
$freq="";
if($period=='')
{
switch($RF)
{
case -1 :
$freq="Daily";
break;
case -7 :
$freq="Weekly";
break;
case -15 :
$freq="BiWeekly";
break;
case -30 :
$freq="Monthly";
break;
}
}
$date = new DateTime($LRT);
$subject = $freq.' '.$tubename.' Activity Report (from '.$date->format("d-m-Y H:i:s").' to '.date('d-m-Y H:i:s').')';

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

$headers .= 'From: CALTTubes@insead.edu' . "\r\n" .
    'Reply-To: CALTTubes@insead.edu' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

//if( $userid=="sahaj.agarwal@insead.edu")
	$val=mail($to, $subject, $message, $headers);

if($val==true)
  echo "Success";
else if($val==false)
  echo "Failure"; 

}



$filename='D:\\prototyping\\scheduler\\reports\\'.$tubename.'\\'.$tubename.'_'.date('d-m-Y H.i.s').'.html';
$file=fopen($filename,"w");
fputs($file,$whole);
fclose($file);
?>

