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

header("Cache-control: private");
//header("Content-type: text/html;charset=UTF-8");
header("Content-type: text/xml");

$userid = isset($_REQUEST['userid']) ? $_REQUEST['userid'] : 'sahajag@gmail.com';
$query=mysql_query("select * from peoplenodes where id='$userid'");
$row=mysql_fetch_array($query);
$competences=explode(",",$row['competences']);
$interests=explode(",",$row['interests']);

//All those videos which user has not seen
$query=mysql_query("select * from videonodes where video_py='#EVERYBODY#' and submittedby!='$userid'");
$i=0;
while($row=mysql_fetch_array($query))
{
	$authors=explode(",",$row['authors']);
	$present=0;
	foreach ( $authors as $author)
	{
		if($author==$userid)
		{
			$present=1;
		}
	}	
	if($present==0)
	{
		$queryedges=mysql_query("SELECT * FROM edges where name='Has seen' and fromID='$userid' and toID='".$row['id']."'");
		if(mysql_num_rows($queryedges)==0)
		{
			$filterlist[$i]=$row;
			$i=$i+1;
		}
	}
}
$p1=0;
$p2=0;
$p3=0;
$p4=0;
$p5=0;
$i=0;
//Matching Video priorities
foreach($filterlist as $video)
	{
	//prority 1 $ 4
	$inp1=0;
	$inp4=0;
	$selectedvideos[$i] = 0;
	$video_score[$i] = 0;
	$filtertags=explode(",",$video['tags']);
	$query=mysql_query("select * from videonodes where submittedby='$userid'");
	while($row=mysql_fetch_array($query))
	{
		$queryedges=mysql_query("select * from edges where name='Is Related To' and ((fromID='".$row['id']."' and toID='".$video['id']."') or (toID='".$row['id']."' and fromID='".$video['id']."'))");
		if(mysql_num_rows($queryedges)!=0)
		{
		$inp1=1;
		continue;
		}
		$tags=explode(",",$row['tags']);
		
		$tagmatch=0;
		foreach($tags as $tag)
		{
			foreach($filtertags as $filtertag)
			{
				if($filtertag==$tag)
				{
					$tagmatch=1;
					continue;
				}
			}
		}
		if($tagmatch==1)
		{
		$inp4=1;
		}
	}
	if($inp1==1)
	{
		$selectedvideos[$i]=$selectedvideos[$i]+5;
		$video_score[$i]=$video_score[$i]+5;
	}
	if($inp4==1)
	{
		$selectedvideos[$i]=$selectedvideos[$i]+2;
		$video_score[$i]=$video_score[$i]+2;
	}

	//priority 2
	$inp2=0;
	$queryedges=mysql_query("select * from edges where name='Knows' and (fromID='$userid' or toID='$userid')");
	while($row=mysql_fetch_array($queryedges))
	{
		if($row['fromID']==$userid)
		{
			if($video['submittedby']==$row['toID'])
			{
				$inp2=1;
				continue;
			}
		}
		else
		{
			if($video['submittedby']==$row['fromID'])
			{
				$inp2=1;
				continue;
			}
		}
	}
	if($inp2==1)
	{
	
	$selectedvideos[$i]=$selectedvideos[$i]+4;
	$video_score[$i]=$video_score[$i]+4;
	}
	//priority 3
	$inp3=0;
	$queryedges=mysql_query("select * from edges where name ='Has seen' and fromID='$userid'");
	while($row=mysql_fetch_array($queryedges))
	{
		//3-1
		if($video['previousversion']==$row['toID'])
		{
			$inp3=1;
			continue;
		}
		//3-2
		$queryedgesagain=mysql_query("select * from edges where name='Is Related To' and ((fromID='".$row['toID']."' and toID='".$video['id']."') or (toID='".$row['toID']."' and fromID='".$video['id']."'))");
		if(mysql_num_rows($queryedgesagain)!=0)
		{
			$inp3=1;
			continue;
		}
		//3-3
		$queryedgesagain=mysql_query("select * from videonodes where id='".$row['toID']."'");
		$rowagain=mysql_fetch_array($queryedgesagain);
		if($video['submittedby']==$rowagain['submittedby'])
		{
			$inp3=1;
			continue;
		}
		//3-4
		$authors=explode(",",$rowagain['authors']);
		$present=0;
		foreach ( $authors as $author)
		{
			if($author==$video['submittedby'])
			{
				$present=1;
				continue;
			}
		}	
		if($present==1)
		{
			$inp3=1;
			continue;
		}
	}
	if($inp3==1)
	{
	
	$selectedvideos[$i]=$selectedvideos[$i]+3;
	$video_score[$i]=$video_score[$i]+3;
	}
	//priority 5
	$inp5=0;
	$titles=explode(" ",$video['name']);
	$tags=explode(",",$video['tags']);
	$descriptions=explode(" ",$video['description']);
	foreach($competences as $competence)
	{
		foreach($titles as $title)
		{
			if($title==$competence)
			{
				$inp5=1;
				continue;
			}
			if($tags==$competence)
			{
				$inp5=1;
				continue;
			}
			if($descriptions==$competence)
			{
				$inp5=1;
				continue;
			}
		}
		
	if($inp5==1)
	continue;
	}
	if($inp5!=1)
	{
	foreach($interests as $interest)
	{
		foreach($titles as $title)
		{
			if($title==$interest)
			{
				$inp5=1;
				continue;
			}
			if($tags==$interest)
			{
				$inp5=1;
				continue;
			}
			if($descriptions==$interest)
			{
				$inp5=1;
				continue;
			}
		}
	if($inp5==1)
		continue;
	}
	}
	if($inp5==1)
	{
		$selectedvideos[$i]=$selectedvideos[$i]+1;
		$video_score[$i]=$video_score[$i]+1;
	}
	
		$vid_name[$i] = $video['id'];
		$vid_netscore[$i] = $video_score[$i]*$video['grandrating'];
	
	$i=$i+1;
	
	}//end foreach
	
	sort_rec($vid_netscore,$vid_name);
	echo "<Result>";
	
	
	
	$j=0;
	for($i=5;$i<count($vid_name);$i++)
	{
	$query=mysql_query("select * from videonodes where id='$vid_name[$i]'");
	$row=mysql_fetch_array($query);
	if($row['grandrating'] >=3)
	{
		$new_subset[$j] = $vid_name[$i];
	}
	}
	shuffle($new_subset);
	//echo("<br />the_toprecommend vids are".$new_subset[0]);
	
	
	//echo "<video name=\"" . $new_subset[0] . "\" />";
	
	$retstring = '';
	for($i=0;$i<5;$i++)
	{
		$query=mysql_query("select * from videonodes where id='$vid_name[$i]'");
		$row = mysql_fetch_array($query);
		$retstring = $retstring . "<Video id = \"$row[id]\" name = \"$row[name]\"  category = \"$row[category]\" nodetype= \"$row[nodetype]\" comments = \"$row[comments]\"  tags = \"$row[tags]\"  grandrating = \"$row[grandrating]\"  submittedby = \"$row[submittedby]\"   submitttime = \"$row[submittime]\"   picture = \"$row[picture]\"   authors = \"$row[authors]\" versionnum = \"$row[versionnum]\"
		parentvideo=\"$row[parentvideo]\" previousversion = \"$row[previousversion]\" nextversion = \"$row[nextversion]\" inspiredby = \"$row[inspiredby]\"   timesseen = \"$row[timesseen]\"    lastsseen = \"$row[lastseen]\"   timesrated = \"$row[timesrated]\"   timesplayed = \"$row[timesplayed]\"   source = \"$row[source]\"   externalLinks = \"$row[externalLinks]\"   docLinks = \"$row[docLinks]\"  description = \"$row[description]\"  />\n";	
	}
	
	echo "<TopRecommendedVideos>";
	$muery=mysql_query("select * from videonodes where id='$new_subset[0]'");
	$row = mysql_fetch_array($muery);
	$retstring = $retstring . "<Video id = \"$row[id]\" name = \"$row[name]\"  category = \"$row[category]\" nodetype= \"$row[nodetype]\" comments = \"$row[comments]\"  tags = \"$row[tags]\"  grandrating = \"$row[grandrating]\"  submittedby = \"$row[submittedby]\"   submitttime = \"$row[submittime]\"   picture = \"$row[picture]\"   authors = \"$row[authors]\" versionnum = \"$row[versionnum]\"
	parentvideo=\"$row[parentvideo]\" previousversion = \"$row[previousversion]\" nextversion = \"$row[nextversion]\" inspiredby = \"$row[inspiredby]\"   timesseen = \"$row[timesseen]\"    lastsseen = \"$row[lastseen]\"   timesrated = \"$row[timesrated]\"   timesplayed = \"$row[timesplayed]\"   source = \"$row[source]\"   externalLinks = \"$row[externalLinks]\"   docLinks = \"$row[docLinks]\"  description = \"$row[description]\"  />\n";	
	
	$splcharacters = array("&","'");
	$retstring_without_splcharacters = str_replace($splcharacters, "",$retstring);
	echo $retstring_without_splcharacters."</TopRecommendedVideos>"."</Result>";
	//echo "</TopRecommendedVideos>";
	//echo "</Result>";
	
	?>
	<?php

//bubble sort of two arrays
function sort_rec( array $a , array $b)
   { 
    for ($i = 1; $i<=count($a); $i++)
      {
        for ($j = 0; $j<$i-1; $j++)
			{
				//echo "\n" . $i." " .$j. " " .$a[$j]." " . $b[$j]." ".$a[$j+1]." ".$b[$j+1] ;
				//if ($a[$j+1] == null|| $a[$j] == null)
				//continue;
				//else($a[$j] < $a[$j+1]);
				if($a[$j] < $a[$j+1])
				 {
					swap( $a[$j], $a[$j+1] );
					swap( $b[$j], $b[$j+1] );
				}
			}
			
		}
	}
	function swap($a,$b)
{
	$temp = $a;
	$a = $b ;
	$b= $temp;
}
	
	
	?>

