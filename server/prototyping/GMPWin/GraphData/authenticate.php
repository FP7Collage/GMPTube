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
	include "conntube.php";
	
	$username 	= $_REQUEST['username'];
	$typed_key 	= $_REQUEST['password'];
	$usrs = mysql_query("SELECT ikey, userid FROM keystable WHERE userid='$username'");
	if(mysql_num_rows($usrs) == 0) {
		showmessage('UserDoesNotExist', '');
		exit;
	}
	
	$row = mysql_fetch_array($usrs);
	$username = $row['userid'];
	
	if(isset($_REQUEST['useKey']) and $_REQUEST['useKey'] == 'true') {
		$constants = simplexml_load_file('constants.xml');
		$key = $constants -> key;
		if($key == '') $key = 'lab1'; // load default key if not given in constants.xml
		if($typed_key == $key) {
			$value = 'Success';
	        updatelog();		
			
		} else {
			$value = 'CheckKey';
		}
	} else {
		$key = $row['ikey'];
		if($typed_key == $key) {
			$value = 'Success';
			updatelog();
			
		} else {
			$value = 'CheckPass';
		}
	}
	showmessage($value, $username);
	exit;
	
	function showmessage($value, $username)
	{
		echo "<rsp><message>$value</message><userid>$username</userid></rsp>";
	}       
   
    function updatelog(){
	$actiontype = 	'Login';
	$takenby 	= 	$_REQUEST['username'];;
	$takenon 	= 	'';
	$entity     = '';
	
	mysql_query ("UPDATE peoplenodes set loggedin=TRUE, lastvisit=current_timestamp(), timesvisited=timesvisited+1, lastaccessed=current_timestamp(), invite='' WHERE id='$takenby'");
    mysql_query ("UPDATE chatusers set loginstatus=true, accesstime=current_timestamp() where id='$takenby'") or die(mysql_error());
     mysql_query ("INSERT INTO useractions(action,TakenBy,TakenOn,Entity,Datetime) VALUES ('$actiontype','$takenby','$takenon','$entity',current_timestamp())");
	

	}
?> 