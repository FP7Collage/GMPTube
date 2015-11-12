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


<?phpinclude "conntube.php";	// Gather info into variables$youremail = $_REQUEST['youremail'];$vid = $_REQUEST['vid'];$query 		= 	"SELECT name FROM videonodes where id ='$vid'";$result 	= 	mysql_query ($query);$row 		= 	mysql_fetch_array($result);$name 	= 	$row['name'];$subject2 = "[" . $full_tubename ."]". " Personal Recommendation for the video : " . $name ;$query 		= 	"SELECT name FROM peoplenodes where id ='$youremail'";$result 	= 	mysql_query ($query);$row 		= 	mysql_fetch_array($result);$personname 	= 	$row['name'];$pr = "This Personal Recommendation to you comes from " . $personname . " (" . $youremail . ")";//$vURL = $full_tubeurl. "?actionName=playvideoByPassLogin&userName=anonymous@gmail.com&key=lab1&videoid=".$vid;$vURL = $full_tubeurl. "?actionName=playmyvideo&videoid=".$vid;$message2 = "Click below to access the video. <br> ";$message2 = $message2 . $vURL ;$message2 = $message2 . "<br><br>" . $pr ;$email = $_REQUEST['email'];$message = $_REQUEST['message'];$subject = $_REQUEST['subject'];// $full_tubename , $full_tubeurl$inviteForm = "<form action='' method='post'>"    ."<font face='Verdana' color='red' size=6><b>Send Invitation to watch the video</b></font><br><br>"    ."<font face='Verdana' color='blue'><b>Recipient's Email: &nbsp;&nbsp;</b> <input type='text' name='email' size='50'><br>(Separated by comma)<br><br>"	."<b>Subject: &nbsp;&nbsp;</b> <input type='text' name='subject' value='$subject2' size='50'><br><br>"    ."<b>Message: </b><br><textarea name='message' cols='70' rows='10'>". $message2. "</textarea> </font><br>"    ."<input type='submit' style='color: red; font-family: Verdana; font-weight: bold; font-size: 16px; ' value='Invite'> </form>";// If $email is not set, $message is empty so as $youremail, then show the formif(!isset($email)){echo $inviteForm;}// If $youremail is empty then show the errorelseif(empty($youremail)){die("Your unique identifier is missing");}elseif(empty($email)){echo $inviteForm;echo "<font color='red' size=5>Please enter the Recipient's Email </font>";}elseif(empty($subject)){echo $inviteForm;echo "<font color='red' size=5>Please enter the subject! </font>";}// If $message is empty then show the errorelseif(empty($message)){echo $inviteForm;echo "<font color='red' size=5>Please enter the message! </font>";}else{$send = sendInvite($email,$subject,$message);// If we can not send this email let's show the errorif(!$send){echo "Can not send your invitation!";}// If this invitation was sent then let's show that "Invitation sent" messageelse{echo "<font color='blue' size=6>Invitation has been sent to ". $email . ". Now you can close this window</font>";}}?> <?phpfunction sendInvite($to, $subject,$message){$headers  = 'MIME-Version: 1.0' . "\r\n";$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";$headers .= 'From: CALTTubes@insead.edu' . "\r\n" .    'Reply-To: CALTTubes@insead.edu' . "\r\n" .    'X-Mailer: PHP/' . phpversion();		$val=mail($to, $subject, $message, $headers);	return $val;}?>