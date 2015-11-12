

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
		import flash.events.TimerEvent;
		import flash.utils.Timer;
		
		import mx.controls.Alert;
		import mx.rpc.events.ResultEvent;

		private var alert:Alert;


		/** event listener for timercomplete event for minuteTimer:Timer
		 * 		we check if the user has a game invitation from another player   */
		private function onTick(evt:TimerEvent):void{
			minuteTimer.stop();
			minuteTimer.reset();
			var variables:URLVariables = new URLVariables();
			variables.user  		= Univ_LoginId;
			checkinvite.url 		= ServerPath + graphData + "checkinvite.php";	  
			checkinvite.request 	= variables;		
			checkinvite.addEventListener(ResultEvent.RESULT,invitecheck );	
			checkinvite.send();				
			return; 
		}
		
 		
		
		
		public var gameinviteAlert:Alert ;
		public var delay:Number = 10000;
		public var gamealerttimer:Timer	=	new Timer(10000,1);
		
		private function removegamealert(t:TimerEvent):void{
			PopUpManager.removePopUp(gameinviteAlert);
			gamealerttimer.stop();
			gamealerttimer.reset();
			minuteTimer.stop();
			minuteTimer.reset();
      		minuteTimer.start();  		
		}
		
		/** event listener for checkinvite.php
		 * 		if the user has a invite, we put a pop up box and remove after 10 seconds, if he
		 * 		does not reply, anyway we send a message to server. 
		 * 		  */
	    private function invitecheck(r:ResultEvent):void{
	    	if ( userplaying == true) 
	    		return;
		  	try{
		  		var response:XML			=	new XML(r.result);	
 				var status:String 			= 	new String(response.message) ;

		    	if (  status == "INVITED"  ){
		    		invite_opponent	=	response.opponent;	
		    		gameinviteAlert		 				= 	new Alert();
		    		gameinviteAlert.title				=	'Game invite';
		    		gameinviteAlert.text				=	'Would you like to join the game ?';
		    		gameinviteAlert.buttonFlags			=	Alert.YES | Alert.NO;
		    		gameinviteAlert.defaultButtonFlag 	= 	Alert.YES;
					gameinviteAlert.addEventListener("close",alertListener);	
	      			PopUpManager.addPopUp(gameinviteAlert, this, false);	
					PopUpManager.centerPopUp(gameinviteAlert);
					gamealerttimer.reset();
					gamealerttimer.start();
	      		}
	      		else{
	      			
	      		//	Alert.show('no invite' + status);
	      			minuteTimer.stop();
					minuteTimer.reset();
	      			minuteTimer.start();
 	      		}
	      		checkinvite.clearResult();
	    	}
	      	catch(e:Error){
	      		trace("fire in the hole !! ");
	      		minuteTimer.stop();
				minuteTimer.reset();
	      		minuteTimer.start(); 
	      	}
	     }
		 
		 private var invite_opponent:String	=	new String();
		 
		 /** close event listener for alert box  */
	     private function alertListener(eventObj:CloseEvent):void {      
	     	var variables:URLVariables = new URLVariables();
			if (eventObj.detail==Alert.YES) {						   
				variables.action   		= 	"2NDPLAYER";
				variables.userid  		= 	Univ_LoginId;
				variables.opponentid	= 	invite_opponent;
					var t_info:Object = new Object();
	      				t_info.action		= "updatelog";	
	      				t_info.actiontype 	= 'GamePlay';
	      				t_info.takenby 		=  Univ_LoginId1 ;  
	      				t_info.takenon 		= invite_opponent;
	      				send_log_msg( t_info );	     							

				goingtoPlay();
				currentState 			= 	'gamemodule';
				pg_gamemodule.connectgame.request 	= variables;				
				pg_gamemodule.connectgame.send();				
 				pg_gamemodule.init();
 				pg_gamemodule.routineTimer.start();
 				alert = Alert.show(plzWWVLM,messgM);	
 				setTimeout(hideAlert, delay);	
 				//CursorManager.setBusyCursor();		
			}		
			else if(eventObj.detail==Alert.NO) {	
				variables.message   = "NOT ACCEPTED";
				variables.user  	= Univ_LoginId;
				setinvite.url 		= ServerPath + graphData + "setinvite.php";	  
				setinvite.request 	= variables;				
				setinvite.send();			
 				minuteTimer.stop();
				minuteTimer.reset();
				minuteTimer.start();
 			}
         }
         
         /** if the user says YES, for invite, then we call this function, 
         * 		make all the necessary measures   
         * 	 based on the current state of the user, we either stop the video or remove the profiles
         * 	 of people	*/
         public function goingtoPlay():void{
         	togglebuttonbar1.enabled= 	false;
 			userplaying 			=	true;
 			adminenter				=	false;
 			gamealerttimer.stop();
			gamealerttimer.reset();
			switch ( currentState ){
				case 'ProfileView':
					profilebox.removeAllChildren();		
	    			for each ( var uiCom:UIComponent in Vbox_Refs )
	    				PopUpManager.removePopUp(uiCom);
					Vbox_Refs 	= new Array();
					Vbox_Ids 	= new Array();	
					break;
				default:	// new_ui and fullscreen
				//	stopVideo();  // To be fixed
					break;
					
			}
         }
         private function hideAlert():void {
                PopUpManager.removePopUp(alert);
            }