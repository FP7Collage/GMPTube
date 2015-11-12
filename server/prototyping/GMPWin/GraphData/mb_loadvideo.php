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
		
	$userid = "";
	$loginId = "";
	$retstring .= "<rsp>";
	$retstring .=  "<Video>";
	
	$vid = $_REQUEST['vid'];
    $isAdmin = true;
	
	
	
// load the video nodes
$rs = mysql_query("SELECT * FROM videonodes where id='". $vid ."'");
while ($row = mysql_fetch_array($rs))
{
	$tagg='';
	$rs1 = mysql_query("Select * from edges where fromID='$row[id]' and name='Has tags' ");
	while($row1=mysql_fetch_array($rs1))
	{
		$rs2=mysql_query("Select * from tagnodes where id='$row1[toID]' ");
		$row2=mysql_fetch_array($rs2);
		$tagg = $tagg.$row2['name'].",";
	}
	if($row['parentvideo'] != '' and !$isAdmin) {
		$parvid = mysql_query("SELECT submittedby, video_py FROM videonodes WHERE id='$row[parentvideo]'");
		$arr = mysql_fetch_array($parvid);
		if($arr['submittedby'] != $loginId) {
			if($arr['video_py'] == '#AUTHORONLY#')
				$row['parentvideo'] == '';
			if(strncmp($arr['video_py'], "#GROUPS#", 8) == 0) {
				//syntax: #GROUPS#gid1,gid2,gid3
				$grps = explode('#', $arr['video_py']);
				$grplist = "'" . str_replace(',', "','", $grps[2]) . "'";
				$mlists = mysql_query("SELECT memberlist FROM grpmaindata WHERE gid in ($grplist)");
				$memberstr = '';
				while($ml = mysql_fetch_array($mlists)) {
					$memberstr .= $ml['memberlist'];
				}
				if(!strpos($memberstr, $loginId))
					$row['parentvideo'] == '';
			}
		}
	}
	if($row['previousversion'] != '' and !$isAdmin) {
		$parvid = mysql_query("SELECT submittedby, video_py FROM videonodes WHERE id='$row[previousversion]'");
		$arr = mysql_fetch_array($parvid);
		if($arr['submittedby'] != $loginId) {
			if($arr['video_py'] == '#AUTHORONLY#')
				$row['previousversion'] == '';
			if(strncmp($arr['video_py'], "#GROUPS#", 8) == 0) {
				//syntax: #GROUPS#gid1,gid2,gid3
				$grps = explode('#', $arr['video_py']);
				$grplist = "'" . str_replace(',', "','", $grps[2]) . "'";
				$mlists = mysql_query("SELECT memberlist FROM grpmaindata WHERE gid in ($grplist)");
				$memberstr = '';
				while($ml = mysql_fetch_array($mlists)) {
					$memberstr .= $ml['memberlist'];
				}
				if(!strpos($memberstr, $loginId))
					$row['previousversion'] == '';
			}
		}
	}
	if($row['nextversion'] != '' and !$isAdmin) {
		$parvid = mysql_query("SELECT submittedby, video_py FROM videonodes WHERE id='$row[nextversion]'");
		$arr = mysql_fetch_array($parvid);
		if($arr['submittedby'] != $loginId) {
			if($arr['video_py'] == '#AUTHORONLY#')
				$row['nextversion'] = '';
			if(strncmp($arr['video_py'], "#GROUPS#", 8) == 0) {
				//syntax: #GROUPS#gid1,gid2,gid3
				$grps = explode('#', $arr['video_py']);
				$grplist = "'" . str_replace(',', "','", $grps[2]) . "'";
				$mlists = mysql_query("SELECT memberlist FROM grpmaindata WHERE gid in ($grplist)");
				$memberstr = '';
				while($ml = mysql_fetch_array($mlists)) {
					$memberstr .= $ml['memberlist'];
				}
				if(!strpos($memberstr, $loginId))
					$row['nextversion'] == '';
			}
		}
	}
	$tagg = substr($tagg,0,strlen($tagg)-1);
	$allow = False;
	if(($row['submittedby'] == $loginId) or ($row['video_py'] == '#EVERYBODY#') or ($row['video_py'] == '#AUTHORONLY#' and $row['submittedby'] == $loginId) or $isAdmin) {
		$allow = True;
	} else if(strncmp($row['video_py'], "#GROUPS#", 8) == 0) {
		$grps = explode('#', $row['video_py']);
		$grplist = "'" . str_replace(',', "','", $grps[2]) . "'";
		$query = "SELECT memberlist FROM grpmaindata WHERE gid in ($grplist)";
		$mlists = mysql_query($query);
		$memberstr = '';
		while($ml = mysql_fetch_array($mlists)) {
			$memberstr .= $ml['memberlist'];
		}
		if($loginId == '' || strpos($memberstr, $loginId) === FALSE) // if not present
			{}
		else
			$allow = True;
	}
	if($allow)
		$retstring = $retstring . " <Node 
			id = \"$row[id]\" 
			name = \"$row[name]\" 
			category = \"$row[category]\" 
			nodetype= \"$row[nodetype]\" 
		
			tags = \"$tagg\" 
			grandrating = \"$row[grandrating]\" 
			submittedby = \"$row[submittedby]\" 
			submitttime = \"$row[submittime]\" 
			picture = \"$row[picture]\" 
			url = \"$row[url]\" 
			authors = \"$row[authors]\" 
			versionnum = \"$row[versionnum]\" 
			parentvideo=\"$row[parentvideo]\" 
			previousversion = \"$row[previousversion]\" 
			nextversion = \"$row[nextversion]\" 
			inspiredby = \"$row[inspiredby]\" 
			timesseen = \"$row[timesseen]\" 
			lastsseen = \"$row[lastseen]\" 
			timesrated = \"$row[timesrated]\" 
			timesplayed = \"$row[timesplayed]\" 
			source = \"$row[source]\" 
			externalLinks = \"$row[externalLinks]\" 
			docLinks = \"$row[docLinks]\" 			
			description = \"$row[description]\"";
		$retstring .= ($isAdmin or $row['submittedby'] == $loginId) ? " privacy = \"$row[video_py]\" " : " privacy = \"\" ";
		$retstring .= "/>";
}

$retstring = $retstring ."</Video>";

$query = "select * from commentstable where videoid='$vid'";
	 $rs=mysql_query ($query);   
      $retstring = $retstring . '<videocomments>';	 
     while ($row = mysql_fetch_array($rs))	 
     {
	 
		  $commText = "<![CDATA[" . $row['comment'] . "]]>";
		  
          $retstring = $retstring .'<comment>';
		  $retstring=$retstring ."<authorid>".$row['authorid']."</authorid>"."<datetime>".$row['datetime']."</datetime>"."<videoid>".$row['videoid']."</videoid>"."<commenttext>".$commText."</commenttext>"."<cid>".$row['cid']."</cid>";
		  ;
		$retstring = $retstring .'</comment>';
	 }
	 $retstring = $retstring .'</videocomments>';
	


$retstring= $retstring ."</rsp>";


$splcharacters = array("&","'");
$retstring_without_splcharacters = "";
$retstring_without_splcharacters = str_replace($splcharacters, "",$retstring);

echo $retstring_without_splcharacters;
?>

