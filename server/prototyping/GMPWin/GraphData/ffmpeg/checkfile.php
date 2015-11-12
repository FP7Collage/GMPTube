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
include "cons.php";
header( 'Content-type: text/html; charset=utf-8' );

	$filename 	=	$_REQUEST['filename'];
	$randomstr	 		=	$_REQUEST['randomstr'];
	$target_file_path 	= 	$filename;
	set_time_limit(800);  // do not make it zero otherwise it will stuck for ever. it is for 10 mins
	define("MAX_EXECUTION_TIME", 700);  // for the conversion
	
	error_log ("starting video conversion checkfile.php randomstr " . $randomstr . " filename " . $filename );
	
	if ($randomstr == "" ){
	      $randomstr = "abcd";
	}

    if (file_exists($filename)) {
		$onlyname		=	$randomstr;
		$streamaddress	=	'.\\..\\upload\\videos\\';
	    $viddestination	=	$streamaddress.$onlyname.'.flv'	;	//	destine.flv		
		
		$exten = substr(strrchr($target_file_path, "."), 1);					
		if (strcasecmp($exten, 'flv') == 0) {
             $flvpath = 		"./../upload/videos/" . $onlyname.'.flv';
			if(rename($target_file_path , $flvpath )) {
			//		  error_log("flv moved");
					}else{
					error_log("flv could not be moved");
					}
		 }else if (strcasecmp($exten, 'mp4') == 0){ 
	           $flvpath = 		"./../upload/videos/" . $onlyname.'.mp4';
					if(rename($target_file_path , $flvpath )) {
				//	  error_log("mp4 moved");
					}else{
					error_log("mp4 could not be moved");
					}		 
	
		}else{  		
//$encode_query	=	'ffmpeg -i "'.$target_file_path.'" -s 1280x720  -ar 44100 -async 44100 -r 29.970 -ac 2 -qscale 5 "'.$viddestination.'"' .' && exit';
//$encode_query	=	'ffmpeg -i "'.$target_file_path.'" -ar 22050  -f flv -s 320x240 "'.$viddestination.'"' .' && exit';
$encode_query	=	'ffmpeg -i "'.$target_file_path.'"   -ar 44100 -async 44100 -r 29.970 -ac 2 -qscale 5 "'.$viddestination.'"' .' && exit';
//$encode_query	=	'ffmpeg -i "'.$target_file_path.'"   -c:v libx264 -preset ultrafast "'.$viddestination.'"' .' && exit';
//ffmpeg -i input.wmv -c:v libx264 -preset ultrafast out.mp4

		//	error_log($encode_query);
				$timeline = time() + MAX_EXECUTION_TIME;
				
			//	PsExecute($encode_query,600,2);
				
				session_write_close();		
				exec($encode_query);
				session_start();
		
				
				check_timeout();		
				
				if(rename($target_file_path , "./archive/" . $target_file_path )) {
				}else{
				     unlink("./archive/" . $target_file_path );
					 rename($target_file_path , "./archive/" . $target_file_path );
					 
				}
				
		
      //   unlink($filename);
		}
		
	   	echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
		echo "<rsp><message>Success</message></rsp>";
		
		    $username = "";
			$message = $full_tubename . "" .$target_file_path;
			sendMail($username, $message);
			
	} 
	else {
	   	echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
		echo "<rsp><message>Failed</message></rsp>";
	}

	error_log ("finishing video conversion checkfile.php randomstr " . $randomstr . " filename " . $filename );
?>



<?php
function check_timeout()
{
if( time() < $GLOBALS['timeline'] )
return;

# timeout reached:
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo "<rsp><message>Failed. It took more than 9 minute in encoding.</message></rsp>";
error_log("timeout reached checkfile.php" );

exit;
}
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

	//	error_log("checkfile1 pid " . $pid);
		
        if( $pid === false )
            return false;

        $cur = 0;
        // Second, loop for $timeout seconds checking if process is running
        while( $cur < $timeout ) {
            sleep($sleep);
            $cur += $sleep;
            // If process is no longer running, return true;
			error_log("checkfile while pid " . $pid);

           echo "\n ---- $cur ------ \n";

            if( !PsExists($pid) )
                return true; // Process must have exited, success!
        }
		
			error_log("checkfile2 pid " . $pid);
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
		
//		error_log("checkfile3 pid " . $pid);
		
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
	
$subject = "Video Converted";
$val=mail($to, $subject, $message3, $headers);

	

  }	

	


	
?>