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

$retstring= $retstring ." <WeeklyReport>";



//total no. of accesses
$query ="select Id from useractions where (Datetime > TIMESTAMPADD(DAY,-7,now()) and action='Login')";    	  
$rs				=	mysql_query ($query);
$no_of_accesses= mysql_num_rows($rs);

$retstring=$retstring ." <TotalNoOfAccesses>".$no_of_accesses."  </TotalNoOfAccesses>";

//no. of new users 
$query ="select name from peoplenodes where jointime > TIMESTAMPADD(DAY,-7,now())";    	  
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
$query ="select name from videonodes where submittime > TIMESTAMPADD(DAY,-7,now())";    	  
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
$query ="select gameid from g3_gamelog where datetime >TIMESTAMPADD(DAY,-7,now())";    	  
$rs				=	mysql_query ($query);
$no_of_times_game_played= mysql_num_rows($rs);
$retstring=$retstring ." <NoOfTimesGamePlayed>".$no_of_times_game_played."  </NoOfTimesGamePlayed>";
//no. of new connections created 
$query ="select id from edges where ( creationtime > TIMESTAMPADD(DAY,-7,now()) and name='Knows')";    	  
$rs				=	mysql_query ($query);
$no_of_new_connections= mysql_num_rows($rs);
$retstring=$retstring ." <NoOfNewConnections>".$no_of_new_connections."  </NoOfNewConnections>";
//no. of new comments made 

$query ="select * from commentstable where (datetime > TIMESTAMPADD(DAY,-7,now()))";    	  
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
	$query1 ="select Id from useractions where (Datetime > TIMESTAMPADD(DAY,-7,now()) and action='Login' and TakenBy='$person')";    	  
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
$query ="select id from videonodes where lastseen >  TIMESTAMPADD(DAY,-7,now())";    	  
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
	$query1 ="select Id from useractions where (Datetime > TIMESTAMPADD(DAY,-7,now()) and action='WatchVideo' and TakenOn='$videoId')";    	  
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


$retstring=$retstring." </WeeklyReport>";
echo $retstring;
	
?>