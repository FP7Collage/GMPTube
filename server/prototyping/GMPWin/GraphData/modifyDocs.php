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
	
	$externalLinks = $_POST['externalLinks'];
	$docLinks = $_POST['docLinks'];
	$videoId = $_POST['videoId'];
//	$docLinks1 = str_replace('|', '|'.$videoId.'_',$docLinks);
	$docLinksArray = explode('|',$docLinks);
	$i=0;
	foreach($docLinksArray as $file){
		if(strncmp($file,$videoId.'_',sizeof($videoId)+1)!=0){
			if(sizeof($file)!=0){
				rename('./upload/'.$file,'./upload/'.$videoId.'_'.$file);
				$docLinksArray[$i] = $videoId.'_'.$file;
			}
		}
		$i++;
	}
	if($docLinks=="")
	{
		$docLinks1="";
	}
	else
	{
	    $docLinks1 = implode('|',$docLinksArray);
	}
	//logging the addition and deletion of links in the useractions table
	
	$data=mysql_query("select * from videonodes where id = '$videoId'");
					$rowlink = mysql_fetch_array($data);
					if($rowlink['externalLinks']!='')
					{
					//error_log("row".$rowlink['externalLinks']);
					$links=explode("\r",$rowlink['externalLinks']);
					$newlinks=explode("\r",$externalLinks);
					//link removed
					//error_log("link[0]".$links[0]);
					//rror_log("link[1]".$links[1]);
					foreach($links as $link)
					{
						//error_log("link=".$link);
						$i=0;
						foreach($newlinks as $newlink)
						{
							//error_log("newlink=".$newlink);
							if($link==$newlink)
							{
							$i=1;
							}
						}
						if($i==0)
						mysql_query ("INSERT INTO useractions(action,TakenBy,TakenOn,Datetime) VALUES ('LinkRemoved','$link','$videoId',current_timestamp())") ;

					}
					
					//link added
					foreach($newlinks as $newlink)
					{
						
						$i=0;
						foreach($links as $link)
						{
							if($link==$newlink)
							{
							$i=1;
							}
						}
						if($i==0)
						mysql_query ("INSERT INTO useractions(action,TakenBy,TakenOn,Datetime) VALUES ('LinkNew','$newlink','$videoId',current_timestamp())") ;

					}
					}
					else
					{
					$links=explode("\r",$externalLinks);
					//new links
					foreach($links as $link)
					{
						mysql_query ("INSERT INTO useractions(action,TakenBy,TakenOn,Datetime) VALUES ('LinkNew','$link','$videoId',current_timestamp())") ;

					}
					}
					
	//logging the addition and deletion of doc links in the useractions table
	
	$data=mysql_query("select * from videonodes where id = '$videoId'");
					$rowlink = mysql_fetch_array($data);
					if($rowlink['docLinks']!='')
					{
					$links=explode('|',$rowlink['docLinks']);
					$newlinks=explode('|',$docLinks1);
					//link removed
					foreach($links as $link)
					{
						
						$i=0;
						foreach($newlinks as $newlink)
						{
							if($link==$newlink)
							{
							$i=1;
							}
						}
						if($i==0)
						mysql_query ("INSERT INTO useractions(action,TakenBy,TakenOn,Datetime) VALUES ('DocLinkRemoved','$link','$videoId',current_timestamp())") ;

					}
					
					//link added
					foreach($newlinks as $newlink)
					{
						
						$i=0;
						foreach($links as $link)
						{
							if($link==$newlink)
							{
							$i=1;
							}
						}
						if($i==0)
						mysql_query ("INSERT INTO useractions(action,TakenBy,TakenOn,Datetime) VALUES ('DocLinkNew','$newlink','$videoId',current_timestamp())") ;

					}
					}
					else
					{
					$links=explode('|',$docLinks1);
					//new links
					foreach($links as $link)
					{
						mysql_query ("INSERT INTO useractions(action,TakenBy,TakenOn,Datetime) VALUES ('DocLinkNew','$link','$videoId',current_timestamp())") ;

					}
					}
	$query = "UPDATE videonodes set externalLinks = '$externalLinks',docLinks = '$docLinks1' where id = '$videoId'";
	mysql_query($query);
	
	echo '<?xml version=\"1.0\" encoding=\"utf-8\"?>';
	echo "<rsp>";
	echo "<result>Success</result>";
	echo "<docLinks1>" . $docLinks1 . "</docLinks1>";
	echo "<externalLinks>" . $externalLinks . "</externalLinks>";
	/*
	echo "<executedQuery>" . $query . "</executedQuery>";
	echo "<docLinks>" . $docLinks . "</docLinks>";
	echo "<docLinksArray>" . print_r($docLinksArray) . "</docLinksArray>";
	*/
	echo "</rsp>";
?>