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


<?phpheader("Content-type: text/xml");include "conntube.php";$retstring= "<?xml version=\"1.0\" encoding=\"utf-8\"?>";  $retstring .= "<rsp>";	// Gather info into variables$youremail = isset ($_POST['youremail']) ? $_POST['youremail'] : "pkmittal82@gmail.com";$vid =       isset ($_POST['vid']) ? $_POST['vid'] : "Video204";$memberList  =    isset ($_POST['memberList']) ? $_POST['memberList'] : "";if($memberList == ""){$toList = "pkmittal81@gmail.com";}else if($memberList == "ALL"){// prepare toList for all members    $toList = "";	$rs = mysql_query("Select * from peoplenodes" );		while ($row = mysql_fetch_array($rs))		{				 if($toList == ""){			 $toList = $row['id'];				 }else{			 $toList = $toList . "," . $row['id'];				 }					}}else{  $toList = $memberList;}$query 		= 	"SELECT name FROM videonodes where id ='$vid'";$result 	= 	mysql_query ($query);$row 		= 	mysql_fetch_array($result);$name 	= 	$row['name'];$subject = "[" . $full_tubename ."]". " Invitation for watching the video : " . $name ;$query 		= 	"SELECT name FROM peoplenodes where id ='$youremail'";$result 	= 	mysql_query ($query);$row 		= 	mysql_fetch_array($result);$personname 	= 	$row['name'];$pr = "This invitation to you comes from " . $personname . " (" . $youremail . ")";$vURL = $full_tubeurl. "?actionName=playvideoByPassLogin&userName=anonymous@gmail.com&key=lab1&videoid=".$vid;$message2 = "You have been invited to watch the video called <b>". $name . "</b> in " . $full_tubename .".<br><br>";$message2 = $message2 . "Please click below to access the video. <br> ";$message2 = $message2 . $vURL ;$message2 = $message2 . "<br><br>" . $pr ;$message = $message2;/*error_log("To List " . $toList);error_log("subject " . $subject);error_log("message " . $message);*/$send = sendInvite($toList,$subject,$message);$send = true;// If we can not send this email let's show the errorif(!$send){$rspMessage = "Can not send your invitation!";}else{$rspMessage = "Success";}$retstring = $retstring . "<message>" . $rspMessage . "</message></rsp>";echo $retstring;?> <?phpfunction sendInvite($to, $subject,$message){$headers  = 'MIME-Version: 1.0' . "\r\n";$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";$headers .= 'From: CALTTubes@insead.edu' . "\r\n" .    'Reply-To: pkmittal82@gmail.com' . "\r\n" .    'X-Mailer: PHP/' . phpversion();		$val=mail($to, $subject, $message, $headers);	return $val;}?>