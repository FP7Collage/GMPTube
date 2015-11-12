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


<html>

<head>
<script>
function validateForm()
{
var x=document.forms["addmultivideos"]["numvideo"].value;
if  ((x==null || x=="") || (x <0 && x >5))
  {
  alert("Please enter the number of videos( between 1 to 5)");
  return false;
  }
}
</script>
</head>




<body>


<font size="5" color ="red"> 
<b>  Fetch Agent  </b> </font>
<br> <br>


<form action="fetchagent.php" method="post">
<input type="hidden" name="action" value="cleanvcm" />
<input type="submit" value="Clean Fetch Agent Data" />
</form>

<!--
<form action="fetchagent.php" method="post">
<input type="hidden" name="action" value="addvideo" />
<input type="submit" value="Upload One Video from YouTube" />
</form>

-->

<font size="2" color ="blue"> <b>Keywords:</b>  collaboration, participation, WEB2.0, wiki, blogs, social media, social networking, social networks, social software, community, Twitter, facebook, LinkedIn, Viadeo, Xing, Yammer
</font>
<br>

<br>
<form name= "addmultivideos" action="fetchagent.php" method="post" onsubmit="return validateForm()">
<font size="4" color ="red"> Search Based On</font>
<br>
<input type="radio" name="sc" value="0" checked>Single Keyword<br>
<input type="radio" name="sc" value="1">Keyword and Performance  <br>
<input type="radio" name="sc" value="2">Collaborative Peformance<br>
<br>
<input type="hidden" name="action" value="addmultivideos" />
Enter the number of videos to be uploaded (1 to 5).

<input type="text" size="10" name="numvideo" value="5" />
<input type="submit" value="Upload Videos from YouTube" />
</form>


</body>
</html> 


<?php

	include "conntube.php";
	global $entity;
	global $userType;
	global $maxSearchResults;	
	global $keywords;
	global $keytags;

	
	

	$keytags =array("collaboraive peformance", "collaboraive" , "performance" );
	$keywords =array("collaboration", "participation", "WEB2.0", "wiki", "blogs", "social media", "social networking", "social networks", "social software", "community", "Twitter", "facebook", "LinkedIn", "Viadeo", "Xing", "Yammer");
	
	$maxSearchResults = 5;
	
	$action		= isset ($_REQUEST['action']) ? $_REQUEST['action'] : 'pkmittal82@gmail.com';
	 
	$userType =  'FA';
	
	switch($action)
	{
		case 'addvideo':
			addvideonode();
			break;
			
	    case 'addmultivideos':
			addmultivideos();
			break;
		
		
			
		case 'cleanvcm':
			cleanvcm();
			break;
			

		default:
			break;	
	}

?>

<?php
  function test(){
  require_once 'Zend/Loader.php'; // the Zend dir must be in your include_path
Zend_Loader::loadClass('Zend_Gdata_YouTube');


$yt = new Zend_Gdata_YouTube();

//$videoEntry = $yt->getVideoEntry('the0KZLEacs');
//printVideoEntry($videoEntry);

//echo "Hello";

//searchAndPrint('innovation');
   
   searchAndPrint('innovation');
  
  }
  
  
?>


<?php

function addmultivideos(){
global $keywords , $keytags , $sc, $numVideo;

$numVideos	= isset ($_REQUEST['numvideo']) ? $_REQUEST['numvideo'] : 5;
$sc	= isset ($_REQUEST['sc']) ? $_REQUEST['sc'] : "0";


for( $i=0;$i<$numVideos;$i++){
  addvideonode();
}




}


?>



	
<?php

	function addvideonode()
	{
	//youtube api key, currently not used
	//AI39si7rHDKrtblS5zIMW6S_nf8X0BEqdl_7p7e12GUTIBqVdYeqeXztNSM0MX5cX44bFpKCbVB7yXmFyxwp0hPncD2q8nCA0A
	
	require_once 'Zend/Loader.php'; // the Zend dir must be in your include_path
	Zend_Loader::loadClass('Zend_Gdata_YouTube');
	
	    $yt = new Zend_Gdata_YouTube();
		
        global	$record_path;
		global $userType;
		global $keywords , $keytags , $sc, $numVideo;
		
	    		
		if($sc ==0){
				shuffle($keywords);
		        $randKey = array_rand($keywords);		
				$searchkeyword = $keywords[$randKey];
				$vcmcategory= "Web2.0";		
		}
		else if($sc ==1){
				shuffle($keywords);
				$randKey = array_rand($keywords);
                $searchkeyword = $keywords[$randKey];				
				$searchkeyword  = $searchkeyword  . " AND " . "Performance";
		  		$vcmcategory= "Experiences";
		}else if ($sc ==2){
		   shuffle($keytags);
		  $searchkeyword = $keytags[array_rand($keytags)];
		  $vcmcategory= "Experiences";
		}
		
		
		$vcmadditiontype = 'NEW VIDEO';
		
		
		
		$vcmtags= $searchkeyword; 
		$newVideoEntry = searchRandomVideo($searchkeyword);		
		
		$vcmnodename1 = $newVideoEntry->getVideoTitle();
		$splcharacters = array("&","'","<",">","\"", ",");	
		$vcmnodename = str_replace($splcharacters, "",$vcmnodename1);
		
		//$vcmdescription = $newVideoEntry->getVideoDescription();		
		$vcmdescription = $vcmnodename;
								
		$vcmurl =  "http://www.youtube.com/v/". $newVideoEntry->getVideoId();	
		$vcmpicture = "http://img.youtube.com/vi/" .$newVideoEntry->getVideoId() ."/default.jpg";

		$vcmauthors = "ragent@ragent.com";
		$vcmsubmittedby = $vcmauthors ;
		
		/*  To assing the random author for submitted by
		$rs1 = mysql_query("Select id from peoplenodes where userType='$userType' order by rand() limit 1");
	    $row1 = mysql_fetch_array($rs1);	   
	    $vcmsubmittedby = $row1['id'];	   	   
		$vcmauthors = $vcmsubmittedby;		
		*/
						
		
		
		/*
		$arr = $newVideoEntry->getVideoTags();		
		$alen = min(sizeof($arr),3);		
		for( $i=0;$i<$alen;$i++){
		   if($i==0){
		   	   $vcmtags = $arr[$i];
			   }else{
			   $vcmtags = $vcmtags . "," . $arr[$i];
			   }
		}
		//$vcmtags = implode(", ", $newVideoEntry->getVideoTags()) ;						
		*/
		
		$vcmsourcetype = "YTB";
		$vcmparentvideo = "";
		$vcminspiredby = "";		
		$additiontype	= $vcmadditiontype;
		$nodename 		= $vcmnodename;
		$tags			= $vcmtags;
		$submittedby	= $vcmsubmittedby;
		$category		= $vcmcategory;
		$picture		= $vcmpicture;
		$url			= $vcmurl;
		$authors		= $vcmauthors;
		$inspiredby		= $vcminspiredby;
		$parentvideo	= $vcmparentvideo;
		$sourcetype		= $vcmsourcetype;	
		//$externalLinks	= $_POST['externalLinks'];
		//$docLinks		= $_POST['docLinks'];
		$externalLinks	= "";
		$docLinks		= "";
		$docLinks1="";
		
		$description	= $vcmdescription;
		$video_py		= '#EVERYBODY#';
		
		
		$rs 			= mysql_query ("SELECT max(number) FROM videonodes");
		$row 			= mysql_fetch_array($rs);
		$videonumber 	= $row['0'];
		$videonumber+=1;
		$VideoId 		= 'Video'.$videonumber; //CONCAT('Video','$videonumber');
		
		/*
		$docLinksArray = explode('|',$docLinks);
		foreach($docLinksArray as $file){
			if($file!= '')
			{
				rename('./upload/'.$file,'./upload/'.$VideoId.'_'.$file);
			}
		}
		if($docLinks=="")
		{
		    $docLinks1="";
		}
		else
		{
		  $docLinks1 = str_replace('|', '|'.$VideoId.'_',$docLinks);
		  $docLinks1 = $VideoId.'_'.$docLinks1;
		}
		*/
		  
		  $entity			=	$VideoId;	// this value will be returned to the client	
		
		// * addition type can be 'NEW VIDEO' or 'VERSIONING'		
		// * how to retrieve select unix_timestamp(time) from table
		
		$previousversion = '';
		
		if( $additiontype == 'NEW VIDEO'){
			
			$query = "INSERT INTO videonodes(timesrated,id,name,nodetype,comments,tags,grandrating,submittedby,submittime,category,picture,url,authors,inspiredby,source,externalLinks,docLinks,description,video_py,userType)
			VALUES ('0','$VideoId','$nodename','Videos','','$tags','0','$submittedby',current_timestamp(),'$category','$picture','$url','$authors','$inspiredby','$sourcetype','$externalLinks','$docLinks1','$description','$video_py','$userType')";
			
			//error_log($query);
			
			mysql_query ($query) or die(mysql_error());   		   		  			
		
		}
		/*
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
			$query="INSERT INTO videonodes(id,name,nodetype,comments,tags,grandrating,submittedby,submittime,category,picture,url,authors,inspiredby,previousversion,parentvideo,versionnum,source,externalLinks,docLinks,description,video_py,userType)
			VALUES('$VideoId','$nodename','Videos','','$tags','1','$submittedby',current_timestamp(),'$category','$picture','$url','$authors','$inspiredby','$previousversion','$parentvideo',$version_count,'$sourcetype','$externalLinks','$docLinks1','$description','$video_py','$userType')";
			//echo $query;
			mysql_query ($query) or die(mysql_error());   		  			
			mysql_query ("UPDATE videonodes SET nextversion ='$VideoId' where id = '$previousversion'");
			
		}
*/

/*			
		// create Is previous version edges !!
		if ( $previousversion != '') {
			$t_rs=mysql_query ("SELECT edgecolor,tooltip,intensity FROM edgetypes where name='Is previous version'");
			while ( $row = mysql_fetch_array($t_rs) ){
				$edgecolor 	= $row['edgecolor'];
				$tooltip 	= $row['tooltip'];	
				$intensity  = $row['intensity'];
			}
			mysql_query ("INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor,creationtime,userType) VALUES ('Is previous version','$tooltip','$previousversion','$VideoId','$intensity','$edgecolor',current_timestamp(),'$userType')");	
		}
*/
		
/*		// create Has inspired edges !!
			$hasinspired = '';
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
				mysql_query ("INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor,creationtime,userType) VALUES ('Is Connected To','$tooltip','$insp_id','$VideoId','$intensity','$edgecolor',current_timestamp(),'$userType')");	
				
		}
*/		
		// create has submitted edge
		$t_rs=mysql_query ("SELECT edgecolor,tooltip,intensity FROM edgetypes where name='Has submitted'");
		while ( $row = mysql_fetch_array($t_rs) ){
			$edgecolor 	= $row['edgecolor'];
			$tooltip 	= $row['tooltip'];	
			$intensity  = $row['intensity'];
		}
	mysql_query ("INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor,creationtime,userType) VALUES ('Has submitted','$tooltip','$submittedby','$VideoId','$intensity','$edgecolor',current_timestamp(),'$userType')");
		
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
			$sql="select * from videonodes";
			if ( mysql_num_rows($rs) == 0 ){
				$rs 		= mysql_query ("SELECT max(number) FROM tagnodes");
				$row 		= mysql_fetch_array($rs);
				$tagnumber 	= $row['0'];	
				$tagnumber+=1;
				$tagId = 'Tag'.$tagnumber;
				if(trim($indiv_tag)!='')
				{
				mysql_query ("INSERT INTO tagnodes(id,name,nodetype,picture,submittedby,submittime) VALUES ('$tagId','$indiv_tag','Tags','media/tag_img.jpg','$submittedby',current_timestamp())");
				
				$sql="INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor,creationtime,userType) VALUES ('Has tags','$tooltip','$VideoId','$tagId','$intensity','$edgecolor',current_timestamp(),'$userType')";
				}
			}
			else{
				$row = mysql_fetch_array($rs);
				$tagId = $row['id'];
				$sql="INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor,creationtime,userType) VALUES ('Has tags','$tooltip','$VideoId','$tagId','$intensity','$edgecolor',current_timestamp(),'$userType')";
			}
			
			mysql_query ($sql) or die(mysql_error());
		}
		
		// * good :)
		
		echo "<br> The Video <a target=\"_blank\" href=\"" . $vcmurl ."\">" . $vcmnodename ."</a> has been uploaded by user <b>" . $vcmsubmittedby ."</b>.  for the search keyword <b>" . $searchkeyword ."</b>";
		//echo "The url of the video is <a target=\"_blank\" href=\"" . $vcmurl ."\">". $vcmurl ."</a>";
	
	    
	}

?>


<?php

	function addpeoplenode()
	{  
	   global $userType;
	   $randomName = get_rand_letters(5);
	   
	   
	  $rlist1 = mysql_query("Select name,pic,email,location from peoplelist where used ='0' order by pic limit 1");
	   $numberppl = mysql_num_rows($rlist1);
		if($numberppl>0){
		   $pplrow1 = mysql_fetch_array($rlist1);	   
		   $vcmName = $pplrow1['name'];	 
		   $picname = $pplrow1['pic'];	 
		   $vcmEmailId = $pplrow1['email'];
			$vcmLocation = $pplrow1['location'];
		   
		   
		   $update1 = mysql_query("update peoplelist set used='1' where name='" . $vcmName . "'");
		   
		}else{
		  $vcmName = $randomName;
		  $picname = "default_people.jpg";
		  $vcmEmailId = $randomName."@gmail.com";
		  $vcmLocation = "France";
		}
		
		$vcmPicture = "http://labs.calt.insead.edu/prototyping/vcmtube/GraphData/media/people/" . $picname;
		 
	   
	   // it assume that atleast one vcm is present in database
	   //$randInvitedId = "pkmittal82@gmail.com";
	   $rs1 = mysql_query("Select id , name from peoplenodes where userType='$userType' order by rand() limit 1");
	   $row1 = mysql_fetch_array($rs1);	   
	   
	   $numberppl = mysql_num_rows($rs1);
	   if($numberppl == 0){
	   $vcmInvitedBy = "albert.angehrn@insead.edu";
	   	$vcmInvitedName    = "Albert Angehrn";   
	   }else{
	   $randInvitedId = $row1['id'];	   
	   $vcmInvitedBy = $randInvitedId;
	   	$vcmInvitedName    = $row1['name'];	   
		}
	   $vcmCompany = "INSEAD";
	   $vcmTitle = "Mr.";
	   $vcmNationality = "French";
	   
	   $vcmWebPage = "";
	   
	   $vcmInterests = "";	   
	   $vcmDescription = "";
	   $vcmCompetence = "";
	   
       	
		$id				= $vcmEmailId;
		$name 			= $vcmName;
		$company		= $vcmCompany;
		$title 			= $vcmTitle;
		$nationality 	= $vcmNationality;
		$picture 		= $vcmPicture;
		$webpage		= $vcmWebPage;
		$email			= $vcmEmailId;
		$location		= $vcmLocation;
		$interests		= $vcmInterests;
		$invitedby		= $vcmInvitedBy;
		$description	= $vcmDescription;
		$competences	= $vcmCompetence;
		
		$default_py		= '#EVERYBODY#';
		$name_py		= $default_py;
		$company_py		= $default_py;
		$title_py		= $default_py;
		$nationality_py	= $default_py;
		$picture_py		= $default_py;
		$url_py			= $default_py;
		$interests_py	= $default_py;
		$profile_py		= $default_py;
		$emailid_py		= $default_py;
		$location_py	= $default_py;
		$invitedby_py	= $default_py;
		$competences_py	= $default_py;
		
		$password = 'lab1'; // DEFAULT KEY
			
		// parse $interests and add new ones to tagnodes table
		$fin_interests	=	'';
		
		$list = explode(",",$interests);
		foreach( $list as $single ){
			$single = trim($single," ,\t,\n,\r");
			$rs = mysql_query ("SELECT name from tagnodes where name='$single'");
			//error_log($single);
			if ( mysql_num_rows($rs) == 0 && trim($single)!=''){
				
				//mysql_query ("INSERT INTO tagnodes(name,submittime) VALUES('$single',current_timestamp())");
				$rstag = mysql_query ("SELECT max(number) FROM tagnodes");
		while ($row1 = mysql_fetch_array($rstag))
		{
			$number = $row1[0];
		}
		
		$number+=1;
		//error_log($single);
		mysql_query ("INSERT INTO tagnodes(number,id,name,nodetype,picture,submittime) VALUES ('$number','Tag$number','".$single."','Tags','media/tag_img.jpg',current_timestamp())");

				
				$fin_interests 	= 	$single.','.$fin_interests;
			}
			else{
				$row 			= 	mysql_fetch_array($rs);
				$temp			= 	$row['name'];
				$fin_interests	=	$temp.','.$fin_interests;
			}
		}
		
		$fin_interests 	= substr($fin_interests, 0, -1);
		
		$query = "INSERT INTO
				peoplenodes(id,name,nodetype,company,title,nationality,picture,url,emailid,location,interests,profile,loggedin,grandscore,jointime,timesvisited,invite,invitedby,competences,
				alias,name_py,company_py,title_py,nationality_py,picture_py,url_py,interests_py,profile_py,emailid_py,location_py,invitedby_py,competences_py,userType)
				VALUES ('$id','$name','People','$company','$title','$nationality','$picture','$webpage','$email','$location','$fin_interests','$description',0,0,current_timestamp(),0,'','$invitedby','$competences',
				'$name','$name_py','$company_py','$title_py','$nationality_py','$picture_py','$url_py','$interests_py','$profile_py','$emailid_py','$location_py','$invitedby_py','$competences_py','$userType')";
		mysql_query ($query) or die(mysql_error());
		
		// add 1st edge for the new user, add Knows edge with $invitedby user
		$t_rs		= 	mysql_query ("SELECT edgecolor,tooltip,intensity FROM edgetypes where name='Knows'");
		$row 		= 	mysql_fetch_array($t_rs);
		$edgecolor 	= 	$row['edgecolor'];
		$tooltip 	= 	$row['tooltip'];
		$intensity  =	$row['intensity'];

		// SSKNOTE: Privacy values for the knows relationship with the user in invitedby field... is yet to be implemented.
		mysql_query ("INSERT INTO
				edges(name,tooltip,fromID,toID,intensity,edgecolor,creationtime,userType)
				VALUES ('Knows','$tooltip','$id','$invitedby','$intensity','$edgecolor',current_timestamp(),'$userType')");

		// insert data into keys
		mysql_query ("INSERT INTO
				keystable(ikey,userid,userType) VALUES ('$password','$id','$userType')") or die(mysql_error());
				
		mysql_query ("INSERT INTO
				chatusers(id,accesstime,loginstatus,userType
				)
				VALUES ('$id',current_timestamp(),false,'$userType')") or die(mysql_error());
		//showSuccessMessage();
		
			echo "<br> The new user <b>" . $vcmName . " (". $vcmEmailId .") </b> has been invited by <b>". $vcmInvitedName . " (". $vcmInvitedBy . ")" . "</b>";
	}

?>
	


<?php

	function showSuccessMessage(){
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
		echo "<rsp><message>Success</message></rsp>";
	}

?>



<?php


function get_rand_letters($length)
{
  if($length>0) 
  { 
  $rand_id="";
   for($i=1; $i<=$length; $i++)
   {
   mt_srand((double)microtime() * 1000000);
   $num = mt_rand(1,26);
   $rand_id .= assign_rand_value($num);
   }
  }
return $rand_id;
} 
function assign_rand_value($num)
{
// accepts 1 - 36
  switch($num)
  {
    case "1":
     $rand_value = "a";
    break;
    case "2":
     $rand_value = "b";
    break;
    case "3":
     $rand_value = "c";
    break;
    case "4":
     $rand_value = "d";
    break;
    case "5":
     $rand_value = "e";
    break;
    case "6":
     $rand_value = "f";
    break;
    case "7":
     $rand_value = "g";
    break;
    case "8":
     $rand_value = "h";
    break;
    case "9":
     $rand_value = "i";
    break;
    case "10":
     $rand_value = "j";
    break;
    case "11":
     $rand_value = "k";
    break;
    case "12":
     $rand_value = "l";
    break;
    case "13":
     $rand_value = "m";
    break;
    case "14":
     $rand_value = "n";
    break;
    case "15":
     $rand_value = "o";
    break;
    case "16":
     $rand_value = "p";
    break;
    case "17":
     $rand_value = "q";
    break;
    case "18":
     $rand_value = "r";
    break;
    case "19":
     $rand_value = "s";
    break;
    case "20":
     $rand_value = "t";
    break;
    case "21":
     $rand_value = "u";
    break;
    case "22":
     $rand_value = "v";
    break;
    case "23":
     $rand_value = "w";
    break;
    case "24":
     $rand_value = "x";
    break;
    case "25":
     $rand_value = "y";
    break;
    case "26":
     $rand_value = "z";
    break;
    case "27":
     $rand_value = "0";
    break;
    case "28":
     $rand_value = "1";
    break;
    case "29":
     $rand_value = "2";
    break;
    case "30":
     $rand_value = "3";
    break;
    case "31":
     $rand_value = "4";
    break;
    case "32":
     $rand_value = "5";
    break;
    case "33":
     $rand_value = "6";
    break;
    case "34":
     $rand_value = "7";
    break;
    case "35":
     $rand_value = "8";
    break;
    case "36":
     $rand_value = "9";
    break;
  }
return $rand_value;
}
?>



<?php

function searchAndPrint($searchTerms)
{
global $maxSearchResults;
  $yt = new Zend_Gdata_YouTube();
  $yt->setMajorProtocolVersion(2);
  $query = $yt->newVideoQuery();
  
   $query->setOrderBy('relevance');
 // $query->setOrderBy('viewCount');
 //  $query->setOrderBy('random');
  $query->setSafeSearch('none');
  $query->setVideoQuery($searchTerms);
    $query->setMaxResults($maxSearchResults);

  // Note that we need to pass the version number to the query URL function
  // to ensure backward compatibility with version 1 of the API.
  $videoFeed = $yt->getVideoFeed($query->getQueryUrl(2));  
 // printVideoFeed($videoFeed, 'Search results for: ' . $searchTerms);
 $randVideoEntry = getRandomVideo($videoFeed);
 
 echo 'random Video: ' . $randVideoEntry->getVideoTitle() . "\n";
  echo ' random Video ID: ' . $randVideoEntry->getVideoId() . "\n";
  
  
 
}

function searchRandomVideo($searchTerms)
{
global $maxSearchResults;
  
  //error_log("max results " . $maxSearchResults);
  
  $yt = new Zend_Gdata_YouTube();
  $yt->setMajorProtocolVersion(2);
  $query = $yt->newVideoQuery();
  
  // $query->setOrderBy('relevance');
 // $query->setOrderBy('viewCount');
 //  $query->setOrderBy('random');
  $query->setSafeSearch('none');
  $query->setVideoQuery($searchTerms);
    $query->setMaxResults($maxSearchResults);

  // Note that we need to pass the version number to the query URL function
  // to ensure backward compatibility with version 1 of the API.
  $videoFeed = $yt->getVideoFeed($query->getQueryUrl(2));  
 // printVideoFeed($videoFeed, 'Search results for: ' . $searchTerms);
 $randVideoEntry = getRandomVideo($videoFeed);
 
  return $randVideoEntry;
  
  
 
}


function getRandomVideo($videoFeed){
global $maxSearchResults;
//error_log("max results gr " . $maxSearchResults);
$totalResults = $videoFeed->totalResults;
$totalResults = (String)$totalResults;
$num = intval($totalResults);
$randLimit = 0;

if($num > $maxSearchResults){
   $randLimit = $maxSearchResults -1;
}else{
       $randLimit = $num - 1;
}
//$randLimit = 49;
//echo "Limit: " . $randLimit . " total " . $totalResults ;

 srand(time());
$random = (rand()%$randLimit);

//error_log("random" . $random);

 $videoEntry = $videoFeed[$random];
 //printVideoEntry($videoEntry);
 return $videoEntry;
 
}

function printVideoFeed($videoFeed)
{
  $count = 1;
  foreach ($videoFeed as $videoEntry) {
    echo "\n<br><br>Entry # " . $count . "\n";
    printVideoEntry($videoEntry);
    echo "\n";
    $count++;
  }
}

function printVideoEntry($videoEntry) 
{
  // the videoEntry object contains many helper functions
  // that access the underlying mediaGroup object
  echo 'Video: ' . $videoEntry->getVideoTitle() . "\n";
  echo 'Video ID: ' . $videoEntry->getVideoId() . "\n";
  echo 'Updated: ' . $videoEntry->getUpdated() . "\n";
  echo 'Description: ' . $videoEntry->getVideoDescription() . "\n";
  echo 'Category: ' . $videoEntry->getVideoCategory() . "\n";
  echo 'Tags: ' . implode(", ", $videoEntry->getVideoTags()) . "\n";
  echo 'Watch page: ' . $videoEntry->getVideoWatchPageUrl() . "\n";
  echo 'Flash Player Url: ' . $videoEntry->getFlashPlayerUrl() . "\n";
  echo 'Duration: ' . $videoEntry->getVideoDuration() . "\n";
  echo 'View count: ' . $videoEntry->getVideoViewCount() . "\n";
  echo 'Rating: ' . $videoEntry->getVideoRatingInfo() . "\n";
  echo 'Geo Location: ' . $videoEntry->getVideoGeoLocation() . "\n";
  echo 'Recorded on: ' . $videoEntry->getVideoRecorded() . "\n";
  
  // see the paragraph above this function for more information on the 
  // 'mediaGroup' object. in the following code, we use the mediaGroup
  // object directly to retrieve its 'Mobile RSTP link' child
  foreach ($videoEntry->mediaGroup->content as $content) {
    if ($content->type === "video/3gpp") {
      echo 'Mobile RTSP link: ' . $content->url . "\n";
    }
  }
  
  echo "Thumbnails:\n";
  $videoThumbnails = $videoEntry->getVideoThumbnails();

  foreach($videoThumbnails as $videoThumbnail) {
    echo $videoThumbnail['time'] . ' - ' . $videoThumbnail['url'];
    echo ' height=' . $videoThumbnail['height'];
    echo ' width=' . $videoThumbnail['width'] . "\n";
  }

}
?>


<?php
function displayControlPanel(){

global $userType;


     
	$rs = mysql_query ("SELECT id, name from peoplenodes where userType='$userType'");
	$numberOfVCM = mysql_num_rows($rs);
		while ($row = mysql_fetch_array($rs))
		{
			$vcmId = $row['id'];			
			$vcmName = $row['name'];	
			$rsInvitedBy = mysql_query ("SELECT id, name from peoplenodes where userType='$userType' and invitedby='".$vcmId."'");
			$numberOfInvitedBy = mysql_num_rows($rsInvitedBy);
			if($numberOfInvitedBy>0){
			   echo "<br><b>" . $vcmName . "</b> has invited ";
				while ($rowInvitedBy = mysql_fetch_array($rsInvitedBy))
				{ 
                  $invitedUserId = $rowInvitedBy['id'];		
				  $invitedUserName = $rowInvitedBy['name'];		
				  echo "<b>" .$invitedUserName ."</b>, ";
				}
			}	
		
		
	
		
			$rsSubmittedBy = mysql_query ("SELECT name, url from videonodes where userType='$userType' and submittedby='".$vcmId."'");
			$numberOfSubmittedBy = mysql_num_rows($rsSubmittedBy);
			if($numberOfSubmittedBy>0){
			   echo "<br><b>" . $vcmName . "</b> has submitted ";
				while ($rowSubmittedBy = mysql_fetch_array($rsSubmittedBy))
				{ 
                  $videoName = $rowSubmittedBy['name'];		
				  $videoUrl = $rowSubmittedBy['url'];		
				  echo "<br><a target=\"_blank\" href=\"". $videoUrl . "\">" .$videoName ."</a>, ";
				}
			}	
			
			
		}
}

?>


<?php
function cleanvcm(){
global $userType;

/*	$query		=	"DELETE FROM peoplenodes WHERE userType='$userType'";
	mysql_query ($query);
	
	$query		=	"DELETE FROM keystable WHERE userType='$userType'";
	mysql_query ($query);
	
	$query		=	"DELETE FROM chatusers WHERE userType='$userType'";
	mysql_query ($query);
	
*/	
	
	$query		=	"DELETE FROM videonodes WHERE userType='$userType'";
	mysql_query ($query);
	
	$query		=	"DELETE FROM edges WHERE userType='$userType'";
	mysql_query ($query);
	
/*	$query		=	"update peoplelist set used='0'";
	mysql_query ($query);
*/	
	
	echo "<br>Fetch Agent Data has been cleaned successfully";



}

?>


<br><br><font color="blue" size="5"><b>Activity Panel</b></font>
<?php
displayControlPanel();
?>


