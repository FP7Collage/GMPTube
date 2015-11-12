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


set_time_limit(3600);
		include "conntube.php";
		session_start();

		header("Cache-control: private");
		header("Content-type: text/html;charset=UTF-8");
	//header("Content-type: text/xml");
			//connectdb(tentubetest);
		 $vuery=mysql_query("select * from videonodes");
		 
		 $retstring ='';
		 
		 while($vow=mysql_fetch_array($vuery))
		
		 { 		
		   $retstring = $retstring.$vow['name'];
		   $retstring =$retstring.' ';
		   // $retstring = $retstring.$vow['comments']; 
			// $retstring =$retstring.' ';		   
		   $retstring = $retstring.$vow['tags'];
		   $retstring =$retstring.' ';
		   $retstring = $retstring.$vow['category'];
		   $retstring =$retstring.' ';
		 //  $retstring = $retstring.$vow['picture'];
		   //$retstring =$retstring.' ';
		 ////  $retstring = $retstring.$vow['url'];
		   //$retstring =$retstring.' ';
		   //$retstring = $retstring.$vow['id'];
		 //  $retstring =$retstring.' ';
		   //$retstring = $retstring.$vow['source'];
		 //  $retstring =$retstring.' ';
		   $retstring = $retstring.$vow['externalLinks'];
		   $retstring =$retstring.' ';
		   $retstring = $retstring.$vow['docLinks'];
		   $retstring =$retstring.' ';
		   $retstring = $retstring.$vow['description'];
		   $retstring =$retstring.' ';
		   
		   
		  }
		   $vuery=mysql_query("select * from topics ");
		 
		 
		 while($vow=mysql_fetch_array($vuery))
		
		 { 		
		   
		   $retstring = $retstring.$vow['name'];  
		   $retstring =$retstring.' ';
		   $retstring = $retstring.$vow['username'];
		   $retstring =$retstring.' ';
		   
		  }
		   
		   $vuery=mysql_query("select * from topicmessages ");
		  
		 while($vow=mysql_fetch_array($vuery))
		
		 { 		
		   $retstring = $retstring.$vow['message'];
		   $retstring =$retstring.' ';
		   $retstring = $retstring.$vow['username'];
			$retstring =$retstring.' ';		   
		   //$retstring = $retstring.$vow['videoid'];
		   //$retstring =$retstring.' ';
		   
		  }
		 
		 $vuery=mysql_query("select * from tagnodes ");
		  
		 while($vow=mysql_fetch_array($vuery))
		
		 { 		
		   $retstring = $retstring.$vow['name'];
		   $retstring =$retstring.' ';
		   //$retstring = $retstring.$vow['picture'];  
		   //$retstring =$retstring.' ';
		   
		   
		  } 
		
		   
		    $vuery=mysql_query("select * from scrapstable ");
		  
		 while($vow=mysql_fetch_array($vuery))
		
		 { 		
		   $retstring = $retstring.$vow['scrap'];
		   $retstring =$retstring.' ';
		   
		   
		  } 
		  $vuery=mysql_query("select * from peoplenodes ");
		  while($vow=mysql_fetch_array($vuery))
		
		 { 		
		   $retstring = $retstring.$vow['name'];
		   $retstring =$retstring.' ';
		   $retstring = $retstring.$vow['company'];
			$retstring =$retstring.' ';		   
		   $retstring = $retstring.$vow['title'];
		   $retstring =$retstring.' ';
		   $retstring = $retstring.$vow['nationality'];
		   $retstring =$retstring.' ';
		   // $retstring = $retstring.$vow['picture'];
		   // $retstring =$retstring.' ';
		   // $retstring = $retstring.$vow['url'];
		   // $retstring =$retstring.' ';
		   // $retstring = $retstring.$vow['id'];
		   // $retstring =$retstring.' ';
		   $retstring = $retstring.$vow['interests'];
		   $retstring =$retstring.' ';
		   $retstring = $retstring.$vow['location'];
		   $retstring =$retstring.' ';
		   $retstring = $retstring.$vow['competences'];
		   $retstring =$retstring.' ';
		   $retstring = $retstring.$vow['alias'];
		   $retstring =$retstring.' ';
		   
		   
		  }
		 
		  $vuery=mysql_query("select * from news ");
		  
		 while($vow=mysql_fetch_array($vuery))
		
		 { 		
		   $retstring = $retstring.$vow['Title'];
		   $retstring =$retstring.' ';
		   $retstring = $retstring.$vow['Description'];  
		   $retstring =$retstring.' ';
		   
		   
		  } 
		  $vuery=mysql_query("select * from grpmaindata ");
		  while($vow=mysql_fetch_array($vuery))
		
		 { 		
		   //$retstring = $retstring.$vow['gid'];
		   //$retstring =$retstring.' ';
		   $retstring = $retstring.$vow['name'];
			$retstring =$retstring.' ';		   
		   //$retstring = $retstring.$vow['picturepath'];
		   //$retstring =$retstring.' ';
		   //$retstring = $retstring.$vow['url'];
		   //$retstring =$retstring.' ';
		   $retstring = $retstring.$vow['description'];
		   $retstring =$retstring.' ';
		   
		   
		   
		  }
		   
		  // $vuery=mysql_query("select * from g4_taboowords ");
		  // while($vow=mysql_fetch_array($vuery))
		
		 // { 		
		  
		   // $retstring = $retstring.$vow['word']; 
			// $retstring =$retstring.' ';		   
		 // }
		 
		 // $vuery=mysql_query("select * from g2_gameadmin ");
		  // while($vow=mysql_fetch_array($vuery))
		
		 // { 		
		   
		   // $retstring = $retstring.$vow['wordsmatched']; 
			// $retstring =$retstring.' ';		   
		   // $retstring = $retstring.$vow['taboowords'];
		   // $retstring =$retstring.' ';
		  
		   
		   
		   
		  // } 
		  
		 
		  
		  // $vuery=mysql_query("select * from g3_gamelog ");
		  // while($vow=mysql_fetch_array($vuery))
		
		 // { 		
		   // $retstring = $retstring.$vow['gameid'];
		   // $retstring =$retstring.' ';
		   
		   // $retstring = $retstring.$vow['player1words_time'];
		   // $retstring =$retstring.' ';
		   // $retstring = $retstring.$vow['player2words_time'];
		   // $retstring =$retstring.' ';
		   // $retstring = $retstring.$vow['wordsmatched'];
		   // $retstring =$retstring.' ';
		   // $retstring = $retstring.$vow['videoid'];
		   // $retstring =$retstring.' ';
		   
		   
		   
		  // } 
		  
		  
		  
		  // $vuery=mysql_query("select * from g1_players ");
		  // while($vow=mysql_fetch_array($vuery))
		
		 // { 		
		   // $retstring = $retstring.$vow['status'];
		   // $retstring =$retstring.' ';
		   
		   
		   
		  // } 
		
		  
		 // $vuery=mysql_query("select * from edgetypes ");
		  // while($vow=mysql_fetch_array($vuery))
		
		 // { 		
		   // $retstring = $retstring.$vow['name'];
		   // $retstring =$retstring.' ';
		   // $retstring = $retstring.$vow['edgecolor'];
			// $retstring =$retstring.' ';		   
		   // $retstring = $retstring.$vow['tooltip'];
		   // $retstring =$retstring.' ';

		  // }  
		  
		 // $vuery=mysql_query("select * from edges ");
		 
		  // while($vow=mysql_fetch_array($vuery))
		
		 // { 		
		   // $retstring = $retstring.$vow['name'];
		   // $retstring =$retstring.' ';
		   // $retstring = $retstring.$vow['tooltip'];  
		   // $retstring =$retstring.' ';
		   
		   
		   
		  // } 
		  // $vuery=mysql_query("select * from dictionary ");
		 
		  // while($vow=mysql_fetch_array($vuery))
		
		 // { 		
		   // $retstring = $retstring.$vow['word'];
		   // $retstring =$retstring.' ';
		  
		   
		   
		   
		  // } 
		    $vuery=mysql_query("select * from control ");
		 
		  while($vow=mysql_fetch_array($vuery))
		
		 { 		
		   $retstring = $retstring.$vow['interests'];
		   $retstring =$retstring.' ';
		  
		   
		   
		   
		  } 
		   
		  
		    $vuery=mysql_query("select * from conchatmessages ");
		 
		  while($vow=mysql_fetch_array($vuery))
		
		 { 		
		   $retstring = $retstring.$vow['message'];
		   $retstring =$retstring.' ';
		  
		   
		   
		   
		  } 
		  $vuery=mysql_query("select * from competencetable ");
		 
		  while($vow=mysql_fetch_array($vuery))
		
		 { 		
		   $retstring = $retstring.$vow['name'];
		   $retstring =$retstring.' ';
		  
		   
		   
		   
		  } 
		  $vuery=mysql_query("select * from commentstable ");
		
		  while($vow=mysql_fetch_array($vuery))
		
		 { 		
		   $retstring = $retstring.$vow['comment'];
		   $retstring =$retstring.' ';
		   
		  } 
		   $vuery=mysql_query("select * from chatmessages ");
		
		  while($vow=mysql_fetch_array($vuery))
		
		 { 		
		   $retstring = $retstring.$vow['message'];
		   $retstring =$retstring.' ';
		   
		  } 
		 
		    $vuery=mysql_query("select * from  affiliationtable ");
		
		  while($vow=mysql_fetch_array($vuery))
		
		 { 		
		   $retstring = $retstring.$vow['name'];
		   $retstring =$retstring.' ';
		   
		  } 
		echo $retstring;   
		   
	   
		   
		   
		   
		   
 ?>