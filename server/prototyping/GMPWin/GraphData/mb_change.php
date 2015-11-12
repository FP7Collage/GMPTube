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

global $retString;
$retString= "<?xml version=\"1.0\" encoding=\"utf-8\"?>";  

	include "conntube.php";
	session_start();
	header("Content-type: text/xml");
	header("Cache-control: private");
			
	$action = $_REQUEST['action'];
	
	global $entity;
	
	switch($action)
	{
		case 'addvideo':
			addvideonode();
			break;
			
		case 'replacevideo':
			replacevideonode();
			break;	
			
		case 'modifyvideo':
			editvideonode();
			break;
			
		case 'addcomments':
			addcomments();
			break;
			
		case 'editcomments':
			editcomments();
			break;	
			
		case 'deletecomment':
			deletecomment();
			break;
			
		case 'getvideocomments':
		      getvideocomments();
		      break;
		
		case 'changerating':
			changerating();
			break;
			
		
		case 'addpeoplenode':
			addpeoplenode();
			break;
			
			case 'editprofile':
			editprofile();
			break;
		
		case 'chPwd':
			chPwd();
			break;
		
		
		
		case 'addscraps':
			addscraps();
			break;
			
		case 'editscraps':
			editscraps();
			break;
			
		case 'getscraps':
			getscraps();
			break;
			
		
			
		case 'deletescrap':
			deletescrap();
			break;
		
		case 'addtagnode':
			addtagnode();
			break;
			
		
		
			
			
		case 'addedge':
			addedge();
			break;
			
		case 'deleteedge':
			deleteedge();
			break;
		
		case 'changeintensity':
			changeintensity();
			break;
			
		
		
			
	 	default:
			echo 'Just one more step!';
		//	$filename = 'demo.txt';
		//	$saveFile = fopen($filename, "w");
		//	fwrite ($saveFile, $query);
		//	fclose($saveFile);
			break;	
	}

?>
	
<?php

	function addvideonode()
	{
        global	$record_path;
		global $retString;
		$additiontype	= $_POST['additiontype'];
		$nodename 		= $_POST['nodename'];
		
		// forming the tags of the video
		$tags      =  gettagarrayid($_POST['tags']);
		
		$submittedby	= $_POST['submittedby'];
		$category		= $_POST['category'];
		$picture		= $_POST['picture'];
		$url			= $_POST['url'];
		$authors		= $_POST['authors'];
		$inspiredby		= isset ($_POST['inspiredby']) ? $_POST['inspiredby'] : '';
		$parentvideo	= $_POST['parentvideo'];
		$sourcetype		= $_POST['sourcetype'];	
		//$externalLinks	= $_POST['externalLinks'];
		//$docLinks		= $_POST['docLinks'];
		
	//	error_log("authors " . $authors);
	//	error_log("inspiredby " . $inspiredby);

		$externalLinks	= "";
		$docLinks		= "";
		
		$edgecolor 	= "";
		$tooltip 	= "";	
		$intensity  = "";
		


		$description	= $_POST['description'];
		$video_py		= isset($_POST['video_py']) ? $_POST['video_py'] : '#EVERYBODY#';
		
		$splcharacters = array("&","'","<",">","\"");	
		$description = str_replace($splcharacters, "",$description);	  
		$nodename = str_replace($splcharacters, "",$nodename);	
		
	
	// Generate the new VideoId
		$rs 			= mysql_query ("SELECT max(number) FROM videonodes");
		$row 			= mysql_fetch_array($rs);
		$videonumber 	= $row['0'];
		$videonumber+=1;
		$VideoId 		= 'Video'.$videonumber; //CONCAT('Video','$videonumber');
		$entity			=	$VideoId;	// this value will be returned to the client	
		
		// * addition type can be 'NEW VIDEO' or 'VERSIONING'
		
		// * how to retrieve select unix_timestamp(time) from table
		
		$previousversion = '';
		
			
		if( $additiontype == 'NEW VIDEO'){
			
			$query = "INSERT INTO videonodes(timesrated,id,name,nodetype,comments,tags,grandrating,submittedby,submittime,category,picture,url,authors,inspiredby,source,externalLinks,docLinks,description,video_py)
			VALUES ('0','$VideoId','$nodename','Videos','','$tags','0','$submittedby',current_timestamp(),'$category','$picture','$url','$authors','$inspiredby','$sourcetype','$externalLinks','$docLinks1','$description','$video_py')";
			mysql_query ($query) or die(mysql_error());   		   		  			
		
		}
		else{ 	
			//	*	addition type is VERSIONING
			//	*	we have the id of parent version. from that id..we find the last version of that video.
			
			$version_count = 2;
			$firstname_rs 	= mysql_query ("SELECT name FROM videonodes WHERE id='$parentvideo'");
			$firstname_row 	= mysql_fetch_array($firstname_rs);
			$firstname 		= $firstname_row['name'];
			
			$calc_record = mysql_query ("SELECT nextversion FROM videonodes WHERE id='$parentvideo'");
			$previousversion = $parentvideo;
			
			while ( $row = mysql_fetch_array($calc_record) ){
				$nextversion = $row['nextversion'];
				if ( $nextversion == null ) 
					break;
				else{
					$version_count+=1;
					$calc_record = mysql_query ("SELECT nextversion FROM videonodes WHERE id='$nextversion'");
					$previousversion = $nextversion;
				}
			}
			
		
			// * previous version of 'new' video is 'ex' video
			// * next version of 'ex' video is 'new' video
			// * first name will the name of parent, then we append its version number
			
			$nodename = $firstname.' v'.$version_count;
			$query="INSERT INTO videonodes(id,name,nodetype,comments,tags,grandrating,submittedby,submittime,category,picture,url,authors,inspiredby,previousversion,parentvideo,versionnum,source,externalLinks,docLinks,description,video_py)
			VALUES('$VideoId','$nodename','Videos','','$tags','1','$submittedby',current_timestamp(),'$category','$picture','$url','$authors','$inspiredby','$previousversion','$parentvideo',$version_count,'$sourcetype','$externalLinks','$docLinks1','$description','$video_py')";
			//echo $query;
			mysql_query ($query) or die(mysql_error());   		  			
			mysql_query ("UPDATE videonodes SET nextversion ='$VideoId' where id = '$previousversion'");
			
		}
			
		// create Is previous version edges !!
		if ( $previousversion != '') {
			$t_rs=mysql_query ("SELECT edgecolor,tooltip,intensity FROM edgetypes where name='Is previous version'");
			while ( $row = mysql_fetch_array($t_rs) ){
				$edgecolor 	= $row['edgecolor'];
				$tooltip 	= $row['tooltip'];	
				$intensity  = $row['intensity'];
			}
			mysql_query ("INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor,creationtime) VALUES ('Is previous version','$tooltip','$previousversion','$VideoId','$intensity','$edgecolor',current_timestamp())");	
		}
		
		// create Has inspired edges !!
		//$hasinspired		= $_POST['hasinspired'];
		$hasinspired		= $inspiredby;
	
		//change has inspired to is related to
		if ( $hasinspired != '') {		// the video has some inspirations
			$t_rs=mysql_query ("SELECT edgecolor,tooltip,intensity FROM edgetypes where name='Is Connected To'");
			while ( $row = mysql_fetch_array($t_rs) ){
				$edgecolor 	= $row['edgecolor'];
				$tooltip 	= $row['tooltip'];	
				$intensity  = $row['intensity'];
			}
			
			$insp_array = explode(',',$hasinspired);
			foreach($insp_array as $insp_id)
				mysql_query ("INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor,creationtime) VALUES ('Is Connected To','$tooltip','$insp_id','$VideoId','$intensity','$edgecolor',current_timestamp())");	
				
		}
		
		// create has submitted edge
		$t_rs=mysql_query ("SELECT edgecolor,tooltip,intensity FROM edgetypes where name='Has submitted'");
		while ( $row = mysql_fetch_array($t_rs) ){
			$edgecolor 	= $row['edgecolor'];
			$tooltip 	= $row['tooltip'];	
			$intensity  = $row['intensity'];
		}
		mysql_query ("INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor,creationtime) VALUES ('Has submitted','$tooltip','$submittedby','$VideoId','$intensity','$edgecolor',current_timestamp())");
		
		// create the has submitted for authors ...
		
		
		
		
		// * check if the tags already exists, if
		// * YES 	- 	add only the edge
		// * NO		-	create the tag node and then add the edge Has submitted
		
		$tags_array = explode(',',$tags);
		
		$t_rs=mysql_query ("SELECT edgecolor,tooltip,intensity FROM edgetypes where name='Has Tags'");
		while ( $row = mysql_fetch_array($t_rs) ){
			$edgecolor 	= $row['edgecolor'];
			$tooltip 	= $row['tooltip'];	
			$intensity  = $row['intensity'];
		}
		
		for( $i=0;$i<sizeof($tags_array);$i++){
		
			$indiv_tag = trim($tags_array[$i]);
			$indiv_tag = trim($indiv_tag," ,\t,\n,\r");
			$rs = mysql_query ("SELECT id from tagnodes where id='$indiv_tag'");
			$sql="select * from videonodes";
			if ( mysql_num_rows($rs) == 0 ){
				$rs 		= mysql_query ("SELECT max(number) FROM tagnodes");
				$row 		= mysql_fetch_array($rs);
				$tagnumber 	= $row['0'];	
				$tagnumber+=1;
				$tagId = 'Tag'.$tagnumber;
				if(trim($indiv_tag)!='' && trim($indiv_tag)!='')
				{
				mysql_query ("INSERT INTO tagnodes(id,name,nodetype,picture,submittedby,submittime) VALUES ('$tagId','$indiv_tag','Tags','media/tag_img.jpg','$submittedby',current_timestamp())");
				
				$sql="INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor,creationtime) VALUES ('Has tags','$tooltip','$VideoId','$tagId','$intensity','$edgecolor',current_timestamp())";
			//	error_log("THE NEW TAGS".$sql);
				}
			}
			else{
				$row = mysql_fetch_array($rs);
				$tagId = $row['id'];
				$tagId = trim($tagId," ,\t,\n,\r");
				
				$sql="INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor,creationtime) VALUES ('Has tags','$tooltip','$VideoId','$tagId','$intensity','$edgecolor',current_timestamp())";
			//	error_log("THE OLD TAGS".$sql);
			}
			//error_log($sql);
			mysql_query ($sql) or die(mysql_error());
		}
		
		// * good :)
		
		$retString = $retString . "<rsp><entity>$entity</entity><message>Success</message></rsp>" ;
	echo $retString;
	    
	}

?>

<?php

	function editvideonode()
	{		
		global $retString;
		
		$out = print_r($_POST, true);
		error_log("post var" . $out);
		
	//	$additiontype	= $_POST['additiontype'];
		$nodename 		= $_POST['nodename'];
		//$tags			= $_POST['tags'];
		$tags      =  gettagarrayid($_POST['tags']);
	//	$submittedby	= $_POST['submittedby'];
		$category		= $_POST['category'];
	//	$picture		= $_POST['picture'];
	//	$url			= $_POST['url'];
		$authors		= $_POST['authors'];
		//$authors = array_unique($authors);
		
		$inspiredby		= $_POST['inspiredby'];
	//	$parentvideo	= $_POST['parentvideo'];		
	//	$sourcetype		= $_POST['sourcetype'];	
	//	$externalLinks	= $_POST['externalLinks'];
	//	$docLinks		= $_POST['docLinks'];
		$description	= $_POST['description'];		
		
		$VideoId				= $_POST['id'];	
		$entity  = $VideoId	;
		$video_py		= isset($_POST['video_py']) ? $_POST['video_py'] : '#EVERYBODY#';
		
		$splcharacters = array("&","'","<",">","\"");	
		$description = str_replace($splcharacters, "",$description);	  
		$nodename = str_replace($splcharacters, "",$nodename);	
		
		
		
	//	$facArea = $_POST['facultyAreaType'];
		
		// add new tags
		
		$updatequery = "UPDATE videonodes SET name='$nodename',category='$category',tags='$tags',authors='$authors',inspiredby='$inspiredby',description='$description',video_py='$video_py' where id='$VideoId' ";
		
		error_log ($updatequery);
		
		mysql_query($updatequery); 
		// create Has inspired edges !!
		
		//$hasinspired		= $_POST['hasinspired'];
		$hasinspired		= $inspiredby;
		
		$edgecolor 	= "";
		$tooltip 	= "";	
		$intensity  = "";
		
		
		
		//change "has inspired" to "is related to"
		if ( $hasinspired != '') {		// the video has some inspirations
			$t_rs=mysql_query ("SELECT edgecolor,tooltip,intensity FROM edgetypes where name='Is Connected To'");
			while ( $row = mysql_fetch_array($t_rs) ){
				$edgecolor 	= $row['edgecolor'];
				$tooltip 	= $row['tooltip'];	
				$intensity  = $row['intensity'];
			}
			
			$insp_array = explode(',',$hasinspired);
			mysql_query(" delete from edges where (name = 'Is Connected To' and toID='$VideoId')");
			foreach($insp_array as $insp_id){
				
			
				$rs = mysql_query("Select * from edges where (name = 'Is Connected To' and fromID='$insp_id' and toID='$VideoId')");
				if(mysql_num_rows($rs)==0){
					mysql_query ("INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor,creationtime) VALUES ('Is Connected To','$tooltip','$insp_id','$VideoId','$intensity','$edgecolor',current_timestamp())");	
				}
			}	
		}
		
		
		
		// * check if the tags already exists, if
		// * YES 	- 	add only the edge
		// * NO		-	create the tag node and then add the edge Has submitted
		
		$tags_array = explode(',',$tags);
		
		$t_rs=mysql_query ("SELECT edgecolor,tooltip,intensity FROM edgetypes where name='Has Tags'");
		while ( $row = mysql_fetch_array($t_rs) ){
			$edgecolor 	= $row['edgecolor'];
			$tooltip 	= $row['tooltip'];	
			$intensity  = $row['intensity'];
		}
		
		
		for( $i=0;$i<sizeof($tags_array);$i++){
		
			$indiv_tag = trim($tags_array[$i]);
			$indiv_tag = trim($indiv_tag," ,\t,\n,\r");
			
			$rs = mysql_query ("SELECT id from tagnodes where id='$indiv_tag'");
			// if not in tagnodes insert both in tagnodes and edges
			if ( mysql_num_rows($rs) == 0 && trim($indiv_tag)!='' ){
				$rs 		= mysql_query ("SELECT max(number) FROM tagnodes");
				$row 		= mysql_fetch_array($rs);
				$tagnumber 	= $row['0'];	
				$tagnumber+=1;
				$tagId = 'Tag'.$tagnumber;
				
				mysql_query ("INSERT INTO tagnodes(id,name,nodetype,picture,submittedby,submittime) VALUES ('$tagId','$indiv_tag','Tags','media/tag_img.jpg','$submittedby',current_timestamp())");
				//$sql="INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor,creationtime) VALUES ('Has tags','$tooltip','$VideoId','$tagId','$intensity','$edgecolor',current_timestamp())";
				$sql="INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor,creationtime) VALUES ('Has tags','$tooltip','$VideoId','$indiv_tag','$intensity','$edgecolor',current_timestamp())";

			}
			//if present in tagnodes
			
			else{
				$row = mysql_fetch_array($rs);
				$tagId = $row['id'];
				$tagId = trim($tagId," ,\t,\n,\r");
				$rs = mysql_query("Select * from edges where (name='Has tags' and fromID='$VideoId' and toID='$tagId')");
				// if present in edges.. continue
				if(mysql_num_rows($rs)!=0) continue;
				// and not in edges.. then insert in edges
				$sql="INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor,creationtime) VALUES ('Has tags','$tooltip','$VideoId','$tagId','$intensity','$edgecolor',current_timestamp())";
			}
				
		
				
			//error_log("the query are".$sql);
			mysql_query ($sql) or die(mysql_error());
		
		}
		
		// now delete the tags
		$rs = mysql_query("Select * from edges where (name='Has tags' and fromID='$VideoId')");
				//error_log("here");
				while ($single = mysql_fetch_array($rs)) {
				$flag=0;
				foreach ($tags_array as $singletag)
				{
				//error_log(substr($single[toID],3));
				//error_log(substr($single[toID],3));
				//error_log($singletag);
				//error_log("here");
				$data=mysql_query("select * from tagnodes where id='$singletag'");
				$name=mysql_fetch_array($data);
				//error_log($name[number]);
				if(substr($single['toID'],3)==$name['number'])
				$flag=1;
				}
				if($flag==0)
				//error_log("no match");
				mysql_query("delete from edges where id='$single[id]'");
				
				//error_log("---------------------------------------------------------------------------------");
				}
		
		
		// * good :)
		
		$retString = $retString . "<rsp><entity>$entity</entity><message>Success</message></rsp>" ;
	echo $retString;
	    
	    
	
	
	}
	
	
	
function replacevideonode()
	{		
	global	$record_path;
	global $retString;
		$picture		= $_POST['picture'];
		$url			= $_POST['url'];
		$sourcetype		= $_POST['sourcetype'];	
		$VideoId		= $_POST['id'];	
		
		$moveResult = "";
		
	
		//error_log("recording" . $moveResult );
				
		$sql = "UPDATE videonodes SET picture ='$picture',url='$url', source='$sourcetype' where id='$VideoId' ";
	//	error_log("the query are".$sql);
		mysql_query($sql); 
		
		
		mysql_query ($sql) or die(mysql_error());
		
		$entity  = $VideoId	;
		
		
	    $retString = $retString . "<rsp><entity>$entity</entity><message>Success</message></rsp>" ;
	echo $retString;
	    
	
	
	}
	
	
	

?>

<?php

	function addpeoplenode()
	{
	global $retString;
		$id				= $_POST['email'];
		$name 			= $_POST['name'];
		$company		= $_POST['company'];
		$title 			= $_POST['title'];
		$nationality 	= $_POST['nationality'];
		$picture 		= $_POST['picture'];
		$webpage		= $_POST['webpage'];
		$email			= $_POST['email'];
		$location		= $_POST['location'];
		//$interests		= $_POST['interests'];
		$interests      =  gettagarrayid2($_POST['interests']);
		$invitedby		= $_POST['invitedby'];
		$description	= $_POST['description'];
		$competences	= $_POST['competences'];
		
		$default_py		= '#EVERYBODY#';
		$name_py		= isset($_POST['name_py'])			? $_POST['name_py']			: $default_py;
		$company_py		= isset($_POST['company_py'])		? $_POST['company_py']		: $default_py;
		$title_py		= isset($_POST['title_py'])		? $_POST['title_py']		: $default_py;
		$nationality_py	= isset($_POST['nationality_py'])	? $_POST['nationality_py']	: $default_py;
		$picture_py		= isset($_POST['picture_py'])		? $_POST['picture_py']		: $default_py;
		$url_py			= isset($_POST['url_py'])			? $_POST['url_py']			: $default_py;
		$interests_py	= isset($_POST['interests_py'])	? $_POST['interests_py']	: $default_py;
		$profile_py		= isset($_POST['profile_py'])		? $_POST['profile_py']		: $default_py;
		$emailid_py		= isset($_POST['emailid_py'])		? $_POST['emailid_py']		: $default_py;
		$location_py	= isset($_POST['location_py'])		? $_POST['location_py']		: $default_py;
		$invitedby_py	= isset($_POST['invitedby_py'])	? $_POST['invitedby_py']	: $default_py;
		$competences_py	= isset($_POST['competences_py'])	? $_POST['competences_py']	: $default_py;
		
		$password = 'lab1'; // DEFAULT KEY
		if(isset($_POST['password']) && $_POST['password'] != '')
			$password = $_POST['password'];
			
		// parse $interests and add new ones to tagnodes table
		$fin_interests	=	'';
		
		$list = explode(",",$interests);
		foreach( $list as $single ){
			$single = trim($single," ,\t,\n,\r");
			//$rs = mysql_query ("SELECT name from tagnodes where name='$single'");
			$rs = mysql_query ("SELECT name from tagnodes where id='$single'");
		//	error_log($single);
			if ( mysql_num_rows($rs) == 0 && trim($single)!=''){
				
				//mysql_query ("INSERT INTO tagnodes(name,submittime) VALUES('$single',current_timestamp())");
				$rstag = mysql_query ("SELECT max(number) FROM tagnodes");
		while ($row1 = mysql_fetch_array($rstag))
		{
			$number = $row1[0];
		}
		
		$number+=1;
		//error_log($single);
		//mysql_query ("INSERT INTO tagnodes(number,id,name,nodetype,picture,submittime) VALUES ('$number','Tag$number','".$single."','Tags','http://labs.calt.insead.edu/prototyping/Tentube/GraphData/media/tag_img.jpg',current_timestamp())");
		
		mysql_query ("INSERT INTO tagnodes(number,id,name,nodetype,picture,submittime) VALUES ('$number','Tag$number','".$single."','Tags','media/tag_img.jpg',current_timestamp())");

				
				//$fin_interests 	= 	$single.','.$fin_interests;
				$fin_interests 	= 	'Tag'.$number.','.$fin_interests;
			}
			else{
				$row 			= 	mysql_fetch_array($rs);
				//$temp			= 	$row['name'];
				$temp			=  $single;
				$fin_interests	=	$temp.','.$fin_interests;
			}
		}
		
		$fin_interests 	= substr($fin_interests, 0, -1);
		
		$query = "INSERT INTO
				peoplenodes(id,name,nodetype,company,title,nationality,picture,url,emailid,location,interests,profile,loggedin,grandscore,jointime,timesvisited,invite,invitedby,competences,
				alias,name_py,company_py,title_py,nationality_py,picture_py,url_py,interests_py,profile_py,emailid_py,location_py,invitedby_py,competences_py)
				VALUES ('$id','$name','People','$company','$title','$nationality','$picture','$webpage','$email','$location','$fin_interests','$description',0,0,current_timestamp(),0,'','$invitedby','$competences',
				'$name','$name_py','$company_py','$title_py','$nationality_py','$picture_py','$url_py','$interests_py','$profile_py','$emailid_py','$location_py','$invitedby_py','$competences_py')";
	//	error_log($query);
		mysql_query ($query) or die(mysql_error());
		
		// add 1st edge for the new user, add Knows edge with $invitedby user
		$t_rs		= 	mysql_query ("SELECT edgecolor,tooltip,intensity FROM edgetypes where name='Knows'");
		$row 		= 	mysql_fetch_array($t_rs);
		$edgecolor 	= 	$row['edgecolor'];
		$tooltip 	= 	$row['tooltip'];
		$intensity  =	$row['intensity'];

		// SSKNOTE: Privacy values for the knows relationship with the user in invitedby field... is yet to be implemented.
		mysql_query ("INSERT INTO
				edges(name,tooltip,fromID,toID,intensity,edgecolor,creationtime)
				VALUES ('Knows','$tooltip','$id','$invitedby','$intensity','$edgecolor',current_timestamp())");

		// insert data into keys
		mysql_query ("INSERT INTO
				keystable(ikey,userid) VALUES ('$password','$id')") or die(mysql_error());
		mysql_query ("INSERT INTO
				chatusers(id,accesstime,loginstatus)
				VALUES ('$id',current_timestamp(),false)") or die(mysql_error());
				
	// send Confirmation email
	//	sendRegisterConfirm($email,$name,$password);
		
	    $retString = $retString . "<rsp><entity>$id</entity><message>Success</message></rsp>" ;
	echo $retString;
	}

?>
	
<?php

	function addcomments()
	{
	global $retString;	
	$nodeid 	= $_POST['nodeid'];
	
		$individualComment = $_POST['individualComment'];
		$authorId   =$_POST['authorId'];
		
		$splcharacters = array("'");	
		$individualComment = str_replace($splcharacters, "\'",$individualComment);	  
		
		$query = "Insert into commentstable(authorid,datetime,videoid,comment) values('$authorId',now(),'$nodeid','$individualComment')";
		mysql_query ($query);
		
		$retString = $retString . "<rsp><message>Success</message></rsp>" ;
	    echo $retString;
	
		//sendCommentsMail($authorId,$nodeid,$individualComment);
	}

?>


<?php

	function addscraps()
	{
	
	global $retString;
		$nodeid 	= $_POST['nodeid'];
		$scrap 	= $_POST['scraps'];
		$scrap 	= trim($scrap);
		
		$splcharacters = array("'");	
		$scrap  = str_replace($splcharacters, "\'",$scrap );	  
		
	
		$authorId   =$_POST['authorId'];
		$query = "Insert into scrapstable(authorid,datetime,userid,scrap) values('$authorId',now(),'$nodeid','$scrap')";
		mysql_query ($query);
		
		
	
		$retString = $retString . "<rsp><message>Success</message></rsp>" ;
	    echo $retString;
		
	//	if($nodeid != $authorId)
	//	sendmail($authorId,$nodeid,$scrap);
	}

?>





<?php	
	function deletecomment()
{
	global $s_message;
	global $retString;
	//$videoid	= 	$_POST['videoid'];
	$target		= 	$_POST['commentid'];
	
	mysql_query ("delete from commentstable where cid='$target'");
	
		$retString = $retString . "<rsp>><message>Success</message></rsp>" ;
	    echo $retString;
	
}
	
?>	



<?php	
	function deletescrap()
{
	global $s_message;
	$userid	= 	$_POST['userid'];
	$scrap		= 	$_POST['scrapid'];
	$scrap		=	trim($scrap);
	
	//error_log("userid " . $userid );
	//error_log("scrap " .$scrap);
	
	mysql_query ("delete from scrapstable where (userid='$userid' and sid='$scrap')");
	
		$retString = $retString . "<rsp><message>Success</message></rsp>" ;
	    echo $retString;
	
	
}
	
?>	


	
<?php

	function editcomments()
	{
	global $retString;
	//	$nodeid 	= $_POST['nodeid'];
	//	$comments 	= $_POST['comments'];
		$individualComment = $_POST['individualComment'];
		$cid		=$_POST['commentid'];
		
		$splcharacters = array("'");	
		$individualComment = str_replace($splcharacters, "\'",$individualComment);	  
		
		$query = "UPDATE commentstable SET comment ='$individualComment' where cid = '$cid'";
		mysql_query ($query);
		
	$retString = $retString . "<rsp><message>Success</message></rsp>" ;
	    echo $retString;	
		
	}

?>	




	
<?php

	function getscraps()
	{
		global $retString;
	  $userid  = $_POST['userid'];
	//  $userid  = 'pkmittal82@gmail.com';
        $query = "select * from scrapstable where userid='$userid'";
	 $rs=mysql_query ($query);   
     $retstring = '<user>';	 
     while ($row = mysql_fetch_array($rs))
     {
         // error_log($row);
		 $commText = "<![CDATA[" . $row['scrap'] . "]]>";
		 
		  $retstring = $retstring .'<scrap>';
		  $retstring=$retstring ."<authorid>".$row['authorid']."</authorid>"."<datetime>".$row['datetime']."</datetime>"."<userid>".$row['userid']."</userid>"."<scraptext>".$commText."</scraptext>"."<sid>".$row['sid']."</sid>";
		  ;
		$retstring = $retstring .'</scrap>';
	 }
	 $retstring = $retstring .'</user>';
	 echo $retstring;

	}

?>	
	


	
	
	
	
<?php

	function editscraps()
	{
		global $retString;
		$scrap 	= $_POST['scrap'];
		$scrap		=	trim($scrap);
				
		$sid		=$_POST['scrapid'];
		
		$splcharacters = array("'");	
		$scrap = str_replace($splcharacters, "\'",$scrap);	  
		
		
		$query = "UPDATE scrapstable SET scrap ='$scrap' where sid = '$sid'";
		mysql_query ($query);
		
		$retString = $retString . "<rsp>><message>Success</message></rsp>" ;
	    echo $retString;		

	}

?>	
	
	
<?php

	function changerating()	
	{
	global $retString;
		$videoid 	= 	$_POST['videoid'];
		$rating 	= 	$_POST['rating'];
		$userid		=	$_POST['userid'];
		
		//$videoid	=	"Video5";
		//$rating		=	"3";
		$count=mysql_query("Select count(*) from useractions where action='Rated' and TakenBy='$userid' and TakenOn='$videoid'");
		$row = mysql_fetch_row($count);
		
		$isPresent = 0;
					
		if($row[0]==0){
			$isPresent = false;
		}
		else{
			$rs		= mysql_query("Select * from useractions where action='Rated' and TakenBy='$userid' and TakenOn='$videoid'");
			while($row = mysql_fetch_array($rs))
			{
				$isPresent = true;
				$prevrating = $row['Entity'];
			}
		}
		
		$rs 		=	mysql_query ("SELECT grandrating,timesrated from videonodes where id ='$videoid'");
		$row 		= 	mysql_fetch_array($rs);
		$currrating = 	$row['grandrating'];
		$timesrated = 	$row['timesrated'];
		
		if($isPresent){
			$new_rating =(($timesrated*$prevrating)-$prevrating+$rating)/($timesrated);
		}
		else{
			$new_rating=(($timesrated*$currrating)+$rating)/($timesrated+1);
			$timesrated = $timesrated+1;
		}
		
		$new_rating = round($new_rating,2);
		
		$query		=	"UPDATE videonodes SET grandrating='$new_rating',timesrated=$timesrated where id = '$videoid'";
		
		mysql_query ($query) or die(mysql_error());
		if($isPresent){
		mysql_query ("update useractions set Entity='$rating' where action='Rated' and TakenBy='$userid' and TakenOn='$videoid'") or die(mysql_error());
		}
		else
		{
		mysql_query ("INSERT INTO useractions(action,TakenBy,TakenOn,Entity,Datetime) VALUES ('Rated','$userid','$videoid','$rating',current_timestamp())") or die(mysql_error());
		}
		//add log message for that user.
		
		$retString = $retString . "<rsp><vid>$videoid</vid> <rating> $new_rating</rating><message>Success</message></rsp>" ;
	    echo $retString;	
		
	}

?>
	
<?php

	function addtagnode()
	{	
	global $retString;
		$name		= $_POST['name'];
		$nodetype 	= $_POST['nodetype'];
		$picture	= $_POST['picture'];
		
		$rs = mysql_query ("SELECT max(number) FROM tagnodes");
		while ($row = mysql_fetch_array($rs))
		{
			$number = $row[0];
		}
		
		$number+=1;
		if(trim($name)!='')
				{
		mysql_query ("INSERT INTO tagnodes(id,name,nodetype,picture) VALUES ('Tag$number','$name','$nodetype','$picture')");
		showSuccessMessage();
		}
	}

?>

<?php

	function addedge()
	{
		global $retString;
		$edgename	= $_POST['edgename'];
		$fromID		= $_POST['fromID'];
		$toID		= $_POST['toID'];
		
		$t_rs		= mysql_query ("SELECT edgecolor,tooltip,intensity FROM edgetypes where name='$edgename'");
		$row 		= mysql_fetch_array($t_rs);
		$edgecolor 	= $row['edgecolor'];
		$tooltip 	= $row['tooltip'];	
		$intensity  = $row['intensity'];
		
		
		// * if edge name is Has seen, then 
		// *	1.  increment the timesseen and change the lastseen fields corresponding to that user id and video id in edges table.
		// * 	2.  increment the timesseen and change the lastseen variable in videonodes table
		// * 	3.  delete the Is watching edge in log table 
		
		if ( $edgename == 'Has seen' ){
			
			// * edges table
			// check if there exists an edge between the user id and video id. if yes, just change the timesseen
			// and lastseen fields. else create new edge.
			
			$t_rs	=	mysql_query ("SELECT id FROM edges where name='$edgename' AND fromID='$fromID' AND toID='$toID'");
			if ( mysql_num_rows($t_rs) == 0 ){
				$timesseen = '1';
				mysql_query ("INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor,timesseen,lastseen,creationtime) VALUES ('$edgename','$tooltip','$fromID','$toID','$intensity','$edgecolor','$timesseen',current_timestamp(),current_timestamp())");
			}
			else{
				$row 	= 	mysql_fetch_array($t_rs);		
				$edgeid = 	$row['id'];
				mysql_query("UPDATE edges SET timesseen=timesseen+1,lastseen=current_timestamp() WHERE id='$edgeid'");
			}
				
			// * video nodes table
			mysql_query("UPDATE videonodes SET timesseen=timesseen+1,lastseen=current_timestamp() where id = '$toID'");
			mysql_query ("INSERT INTO useractions(action,TakenBy,TakenOn,Entity,Datetime) VALUES ('WatchVideo','$fromID','$toID','',current_timestamp())");
			mysql_query("DELETE FROM useractions WHERE action='Is watching' AND TakenBy='$fromID' AND TakenOn='$toID'");
			showSuccessMessage();
		}
		else{	
			mysql_query ("INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor,creationtime) VALUES ('$edgename','$tooltip','$fromID','$toID','$intensity','$edgecolor',current_timestamp())");	
			$t_rs	= 	mysql_query ("SELECT id,creationtime FROM edges where name='$edgename' AND fromID='$fromID' AND toID='$toID'");
			$row 	= 	mysql_fetch_array($t_rs);
			$id 	= 	$row['id'];
			$time	=	$row['creationtime'];
			echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
			echo "<rsp><message>Success</message><id>$id</id><creationtime>$time</creationtime></rsp>";
		}
					
		
	}

?>

<?php

	function changeintensity()
	{
	global $retString;
		$id			= 	$_POST['id'];
		$intensity	= 	$_POST['intensity'];
	
		$query = "UPDATE edges SET intensity = '$intensity' where id=$id";
		
		mysql_query ($query);
	
	}

?>
	
<?php
	
	function editprofile()
	{
			global $retString;
				$out = print_r($_POST, true);
		error_log("post var" . $out);
	
		//$id 			= $_POST['id'];			
		$id 			= $_POST['email'];			
		$name 			= $_POST['name'];
		$alias			= isset($_POST['alias']) ? $_POST['alias'] : $name . ' alias';
		$company		= $_POST['company'];
		$title 			= $_POST['title'];
		$nationality 	= $_POST['nationality'];
		$picture 		= $_POST['picture'];
		
		$webpage		= $_POST['webpage'];
		$email			= $_POST['email'];
		$location		= $_POST['location'];
		//$interests		= $_POST['interests'];
		$interests      =  gettagarrayid2($_POST['interests']);
		$description	= $_POST['description'];
		//error_log("$description".$description);
		$competences	= $_POST['competences'];
		
		$default_py		= '#EVERYBODY#';
		$name_py		= isset($_POST['name_py'])			? $_POST['name_py']			: $default_py;
		$title_py		= isset($_POST['invitedby_py'])	? $_POST['invitedby_py']	: $default_py;
		$company_py		= isset($_POST['company_py'])		? $_POST['company_py']		: $default_py;
		$nationality_py	= isset($_POST['nationality_py'])	? $_POST['nationality_py']	: $default_py;
		$picture_py		= isset($_POST['picture_py'])		? $_POST['picture_py']		: $default_py;
		$url_py			= isset($_POST['url_py'])			? $_POST['url_py']			: $default_py;
		$interests_py	= isset($_POST['interests_py'])	? $_POST['interests_py']	: $default_py;
		$profile_py		= isset($_POST['profile_py'])		? $_POST['profile_py']		: $default_py;
		$emailid_py		= isset($_POST['emailid_py'])		? $_POST['emailid_py']		: $default_py;
		$location_py	= isset($_POST['location_py'])		? $_POST['location_py']		: $default_py;
//		$competences_py	= isset($_POST['competences_py'])	? $_POST['competences_py']	: $default_py;
		
		// parse $interests and add new ones to tagnodes table
		$fin_interests	=	'';
		
		$list = explode(',',$interests);


		foreach( $list as $single ){
              $single = trim($single," ,\t,\n,\r");
			//$rs = mysql_query ("SELECT name from tagnodes where name='$single'");
			$rs = mysql_query ("SELECT name from tagnodes where id='$single'");
			//Thses are used for testing only
			
		/*	$mingle = "        tag1234  1 ";
			$mingle = trim($mingle," ,\t,\n,\r");
			error_log("the ids are".$single);
			error_log("the mingle is".$mingle); 
			*/
			
			if ( mysql_num_rows($rs) == 0 && trim($single)!=''){
				//error_log("inside".$single);
				//mysql_query ("INSERT INTO tagnodes(name,submittime) VALUES('$single',current_timestamp())");
				$rstag = mysql_query ("SELECT max(number) FROM tagnodes");
		while ($row1 = mysql_fetch_array($rstag))
		{
			$number = $row1[0];
		}
		
		$number+=1;
		mysql_query ("INSERT INTO tagnodes(number,id,name,nodetype,picture,submittime) VALUES ('$number','Tag$number','".$single."','Tags','media/tag_img.jpg',current_timestamp())");

				
				//$fin_interests 	= 	$single.','.$fin_interests;
				$fin_interests 	= 	'Tag'.$number.','.$fin_interests;
			}
			else{
				$row 			= 	mysql_fetch_array($rs);
				//$temp			= 	$row['name'];	
				$temp			= 	$single;	
				$fin_interests	=	$temp.','.$fin_interests;
			}
		}
		
		$fin_interests 	= substr($fin_interests, 0, -1);
		
		$splcharacters = array("&","'","<",">");	
		$description = str_replace($splcharacters, "",$description);	
		
		
		$query = "UPDATE peoplenodes SET name='$name',alias='$alias',company='$company',title='$title',nationality='$nationality',picture='$picture',url='$webpage',emailid='$email',location='$location',interests='$fin_interests',profile='$description',competences='$competences',
				name_py='$name_py',company_py='$company_py',title_py='$title_py',nationality_py='$nationality_py',picture_py='$picture_py',url_py='$url_py',emailid_py='$emailid_py',interests_py='$interests_py',profile_py='$profile_py',location_py='$location_py',competences_py='$competences_py' WHERE id='$id'";
		
		/*$filename = 'demo.txt';
		$saveFile = fopen($filename, "w");
		fwrite ($saveFile, $query);
		fclose($saveFile);
		*/
		
		//error_log($query);
		mysql_query ($query) or die(mysql_error());
		
	//	showSuccessMessage();
	
	
	    $retString = $retString . "<rsp><entity>$id</entity><message>Success</message></rsp>" ;
	echo $retString;
	
	}

?>

<?php

function chPwd() {
global $retString;
	$uid = $_REQUEST['loginId'];
	$newPwd = $_REQUEST['newPwd'];
	
	
	$count = mysql_query("UPDATE keystable SET ikey='$newPwd' WHERE userid='$uid'");
	if($count > 0)
		$message="Success";
	else
		$message="Failure" . mysql_error();
	
	
	 $retString = $retString . "<rsp><entity>$uid</entity><message>$message</message></rsp>" ;
	echo $retString;
}

?>

<?php

	function deleteedge()
	{
	global $retString;
		$id		= 	$_POST['id'];
		$query		=	"DELETE FROM edges WHERE id=$id";
		mysql_query ($query);
	}

?>

<?php

	function showSuccessMessage(){
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
		echo "<rsp><message>Success</message></rsp>";
	}

?>
<?php
function gettagarrayid($tagname)
{
$tags=explode(",",$tagname);
//error_log("The tags which i specified are" .$tags[1].','.$tags[0]);

	for($i=0; $i<count($tags);$i++)
	{
	$tags[$i] = trim($tags[$i]," ,\t,\n,\r");
	}
	$tags = array_unique($tags);
	
	
	$id = $row['id'];
	$temptagsid = array();
	for($i=0; $i<count($tags);$i++)
	{ 
	$tags[$i] = trim($tags[$i]," ,\t,\n,\r");
	$muery=mysql_query("select * from tagnodes where name='$tags[$i]'");
			if(mysql_num_rows($muery) > 0)
			{
				while($now=mysql_fetch_array($muery))
					{
					$temptagsid[$i] = $now['id'];
					error_log("pingooo" .$temptagsid[$i]);
					}
			}
			else
			{
				$juery=mysql_query("select * from tagnodes ");
					while($how=mysql_fetch_array($juery))
					{
						$number = $how['number'];
					}
					$number = $number+1;
				$taginsert = 'Tag'.$number;
				$tags[$i] = trim($tags[$i]," ,\t,\n,\r");
				if (trim($tags[$i])!=''){
					$squery="INSERT INTO tagnodes(name, nodetype, picture, id) VALUES ('$tags[$i]','Tags','media/tag_img.jpg','$taginsert')";
	
					mysql_query ($squery) or die(mysql_error());  
					error_log("the other query" .$squery);	
					$temptagsid[$i] = $taginsert;
				}
			}
	
	}	
	
	$currtags = "";
	foreach($temptagsid as $temptag )
	{
	$currtags .= $temptag."," ;
	}
	$currtags= substr($currtags, 0, -1);
	error_log("the current tag of the query" .$currtags);	
	return $currtags;
	}
	
	
	
	
function gettagarrayid2($tagname)
{
$tags=explode(",",$tagname);
//error_log("The tags which i specified are" .$tags[1].','.$tags[0]);
//	$id = $row['id'];
	
	$temptagsid = array();
	for($i=0; $i<count($tags);$i++)
	{
	$tags[$i] = trim($tags[$i]," ,\t,\n,\r");
	}
	$tags = array_unique($tags);
	
	for($i=0; $i<count($tags);$i++)
	{ 
//	error_log("pingooo" .$tags[$i]);
	$tags[$i] = trim($tags[$i]," ,\t,\n,\r");
	$muery=mysql_query("select * from tagnodes where name='$tags[$i]'");
			if(mysql_num_rows($muery) > 0)
	{
	while($now=mysql_fetch_array($muery))
	{
	$temptagsid[$i] = $now['id'];
//	error_log("pingooo" .$temptagsid[$i]);
	}
	}
		else
	$temptagsid[$i] = $tags[$i];
//	error_log("other" .$temptagsid[$i]);
	/*{
	$juery=mysql_query("select * from tagnodes ");
	while($how=mysql_fetch_array($juery))
	{
	$number = $how['number'];
	}
	$number = $number+1;
	$taginsert = 'Tag'.$number;
	$squery="INSERT INTO tagnodes(name, nodetype, picture, id) VALUES ('$tags[$i]','Tags','media/tag_img.jpg','$taginsert')";
	mysql_query ($squery) or die(mysql_error());  
	error_log("the other query" .$squery);	
	$temptagsid[$i] = $taginsert;
	}*/
	
	}	
	$currtags = "";
	foreach($temptagsid as $temptag )
	{
	$currtags .= $temptag."," ;
	}
	$currtags= substr($currtags, 0, -1);
//	error_log("the current tag of the query" .$currtags);	
	return $currtags;
	}


function sendRegisterConfirm($email,$name,$password){

global $full_tubename ;
global $full_tubeurl ;
global $retString;

$to      = $email;
//$to      = 'pkmittal82@gmail.com';
$subject = '['.$full_tubename.'] '.'Registration Confirmation Message';
$message = 'Dear' .' '. $name.', '.'Thanks for registering to '.$full_tubename."<br>" ;	
$message = $message . 'The link to '.$full_tubename.' is following '."<br>" .$full_tubeurl;
$message = $message . "<br><br>" . "Username: " . $email;
$message = $message . "<br>" . "Password/Key: " . $password;


$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: CALTTubes@insead.edu' . "\r\n" .
    'Reply-To:' .$to . "\r\n" .
	'BCC:' .'pkmittal82@gmail.com,Marco.Luccini@insead.edu' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
	
		
$val=mail($to, $subject, $message, $headers);
	return $val;

}
	
function sendmail($fromuser,$touser,$message)
{
global $full_tubename ;
global $full_tubeurl ;


$to      = $touser;

$muery=mysql_query("select * from peoplenodes where id='$fromuser'");
while($now=mysql_fetch_array($muery))
	{
	$fromusername= $now['name'];
	break;
	}
$subject = '['.$full_tubename.']'.' Comment posted in your profile by '.$fromusername;
//$message = $retstring;
$headers = 'From:'.$fromuser."\r\n" .
    'Reply-To:'.$fromuser."\r\n" .
    'X-Mailer: PHP/' . phpversion();
	
$message1 = $fromusername.' has posted the comment in your profile in '.$full_tubename."\r\n\n" ;	
$message2 = 'The link to '.$full_tubename.' is following '."\r\n" .$full_tubeurl;
$message3 = $message1.$message."\r\n\n".$message2;

$val=mail($to, $subject, $message3, $headers);

if($val==true)
  echo "Success";
else if($val==false)
  echo "Failure"; 
}	
	

function sendCommentsMail($fromuser,$videoId,$message)
{
global $full_tubename ;
global $full_tubeurl ;
global $retString;

$muery=mysql_query("select * from videonodes where id='$videoId'");
while($row1=mysql_fetch_array($muery))
	{
	 $vidName = $row1['name'];
	$authors= $row1['authors'];
	break;
	}




$to      = $authors;

$muery=mysql_query("select * from peoplenodes where id='$fromuser'");
while($now=mysql_fetch_array($muery))
	{
	$fromusername= $now['name'];
	break;
	}
$subject = '['.$full_tubename.']'.' Comment posted in video "'. $vidName . '" by '.$fromusername;
//$message = $retstring;
$headers = 'From:'.$fromuser."\r\n" .
    'Reply-To:'.$fromuser."\r\n" .
    'X-Mailer: PHP/' . phpversion();
	
$message1 = $fromusername.' has posted the comment in video "'. $vidName . '" in '.$full_tubename."\r\n\n";	
$message2 = 'The link to '.$full_tubename.' is following '."\r\n" .$full_tubeurl;
$message3 = $message1.$message."\r\n\n".$message2;

$val=mail($to, $subject, $message3, $headers);

if($val==true)
  echo "Success";
else if($val==false)
  echo "Failure"; 
}	
	

function getvideocomments(){
global $retString;
 $videoId  = $_REQUEST['videoid'];
   //  $videoId ="Video200";
     $query = "select * from commentstable where videoid='$videoId'";
	 $rs=mysql_query ($query);   
     $retString = $retString . '<video>';	 
     while ($row = mysql_fetch_array($rs))	 
     {
	 
		  $commText = "<![CDATA[" . $row['comment'] . "]]>";
		  
          $retString = $retString .'<comment>';
		  $retString=$retString ."<authorid>".$row['authorid']."</authorid>"."<datetime>".$row['datetime']."</datetime>"."<videoid>".$row['videoid']."</videoid>"."<commenttext>".$commText."</commenttext>"."<cid>".$row['cid']."</cid>";
		  ;
		$retString = $retString .'</comment>';
	 }
	 $retString = $retString .'</video>';
	 echo $retString;

}
	
	
	
?>
