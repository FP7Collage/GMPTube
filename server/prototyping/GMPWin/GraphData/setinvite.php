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
	
	if(isset($_REQUEST['user']) && isset($_REQUEST['message']) ){
	  	$user		=	$_REQUEST['user'];  
	  	$message 	=	$_REQUEST['message'];
	} 
	//$query = "UPDATE peoplenodes SET invite='$message' where id ='$user'";
	//mysql_query ($query) or die(mysql_error());
	
//	error_log("the MESSAGE IS ".$message);	
	if($message  == 'NOT ACCEPTED'){
	changeupdates($user);
//	error_log("the users are ".);	
	}
	mysql_close($conn);

?>

<?php 
function changeupdates($user)
{

	$query 		= 	"SELECT invite FROM peoplenodes where id ='$user'";
	
	$result 	= 	mysql_query ($query);
	$row 		= 	mysql_fetch_array($result);
	$invite 	= 	$row['invite'];
	
	$t_array = explode(',',$invite);
	if ( $t_array[0] == 'INVITED'){
		$opponentid = $t_array[1];
		
		$updatequery = "UPDATE peoplenodes SET invite = '' where id ='$user'";
		mysql_query ($updatequery) or die(mysql_error());
		
		$updatequery = "UPDATE g1_players SET status = 'DENIED' where playerid ='$opponentid'";
		mysql_query ($updatequery) or die(mysql_error());
		
			
		$updatequery = "UPDATE peoplenodes SET invite = '' where id ='$opponentid'";
		mysql_query ($updatequery) or die(mysql_error());
	}
	
     // We want to get the user
	  
	//error_log("the users are ".$otheruser.','.$userid );	
		
		    
			
		
}
