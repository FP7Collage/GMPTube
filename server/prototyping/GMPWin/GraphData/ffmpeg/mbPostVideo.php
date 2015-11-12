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
header("Content-type: text/xml");

global $retString;
$retString= "<?xml version=\"1.0\" encoding=\"utf-8\"?>";  
$message1 = "";
$message2 = "";
$message3 = "";



    $randomstr	 		=	$_REQUEST['randomstr'];
	$name	=	$_REQUEST['name'];
	$vidfilename  = $name;
	$viddestination = '.\\..\\upload\\videos\\' . $randomstr . ".mp4";

	
	
// Start of uploading snapshot file
	$imagefilename	=	'../media/videos/'.$randomstr.'.jpg';
   if($_FILES['image']['error']!= UPLOAD_ERR_OK){
//		echo 'Failed at point 1';
         $message1 = "Video Thumnnail is not found";
		}
   if(move_uploaded_file($_FILES['image']['tmp_name'], $imagefilename)){
  //       echo "Success1" . "\n";
          $message1 = "Success1";
		 }
  else{
         //echo 'There was an error during the image upload.  Please try again.'; // It failed :(.
		 $message1 = "There was an error during the thumbnail upload. Please try again.";
		 
		 }
		 
// Start of Uploading Video file	
   if($_FILES['file']['error']!= UPLOAD_ERR_OK){
	//	echo 'Failed at point 2';
	$message2 = 'Video file is not found for uploading';
		}
   if(move_uploaded_file($_FILES['file']['tmp_name'], "./../upload/videos/".$vidfilename)){
         //echo "Success2" . "\n";
		 $message2 = 'Success2';
					
	/*		$encode_query	=	'ffmpeg -i "'.$vidfilename.'" -ar 22050  -f flv -s 320x240 "'.$viddestination.'"';
	 		PsExecute($encode_query,180,2);
			unlink($vidfilename);
			*/
	
		// echo "Success3" . "\n";
		    $message3 = 'Success3';
		 
		 }
  else{
       //  echo 'There was an error during the file upload.  Please try again.'; 
	    $message2 = 'There was an error during the video upload. Please try again.';
		 
		 }
		 
	$retString = $retString . "<rsp><message1>$message1</message1><message2>$message2</message2><message3>$message3</message3></rsp>" ;
		
		echo $retString;
		
				 

?>





<?php


	
	function showmessage(){
		global $Message ;
		//echo "<rsp>";
		//echo "<message>$Message</message>";
		//echo "</rsp>";
		//echo "Something";
		echo $Message;
		sleep(1);
		ob_flush();
		flush();
	}
  function runExternal( $cmd, &$code ) {
//    echo "command" . $cmd;
    $descriptorspec = array(
      0 => array("pipe", "r"), // stdin is a pipe that the child will read from
      1 => array("pipe", "w"), // stdout is a pipe that the child will write to
      2 => array("pipe", "w") // stderr is a file to write to
    );
    $pipes= array();
    $process = proc_open($cmd, $descriptorspec, $pipes);
    $output= "";
    if (!is_resource($process)) return false;
     
    #close child's input
    fclose($pipes[0]);
    stream_set_blocking($pipes[1],false);
    stream_set_blocking($pipes[2],false);
    $todo=array($pipes[1],$pipes[2]);
    while(true) {
      $read= array();
      if(!feof($pipes[1]) ) $read[]= $pipes[1];
      if(!feof($pipes[2]) ) $read[]= $pipes[2];
      if(!$read) break;
      $ready= stream_select($read, $write=NULL, $ex= NULL, 2);
      if ($ready === false) {
        echo "died";
        break;
        #should never happen - something died
      }
      foreach($read as $r) {
        $s=fread($r,1024);
        $output.=$s;
      }
    }
    fclose($pipes[1]);
    fclose($pipes[2]);
    $code=proc_close($process);
    return $output;
  }
	
          

?>

<?php
  function PsExecute($command, $timeout = 60, $sleep = 2) {
        // First, execute the process, get the process ID

        $pid = PsExec($command);

        if( $pid === false )
            return false;

        $cur = 0;
        // Second, loop for $timeout seconds checking if process is running
        while( $cur < $timeout ) {
            sleep($sleep);
            $cur += $sleep;
            // If process is no longer running, return true;

           echo "\n ---- $cur ------ \n";

            if( !PsExists($pid) )
                return true; // Process must have exited, success!
        }

        // If process is still running after timeout, kill the process and return false
        PsKill($pid);
        return false;
    }

    function PsExec($commandJob) {

        $command = $commandJob.' > /dev/null 2>&1 & echo $!';
        exec($command ,$op);
        $pid = (int)$op[0];

        if($pid!="") return $pid;

        return false;
    }

    function PsExists($pid) {

        exec("ps ax | grep $pid 2>&1", $output);

        while( list(,$row) = each($output) ) {

                $row_array = explode(" ", $row);
                $check_pid = $row_array[0];

                if($pid == $check_pid) {
                        return true;
                }

        }

        return false;
    }

    function PsKill($pid) {
        exec("kill -9 $pid", $output);
    }

	?>