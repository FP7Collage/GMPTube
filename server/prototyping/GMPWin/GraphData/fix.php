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
include "conntube.php";
//gettagarrayid('importispecification');
$query=mysql_query("select * from videonodes ");
$i=0;
while($row=mysql_fetch_array($query))
{
	$tags=explode(",",$row['tags']);
	$id = $row['id'];
	$temptagsid = array();
	for($i=0; $i<count($tags);$i++)
	{ 
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
	$temptagsid[$i] =  $tags[$i];
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
	$nuery = "UPDATE videonodes SET tags ='$currtags' where id = '$id'";
	mysql_query ($nuery) or die(mysql_error());  
	error_log("pingooo" .$nuery);
}



$query=mysql_query("select * from peoplenodes ");
$i=0;
while($row=mysql_fetch_array($query))
{
	$tags=explode(",",$row['interests']);
	$id = $row['id'];
	$temptagsid = array();
	for($i=0; $i<count($tags);$i++)
	{ 
	$muery=mysql_query("select * from tagnodes where name='$tags[$i]'");
			if(mysql_num_rows($muery) > 0)
	{
	while($now=mysql_fetch_array($muery))
	{
	$temptagsid[$i] = $now['id'];
	//error_log("pingooo" .$temptagsid[$i]);
	}
	}
		else
	$temptagsid[$i] = $tags[$i];
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
	$nuery = "UPDATE peoplenodes SET interests ='$currtags' where id = '$id'";
	error_log("the people query" .$nuery);	
	mysql_query ($nuery) or die(mysql_error());  
	
}
?>
<?php

function gettagid($tagname)
{	$temptagid ="";

	$muery=mysql_query("select * from tagnodes where name='$tagname'");
	
	if(mysql_num_rows($muery) > 0)
	{
	while($now=mysql_fetch_array($muery))
	{
	$temptagid = $now['id'];
	
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
	$squery="INSERT INTO tagnodes(name, nodetype, picture, id) VALUES ('$tagname','Tags','media/tag_img.jpg','$taginsert')";
	//mysql_query ($squery) or die(mysql_error());  
	
	$temptagid = $taginsert;
	
	}
	return $temptagid;
}	


function gettagarrayid($tagname)
{
$tags=explode(",",$tagname);
	$id = $row['id'];
	$temptagsid = array();
	for($i=0; $i<count($tags);$i++)
	{ 
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
	$squery="INSERT INTO tagnodes(name, nodetype, picture, id) VALUES ('$tags[$i]','Tags','media/tag_img.jpg','$taginsert')";
	//mysql_query ($squery) or die(mysql_error());  
	error_log("the other query" .$squery);	
	$temptagsid[$i] = $taginsert;
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
?>