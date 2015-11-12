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
session_start();
header("Cache-control: private");


header("Content-type: text/xml");
	
	
	include "conntube.php";
    
	do
   {
      $randomNum=rand(1,10000000);
      $fname="xpertumData/".$randomNum.".xml";
   }while(file_exists($fname)==true);
	$value=$_POST['value'];
   
   // for testing
 //  $value="ALL";
   	
	$fid=fopen($fname,"w");
	$idsArray=array();
	$copy_idsArray=array();
	
	 $retstring="<?xml version=\"1.0\" encoding=\"utf-8\"?>"; 
     $retstring =  $retstring . "<graphml>";
	 $retstring =  $retstring . "<key id=\"ed-strength\" for=\"edge\" attr.name=\"strength\" attr.type=\"double\" />";
	 $retstring =  $retstring . "<key id=\"nd-firstname\" for=\"node\" attr.name=\"firstname\" attr.type=\"string\" />";
	 $retstring =  $retstring . "<key id=\"nd-lastname\" for=\"node\" attr.name=
	 \"lastname\" attr.type=\"string\" />";
	 $retstring =  $retstring . "<key id=\"nd-alias\" for=\"node\" attr.name=\"alias\" attr.type=\"string\" />";
	 $retstring =  $retstring . "<key id=\"nd-email\" for=\"node\" attr.name=\"email\" attr.type=\"string\" />";
	 $retstring =  $retstring . "<key id=\"nd-jobrole\" for=\"node\" attr.name=\"jobrole\" attr.type=\"string\" />";
	 $retstring =  $retstring . "<key id=\"nd-organization\" for=\"node\" attr.name=\"organization\" attr.type=\"string\" />";
	 $retstring =  $retstring . "<key id=\"nd-phone\" for=\"node\" attr.name=\"phone\" attr.type=\"string\" />";
	 $retstring =  $retstring . "<key id=\"nd-region\" for=\"node\" attr.name=\"region\" attr.type=\"string\" />"; 
	$retstring =  $retstring . "<key id=\"nd-postalcode\" for=\"node\" attr.name=\"postalcode\" attr.type=\"string\" />"; 
	$retstring =  $retstring . "<key id=\"nd-country\" for=\"node\" attr.name=\"country\" attr.type=\"string\" />"; 
	$retstring =  $retstring . "<key id=\"nd-timezone\" for=\"node\" attr.name=\"timezone\" attr.type=\"string\" />"; 
	$retstring =  $retstring . "<key id=\"nd-pictname\" for=\"node\" attr.name=\"pictname\" attr.type=\"string\" />"; 
	$retstring =  $retstring . "<key id=\"nd-pos_x\" for=\"node\" attr.name=\"pos_x\" attr.type=\"double\" />"; 
	$retstring =  $retstring . "<key id=\"nd-pos_y\" for=\"node\" attr.name=\"pos_y\" attr.type=\"double\" />"; 
	$retstring =  $retstring . "<key id=\"nd-pos_z\" for=\"node\" attr.name=\"pos_z\" attr.type=\"double\" />"; 
	$retstring =  $retstring . "<key id=\"nd-type\" for=\"node\" attr.name=\"type\" attr.type=\"string\" />"; 
	$retstring =  $retstring . "<key id=\"nd-area\" for=\"node\" attr.name=\"area\" attr.type=\"string\" />"; 
	$retstring =  $retstring . "<key id=\"nd-id_area\" for=\"node\" attr.name=\"area\" attr.type=\"int\" />"; 
	$retstring =  $retstring . "<key id=\"nd-name\" for=\"node\" attr.name=\"name\" attr.type=\"string\" />"; 
	
	$retstring =  $retstring . "<graph id=\"G\" edgedefault=\"undirected\">";
		
    	
	//to put the affiliationtypes and corresponding affiliations
	 $affiliation_rs=mysql_query("select * from affiliationtable");
	 while($affiliation_row=mysql_fetch_array($affiliation_rs))
	 {
	       $affiliationtype_name=$affiliation_row['name'];
		   $affiliationtype_id=$affiliation_row['id'];
		   $competencesList=$affiliation_row['competences'];
		   
		   
		   
		   	      
		   $competenceToken = strtok($competencesList, ",");
		   while ($competenceToken !== false)
           {
		        $result=mysql_query("select name from competencetable where id='$competenceToken'");
                while ( $competenceRow = mysql_fetch_array($result) )
				{
	            $competenceName		= 	$competenceRow['name'];
				
				$retstring=$retstring."<node id=\"$competenceToken\">";
				$retstring=$retstring."<data key=\"nd-pos_x\">0</data>";
				$retstring=$retstring."<data key=\"nd-pos_y\">0</data>";
				$retstring=$retstring."<data key=\"nd-pos_z\">0</data>";
				$retstring=$retstring."<data key=\"nd-type\">affiliation</data>"; 
                $retstring=$retstring."<data key=\"nd-area\">$affiliationtype_name</data>";
			    $retstring=$retstring."<data key=\"nd-id_area\">$affiliationtype_id</data>";
                $retstring=$retstring."<data key=\"nd-name\">$competenceName</data>";
				$retstring=$retstring."</node>";
				
                array_push($idsArray,$competenceToken);						  
	            }
			
                $competenceToken = strtok(",");
           }

		   	   
	 }
	
	$rs = '';
	$echostr="";
    if($value=="ALL")
    	$rs				=	mysql_query ("Select * from peoplenodes order by name");
    else
	{
	//  $value ="krishna.mahesh@insead.edu,Rakesh.lalwani@insead.edu,albert.angehrn@insead.edu,marco.luccini@insead.edu,katrina.maxwell@insead.edu";
	    $queryStr="";
	    $queryStrToken = strtok($value, ",");
		   while ($queryStrToken !== false)
           {
		    if($queryStr=="")
		      $queryStr="id='".$queryStrToken."'";
            else
 			  $queryStr=$queryStr."or id='".$queryStrToken."'";
			
			 $queryStrToken = strtok(",");
		   }	
//		echo $queryStr;
	  // $value ="id='krishna.mahesh@insead.edu' or id='Rakesh.lalwani@insead.edu' or id='albert.angehrn@insead.edu'";
      $query ="Select * from peoplenodes where $queryStr order by name";    	  
	  $rs				=	mysql_query ($query);
	 //  echo $query;
	}
	$id=0;
   while ($row = mysql_fetch_array($rs))
   {
		//$retstring=$retstring."<person\n";
		//fputs($fid,"<person\n");
		$name=$row['name'];
		$space_pos=stripos($name," ");
		if($space_pos==false)
		{
           $firstName=$name;
           $lastName=$name;
        }
        else
        {		
		 $firstName=substr($name,0,$space_pos);
		 $lastName=substr($name,$space_pos+1);
		}
		//	firstname="Krishna"
		//lastname="Mahesh"
		$alias=$firstName;
		$email=$row['id'];
		$jobrole=$row['title'];
		$organization=$row['company'];
		$region=$row['location'];
		$country=$row['nationality'];
		$timezone="GTM (+01)";
		$pictname=$row['picture'];
        $comp_strengths_list=$row['competences']; 	
		$id=$id+1;
	//	echo $id;
	//$retstring=$retstring."id=\"$id\"\nfirstname=\"$firstName\"\nlastname=\"$lastName\"\nalias=\"$alias\"\nemail=\"$email\"\njobrole=\"$jobrole\"\norganization=\"$organization\"\nphone=\"-\" \nregion=\"$region\"\npostalcode=\"77305\"\ncountry=\"$country\"\ntimezone=\"$timezone\"\npictname=\"$pictname\">\n";
//fputs($fid,"id=\"$id\"\nfirstname=\"$firstName\"\nlastname=\"$lastName\"\nalias=\"$alias\"\nemail=\"$email\"\njobrole=\"$jobrole\"\norganization=\"$organization\"\nphone=\"-\" \nregion=\"$region\"\npostalcode=\"77305\"\ncountry=\"$country\"\ntimezone=\"$timezone\"\npictname=\"$pictname\">\n");
  // to copy idsarray to copy_idsarray	
  
  $retstring=$retstring."<node id=\"$id\">";
  $retstring=$retstring."<data key=\"nd-firstname\">$firstName</data>"; 
  $retstring=$retstring."<data key=\"nd-lastname\">$lastName</data>"; 
  $retstring=$retstring."<data key=\"nd-alias\">$alias</data>"; 
  $retstring=$retstring."<data key=\"nd-email\">$email</data>"; 
  $retstring=$retstring."<data key=\"nd-jobrole\">$jobrole</data>"; 
  $retstring=$retstring."<data key=\"nd-organization\">$organization</data>"; 
  $retstring=$retstring."<data key=\"nd-phone\">0000</data>"; 
  $retstring=$retstring."<data key=\"nd-region\">$region</data>"; 
  $retstring=$retstring."<data key=\"nd-postalcode\">0000</data>"; 
  $retstring=$retstring."<data key=\"nd-country\">$country</data>"; 
  $retstring=$retstring."<data key=\"nd-timezone\">$timezone</data>"; 
  $retstring=$retstring."<data key=\"nd-pictname\">$pictname</data>"; 
  $retstring=$retstring."<data key=\"nd-pos_x\">0</data>"; 
  $retstring=$retstring."<data key=\"nd-pos_y\">0</data>"; 
  $retstring=$retstring."<data key=\"nd-pos_z\">0</data>"; 
  $retstring=$retstring."<data key=\"nd-type\">person</data>"; 
  $retstring=$retstring."</node>";

 

       for($k=0;$k<sizeof($idsArray);$k++)
	   {
	      $copy_idsArray[$k] = $idsArray[$k];
	   }
    


	
        $comp_strength_token = strtok($comp_strengths_list, ",");
		 
		   while ($comp_strength_token !== false)
           {
		   
		            $pos=stripos($comp_strength_token,"?");
		            $competenceId=substr($comp_strength_token,0,$pos);
					$competenceStrength=substr($comp_strength_token,$pos+1) / 100;
				//	echo $competenceId."--".$competenceStrength."</br>";
			//		$retstring=$retstring."<affiliation id=\"$competenceId\" strength=\"$competenceStrength\"/>\n";
					
				 $retstring=$retstring."<edge id=\"$competenceId\" source=\"$id\" target=\"$competenceId\" >";	
			     $retstring=$retstring."<data key=\"ed-strength\">$competenceStrength</data>";
				 $retstring=$retstring."</edge>";	
			          
					$val=array_search($competenceId,$copy_idsArray);
                  //  unset($copy_idsArray[$val]);					
			        $copy_idsArray[$val]="";
			
			
			/*	    
		        $result=mysql_query("select name from competencetable where id='$competenceId'");
              
			   while ( $competenceRow = mysql_fetch_array($result) )
				{
	                   	 $competenceName		= 	$competenceRow['name'];
			              fputs($fid,"<affiliation id=\"$competenceI\" name=\"$competenceName\"/>\n"); 		
	                               }
			*/
                $comp_strength_token = strtok(",");
           }
	
	//     $echostr=$echostr. $name."\n".$alias."\n".$email."\n".$jobrole."\n".$organization."\n".$region."\n".$country."\n".$pictname."\n";
        //now need to put strength =0 for other competences that are not declared
        //here size of idsArray and copy_idsArray has to be same 
         for($k=0;$k<sizeof($idsArray);$k++)
	     {
	       if($copy_idsArray[$k]!="")
		   {
		     //$retstring=$retstring."<affiliation id=\"$copy_idsArray[$k]\" strength=\"0\" />\n";
			 $retstring=$retstring."<edge id=\"$copy_idsArray[$k]\" source=\"$id\" target=\"$copy_idsArray[$k]\" >";	
			     $retstring=$retstring."<data key=\"ed-strength\">0</data>";
				 $retstring=$retstring."</edge>";	
			     
			 
			        
		   }
	     }		

		     

   }

	

      $retstring=$retstring."</graph></graphml>";
	 
	 // for tesing
	  //echo $retstring;
	 fwrite($fid, $retstring);
	 fclose($fid);
	
	//$fname="sai.xml";
	echo $fname;
	?>