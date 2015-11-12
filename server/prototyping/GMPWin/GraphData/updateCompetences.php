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
	$name = $_POST['name'];
	$category=$_POST['category'];
	$strength=$_POST['strength'];
	$id=$_POST['id'];
//	$name="BrandNew";
//	$category="Hardware3";
//	$strength=90;
//	$id="Rakesh.lalwani@insead.edu";
	$rs = mysql_query("Select * from affiliationtable");
	$boo=false;
	$largestId=0;
	while ($row = mysql_fetch_array($rs))
	{
		$name_db	=	$row['name'];
		if($largestId<$row['id'])
		{
		  $largestId=$row['id'];
		}
		if($category==$name_db)
		{
		  $compArray = explode(',',$row['competences']);
		  $boo=true;
		  $result = count($compArray);
		  $lastId= $compArray[$result-1];
		 // $compArray[$result]=$lastId+1;
		  $newId=$lastId+1;
		 $competences = $row['competences'].",".$newId;//implode(',',$compArray);
		 mysql_query("UPDATE affiliationtable set competences = '$competences' where name = '$name_db'");
		 mysql_query("INSERT into competencetable values ('$newId','$name')");
		 
		 $rs1=mysql_query("Select competences from peoplenodes where id='$id'");
		   while ($row1 = mysql_fetch_array($rs1))
		   {
		     
			  $comp_strengths=$row1['competences'].",".$newId."?".$strength;
			  mysql_query("UPDATE peoplenodes set competences = '$comp_strengths' where id = '$id'");
		      echo "success";
	      
		   }
		}
	}
	
	if($boo==false)
	{
	     $categoryId=$largestId+1;
	     $comp_Id=$categoryId."01";
         mysql_query("INSERT INTO affiliationtable (name,competences,id) VALUES('$category','$comp_Id','$categoryId')");         		 
		 mysql_query("INSERT into competencetable values ('$comp_Id','$name')");
		 $rs2=mysql_query("Select competences from peoplenodes where id='$id'");
		   while ($row2 = mysql_fetch_array($rs2))
		   {
		     
			  $comp_strengths=$row2['competences'].",".$comp_Id."?".$strength;
			  mysql_query("UPDATE peoplenodes set competences = '$comp_strengths' where id = '$id'");
		   }
    echo "Success";   
   }
	
?>
	