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
function genRandomString() {
    $length = 10;
    $characters = '01234SS6789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = "";    

    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, (strlen($characters))-1)];
    }

    return $string;
}
?>
<?php

	//	* 	to upload bitamp data and write into jpeg file
	//	*	source:	http://tinkerlog.com/2007/11/03/webcam-snapshots-with-flex3/#more-46
	
	$filename	=	genRandomString();
	$binddata	=	$_REQUEST['bindata'];
	$id = $_REQUEST['id'];

//
error_log("User Id" . $filename);	
	if ( $binddata === NULL) { 
		echo "missing parameter.";
	}
	else {
		$img_data = base64_decode( $binddata );
		$name = $filename === NULL ? "anonymous\n" : $filename ."\n";
		$name = strip_tags($name);
		
		$img_size = strlen($img_data);
		
		$img_filename	=	'./media/people/'.$filename.'.jpg';
		
		
		$img_file = fopen($img_filename, "w") or die("can't open file");
		
		fwrite($img_file, $img_data);
		
		
		
		
		fclose($img_file);
		
		echo "$img_size bytes uploaded.";
	}

	include 'conntube.php';
	session_start();
	
	
			//$query=mysql_query("select * from peoplenodes where id='$id'");
			//$row=mysql_fetch_array($query);
			//$picture1 = $row['picture'];
			//$newpicture = str_replace(basename($picture1), $filename.'.jpg', $picture1);
			//error_log("titleee" . selfURL() );
			$newpicture = str_replace(basename(selfURL()), 'media/people/'.$filename.'.jpg', selfURL());
			
			
			$query = " UPDATE PEOPLENODES SET picture = '$newpicture' WHERE id = '$id' " ; 
			//error_log("titleee" . $query);
			mysql_query ($query) or die("MYSQL ERROR: ".mysql_error());
?>

<?php
function selfURL() 
{ 
$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : ""; $protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s; 
$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]); return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];

} 

function strleft($s1, $s2)
{
 return substr($s1, 0, strpos($s1, $s2)); 
}

?>