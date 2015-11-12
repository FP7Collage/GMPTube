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


<?phpheader("Content-type: text/xml");include "conntube.php";	// Gather info into variables$subject = "Your Password for" . $full_tubename;$vURL = $full_tubeurl; $email = $_REQUEST['email'];$passValue = getPassword($email);if(($passValue=="") or ($passValue==null)){$retMessage = "NotRegistered";}else{$retMessage = "Success";$message = "Hi, You have requested for the password for " . $full_tubename .".Here are the details.";$message = $message . "<br><br>" . $vURL;$message = $message . "<br>" . "Username: " . $email;$message = $message . "<br>" . "Password/Key: " . $passValue;$send = sendPassword($email,$subject,$message);// If we can not send this email let's show the error}$retString =  "<rsp><entity>$email</entity><message>$retMessage</message></rsp>" ;echo $retString;?> <?phpfunction getPassword($email){$query 		= 	"SELECT ikey FROM keystable where userid ='$email'";$result 	= 	mysql_query ($query);$row 		= 	mysql_fetch_array($result);$val = $row['ikey'];return $val;}function sendPassword($to, $subject,$message){$headers  = 'MIME-Version: 1.0' . "\r\n";$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";$headers .= 'From: CALTTubes@insead.edu' . "\r\n" .    'Reply-To: pkmittal82@gmail.com' . "\r\n" .    'X-Mailer: PHP/' . phpversion();		$val=mail($to, $subject, $message, $headers);	return $val;}?>