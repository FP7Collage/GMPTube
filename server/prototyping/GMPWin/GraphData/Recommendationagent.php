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
//	session_start();
	header("Cache-control: private");
	header("Content-type: text/xml");
	
	include "conntube.php";
	//    The case for recomemnded videos and recomennded people
	// calculate the details of the logged user
	$userid = isset($_REQUEST['guid']) ? $_REQUEST['guid'] : 'pkmittal82@gmail.com';
	
	
	error_log($full_tubename . " calling reco agent " .$userid );
	
	$relatedName = "Is Connected To";
	
	
$query=mysql_query("select * from peoplenodes where id='$userid'");
$row=mysql_fetch_array($query);
$competences=explode(",",$row['competences']);
$interests=explode(",",$row['interests']);


//All those videos which user has not submitted and also is not the author of the video and also not seen
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

//var_dump($filterlist);
//error_log(print_r($filterlist, true));

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
		
		$queryedges=mysql_query("select * from edges where name='$relatedName' and ((fromID='".$row['id']."' and toID='".$video['id']."') or (toID='".$row['id']."' and fromID='".$video['id']."'))");
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
		$queryedgesagain=mysql_query("select * from edges where name='$relatedName' and ((fromID='".$row['toID']."' and toID='".$video['id']."') or (toID='".$row['toID']."' and fromID='".$video['id']."'))");
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
	$new_subset[$j] = $vid_name[$i];
/*	if($row['grandrating'] >=3)
	{
		$new_subset[$j] = $vid_name[$i];
	}
	*/
	$j++;
	}
	shuffle($new_subset);
//	error_log(print_r($new_subset, true));
	
	// first 5 videos
	$retstring = '';
	for($i=0;$i<5;$i++)
	{
		$query=mysql_query("select * from videonodes where id='$vid_name[$i]'");
		$row = mysql_fetch_array($query);
		$retstring = $retstring . "<Video id = \"$row[id]\" name = \"$row[name]\"  category = \"$row[category]\" nodetype= \"$row[nodetype]\" comments = \"$row[comments]\"  tags = \"$row[tags]\"  grandrating = \"$row[grandrating]\"  submittedby = \"$row[submittedby]\"   submitttime = \"$row[submittime]\"   picture = \"$row[picture]\"   authors = \"$row[authors]\" versionnum = \"$row[versionnum]\"
		parentvideo=\"$row[parentvideo]\" previousversion = \"$row[previousversion]\" nextversion = \"$row[nextversion]\" inspiredby = \"$row[inspiredby]\"   timesseen = \"$row[timesseen]\"    lastsseen = \"$row[lastseen]\"   timesrated = \"$row[timesrated]\"   timesplayed = \"$row[timesplayed]\"   source = \"$row[source]\"   externalLinks = \"$row[externalLinks]\"   docLinks = \"$row[docLinks]\"  description = \"$row[description]\"  />\n";	
	}
	
	echo "<TopRecommendedVideos>";
	
	/*
	// 6th video
	$muery=mysql_query("select * from videonodes where id='$new_subset[0]'");	
	$row = mysql_fetch_array($muery);	
	$retstring = $retstring . "<Video id = \"$row[id]\" name = \"$row[name]\"  category = \"$row[category]\" nodetype= \"$row[nodetype]\" comments = \"$row[comments]\"  tags = \"$row[tags]\"  grandrating = \"$row[grandrating]\"  submittedby = \"$row[submittedby]\"   submitttime = \"$row[submittime]\"   picture = \"$row[picture]\"   authors = \"$row[authors]\" versionnum = \"$row[versionnum]\"
	parentvideo=\"$row[parentvideo]\" previousversion = \"$row[previousversion]\" nextversion = \"$row[nextversion]\" inspiredby = \"$row[inspiredby]\"   timesseen = \"$row[timesseen]\"    lastsseen = \"$row[lastseen]\"   timesrated = \"$row[timesrated]\"   timesplayed = \"$row[timesplayed]\"   source = \"$row[source]\"   externalLinks = \"$row[externalLinks]\"   docLinks = \"$row[docLinks]\"  description = \"$row[description]\"  />\n";	
	
*/
        
		$str = "select * from videonodes where userType='FA'";
		$rs=mysql_query($str);
        $i=0;		
		while ($row = mysql_fetch_array($rs)){
		
		if (in_array($row['id'], $new_subset)) {
		 if($i>=3){
		   break;
		 }
		 $i=$i+1;
		
		$retstring = $retstring . "<Video id = \"$row[id]\" name = \"$row[name]\"  category = \"$row[category]\" nodetype= \"$row[nodetype]\" comments = \"$row[comments]\"  tags = \"$row[tags]\"  grandrating = \"$row[grandrating]\"  submittedby = \"$row[submittedby]\"   submitttime = \"$row[submittime]\"   picture = \"$row[picture]\"   authors = \"$row[authors]\" versionnum = \"$row[versionnum]\"
		parentvideo=\"$row[parentvideo]\" previousversion = \"$row[previousversion]\" nextversion = \"$row[nextversion]\" inspiredby = \"$row[inspiredby]\"   timesseen = \"$row[timesseen]\"    lastsseen = \"$row[lastseen]\"   timesrated = \"$row[timesrated]\"   timesplayed = \"$row[timesplayed]\"   source = \"$row[source]\"   externalLinks = \"$row[externalLinks]\"   docLinks = \"$row[docLinks]\"  description = \"$row[description]\"  />\n";	
			
		}
		
		
		}


		
		
			

	
	$splcharacters = array("&","'");
	$retstring_without_splcharacters = str_replace($splcharacters, "",$retstring);
	
	echo $retstring_without_splcharacters."</TopRecommendedVideos>";
	
	
	Recopeople($userid);
	
	?>
	<?php
	
	function Recopeople($userid)
	{
	$query=mysql_query("select * from peoplenodes where id='$userid'");
	$row=mysql_fetch_array($query);
		$knowi = array();
		$competencesi = array();
		$competencesj = array();
		$interestsi = array();
		$interestsj = array();
		$viewarrayi = array();
		$tagsi = array();
	// calculate the competences of the logged user
	if($row['competences']=='')
	{
		$nci=0;
	}else
	{
		$competencesi=explode(",",$row['competences']);
		$nci=count($competencesi);

	}
	// calculate the interests of the logged user
	if($row['interests']=='')
	{
		$nii=0;
	}
	else
	{
		$interestsi=explode(",",$row['interests']);
		$nii=count($interestsi);
	}
	
	// calculate the Has seen relationship of the logged user
	$query=mysql_query("select * from edges where name='Has seen' and fromID='$userid'");
	$i=0;
	while($row=mysql_fetch_array($query))
	{
		//array_push($viewarrayi,$row['toID']);
		$viewarrayi[$i]=$row['toID'];
		$i=$i+1;
	}
	$nvi=count($viewarrayi);
	
	// calculate the tags of videos submitted by the logged user
	$query=mysql_query("select * from videonodes where submittedby='$userid'");
	$i=0;
	while($row=mysql_fetch_array($query))
	{
		if($row['tags']=='')
		{
			continue;
		}
		else
		{
			$tags=explode(',',$row['tags']);
		}
		foreach ($tags as $tag)
		{
			//array_push($tagsi,$tag);
			$tagsi[$i]=$tag;
			$i=$i+1;
		}
	}
	array_unique($tagsi);
	$nti=count($tagsi);

$query=mysql_query("select * from edges where name='Knows' and fromID='$userid'");
$i=0;
while($row=mysql_fetch_array($query))
{
if($row['intensity']> 3 )
{
//array_push($knowi,$row['toID']);
$knowi[$i]=$row['toID'];
$i=$i+1;
}
}
// calculate the people known  by the logged user
$npi=count($knowi);

$j = 0;
$rec1 = 0;
$rec2 = 0; 
$rec3= 0;
$rec4 = 0;
$rec5 = 0;
$ci =  0;
$pi = 0;
$ti = 0;
$vi = 0;
$ii = 0;
$unique_subset = array();
$rec1_array  = array();
 $rec2_array= array();
 $rec3_array  = array();
 $rec4_array  = array();
 $tagsj = array();
 $rec5_array  = array();
 $knowj = array();
 
 $viewarrayj = array();
 $si = array();
 $rec1_simscore = array();
  $rec2_simscore = array();
   $rec3_simscore = array();
    $rec4_simscore = array();
	 $rec5_simscore = array();
	 $recommended = array();
	 $filterlist = array();
	 $tilterlist = array();
//error_log("User Id" . $userid);
$j = 0;
$f=0;
$t=0;
for ($k=0; $k<5; $k++)
{
$rec1_array[$k] = null;
$rec2_array[$k] = null;
$rec3_array[$k] = null;
$rec4_array[$k] = null;
$rec5_array[$k] = null;
}

$juery=mysql_query("select * from edges where name='Knows' and toID='$userid'");

while($how=mysql_fetch_array($juery))
{
	
	$tilterlist[$t]=$how['fromID'];
	$t=$t+1;

}
$juery=mysql_query("select * from edges where name='Knows' and fromID='$userid'");
while($how=mysql_fetch_array($juery))
{
	
	$tilterlist[$t]=$how['toID'];
	$t=$t+1;

}
// calculate the details of all the other users
$query=mysql_query("select * from peoplenodes where id not in ( select toID from edges where name ='Knows' and fromID='$userid' ) ");

while($low=mysql_fetch_array($query))
{			$filterlist[$f]=$low;
			$f=$f+1;
}

$query=mysql_query("select * from peoplenodes where id not in ( select fromID from edges where name ='Knows' and toID='$userid' ) ");

while($chow=mysql_fetch_array($query))
{			
			$filterlist[$f]=$chow;
			$f=$f+1;
}
// one by one calculation of the details of all the other users
foreach($filterlist as $now)
	{
		if ($now['id'] == $userid || in_array($now['id'],$tilterlist))
			{
			
			continue;
			}
		else
		{
			if($now['competences']=='')
			$ncj=0;
			else
			{
				$competencesj=explode(",",$now['competences']);
				$ncj=count($competencesj);
			}

			if($now['interests']=='')
			$nij=0;
			else
			{
				$interestsj=explode(",",$now['interests']);
				$nij=count($interestsj);
			}


			// The second user 
		$userj[$j]  = $now['id'];
		//echo("\;;the_seconduser ".$userj[$j]);
		$unique_subset[$j] = $now['id'];

 
 //// The second user VIEWS
		$muery=mysql_query("select * from edges where name='Has seen' and fromID='$userj[$j]'");
		$i=0;
		while($cow = mysql_fetch_array($muery))
		{
		//array_push($viewarrayj,$row['toID']);
			$viewarrayj[$i]=$cow['toID'];
			$i=$i+1;
		}
		$nvj=count($viewarrayj);


 
		// The second user tags
		$vuery=mysql_query("select * from videonodes where submittedby='$userj[$j]'");
		$i=0;
		while($vow=mysql_fetch_array($vuery))
		{
			if($vow['tags']=='')
				continue;
			else
			$tags=explode(',',$vow['tags']);
			foreach ($tags as $tag)
			{
				$tagsj[$i]=$tag;
				$i=$i+1;
			}
		}
		array_unique($tagsj);
		$ntj=count($tagsj);


	
		// // The second user knows
		$puery=mysql_query("select * from edges where name='Knows' and fromID='$userj[$j]'");
		$i=0;
		if ($puery =='null')
		{
			$knowj[$i++] = '';
			$npj = 0;
		}
		else
		{
		while($pow=mysql_fetch_array($puery))
		{
			if($pow['intensity']> 3 )
			{
				//array_push($knowj,$row['toID']);
				$knowj[$i]=$pow['toID'];
				$i=$i+1;
			}
		}
		$npj=count($knowj);
		}

		// to calculate the number of matches between logged user and the clculatd user
		$wc = 0.2;
		$wi = 0.2;
		$wv = 0.2;
		$wt = 0.2;
		$wp = 0.2;

		$ncij =0;
		foreach($competencesi as $competencei)
		{
			if(in_array($competencei,$competencesj))
			$ncij=$ncij +1; 
		}
		if($ncij>0)
		{$rec1_array[$rec1] = $userj[$j];// to populate the first set of people
		$rec1_simscore[$rec1++] = Calc_similarity($ncij,$nci,$ncj,$wc);
		}

		$niij=0;
		foreach($interestsi as $interesti)
		{
			if(in_array($interesti,$interestsj))
			$niij=$niij +1;
		}
		if($niij>0)
		{$rec2_array[$rec2] = $userj[$j];// to populate the second set of people
		$rec2_simscore[$rec2++] = Calc_similarity($niij,$nii,$nij,$wi);
		}

	// to calculate the number of matches  of similar views between logged user and the clculatd user
	$nvij=0;
	foreach($viewarrayi as $viewi)
	{
		if(in_array($viewi,$viewarrayj))
		$nvij=$nvij +1;
	}
	if($nvij>0)
		{$rec3_array[$rec3] = $userj[$j];// to populate the third set of people
		$rec3_simscore[$rec3++] = Calc_similarity($nvij,$nvi,$nvj,$wv);
		}

	// to calculate the number of matches similar tags between logged user and the clculatd user
		$ntij=0;
		foreach($tagsi as $tagi)
		{
		if(in_array($tagi,$tagsj))
		$ntij=$ntij +1;
		}

		if($ntij>0)
		{$rec4_array[$rec4] = $userj[$j];// to populate the fourth set of people
		$rec4_simscore[$rec4++] = Calc_similarity($ntij,$nti,$ntj,$wt);
		}

		// to calculate the number of knowings matches between logged user and the clculatd user
		$npij=0;
	foreach($knowi as $knoi)
	{
		if(in_array($knoi,$knowj))
		$npij=$npij +1;
	}
		if($npij>0)
	{
	$rec5_array[$rec5] = $userj[$j];// to populate the fifth set of people
	$rec5_simscore[$rec5++] = Calc_similarity($npij,$npi,$npj,$wp);
	}

	
// to calculate the net competences scorelogged user and the clculatd user
	$ci = $nci + $ncj - $ncij;
	  if ($ci!= 0)
		{$ci = $ncij/$ci;
		$ci = $ci*$wc; }

// to calculate the net interests scorelogged user and the clculatd user
	$vi = $nvi + $nvj - $nvij;
	  if ($vi!= 0)
	  {$vi = $nvij/$vi;
	  $vi = $vi*$wv;} 	

// to calculate the net views scorelogged user and the clculatd user
	$ii = $nii + $nij - $niij;
	  if ($ii!= 0)
	  {$ii = $niij/$ii;
	  $ii = $ii*$wi; }


 // to calculate the net tags scorelogged user and the clculatd user
	$ti = $nti + $ntj - $ntij;
	 if ($ti!= 0)
	 {$ti = $ntij/$ti;
	 $ti = $ti*$wt;}

	 
// to calculate the net knows scorelogged user and the clculatd user
	$pi = $npi + $npj - $npij;
	 if ($pi!= 0)
	 {$pi = $npij/$pi;
	 $pi = $pi*$wp;	
	}

// to calculate the net similarity scorelogged user and the clculatd user	
	$si[$j] = $ci+$ti+$vi+$pi+$ii;
	 
	 $si[$j] =  round($si[$j]*100);
	$j = $j+1;
	}//end while }
	}
	
	// to display the net similarity scorelogged user and the clculatd user	
	for($k=0; $k<$j; $k++)
	{
		if ($si[$k] == 0)
			continue;
		//else
		//echo $si[$k] . "<br />";
	}

// to sort the first set of people	 
if(isset($rec1_array) || isset($rec1_simscore))
	sort_rec($rec1_simscore,$rec1_array);
// to populate the second set of people
if(isset($rec2_array) || isset($rec2_simscore))
	sort_rec($rec2_simscore,$rec2_array);
// to populate the third set of people
if(isset($rec3_array) || isset($rec3_simscore))
	sort_rec($rec3_simscore,$rec3_array);
// to populate the fourth set of people
if(isset($rec4_array) || isset($rec4_simscore))
	sort_rec($rec4_simscore,$rec4_array);
// to populate the fifth set of people
if(isset($rec5_array) || isset($rec5_simscore))
	sort_rec($rec5_simscore,$rec5_array);

	
	 
// to display the first set of people
if(isset($rec1_array) || isset($rec1_simscore))	
	//echo("<person id=\"" . $rec1_array[0] . "\" />");

// to display the second set of people
if(isset($rec2_array) || isset($rec2_simscore))
	//echo "<person id=\"" . $rec2_array[0] . "\" />";
// to display the third set of people
if(isset($rec3_array) || isset($rec3_simscore))
	//echo "<person id=\"" . $rec3_array[0] . "\" />";
// to display the fourth set of people
if(isset($rec4_array) || isset($rec4_simscore))
	//echo "<person id=\"" . $rec4_array[0] . "\" />";
// to display the fifth set of people
if(isset($rec5_array) || isset($rec5_simscore))
	//echo "<person id=\"" . $rec5_array[0] . "\" />";

for ($k=0; $k<5; $k++)
{
	if($rec1_array[$k]!= null)
		$recommended[0][$k] = $rec1_array[$k];
	else
	$recommended[0][$k]= null;
	
	if($rec2_array[$k]!= null)
	$recommended[1][$k] = $rec2_array[$k];
	else
	$recommended[1][$k]= null;
	
	if($rec3_array[$k]!= null)
	$recommended[2][$k] = $rec3_array[$k];
	else
	$recommended[2][$k]= null;
	
	if($rec4_array[$k]!= null)
	$recommended[3][$k] = $rec4_array[$k];
	else
	$recommended[3][$k]= null;
	
	if($rec5_array[$k]!= null)
	$recommended[4][$k] = $rec5_array[$k];
	else
	$recommended[4][$k]= null;
	
}


// to check for duplicates of people and replace set of people
for ($i = 1 ; $i<=5; $i++)
	for ($j = 0 ; $j<4; $j++)
	{
	if( $recommended[$j][0] == $recommended[$j+1][0])
     {
	  if($recommended[$j][1] != null)
      $recommended[$j][0] = $recommended[$j][1];
      else
      $recommended[$j][0] = null;
     }
	}


// to display one randomly out of set  of people
$Top_recommend = array($recommended[0][0], $recommended[1][0], $recommended[2][0], $recommended[3][0], $recommended[4][0]);
shuffle($Top_recommend);



// to display the unique subset in case rendomly selected people set is empty 
	if(count($Top_recommend) == 0)
	{
		shuffle($unique_subset);
	}
	
	$retstring = '<recommendedpeople>';
	$pingstring = '<toprecommendedperson>';
	
	$query=mysql_query("select * from peoplenodes where id='$Top_recommend[0] ' or id='$Top_recommend[1]' or id='$Top_recommend[2]' or id='$Top_recommend[3]' or id='$Top_recommend[4]'");
	while($row=mysql_fetch_array($query))
	{
		$retstring = $retstring . "<people id = \"$row[id]\" name = \"$row[name]\"  nationality = \"$row[nationality]\" nodetype= \"$row[nodetype]\" company = \"$row[company]\"  picture = \"$row[picture]\"  loggedin = \"$row[loggedin]\"  url = \"$row[url]\"   grandscore = \"$row[grandscore]\"   interests = \"$row[interests]\"  emailid = \"$row[emailid]\" jointime = \"$row[jointime]\"
		timesvisited=\"$row[timesvisited]\" invite = \"$row[invite]\" lastaccessed = \"$row[lastaccessed]\" invitedby = \"$row[invitedby]\"   competences = \"$row[competences]\"    name_py= \"$row[name_py]\"   alias = \"$row[alias]\"   RF = \"$row[RF]\"     competences_py = \"$row[competences_py]\"   emailid_py = \"$row[emailid_py]\"  picture_py = \"$row[picture_py]\"  />\n";	
	}
	
	for ($i = 0 ; $i<5; $i++)
	{
		if($Top_recommend[$i] == null)
			continue;
		else
		{
			$query=mysql_query("select * from peoplenodes where id='$Top_recommend[$i]'");
			$row=mysql_fetch_array($query);
			$pingstring = $pingstring . "<people id = \"$row[id]\" name = \"$row[name]\"  nationality = \"$row[nationality]\" nodetype= \"$row[nodetype]\" company = \"$row[company]\"  picture = \"$row[picture]\"  loggedin = \"$row[loggedin]\"  url = \"$row[url]\"   grandscore = \"$row[grandscore]\"   interests = \"$row[interests]\"  emailid = \"$row[emailid]\" jointime = \"$row[jointime]\"
			timesvisited=\"$row[timesvisited]\" invite = \"$row[invite]\" lastaccessed = \"$row[lastaccessed]\" invitedby = \"$row[invitedby]\"   competences = \"$row[competences]\"    name_py= \"$row[name_py]\"   alias = \"$row[alias]\"   RF = \"$row[RF]\"     competences_py = \"$row[competences_py]\"   emailid_py = \"$row[emailid_py]\"  picture_py = \"$row[picture_py]\"  />\n";	
			break;
		}
	}
	$pingstring .= '</toprecommendedperson>';
	$splcharacters = array("&","'");
	$pingstring_without_splcharacters = str_replace($splcharacters, "",$pingstring);
	
	
	
	if(count($Top_recommend) == 0)
	{
		$query	=mysql_query("select * from peoplenodes where id='$unique_subset[0]'");
		$row = mysql_fetch_array($query);
		$retstring = $retstring . "<people id = \"$row[id]\" name = \"$row[name]\"  nationality = \"$row[nationality]\" nodetype= \"$row[nodetype]\" company = \"$row[company]\"  picture = \"$row[picture]\"  loggedin = \"$row[loggedin]\"  url = \"$row[url]\"   grandscore = \"$row[grandscore]\"   interests = \"$row[interests]\"  emailid = \"$row[emailid]\" jointime = \"$row[jointime]\"
		timesvisited=\"$row[timesvisited]\" invite = \"$row[invite]\" lastaccessed = \"$row[lastaccessed]\" invitedby = \"$row[invitedby]\"   competences = \"$row[competences]\"    name_py= \"$row[name_py]\"   alias = \"$row[alias]\"   RF = \"$row[RF]\"     competences_py = \"$row[competences_py]\"   emailid_py = \"$row[emailid_py]\"  picture_py = \"$row[picture_py]\"  />\n";	
	}
	
	$retstring .= '</recommendedpeople>';
	
	$splcharacters = array("&","'");
	$retstring_without_splcharacters = str_replace($splcharacters, "",$retstring);
	echo $pingstring_without_splcharacters.$retstring_without_splcharacters."</Result>";
	
	}
	
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
	
	// similarity calculator 
function Calc_similarity ($ncij,  $nci,  $ncj, $wc)
{      $si = 0;
      $si = $nci + $ncj - $ncij;
	  if ($si != 0)
	  {$si = $ncij/$si;
	  $si = $si*$wc; 
       $si =  round($si*100);
	  }
	  else
	  $si = 0;
	   return $si;
}

function swap($a,$b)
{
	$temp = $a;
	$a = $b ;
	$b= $temp;
}
?>