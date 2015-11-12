

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




// ActionScript file
		//import component.simpleLogin;
		
		import mx.rpc.events.ResultEvent;



		
		
	// added to include edit comments functionality	
/*
		public function editcomment(comment:String,individualComment:String):void{
		//	CursorManager.setBusyCursor();	
			
			var variables:URLVariables = new URLVariables();
			variables.action   			= 	"editcomment";
			variables.videoid  			= 	Curr_Video.@id.toString();
			variables.comment  			= 	comment;
			variables.individualComment =   individualComment;
			editcommentreq.request 			= 	variables;		
			editcommentreq.url				=	ServerPath + graphData + "editusercomment.php";
			comments_window.lastupdated	=	Curr_Video.@id.toString();
			editcommentreq.addEventListener(ResultEvent.RESULT,adminresponse_com);			
			editcommentreq.send();				
	
	
	
			
	
		}
		
		
*/		

private function addCommentFault():void{
					trace("addCommentFault()");					
				} 	
private function addScrapFault():void{
					trace("addScrapFault()");					
				}	
				
public function refreshcomments():void{
					var curid:String	=	playingvideo.data.@id.toString();
						
					var variables:URLVariables  = 	new URLVariables();
					variables.videoId  			= 	curid;	
					
					comments_window.get_commentsxml.url            = 	ServerPath + graphData + "getVideoComments.php";	  
					comments_window.get_commentsxml.request 		= 	variables;
					comments_window.get_commentsxml.send();
}

public function refreshscraps():void{
					var userid:String	=	detailedprofileId;
						
					var variables:URLVariables  = 	new URLVariables();
					variables.userid  			= 	userid;	
					
					tentube_profile.scraps_window.get_scrapsxml.url            = 	ServerPath + graphData + "getScraps.php";	  
					tentube_profile.scraps_window.get_scrapsxml.request 		= 	variables;
					tentube_profile.scraps_window.get_scrapsxml.send();
}

	public function deletecomment(commentid:String):void{
		//	CursorManager.setBusyCursor();	
			var variables:URLVariables = new URLVariables();
			variables.action   			= 	"deletecomment";
			//variables.videoid  			= 	Curr_Video.@id.toString();
			variables.commentid  			= 	commentid;
			//variables.individualComment =   usrcomment;
			edit_comments.request 			= 	variables;		
			edit_comments.url				=	ServerPath + graphData + "change.php";
			comments_window.lastupdated	=	Curr_Video.@id.toString();
			//edit_comments.addEventListener(app.ResultEvent.RESULT,app.adminresponse_com);			
			edit_comments.send();	;			
						
						
		}
	
		/** if the comment is updated succesfully in server, we reload the network again.
		 * 	 **/

		public function deletescrap(scrapid:String):void{
		//	CursorManager.setBusyCursor();	
			var variables:URLVariables = new URLVariables();
			variables.action   			= 	"deletescrap";
			variables.userid  			= 	detailedprofileId;
			variables.scrapid  			= 	scrapid;
			//variables.individualComment =   usrcomment;
			edit_scraps.request 			= 	variables;		
			edit_scraps.url				=	ServerPath + graphData + "change.php";
			//comments_window.lastupdated	=	Curr_Video.@id.toString();
			//edit_comments.addEventListener(app.ResultEvent.RESULT,app.adminresponse_com);			
			edit_scraps.send();	;			
						
						
		}
		
		
		
		// ActionScript file
