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
// no empty lines
//session_start();
header('Cache-Control: private');
header('Content-type: text/xml');

require 'conntube.php';



$res  = '<?xml version="1.0" encoding="utf-8"?>';
$res .= "<rsp>";
/*
if(!isset($_REQUEST['loginId'])) {
	$res .= '<status>false</status>';
	$res .= '<message>Please give the loginId</message>';
	die($res . '</rsp>');
}
*/
//$loginId	= isset ($_REQUEST['$loginId']) ? $_REQUEST['$loginId'] : 'pkmittal82@gmail.com';

$loginId = $_REQUEST['loginId'];
error_log($full_tubename . " calling viewlatest welcome agent " .$loginId );

$res .= "<loginId>$loginId</loginId>";

$rs2 = mysql_query("SELECT p.id, p.name, p.name_py, p.alias, p.picture, p.picture_py
				   FROM peoplenodes p WHERE p.jointime > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 7 DAY)");

$res .= "<users>";
while($row = mysql_fetch_array($rs2)) {
	$res .= "<user>";
	$res .= "<id>$row[id]</id>";
	$res .= "<name>" . filterAccess($row, $loginId, 'name') . "</name>";
	//$res .= "<name_py>$row[name_py]</name_py>";
	$res .= "<alias>$row[alias]</alias>";
	$res .= "<picture>" . filterAccess($row, $loginId, 'picture') . "</picture>";
	//$res .= "<picture_py>$row[picture_py]</picture_py>";
	$res .= "</user>";
}
$res .= "</users>";

$rs1 = mysql_query("SELECT v.* FROM videonodes v,
				   (SELECT MAX(t.dt) AS lastlogin FROM (SELECT datetime AS dt FROM useractions u
				   WHERE takenby='$loginId' AND action='Login') t) l WHERE v.submittime > l.lastlogin");

$res .= "<videos>";
while($row = mysql_fetch_array($rs1)) {
	if($row['parentvideo'] != '') {
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
	if($row['previousversion'] != '') {
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
	if($row['nextversion'] != '') {
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
	if(($row['submittedby'] == $loginId) or ($row['video_py'] == '#EVERYBODY#') or ($row['video_py'] == '#AUTHORONLY#' and $row['submittedby'] == $loginId)) {
		$res .= "<video>";
		$res .= "<id>$row[id]</id>";
		$res .= "<number>$row[number]</number>";
		$res .= "<name>$row[name]</name>";
		$res .= "<picture>$row[picture]</picture>";
		$res .= "<video_py>$row[video_py]</video_py>";
		$res .= "<comments>$row[comments]</comments>";
		$res .= "</video>";
	} else if(strncmp($row['video_py'], "#GROUPS#", 8) == 0) {
		$grps = explode('#', $row['video_py']);
		$grplist = "'" . str_replace(',', "','", $grps[2]) . "'";
		$query = "SELECT memberlist FROM grpmaindata WHERE gid in ($grplist)";
		$mlists = mysql_query($query);
		$memberstr = '';
		while($ml = mysql_fetch_array($mlists)) {
			$memberstr .= $ml['memberlist'];
		}
		if(strpos($memberstr, $loginId) === FALSE) // if not present
			{}
		else {
			$res .= "<video>";
			$res .= "<id>$row[id]</id>";
			$res .= "<number>$row[number]</number>";
			$res .= "<name>$row[name]</name>";
			$res .= "<picture>$row[picture]</picture>";
			$res .= "<video_py>$row[video_py]</video_py>";
			$res .= "<comments>$row[comments]</comments>";
			$res .= "</video>";
		}
	}
}
$res .= "</videos>";

$rs3 = mysql_query("SELECT u.action, p.id, p.name, p.name_py, p.alias, u.logs_py,
				   DATE_FORMAT(u.Datetime, '%d/%m/%Y - %r') AS dt FROM useractions u, peoplenodes p, (SELECT MAX(t.dt) AS lastlogin
				   FROM (SELECT datetime AS dt FROM useractions u WHERE takenby='$loginId'
				   AND action='Login') t) l WHERE u.Datetime > l.lastlogin AND p.id=u.TakenBy");

$res .= "<acts>";
while($row = mysql_fetch_array($rs3)) {
	$res .= "<act>";
	$res .= "<action>$row[action]</action>";
	//$res .= "<name>$row[name]</name>";
	$res .= "<name>" . filterAccess($row, $loginId, 'name') . "</name>";
	//$res .= "<name_py>$row[name_py]</name_py>";
	$res .= "<dt>$row[dt]</dt>";
	//$res .= "<logs_py>$row[logs_py]</logs_py>";
	$res .= "</act>";
}
$res .= "</acts>";

$res .= '<status>true</status>';

die($res . '</rsp>');
mysql_close($conn);


?>

<?php
function filterAccess($row, $uid, $prop) {
	global $loginId;
	if($prop == 'alias' || (isset($row['id']) && $row['id'] == $uid))
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
		if(strpos($memberstr, $loginId) === FALSE) // if not present
			if($prop == 'picture') {
				//$constants = simplexml_load_file("constants.xml");
				return 'http://labs.calt.insead.edu/prototyping/Tentube/Testing/GraphData/media/people/default_people.jpg';
			}
		else if($prop == 'name')
			return $row['alias'];
		else
		return 'Not Visible';
		else return $row[$prop];
	}
	return "##ERROR#'" . $row[$prop . "_py"] . "'";
}
?>

