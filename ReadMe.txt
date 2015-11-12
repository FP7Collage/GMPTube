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
 
 Technical Guide to deploy CALTTubes
------------------------------------------------------------------

Software’s Requirement to Deploy the CALTTubes

CALTTubes have been tested on the machine having 4 GB of RAM and Intel® Xeon 5130 @2.00 GHZ. Ideally it would be good to have such a powerful machine as video streaming requires expensive CPU Usage.

Here is the list of the software’s that should be installed on the machine.

1.	Windows Server
2.	PHP 5
3.	My SQL 5.0 Server 
4.	WAMP Server


Technical Steps to Install the CALTTubes

Set up PHP and MySQL on windows machine. You can use WAMP to do the installation if you do not plan to install PHP and MYSQL separately.

WAMP can be downloaded from http://www.wampserver.com/en/download.php

After installing PHP and My SQL, Follow the steps below to deploy the CALTTube Package.

Download the CALTTube package.

Step1:
Copy the prototyping folder to the root directory of your server. (For WAMP Installation, you can copy prototyping\GMPWin folder to www folder)

Step2: 
Create New User to access MYSQL database. 
Name of the User: “demo”
Password: “demo”

Step3:
Restore MYSQL Database from InnoTube.sql (Created Database Name will be ostube)
Assign all the Available Database Privileges to “demo” user.  
You can use MySql Admin to assign all the privileges (SELECT, INSERT, UPDATE, DELETE, etc) in the User Administrator Section. 

Step4: (Already added in the given package)
To change the location of video upload, Open GMPWin/GraphData/ffmpeg/conntube.php using some text editor

$full_tubename  = "C:\\wamp\\www\\prototyping\\GMPWin\\GraphData\\upload\\videos\\";
$pictureaddress  = "C:\\wamp\\www\\prototyping\\GMPWin\\GraphData\\media\\videos\\";
$ffmpegaddress  = "C:\\wamp\\www\\prototyping\\GMPWin\\GraphData\\ffmpeg\\";


Step5:  (Already added in the given package)
Open GMPWin/GraphData/ConnTube.php using some text editor
Change the $host parameter to your server name

$host		=	"localhost";
$loginname	=	"demo";
$passwd		=	"demo";
$db		=	"osube";


Step6:  (Already added in the given package)
Open GMPWin/GMPWin.html using some text editor
Go to Line 74 and change only the serverName in the flashvars to your host name.

"flashvars",'servername=http://localhost&fmsname=rtmp://localhost/gmpwin&tubepath=/gmpwin&tubeName=gmpwin',

Step7:  (Already added in the given  package)
Open gmpwin/GraphData/constant.xml using some text editor
Change the serverName to your host name
<ServerName>localhost </ServerName>

Step8: Changes In the php.ini
To make the upload work for larger video, you need to change the following parameter in php.ini file.
For wamp server, php.ini file is located in the Apache2 directory for example (C:\wamp\Apache2\bin)
post_max_size = 100M
upload_max_filesize = 100M
session.gc_maxlifetime = 28800

You are all done.


