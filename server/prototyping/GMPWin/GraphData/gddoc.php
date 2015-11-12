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
require_once './../../google-api-php-client/src/Google_Client.php';
require_once './../../google-api-php-client/src/contrib/Google_DriveService.php';
require_once "./../../google-api-php-client/src/contrib/Google_Oauth2Service.php";

$DRIVE_SCOPE = 'https://www.googleapis.com/auth/drive';
$SERVICE_ACCOUNT_EMAIL = '550363306290-edbkvt1lg0vulhv4716k1fn7f6svf1eb@developer.gserviceaccount.com';
$SERVICE_ACCOUNT_PKCS12_FILE_PATH = './../../google-api-php-client/c8dd2312e38f0062842697ea245fe32002a7d561-privatekey.p12';


// if the google doc link already exist, if not then create the new google doc, and set the link into the database and redirect to open it
// else redirect to open it 
include "conntube.php";

$youremail = $_REQUEST['youremail'];
$vid = $_REQUEST['vid'];
$headURL = "";

if ($vid == "") {
echo "The video ID is not found";
exit;
}

$query 		= 	"SELECT * FROM videonodes where id ='$vid'";
$result 	= 	mysql_query ($query);
$row 		= 	mysql_fetch_array($result);
$gdurl 	= 	$row['gdurl'];
$vidName = $row['name'];

if (($gdurl != null) or ($gdurl != ""))  {
    // Set the URL to which it has to be redirected.
	$headURL = $gdurl;

}else{


//Build the Drive Service object authorized with your Service Account
$auth = new Google_AssertionCredentials(
    $SERVICE_ACCOUNT_EMAIL,
    array( $DRIVE_SCOPE ),
    file_get_contents($SERVICE_ACCOUNT_PKCS12_FILE_PATH)
);
$client = new Google_Client();
$client->setUseObjects( true );
$client->setAssertionCredentials( $auth );
$service = new Google_DriveService( $client );

//Create the file
$file = new Google_DriveFile();
$file->setTitle($vidName);
//$file->setDescription("Please feel free to write to this document and collaborate");
// set the default description of the document

$file->setMimeType( 'application/vnd.google-apps.document' );
$file = $service->files->insert( $file );

//Give everyone permission to read and write the file
$permission = new Google_Permission();
$permission->setRole( 'writer' );
$permission->setType( 'anyone' );
$permission->setValue( 'me' );
$permission->setwithLink( true);
$service->permissions->insert( $file->getId(), $permission );

$headURL = $file->getalternateLink();

mysql_query ("UPDATE videonodes set gdurl='$headURL' WHERE id='$vid'") or die(mysql_error());		

//echo $file->getalternateLink();
//print_r( $file);

} // end of gdurl 
  echo "<br> <font color =\"red\" size = \"5\" > If the page is not redirected automatically then please click <a href=\"". $headURL ."\">here</a> to open the document </font>";
  $hstr ="Location: " . $headURL; 
  header($hstr);
/* Make sure that code below does not get executed when we redirect. */
//exit;

?>