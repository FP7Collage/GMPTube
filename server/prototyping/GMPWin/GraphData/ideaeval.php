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
			
			
			
			$muery = mysql_query("select * from idea ");
		
		
		while($how=mysql_fetch_array($muery))
		{
		if ($id[$i] == $how['id'])
		{
		$a = false;	
		break;
		}
		}
			if($a == true  )
			{
			$query = "INSERT INTO idea(id, title, abstract, description, createddate)
			VALUES ('$id[$i]','$title[$i]','$abstract[$i]','$description[$i]','$createddate[$i]')";
			error_log("titleee" . $query);
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
			
			//error_log("titleeesas" .$id[$i]);
			//error_log("titleeesas" .$email[$i]);
			//error_log("titleeesas" .string($keyDOM));
			//echo $id[$i];
			//echo $email[$i];
			
			$muery = mysql_query("select * from evaluation ");
		
		
		while($how=mysql_fetch_array($muery))
		{
		if ($id[$i] == $how['id'])
		{
		$a = false;	
		break;
		}
		}
			if($a == true )
			{
			
			
			$query = "INSERT INTO evaluation(id, evaluation_type, subcriterion, createddate)
			VALUES ('$id[$i]','$evaluation_type[$i]','$subcriterion[$i]','$createddate[$i]')";
			error_log("titleee" . $query);
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
            $userid[$i] = $commDOM->getAttribute('creator_id');
		
			$ideaid[$i] =$commDOM->getAttribute('idea_id');
			
            $comment[$i] =$commDOM->getAttribute('comment_text');
			//$description[$i] = $keyDOM->getAttribute('description') ;
			$createddate[$i] = $commDOM->getAttribute('created') ;
			if( $createddate[$i] == '')
			$createddate[$i] = "0000-00-00 00:00:00";
			
			
		
		for($j = 1; $j<=$i;$j++)
		{
		if($comment[$j] == $comment[$i])
		{
		$b = false;
		break;
		}
		}
		
		
		$muery = mysql_query("select * from commentstable");
		while($how=mysql_fetch_array($muery))
		{
		if (( $userid[$i] == $how['authorid'] && $ideaid[$i] = $how['videoid'] && $b == false ) || ($userid[$i] == '') || ($ideaid[$i] == ''))
		{
		$a = false;	
		break;
		}
		}
		
			if($a == true )
			{
			$query = "INSERT INTO commentstable(authorid, datetime, videoid, comment)
			VALUES ('$userid[$i]','$createddate[$i]',' $ideaid[$i]','$comment[$i]')";
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
			$userid	=	$item->getAttribute('evaluator_id');
			$createddate = $item->getAttribute('created') ;
			$rating_value =$item->getAttribute('rating_value');
			
           
		   
		   $userid = "User". $userid;
			 $idea = "Idea".$ideaid[$i];
			$muery = mysql_query("select * from useractions ");
			while($how=mysql_fetch_array($muery))
			{
			if (( $userid == $how['TakenBy'] && $idea == $how['TakenOn'] ) || ($userid == 'User') || ($ideaid == ''))
			{
			$a = false;	
			break;
			}
			}
			 
		
			if($a == true )
			{
			$query = "INSERT INTO useractions(action, TakenBy, TakenOn, Entity, Datetime)
			VALUES ('Has rated','$userid','$idea','$rating_value','$createddate')";
			//error_log("titleee" . $query);
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
					
					$userid	=	$item->getAttribute('id');
					
					$eval = "EvalOut". $evalid;
					$user = "User".$userid;
					$muery = mysql_query("select * from edges ");
					while($how=mysql_fetch_array($muery))
					{
					if (( $user == $how['fromID'] && $eval == $how['toID'] && $how['name'] == 'Has been involved in' ) || ($evalid == '') || ($userid == ''))
					{
					$a = false;	
					break;
					}
					}
			
		
					if($a == true )
					{
					$query = "INSERT INTO edges(name, tooltip, fromID, toID, intensity, edgecolor, creationtime)
					VALUES ('Has been involved in', 'has been involved in','$user','$eval','1','0xff0000','$createddate')";
					//error_log("edgeinvoled alike thisquery" . $query);
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
			$creatorid	=	$item->getAttribute('id');
			
			$creatorid = "User". $creatorid;
			$idea = "Idea".$ideaid[$i];
			
			$muery = mysql_query("select * from edges ");
			while($how=mysql_fetch_array($muery))
			{
			if (( $creatorid == $how['fromID'] && $idea == $how['toID'] && $how['name'] == 'Has given' ) || ($creatorid== 'User') || ($ideaid[$i] == ''))
			{
			$a = false;	
			break;
			}
			}
			
		
			if($a == true )
			{
			$query = "INSERT INTO edges(name, tooltip, fromID, toID, intensity, edgecolor, creationtime)
			VALUES ('Has given', 'has given','$creatorid','$idea','1','0x550055','$createddate')";
			//error_log("This the given queryquery" . $query);
			mysql_query ($query) or die("MYSQL ERROR: ".mysql_error());
			}
			}
			}
	
	$i++ ;

}










	
	
		
?>