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
// the next 2 lines to prevent caching
//session_start();
header("Cache-control: private");

	include "conntube.php";
	
	//callvideoagent('albert.angehrn@insead.edu','nicholas.leck@insead.edu');
	
	
	//		g1_players, g2_gameadmin, g3_gamelog, g4_taboowords
	
	$action 		= $_REQUEST['action'];
	
	// These variables are sent from server to client on every Routine request.
	$s_message			=	'';
	$s_videoid			=	'';
	$s_taboo			=	'';
	$s_sessionscore		=	'';	
	$s_opponentstatus	=	'';
	$s_entity			=	'';

	switch($action)
	{
		case 'PLAY':			// a user wants to play
			newplayer();
			break;
		
		case '2NDPLAYER':			// a user wants to play
			_2ndplayer();
			break;
			
		//case 'OPPONENTFOUND':	// from CA 
		//	opponentfound();
		//	break;
		
		//case 'VIDEOFOUND':		// from VA 
		//	videofound();
		//	break;
			
		case 'NEWWORD':			// any of the user types a new word
			newword();
			break;	
			
		case 'PASS':			// player passes the video
			videopass();
			break;	
		
		case 'VIDEOOVER':		// video gets over for one of the players
			videoover();
			break;	
		
		case 'FORCEPASS':
			forcepass();	
				break;
		case 'QUITGAME':
		     quitgame();
			 break;
						
		case 'ROUTINE':			// routine request from client for update of score, new messages, etc..
			routinecheck();
			break;	
		
		//case 'CLEARRECORD':		
		//	clearrecord();
		//	break;	
			
		default:
			//echo 'Just one more step!';
			break;
	
	}

  mysql_close($conn);


				
				
				

/*		<user userid=”player_a” action=”PLAY”/>	

	*	create a new row for the user in g1
	*	call CA with the user id
	*	send a 'Wait' message to client, from now onwards till the end of game, the client sends a 'routine'
			request every 2 seconds
	
*/

function newplayer()
{
	global $s_message;
	global $otherplayer;
//	$userid		=	'albert.angehrn@insead.edu';
	$userid 		= 	$_POST['userid'];
	$otherplayer	=   $_POST['secondplayer'];
	$count_players	=	$_POST['count'];
	
	mysql_query ( "DELETE FROM g1_players WHERE playerid = '$userid'");
	mysql_query ( "INSERT INTO g1_players(playerid,status,gameid,wordstyped,wordstime,sessionscore,lastaccessedtime) VALUES ('$userid','WAITING','','','','0',current_timestamp())") or die(mysql_error());	
	mysql_query ( "UPDATE peoplenodes SET invite = 'PROCESSING' where id ='$userid'" );
	
	$s_message	=	'WAITING';
	sendMessage();
		
	//IamtheAgent('nicholas.leck@insead.edu');
	//IamtheAgent($userid);		// connection agent
	
	if($count_players==1)
	{
		error_log("The i am the connecting agent");
		IamtheConnectingAgent($userid);
	}
	else
	{
			error_log("the IamtheAgent is");
			IamtheAgent($userid);
	}	
	//IamtheAgent($userid);
		
		//sendMessage();
	
}



function _2ndplayer(){
	global $s_message;
	
	$opponentid = 	$_POST['userid'];		// id of the 2nd player
	$userid 	= 	$_POST['opponentid'];	// id of the 1st player	( the one who initiated the play )
	
//	$opponentid			=	'vijay.babu@insead.edu';
//	$userid				=	'albert.angehrn@insead.edu';

	$query 		= 	"UPDATE peoplenodes SET invite='PLAYING' where id ='$opponentid'";
	$result 	= 	mysql_query ($query);
	
	$query 		= 	"UPDATE peoplenodes SET invite='PLAYING' where id ='$userid'";
	$result 	= 	mysql_query ($query);
	
	
	mysql_query ("DELETE FROM g1_players WHERE playerid = '$opponentid'");
	mysql_query ("INSERT INTO g1_players(playerid,status,gameid,wordstyped,wordstime,sessionscore,lastaccessedtime) VALUES ('$opponentid','WAITING','','','','0',current_timestamp())") or die(mysql_error());
	
	$s_message	=	'WAITING';
	sendMessage();
	
	callvideoagent($userid,$opponentid);
	//sendMessage();
		
}




/*		<internal userid=”player_b” opponentid=”player_a” />		
				
	*	CA sends this request. it sends the id's of 2 players.
	*	create a row for 2nd player 
	*	call the VA with the player id's
	
*/

function opponentfound()
{		
	$userid 		= 	$_POST['userid'];		// id of the 1st player
	$opponentid 	= 	$_POST['opponentid'];	// id of the 2nd player	

	//$opponentid 	='nicholas.leck@insead.edu';
	//	*	create a row for 2nd player 
	
	mysql_query ("INSERT INTO g1_players(playerid,status,gameid,wordstyped,wordstime,sessionscore,lastaccessedtime) VALUES ('$opponentid','WAITING','','','','0',current_timestamp())") or die(mysql_error());
	
	//	*	call VA	( $userid , $opponentid )

}






/*		<internal userid=”player_b” opponentid=”player_a” videod='Video6'/>		
				
	*	VA sends this request. it sends the id's of 2 players + the video selected
	*	create a game with player id's and video id. 
	*	change status of players to START
	
*/

function videofound(){
		
	$userid 		= 	$_POST['userid'];		// id of the 1st player
	$opponentid 	= 	$_POST['opponentid'];	// id of the 2nd player	
	$videoid		= 	$_POST['videoid'];		//	$_POST['videoid'];
	$wordlist="";
//	$userid 		= 	'jessica.nay@insead.edu';		// id of the 1st player
//	$opponentid 	= 	'nicholas.leck@insead.edu';		// id of the 2nd player	
//	$videoid		= 	'Video11';						//	$_POST['videoid'];
	
	$rs				=	mysql_query ("SELECT word FROM g4_taboowords where videoid = '$videoid' AND frequency>5");

	while ( $row = mysql_fetch_array($rs) ){
		$word		= 	$row['word'];
		$wordlist	=	$word.','.$wordlist;
	}
	
	$taboowords		=	substr($wordlist, 0, -1);  	// 	find the taboo words for $videoid
	
	$currenttime	=	date('d-m-Y h:s:m'); 	
	$new_game_id	= 	$userid.$currenttime.$opponentid;	
	$playerids		=	$userid.','.$opponentid;
	
	$query			=	"INSERT INTO g2_gameadmin(gameid,playerids,videoid,wordsmatched,gamescore,taboowords) VALUES ('$new_game_id','$playerids','$videoid','','0','$taboowords')";
//	echo $query;
	mysql_query ($query) or die(mysql_error());
	
	mysql_query ("UPDATE g1_players SET status='START',gameid='$new_game_id' where playerid = '$userid'");		
	mysql_query ("UPDATE g1_players SET status='START',gameid='$new_game_id' where playerid = '$opponentid'");		
	
	// function over
}






/*		<user userid=”player_a” action=”newtext” word=”air” time=”current video time” />	

	*	Store the word and check for a match ( YES- increase the points , NO- thread dies )
	
*/

function newword()
{
	
	$userid 	= 	$_POST['userid'];
	$newword 	= 	$_POST['word'];
	$time 		= 	$_POST['time'];
		
	/*
	$chk_dic	=	mysql_query ("SELECT id FROM dictionary where word = '$newword'");
	if ( mysql_num_rows($chk_dic) == 0 ){
		// the word types is not a dictionary word
		exit;
	}
	*/

	
//	$userid 	= 'nicholas.leck@insead.edu';
//	$newword 	= 'jes1';
//	$time 		= '8:50';

	$word_time 	= $newword.'>at>'.$time.',';
	$word_comma	= $newword.',';
	
	mysql_query ("UPDATE g1_players SET wordstime = CONCAT(wordstime,'$word_time') where playerid = '$userid'") or die(mysql_error());
	mysql_query ("UPDATE g1_players SET wordstyped = CONCAT(wordstyped,'$word_comma') where playerid = '$userid'") or die(mysql_error());
	
	// check for a match.
	// find the game id and 2nd player's id.
	$gameid		=	GetGameId($userid);
	
	increaseFrequency($gameid,$newword);
	
	$opponent 	= 	GetOpponentId($userid);
	
	$rs			=	mysql_query ("SELECT wordstyped FROM g1_players where playerid = '$opponent'");
	$row 		= 	mysql_fetch_array($rs);
	$typedwords = 	$row['wordstyped'];
	
	$wordslist = explode(',',$typedwords);		// in $wordslist the last word will be empty.
	foreach($wordslist as $old_word){	
		if ( $old_word == $newword ){
			$points = 	50;
						
			// increase score in g2 for the game id.
			mysql_query ("UPDATE g2_gameadmin SET gamescore = gamescore+$points where gameid = '$gameid'") or die(mysql_error());
			
			// increase score in g1 for both the players.
			mysql_query ("UPDATE g1_players SET sessionscore = sessionscore+$points where playerid = '$userid'") or die(mysql_error());
			mysql_query ("UPDATE g1_players SET sessionscore = sessionscore+$points where playerid = '$opponent'") or die(mysql_error());
			
			// add to words matched in G2 
			mysql_query ("UPDATE g2_gameadmin SET wordsmatched = CONCAT(wordsmatched,'$word_comma') where gameid = '$gameid'") or die(mysql_error());
						
		}
	}
	
	// function over.
}







/*		<user userid=”player_a” action=”PASS” time=”” /> 

	*	check if the status of other user is also PASS 
			YES - 	2. negative points
					3. dump information g1,g2->g3
					4. clear g1,g2
					5. call VA
			NO	-	thread dies 
	
*/

function videopass()
{
	
	$userid 	= 	$_POST['userid'];
	mysql_query ("UPDATE g1_players SET status = 'PASS' where playerid = '$userid'");
//	$userid 	= 	'nicholas.leck@insead.edu';

	$opponent 	= 	GetOpponentId($userid);
	$gameid 	= 	GetGameId($userid);
	
	$rs			=	mysql_query ("select status from g1_players where playerid = '$opponent'");
	$row 		= 	mysql_fetch_array($rs);
	$opp_status = 	$row['status'];
	
	if ( $opp_status == 'PASS'){
		//mysql_query ("UPDATE g1_players SET status = 'PASSAGREED' where playerid = '$userid'");
		//mysql_query ("UPDATE g1_players SET status = 'PASSAGREED' where playerid = '$opponent'");
		
		// 	game score-- in g2 for that game id
		$negative_points	=	20;
		mysql_query ("UPDATE g2_gameadmin SET gamescore = gamescore-$negative_points where gameid = '$gameid'") or die(mysql_error());
		
		// 	session score-- in g1 for both players
		mysql_query ("UPDATE g1_players SET sessionscore=sessionscore-$negative_points WHERE playerid='$userid'") or die(mysql_error());
		mysql_query ("UPDATE g1_players SET sessionscore=sessionscore-$negative_points WHERE playerid='$opponent'") or die(mysql_error());
		
		// store game information in G3 and update the fields in G1, G2		
        
    	   // $rs1				=	mysql_query ("SELECT playerids,videoid,wordsmatched,gamescore FROM g2_gameadmin where gameid = '$gameid'");
           // $row1 			= 	mysql_fetch_array($rs1);
           // $gamescore		=	$row['gamescore'];
		   // mysql_query ("UPDATE peoplenodes SET grandscore=grandscore+'$gamescore' where id = '$userid'");
           // mysql_query ("UPDATE peoplenodes SET grandscore=grandscore+'$gamescore' where id = '$opponent'");
		
		ExtractGist($gameid);
		
		callvideoagent($userid,$opponent);		// 	call VA ( $userid, $opponent )		
	}
	
	//	function over
}







/*		<user id=”player_b” action=”videoover” decision="ANOTHERGAME/PROFILEYES/ENDSESSION" /> 
	

	*	if the decision is ANOTHERGAME and the status(opponent) = ANOTHERGAME,
			call VA
	
*/

function videoover(){

	$userid 	= 	$_POST['userid'];
	$decision	= 	$_POST['decision'];
	$opponent 	= 	GetOpponentId($userid);
	
	$rs			=	mysql_query ("select status from g1_players where playerid = '$opponent'");
	$row 		= 	mysql_fetch_array($rs);
	$opp_status = 	$row['status'];
	
	error_log("the start of the main videooiver".$decision.','.$opponent.','.$opp_status );
	mysql_query ("UPDATE g1_players SET status='$decision' where playerid = '$userid'");
	if ( ($decision == 'ENDSESSION') && ( $opp_status == 'ENDSESSION' ) ){
	//if ( ($decision == 'ENDSESSION')  ){
		clearrecord($userid);
		// delete, clear everything	
		// no more messages will be sent from any of them.
		exit;
	}
	
	switch($decision){
		case 'ANOTHERGAME':
			switch($opp_status){
				case 'ANOTHERGAME':	
					$gameid 	= 	GetGameId($userid);
					ExtractGist($gameid);
					error_log("the video agent is called via anothergame");
					callvideoagent($userid,$opponent);	
					break;
				case 'PROFILEYES':	
					mysql_query ("UPDATE g1_players SET status='PROFILEINVITE' where playerid = '$userid'");
					break;	
				default:
				$gameid 	= 	GetGameId($userid);
				error_log("Calling default form video over");
				ExtractGist($gameid);
				error_log("the video agent is called via default");
				callvideoagent($userid,$opponent);	
				break;		
			}
		break;
		case 'PROFILEYES':		
			switch($opp_status){
				case 'ANOTHERGAME':	
					mysql_query ("UPDATE g1_players SET status='PROFILEINVITE' where playerid = '$opponent'");
					break;
				case 'PROFILEYES':	
					mysql_query ("UPDATE g1_players SET status='SHOWPROFILE' where playerid = '$userid'");
					mysql_query ("UPDATE g1_players SET status='SHOWPROFILE' where playerid = '$opponent'");
					break;
				default:
					mysql_query ("UPDATE g1_players SET status='PROFILEINVITE' where playerid = '$opponent'");
					break;
				
			}
		break;	
	}
	
}//end of  function videoover.




function forcepass()
{return;}
function quitgame(){


		$userid 	= 	$_POST['userid'];
		$opponent 	= 	GetOpponentId($userid);
		
       mysql_query ("UPDATE g1_players SET status='ENDSESSION' where playerid = '$userid'");
	   mysql_query ("UPDATE g1_players SET status='QUITGAME' where playerid = '$opponent'");
	   


}//end of function quit game



function clearrecord($userid){
	//	$userid 	= 	$_POST['userid'];
	//	$rs			=	mysql_query ("select status from g1_players where playerid = '$opponent'");
	//	$row 		= 	mysql_fetch_array($rs);
	//	$opp_status = 	$row['status'];
		
	//	if ( $opp_status != 'CLEAN' ){
	//		mysql_query ("UPDATE g1_players SET status='CLEAN' where playerid = '$userid'");
	//		exit;
	//	}	
		
		$opponent 	= 	GetOpponentId($userid);
		
		// store game information in G3 		
		$gameid		=	GetGameId($userid);
		ExtractGist($gameid);
		
		mysql_query ("UPDATE peoplenodes SET invite='' where id = '$userid'");
		mysql_query ("UPDATE peoplenodes SET invite='' where id = '$opponent'");
		
		mysql_query ("DELETE FROM g1_players WHERE playerid = '$userid'");
		mysql_query ("DELETE FROM g1_players WHERE playerid = '$opponent'");
		
//		$rs				=	mysql_query ("select sessionscore from g1_players where playerid = '$userid'");
//		$row 			= 	mysql_fetch_array($rs);
//		$sessionscore 	= 	$row['sessionscore'];
		
//		$rs				=	mysql_query ("select sessionscore from g1_players where playerid = '$opponent'");
//		$row 			= 	mysql_fetch_array($rs);
//		$sessionscore 	= 	$row['sessionscore'];
		
//		mysql_query ("DELETE FROM g2_gameadmin WHERE gameid = '$gameid'");
		
		
}






	/* 	
		*	routine request from client for update of score, new messages, etc..
		*	<user id=”player_a” action=”ROUTINE”/>		
	*/

function routinecheck()
{	
	
	global $s_message,$s_videoid,$s_taboo,$s_matched,$s_sessionscore,$s_opponentstatus,$s_entity;
	
	$userid 	= 	$_POST['userid'];
	$rs			=	mysql_query ("SELECT status FROM g1_players where playerid = '$userid'");
	$row 		= 	mysql_fetch_array($rs);
	$status 	= 	$row['status'];
	//error_log("routine check status".$userid.' '.$status );
	mysql_query ("UPDATE g1_players SET lastaccessedtime= current_timestamp() where playerid = '$userid'");
	
	switch($status){
		case 'WAITING':	
			//error_log("routine check waiting ".$userid );		
			$s_message 		= 'WAITING';
			
			sendMessage();
			break;	
		
		case 'START':	// send start, video id, taboo words, session score
		//error_log("routine check start ".$userid );
			mysql_query ("UPDATE g1_players SET status='PLAYING' where playerid = '$userid'");			
			$gameid			=	GetGameId($userid);
			$rs				=	mysql_query ("SELECT videoid,taboowords FROM g2_gameadmin where gameid = '$gameid'");
			$row 			= 	mysql_fetch_array($rs);			
			$s_message		=	'START';
			$s_videoid 		= 	$row['videoid'];
			$s_taboo		= 	$row['taboowords'];
			
			$rs				=	mysql_query ("SELECT sessionscore FROM g1_players where playerid = '$userid'");
			$row 			= 	mysql_fetch_array($rs);			
			$s_sessionscore	= 	$row['sessionscore'];
			//error_log("routine check start ".$userid );
			sendMessage();
			break;		
		
		case 'ANOTHERGAME':	
			//error_log("routine check another game ".$userid );		
			$s_message 			=	'ANOTHERGAME';
			$opponentid			=	GetOpponentId($userid);
			$rs					=	mysql_query ("SELECT status FROM g1_players WHERE playerid = '$opponentid'");
			$row 				= 	mysql_fetch_array($rs);
			$s_opponentstatus 	= 	$row['status'];	
			
			sendMessage();
			break;	

		case 'PROFILEYES':				
			//error_log("routine check profile eyes ".$userid );		
			$s_message 			=	'PROFILEYES';
			$opponentid			=	GetOpponentId($userid);
			$rs					=	mysql_query ("SELECT status FROM g1_players WHERE playerid = '$opponentid'");
			$row 				= 	mysql_fetch_array($rs);
			$s_opponentstatus 	= 	$row['status'];	
			sendMessage();
			break;
				
		case 'PROFILEINVITE':	
			//error_log("routine check profile invite ".$userid );		
			$s_message 			=	'PROFILEINVITE';
			$opponentid			=	GetOpponentId($userid);
			$rs					=	mysql_query ("SELECT status FROM g1_players WHERE playerid = '$opponentid'");
			$row 				= 	mysql_fetch_array($rs);
			$s_opponentstatus 	= 	$row['status'];	
			sendMessage();
			break;	
			
		case 'SHOWPROFILE':		
			//error_log("routine check showprofile ".$userid );
			$s_message 			=	'SHOWPROFILE';
			$s_entity			=	GetOpponentId($userid);					
			mysql_query ("UPDATE g1_players SET status='ENDSESSION' where playerid = '$userid'");		
			sendMessage();
			break;	
			
	//	case 'ENDSESSION':					
	//		$s_message 			= 	'ENDSESSION';
	//		sendMessage();
	//		break;	
							
		case 'PLAYING':		//return score and opponent's status, matched words	
			//error_log("routine check play ".$userid );
			$s_message			=	'PLAY';
			$opponentid			=	GetOpponentId($userid);
			$rs					=	mysql_query ("SELECT status FROM g1_players WHERE playerid = '$opponentid'");
			$row 				= 	mysql_fetch_array($rs);
			$s_opponentstatus 	= 	$row['status'];							

			$rs					=	mysql_query ("SELECT sessionscore FROM g1_players WHERE playerid = '$userid'");
			$row 				= 	mysql_fetch_array($rs);			
			$s_sessionscore		= 	$row['sessionscore'];
			
			$gameid				=	GetGameId($userid);
			$rs					=	mysql_query ("SELECT wordsmatched FROM g2_gameadmin WHERE gameid = '$gameid'");
			$row 				= 	mysql_fetch_array($rs);			
			$s_entity			= 	$row['wordsmatched'];		// matched words
			$s_entity			=	substr($s_entity, 0, -1);
			//error_log("routine check play ".$userid );
			sendMessage();	
			break;	
			
		case 'PASS':		//return score and opponent's status, matched words	
			//error_log("routine check pass ".$userid );
			$s_message			=	'PLAY';
			$opponentid			=	GetOpponentId($userid);
			$rs					=	mysql_query ("SELECT status FROM g1_players WHERE playerid = '$opponentid'");
			$row 				= 	mysql_fetch_array($rs);
			$s_opponentstatus 	= 	$row['status'];							

			$rs					=	mysql_query ("SELECT sessionscore FROM g1_players WHERE playerid = '$userid'");
			$row 				= 	mysql_fetch_array($rs);			
			$s_sessionscore		= 	$row['sessionscore'];
			
			$gameid				=	GetGameId($userid);
			$rs					=	mysql_query ("SELECT wordsmatched FROM g2_gameadmin WHERE gameid = '$gameid'");
			$row 				= 	mysql_fetch_array($rs);			
			$s_entity			= 	$row['wordsmatched'];		// matched words
			$s_entity			=	substr($s_entity, 0, -1);
			sendMessage();	
			break;	
			
		case 'QUITGAME':
            $s_message			=	'QUITGAME';		 
            sendMessage();  
			clearrecord($userid);
            
  		 break;
		
		case 'DENIED':				
			mysql_query ("DELETE FROM g1_players WHERE playerid = '$userid'");
			$s_message 			= 	'DENIED';
			sendMessage();
			break;	
			
		//	echo 'Just one more step! Life is like that !';
			
	}

	
	// function over
}














/*
	****		****		****			****			****		****			****				

		THE FUNCTIONS BELOW ASSIST THE IMPORTANT MODULES ABOVE
		
		List of functions implemented below:
		
		1. 	GetGameId($playerId)					returns		game id of player
		2. 	GetOpponentId($playerId)				returns		opponentid of a player
		3. 	clearg1g2($gameid)									
		4. 	ExtractGist($gameid)								stores game info in g3_gamelog 
		5. 	increaseFrequency($gameid,$newword)					increases freq in g4_taboowords 
		6.	sendMessage()										sends message to client
		
*/







/*
	*  	Input	-	Player id
	*	Returns	-	Game id
	*	Queries	-	G1
*/

function GetGameId($playerId)
{
	
	$rs			=	mysql_query ("SELECT gameid FROM g1_players where playerid = '$playerId'");
	$row 		= 	mysql_fetch_array($rs);
	$gameid 	= 	$row['gameid'];
	
	return $gameid;
}



/*
	*  	Input	-	Player id
	*	Returns	-	Opponent id 
	*	Queries	-	G1, G2 		
*/

function GetOpponentId($playerId)
{
	$gameid 	= 	GetGameId($playerId);
	
	$rs			=	mysql_query ("SELECT playerids FROM g2_gameadmin where gameid = '$gameid'");
	$row 		= 	mysql_fetch_array($rs);
	$players	= 	$row['playerids'];
	
	$playerlist = explode(',',$players);
	
	if ( $playerlist[0] == $playerId){
		$opponent = $playerlist[1];
	}
	else {
		$opponent = $playerlist[0];
	}
	return $opponent;
}



/*
	*  	Input	-	Game id
	*	Function-	Empties the contents in g1 and g2 corresponding to that game id
	*	Queries	-	G1, G2 		
*/

function clearg1g2($gameid){
	/* g1 - 	status=waiting		gameid=''		wordstyped=''	wordstime=''		*/
	
	$rs			=	mysql_query ("SELECT playerids FROM g2_gameadmin where gameid = '$gameid'");
	$row 		= 	mysql_fetch_array($rs);
	$players	= 	$row['playerids'];
	
	$playerlist = explode(',',$players);
	error_log("The players are".$playerlist);
	
	for( $i=0;$i<sizeof($playerlist);$i++){
		$playerid = trim($playerlist[$i]);
		mysql_query ("UPDATE g1_players SET status='WAITING',gameid='',wordstyped='',wordstime='' where playerid = '$playerid'");		
	}
	
	/*	g2 - 	wordsmatched=''		gamescore='0'		videoid=''		taboowords=''	*/
	
	mysql_query ("DELETE FROM g2_gameadmin WHERE  gameid = '$gameid'");
	//mysql_query ("UPDATE g2_gameadmin SET wordsmatched='',gamescore='0',videoid='',taboowords='' where gameid = '$gameid'");

	
	// function over
}






/*
	*  	Input	-	Game id
	*	Function-	Stores info from G1, G2 to G3. 
	*	Queries	-	G1, G2 		
*/


function ExtractGist($gameid)
{
	// 	check if the game id is already present in g3_gamelog.
	//	just a safety measure :) 	
	
	$rs	=	mysql_query ("SELECT * FROM g3_gamelog where gameid = '$gameid'");
	if ( mysql_num_rows($rs) != 0 ){
		error_log( "Error. Some serious error which is easy to rectify !" );
		exit;
	}
	
	$rs				=	mysql_query ("SELECT playerids,videoid,wordsmatched,gamescore FROM g2_gameadmin where gameid = '$gameid'");
	$row 			= 	mysql_fetch_array($rs);
	$playerids		=	$row['playerids'];
	$videoid		=	$row['videoid'];
	$wordsmatched	=	$row['wordsmatched'];
	$gamescore		=	$row['gamescore'];
	
	// increase the number of timesplayed field in videonodes table for this video id.
	$qu		=	"UPDATE videonodes SET timesplayed=timesplayed+1 where id = '$videoid'";
	//$filename = 'demo.txt';
	//$saveFile = fopen($filename, "w");
	//fwrite ($saveFile, $qu);
	//fclose($saveFile);
	mysql_query ($qu);
	
	
	$wordsmatched 	= substr($wordsmatched, 0, -1);
	
	$players_array 	= explode(',',$playerids);
	error_log("The players ids".$players_array);
	
	mysql_query ("UPDATE peoplenodes SET grandscore=grandscore+'$gamescore' where id = '$players_array[0]'");
	$rs					=	mysql_query ("SELECT wordstime FROM g1_players where playerid = '$players_array[0]'");
	$row 				= 	mysql_fetch_array($rs);
	$player1words_time	=	$row['wordstime'];
	
	mysql_query ("UPDATE peoplenodes SET grandscore=grandscore+'$gamescore' where id = '$players_array[1]'");
	$rs					=	mysql_query ("SELECT wordstime FROM g1_players where playerid = '$players_array[1]'");
	$row 				= 	mysql_fetch_array($rs);
	$player2words_time	=	$row['wordstime'];
	
	$player1words_time 	= 	substr($player1words_time, 0, -1);
	$player2words_time 	= 	substr($player2words_time, 0, -1);
	
	
	$query = "INSERT INTO g3_gamelog(gameid,playerids,player1words_time,player2words_time,wordsmatched,score,videoid,datetime) VALUES ('$gameid','$playerids','$player1words_time','$player2words_time','$wordsmatched','$gamescore','$videoid',current_timestamp())";
	mysql_query ($query) or die(mysql_error());
	
	clearg1g2($gameid);
	
	//	function over
	
}


/*
	*  	Input	-	Game id, word
	*	Function-	Increases the frequency of appearance that word with respect to the video
	*	Queries	-	G2,G4 		
*/

function increaseFrequency($gameid,$newword){
	$rs			=	mysql_query ("SELECT videoid FROM g2_gameadmin where gameid = '$gameid'");
	$row 		=	mysql_fetch_array($rs);
	$videoid	=	$row['videoid'];
	
	$rs			=	mysql_query ("SELECT frequency FROM g4_taboowords where videoid = '$videoid' AND word='$newword'");
	if ( mysql_num_rows($rs) == 0 ){
		mysql_query ("INSERT INTO g4_taboowords(videoid,word,frequency) VALUES ('$videoid','$newword','1')") or die(mysql_error());
		exit;
	}
	mysql_query ("UPDATE g4_taboowords SET frequency=frequency+1 WHERE videoid='$videoid' AND word='$newword'") or die(mysql_error());
	
}


	
function sendMessage(){
	// $s_entity will contain opponent user id, when message is Showprofile
	// $s_entity will have the matched words list when message is Play 
	
	global $s_message,$s_videoid,$s_taboo,$s_sessionscore,$s_opponentstatus,$s_entity;
	
	
	echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";		
    echo "<rsp>";
    echo "<message>$s_message</message>";
    echo "<videoid>$s_videoid</videoid>";
    echo "<taboo>$s_taboo</taboo>";
    echo "<sessionscore>$s_sessionscore</sessionscore>";
    echo "<opponentstatus>$s_opponentstatus</opponentstatus>";
    echo "<object>$s_entity</object>";
    echo "</rsp>";
}
	
function displayMessage($s_message,$s_videoid,$s_taboo,$s_sessionscore,$s_opponentstatus,$s_entity){
	// $s_entity will contain opponent user id, when message is Showprofile
	// $s_entity will have the matched words list when message is Play 
	
	//global $s_message,$s_videoid,$s_taboo,$s_sessionscore,$s_opponentstatus,$s_entity;
	
	
	echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";		
    echo "<rsp>";
    echo "<message>$s_message</message>";
    echo "<videoid>$s_videoid</videoid>";
    echo "<taboo>$s_taboo</taboo>";
    echo "<sessionscore>$s_sessionscore</sessionscore>";
    echo "<opponentstatus>$s_opponentstatus</opponentstatus>";
    echo "<object>$s_entity</object>";
    echo "</rsp>";
}

	


	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		
			
/*
 	****			TENTUBE GAME MAIN PHP FILE 			****

 * All requests from the client are sent to this php file
 * It seprates the request types and takes corresponding actions
 * First, the requests that can be sent
 
 1. User clicks play:   		
 						<user id=”player_a” action=”play” time=”” />
 
		a. Create a row(player_a) in G1 with status as WAITING
		b. return Wait 		<server message=”Wait”  />
		
		On the background,
		c. Invoke CA(player_a): It returns the opponent, player_b
		d. Create a row(player_b) in G1 with status as WAITING
		e. Invoke VA(player_a,player_b) : It returns video id, Video6
		f. Create a game 
		g. status(player_a), status(player_b) = START
		
		
		
 2. Routine message: 			
 						<user id=”player_a” action=”routine” />
 						
 		Sent every 2 seconds after clicking play till the end of the game. 
		
 		If status(player_a) in G1 == WAITING
			return WAITING		<server message=”WAITING”  />
		
		If status(player_a) in G1 == START
			return GAME INFO. 	<server message=”STARTVIDEO” video=”Video id” sessionscore=”0” taboowords=”w1,w2,w3” />
				** start the game with the video id.
			
		If status(player_a) in G1 == PLAYING,PASS	
			return GAME STATUS 	<server sessionscore=”150” taboowords=”w1,w2,w3” message=”opponent status” />
			
		
		If status(player_a) in G1 == PASSAGREED	
			return GAME STATUS 	<server sessionscore=”150” message=”PASSAGREED” />
				** stop video, alert the user that pass is agreed and new video being retrieved
			
		If status(player_b) in G1 == ANOTHERGAME,PROFILEYES,PROFILENO
			return GAME STATUS 	<server message=”status” />
				** in client side, we check the response of player_a, if it is also ANOTHERGAME,
				   then send a request with action=NEXTVIDEO
				** if status is profileyes,
			
				
 3. User types a word: 
 						<user id=”player_a” action=”newtext” word=”air” time=”current video time” />
 						
 		1. wordstyped(player_a) = concat (wordstyped(player_a),newword)
 		2. If match == true 
 			a.	Add the word to Wordsmatched/Time in G2 for this game id.
			b.	Calculate the points for this match 
			c.	Increase the ‘Session points’ in G2 for his game id.
			d.	Add the word to ‘Taboo  words’ in G2 for his game id

			

 4. User passes a video:
 						<user id=”player_a” action=”pass” time=”” /> 
 		a. status(player_a) = PASS
 
 		
 5. Opponent agrees for a pass:
 					 	<user id=”player_b” action=”passagreed” time=”” /> 
 		a. status(player_a), status(player_b) = PASSAGREED
 		b. session points-- 
 		c. dump information from G1, G2 into G3 . change the game id( in G2 ) too
 		d. wait for 5 seconds and then invoke VA(player_a, player_b)
 		e. update in G1, G2 with the new video

 
 6. Video gets over:
 		after video gets over, the player has 3 choices: (play another game,dont play another 
 		game but see profile, dont play another game and dont want to see profile )
 		By profile, i mean opponent's profile. ok.
 		
 		So, instead of sending 'VIDEOOVER' directly, we send ANOTHERGAME/PROFILEYES/PROFILENO
 		
 		<user id=”player_a” action=”anothergame” />		
 		1. dump information from G1, G2 into G3 . change the game id( in G2 ) too
		2. status(player_a) = ANOTHERGAME
		3. if status(player_b) != ANOTHERGAME && == PROFILEYES/PROFILENO
		
		END: OK. THIS PART IS LIIIIIITLE BIIIIIIT CONFUSING. 
		SO, LL TRY TO FINISH TILL THIS AND THEN THINK ABT THE REST. GUD.
 		
 */


?>
























<?php

function IamtheConnectingAgent($user){

		$otheruser1 	= 	$_POST['secondplayer'];
		//$otheruser1 ='shrikantsharat.k@gmail.com';
		$invitefld1	=	'INVITED,'.$user;
		$query 		= 	"UPDATE peoplenodes SET invite = '$invitefld1' where id ='$otheruser1' and invite = ''";
			
		mysql_query ($query) or die(mysql_error());
		//sleep(15);
			
		$checkquery 	= 	"SELECT invite FROM peoplenodes where id ='$otheruser1'";
			
		$checkresult 	= 	mysql_query($checkquery) or die(mysql_error());
		$Crow 			= 	mysql_fetch_array($checkresult);
		
		// invite field can be the same or NOT ACCEPTED
		// in both cases, we set "DENIED" to the 1st players "Status" field in g1_players
		// set "" to opponent player's "invite" field in peoplenodes
			
		if( ($Crow[0] == 'NOT ACCEPTED') || ($Crow[0] == $invitefld) ) {
			
			$updatequery = "UPDATE peoplenodes SET invite = '' where id ='$otheruser1'";
			mysql_query ($updatequery) or die(mysql_error());
			
			$updatequery = "UPDATE peoplenodes SET invite = '' where id ='$user'";
			mysql_query ($updatequery) or die(mysql_error());
		    
			$updatequery = "UPDATE g1_players SET status = 'DENIED' where playerid ='$user'";
			mysql_query ($updatequery) or die(mysql_error());
			exit;
		}
		exit;
}


function IamtheAgent($user){
	
	global $arrayotherusers;	
	$usercount 			= '0';
		
	$query 		= 	"SELECT id,lastaccessed FROM peoplenodes where  id !='$user' and id <> 'anonymous@gmail.com'";
	$result 	= 	mysql_query ($query);
	$otherloggedinusers = array();
	while ($row = mysql_fetch_array($result)){	
		$timequery  = "SELECT UNIX_TIMESTAMP('$row[1]') from peoplenodes";
		$timeresult = mysql_query ($timequery);
		$timerow    = mysql_fetch_array($timeresult);	
		
		if ((time() - $timerow[0]) < 600){
			array_push($otherloggedinusers,$row[0]);
		}
	}
	
	
	/* the number of logges in users can be 
		0	-	change status to DENIED
		1	-	change "invite" field of that user in peoplenodes to ("INVITE",opponentname)			
		>1	-	call the user matching function
	*/
	
	$noofloggedinusers = sizeof($otherloggedinusers);
	if ( $noofloggedinusers == 0 ){
		mysql_query ( "UPDATE g1_players SET status = 'DENIED' where playerid ='$user'" );
		mysql_query ( "UPDATE peoplenodes SET invite = '' where id ='$user'" );
		exit;
	}
	elseif($noofloggedinusers >= 1){
	//Shuffle the array
		shuffle($otherloggedinusers);
		error_log("the second user is ".$otherloggedinusers[0]);
		$otheruser 	= 	$otherloggedinusers[0];
		//Here noofloggedinusers is the size of the the ther loggedin  users.We need to handle the case if we want to play more than one game and when more number of users are playinsg
		$invitefld	=	'INVITED,'.$user;
		//$query 		= 	"UPDATE peoplenodes SET invite = '$invitefld' where id ='$otheruser' and invite = ''";
		$query 		= 	"UPDATE peoplenodes SET invite = '$invitefld' where id ='$otheruser'";
			
		mysql_query ($query) or die(mysql_error());
		//sleep(15);
			
		/*
		$checkquery 	= 	"SELECT invite FROM peoplenodes where id ='$otheruser'";
			
		$checkresult 	= 	mysql_query($checkquery) or die(mysql_error());
		$Crow 			= 	mysql_fetch_array($checkresult);
		*/
		
		// invite field can be the same or NOT ACCEPTED
		// in both cases, we set "DENIED" to the 1st players "Status" field in g1_players
		// set "" to opponent player's "invite" field in peoplenodes
			
		/*
		if( ($Crow[0] == 'NOT ACCEPTED') || ($Crow[0] == $invitefld) ) {
			
			$updatequery = "UPDATE peoplenodes SET invite = '' where id ='$otheruser'";
			mysql_query ($updatequery) or die(mysql_error());
			
			$updatequery = "UPDATE peoplenodes SET invite = '' where id ='$user'";
			mysql_query ($updatequery) or die(mysql_error());
		    
			$updatequery = "UPDATE g1_players SET status = 'DENIED' where playerid ='$user'";
			mysql_query ($updatequery) or die(mysql_error());
			exit;
		}
		*/
		exit;
		
	}
	
	// number of logged in users > 1
	
	/* for($j=0;$j<sizeof($otherloggedinusers);$j++){	
		$arrayotherusers[$usercount][0]= $otherloggedinusers[$j];	
		$arrayotherusers[$usercount][1]= 0;
		compareusers($user,$otheruser,$usercount);
		$usercount= $usercount + 1;
	}
		
	findbestuser($user); */
}


?>









<?php

function compareusers($user,$otheruser,$usercount)
{

global $arrayotherusers;
$query = "SELECT toID FROM edges where name = 'Has seen' and fromID='$user'";
$result = mysql_query ($query) or die(mysql_error());

while ($row = mysql_fetch_array($result))
{
$video = $row[0];
$query = "SELECT timesseen FROM edges where name ='Has seen' and fromID ='$otheruser' and toID='$video'";
$res = mysql_query ($query) or die(mysql_error());
$timesseenrow = mysql_num_rows($res);
$ro = mysql_fetch_array($res);

if($timesseenrow!=0){
$numberoftimesseen = $ro[0];
$arrayotherusers[$usercount][1] = $arrayotherusers[$usercount][1] + ($numberoftimesseen * 5);


}
}


$query = "SELECT toID FROM edges where name ='Has submitted' and fromID ='$user'";
$result = mysql_query ($query);
$times = mysql_num_rows($result);

while ($row = mysql_fetch_array($result))
{
$video = $row[0];

$query = "SELECT timesseen FROM edges where name ='Has seen' and fromID ='$otheruser' and toID='$video'";
$res = mysql_query ($query) or die(mysql_error());
$timesseenrow = mysql_num_rows($res);
$ro = mysql_fetch_array($res);

if($timesseenrow!=0){
$numberoftimesseen = $ro[0];
$timesseenrow = mysql_num_rows($res);
$arrayotherusers[$usercount][1] = $arrayotherusers[$usercount][1] + ($timesseenrow * 5);
}
}

$query = "SELECT toID FROM edges where name ='Has submitted' and fromID ='$otheruser'";
$result = mysql_query ($query);
$times = mysql_num_rows($result);

while ($row = mysql_fetch_array($result))
{
$video = $row[0];

$query = "SELECT timesseen FROM edges where name ='Has seen' and fromID ='$user' and toID='$video'";

$res = mysql_query ($query) or die(mysql_error());
$timesseenrow = mysql_num_rows($res);
$ro = mysql_fetch_array($res);

if($timesseenrow!=0){

$numberoftimesseen = $ro[0];

$arrayotherusers[$usercount][1] = $arrayotherusers[$usercount][1] + ($numberoftimesseen  * 5);
}
}

$query = "SELECT TakenOn FROM useractions where action ='Has commented' and TakenBy ='$user'";
$result = mysql_query ($query);
while ($row = mysql_fetch_array($result))
{

$Video = $row[0];
$query = "SELECT id FROM useractions where action ='Has commented' and TakenBy ='$otheruser' and TakenOn='$Video'";
$res = mysql_query ($query) or die(mysql_error());
$numberofcomments = mysql_num_rows($res);
$arrayotherusers[$usercount][1] = $arrayotherusers[$usercount][1] + ($numberofcomments * 5);
}


$query = "SELECT toID FROM edges where name ='Has uploaded' and fromID ='$user'";
$result = mysql_query ($query);
while ($row = mysql_fetch_array($result))
{
$Video = $row[0];
$query = "SELECT id FROM useractions where action ='Has commented' and TakenBy ='$otheruser' and TakenOn='$Video'";
$res = mysql_query ($query) or die(mysql_error());
$numberofcomments  = mysql_num_rows($res);
$arrayotherusers[$usercount][1] = $arrayotherusers[$usercount][1] + ($numberofcomments * 5);
}

$query = "SELECT TakenOn FROM useractions where action ='Has rated' and TakenBy ='$user'";
$result = mysql_query ($query);
while ($row = mysql_fetch_array($result))
{
$Video = $row[0];
$query = "SELECT id FROM useractions where action ='Has rated' and TakenBy ='$otheruser' and TakenOn='$Video'";
$res = mysql_query ($query) or die(mysql_error());
$numberofratings  = mysql_num_rows($res);
$arrayotherusers[$usercount][1] = $arrayotherusers[$usercount][1] + ($numberofratings * 5);

}



$query = "SELECT TakenOn FROM useractions where action ='Has uploaded' and TakenBy ='$user'";
$result = mysql_query ($query);
$times = mysql_num_rows($result);

while ($row = mysql_fetch_array($result))
{
$Video = $row[0];
$query = "SELECT id FROM useractions where action ='Has rated' and TakenBy ='$otheruser' and TakenOn='$Video'";
$res = mysql_query ($query) or die(mysql_error());
$numberofratings = mysql_num_rows($res);

$arrayotherusers[$usercount][1] = $arrayotherusers[$usercount][1] + ($numberofratings * 5);
}



}

?>












<?php

function findbestuser($user)
{
	global $arrayotherusers;
	
	function compare($x,$y)
	{
		if ( $x[1] == $y[1] )
	  		return 0;
	 	else if ( $x[1] > $y[1] )
	  		return -1;
	 	else
	  		return 1;
	}
	
	usort($arrayotherusers,'compare');
	
	for( $i=0;$i<sizeof($arrayotherusers);$i++ ){
		$otheruser = $arrayotherusers[$i][0];
		
		$invitefld	=	'INVITED,'.$user;
		$query 		= 	"UPDATE peoplenodes SET invite = '$invitefld' where id ='$otheruser' and invite=''";
		//echo $query;	
		mysql_query ($query) or die(mysql_error());
		//sleep(15);
		
		
		$checkquery 	= "SELECT invite FROM peoplenodes where id ='$user'";
		$checkresult 	= mysql_query($checkquery) or die(mysql_error());
		$Crow 			= mysql_fetch_array($checkresult);
		
		if ($Crow[0] == 'PLAYING') {
			// one of the invited users have agreed for a play. QUIT ! PRINCIPLE !
			exit();
		}

			
		$checkquery 	= "SELECT invite FROM peoplenodes where id ='$otheruser'";
		$checkresult 	= mysql_query($checkquery) or die(mysql_error());
		$Crow 			= mysql_fetch_array($checkresult);
		//echo $row[0];
		
		// invite field can be the same or NOT ACCEPTED
		// in both cases, we set "DENIED" to the 1st players "Status" field in g1_players
		// set "" to opponent player's "invite" field in peoplenodes	
		
		if( ($Crow[0] == 'NOT ACCEPTED') || ($Crow[0] ==  $invitefld ) ) {		
			$updatequery = "UPDATE peoplenodes SET invite = '' where id ='$otheruser'";
			mysql_query ($updatequery) or die(mysql_error());
	
		}
	}
	

	$updatequery = "UPDATE peoplenodes SET invite = '' where id ='$user'";
	mysql_query ($updatequery) or die(mysql_error());

	$updatequery = "UPDATE g1_players SET status = 'DENIED' where playerid ='$user'";
	mysql_query ($updatequery) or die(mysql_error());


}




?>




<?php

function callvideoagent($userid,$opponentid)
{
error_log("Strat of video agent");
//	$userid 		= 	$_POST['userid'];		// id of the 1st player
//	$opponentid 	= 	$_POST['opponentid'];	// id of the 2nd player	
	
//	$videoid		= 	$_POST['videoid'];		//	$_POST['videoid'];
	
//	$userid 		= 	'jessica.nay@insead.edu';		// id of the 1st player
//	$opponentid 	= 	'nicholas.leck@insead.edu';		// id of the 2nd player	
	
	//$videoid		= 	'Video157';						//	$_POST['videoid'];
    $wordlist="";
	

	
	$videoid		= 	videoagent($userid,$opponentid);
	//$videoid		=   'Video151';
	$rs				=	mysql_query ("SELECT word FROM g4_taboowords where videoid = '$videoid' AND frequency>0");

	while ( $row = mysql_fetch_array($rs) ){
		$word		= 	$row['word'];
		$wordlist	=	$word.','.$wordlist;
	}
	
	$taboowords		=	substr($wordlist, 0, -1);  	// 	find the taboo words for $videoid
	
	$currenttime	=	date('d-m-Y h:s:m'); 
	$new_game_id	= 	$userid.$currenttime.$opponentid;	
	$playerids		=	$userid.','.$opponentid;
	
	$query			=	"INSERT INTO g2_gameadmin(gameid,playerids,videoid,wordsmatched,gamescore,taboowords) VALUES ('$new_game_id','$playerids','$videoid','','0','$taboowords')";
	mysql_query ($query) or die(mysql_error());
	
	mysql_query ("UPDATE g1_players SET status='START',gameid='$new_game_id' where playerid = '$userid'");		
	mysql_query ("UPDATE g1_players SET status='START',gameid='$new_game_id' where playerid = '$opponentid'");		
	
	error_log("the videoagent ends");
	// function over
	
}
?>







<?php

function videoagent($firstuser,$seconduser){
	

//$firstuser  = 'albert.angehrn@insead.edu';
//$seconduser = 'bertrand.sereno@insead.edu';
$seenvideos = array();
$tagcount = 0;
// select the tags of videos seen by both the users and add them in tagsarray

$query = "SELECT toID FROM edges where name = 'Has seen' and fromID='$firstuser'";
$result = mysql_query ($query) or die(mysql_error());
$tagarray   = array();
$videoarray = array();
while ($row = mysql_fetch_array($result))
{
		$video = $row[0];
		$query = "SELECT toID FROM edges where name ='Has tags' and fromID = '$video'";
		$res = mysql_query ($query) or die(mysql_error());
		$nooftags = mysql_num_rows($res);
		$ro = mysql_fetch_array($res);
if($nooftags!=0){
 if (in_array($ro[0],$tagarray))
		continue;
		$tagarray[$tagcount] = $ro[0];
		$tagcount = $tagcount + 1;
}
}

	$query = "SELECT toID FROM edges where name = 'Has seen' and fromID='$seconduser'";
	$result = mysql_query ($query) or die(mysql_error());
while ($row = mysql_fetch_array($result))
{
	$video = $row[0];
	$query = "SELECT toID FROM edges where name ='Has tags' and fromID = '$video'";
	$res = mysql_query ($query) or die(mysql_error());
	$nooftags = mysql_num_rows($res);
	$ro = mysql_fetch_array($res);
   
  if($nooftags!=0){
if (in_array($ro[0],$tagarray))
		continue;
		$tagarray[$tagcount] = $ro[0];
		$tagcount = $tagcount + 1;
}
}

// the videos of the tags that are seen by both the users are then selected , this is done to choose a video suitable to the user
// add them in videos array
for($i=1;$i<sizeof($tagarray);$i++)
{
	$query = "SELECT fromID FROM edges where name = 'Has tags' and  toID='$tagarray[$i]'";
	$result = mysql_query ($query) or die(mysql_error());
	while ($row = mysql_fetch_array($result))
	{
		$video  = $row[0];
		$firstquery = "SELECT id FROM edges where name ='Has seen' and fromID ='$firstuser' and toID='$video'";
		$firstres = mysql_query ($firstquery) or die(mysql_error());
		
		$secondquery = "SELECT id FROM edges where name ='Has seen' and fromID = '$seconduser' and toID='$video'";
		$secondres = mysql_query ($secondquery) or die(mysql_error());
		 	
		if(  (mysql_num_rows($firstres) == 0) && ( mysql_num_rows($secondres) == 0) )
		{
			if ( in_array($row[0],$videoarray ))
				continue;
			else
				array_push($videoarray,$row[0]);
			//$secondquery = "SELECT id FROM edges where name ='Has seen' and fromID = '$seconduser' and toID='$video'";
			//$secondres = mysql_query ($secondquery) or die(mysql_error());
			
			//if( mysql_num_rows($secondres) == 0){
				//if (in_array($row[0],$videoarray)){
				//	continue;
				//}
				//array_push($videoarray,$row[0]);
		}

	}
		
}






// If the size of the videoarray is 0 or 1 then a random video is selected from the videos database and chosen if it was not 
// played by either of them
if(sizeof($videoarray) == 0 || sizeof($videoarray) == 1)
{
	$selectquery = "SELECT playerids,videoid from g3_gamelog";
    $selectresult = mysql_query ($selectquery) or die(mysql_error());
    while ($row = mysql_fetch_array($selectresult)){
  		$players = explode(',',$row[0]);
  if(($players[0] == $firstuser || $players[1] == $firstuser) || ($players[0]  == $seconduser || $players[1] == $seconduser))
   {

   	array_push($seenvideos,$row[1]);
  
     }
     }
	 // Here we are only selecting the Non FMS videos
     /* $selectquery = "SELECT id from videonodes ";*/
	 $selectquery = "SELECT id from videonodes where source='YTB';";
    $selectresult = mysql_query ($selectquery) or die(mysql_error());
    while ($row = mysql_fetch_array($selectresult))
    {
       if (in_array($row[0],$seenvideos))
           continue ;
       $selectedvideo = $row[0];
        break;  
    }
}

// else select a video from the videosarray that was not played by either of the users
else 
{
	$selectquery = "SELECT playerids,videoid from g3_gamelog";
	$selectresult = mysql_query ($selectquery) or die(mysql_error());
	while ($row = mysql_fetch_array($selectresult))
	{
		$players = explode(',',$row[0]);
		if(($players[0] == $firstuser || $players[1] == $firstuser) || ($players[0]  == $seconduser || $players[1] == $seconduser))
		{
			if ( in_array($videoarray[$i],$seenvideos ))
				continue;
			array_push($seenvideos,$row[1]);
		  		 
		}
	}
		$i=0;
        for($i=0;$i<sizeof($videoarray);$i++)
        {
        	if ( in_array($videoarray[$i],$seenvideos ))
				continue;
				else $selectedvideo = $videoarray[$i];
				  break;
        }

}

if($selectedvideo != null)
{
	if(Videocheck2($selectedvideo)	)
	{	
		error_log("the selected video is".$selectedvideo);
		return $selectedvideo;
	}
}

	$vidsarray = array();
	/*$queryvids=mysql_query("SELECT * FROM videonodes");*/
	$queryvids=mysql_query("SELECT * FROM videonodes where source='YTB';");
	while($row=mysql_fetch_array($queryvids))
		{
			array_push($vidsarray,$row['id']);
		}
		shuffle($vidsarray);
		error_log("the returned video is".$vidsarray[0]);
	return $vidsarray[0];



/*
if($selectedvideo == null)
{	
		//tHIS RANDOMISES THE VIDEOSARRAY
		shuffle($videoarray);

	$selectedvideo = $videoarray[0];
    return $selectedvideo;
}

else
{
  	return $selectedvideo;
}
*/

}



?>
<?php

function Videocheck($videoid)
{
$queryvideo=mysql_query("SELECT * FROM videonodes where id ='$videoid'");
if(mysql_num_rows($queryvideo)==0)
return false;
else 
return true;
}

function Videocheck2($videoid)
{
$queryvideo=mysql_query("SELECT * FROM videonodes where id ='$videoid' and source='YTB';");
if(mysql_num_rows($queryvideo)==0)
return false;
else 
return true;
}
?>		

