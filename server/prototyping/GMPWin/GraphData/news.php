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
	
	$action = $_REQUEST['action'];
	
	//global $entity;
	$newsidlist = array();
	switch($action)
	{
		case 'addnews':
			addnews();
			break;
			
		case 'deletenews':
			deletenews();
			break;
		
		case 'editnews':
			editnews();
			break;
		
		
		
		default:
			echo 'Just one more step!';
		
			break;	
	}

?>
	
<?php

	function addnews()
	{	
        global	$record_path;
		if (isset($_POST['title'])) {  
		$Title	= 			$_POST['title'];
		}
		if (isset($_POST['description'])) {  
		$Description 		= $_POST['description'];
		}
		
		
		$Datepost	= date("Y-m-d");
		
		if (isset($_POST['isactive'])) {  
		$active	= 	$_POST['isactive'];
		}
		//error_log("titleee" . date("Y-m-d"));
		//error_log("titleee" . $active);
		
		if ($active == 'false')
		$isactive = 0;
		else 
		$isactive = 1;
		
		
		//error_log("titleee" . $isactive);

		// $externalLinks	= "";
		// $docLinks		= "";
		$i = 0;
		$query = mysql_query("select * from news ");
		
		while($how=mysql_fetch_array($query))
		{
		//echo $how['id'];
		$i = $how['id'];
		
		
		}
		
			echo $i;
			$id = $i+ 1 ;
			$query = "INSERT INTO NEWS(id,Title,Description,isactive,Date)
			VALUES ('$id','$Title','$Description','$isactive','$Datepost')";
			//error_log("titleee" . $query);
			mysql_query ($query) or die("MYSQL ERROR: ".mysql_error());
			
			showSuccessMessage();
		}

		
?>

<?php

	function editnews()
	{		
		if (isset($_POST['id'])){
		$id		= $_POST['id'];	
		}
		//error_log("titleee" . $id);
		if (isset($_POST['title'])){
		
		$Title	= 		$_POST['title'];
		}
		//error_log("titleee" . $Title);
		if (isset($_POST['description'])) {  
		$Description 		= $_POST['description'];
		}
		$Datepost			= date("Y-m-d");
		//error_log("titleee" . $Datepost);
		
		if (isset($_POST['isactive'])) {  
		$active	= 	$_POST['isactive'];
		}
		
		//error_log("titleee" . $active);
		if ($active == 'false')
		$isactive = 0;
		else 
		$isactive = 1;
	
		$query = " UPDATE NEWS SET id = '$id',Title = '$Title',Description ='$Description', isactive = '$isactive', Date = '$Datepost' WHERE id = '$id' "; 
		//error_log("titleee" . $query);
		// create Has inspired edges !!
		mysql_query ($query) or die(mysql_error()); 
		
	}	
?>
<?php

	function deletenews(){
	if (isset($_POST['id2'])){
		$id2	= $_POST['id2'];
		}
		//error_log("titleee" .$_POST['id2']);
			$query = "DELETE FROM NEWS WHERE id = '$id2' ";
			mysql_query ($query) or die(mysql_error()); 
			
	}

?>

<?php

	function showSuccessMessage()
	{
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
		echo "<rsp><message>Success</message></rsp>";
	}

?>