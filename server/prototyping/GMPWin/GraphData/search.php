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
session_start();
header("Cache-control: private");
header("Content-type: text/xml");
include "conntube.php";
$cat = isset($_REQUEST['cat']) ? $_REQUEST['cat'] : '';
$rs = null;
if($cat == '')
	$rs = mysql_query("Select * from videonodes ");
else
	$rs = mysql_query("Select * from videonodes where category='$cat'");
$retstring = '<rsp>';
while ($row = mysql_fetch_array($rs)) {
	$tagg='';
	$rs1 = mysql_query("Select * from edges where fromID='$row[id]' and name='Has tags' ");
	while($row1=mysql_fetch_array($rs1)) {
		$rs2=mysql_query("Select * from tagnodes where id='$row1[toID]' ");
		$row2=mysql_fetch_array($rs2);
		$tagg = $tagg.$row2['name'].",";
	}
	$tagg = substr($tagg,0,strlen($tagg)-1);
	$tosearch=$_REQUEST['keyword'];
	$search_array = explode(',',$tosearch);
	//echo($tosearch);
	if($row[source]=="FMS")
	$url=str_ireplace("rtmp://labs.calt.insead.edu/tentube/","http://labs.calt.insead.edu/prototyping/TenTube/Graphdata/upload/videos/",$row[url]) . ".flv";
	foreach($search_array as $key)
	{
	if(stripos($row[name],$key ) != FALSE || stripos($row[tags],$key ) != FALSE )
	$retstring = $retstring . "<Node id = \"$row[id]\" name = \"$row[name]\"  category = \"$row[category]\" nodetype= \"$row[nodetype]\" comments = \"$row[comments]\"  tags = \"$tagg\"  grandrating = \"$row[grandrating]\"  submittedby = \"$row[submittedby]\"   submitttime = \"$row[submittime]\"   picture = \"$row[picture]\"  url = \"$url\" authors = \"$row[authors]\" versionnum = \"$row[versionnum]\"
	parentvideo=\"$row[parentvideo]\" previousversion = \"$row[previousversion]\" nextversion = \"$row[nextversion]\" inspiredby = \"$row[inspiredby]\"   timesseen = \"$row[timesseen]\"    lastsseen = \"$row[lastseen]\"   timesrated = \"$row[timesrated]\"   timesplayed = \"$row[timesplayed]\"   source = \"$row[source]\"   externalLinks = \"$row[externalLinks]\"   docLinks = \"$row[docLinks]\"  description = \"$row[description]\"  />\n";	
	}
	}
$retstring .= '</rsp>';
// make the XML valid by replacing the special characters with their equivalent escape sequences

$retstring=str_replace("&","-ampersand-",$retstring);
/*
str_replace("<","&lt",$retstring);
str_replace(">","&gt",$retstring);
str_replace("\"","&quot",$retstring);
str_replace("'","&#39",$retstring);
*/




echo $retstring;
?>
