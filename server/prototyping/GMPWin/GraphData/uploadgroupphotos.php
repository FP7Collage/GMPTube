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


	// extract the file name from in input field Filedata
	
	$file_name = basename( $_FILES['Filedata']['name']);
	
	// destination	
	$target_file_path = './media/groupphotos/'.$file_name;
	
	// Get the temporary filename of the file in which the uploaded file was stored on the server
	$downloaded_file = $_FILES['Filedata']['tmp_name'];
	
	//$finalpicname	 = $_FILES['Filedata']['picname'];
	
	// check if the uploading was all right
	$upload_status = $_FILES['Filedata']['error'];
	
	if ($upload_status != UPLOAD_ERR_OK) {
		echo "\n<br>error. Code: " . $upload_status;
		// echo "\n<br>see  <a href='http://au.php.net/manual/en/features.file-upload.errors.php'>error code</a>"
		print_r('<br>to (target_path) = ' . $target_file_path );
		print_r('<br>from = ' . $downloaded_file);
	}
	
	
	
	// move the file from the temporary location to the final location
	if(move_uploaded_file($downloaded_file, $target_file_path )){
		//rename("./media/groupphotos/".$_FILES['Filedata']['name'], "./media/groupphotos/".$finalpicname);
	    echo "The file " .  $file_name . " has been uploaded";
	} 
	else{
	    echo "\n<br>There was an error: " . $_FILES['Filedata']['error'];
	}

?>