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
	
	global $entity;
	
	switch($action)
	{
		
		case 'setsettings':
			setsettings();
			break;
		
		case 'getsettings':
			getsettings();
			break;
		
		default:
			echo 'Just one more step!';
		//	$filename = 'demo.txt';
		//	$saveFile = fopen($filename, "w");
		//	fwrite ($saveFile, $query);
		//	fclose($saveFile);
			break;	
	}

?>


<?php

function setsettings() {
	$uid = $_REQUEST['userid'];
	$freq= $_REQUEST['freq'];
	//error_log($freq);
	
	switch($freq)
	{
		
		case 'Daily':
			$rf=-1;
			break;
		
		case 'Weekly':
			$rf=-7;
			break;
		case 'Biweekly':
			$rf=-15;
			break;
		case 'Monthly':
			$rf=-30;
			break;
		case 'Never':
			$rf=0;
			break;
		default:
			echo 'Just one more step!';
		//	$filename = 'demo.txt';
		//	$saveFile = fopen($filename, "w");
		//	fwrite ($saveFile, $query);
		//	fclose($saveFile);
			break;	
	}
	echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
	echo "<rsp><message>";
	$count = mysql_query("UPDATE peoplenodes SET RF='$rf' WHERE id='$uid'");
	if($count > 0)
		echo 'Success';
	else
		echo 'Failed:' . mysql_error();
	echo '</message></rsp>';
}

?>


<?php

function getsettings() {
	$uid = $_REQUEST['userid'];
	echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
	echo "<rsp><message>";
	$query = mysql_query("select RF from peoplenodes WHERE id='$uid'");
	$row=mysql_fetch_array($query);
	$freq=$row['RF'];
	//echo $freq;
	if($query > 0)
		echo $freq;
	else
		echo 'Failed:' . mysql_error();
	echo '</message></rsp>';
}

?>

<?php

	function showSuccessMessage(){
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
		echo "<rsp><message>Success</message></rsp>";
	}

?>

