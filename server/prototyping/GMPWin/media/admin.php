<?php

	include "conninnotube.php";

	$action = $_REQUEST['action'];
	
	switch($action)
	{
	case 'addvideo':
		addvideonode();
		break;
	
	case 'addpeoplenode':
		addpeoplenode();
		break;
	
	case 'changerating':
		changerating();
		break;
	
	case 'addcomments':
		addcomments();
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
	
	case 'editprofile':
		editprofile();
		break;
		
		
	// deleting objects in admin mode

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
					
$additiontype	= $_POST['additiontype'];
$nodename 		= $_POST['nodename'];
$tags			= $_POST['tags'];
$submittedby	= $_POST['submittedby'];
$category		= $_POST['category'];
$picture		= $_POST['picture'];
$url			= $_POST['url'];
$authors		= $_POST['authors'];
$inspiredby		= $_POST['inspiredby'];
$parentvideo	= $_POST['parentvideo'];		
$sourcetype		= $_POST['sourcetype'];		
		
	

$rs 			= mysql_query ("SELECT max(number) FROM videonodes");
$row 			= mysql_fetch_array($rs);
$videonumber 	= $row['0'];
$videonumber+=1;
$VideoId 		= 'Video'.$videonumber; //CONCAT('Video','$videonumber');
 

// * addition type can be 'NEW VIDEO' or 'VERSIONING'

// * how to retrieve select unix_timestamp(time) from table


if( $additiontype == 'NEW VIDEO'){
	
	$query	=	"INSERT INTO videonodes(id,name,nodetype,comments,tags,grandrating,submittedby,submittime,category,picture,url,authors,inspiredby,source) VALUES ('$VideoId','$nodename','Videos','','$tags','1','$submittedby',current_timestamp(),'$category','$picture','$url','$authors','$inspiredby','$sourcetype')";
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
	$query="INSERT INTO videonodes(id,name,nodetype,comments,tags,grandrating,submittedby,submittime,category,picture,url,authors,inspiredby,previousversion,parentvideo,versionnum,source) VALUES('$VideoId','$nodename','Videos','','$tags','1','$submittedby',current_timestamp(),'$category','$picture','$url','$authors','$inspiredby','$previousversion','$parentvideo',$version_count,'$sourcetype')";
	//echo $query;
	mysql_query ($query) or die(mysql_error());   		  			
	mysql_query ("UPDATE videonodes SET nextversion ='$VideoId' where id = '$previousversion'");
	
}





	
// create is a version of edges !!
	
if ( $previousversion != '') {
	$t_rs=mysql_query ("SELECT edgecolor,tooltip,intensity FROM edgetypes where name='Is version of'");
	while ( $row = mysql_fetch_array($t_rs) ){
		$edgecolor 	= $row['edgecolor'];
		$tooltip 	= $row['tooltip'];	
		$intensity  = $row['intensity'];
	}
	mysql_query ("INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor) VALUES ('Is version of','$tooltip','$VideoId','$previousversion','$intensity','$edgecolor')");	
}







// create inspired by edges !!

$inspiredby		= $_POST['inspiredby'];

if ( $inspiredby != '') {		// the video has some inspirations
	$t_rs=mysql_query ("SELECT edgecolor,tooltip,intensity FROM edgetypes where name='Is inspired by'");
	while ( $row = mysql_fetch_array($t_rs) ){
		$edgecolor 	= $row['edgecolor'];
		$tooltip 	= $row['tooltip'];	
		$intensity  = $row['intensity'];
	}
	
	$insp_array = explode(',',$inspiredby);
	foreach($insp_array as $insp_id)
		mysql_query ("INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor) VALUES ('Is inspired by','$tooltip','$VideoId','$insp_id','$intensity','$edgecolor')");	
		
}











// create has submitted edge

$t_rs=mysql_query ("SELECT edgecolor,tooltip,intensity FROM edgetypes where name='Has submitted'");
while ( $row = mysql_fetch_array($t_rs) ){
	$edgecolor 	= $row['edgecolor'];
	$tooltip 	= $row['tooltip'];	
	$intensity  = $row['intensity'];
}
mysql_query ("INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor) VALUES ('Has submitted','$tooltip','$submittedby','$VideoId','$intensity','$edgecolor')");






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
	$rs = mysql_query ("SELECT id from tagnodes where name='$indiv_tag'");
	
	if ( mysql_num_rows($rs) == 0 ){
		$rs 		= mysql_query ("SELECT max(number) FROM tagnodes");
		$row 		= mysql_fetch_array($rs);
		$tagnumber 	= $row['0'];	
		$tagnumber+=1;
		$tagId = 'Tag'.$tagnumber;
		
		mysql_query ("INSERT INTO tagnodes(id,name,nodetype,picture,submittedby,submittime) VALUES ('$tagId','$indiv_tag','Tags','media/tag_img.jpg','$submittedby',current_timestamp())");
		$sql="INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor) VALUES ('Has tags','$tooltip','$VideoId','$tagId','$intensity','$edgecolor')";
	}
	else{
		$row = mysql_fetch_array($rs);
		$tagId = $row['id'];
		$sql="INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor) VALUES ('Has tags','$tooltip','$VideoId','$tagId','$intensity','$edgecolor')";
	}
		

	mysql_query ($sql) or die(mysql_error());

}

// * good :)
showSuccessMessage();


}


?>


<?php
function addpeoplenode()
{
	$id				= $_POST['email'];
	$name 			= $_POST['name'];
	
	$company		= $_POST['company'];
	$title 			= $_POST['title'];
	$nationality 	= $_POST['nationality'];
	$picture 		= $_POST['picture'];
	
	$webpage		= $_POST['webpage'];
	$email			= $_POST['email'];
	$location		= $_POST['location'];
	$interests		= $_POST['interests'];
	$description	= $_POST['description'];
	
	
	// parse $interests and add new ones to controlpanel table
	$fin_interests	=	'';
	
	$list = explode(',',$interests);
	foreach( $list as $single ){
		$rs = mysql_query ("SELECT interests from controlpanel where interests='$single'");
		if ( mysql_num_rows($rs) == 0 ){
			mysql_query ("INSERT INTO controlpanel(interests) VALUES('$single')");
			$fin_interests 	= 	$single.','.$fin_interests;
		}
		else{
			$row 			= 	mysql_fetch_array($rs);
			$temp			= 	$row[interests];	
			$fin_interests	=	$temp.','.$fin_interests;
		}
	}
	
	$fin_interests 	= substr($fin_interests, 0, -1);
	
	$query			= "INSERT INTO peoplenodes(id,name,nodetype,company,title,nationality,picture,url,emailid,location,interests,profile,loggedin,grandscore,jointime,timesvisited,invite) VALUES ('$id','$name','People','$company','$title','$nationality','$picture','$webpage','$email','$location','$fin_interests','$description',0,0,current_timestamp(),0,'')";
	mysql_query ($query) or die(mysql_error());
	
	
	// insert data into keys
	
	mysql_query ("INSERT INTO keystable(ikey,userid) VALUES ('lab1','$id')") or die(mysql_error());
	
	
	


	showSuccessMessage();
}

?>



<?php
function addcomments()
{

$nodeid 	= $_POST['nodeid'];
$comments 	= $_POST['comments'];

$query = "UPDATE videonodes SET comments = CONCAT(comments,'$comments') where id = '$nodeid'";
mysql_query ($query);

showSuccessMessage();
}


?>



<?php
function changerating()
{
	$videoid = $_POST['videoid'];
	$userid = $_POST['userid'];
	$rating = $_POST['rating'];
	
	$rs = mysql_query ("SELECT rating from videonodes where id ='videoid'");
	while ($row = mysql_fetch_array($rs))
	{
	$currrating = $row[0];
	}
	echo $currrating;
	$splitrating = explode("*", $currrating);
	$newusers = $splitrating[1]+1;
	$newrating = (($splitrating[0] * $splitrating[1])+ ($rating))/($newusers);
	$combine = $newrating.'*'.$newusers ;
	$query = "UPDATE videonodes SET rating = '$combine' where id = 'videoid'";
	mysql_query ($query);
	
	//add log message for that user.
	
	showSuccessMessage();
	}
?>





<?php
function addtagnode()
{
	
	$name		= $_POST['name'];
	$nodetype 	= $_POST['nodetype'];
	$picture	= $_POST['picture'];
	
	$rs = mysql_query ("SELECT max(number) FROM tagnodes");
	while ($row = mysql_fetch_array($rs))
	{
		$number = $row[0];
	}
	
	$number+=1;
	
	mysql_query ("INSERT INTO tagnodes(id,name,nodetype,picture) VALUES ('Tag$number','$name','$nodetype','$picture')");
	showSuccessMessage();
	}

?>
	
<?php
function addedge()
{
	
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
			mysql_query ("INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor,timesseen,lastseen) VALUES ('$edgename','$tooltip','$fromID','$toID','$intensity','$edgecolor','$timesseen',current_timestamp())");
		}
		else{
			$row 	= 	mysql_fetch_array($t_rs);		
			$edgeid = 	$row['id'];
			mysql_query("UPDATE edges SET timesseen=timesseen+1,lastseen=current_timestamp() WHERE id='$edgeid'");
		}
			
		// * video nodes table
		mysql_query("UPDATE videonodes SET timesseen=timesseen+1,lastseen=current_timestamp() where id = '$toID'");
		mysql_query("DELETE FROM useractions WHERE action='Is watching' AND TakenBy='$fromID' AND TakenOn='$toID'");
		showSuccessMessage();
	}
	else{	
		mysql_query ("INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor) VALUES ('$edgename','$tooltip','$fromID','$toID','$intensity','$edgecolor')");	
		$t_rs	= 	mysql_query ("SELECT id FROM edges where name='$edgename' AND fromID='$fromID' AND toID='$toID'");
		$row 	= 	mysql_fetch_array($t_rs);
		$id 	= 	$row['id'];
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
		echo "<rsp><message>Success</message><id>$id</id></rsp>";
	}
				
	
}

?>


<?php
function changeintensity()
{
	$id			= 	$_POST['id'];
	$intensity	= 	$_POST['intensity'];

	$query = "UPDATE edges SET intensity = '$intensity' where id=$id";
	
	mysql_query ($query);

}

?>


<?php
function editprofile()
{
		
	$id 			= $_POST['id'];			
	$name 			= $_POST['name'];
	$company		= $_POST['company'];
	$title 			= $_POST['title'];
	$nationality 	= $_POST['nationality'];
	$picture 		= $_POST['picture'];
	
	$webpage		= $_POST['webpage'];
	$email			= $_POST['email'];
	$location		= $_POST['location'];
	$interests		= $_POST['interests'];
	$description	= $_POST['description'];

	// parse $interests and add new ones to controlpanel table
	$fin_interests	=	'';
	
	$list = explode(',',$interests);
	foreach( $list as $single ){
		$rs = mysql_query ("SELECT interests from controlpanel where interests='$single'");
		if ( mysql_num_rows($rs) == 0 ){
			mysql_query ("INSERT INTO controlpanel(interests) VALUES('$single')");
			$fin_interests 	= 	$single.','.$fin_interests;
		}
		else{
			$row 			= 	mysql_fetch_array($rs);
			$temp			= 	$row[interests];	
			$fin_interests	=	$temp.','.$fin_interests;
		}
	}
	
	$fin_interests 	= substr($fin_interests, 0, -1);
		
	$query			= "UPDATE peoplenodes SET name='$name',company='$company',title='$title',nationality='$nationality',picture='$picture',url='$webpage',emailid='$email',location='$location',interests='$fin_interests',profile='$description' WHERE id='$id'";
	
	$filename = 'demo.txt';
	$saveFile = fopen($filename, "w");
	fwrite ($saveFile, $query);
	fclose($saveFile);
	
	mysql_query ($query) or die(mysql_error());
	
	showSuccessMessage();

}

?>



<?php
function deleteedge()
{
	$id		= 	$_POST['id'];

	$query		=	"DELETE FROM edges WHERE id=$id";
	
	mysql_query ($query);
	
}

?>









<?php
function deletevideo()
{
	showSuccessMessage();
}

?>

<?php
function deleteuser()
{
	showSuccessMessage();
}

?>



<?php
	/*	*	delete nodes with the id in tag nodes table
		*	delete tags in tags field in video nodes 
	*/
function deletetag()
{
	$tag		= 	$_POST['tag'];
	
	$rs 	= mysql_query ("SELECT id tagnodes WHERE name=$tag");
	$row 	= mysql_fetch_array($rs);
	$id 	= $firstname_row['id'];
	
	showSuccessMessage();
}

?>
<?php
function deletecomment()
{
	showSuccessMessage();
}

?>









<?php
	
	function showSuccessMessage(){
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
		echo "<rsp><message>Success</message></rsp>";
		}
	
?>

