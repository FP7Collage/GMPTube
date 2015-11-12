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
//session_start();
header("Cache-control: private");
header("Content-type: text/xml");

$id = $_POST['id'];
$time = $_POST['time'];

include "conntube.php";
// if($_GET['from']!="")
// {
// $id = $_GET['from'];
// }
$retstring= "<?xml version=\"1.0\" encoding=\"utf-8\"?>";  
$retstring= $retstring ." <Messages>";
//mysql_query ("UPDATE chatusers SET accesstime = current_timestamp() where id = '$id'");
$count=0;
$rs=mysql_query("Select * from chatmessages where (toid='$id' or toid='all') and messagetime>'$time'");
// while(($count<10) &&(mysql_num_rows($rs)<1))
// {
// sleep(2);
// $rs=mysql_query("Select * from chatmessages where toid='$id'");
// $count++;
// }
if(mysql_num_rows($rs)>0)
{
while ($row = mysql_fetch_array($rs))
{
$retstring=$retstring ." <Message id = \"$row[fromid]\" text = \"$row[message]\" time = \"$row[messagetime]\" />" ;
}
}
$retstring=$retstring ."  </Messages>";
//if($count<10)
//{
//mysql_query("delete from chatmessages where toid='$id'");
//}
echo $retstring;

mysql_close($conn);
?>