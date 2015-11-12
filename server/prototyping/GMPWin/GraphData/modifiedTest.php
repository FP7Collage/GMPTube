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
header("Content-type: text/xml");
	
include "conntube.php";
$retstring= "<?xml version=\"1.0\" encoding=\"utf-8\"?>";  

$retstring= $retstring ." <DailyReport>";

$period =-1;


//total no. of accesses
$query ="select Id from useractions where (Datetime > TIMESTAMPADD(DAY,'$period',now()) and action='Login')";    	  
$rs				=	mysql_query ($query);
$no_of_accesses= mysql_num_rows($rs);

$retstring=$retstring ." <TotalNoOfAccesses>".$no_of_accesses."  </TotalNoOfAccesses>";

//no. of new users 
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
	  $nameStr=$nameStr."\n".$row['name'];

	}
}
$no_of_new_users= mysql_num_rows($rs);
$retstring=$retstring ." <NoOfNewUsers>".$no_of_new_users."  </NoOfNewUsers>";
$retstring=$retstring ." <NewUsers>".$nameStr."</NewUsers>";
//no. of new videos
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
	  $nameStr=$nameStr."\n".$row['name'];

	}
}

$retstring=$retstring ." <NoOfNewVideos>".$no_of_new_videos."  </NoOfNewVideos>";
$retstring=$retstring ." <NewVideos>".$nameStr."</NewVideos>";
//no. of times game was played
$query ="select gameid from g3_gamelog where datetime >TIMESTAMPADD(DAY,'$period',now())";    	  
$rs				=	mysql_query ($query);
$no_of_times_game_played= mysql_num_rows($rs);
$retstring=$retstring ." <NoOfTimesGamePlayed>".$no_of_times_game_played."  </NoOfTimesGamePlayed>";
//no. of new connections created 
$query ="select id from edges where ( creationtime > TIMESTAMPADD(DAY,'$period',now()) and name='Knows')";    	  
$rs				=	mysql_query ($query);
$no_of_new_connections= mysql_num_rows($rs);
$retstring=$retstring ." <NoOfNewConnections>".$no_of_new_connections."  </NoOfNewConnections>";
//no. of new comments made 

$query ="select * from commentstable where (datetime > TIMESTAMPADD(DAY,'$period',now()))";    	  
$rs				=	mysql_query ($query);
$no_of_new_comments= mysql_num_rows($rs);
$retstring=$retstring ." <NoOfNewComments>".$no_of_new_comments."  </NoOfNewComments>";

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
$top3users = $p1."\n".$p2."\n".$p3;
$retstring=$retstring ." <topThreeUsers>".$top3users."  </topThreeUsers>";
//no. of different videos seen
$query ="select id from videonodes where lastseen >  TIMESTAMPADD(DAY,'$period',now())";    	  
$rs				=	mysql_query ($query);
$no_of_diff_videos_seen= mysql_num_rows($rs);
$retstring=$retstring ." <NoOfDifferentVideosSeen>".$no_of_diff_videos_seen."  </NoOfDifferentVideosSeen>";
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
$top3videos = $p1."\n".$p2."\n".$p3;
$retstring=$retstring ." <topThreeVideos>".$top3videos."  </topThreeVideos>";

//no of times rating of videos had been done
$query ="select Id from useractions where (Datetime > TIMESTAMPADD(DAY,'$period',now()) and action='Rated')";    	  
$rs				=	mysql_query ($query);
$no_of_ratings= mysql_num_rows($rs);

$retstring=$retstring ." <TotalNoOfRatings>".$no_of_ratings."  </TotalNoOfRatings>";

//no of videostarts logged in useracitons
$query ="select Id from useractions where (Datetime > TIMESTAMPADD(DAY,'$period',now()) and action='VideoStart')";    	  
$rs				=	mysql_query ($query);
$no_of_video_starts= mysql_num_rows($rs);

$retstring=$retstring ." <TotalNoOfVideoStarts>".$no_of_video_starts."  </TotalNoOfVideoStarts>";

//no of videoends logged in useracitons
$query ="select Id from useractions where (Datetime > TIMESTAMPADD(DAY,'$period',now()) and action='VideoEnd')";    	  
$rs				=	mysql_query ($query);
$no_of_video_ends= mysql_num_rows($rs);

$retstring=$retstring ." <TotalNoOfVideoEnds>".$no_of_video_ends."  </TotalNoOfVideoEnds>";

//total no. of profileviews 
$query ="select Id from useractions where (Datetime > TIMESTAMPADD(DAY,'$period',now()) and action='ProfileView')";    	  
$rs				=	mysql_query ($query);
$no_of_profileviews= mysql_num_rows($rs);

$retstring=$retstring ." <TotalNoOfProfileViews>".$no_of_profileviews."  </TotalNoOfProfileViews>";

//time spent in channel, profiles,network
$retstring=$retstring ." <accessTimes>";
$ctime=0;
$ntime=0;
$ptime=0;
$stillToWrite=false;

$query = "select id from peoplenodes";
$rs				=	mysql_query ($query);
while ($row = mysql_fetch_array($rs))
{
   if($stillToWrite==true && ($ctime!= 0 || $ptime != 0 || $ntime!= 0))
   {
      $retstring=$retstring . " <Time id = '$person' channelTime='$ctime' networkTime='$ntime' profileTime='$ptime'/>";
      $stillToWrite=false;
   }

   $person=$row['id'];
   
 // $person='paolo.pesatori@alfaromeo.com'; 
   $query1 ="select action,datetime from useractions where (Datetime > TIMESTAMPADD(DAY,'$period',now()) and TakenBy='$person') order by Datetime";    	  
    $rs1	=	mysql_query ($query1);
	
	$currentAction="";
	$currentTime="";
	$ctime=0;
    $ntime=0;
    $ptime=0;
	
	while($row1=mysql_fetch_array($rs1)) 
	{
	  $nextAction = $row1['action'];
	  $nextTime=$row1['datetime'];  
	  switch ($nextAction)
     {
	    // as soon as the user logs in then he is automatically put in channel space
		//and till user presses any other application bar tab buttons he will be in channel.
		case 'Login':
		   $currentTime=$nextTime;
		   $currentAction=$nextAction;
		  if($ctime!=0)
          {
               //this means the user has not press the logout button.but closed the browser directly. 
		       //case will be written later
			$retstring=$retstring . " <'$person' '$ctime'  '$ntime' '$ptime'/>";
            $stillToWrite=false;
			$ctime=0;
            $ptime=0;
            $ntime=0;
		  }		  
		  break;
		  
		case 'Network':
		   if($currentAction=='Login' ||$currentAction=='Channel')
		   {
		      $ctime=$ctime+(strtotime("$nextTime")-strtotime("$currentTime"))/60;
		   }
		   else if($currentAction=='Profiles')
		   {
		      $ptime=$ptime+(strtotime("$nextTime")-strtotime("$currentTime"))/60; 
		   }
		   $stillToWrite=true;
	       $currentTime=$nextTime;
		   $currentAction=$nextAction;	   
           break;
		   
        case 'Profiles':
      		if($currentAction=='Login' ||$currentAction=='Channel')
		   {
		      $ctime=$ctime+(strtotime("$nextTime")-strtotime("$currentTime"))/60;
		   }
		   else if($currentAction=='Network')
		   {
		      $ntime=$ntime+(strtotime("$nextTime")-strtotime("$currentTime"))/60; 
		   }
		   $stillToWrite=true;
           $currentTime=$nextTime;
		   $currentAction=$nextAction;		  
	       break;
		   
		case 'Channel':
		   if($currentAction=='Profiles')
		   {
		      $ptime=$ptime+(strtotime("$nextTime")-strtotime("$currentTime"))/60;
		   }
		   else if($currentAction=='Network')
		   {
		      $ntime=$ntime+(strtotime("$nextTime")-strtotime("$currentTime"))/60; 
		   }
		   $stillToWrite=true;
	       $currentTime=$nextTime;
		   $currentAction=$nextAction;		  
	       break;
		   
		case 'Logout':
		   if($currentAction=='Login' || $currentAction=='Channel')
		   {
		      $ctime=$ctime+(strtotime("$nextTime")-strtotime("$currentTime"))/60;
		   }
		   else if($currentAction=='Profiles')
		   {
		      $ptime=$ptime+(strtotime("$nextTime")-strtotime("$currentTime"))/60;
		   }
		   else if($currentAction=='Network')
		   {
		      $ntime=$ntime+(strtotime("$nextTime")-strtotime("$currentTime"))/60; 
		   }
		   
		   $retstring=$retstring . " <'$person' '$ctime' '$ntime' '$ptime'/>";
	       $stillToWrite=false;
		   $currentTime="";
		   $currentAction="";
           $ctime=0;
           $ptime=0;
           $ntime=0;		   
	       break;	
	    default:
		 //   $retstring=$retstring."<nextAction>".$nextAction."</nextAction>";
		   break;
	
	  }
	  
	}
}
if($stillToWrite==true && ($ctime!= 0 || $ptime != 0 || $ntime!= 0))
{
      $retstring=$retstring . " <'$person'  '$ctime' '$ntime' '$ptime'/>";
      $stillToWrite=false;
}	
	
$retstring=$retstring ." </accessTimes>";
//end of code for timespent
$retstring=$retstring." </DailyReport>";
echo $retstring;
	
?>