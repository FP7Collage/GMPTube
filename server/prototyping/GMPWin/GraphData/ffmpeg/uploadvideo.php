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
set_time_limit(1200);
define("MAX_EXECUTION_TIME", 300);

include "cons.php";
// the next 2 lines to prevent caching
session_start();
header("Cache-control: private");
header( 'Content-type: text/html; charset=utf-8' );

	// 	extract the file name from in input field Filedata		

	$file_name 			=	$_POST['filename'];
	$onlyname	 		=	$_POST['onlyname'];
	$randomstr	 		=	$_POST['randomstr'];
	
	if ($randomstr == "" ){
	      $randomstr = "abcd";
	}

	error_log (" Starting Thumbnail  uploadvideo.php randomstr " . $randomstr . " filename " . $file_name );
	
	// destination	
	$target_file_path 	= 	$file_name;
	
	// Get the temporary filename of the file in which the uploaded file was stored on the server
	$downloaded_file = $_FILES['Filedata']['tmp_name'];
	//set_time_limit(0);
	
	// check if the uploading went fine
	if ($_FILES['Filedata']['error'] != UPLOAD_ERR_OK) {
		//echo "\n<br>error. Code: " . $_FILES['Filedata']['error'];
		echo 'Upload Failure';
		print_r('<br>to (target_path) = ' . $target_file_path );
		print_r('<br>from = ' . $downloaded_file);
		exit;
	}
	
	if (file_exists("$target_file_path")){
		// $aldreadyexists = true;
		// $Message = 'Exists';
		// showmessage();
		// exit;
		unlink($target_file_path);
	}

	
	// move the file from the temporary location to the final location
//	$exten = strrchr($file_name,'.');
		$exten = substr(strrchr($file_name, "."), 1);
		error_log("exten ". $exten);
	if($exten =='.swf'|| $exten=='.SWF'||$exten=='.Swf')
	{
		$target_file_path = './swf/'.$randomstr.'.swf';
		if(move_uploaded_file($downloaded_file, $target_file_path )){
			echo 'SuccessSWF';
			exit;
		}
		else{
	   // $Message =  "\n<br>There was an error: " . $_FILES['Filedata']['error'];
	    $Message = 'Error';
	    showmessage();
		exit;
		}
	}
	else if(move_uploaded_file($downloaded_file, $target_file_path )) {
	
		// error_log("uploadvideo exten ". $exten);
		
		$onlyname		=	$randomstr;
		$thumbnail		=	$pictureaddress.$onlyname.'.jpg';	//	destine.jpg
		$encode_query	= $ffmpegaddress . 'ffmpeg.exe -itsoffset -4 -i ' . '"' . $ffmpegaddress .$file_name.'" -vcodec mjpeg -vframes 1 -an -f rawvideo -s 100x100  "'. $thumbnail.'"' .' && exit';
		
	//	error_log($encode_query);
		
			$username = "";
			$message = $thumbnail;
			sendMail($username, $message);
		
				$timeline = time() + MAX_EXECUTION_TIME;
				
				//PsExecute($encode_query,60,2);
				
				session_write_close();		
				exec($encode_query);
				session_start();
		
				check_timeout();
				
	     		echo 'Success';
			

			error_log ("finishing Thumbnail  uploadvideo.php randomstr " . $randomstr . " filename " . $file_name );
			
			ob_flush();
			flush();
		    exit;
	} 
	else{
	    $Message = 'Error';
	    showmessage();
	}

	
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
		
	//	error_log("uploadvideo1 pid " . $pid);

        if( $pid === false )
            return false;

        $cur = 0;
        // Second, loop for $timeout seconds checking if process is running
        while( $cur < $timeout ) {
            sleep($sleep);
            $cur += $sleep;
            // If process is no longer running, return true;
			error_log("uploadvideo while pid " . $pid);

           echo "\n ---- $cur ------ \n";

            if( !PsExists($pid) )
                return true; // Process must have exited, success!
        }

		error_log("uploadvideo2 pid " . $pid);
        // If process is still running after timeout, kill the process and return false
        PsKill($pid);
        return false;
    }

    function PsExec($commandJob) {

        $command = $commandJob.' > /dev/null 2>&1 & echo $!';
		
        session_write_close();		
        exec($command ,$op);
		session_start();
		
        $pid = (int)$op[0];
		
   //     error_log("uploadvideo3 pid " . $pid);
		
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

	


function sendMail($username,$message)
{

$fromuser = "CALTTubes@insead.edu'";
$to = "pkmittal82@gmail.com";

//$message = $retstring;
$headers = 'From:'.$fromuser."\r\n" .
    'Reply-To:'.$fromuser."\r\n" .
    'X-Mailer: PHP/' . phpversion();
	
$message3 = $message;
	
$subject = "Thumbnail Uploaded";
$val=mail($to, $subject, $message3, $headers);

	

  }	
  
  
	


	
?>


<?php
function check_timeout()
{
if( time() < $GLOBALS['timeline'] )
return;

# timeout reached:

echo "It took more than 9 minute in converting video.";
error_log("timeout reached uploadvideo.php" );
exit;
}
?>

	
	
	
