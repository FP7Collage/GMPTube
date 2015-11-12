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
// the next 2 lines to prevent caching no empty lines
//session_start();
header("Cache-control: private");
header("Content-type: text/xml");
$retstring= "<?xml version=\"1.0\" encoding=\"utf-8\"?>";  

//$username 	= $_REQUEST['userid'];
// $password 	= $_REQUEST['key'];

$username 	= isset($_REQUEST['userid'])? $_REQUEST['userid'] : $_POST['userid'] ;
$password 	= isset($_REQUEST['key'])? $_REQUEST['key'] : $_POST['key'] ;


//$username 	= $_POST['userid'];
//$password 	= $_POST['key'];


$retstring= $retstring ." <rsp>";

if (($username  == "") || ($username == null)){
  $retstring= $retstring .  "<error>Userid parameter is missing </error>";
}else if (($password  == "") || ($password == null)){
   $retstring= $retstring .  "<error>Key/password parameter is missing </error>";
} else if (($password == "demo") && ($username=="demo@demo.com")){
$authvalue = true;
} else {
$authvalue = false;
$retstring= $retstring .  "<error>User name and password does not match</error>";
}

if ($authvalue){

$retstring= $retstring .  "<game>";
$retstring= $retstring .  "<gameid>game1</gameid>";
$retstring= $retstring .  "<playerid>player1</playerid>";
$retstring= $retstring .  "<status>playing</status>";
$retstring= $retstring .  "<wordstyped>word1,word2</wordstyped>";
$retstring= $retstring .  "<lastaccessedtime>2014-26-01 20:00:20</lastaccessedtime>";
$retstring= $retstring .  "</game>";

$retstring= $retstring .  "<game>";
$retstring= $retstring .  "<gameid>game1</gameid>";
$retstring= $retstring .  "<playerid>player2</playerid>";
$retstring= $retstring .  "<status>waiting</status>";
$retstring= $retstring .  "<wordstyped></wordstyped>";
$retstring= $retstring .  "<lastaccessedtime>2014-26-01 19:00:20</lastaccessedtime>";
$retstring= $retstring .  "</game>";

}

$retstring=$retstring ."  </rsp>";
echo $retstring;

?>