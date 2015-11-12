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
	
	$s_message	=	'';
	$s_entity	=	'';
	
	$user 		= 	$_REQUEST['user'];
	mysql_query ("UPDATE peoplenodes SET lastaccessed= current_timestamp() where id = '$user'");
	
	//$user = 'vijay.babu@insead.edu';
	
	$query 		= 	"SELECT invite FROM peoplenodes where id ='$user' and id <> 'anonymous@gmail.com'";
	
	$result 	= 	mysql_query ($query);
	$row 		= 	mysql_fetch_array($result);
	$invite 	= 	$row['invite'];
	
	// the invite field can have one of the following values
	// ""					-	empty
	// "INVITED,USERNAME"	-	a invite to play with user with id:USERNAME


	$t_array = explode(',',$invite);
	if ( $t_array[0] == 'INVITED'){
		$s_message	=	'INVITED';
		$s_entity	=	$t_array[1];
		sendMessage();
	}
	else {
		$s_message = 'EMPTY';
		sendMessage();
	}
	
	mysql_close($conn);
	
	exit;
		

	
	
		function sendMessage(){	
			global $s_message,$s_entity;
			echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";		
		    echo "<rsp>";
		    echo "<message>$s_message</message>";
		    echo "<opponent>$s_entity</opponent>";
		    echo "</rsp>";
		}


?>