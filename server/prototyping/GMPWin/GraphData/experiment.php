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

IamtheAgent("Rakesh.lalwani@insead.edu");

function IamtheAgent($user){
	
	 $arrayotherusers=array();;	
	$usercount 			= '0';
		
	$query 		= 	"SELECT id,lastaccessed FROM peoplenodes where  id !='$user'";
	$result 	= 	mysql_query ($query);
	echo $query;
	echo '</br>';
	$otherloggedinusers = array();
	while ($row = mysql_fetch_array($result)){
        
		$timequery  = "SELECT UNIX_TIMESTAMP('$row[1]') from peoplenodes";    //this is providing lastaccess time for $row[1]
		echo $timequery;
		$timeresult = mysql_query ($timequery);
	    $timerow    = mysql_fetch_array($timeresult);	
		echo $timerow;
		echo '</br>';
		echo time();
		
		echo "				".$timerow[0];
		echo '</br>';
		if ((time() - $timerow[0]) < 600){
			array_push($otherloggedinusers,$row[0]);
			echo "User added...hahahahahaha".$row[0];
			echo '</br>';
		}
	}
	
	
	/* the number of logges in users can be 
		0	-	change status to DENIED
		1	-	change "invite" field of that user in peoplenodes to ("INVITE",opponentname)			
		>1	-	call the user matching function
	*/
	
	$noofloggedinusers = sizeof($otherloggedinusers);
	echo "no. of logged in users =".$noofloggedinusers.'</br>';
	if ( $noofloggedinusers == 0 ){
	    echo " no of logged in users =0 here </br>";
		mysql_query ( "UPDATE g1_players SET status = 'DENIED' where playerid ='$user'" );
		mysql_query ( "UPDATE peoplenodes SET invite = '' where id ='$user'" );
		exit;
	}
	elseif($noofloggedinusers == 1){
		$otheruser 	= 	$otherloggedinusers[0];
		$invitefld	=	'INVITED,'.$user;
		$query 		= 	"UPDATE peoplenodes SET invite = '$invitefld' where id ='$otheruser' and invite = ''";
			
		mysql_query ($query) or die(mysql_error());
		sleep(15);
			
		$checkquery 	= 	"SELECT invite FROM peoplenodes where id ='$otheruser'";
			
		$checkresult 	= 	mysql_query($checkquery) or die(mysql_error());
		$Crow 			= 	mysql_fetch_array($checkresult);
		
		// invite field can be the same or NOT ACCEPTED
		// in both cases, we set "DENIED" to the 1st players "Status" field in g1_players
		// set "" to opponent player's "invite" field in peoplenodes
			
		if( ($Crow[0] == 'NOT ACCEPTED') || ($Crow[0] == $invitefld) ) {
			
			$updatequery = "UPDATE peoplenodes SET invite = '' where id ='$otheruser'";
			mysql_query ($updatequery) or die(mysql_error());
			
			$updatequery = "UPDATE peoplenodes SET invite = '' where id ='$user'";
			mysql_query ($updatequery) or die(mysql_error());
		    
			$updatequery = "UPDATE g1_players SET status = 'DENIED' where playerid ='$user'";
			mysql_query ($updatequery) or die(mysql_error());
			exit;
		}
		exit;
	}
	
	// number of logged in users > 1
	
	for($j=0;$j<sizeof($otherloggedinusers);$j++){	
		$arrayotherusers[$usercount][0]= $otherloggedinusers[$j];	
		$arrayotherusers[$usercount][1]= 0;
		compareusers($user,$otheruser,$usercount);
		$usercount= $usercount + 1;
	}
		
	findbestuser($user);
}


?>







