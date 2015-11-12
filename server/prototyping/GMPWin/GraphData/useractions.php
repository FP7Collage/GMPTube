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
	
	$action = $_POST['action'];
	
	
	switch($action)
	{
		case 'updatelog':
		updatelog();
		break;
		
		case 'deletelog':
		deletelog();
		break;
		
		case 'changelog':
		changelog();
		break;
		
		echo 'Thats the best part of it!!';
		break;
	}


?>

<?php
function updatelog()
{
	$actiontype = 	$_POST['actiontype'];
	//$takenby 	= 	$_POST['takenby'];
	//$takenon 	= 	$_POST['takenon'];
      $takenby	= isset ($_POST['takenby']) ? $_POST['takenby'] : '';
	  $takenon	= isset ($_POST['takenon']) ? $_POST['takenon'] : '';
	
	
//	$entity 	= 	$_POST['entity'];
	$entity     = "";
	//$actiontype = 'Logout';
	//$takenby 	= 'albert.angehrn@insead.edu';
	//$takenon 	= 'tentube';
	
	
	/*
		changes that needs to happen:
		for the login user	:
			set loggedin field to true
			lastvisit, lastaccessed time to now()
			timesvisited++
		for the logoff user	:
			 set loggedin to false
			 invite field to ""
			 status in g1_players to "ENDSESSION"	-	if the player logs off in the middle of some game, with ENDSESSION
			 	status, we can manage things. 
	*/
	if ( $actiontype == 'Login'){		
		mysql_query ("UPDATE peoplenodes set loggedin=TRUE, lastvisit=current_timestamp(), timesvisited=timesvisited+1, lastaccessed=current_timestamp(), invite='' WHERE id='$takenby'");
		mysql_query ("UPDATE chatusers set loginstatus=true, accesstime=current_timestamp() where id='$takenby'") or die(mysql_error());
	}
	elseif ( $actiontype == 'Logout' ){
		mysql_query ("UPDATE peoplenodes set loggedin=FALSE WHERE id='$takenby'") or die(mysql_error());		
		mysql_query ("UPDATE peoplenodes set invite='' WHERE id='$takenby'") or die(mysql_error());		
		mysql_query ("UPDATE g1_players set status='ENDSESSION' WHERE playerid='$takenby'") ;
		mysql_query ("UPDATE chatusers set loginstatus=false where id='$takenby'") or die(mysql_error());
	}
	//error_log($actiontype);
	//error_log($takenby);
	//error_log($takenon);
	//error_log($entity);
	//error_log(current_timestamp());
	mysql_query ("INSERT INTO useractions(action,TakenBy,TakenOn,Entity,Datetime) VALUES ('$actiontype','$takenby','$takenon','$entity',current_timestamp())");
	
}

?>


<?php
function changelog()
{
	$actionoldtype = $_POST['actiontype'];
	$actionnewtype = $_POST['actionnewtype'];
	$takenby = $_POST['takenby'];
	$takenon = $_POST['takenon'];
	$entity = $_POST['entity'];
	
	// we just change the old action type to the new one.
	// for the moment its used only to change Is playing action to Has accessed action, when the user 'Stops' the video
	// by clicking stop button ( if the user closed the application the function is not called ) :)
	$query = "UPDATE useractions SET action='$actionnewtype' WHERE action='$actionoldtype' AND TakenBy='$takenby' AND TakenOn='$takenon'";
	mysql_query ($query);

}

?>


<?php
function deletelog()
{
	// delete log action for is watching edge
	$actiontype 	= 	$_POST['actiontype'];
	$fromID 		= 	$_POST['takenby'];
	
	if ($actiontype == 'Is watching' ){
		$t_rs		= mysql_query ("SELECT id FROM useractions WHERE action='$actiontype' AND TakenBy='$fromID'");
		$row 		= mysql_fetch_array($t_rs);
		//$target_id 	= $row['id'];
		
		foreach($row as $target_id ){
			mysql_query("DELETE FROM useractions WHERE Id= $target_id");
		}
		exit;
	}
	
	$toID		= $_POST['takenon'];
	$t_rs		= mysql_query ("SELECT id FROM useractions WHERE action='$actiontype' AND TakenBy='$fromID' AND TakenOn='$toID'");
	$row 		= mysql_fetch_array($t_rs);
	$target_id 	= $row['id'];
	
	mysql_query("DELETE FROM useractions WHERE Id= $target_id");
	
/*	mysql_query("DELETE FROM useractions WHERE Id= $target_id");
	if ( $actiontype == "Is watching"){
		$t_rs=mysql_query ("SELECT tooltip FROM edgetypes where name='Has seen'");
		$row = mysql_fetch_array($t_rs);
		$tooltip = $row[0];
			
		$t_rs=mysql_query ("SELECT intensity FROM edgetypes where name='Has seen'");
		$row = mysql_fetch_array($t_rs);
		$intensity = $row[0];
		
		$t_rs=mysql_query ("SELECT edgecolor FROM edgetypes where name='Has seen'");
		$row = mysql_fetch_array($t_rs);
		$edgecolor = $row[0];

		mysql_query ("INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor) VALUES ('Has seen','$tooltip','$fromID','$toID','$intensity','$edgecolor')");
	}
	*/
	
}
?>