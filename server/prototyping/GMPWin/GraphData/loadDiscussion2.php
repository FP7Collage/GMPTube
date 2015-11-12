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
//session_start();
header("Cache-control: private");


header("Content-type: text/xml");
include "conntube.php";

$action = $_REQUEST['action'];
$videoid = $_REQUEST['videoid'];

switch($action)
{
	case 'load':
		
		break;
	
	case 'newtopic':
		$userid = $_POST['userid'];
		$name = $_POST['name'];
		$username = $_POST['username'];
		//error_log($userid);
		//error_log($videoid);
		//error_log($name);
		//error_log($username);
		mysql_query("insert into topics(time,userid,videoid,name,username) values(current_timestamp(),'$userid','$videoid','$name','$username')") or die(mysql_error());
		mysql_query("update videonodes set LDATime=current_timestamp() where id='$videoid'");
		break;
		
	case 'deletetopic':
		$topicid = $_POST['topicid'];
		//error_log($topicid);
		mysql_query("delete from topics where topicid='$topicid'");
		mysql_query("delete from topicmessages where topicid='$topicid'");
		//mysql_query("update videonodes set LDATime=current_timestamp() where id='$videoid'");
		break;
	
	case 'edittopic':
		$name = $_POST['name'];
		$topicid = $_POST['topicid'];
		//error_log($topicid);
		mysql_query("update topics set name='$name' where topicid='$topicid'");
		//mysql_query("delete from topicmessages where topicid='$topicid'");
		mysql_query("update videonodes set LDATime=current_timestamp() where id='$videoid'");
		//mysql_query("update videonodes set LDATime=current_timestamp() where id='$videoid'");
		break;
		
	case 'newmessage':
		$userid = $_POST['userid'];
		$topicid = $_POST['topicid'];
		$message = $_POST['message'];
		$username = $_POST['username'];
		
		$splcharacters = array("'");	
		$message = str_replace($splcharacters, "\'",$message);	  
	
		
		mysql_query("insert into topicmessages(time,userid,videoid,username,topicid,message) values(current_timestamp(),'$userid','$videoid','$username','$topicid','$message')") or die(mysql_error());
		mysql_query("update videonodes set LDATime=current_timestamp() where id='$videoid'");

		break;
		
	
	case 'delete':
		$messageid = $_POST['messageid'];
		mysql_query("delete from topicmessages where messageid = '$messageid'") or die(mysql_error());
		break;
	
	case 'edit':
		$messageid = $_POST['messageid'];
		$message = $_POST['message'];
	
      	$splcharacters = array("'");	
		$message = str_replace($splcharacters, "\'",$message);	  
	
	
		mysql_query("update topicmessages set message='$message' where messageid = '$messageid'") or die(mysql_error());
		break;
		
	default :
	break;
}

$retstring= "<?xml version=\"1.0\" encoding=\"utf-8\"?>";  

		$retstring= $retstring ." <Data>";
		$rs = mysql_query("Select name from videonodes where id ='$videoid'");
		while ($row = mysql_fetch_array($rs))
		{
			$retstring=$retstring."<VideoName name = \"$row[name]\" />";
		}

		$rs = mysql_query("Select * from topics where videoid ='$videoid'");
		while ($row = mysql_fetch_array($rs))
		{
			$retstring=$retstring ." <Topic topicid = \"$row[topicid]\"  time = \"$row[time]\"  userid = \"$row[userid]\"  videoid = \"$row[videoid]\"  name = \"$row[name]\"  username = \"$row[username]\" />" ;
		/*	$retstring=$retstring ." <Topic>";		 
		 $retstring=$retstring ."<topicid>".$row['topicid']."</topicid>"."<time>".$row['time']."</time>"."<userid>".$row['userid']."</userid>"."<videoid>".$row['videoid']."</videoid>"."<username>".$row['username']."</username>";
		$retstring = $retstring .'</Topic>';
		*/
		}


		$rs = mysql_query("Select * from topicmessages where videoid ='$videoid' order by time DESC");
		while ($row = mysql_fetch_array($rs))
		{
	//		$retstring=$retstring ." <Message topicid = \"$row[topicid]\"  time = \"$row[time]\"  userid = \"$row[userid]\"  messageid = \"$row[messageid]\"  message = \"$row[message]\"  username = \"$row[username]\" />" ;
		
	 $commText = "<![CDATA[" . $row['message'] . "]]>";
		  
		  
//          $retstring = $retstring .'<Message>';
  //       $retstring=$retstring ."<topicid>".$row['topicid']."</topicid>"."<time>".$row['time']."</time>"."<userid>".$row['userid']."</userid>"."<message>".$commText."</message>"."<username>".$row['username']."</username>";
		  
	   $retstring=$retstring ." <Message topicid = \"$row[topicid]\"  time = \"$row[time]\"  userid = \"$row[userid]\"  messageid = \"$row[messageid]\"    username = \"$row[username]\" >" ;
		$retstring=$retstring ."<message>".$commText."</message>";	
	    $retstring = $retstring .'</Message>';
		
		
		
		
		}



		$retstring=$retstring . " </Data>";

	//	$splcharacters = array("&","'");
	//	$retstring_without_splcharacters = str_replace($splcharacters, "",$retstring);

	//	echo $retstring_without_splcharacters;
	
	//error_log ($retstring);
	    echo $retstring;
		
		
?>

