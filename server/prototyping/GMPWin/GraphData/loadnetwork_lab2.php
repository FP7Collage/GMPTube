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

$loginId = isset($_REQUEST['loginId']) ? $_REQUEST['loginId'] : '';
$isAdmin = isset($_REQUEST['admin']) ? ($_REQUEST['admin'] == 'True' ? True : False) : False;

$retstring= "<?xml version=\"1.0\" encoding=\"utf-8\"?>";  

$retstring .= " <Graph>";
$retstring .= " <loginId>$loginId</loginId>";
//$retstring .= " <isAdmin>" . ($isAdmin ? 'TRUE' : 'FALSE') . "</isAdmin>";

// load the nodetypes
$retstring=$retstring ." <NodeTypes>";
$rs = mysql_query("Select * from nodetypes");
while ($row = mysql_fetch_array($rs))
{
$retstring=$retstring ." <NodeType name = \"$row[name]\" />" ;
}
$retstring=$retstring ."  </NodeTypes>";


// load the edgetypes
$retstring=$retstring ." <EdgeTypes>";
$rs = mysql_query("Select * from edgetypes");
while ($row = mysql_fetch_array($rs))
{
	$retstring .= "<EdgeType";
	$retstring .= " name = \"$row[name]\"";
	$retstring .= " edgecolor = \"$row[edgecolor]\"";
	$retstring .= " validnodepairs= \"$row[validnodepairs]\"/>";
}
$retstring=$retstring . " </EdgeTypes>";






// load the nodes
$retstring=$retstring . " <Nodes>";

// first load the people nodes
$rs = mysql_query("Select * from peoplenodes");
while ($row = mysql_fetch_array($rs))
{
	$retstring .= " <Node";
	$retstring .= " id = \"$row[id]\"";
	$retstring .= " name = \"" . filterAccess($row, $loginId, 'name') . "\"";
	$retstring .= " alias = \"" . filterAccess($row, $loginId, 'alias') . "\"";
	$retstring .= " nodetype= \"$row[nodetype]\"";
	$retstring .= " company = \"" . filterAccess($row, $loginId, 'company') . "\"";
	$retstring .= " title = \"" . filterAccess($row, $loginId, 'title') . "\"";
	$retstring .= " nationality = \"" . filterAccess($row, $loginId, 'nationality') . "\"";
	$retstring .= " picture = \"" . filterAccess($row, $loginId, 'picture') . "\"";
	$retstring .= " invitedby = \"" . filterAccess($row, $loginId, 'invitedby') . "\"";
	$retstring .= " url = \"" . filterAccess($row, $loginId, 'url') . "\"";
	$retstring .= " interests = \"" . filterAccess($row, $loginId, 'interests') . "\"";
	$retstring .= " profile = \"" . filterAccess($row, $loginId, 'profile') . "\"";
	$retstring .= " emailid= \"" . filterAccess($row, $loginId, 'emailid') . "\"";
	$retstring .= " loggedin = \"$row[loggedin]\"";
	$retstring .= " location = \"" . filterAccess($row, $loginId, 'location') . "\"";
	$retstring .= " grandscore = \"$row[grandscore]\"";
	$retstring .= " jointime = \"$row[jointime]\"";
	$retstring .= " timesvisited = \"$row[timesvisited]\"";
	$retstring .= " lastvisit = \"$row[lastvisit]\"";
	$retstring .= " lastaccessed = \"$row[lastaccessed]\"";
	$retstring .= " competences=\"" . filterAccess($row, $loginId, 'competences') . "\"/>";
}

// load the Evaluation nodes here new code has been aded......Please refer to this one
$rs = mysql_query("Select * from evaluation");
while ($row = mysql_fetch_array($rs))
{
	$retstring .= " <Node";
	$retstring .= " id = \"$row[id]\"";
	$retstring .= " islab = \"true\"";
	$retstring .= " nodetype= \"Evaluation\"";
	$retstring .= " picture = \"http://labs.calt.insead.edu/prototyping/InnoTubeProject/media/eval.jpeg\"";
	$retstring .= " evaluation_type = \"$row[evaluation_type]\"";
	$retstring .= " subcriterion= \"$row[subcriterion]\"";
	$retstring .= " createddate = \"$row[createddate]\"/>";
	
}
$rs = mysql_query("Select * from idea");
while ($row = mysql_fetch_array($rs))
{


	$retstring .= " <Node";
	$retstring .= " id = \"$row[id]\"";
	$retstring .= " title = \"$row[title]\"";
	$retstring .= " abstract= \"$row[abstract]\"";
	$rowdesc    = str_replace('"', '', $row['description']); 
	$retstring .= " description= \"$rowdesc\"";
	$retstring .= "	islab = \"true\"";
	$retstring .= " nodetype= \"Ideas\"";
	$retstring .= " picture = \"http://labs.calt.insead.edu/prototyping/InnoTubeProject/media/images.jpeg\"";	
	$retstring .= " createddate = \"$row[createddate]\"/>";
	
}


// load the video nodes
$rs = mysql_query("SELECT * FROM videonodes ORDER BY name");
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
			comments = \"$row[comments]\" 
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


// load the tags
$rs = mysql_query("Select * from tagnodes");
while ($row = mysql_fetch_array($rs))
{
$retstring=$retstring . " <Node id = \"$row[id]\" name = \"$row[name]\"  nodetype= \"$row[nodetype]\" picture = \"$row[picture]\" submittedby = \"$row[submittedby]\" submittime = \"$row[submittime]\" />";
}



$retstring=$retstring . " </Nodes>";


// load the edges

$retstring=$retstring . " \n<Edges>";
$rs = mysql_query("Select * from edges");
while ($row = mysql_fetch_array($rs))
{
	if(!$isAdmin and strncmp($row['fromID'], "Video", 5) == 0) {
		$parvid = mysql_query("SELECT submittedby, video_py FROM videonodes WHERE id='$row[fromID]'");
		$arr = mysql_fetch_array($parvid);
		if($arr['submittedby'] != $loginId) {
			if($row['relationship_py'] == '#AUTHORONLY#')
				continue;
			if(strncmp($row['relationship_py'], "#GROUPS#", 8) == 0) {
				//syntax: #GROUPS#gid1,gid2,gid3
				$grps = explode('#', $row['relationship_py']);
				$grplist = "'" . str_replace(',', "','", $grps[2]) . "'";
				$mlists = mysql_query("SELECT memberlist FROM grpmaindata WHERE gid in ($grplist)");
				$memberstr = '';
				while($ml = mysql_fetch_array($mlists)) {
					$memberstr .= $ml['memberlist'];
				}
				if(!strpos($memberstr, $loginId))
					continue;
			}
		}
	}
	if(!$isAdmin and strncmp($row['toID'], "Video", 5) == 0) {
		$parvid = mysql_query("SELECT submittedby, video_py FROM videonodes WHERE id='$row[toID]'");
		$arr = mysql_fetch_array($parvid);
		if($arr['submittedby'] != $loginId) {
			if($row['relationship_py'] == '#AUTHORONLY#')
				continue;
			if(strncmp($row['relationship_py'], "#GROUPS#", 8) == 0) {
				//syntax: #GROUPS#gid1,gid2,gid3
				$grps = explode('#', $row['relationship_py']);
				$grplist = "'" . str_replace(',', "','", $grps[2]) . "'";
				$mlists = mysql_query("SELECT memberlist FROM grpmaindata WHERE gid in ($grplist)");
				$memberstr = '';
				while($ml = mysql_fetch_array($mlists)) {
					$memberstr .= $ml['memberlist'];
				}
				if(!strpos($memberstr, $loginId))
					continue;
			}
		}
	}
	$retstring=$retstring . " <Edge id = \"$row[id]\" name = \"$row[name]\"  tooltip= \"$row[tooltip]\" fromID = \"$row[fromID]\"  toID = \"$row[toID]\"  intensity = \"$row[intensity]\"  edgecolor = \"$row[edgecolor]\" creationtime = \"$row[creationtime]\" />";
}
// load the has rated relationship
$rs = mysql_query("Select Id,TakenBy,TakenOn,Datetime,Entity,count(*) as count from useractions where action='Rated' group by TakenBy,TakenOn");
while ($row = mysql_fetch_array($rs))
{
	if(!$isAdmin) {
		$edgeid="r".$row['Id'];
		
		//$query=mysql_query("select * from useractions where action='Rated' and TakenBy='".$row['TakenBy']."' and TakenOn='".$row['TakenOn']."' and Datetime > '".$row['Datetime']."'");
		if($row['count']==1)
		$edgeintensity=2;
		else
		$edgeintensity=4;
		$edgename='Has Rated';
		$edgetooltip='has rated';
		$edgecolor='0xfffc00';
		
		
	}
	$retstring=$retstring . " <Edge id = \"$edgeid\" name = \"$edgename\"  tooltip= \"$edgetooltip\" fromID = \"$row[TakenBy]\"  toID = \"$row[TakenOn]\"  intensity = \"$edgeintensity\"  edgecolor = \"$edgecolor\" creationtime = \"$row[Datetime]\" value = \"$row[Entity]\" />";
}

// load the has commented relationship
$rs = mysql_query("Select cid,authorid,videoid,datetime,comment,count(*) as count from commentstable group by authorid,videoid");
while ($row = mysql_fetch_array($rs))
{
	if(!$isAdmin) {
		$edgeid="c".$row['cid'];
		
		//$query=mysql_query("select * from commentstable videoid='".$row['videoid']."' and authorid='".$row['authorid']."' and datetime > '".$row['datetime']."'");
		if($row['count']==1)
		$edgeintensity=2;
		else
		$edgeintensity=4;
		$edgename='Has Commented';
		$edgetooltip='has commented';
		$edgecolor='0xeeaaee';
		
	}
	$retstring=$retstring . " <Edge id = \"$edgeid\" name = \"$edgename\"  tooltip= \"$edgetooltip\" fromID = \"$row[authorid]\"  toID = \"$row[videoid]\"  intensity = \"$edgeintensity\"  edgecolor = \"$edgecolor\" creationtime = \"$row[datetime]\" value = \"$row[comment]\" />";
}

// load the has discussed relationship
$rs = mysql_query("Select messageid,userid,videoid,time,message,count(*) as count from topicmessages group by userid,videoid");
while ($row = mysql_fetch_array($rs))
{
	if(!$isAdmin) {
		$edgeid="d".$row['messageid'];
		
		//$query=mysql_query("select * from topicmessages where userid='".$row['userid']."' and videoid='".$row['videoid']."' and time > '".$row['time']."'");
		if($row['count']==1)
		$edgeintensity=2;
		else
		$edgeintensity=4;
		$edgename='Has Discussed';
		$edgetooltip='has discussed';
		$edgecolor='0xee66cc';
		
	}
	$retstring=$retstring . " <Edge id = \"$edgeid\" name = \"$edgename\"  tooltip= \"$edgetooltip\" fromID = \"$row[userid]\"  toID = \"$row[videoid]\"  intensity = \"$edgeintensity\"  edgecolor = \"$edgecolor\" creationtime = \"$row[time]\" value = \"$row[message]\" />";
}



$retstring=$retstring ." </Edges>\n";


$retstring=$retstring ." <Competences>";
$rs = mysql_query("Select * from competencetable");
while ($row = mysql_fetch_array($rs))
{
	$retstring=$retstring . " <Competence id = \"$row[id]\" name = \"$row[name]\"  />";
}
$retstring=$retstring ." </Competences>\n";


$retstring=$retstring ." <CompetenceTypes>";
$rs = mysql_query("Select * from affiliationtable");
while ($row = mysql_fetch_array($rs))
{
	$retstring=$retstring . " <CompetenceType id = \"$row[id]\" name = \"$row[name]\" competences=\"$row[competences]\" />";
}
$retstring=$retstring ." </CompetenceTypes>\n";


$rs = mysql_query("Select current_timestamp() as tim");
$row = mysql_fetch_array($rs);
$retstring=$retstring."<Time value= \"$row[tim]\" />" ;

$retstring=$retstring . " </Graph>";

$splcharacters = array("&","'");
$retstring_without_splcharacters = str_replace($splcharacters, "",$retstring);

echo $retstring_without_splcharacters;

?>

<?php
function filterAccess($row, $uid, $prop) {
	global $loginId;
	global $isAdmin;
	if($prop == 'alias' || $row['id'] == $uid || $isAdmin)
		return $row[$prop];
	if($row[$prop . '_py'] == '#EVERYBODY#')
		return $row[$prop];
	if($row[$prop . '_py'] == '#AUTHORONLY#')
		if($prop == 'picture')
			return 'http://labs.calt.insead.edu/prototyping/Tentube/Testing/GraphData/media/people/default_people.jpg';
		else if($prop == 'name')
			return $row['alias'];
		else
			return 'Not Visible';
	if(strncmp($row[$prop . '_py'], "#GROUPS#", 8) == 0) {
		$grps = explode('#', $row[$prop . '_py']);
		$grplist = "'" . str_replace(',', "','", $grps[2]) . "'";
		$query = "SELECT memberlist FROM grpmaindata WHERE gid in ($grplist)";
		$mlists = mysql_query($query);
		$memberstr = '';
		while($ml = mysql_fetch_array($mlists)) {
			$memberstr .= $ml['memberlist'];
		}
		if($loginId == '' || strpos($memberstr, $loginId) === FALSE) // if not present
			if($prop == 'picture')
				return 'http://labs.calt.insead.edu/prototyping/Tentube/Testing/GraphData/media/people/default_people.jpg';
			else if($prop == 'name')
				return $row['alias'];
			else
				return 'Not Visible';
		else return $row[$prop];
	}
	return "##ERROR#'" . $row[$prop . "_py"] . "'";
}
?>
