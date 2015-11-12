

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
		import component.simpleLogin;
		
		import mx.rpc.events.ResultEvent;



		/** ADMIN FUNCTIONS
		 * 		
		 * 	DELETE VIDEO NODES, REMOVE USERS, DELETE TAGS
		 *  DELETE COMMENTS		 
		 * **/
		 
		
		private function AdminLoginScreen():void{
			if ( adminenter ){
				adminenter 		= 	false;
				currentState	=	'new_ui';
				return;
			}	
			var loginWin:simpleLogin=simpleLogin(PopUpManager.createPopUp(this,simpleLogin,true));	
			PopUpManager.centerPopUp(loginWin);		
		}
		
		
		
		
		/* 
		
		public function deletecomment(comment:String,individualComment:String):void{
		//	CursorManager.setBusyCursor();	
			var variables:URLVariables = new URLVariables();
			variables.action   			= 	"deletecomment";
			variables.videoid  			= 	Curr_Video.@id.toString();
			variables.comment  			= 	comment;
			variables.individualComment =   individualComment;
			adminreq.request 			= 	variables;		
			adminreq.url				=	ServerPath + graphData + "admin.php";
			comments_window.lastupdated	=	Curr_Video.@id.toString();
			adminreq.addEventListener(ResultEvent.RESULT,adminresponse_com);			
			adminreq.send();				
		}
		 */
		/** if the comment is updated succesfully in server, we reload the network again.
		 * 	 **/
		
		public function adminresponse_com(r:ResultEvent):void{
			try{
   				var responseMessage:String;
  				responseMessage = r.result.rsp.message;
	   			if ( responseMessage == 'Success'){
	   				Alert.show(v1001,successAlert);	 
	   				dataloaded	=	false;
	   				comments_window.waitingforupdate.start();
	   				onAppCreationComplete();
	   				//var item:Item			=	fullGraph.find( comments_window.lastdeleted );
					//commentspanel.title		=	item.data.@name.toString();
					//comments_window.PutComments( item.data.@comments.toString() );
						
	   				//comments_window.PutComments ( Curr_Video.@comments.toString() );
	   				//dataloaded	=	false;
	   				//comments_window.lastupdated = 	Curr_Video.@id.toString();
	   			}
	   			else{
	   				trace('Update failed','Try again');
	   			}		   				
	   		}
   			catch(e:Error){
   				trace('Unable to update','Error');
   			}
   			CursorManager.removeBusyCursor();	
   			adminreq.removeEventListener(ResultEvent.RESULT,adminresponse_com);
   			return;
		}
		
		/* public function adminresponse_com(r:ResultEvent):void{
			try{
   				var responseMessage:String;
  				responseMessage = r.result.rsp.message;
	   			if ( responseMessage == 'Success'){
	   				Alert.show('Updated in server','Success');	 
	   				dataloaded	=	false;
	   				comments_window.lastupdated = 	Curr_Video.@id.toString();
	   				comments_window.waitingforupdate.stop();
	   				comments_window.waitingforupdate.reset();
	   				comments_window.waitingforupdate.start();
	   				onAppCreationComplete();	
	   			}
	   			else{
	   				Alert.show('Update failed','Try again');
	   			}		   				
	   		}
   			catch(e:Error){
   				Alert.show('Unable to update','Error');
   			}
   			CursorManager.removeBusyCursor();	
   			adminreq.removeEventListener(ResultEvent.RESULT,adminresponse_com);
   			return;
		} */
		
		public function change_videocategory( id:String ,req_category:String ):void
			{
			var variables:URLVariables = new URLVariables();
			variables.action   		= 	"updatevideocategory";
			variables.videoid  		= 	id;
			
			/* switch(application.change_video_category.selectedLabel)
	        	{
	        		
	        		case 'CD':
	        		  if(data.@category.toString().equals("CD"))
	        		  {
	        		    Alert.show("The Video is present in CD itself", "Alert");   
	        		    return; 
	        		  }
	        		  variables.category = "CD";
	        		 break;
	        		 
	        		case 'CD Opportunities':
	        		  if(data.@category.toString().equals("CD Opportunities"))
	        		  { 
	        		  	 Alert.show("The Video is present in CD Opportunities itself", "Alert");
	        		  	 return;
	        		  }
	        		  variables.category = "CD Opportunities";
	        		 break;
	        		 
	        		case 'CD Experts':
	        		  if(data.@category.toString().equals("CD Opportunities"))
	        		   {
	        		   	  Alert.show("The Video is present in CD Opportunities itself", "Alert");
	        		   	  return;
	        		   }
	        		variables.category = "CD Experts";
	        		 break;
	        	}
 */			
		    variables.category = req_category ;		
			adminreq.request 		= 	variables;		
			adminreq.url			=	ServerPath + graphData + "admin.php";		
			adminreq.addEventListener(ResultEvent.RESULT,admin_change_videocategory);	
			adminreq.send();			
			}
			
			public function admin_change_videocategory(r:ResultEvent):void
			{
			try{
   				var responseMessage:String;
  				responseMessage = r.result.rsp.message;
  				
  				if ( responseMessage == 'Success'){
	   				Alert.show(v1001,successAlert);	 
	   				onAppCreationComplete();		
	   				select_centre	=	true;
					centre_id		=	Univ_LoginId;
	   			}
	   			else{
	   				Alert.show('Update failed','Try again');
	   			}		   				
	   		}
   			catch(e:Error){
   				trace('Unable to update','Error');
   			}
   		   adminreq.removeEventListener(ResultEvent.RESULT,admin_change_videocategory);
   		   return;
		}
		
		
		
		
		
		public function deletevideo(id:String):void{
			var variables:URLVariables = new URLVariables();
			variables.action   		= 	"deletevideo";
			variables.videoid  		= 	id;
			adminreq.request 		= 	variables;		
			adminreq.url			=	ServerPath + graphData + "admin.php";		
			adminreq.addEventListener(ResultEvent.RESULT,adminresponse_vid);	
			adminreq.send();				
 		//	CursorManager.setBusyCursor();						
		}
		
		public function adminresponse_vid(r:ResultEvent):void{
			try{
   				var responseMessage:String;
  				responseMessage = r.result.rsp.message;
  				if ( responseMessage == 'Next version exists' ){
  					var nextver:String = r.result.rsp.entity;
  					Alert.show('The selected video has a next version with the name "' + nextver + '". Please delete that before this','Cannot delete !');	
  				} 
  				else if ( responseMessage == 'Success'){
	   				Alert.show(v1001,successAlert);	 
	   				onAppCreationComplete();		
	   				select_centre	=	true;
					centre_id		=	Univ_LoginId;
	   			}
	   			else{
	   				Alert.show('Update failed','Try again');
	   			}		   				
	   		}
   			catch(e:Error){
   				trace('Unable to update','Error');
   			}
   			CursorManager.removeBusyCursor();	
   			adminreq.removeEventListener(ResultEvent.RESULT,adminresponse_vid);
   			return;
		}
		
		