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
//header("Content-type: text/xml");
include "conntube.php";
//session_start();
//header("Cache-control: private");
header("Content-type: text/html;charset=UTF-8");

$url = "http://laboranova.lsi.upc.edu/live/users.xml";


$xmlDoc = new DOMDocument();
//$xmlDoc->load("labo_users.xml");
$xmlDoc->load($url);
$domXPath = new DOMXPath($xmlDoc);

$id = array();
$email = array();
	$i = 0;
	$a = true;
foreach ($domXPath->query('//user') as $keyDOM) {
			$a = true;
            $id[$i] = $keyDOM->getAttribute('id');
            $email[$i] = $keyDOM->getAttribute('email') ;
			$firstname[$i]  = $keyDOM->getAttribute('first_name') ;
			$splcharacters = array("&","'");
			$lastname[$i]  = $keyDOM->getAttribute('last_name');
			$lastname[$i] = str_replace($splcharacters, "",$lastname[$i] );
			$fullname[$i] =  $keyDOM->getAttribute('full_name');
			$fullname[$i]= str_replace($splcharacters, "",$fullname[$i] );
			
			$muery = mysql_query("select * from mapping ");
		
		while($how=mysql_fetch_array($muery))
		{
		if ($id[$i] == $how['guid'])
		{
		
		$query = "UPDATE MAPPING SET guid = '$id[$i]',userid = '$email[$i]',firstname='$firstname[$i]', lastname='$lastname[$i]', fullname = '$fullname[$i]'
			WHERE guid='$id[$i]'  ";
		$a = false;	
		//error_log("the query of mapping" . $query);
		mysql_query ($query) or die("MYSQL ERROR: ".mysql_error());
		break;
		
		//mysql_query ($query) or die("MYSQL ERROR: ".mysql_error());
		}
		}
			if($a == true && $email[$i] != '')
			{
			$query = "INSERT INTO MAPPING(guid, userid, firstname, lastname, fullname)
			VALUES ('$id[$i]','$email[$i]','$firstname[$i]','$lastname[$i]','$fullname[$i]')";
			//error_log("titleee" . $query);
			mysql_query ($query) or die("MYSQL ERROR: ".mysql_error());
			}
			$i++ ;
		}
		
$i = 0;
foreach ($domXPath->query('//user') as $keyDOM) {
			$a = true;
            $id[$i] = $keyDOM->getAttribute('id');
            $email[$i] = $keyDOM->getAttribute('email') ;
			$str = strtolower($email[$i]);
			$company[$i] =	$keyDOM->getAttribute('company') ;
			$title[$i] = $keyDOM->getAttribute('job_title');
			$fullnm[$i] = $keyDOM->getAttribute('full_name');
			$splcharacters = array("&","'");
			$full_name[$i] = str_replace($splcharacters, "",$fullnm[$i]);
			$created[$i]   = $keyDOM->getAttribute('created');
			 $city[$i] =  $keyDOM->getAttribute('city');
			 
			 
			  $firstnm[$i] = $keyDOM->getAttribute('first_name');
			$splcharacters = array("&","'");
			$first_name[$i] = str_replace($splcharacters, "",$firstnm[$i]);
			$lastnm[$i] = $keyDOM->getAttribute('last_name');
			$splcharacters = array("&","'");
			$last_name[$i] = str_replace($splcharacters, "",$lastnm[$i]);
			//error_log("titleeesas" .$id[$i]);
			//error_log("titleeesas" .$email[$i]);
			//error_log("titleeesas" .string($keyDOM));
			//echo $id[$i];
			//echo $email[$i];
			
			$muery = mysql_query("select * from keystable ");
		
		while($how=mysql_fetch_array($muery))
		{
		if (  strtolower($str) == strtolower($how['userid']))
		{
		$a = false;	
		//error_log("titleee" . $email[$i]);
		break;
		}
		}
			if($a == true && $email[$i] != '' )
			{
			$query = "INSERT INTO keystable(ikey, userid, firstname, lastname, fullname)
			VALUES ('lab1','$email[$i]','$first_name[$i]','$last_name[$i]','$full_name[$i]')";
			//error_log("titleee" . $query);
			mysql_query ($query) or die("MYSQL ERROR: ".mysql_error());
			}
			$i++ ;
		}

		
		$i = 0;
foreach ($domXPath->query('//user') as $keyDOM) {
			$a = true;
            $id[$i] = $keyDOM->getAttribute('id');
            $email[$i] = $keyDOM->getAttribute('email') ;
			$company[$i] =	$keyDOM->getAttribute('company') ;
			$title[$i] = $keyDOM->getAttribute('job_title');
			$fullnm[$i] = $keyDOM->getAttribute('full_name');
			$splcharacters = array("&","'");
			$full_name[$i] = str_replace($splcharacters, "",$fullnm[$i]);
			$created[$i]   = $keyDOM->getAttribute('created');
			 $city[$i] =  $keyDOM->getAttribute('city');
			//error_log("titleeesas" .$id[$i]);
			//error_log("titleeesas" .$email[$i]);
			//error_log("titleeesas" .string($keyDOM));
			//echo $id[$i];
			//echo $email[$i];
			
			$muery = mysql_query("select * from peoplenodes ");
		
		while($how=mysql_fetch_array($muery))
		{
		if (strtolower($email[$i]) == strtolower($how['id']))
		{
		$a = false;	
		break;
		}
		}
			if($a == true && $email[$i] != '' )
			{
			$query = "INSERT INTO peoplenodes(id, name, nodetype, company, title, nationality, picture, url, interests, emailid, location, jointime, invitedby, alias)
			VALUES ('$email[$i]','$full_name[$i]','People','$company[$i]','$title[$i]','','http://labs.calt.insead.edu/prototyping/InnoTubeProject/graphdata/media/people/default_people.jpg','','','$email[$i]',' $city[$i]','$created[$i]','','')";
			//error_log("titleee" . $query);
			mysql_query ($query) or die("MYSQL ERROR: ".mysql_error());
			}
			$i++ ;
		}

$url = "http://laboranova.lsi.upc.edu/live/ideas.xml";


$xmlDoc = new DOMDocument();
//$xmlDoc->load("labo_users.xml");
$xmlDoc->load($url);
$domXPath = new DOMXPath($xmlDoc);

$id = array();
$email = array();
	$i = 0;
	$a = true;
foreach ($domXPath->query('//idea') as $keyDOM) {
			$a = true;
            $id[$i] = $keyDOM->getAttribute('id');
            $abstract[$i] = $keyDOM->getAttribute('abstract') ;
			$title[$i] = $keyDOM->getAttribute('title') ;
			$description[$i] = $keyDOM->getAttribute('description') ;
			$createddate[$i] = $keyDOM->getAttribute('created') ;
			
			
			$idea_id =	 "Idea".$id[$i];
			$muery = mysql_query("select * from idea ") ;
		
		
		while($how=mysql_fetch_array($muery))
		{
		if ($idea_id == $how['id'])
		{
		$a = false;	
		break;
		}
		}
			if($a == true  )
			{
			$query = "INSERT INTO idea(id, title, abstract, description, createddate)
			VALUES ('$idea_id','$title[$i]','$abstract[$i]','$description[$i]','$createddate[$i]')";
			//error_log("titleeedea" . $query);
			mysql_query ($query) or die("MYSQL ERROR: ".mysql_error());
			}
			$i++ ;
		}
		
		
		
$purl = "http://laboranova.lsi.upc.edu/live/evaluation_outcomes.xml";


$xmlDoc1 = new DOMDocument();
//$xmlDoc->load("labo_users.xml");
$xmlDoc1->load($purl);
$domXPath1 = new DOMXPath($xmlDoc1);

$id = array();
$email = array();
	$i = 0;
	$a = true;
foreach ($domXPath1->query('//evaluation_outcome') as $keyDOM) {
			$a = true;
            $id[$i] = $keyDOM->getAttribute('id');
            $evaluation_type[$i] = $keyDOM->getAttribute('evaluation_type') ;
			$subcriterion[$i] = $keyDOM->getAttribute('subcriterion') ;
			//$description[$i] = $keyDOM->getAttribute('description') ;
			$createddate[$i] = $keyDOM->getAttribute('created') ;
			
			
			$eval_id =	 "EvalOut".$id[$i];
			$muery = mysql_query("select * from evaluation ");
		
		
		while($how=mysql_fetch_array($muery))
		{
		if ($eval_id  == $how['id'])
		{
		$a = false;	
		break;
		}
		}
			if($a == true )
			{
			
			
			$query = "INSERT INTO evaluation(id, evaluation_type, subcriterion, createddate)
			VALUES ('$eval_id','$evaluation_type[$i]','$subcriterion[$i]','$createddate[$i]')";
			//error_log("titleee" . $query);
			mysql_query ($query) or die("MYSQL ERROR: ".mysql_error());
			}
			$i++ ;
		}		
		



// This thge new code please dont touch this either try the test code somewhere else/..........please	....this the code for the HAS COMMENTED
$purl = "http://laboranova.lsi.upc.edu/live/ideas.xml";


$xmlDoc2 = new DOMDocument();
//$xmlDoc->load("labo_users.xml");
$xmlDoc1->load($purl);
$domXPath1 = new DOMXPath($xmlDoc1);

$id = array();
$email = array();
	$i = 0;
	$a = true;


	foreach ($domXPath1->query('//idea/comments/comment') as $commDOM){
		
		
			$b = true;
			$a = true;
			$uid = $commDOM->getAttribute('creator_id');
			$userid[$i] = returnMailid($uid);
			$ideaid[$i] =$commDOM->getAttribute('idea_id');
			
            $comment[$i] =$commDOM->getAttribute('comment_text');
			//$description[$i] = $keyDOM->getAttribute('description') ;
			$createddate[$i] = $commDOM->getAttribute('created') ;
			if( $createddate[$i] == '')
			$createddate[$i] = "0000-00-00 00:00:00";
			
			$idea = "Idea".$ideaid[$i];
		
		$muery = mysql_query("select * from commentstable");
		while($now=mysql_fetch_array($muery))
		{
		if(strtolower($comment[$i]) == strtolower($now['comment']))
		{
		$b = false;
		break;
		
		}
		}
		
		
		$muery = mysql_query("select * from commentstable");
		while($how=mysql_fetch_array($muery))
		{
		if (( strtolower($userid[$i]) == strtolower($how['authorid']) && $idea == $how['videoid']  && $b == false ) || ($userid[$i] == '') || ($ideaid[$i] == ''))
		{
		$a = false;	
		break;
		}
		}
		
			if($a == true )
			{
			$query = "INSERT INTO commentstable(authorid, datetime, videoid, comment)
			VALUES ('$userid[$i]','$createddate[$i]','$idea','$comment[$i]')";
			//error_log("titleee" . $query);
			mysql_query ($query) or die("MYSQL ERROR: ".mysql_error());
			}
			
		$i++ ;	
	}
	
		
// This thge new code please dont touch this either try the test code somewhere else/..........please	....this the code for the HAS RATED		
$purl = "http://laboranova.lsi.upc.edu/live/ideas.xml";
$xmlDoc2 = new DOMDocument();
//$xmlDoc->load("labo_users.xml");
$xmlDoc1->load($purl);
$domXPath1 = new DOMXPath($xmlDoc1);

$id = array();
$email = array();
	$i = 0;
	$a = true;
	$j = 0;

foreach ($domXPath1->query('//idea') as $keyDOM)
{
	$ideaid[$i] =$keyDOM->getAttribute('id');

	$comid[$i]	= $keyDOM->getElementsByTagName('ratings')->item(0);
			//if( $comid[$i] != '')
			
		
	$commid[$i] = $comid[$i]->getElementsByTagName('rating');
		if( $commid[$i] != ''){	 
			foreach ($commid[$i] as $item) {
		
			$a = true;
			$uid	=	$item->getAttribute('evaluator_id');
			$createddate = $item->getAttribute('created') ;
			$rating_value =$item->getAttribute('rating_value');
			
           
		   $userid = returnMailid($uid);
		   
		   
		   
			$idea = "Idea".$ideaid[$i];
			$muery = mysql_query("select * from useractions ");
			while($how=mysql_fetch_array($muery))
			{
			if (( strtolower($userid) == strtolower($how['TakenBy']) && $idea == $how['TakenOn'] ) || ($userid == '') || ($ideaid == ''))
			{
			$a = false;	
			break;
			}
			}
			 
		
			if($a == true )
			{
			$query = "INSERT INTO useractions(action, TakenBy, TakenOn, Entity, Datetime)
			VALUES ('Rated','$userid','$idea','$rating_value','$createddate')";
			//error_log("rating queries" . $query);
			mysql_query ($query) or die("MYSQL ERROR: ".mysql_error());
			}
			}
			}
	
	$i++ ;

}
	
// This thge new code please dont touch this either try the test code somewhere else/..........please	....this the code for the HAS BEEN ASSESED

$purl = "http://laboranova.lsi.upc.edu/live/ideas.xml";
$xmlDoc2 = new DOMDocument();
//$xmlDoc->load("labo_users.xml");
$xmlDoc1->load($purl);
$domXPath1 = new DOMXPath($xmlDoc1);

$id = array();
$email = array();
	$i = 0;
	$a = true;
	$j = 0;

foreach ($domXPath1->query('//idea') as $keyDOM)
{
	$ideaid[$i] =$keyDOM->getAttribute('id');

	$comid[$i]	= $keyDOM->getElementsByTagName('evaluation_outcomes')->item(0);
			//if( $comid[$i] != '')
			
		
	$commid[$i] = $comid[$i]->getElementsByTagName('evaluation_outcome');
		if( $commid[$i] != ''){	 
			foreach ($commid[$i] as $item) {
		
			$a = true;
			$evalid	=	$item->getAttribute('id');
			$createddate = $item->getAttribute('created') ;
			//$rating_value =$item->getAttribute('rating_value');
			
            
			$evalid = "EvalOut". $evalid;
			 $idea = "Idea".$ideaid[$i];
			$muery = mysql_query("select * from edges ");
			while($how=mysql_fetch_array($muery))
			{
			if (( $idea == $how['fromID'] && $evalid == $how['toID'] && $how['name'] == 'Has been assessed in' ) || ($evalid == 'EvalOut') || ($ideaid[$i] == ''))
			{
			$a = false;	
			break;
			}
			}
			
		
			if($a == true )
			{
			$query = "INSERT INTO edges(name, tooltip, fromID, toID, intensity, edgecolor, creationtime)
			VALUES ('Has been assessed in', 'has been assessed in','$idea','$evalid','1','0x8B4513','$createddate')";
			//error_log("edgequery" . $query);
			mysql_query ($query) or die("MYSQL ERROR: ".mysql_error());
			}
			}
			}
	
	$i++ ;

}

// This thge new code please dont touch this either try the test code somewhere else/..........please	....this the code for the HAS BEEN INVOLVED
$purl = "http://laboranova.lsi.upc.edu/live/ideas.xml";
$xmlDoc2 = new DOMDocument();
//$xmlDoc->load("labo_users.xml");
$xmlDoc1->load($purl);
$domXPath1 = new DOMXPath($xmlDoc1);

$id = array();
$email = array();
	$i = 0;
	$a = true;
	$j = 0;

foreach ($domXPath1->query('//idea') as $keyDOM)
{
	$ideaid[$i] =$keyDOM->getAttribute('id');

	$comid[$i]	= $keyDOM->getElementsByTagName('evaluation_outcomes')->item(0);
			//if( $comid[$i] != '')
			
		
	$coddid[$i] = $comid[$i]->getElementsByTagName('evaluation_outcome');
		if( $coddid[$i] != ''){	 
			foreach ($coddid[$i] as $anitem) {
				$participating_users	 =	$anitem->getElementsByTagName('participating_users')->item(0);
				$evalid	=	$anitem->getAttribute('id');
				$createddate = $anitem->getAttribute('created') ;
				if($participating_users)
				{
					$cohhid[$i] = $participating_users->getElementsByTagName('participating_user');
					
					foreach ($cohhid[$i] as $item) {
					$a = true;
					
					$uid	=	$item->getAttribute('id');
					 $userid = returnMailid($uid);
					
					$eval = "EvalOut". $evalid;
					
					$muery = mysql_query("select * from edges ");
					while($how=mysql_fetch_array($muery))
					{
					if (( strtolower($userid) == strtolower($how['fromID']) && $eval == $how['toID'] && $how['name'] == 'Has been involved in' ) || ($evalid == '') || ($userid == ''))
					{
					$a = false;	
					break;
					}
					}
			
		
					if($a == true )
					{
					$query = "INSERT INTO edges(name, tooltip, fromID, toID, intensity, edgecolor, creationtime)
					VALUES ('Has been involved in', 'has been involved in','$userid','$eval','1','0xff0000','$createddate')";
					error_log("edgeinvoled alike thisquery" . $query);
					mysql_query ($query) or die("MYSQL ERROR: ".mysql_error());
					}
					}
					}
			}}
	$i++ ;

}


// This thge new code please dont touch this either try the test code somewhere else/..........please	....this the code for the HAS GIVEN
$purl = "http://laboranova.lsi.upc.edu/live/ideas.xml";
$xmlDoc2 = new DOMDocument();
//$xmlDoc->load("labo_users.xml");
$xmlDoc1->load($purl);
$domXPath1 = new DOMXPath($xmlDoc1);

$id = array();
$email = array();
	$i = 0;
	$a = true;
	$j = 0;

foreach ($domXPath1->query('//idea') as $keyDOM)
{
	$ideaid[$i] =$keyDOM->getAttribute('id');
	$createddate = $keyDOM->getAttribute('created') ;

	$comid[$i]	= $keyDOM->getElementsByTagName('creators')->item(0);
			//if( $comid[$i] != '')
			
		
	$commid[$i] = $comid[$i]->getElementsByTagName('creator');
		if( $commid[$i] != ''){	 
			foreach ($commid[$i] as $item) {
		
			$a = true;
			$uid	=	$item->getAttribute('id');
			
			
			$creatorid = returnMailid($uid);;
			$idea = "Idea".$ideaid[$i];
			
			$muery = mysql_query("select * from edges ");
			while($how=mysql_fetch_array($muery))
			{
			if (( strtolower($creatorid) == strtolower($how['fromID']) && $idea == $how['toID'] && $how['name'] == 'Has given' ) || ($creatorid== '') || ($ideaid[$i] == ''))
			{
			$a = false;	
			break;
			}
			}
			
		
			if($a == true )
			{
			$query = "INSERT INTO edges(name, tooltip, fromID, toID, intensity, edgecolor, creationtime)
			VALUES ('Has given', 'has given','$creatorid','$idea','1','0x550055','$createddate')";
			error_log("This the given queryquery" . $query);
			//mysql_query ($query) or die("MYSQL ERROR: ".mysql_error());
			}
			}
			}
	
	$i++ ;

}


	
?>
<?php

function returnMailid($userid) {
$muery = mysql_query("select * from mapping ");
			while($how=mysql_fetch_array($muery))
			{
			if (strtolower($userid) == strtolower($how['guid']))
			{ 
			return $how['userid'];
			break;
			}
			}
}	
?>		