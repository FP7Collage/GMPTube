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

$reqPrivacy = isset($_REQUEST['reqPrivacy']) ? $_REQUEST['reqPrivacy'] : '';

switch($reqPrivacy) {
	case 'people':
		require('conntube.php');
		$loginId = $_REQUEST['loginId'];
		$rs = mysql_query("SELECT name_py, company_py, title_py,
			nationality_py, picture_py, url_py, interests_py,
			profile_py, emailid_py, location_py, invitedby_py,
			competences_py FROM peoplenodes WHERE id='$loginId'");
		$result = '<rsp>';
		while($row = mysql_fetch_array($rs)) {
			$result .= '<name_py>' . $row['name_py'] . '</name_py>';
			$result .= '<company_py>' . $row['company_py'] . '</company_py>';
			$result .= '<title_py>' . $row['title_py'] . '</title_py>';
			$result .= '<nationality_py>' . $row['nationality_py'] . '</nationality_py>';
			$result .= '<picture_py>' . $row['picture_py'] . '</picture_py>';
			$result .= '<url_py>' . $row['url_py'] . '</url_py>';
			$result .= '<interests_py>' . $row['interests_py'] . '</interests_py>';
			$result .= '<profile_py>' . $row['profile_py'] . '</profile_py>';
			$result .= '<emailid_py>' . $row['emailid_py'] . '</emailid_py>';
			$result .= '<location_py>' . $row['location_py'] . '</location_py>';
			$result .= '<invitedby_py>' . $row['invitedby_py'] . '</invitedby_py>';
			$result .= '<competences_py>' . $row['competences_py'] . '</competences_py>';
		}
		$result .= '<status>True</status>';
		$result .= '</rsp>';
		echo $result;
		break;
	default:
		echo "<rsp>";
		echo "<status>False</status>";
		echo "<reqPrivacy>$reqPrivacy</reqPrivacy>";
		echo "<loginId>$loginId</loginId>";
		echo "</rsp>";
}

function reply($status) {
	echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
	if($status) {
		echo "<rsp>";
		echo "</rsp>";
	} else {
		echo "<rsp><status>False</status></rsp>";
	}
	die();
}

?>
