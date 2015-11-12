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
	header("Cache-Control: private");
	header("Content-type: text/xml");
	
	include "conntube.php";
	
	$prefix = '';
	if(isset($_POST['prefix']))
		$prefix = $_POST['prefix'];
	
	$isRandom = true;
	$randomString = strtoupper(substr(md5($prefix . uniqid(rand(), true)), rand(0, 22), 10));
	
	/*
	do {
		//$randomString = strtoupper(substr(md5($prefix . uniqid(rand(), true)), rand(0, 22), 10));
		$urlResult = mysql_query("SELECT url FROM videonodes", $conn);
		while($url = mysql_fetch_row($urlResult)) {
			if(strpos($url[0], $randomString)) {
				$isRandom = false;
				break;
			}
		}
	} while(!$isRandom);
	*/
	
	echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
	echo "<rsp><name>" . $randomString . "</name></rsp>";
	
?>