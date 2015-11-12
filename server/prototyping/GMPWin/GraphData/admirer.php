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
$csv_sep = ',';
$query = isset($_REQUEST['query']) ? stripslashes($_REQUEST['query']) : '';
if($query != '') {
	require('conntube.php');
	$response = mysql_query($query);
	if(!is_resource($response)) {
		if($response > 0) {
			showPage($query, 'Query exececuted successfully. No ResultSet returned.');
		}
		else {
			showPage($query, 'Sorry, your query could not be processed.<HR style="color: #FF9900; width: 40%; margin: 4px auto 2px auto">' . mysql_error());
		}
	} else {
		$result = "";
		header("Content-type: application/octet-stream");
		header('Content-disposition: attachment; filename="ResultSet.csv"');
		while ($row = mysql_fetch_row($response)) {
			for ($col = 0; $col < count($row); $col++) {
				if ($col > 0) $result .= $csv_sep;
				$result .= '"'.addslashes($row[$col]).'"';
			}
			$result .= "\n";
		}
		print ($result);
		exit();
	}
} else {
	showPage('', '');
}
?>

<?php
function showPage($query, $status) {
	echo '<HTML>';
	echo '<HEAD>';
	echo '<TITLE>CALT LABS</TITLE>';
	echo '</HEAD>';
	echo '<BODY style="margin: auto;margin-top: 10px;max-width: 40em;text-align: center;">';
	echo '<DIV class="body" style="background: #99CCFF;padding: 10px;">';
	echo '	<DIV class="status" style="background: #FFCC33;">' . $status . '</DIV>';
	echo '	<DIV class="form" style="padding-top: 5px">';
	echo '		<H4 for="query">Enter Query in the following box</H4>';
	echo '		<FORM action="admirer.php" method="POST">';
	echo '			<TEXTAREA name="query" cols="80" rows="7">' . $query . '</TEXTAREA> <BR>';
	echo '			<INPUT type="submit" value="EXECUTE QUERY"/>';
	echo '			<INPUT type="button" value="CLEAR QUERY" onclick="document.getElementsByName(\'query\')[0].value=\'\';"/>';
	echo '		</FORM>';
	echo '		<H6>NOTE: If the given query returns a result set, it shall be available for download as a csv file.</H6>';
	echo '	</DIV>';
	echo '</DIV>';
	echo '</BODY>';
	echo '</HTML>';
}
?>
