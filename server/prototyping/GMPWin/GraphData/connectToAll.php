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

$groupName = $_REQUEST['groupName'];
$memberName = $_REQUEST['memberName'];
$intensityValue = $_REQUEST['intensityValue'];

//error_log("GroupName ". $groupName ."memberName " . $memberName . " intensityValue ". $intensityValue);



MemberToOtherMembers($groupName,$memberName, $intensityValue);

echo "<rsp><message>Success</message></rsp>";

/*
$memberlist=mysql_query("select memberlist from grpmaindata where gid='8D3BE64228'");
$row= mysql_fetch_array($memberlist);


$memberlist= $row['memberlist'];

$tomemberid=strtok($memberlist,";");

while($tomemberid != false)
{
MemberToOtherMembers('8D3BE64228',$tomemberid);

$tomemberid=strtok(";");
}
*/


?>





<?php



function MemberToOtherMembers($groupName,$memberid, $intensityValue)
{

//echo  $memberid . "\n" ;
//$memberlist=mysql_query("select memberlist from grpmaindata where gid='$groupid'");
$memberlist=mysql_query("select memberlist from grpmaindata where name='$groupName'");

$row= mysql_fetch_array($memberlist);
$memberlist= $row['memberlist'];

$tomemberid=strtok($memberlist,";");

//error_log($tomemberid);
while($tomemberid != false)
{
if($tomemberid==$memberid){
// do nothing
}

else
{
$check=mysql_query("select * from edges where fromID='$memberid' AND toID='$tomemberid' UNION select * from edges where toID='$memberid' AND fromID='$tomemberid'");
//error_log($check);
if(mysql_num_rows($check) == 0){
mysql_query("INSERT INTO edges(name,tooltip,fromID,toID,intensity,edgecolor,creationtime,relationship_py) VALUES ('Knows','knows','$memberid','$tomemberid',$intensityValue,'0x3366cc',current_timestamp(),'#EVERYBODY#')");
 //error_log( "Rows Created from " . $memberid .  " to " .  $tomemberid . "\n" );
}else{
  mysql_query("update edges set intensity =".$intensityValue ." where (fromID='$memberid' AND toID='$tomemberid') OR (toID='$memberid' AND fromID='$tomemberid')");
 //error_log( "rating updated". $memberid . " " . $tomemberid ) ;
}
}

$tomemberid=strtok(";");
}

}




?>