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
header("Content-type: text/xml");

global $retString;

include 'conntube.php';
session_start();
	
$retString= "<?xml version=\"1.0\" encoding=\"utf-8\"?>";  
$message1 = "";
$message2 = "";
$message3 = "";
$randomstr ="";
$newpicture = "";

$randomstr	 		=	$_REQUEST['randomstr'];
$emailid  =  $_REQUEST['emailid'];

// Start of uploading snapshot file
	$imagefilename	=	'./media/people/'.$randomstr.'.jpg';
   if($_FILES['image']['error']!= UPLOAD_ERR_OK){
//		echo 'Failed at point 1';
         $message1 = "People Thumnnail is not found";
		} 
		if(move_uploaded_file($_FILES['image']['tmp_name'], $imagefilename)){
  //       echo "Success1" . "\n";
          $message1 = "Success1";
		 }
  else{
         //echo 'There was an error during the image upload.  Please try again.'; // It failed :(.
		 $message1 = "There was an error during the thumbnail upload. Please try again.";
		 
		 }

         $newpicture = str_replace(basename(selfURL()), 'media/people/'.$randomstr.'.jpg', selfURL());
		 $query = " UPDATE PEOPLENODES SET picture = '$newpicture' WHERE id = '$emailid' " ; 
			//error_log("titleee" . $query);
			mysql_query ($query) or die("MYSQL ERROR: ".mysql_error());
	
	$retString = $retString . "<rsp><message1>$message1</message1></rsp>" ;
		
		echo $retString;
	
	

?>


<?php
function selfURL() 
{ 
$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : ""; $protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s; 
$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]); return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];

} 

function strleft($s1, $s2)
{
 return substr($s1, 0, strpos($s1, $s2)); 
}

?>