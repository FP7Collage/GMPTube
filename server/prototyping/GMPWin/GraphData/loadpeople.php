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
	$list='';
	$rs = mysql_query("Select id from peoplenodes");
	while ($row = mysql_fetch_array($rs)){
		$list	=	$row['id'].','.$list;
	}

	$list 	= substr($list, 0, -1);
	
	
	$retstring = "<rsp>";
	$retstring .= "<ppllist>".$list."</ppllist>";
	

	$retstring .=  "<Person>";

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
	//$retstring .= " profile = \"" . filterAccess($row, $loginId, 'profile') . "\"";
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

$retstring = $retstring ."</Person>";
$retstring= $retstring ."</rsp>";


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

