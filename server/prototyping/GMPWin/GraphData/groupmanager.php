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
$result = "Not attempted";

$action 		= $_REQUEST['action'];
include("conntube.php");

	switch($action)
	{
		case 'creategroup':
					$name 				= $_REQUEST['name'];
					$author 			= $_REQUEST['author'];
					$url 				= $_REQUEST['url'];
					$description 		= $_REQUEST['description'];
					$members 			= $_REQUEST['members'];
					$picture 			= $_REQUEST['picture'];
					$id 				= $_REQUEST['id'];
					
					$count=mysql_query("SELECT count(*) FROM grpmaindata WHERE name='$name' AND isactivated='yes'",$conn);
					$row = mysql_fetch_row($count);
					
					if($row[0]>0){
						$result = 'Exists';
						break;
					}
					
					mysql_query("INSERT INTO grpmaindata(gid,name,picturepath,url,description,createdtime,modifiedtime,author,memberlist) values('$id','$name','$picture','$url','$description',current_timestamp(),current_timestamp(),'$author','$members')",$conn);
					$result = 'Success';
					
					break;
					
		case 'editgroup':
					$name 				= $_REQUEST['name'];
					$author 			= $_REQUEST['author'];
					$url 				= $_REQUEST['url'];
					$description 		= $_REQUEST['description'];
					$members 			= $_REQUEST['members'];
					$id 				= $_REQUEST['id'];
					
					$count=mysql_query("SELECT count(*) FROM grpmaindata WHERE name='$name' AND (gid<>'$id' AND isactivated='yes')",$conn);
					$row = mysql_fetch_row($count);
					
					if($row[0]>0){
						$result = 'Exists';
						break;
					}
					
					mysql_query("UPDATE grpmaindata SET name='$name' WHERE gid='$id'",$conn);
					mysql_query("UPDATE grpmaindata SET author='$author' WHERE gid='$id'",$conn);
					mysql_query("UPDATE grpmaindata SET url='$url' WHERE gid='$id'",$conn);
					mysql_query("UPDATE grpmaindata SET description='$description' WHERE gid='$id'",$conn);
					//update the log.. the table useractions
					$data=mysql_query("select * from grpmaindata where gid='$id'",$conn);
					$row = mysql_fetch_array($data);
					$memberlist=explode(';',$row['memberlist']);
					$newmembers=explode(';',$members);
					//memebrs departed
					foreach($memberlist as $member)
					{
						
						$i=0;
						foreach($newmembers as $newmember)
						{
							if($member==$newmember)
							{
							$i=1;
							}
						}
						if($i==0)
						mysql_query ("INSERT INTO useractions(action,TakenBy,TakenOn,Datetime) VALUES ('MemberDeparted','$member','$id',current_timestamp())") or die(mysql_error());

					}
					
					//memebrs joined
					foreach($newmembers as $newmember)
					{
						
						$i=0;
						foreach($memberlist as $member)
						{
							if($member==$newmember)
							{
							$i=1;
							}
						}
						if($i==0)
						mysql_query ("INSERT INTO useractions(action,TakenBy,TakenOn,Datetime) VALUES ('MemberNew','$newmember','$id',current_timestamp())") or die(mysql_error());

					}
					mysql_query("UPDATE grpmaindata SET memberlist='$members' WHERE gid='$id'",$conn);
					mysql_query("UPDATE grpmaindata SET modifiedtime=current_timestamp() WHERE gid='$id'",$conn);
					$result='Success';
					
					break;
					
		case 'renamephotocreate':
					$result='';
					$finalname			=$_REQUEST['finalname'];
					$existingname		=$_REQUEST['existingname'];
					$serverpath			=$_REQUEST['serverpath'];
					
					$ext=substr($existingname, strrpos($existingname, '.') + 1);
					$pwd=getcwd();
					
					$pastname=$pwd.'/media/groupphotos/'.$existingname;
					$presentname=$pwd.'/media/groupphotos/'.$finalname.".".$ext;
					
					rename($pastname,$presentname);
					
					$finalpath=$serverpath."GraphData/media/groupphotos/".$finalname.".".$ext;
					mysql_query("UPDATE grpmaindata SET picturepath='$finalpath' WHERE gid='$finalname' AND isactivated='yes'",$conn);
					$result='Success'.'%@%'.$finalpath;
					break;
		
		case 'renamephotoedit':
					$result='';
					$id					=$_REQUEST['id'];
					$finalname			=$_REQUEST['finalname'];
					$existingname		=$_REQUEST['existingname'];
					$serverpath			=$_REQUEST['serverpath'];
					
					$recgrp=mysql_query("SELECT picturepath FROM grpmaindata WHERE gid='$id'",$conn);
					while ($row = mysql_fetch_assoc($recgrp))
					{
						$pathonserver=$row['picturepath'];
					}
					
					
					
					$ext=substr($pathonserver, strrpos($pathonserver, '.') + 1);
					$pwd=getcwd();
					$tot=strlen($ext)+11;
					$deletename=$pwd.'/media/groupphotos/'.substr($pathonserver,-$tot);
					
					chmod($deletename,0777);
					unlink($deletename);
					
					$ext=substr($existingname, strrpos($existingname, '.') + 1);
					$pwd=getcwd();
					
					$pastname=$pwd.'/media/groupphotos/'.$existingname;
					$presentname=$pwd.'/media/groupphotos/'.$finalname.".".$ext;
					
					rename($pastname,$presentname);
					
					$finalpath=$serverpath."GraphData/media/groupphotos/".$finalname.".".$ext;
					mysql_query("UPDATE grpmaindata SET picturepath='$finalpath' WHERE gid='$id' AND isactivated='yes'",$conn);
					$result='Success'.'%@%'.$finalpath;
					break;
		
		case 'getmemberof':
					header("Content-type: text/xml");
					$pid = isset($_REQUEST['profileId']) ? $_REQUEST['profileId'] : '';
					$result = '<rsp>';
					$rs = mysql_query("SELECT gid, name FROM grpmaindata WHERE memberlist LIKE '%$pid%';");
					$result .= "<groups>";
					while ($row = mysql_fetch_assoc($rs)) {
						$result .= "<group>";
						$result .= "<gid>$row[gid]</gid>";
						$result .= "<gname>$row[name]</gname>";
						$result .= "</group>\n";
					}
					$result .= "</groups>";
					$query=mysql_query("select * from peoplenodes where id='$pid'");
					$row=mysql_fetch_array($query);
					$result .= "<profile>";
					$result .= "<![CDATA[";
					
					
				//	$splcharacters = array("&","'","<",">");
					
				//	$profile = str_replace($splcharacters, "",$row['profile']);
					$result .=$row['profile'];
				//	$result .=$profile;
					
					$result .= "]]>";
					
					$result .= "</profile>";
					$result .= '</rsp>';
					echo $result;
					die();
					break;
		
		case 'joingroup':
					$result='';
					
					$id				=$_REQUEST['id'];
					$pid			=$_REQUEST['pid'];
					
					//$pid='rakesh364bits@gmail.com';
					//$id='879269F409';
					
					if($pid == '') {
						$result='Member not supplied for addition';
						break;
					} 
					elseif($id == '') {
						$result='Group Identification failed!!';
						break;
					}
					else {
						$ml = mysql_query("SELECT memberlist FROM grpmaindata WHERE gid='$id'",$conn);
						$mlist = mysql_fetch_row($ml);
						if((strpos($mlist[0], $pid) === False)) {
							$str=$mlist[0];
							if($str=='')
								$str=$pid;
							else
								$str=$str.';'.$pid;	
								
							mysql_query("UPDATE grpmaindata SET memberlist='$str' WHERE gid='$id'",$conn);
							$result='Success';
						}
						else{
							$result='Already';
							break;
						}
					}
					break;
		
		case 'unjoingroup':
					$result='';
					
					$id				=$_REQUEST['id'];
					$pid			=$_REQUEST['pid'];
					
					if($pid == '') {
						$result='Member not supplied for addition';
						break;
					} 
					elseif($id == '') {
						$result='Group Identification failed!!';
						break;
					} 
					else {
						$ml = mysql_query("SELECT memberlist FROM grpmaindata WHERE gid='$id'");
						$mlist = mysql_fetch_row($ml);
						if((strpos($mlist[0], $pid) === False)) {
							$result='Not a member';
							break;
						}
						else{
							$str=$mlist[0];
							$str=ereg_replace($pid,'',$str);
							if(substr($str,-1)==';'){
								$str=substr($str,0,(strlen($str)-1));    
							}
							elseif(substr($str,0,1)==';'){
								$str=substr($str,0,-1);    
							}
							else{
								$str=ereg_replace(';;',';',$str);
							}
							mysql_query("UPDATE grpmaindata SET memberlist='$str' WHERE gid='$id'",$conn);
							$result='Success';
						}
					}
					break;
		
		case 'getgrouplist':
					$result='';
					$count=mysql_query("SELECT count(*) FROM grpmaindata WHERE isactivated='yes'");
					$row = mysql_fetch_row($count);
					
					if($row[0]==0){
						$result = 'NoGroup';
						break;
					}
				
					$recgrp=mysql_query("SELECT gid,name,picturepath FROM grpmaindata WHERE isactivated='yes'",$conn);
					$result="";
					while ($row = mysql_fetch_assoc($recgrp))
					{
						$result=$result.$row['gid'].'%@%';
						$result=$result.$row['name'].'%@%';
						$result=$result.$row['picturepath'].'#@#';
					}
					break;
		
		case 'directlogin':
					
					$result='';
					
					$username=$_REQUEST['username'];
					$key	 =$_REQUEST['key'];
					
					//$username='rakesh364bits@gmail.com';
					//$key='lab1';
					
					$count=mysql_query("SELECT count(*) FROM keystable WHERE (ikey='$key' AND userid='$username')");
					$row = mysql_fetch_row($count);
					
					if($row[0]>0){
						$result = 'Success';
						break;
					}
					else{
						$result = 'Fail';
						break;
					}
					
					
		case 'getgroupdata':
					$id=$_REQUEST['id'];
					//$id='6CD6634341';
					$recgrp=mysql_query("SELECT gid,name,picturepath,url,description,author,memberlist FROM grpmaindata WHERE gid='$id' AND isactivated='yes'");
					$result="";
					while ($row = mysql_fetch_assoc($recgrp))
					{
						$result=$result.$row['gid'].'%@%';
						$result=$result.$row['name'].'%@%';
						$result=$result.$row['picturepath'].'%@%';
						$result=$result.$row['url'].'%@%';
						$result=$result.$row['description'].'%@%';
						$result=$result.$row['author'].'%@%';
						$result=$result.$row['memberlist'];
					}
					break;
		
		case 'deletegroup':
					$id=$_REQUEST['id'];
					mysql_query("UPDATE grpmaindata SET isactivated='no' WHERE gid='$id'");
					mysql_query("UPDATE grpmaindata SET removaldate=current_timestamp() WHERE gid='$id'");
					$result='Success';
					break;
		
		case 'competence':
					$recgrp=mysql_query("SELECT id,name FROM competencetable",$conn);
					$result="";
					while ($row = mysql_fetch_assoc($recgrp))
					{
						$result=$result.$row['id'].'%@%';
						$result=$result.$row['name'].'#@#';
					}
					break;
		
		case 'timestamp':
					$count=mysql_query("SELECT CURRENT_TIMESTAMP",$conn);
					$row = mysql_fetch_row($count);
					$result=$row[0];
					break;
		
		case 'noofchatusers':
					$count=mysql_query("SELECT count(*) FROM chatusers where accesstime>TIMESTAMPADD(SECOND,-40,current_timestamp()) and loginstatus=true");
					$row = mysql_fetch_row($count);
					$result=$row[0];
					break;
					
		case 'loadvideos':
		
					$result='';
					$count=mysql_query("SELECT count(*) FROM videonodes");
					$row = mysql_fetch_row($count);
					
					if($row[0]==0){
						$result = 'NoVideo';
						break;
					}
				
				//	$recgrp=mysql_query("SELECT id,name,category,picture FROM videonodes order by category,name ASC");
			$recgrp=mysql_query("SELECT id,name,category,picture,submittime  FROM videonodes where video_py <> \"#AUTHORONLY#\" ORDER BY name asc");
					$result="";
					//sort($recgrp);
					//error_log("sorted");
					//$array=mysql_fetch_array($recgrp
					while ($row = mysql_fetch_assoc($recgrp))
					{
						$result=$result.$row['id'].'%@%';
						$result=$result.$row['name'].'%@%';
						$result=$result.$row['category'].'%@%';
						$result=$result.$row['picture'].'#@#';
					}
					break;
					
		case 'loadvideosactive':
		
					$result='';
					$count=mysql_query("SELECT count(*) FROM videonodes");
					$row = mysql_fetch_row($count);
					
					if($row[0]==0){
						$result = 'NoVideo';
						break;
					}
				
				//	$recgrp=mysql_query("SELECT id,name,category,picture FROM videonodes order by category,name ASC");
			$recgrp=mysql_query("SELECT id,name,category,picture,submittime  FROM videonodes where video_py <> \"#AUTHORONLY#\" ORDER BY LDATime desc");
					$result="";
					//sort($recgrp);
					//error_log("sorted");
					//$array=mysql_fetch_array($recgrp
					while ($row = mysql_fetch_assoc($recgrp))
					{
						$result=$result.$row['id'].'%@%';
						$result=$result.$row['name'].'%@%';
						$result=$result.$row['category'].'%@%';
						$result=$result.$row['picture'].'#@#';
					}
					break;
					
		case 'loadtopics':
					
					$result='';
					$count=mysql_query("SELECT t1.*, t2.lastpost FROM
											(SELECT t.topicid as tid,
											t.name as topicname,
											t.username as creator,
											t.videoid as vidid,
											v.name as videoname
											FROM topics t, videonodes v
											WHERE t.videoid=v.id) t1,
											(SELECT tm.topicid as tid, MAX(time) as lastpost
											FROM topicmessages tm GROUP BY tm.topicid) t2
											WHERE t1.tid=t2.tid ORDER BY t2.lastpost DESC;",$conn);
					$row = mysql_fetch_row($count);
					
					if($row[0]==0){
						$result = 'NoTopic';
						break;
					}
				
					$recgrp=mysql_query("SELECT t1.*, t2.lastpost FROM
											(SELECT t.topicid as tid,
											t.name as topicname,
											t.username as creator,
											t.videoid as vidid,
											v.name as videoname
											FROM topics t, videonodes v
											WHERE t.videoid=v.id) t1,
											(SELECT tm.topicid as tid, MAX(time) as lastpost
											FROM topicmessages tm GROUP BY tm.topicid) t2
											WHERE t1.tid=t2.tid ORDER BY t2.lastpost DESC;",$conn);
											
					$result="";
					while ($row = mysql_fetch_assoc($recgrp))
					{
						$result=$result.$row['topicname'].'%@%';
						$result=$result.$row['creator'].'%@%';
						$result=$result.$row['vidid'].'%@%';
						$result=$result.$row['videoname'].'%@%';
						$result=$result.$row['lastpost'].'#@#';
					}
					break;
				
					
		
		default:
					$result = 'Bad request to server!!';
					break;
					
	}
	
	
	echo "<rsp><message>$result</message></rsp>";
	
?>