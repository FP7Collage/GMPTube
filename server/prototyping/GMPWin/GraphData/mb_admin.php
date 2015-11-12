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

/* global $retString;
$retString= "<?xml version=\"1.0\" encoding=\"utf-8\"?>";  
*/
	include "conntube.php";

	header("Content-type: text/xml");
	header("Cache-control: private");	
	
	$s_message	=	'';
	$s_entity	=	'';
	$action 	= 	$_REQUEST['action'];
	
	switch($action)
	{
		case 'deletevideo':
			deletevideo();
			break;
			
		case 'deleteuser':
			deleteuser();
			break;
			
		case 'deletetag':
			deletetag();
			break;
			
		case 'deletecomment':
			deletecomment();
			break;	
			
		case 'updatevideocategory' :
		      updatevideocategory();
			  break;
				
		case 'modifytag':
			modifytag();
			break;
		
		default:
			echo 'Just one more step!';
		//	$filename = 'demo.txt';
		//	$saveFile = fopen($filename, "w");
		//	fwrite ($saveFile, $query);
		//	fclose($saveFile);
			break;
	}



	
	
	
	
/*
	to delete a video
	inputs:	video id		Video16
	
	changes to be made:
		delete video file in server			( for FMS )
		delete thumbnail picture in server	( for FMS )
		
		delete video in videonodes table
		delete edges ( has tags  - 	videos,tags	) ( has submitted	-	people-videos )
					 ( has seen	 -	people,videos)
		
	assumptions:
		the video must not have any next version, or in parent fields
		if the video has a previous version, then we change the next version field of that video
		
*/
function updatevideocategory()
{
   global $s_message;
   global $s_entity;
	
	$target_videoid = $_POST['videoid'];
	$target_category= $_POST['category'];
	
	$rs 		= 	mysql_query ("UPDATE  videonodes SET category='$target_category' where id='$target_videoid'");
	$s_message			=	'Success';
	showSuccessMessage();
   
}

function deletevideo()
{
	
	global $s_message;
	global $s_entity;
	
	$target		= 	$_POST['videoid'];
//	$target		= 	$_REQUEST['videoid'];

	$rs 		= 	mysql_query ("SELECT name,nextversion FROM videonodes WHERE id='$target'");
	$row 		= 	mysql_fetch_array( $rs );
	$next		=	$row['nextversion'];
	if ( $next != '' ){
		$s_message	=	'Next version exists';
		$rs 		= 	mysql_query ("SELECT name FROM videonodes WHERE id='$next'");
		$row 		= 	mysql_fetch_array( $rs );
		$s_entity	=	$row['name'];
	
		showSuccessMessage();
		exit;
	}

	$rs 		= 	mysql_query ("SELECT previousversion FROM videonodes WHERE id='$target'");
	$row 		= 	mysql_fetch_array( $rs );
	$prev		=	$row['previousversion'];
	if ( $prev != '' ){
		mysql_query ("UPDATE videonodes SET nextversion='' WHERE id = '$prev'");
	}

	// delete has tags edge
	mysql_query ("DELETE FROM edges WHERE fromID = '$target'");
	
	
	// delete has submitted edge, has seen 
	mysql_query ("DELETE FROM edges WHERE toID = '$target'");
	
	
	// delete video file and thumbnail picture, if it is a FMS one.
	$rs 		= 	mysql_query ("SELECT source FROM videonodes WHERE id='$target'");
	$row 		= 	mysql_fetch_array( $rs );
	$sourcetype	=	$row['source'];
	
	if ( $sourcetype == 'FMS'){
		// DELETE THE VIDEO FILE
	}
	
	// delete video node
	$rs 		= 	mysql_query ("SELECT docLinks FROM videonodes WHERE id='$target'");
	$row 		= 	mysql_fetch_array( $rs );

	$docLinks = $row['docLinks'];
	mysql_query ("DELETE FROM videonodes WHERE id = '$target'");
	$s_message			=	'Success';
	showSuccessMessage();
	
	if($docLinks!=''){
		$docLinksArray = explode("|",$docLinks);
		foreach($docLinksArray as $file){
			
			chmod('./upload/'.$file,0777);
			unlink('./upload/'.$file);
			
		}
	}
}







/*
	to delete the user
	inputs:	user id
	changes to be made:
		delete user details in peoplenodes
		delete user row in keystable	
		delete from useractions table
		delete from edges table.. all edges which as fromID or toID as user id 
*/

function deleteuser()
{
	global $s_message;
	$target	= 	$_POST['id'];
	
	// delete from useractions
	mysql_query ("DELETE FROM useractions WHERE TakenBy = '$target'") or die(mysql_error());
	
	// delete from edges
	mysql_query ("DELETE FROM edges WHERE fromID = '$target'") or die(mysql_error());
	mysql_query ("DELETE FROM edges WHERE toID = '$target'") or die(mysql_error());
	
	// delete from peoplenodes
	mysql_query ("DELETE FROM peoplenodes WHERE id = '$target'") or die(mysql_error());
	
	// delete from keystable
	mysql_query ("DELETE FROM keystable WHERE userid = '$target'") or die(mysql_error());
	
	$s_message			=	'Success';
	showSuccessMessage();
	
	
	/*go through all the tags, and if the submittedby field is user id, change it to anonymous
	go through all the video nodes, submittedby field is user id, change it to anonymous
	mysql_query ("UPDATE videonodes SET submittedby='Anonymous' WHERE submittedby='$target'")  or die(mysql_error());
	mysql_query ("UPDATE tagnodes SET submittedby='Anonymous' WHERE submittedby='$target'") or die(mysql_error());
	*/
	
	/* substitute to Anonymous in authors field of videonodes table
	$rs 	= 	mysql_query ("SELECT id,authors FROM videonodes");
	while ( $row = mysql_fetch_array( $rs ) ){
		$videoid	=	$row['id'];
		$authors	= 	$row['authors'];	

		$new_authors=	str_replace($target, "Anonymous", $authors);
		mysql_query ("UPDATE videonodes SET authors ='$new_authors' where id = '$videoid'");
	}
	*/

	
}






	/*	*	delete nodes with the id in tag nodes table
		*	delete tags in tags field in video nodes 
		*	delete all edges with toID = tag id from edges table
	*/
function deletetag()
{
	global $s_message;
	$tagid		= 	$_POST['tag'];
	$rs 		= 	mysql_query ("SELECT name FROM tagnodes WHERE id='$tagid'");
	$row 		= 	mysql_fetch_array( $rs ) ;
	$tagname	=	$row['name'];
	
	
	
	$target		=	strtoupper($tagname);
	
	// deleting from tags field in videonodes table
	$rs 	= 	mysql_query ("SELECT id,tags FROM videonodes");
	while ( $row = mysql_fetch_array( $rs ) ){
		$videoid		=	$row['id'];
		$new_list		=	'';
		$tags_list		= 	$row['tags'];		
		$tags_array 	= 	explode(',',$tags_list);
		
		foreach($tags_array as $single){
			$check	=	strtoupper($single);
			if ( $check == $target  ){
				//	do nothing
			}
			else{
				$new_list	=	$single.','.$new_list;
			}
		}
		
		$new_list 	= 	substr($new_list, 0, -1);
		mysql_query ("UPDATE videonodes SET tags ='$new_list' where id = '$videoid'");

	}
	
	// deleting from tagnodes table
	mysql_query ("DELETE FROM tagnodes WHERE id = '$tagid'");
	
	// deleting from edges table
	mysql_query ("DELETE FROM edges WHERE toID = '$tagid'") or die(mysql_error());
	
	$s_message			=	'Success';
	showSuccessMessage();
}






/*
	to delete a comment
	inputs:
		video id		Video16
		comment text	albert.angehrn@insead.edu>says>marissa in afternoon>c>
	method:
		we replace the substring by empty quotes( "" ) and update the table
*/

function deletecomment()
{
	global $s_message;
	$videoid	= 	$_POST['videoid'];
	$target		= 	$_POST['comment'];
	$individualComment = $_POST['individualComment'];
	//$target		= 	'albert.angehrn@insead.edu>says>marissa in afternoon>c>';
	//$videoid	= 	'Video5';	
	
	$rs 		= 	mysql_query ("SELECT comments FROM videonodes WHERE id='$videoid'");
	$row 		= 	mysql_fetch_array( $rs );
	$com_list	= 	$row['comments'];	

	$new_list	=	str_replace($target, "", $com_list);

	mysql_query ("UPDATE videonodes SET comments ='$new_list' where id = '$videoid'");
	//error_log("b4 deleting row2");
	//error_log($videoid);
	//error_log($individualComment);
	mysql_query ("delete from commentstable where (videoid='$videoid'and comment='$individualComment')");
	//error_log("aafter deleting row2");
	$s_message			=	'Success';
	showSuccessMessage();
	
}


function modifytag()
{
	global $s_message;
	$tagid		= 	$_POST['tag'];
	$newtag = $_POST['newtag'];
	mysql_query("UPDATE tagnodes SET name='$newtag' where id='$tagid'") or die(mysql_error());
	$s_message			=	'Success';
	showSuccessMessage();
}


	
	
	function showSuccessMessage()
	{	
		global $s_message;
		global $s_entity;
		
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
		echo "<rsp>";
		echo "<message>$s_message</message>";
		echo "<entity>$s_entity</entity>" ;
		echo "</rsp>";
	
		
	/*		$retString = $retString . "<rsp><entity>$s_entity</entity><message>$s_message</message></rsp>" ;
	echo $retString;
	*/
	}
	
?>

