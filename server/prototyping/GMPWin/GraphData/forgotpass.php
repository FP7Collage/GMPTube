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


<?phpinclude "conntube.php";	// Gather info into variables$subject = "Your Password for " . $full_tubename;$vURL = $full_tubeurl; $forgotForm = "<form action='' method='post'>"    ."<font face='Verdana' color='red' size=6><b>Forgot Password</b></font><br><br>"    ."<font face='Verdana' color='blue'><b>Enter your email address: &nbsp;&nbsp;</b> <input type='text' name='email' size='50'><br><br>"	."<input type='submit' style='color: red; font-family: Verdana; font-weight: bold; font-size: 16px; ' value='Submit'> </form>";// If $email is not set, $message is empty so as $youremail, then show the formif(!isset($_REQUEST['email'])){echo $forgotForm;}/*if(empty($email)){echo "<font color='red' size=5>Please enter the email address</font>";}*/else{$email = $_REQUEST['email'];$passValue = getPassword($email);if(($passValue=="") or ($passValue==null)){echo $forgotForm;echo "<font color='red' size=3>Your email address is not registered. Please contact the administrator on pkmittal82@gmail.com if you have any difficulties.</font>";}else{$message = "Hi, You have requested for the password for " . $full_tubename .".Here are the details.";$message = $message . "<br><br>" . $vURL;$message = $message . "<br>" . "Username: " . $email;$message = $message . "<br>" . "Password/Key: " . $passValue;$send = sendPassword($email,$subject,$message);// If we can not send this email let's show the errorif(!$send){echo "Error in sending email!";}// If this invitation was sent then let's show that "Invitation sent" messageelse{echo "<font color='blue' size=6>Your password has been sent to ". $email . ". Now you can close this window</font>";}}}?> <?phpfunction getPassword($email){$query 		= 	"SELECT ikey FROM keystable where userid ='$email'";$result 	= 	mysql_query ($query);$row 		= 	mysql_fetch_array($result);$val = $row['ikey'];return $val;}function sendPassword($to, $subject,$message){$headers  = 'MIME-Version: 1.0' . "\r\n";$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";$headers .= 'From: CALTTubes@insead.edu' . "\r\n" .    'Reply-To: pkmittal82@gmail.com' . "\r\n" .    'X-Mailer: PHP/' . phpversion();		$val=mail($to, $subject, $message, $headers);	return $val;}?>