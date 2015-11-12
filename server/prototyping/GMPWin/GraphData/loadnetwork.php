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

$retstring= "<?xml version=\"1.0\" encoding=\"utf-8\"?>";  

$retstring= $retstring ." <Graph>";






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
	$retstring .= " name = \"$row[name]\"";
	$retstring .= " nodetype= \"$row[nodetype]\"";
	$retstring .= " company = \"$row[company]\"";
	$retstring .= " title = \"$row[title]\"";
	$retstring .= " nationality = \"$row[nationality]\"";
	$retstring .= " picture = \"$row[picture]\"";
	$retstring .= " invitedby = \"$row[invitedby]\"";
	$retstring .= " url = \"$row[url]\"";
	$retstring .= " interests = \"$row[interests]\"";
	$retstring .= " profile = \"$row[profile]\"";
	$retstring .= " emailid= \"$row[emailid]\"";
	$retstring .= " loggedin = \"$row[loggedin]\"";
	$retstring .= " location = \"$row[location]\"";
	$retstring .= " grandscore = \"$row[grandscore]\"";
	$retstring .= " jointime = \"$row[jointime]\"";
	$retstring .= " timesvisited = \"$row[timesvisited]\"";
	$retstring .= " lastvisit = \"$row[lastvisit]\"";
	$retstring .= " lastaccessed = \"$row[lastaccessed]\"";
	$retstring .= " competences=\"$row[competences]\"/>";
}

$rs = mysql_query("Select * from evaluation");
while ($row = mysql_fetch_array($rs))
{
	$retstring .= " <Node";
	$retstring .= " id = \"$row[id]\"";
	$retstring .= " evaluation_type = \"$row[evaluation_type]\"";
	$retstring .= " subcriterion= \"$row[subcriterion]\"";
	$retstring .= " createddate = \"$row[createddate]\"/>";
	
}


// load the video nodes

$rs = mysql_query("Select * from videonodes ");
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
	$tagg = substr($tagg,0,strlen($tagg)-1);
$retstring=$retstring . " <Node id = \"$row[id]\" name = \"$row[name]\"  category = \"$row[category]\" nodetype= \"$row[nodetype]\" comments = \"$row[comments]\"  tags = \"$tagg\"  grandrating = \"$row[grandrating]\"  submittedby = \"$row[submittedby]\"   submitttime = \"$row[submittime]\"   picture = \"$row[picture]\"  url = \"$row[url]\" authors = \"$row[authors]\" versionnum = \"$row[versionnum]\" parentvideo=\"$row[parentvideo]\" previousversion = \"$row[previousversion]\" nextversion = \"$row[nextversion]\" inspiredby = \"$row[inspiredby]\"   timesseen = \"$row[timesseen]\"    lastsseen = \"$row[lastseen]\"   timesrated = \"$row[timesrated]\"   timesplayed = \"$row[timesplayed]\"   source = \"$row[source]\"   externalLinks = \"$row[externalLinks]\"   docLinks = \"$row[docLinks]\"  description = \"$row[description]\"  />";	
}




// load the tags

$rs = mysql_query("Select * from tagnodes");
while ($row = mysql_fetch_array($rs))
{
$retstring=$retstring . " <Node id = \"$row[id]\" name = \"$row[name]\"  nodetype= \"$row[nodetype]\" picture = \"$row[picture]\" submittedby = \"$row[submittedby]\" submittime = \"$row[submittime]\" />";
}



$retstring=$retstring . " </Nodes>";







// load the edges

$retstring=$retstring . " <Edges>";
$rs = mysql_query("Select * from edges");
while ($row = mysql_fetch_array($rs))
{
$retstring=$retstring . " <Edge id = \"$row[id]\" name = \"$row[name]\"  tooltip= \"$row[tooltip]\" fromID = \"$row[fromID]\"  toID = \"$row[toID]\"  intensity = \"$row[intensity]\"  edgecolor = \"$row[edgecolor]\" creationtime = \"$row[creationtime]\" />";

}


$retstring=$retstring ." </Edges>";
$retstring=$retstring ." <Competences>";
$rs = mysql_query("Select * from competencetable");
while ($row = mysql_fetch_array($rs))
{
$retstring=$retstring . " <Competence id = \"$row[id]\" name = \"$row[name]\"  />";

}

$retstring=$retstring ." </Competences>";
$retstring=$retstring ." <CompetenceTypes>";
$rs = mysql_query("Select * from affiliationtable");
while ($row = mysql_fetch_array($rs))
{
$retstring=$retstring . " <CompetenceType id = \"$row[id]\" name = \"$row[name]\" competences=\"$row[competences]\" />";

}

$retstring=$retstring ." </CompetenceTypes>";

$rs = mysql_query("Select current_timestamp() as tim");
$row = mysql_fetch_array($rs);
$retstring=$retstring."<Time value= \"$row[tim]\" />" ;

$retstring=$retstring . " </Graph>";

$splcharacters = array("&","'");
$retstring_without_splcharacters = str_replace($splcharacters, "",$retstring);

echo $retstring_without_splcharacters;

?>


