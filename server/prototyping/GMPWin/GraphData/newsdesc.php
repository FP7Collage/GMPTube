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
	session_start();

	header("Cache-control: private");
	//header("Content-type: text/html;charset=UTF-8");
	header("Content-type: text/xml");
	
	
	
	$newsidlist = array();
	$newsdesc = array();
	$newstitle = array();
	$splcharacters = array("&","'");
	$i = 0 ;
	
	$query = mysql_query("select * from news ");
	
	while($how=mysql_fetch_array($query))
	{
	$newsidlist[$i] = $how['id'];
	
	$newsdesc[$i] = $how['Description'];
	$newstitle[$i] = $how['Title'];
	
	if($how['isactive']== '1')
	{$newsisactive[$i] = 'true';}
	else 
	{$newsisactive[$i] = 'false';}
	
	$i=$i+1;
	}
	
	echo "<partnerarticles>";
	for($j=0; $j<$i;$j++)
	{
	$retstring = $newsidlist[$j];
	$splcharacters = array("&","'");
	$retstring_without_splcharacters = str_replace($splcharacters, "",$retstring);
	
	$petstring = $newsdesc[$j];
	$splcharacters = array("&","'");
	$petstring_without_splcharacters = str_replace($splcharacters, "",$petstring);
	
	$detstring = $newstitle[$j];
	$splcharacters = array("&","'");
	$detstring_without_splcharacters = str_replace($splcharacters, "",$detstring);
	
	$netstring = $newsisactive[$j];
	$splcharacters = array("&","'");
	$netstring_without_splcharacters = str_replace($splcharacters, "",$netstring);
	//error_log("titleeesas" .$netstring_without_splcharacters);

	echo "<item>"."<id>".$retstring_without_splcharacters."</id>"."<title>".$detstring_without_splcharacters."</title>"."<desc>".$petstring_without_splcharacters."</desc>"."<isactive>".$netstring_without_splcharacters."</isactive>"."</item>";
	}
	echo "</partnerarticles>";
	