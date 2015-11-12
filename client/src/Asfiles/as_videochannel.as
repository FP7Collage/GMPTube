

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
		
		/** contains functions related to video playback, etc.	  */
		import com.adobe.flex.extras.controls.springgraph.Item;
		
		import flash.display.Loader;
		import flash.events.Event;
		import flash.net.URLVariables;
		
		import mx.collections.XMLListCollection;
		import mx.rpc.events.ResultEvent;
		import mx.rpc.http.mxml.HTTPService;
		
		[Bindable] public var searchResults:XMLList = new XMLList();
		
        [Bindable] public var xmldata:Object = new Object;
       [Bindable] public var xmldata_constants:Object = new Object;
		
		[Bindable] private var ytpDemoVid:String;
        
        [Bindable] public  var servername:String ;
	    [Bindable] public var ServerPath:String ;
	    [Bindable] public var graphData:String = 'GraphData/';
		public var YouTubeloader:Loader = new Loader();;
		
		public var playingvideo:Item			=	new Item();
		[Bindable] public var Curr_Video:XML 	= 	new XML();
					
		 private function showVideoScreen():void{
		 	
		 	
		 	
		 	if (! isLoggedIn()){
 			      forceLogin();
 			      return;
 			}
				pausevideo();
				currentState = 'videoupload';
			}
		
		/** function is called, on clicking datagrid in tabnavigator
		 * the Curr_Video variable contains info abt a video, when we post a comment
		 by default..it goes to this video. its name is set as title of video player.	*/
		public function changeCurVideo( category:String ):void{
			switch( category ){
				case videoCategory_CDLabel:
					Curr_Video = new XML ( generalVideos.selectedItem );
				break;
				case videoCategory_CDOppLabel:
					Curr_Video =  new XML (oppurtunityVideos.selectedItem);
				break;
				case videoCategory_CDExpertsLabel:
					Curr_Video =  new XML (expertsVideos.selectedItem );
				case label_fourth_videoCategory:
						Curr_Video =  new XML (fourth_videoCategory_id.selectedItem );
				case label_fifth_videoCategory:
						Curr_Video =  new XML (fifth_videoCategory_id.selectedItem );
				case label_sixth_videoCategory:
						Curr_Video =  new XML (sixth_videoCategory_id.selectedItem );
						
						
				break;
			}
			
			// setting some of the data for the databinding
		
			
			
			
		//	comments_window.PutComments ( Curr_Video.@comments.toString() );
		//	commentspanel.title	=	Curr_Video.@name.toString();
			//comments_window.PutComments ( Curr_Video.@id.toString(), Curr_Video.@comments.toString() );
			// 	only if no video is being played, streamed or in pause state
			//	we change the title of panel2 ( it has the video player inside it )
/* 			if 	( innovideoStatus == 'stopped') 
				panel2.title 		= 	Curr_Video.@name.toString();
 */			return;
		}
		
		
		private function setTagsDesc():void{
			
			
		}
		
				
			
			/** 	FUNCTIONS RELATED TO VIDEO PLAY, PAUSE, STOP,
			 * 		LOAD FROM YOUTUBE 		
			 * 														STARTS	**/
			
			
		private function handlerLoaderInit (event:Event):void	{
				try{
					var urlVars:URLVariables = new URLVariables ();
					urlVars.decode (YouTubeloader.contentLoaderInfo.url.split("?")[1]);
					constructFLVURL (urlVars.video_id, urlVars.t);
					
				}
				catch(e:Error){
					trace('handlerLoaderInit:Unable to play the video. Contact the administrator','Error !');
					// do nothing
				}	
			}
			
			private function constructFLVURL (video_id:String, t:String):void{
				/*
				var str:String = "http://www.youtube.com/get_video.php?";
				str += "video_id=" + video_id;
				//str += "&t=" + t;
				str += '&t=' + 'OEgsToPDskKMQele42sa9dvzYdaUzzGO';
				return str;
				*/
				var serv:HTTPService = new HTTPService();
				serv.url = ServerPath + graphData + "youtubeurl.php?vid=" + video_id;
				serv.resultFormat = 'text';
				serv.addEventListener(ResultEvent.RESULT, handleYTresponse);
				serv.send();
			}
			
			private function handleYTresponse(evt:ResultEvent):void {
				var url:String = evt.result.toString();
				playVideo(url);
				YouTubeloader.unload();
			}
			
			public function getCurrentPlayMode():String {
				switch(playerStack.selectedIndex) {
					case 0: return 'FMS';
					case 1: return 'SWF';
					case 2: return 'YTB';
				}
				return '';
			}
			
			public function setActivePlayer(mode:String):void {
				switch(mode) {
					case 'FMS':  playerStack.selectedIndex = 0; break;
					case 'SWF':  playerStack.selectedIndex = 1; break;
					case 'YTB':  playerStack.selectedIndex = 2; break;
					case 'FMSG':gplayerStack.selectedIndex = 0; break;
					case 'SWFG':gplayerStack.selectedIndex = 1; break;
					case 'YTBG':gplayerStack.selectedIndex = 2; break;
				}
			}
			
			public function updateVolume(value:Number):void {
				switch(getCurrentPlayMode()) {
					case 'FMS':
						vPlayer.volume = value;
						break;
					case 'YTB':
				//		ytp.volume = value*100;
						ytp.volume = value;
						break;
				}
			}
			
			
		
		
			public function checkClick(r:TimerEvent):void{
				trace("Check Click");
				trace("video id for play" + passed_videoID);
				startLoadingSingleClick(passed_videoID);
			//	alert.show("Check click");
			}
			
		
			
			
			
			
		
			/** whenever the user clicks on a thumbnail, this function is called  */
			
			public function startLoading(id:String):void{
				
				
				
				bigplaybutton.visible=false;panel2.visible=true;
				innovideoStatus		= 	'processing';
				clearPlayers();
				playingvideo		=	fullGraph.find( id );
				panel2.title 		= 	playingvideo.data.@name.toString();
				
				// FOR VIEWING AUTO MATICALLY
				//Curr_Video = new XML (playingvideo);
				//trace(Curr_Video);					
		
				var videourl:String = 	playingvideo.data.@url.toString();
				
				setActivePlayer(playingvideo.data.@source.toString());
				
				if ( playingvideo.data.@source.toString() == 'FMS'){
				}
				else if(playingvideo.data.@source.toString() == 'SWF')
				{
				}
				else{
				}
				
				// start playing the video
				playVideo(videourl);
				
				// get the comments
				//comments_window.PutComments ( playingvideo.data.@comments.toString() );
				commentspanel.title	=	playingvideo.data.@name.toString();
				var variables:URLVariables  = 	new URLVariables();
				variables.videoId  			= 	id; 				
				comments_window.get_commentsxml.url = ServerPath + graphData + "getVideoComments.php";	  
				comments_window.get_commentsxml.request = variables;
				comments_window.get_commentsxml.send();
				
				
	      		//to create the relation ship has seen as soon as the video starts playing
	      /*		
	      		var http_ser:HTTPService	= new HTTPService();
				var variables:URLVariables 	= new URLVariables();
				variables.action	=   "addedge";		
				variables.edgename  =   "Has seen";				
				variables.fromID 	=   Univ_LoginId ;
				variables.toID 		=   playingvideo.data.@id.toString();	
				variables.status = 1;  // for start
				http_ser.url 		= 	ServerPath + graphData + "change.php";					
				http_ser.method 	= 	'POST';
				http_ser.request 	= 	variables;									 
				http_ser.send();
				
			*/	
				
				// to refresh so that has seen is updated
				//onAppCreationComplete();
	      		
	    /* 	// store the action logs	
				var t_info:Object = new Object();
	      		t_info.action		= "updatelog";
	      		t_info.actiontype 	= 'VideoStart';
	      		t_info.takenby 		=  Univ_LoginId1 ;  
	      		t_info.takenon 		= id;
	      		send_log_msg( t_info );
	    */  	
	      		
			//	playVideo(videourl);
			}
			
			
			//new function for loading and then pausing
			
			public function startLoadingSingleClick(id:String):void{
				innovideoStatus		= 	'processing';
				clearPlayers();
				playingvideo		=	fullGraph.find( id );
				panel2.title 		= 	playingvideo.data.@name.toString();
					
		
				var videourl:String = 	playingvideo.data.@url.toString();				
				setActivePlayer(playingvideo.data.@source.toString());
				
				if ( playingvideo.data.@source.toString() == 'FMS'){					
				}
				else if(playingvideo.data.@source.toString() == 'SWF')
				{				
  				}
				else{
					}
				
				// load the play video on the video window
				playVideo(videourl);
				//bigplaybutton.height=playerStack.height;
				//bigplaybutton.width=playerStack.width;
				bigplaybutton.visible=true;
				//panel2.visible=false;
				
				// get the comments
				//comments_window.PutComments ( playingvideo.data.@comments.toString() );
				commentspanel.title	=	playingvideo.data.@name.toString();
				var variables:URLVariables  = 	new URLVariables();
				variables.videoId  			= 	id; 				
				comments_window.get_commentsxml.url = ServerPath + graphData + "getVideoComments.php";	  
				comments_window.get_commentsxml.request = variables;
				comments_window.get_commentsxml.send();
				
				pausevideonew();
				
			/*
			   can log the details later
				// store the action logs	
				var t_info:Object = new Object();
	      		t_info.action		= "updatelog";
	      		t_info.actiontype 	= 'VideoStart';
	      		t_info.takenby 		=  Univ_LoginId1 ;  
	      		t_info.takenon 		= id;
	      		send_log_msg( t_info );
	      		
	      		*/    	
	      			      		
			//	playVideo(videourl);
			}
			
			
			
			private function clearPlayers(except:String = ''):void {
				if(except != 'FMS') {
					if ((innovideoStatus == 'playing' ) || (innovideoStatus == 'paused' )){
						vPlayer.stop();
						vPlayer.close();
					}
                    vPlayer.source=null;
				}
				if(except != 'SWF') {
					sPlayer.source = "";
					sPlayer.load(null);
					SoundMixer.stopAll();
				}
				if(except != 'YTB') {
					ytp.stopVideo();
				//	ytp.clearVideo();  // has been commented recently
				}
			}
			
			public function playVideo(url:String):void{
					created = false;  // has seen is not created for new video
						
				try{
				switch(playingvideo.data.@source.toString()) {
					case 'FMS':
					// stop the player in the beginnnig
							
					
						if ( (url == '') || ( url == null) ) {
							innovideoStatus = "stopped";
							CursorManager.removeBusyCursor();
							return;
						} 
						
						if(useFMS=='false') {
							if(url.indexOf("youtube")<0) {
								var my_arrayfilename:Array=url.split("/");
								var mediafilename:String=my_arrayfilename[my_arrayfilename.length - 1];
								
								if(mediafilename.indexOf(".")<0) {
									url="http://" + ServerNameConstant + "/" + VideoPath + "/" + mediafilename + ".flv";
								}else{
									url="http://" + ServerNameConstant + "/" + VideoPath + "/" + mediafilename ;
								}
								
							}
						}
						
						//Application.application.uname.text=url;
						vPlayer.source 		= 	url;
						//vPlayer.bufferTime = vPlayer.totalTime / 20; // 20 is selected as a smart number. it is not to be a magic number.
						panel2.title 		= 	playingvideo.data.@name.toString();
						innovideoStatus	 	= 	'playing';
						showPlayBtn = false;
						vPlayer.play();
		  				break;
		  			case 'SWF':
						//sPlayer.source = url;
	                    //panel2.title = playingvideo.data.@name.toString();
	                    var webPageURL:URLRequest = new URLRequest( url);
	  		    		navigateToURL(webPageURL, '_blank');
		  				break;
		  			case 'YTB':
						ytp.stopVideo();
						ytp.loadVideoById(url.split('/v/')[1]);
						panel2.title = playingvideo.data.@name.toString();
						innovideoStatus = 'playing';
						showPlayBtn = false;
						break;
				}	
					
				}	catch(e:Error){
					trace('PlayVideo error:' + e.message);
					vPlayer.close();
				  
				  //innovideoStatus  = 	'stopped';
					//stopVideo();
			      }
				
				highLightVideo();
				
				CursorManager.removeBusyCursor();
				rated = false;
			}
			
			private function setSelectedItem(dGrid:Object, hvId:String):void
			  {
			  	var vId : String;
				var gData:XMLListCollection = dGrid.dataProvider;				
				//for(var i:Number=0; i < gData.length; i++)
		//		{					
			//		var thisObj:Object = Item(gData.getItemAt(i));
			//		vId = thisObj.data.@name.toString();
			      var i =0;
			      for each (var node : XML in gData){								
					vId = node.@name.toString();
					if(vId == hvId)
					{
					dGrid.selectedIndex = i;
					//sometimes scrollToIndex doesnt work if validateNow() not done
					dGrid.validateNow();
					dGrid.scrollToIndex(i);
					trace("I" + i);
					return;
					}
					i = i+1;
				}
			}
			
			public function highLightVideo ():void{
				var channelName:String;
				var hvId: String;
				if(playingvideo != null){					
					channelName = playingvideo.data.@category.toString();
					hvId = 	playingvideo.data.@name.toString();
					if(channelName == videoCategory_CDLabel){
						tabnavigator1.selectedIndex =0;											
						setSelectedItem(generalVideos,hvId);	
					}else if(channelName == videoCategory_CDOppLabel){
						tabnavigator1.selectedIndex =1;					
						setSelectedItem(oppurtunityVideos,hvId);						
					}else if(channelName == videoCategory_CDExpertsLabel){
						tabnavigator1.selectedIndex =2;					
						setSelectedItem(expertsVideos,hvId);
					}else if(channelName == label_fourth_videoCategory){
						tabnavigator1.selectedIndex =3;					
						setSelectedItem(fourth_videoCategory_id,hvId);
					}else if(channelName == label_fifth_videoCategory){
						tabnavigator1.selectedIndex =4;						
						setSelectedItem(fifth_videoCategory_id,hvId);
					}else if(channelName == label_sixth_videoCategory){
						tabnavigator1.selectedIndex =5;						
						setSelectedItem(sixth_videoCategory_id,hvId);
					}									
				}
				return;
				
			}
			
			
			
			public function stopVideo():void{
				if ((innovideoStatus == 'playing' ) || (innovideoStatus == 'paused' )){
					switch(getCurrentPlayMode()) {
						case 'FMS':
							vPlayer.stop();
							vPlayer.close();
							break;
						case 'SWF':
							sPlayer.source = '';
							sPlayer.load(null);
							break;
						case 'YTB':
							ytp.stopVideo();
							//ytp.clearVideo();
							break;
					}
					/*
					if ( YouTubeloader != null ){
						YouTubeloader.unload();
						YouTubeloader =  null;
						YouTubeloader = new Loader();
					}
					*/
					innovideoStatus 	= 	'stopped';
					showPlayBtn = true;
					playheadtext.text	=	'0:00-0:00';
					vSlider.value		=	0;
				}
			}
			
			
			//pausing the videos after single click
			
			public function pausevideonew():void{
				if (innovideoStatus == 'playing'){
					innovideoStatus		= 'paused';
					showPlayBtn = true;
				}
				if(vPlayer!= null && getCurrentPlayMode()!='YTB')//&& ytp==null)//&& vPlayer.playing) 
				vPlayer.pause();
				if(gameVidPlayer != null && gameVidPlayer.playing) gameVidPlayer.pause();
				if(ytp != null)// && ytp.isPauseable) 
				
				ytp.pauseVideo();
				return;
			}
			
			
			public function pausevideo():void{
				if (innovideoStatus == 'playing'){
					innovideoStatus		= 'paused';
					showPlayBtn = true;
				}
				if(vPlayer!= null && vPlayer.playing) vPlayer.pause();
				if(gameVidPlayer != null && gameVidPlayer.playing) gameVidPlayer.pause();
				if(ytp != null && ytp.isPauseable) ytp.pauseVideo();
				return;
			}
			
			public function prePlay():void{
				
				switch(innovideoStatus) {
					case 'stopped':
						if(playingvideo==null || playingvideo.data==null)
							startLoading( Curr_Video.@id.toString() );
						else
							startLoading(playingvideo.data.@id.toString());
						break;
					case 'paused':
						switch(getCurrentPlayMode()) {
							case 'FMS':
								vPlayer.play();
								break;
							case 'YTB':
								ytp.playVideo();
								//ytp.pauseVideo();
								break;
						}
						innovideoStatus = 'playing';
						showPlayBtn = false;
						break;
					case 'processing':
						break;
				}
				/*
				switch(innovideoStatus){
					case 'stopped':	
					break;
						vPlayer.pause();
						innovideoStatus		= 'paused';	
						vPlay_btn.visible = true;
					case 'paused':	
						vPlayer.play();
						innovideoStatus		= 	'playing';
						vPlay_btn.visible 	= 	false;
						return;
					case 'processing':
						if ( YouTubeloader != null ){
							YouTubeloader.unload();	
							YouTubeloader =  new Loader();
						}	
					break;				
				}
				if(playingvideo==null || playingvideo.data==null){
					startLoading( Curr_Video.@id.toString() );			
				}	
				else{
					startLoading(playingvideo.data.@id.toString());
				}		
				return;
				*/
			}
			

		/* public function playheadUpdate(evt:VideoEvent):void{		
		} */
		
		public function updatePlayheadTime(t:int):void{
			vPlayer.playheadTime = t;
		}
		
		
		
		private function formatPositionToolTip(value:int):String{
		//  do only handle minuts.	
			var result:String = (value % 60).toString();
        	if (result.length == 1){
            	result = Math.floor(value / 60).toString() + ":0" + result;
        	} 
        	else {
            	result = Math.floor(value / 60).toString() + ":" + result;
        	}
        	return result;
		}
		
		private function formatedYouTubeTime(value:Number):String{
						value = Math.floor(value / 1000);
			var result:String = (value % 60).toString();
        	if (result.length == 1){
            	result = Math.floor(value / 60).toString() + ":0" + result;
        	} 
        	else {
            	result = Math.floor(value / 60).toString() + ":" + result;
        	}
        	return result; 

			
			}	
	
	
	    
		// when a video gets over, both in gamemodule and new_ui state, this function is called.
		// if currentState is new_ui, then we add a has seen edge to our Xml data.	    	
		private function VideoOver(evt:Event):void{
			var tot:Number	= 0;
			var head:Number	= 0;

			switch(getCurrentPlayMode()) {
				case 'FMS':
					tot 	= vPlayer.totalTime;
					head 	= vPlayer.playheadTime;
					break;
				case 'YTB':
					//tot = ytp.duration;
					//head = ytp.currentTime;
					tot = ytp.duration/1000;
					head = ytp.currentTime/1000;
					
					
					
			}
			
			if( (head > 5) && !created){
				
				//to create the relation ship has seen as soon as the video starts playing
	      		created = true;
	      		var http_ser:HTTPService	= new HTTPService();
				var variables:URLVariables 	= new URLVariables();
				variables.action	=   "addedge";		
				variables.edgename  =   "Has seen";				
				variables.fromID 	=   Univ_LoginId ;
				variables.toID 		=   playingvideo.data.@id.toString();	
				variables.status = 1;  // for start
				http_ser.url 		= 	ServerPath + graphData + "change.php";					
				http_ser.method 	= 	'POST';
				http_ser.request 	= 	variables;									 
				http_ser.send();
				
				
			}
			
			if ( (tot>0 ) && (head>0 ) && ( tot-head < 5 ) && !rated){
			//	innovideoStatus 	= 'empty';
			/// added to fix the the play button after the video player stops playing the last video
				innovideoStatus 	= 'stopped';
				showPlayBtn = true;
				addChild(ratingWindow);	
				ratingWindow.visible = true;
				
				rated = true;
				
								
				var t_info:Object = new Object();
	      		t_info.action		= "updatelog";
	      		t_info.actiontype 	= 'VideoEnd';
	      		t_info.takenby 		=  Univ_LoginId1;
	      		t_info.takenon 		= playingvideo.data.@id.toString();
	      		send_log_msg( t_info );	     							

				
				// you are calling has seen more than once.. in the starting and also in the end
				
		/*	
		    since u have called it after 5 seconds so u do not need to call now
		    
			var http_ser:HTTPService	= new HTTPService();
				var variables:URLVariables 	= new URLVariables();
				variables.action	=   "addedge";		
				variables.edgename  =   "Has seen";				
				variables.fromID 	=   Univ_LoginId ;
				variables.toID 		=   playingvideo.data.@id.toString();
				variables.status = 3;  // for end	
				http_ser.url 		= 	ServerPath + graphData + "change.php";	
				http_ser.method 	= 	'POST';
				http_ser.request 	= 	variables;									 
				http_ser.send();
				
		*/		
				
				//rated = true;
				
				// this line have been added to fix the busy curson after the rating
				CursorManager.removeBusyCursor();
			} 
			return;
		}
         
         
			/**  innovideoStatus: this variable changes based on the state of vPlayer:VideoDisplay
			 *   It can take one of these values   
			 * 		playing		-	when a video is being played
			 * 		processing	-	when play button or thumbnail is clicked, and the app is trying to load 
			 * 						the flv file
			 * 		paused		-	when a video is paused
			 * 		stopped		-	when no video is currently played/paused.
			 *  **/	 
			public var innovideoStatus:String="stopped";
				


////////////////////// Functions related to the demo of the Game  //////////////////////////////////	

	
	private function Gamepass():void{
		if (  btnPass.label == 'Start' ){
			 countDownClock.init();
			 txtPassMsg.visible = false;
			 gameplayer_playbtn.visible = false;
			 gameplayer_pausebtn.visible = true;
			 
			 btnPass.label = 'Pass';	
			 btnPass.toolTip=displayNextVM;
			 btnPass.enabled = true;
			 btnFlag.enabled = true;
			 userGuess.enabled = true;
			 guessesList.enabled = true;
			 			 
			 //startLoading('playnew');
			 startLoadingDemo(demoVideoId);
			
			
			 			 
			 gamePlayerPanel.title = Curr_Video.name;
		}
		else{
			//game play is on
			 if ( gameVidPlayer.playing ){
				gameVidPlayer.stop();	
				YouTubeloader.unload();
				gamePlayerPanel.title = 'Video'; 
				gameplayer_playbtn.visible = true;
			 	gameplayer_pausebtn.visible = false;
			 }
			 countDownClock.ticker.reset();
			 countDownClock.ticker.stop();
			 countDownClock.text = '2:30';
			 countDownClock.minutes = 2;
			 countDownClock.seconds = 30;			 
			 userGuess.text = '';
			 btnPass.label = 'Start';	
			 btnPass.toolTip=toolTip_startVideo;		 
			 guessesList.text = guessListText;
			 txtPassMsg.text= 'YOUR PARTNER WANTS A PASS !!';
			 txtPassMsg.visible = true;
			 
			 btnFlag.enabled = false;
			 userGuess.enabled = false;
			 guessesList.enabled = false;
		}
		
		
		 return;
	}

		
	public function startLoadingDemo (id:String):void
			{
				userGuess.addEventListener(KeyboardEvent.KEY_DOWN, guessHandlerDemo );
				stopVideoDemo();
			   //CursorManager.setBusyCursor();	
				//playingvideo		=	fullGraph.find(Curr_Video.@id.toString());
				//panel2.title 		= 	Curr_Video.@name.toString();
				playingvideo		=	fullGraph.find( id );
				//panel2.title 		= 	playingvideo.data.@name.toString();
				var videourl:String = 	playingvideo.data.@url.toString();
				
				if ( playingvideo.data.@source.toString() == 'FMS'){
					/*
					if ( useFMS == 'true' && fms_nc.connected == false ){
						Alert.show("Flash media server is disconnected. Click YES to connect", "Alert",Alert.YES | Alert.NO, this,fms_alertListener, null, Alert.YES);
						return;
					}
					*/
					setActivePlayer('FMSG');
				}
				else{
					setActivePlayer('YTBG');
				}
				playVideoDemo(videourl);
				innovideoStatus		= 	'processing';
			}

		
		private function handlerLoaderInitDemo (event:Event):void	{
				try{
					var urlVars:URLVariables = new URLVariables ();
					urlVars.decode (YouTubeloader.contentLoaderInfo.url.split("?")[1]);
					constructFLVURL (urlVars.video_id, urlVars.t);
					/*
					var flvURL:String = constructFLVURL (urlVars.video_id, urlVars.t);
					playVideoDemo (flvURL);
					YouTubeloader.unload();
					*/ 
				}
				catch(e:Error){
					trace('handlerLoaderInit:Unable to play the video. Contact the administrator','Error !');
					// do nothing
				}	
				
			}
			
		private function playVideoDemo(url:String):void{
				if ( (url == '') || ( url == null) ) {
					innovideoStatus = "stopped";
					CursorManager.removeBusyCursor();	
					return;
				} 
				
				if(useFMS=='false') {
					if(url.indexOf("youtube")<0) {
						var my_arrayfilename:Array=url.split("/");
						var mediafilename:String=my_arrayfilename[my_arrayfilename.length - 1];
						
							if(mediafilename.indexOf(".")<0) {
									url="http://" + ServerNameConstant + "/" + VideoPath + "/" + mediafilename + ".flv";
								}else{
									url="http://" + ServerNameConstant + "/" + VideoPath + "/" + mediafilename ;
								}
							
											
						
					}
				}
				
				if(playingvideo.data.@source.toString() == 'FMS'){
					gameVidPlayer.source 		= 	url;
					//vPlayer.bufferTime = vPlayer.totalTime / 20; // 20 is selected as a smart number. it is not to be a magic number.
					gamePlayerPanel.title 		= 	playingvideo.data.@name.toString();	
					innovideoStatus	 	= 	'playing';
					gameplayer_playbtn.visible 	= 	false;
					gameplayer_pausebtn.visible 	= 	true;
					gameVidPlayer.play();
				} else if(playingvideo.data.@source.toString() == 'YTB'){
					ytpGame.stopVideo();
					ytpDemoVid = url.split('/v/')[1];
					ytpGame.loadVideoById(ytpDemoVid);
					//panel2.title = playingvideo.data.@name.toString();
					innovideoStatus = 'playing';
					gameplayer_playbtn.visible 	= 	false;
					gameplayer_pausebtn.visible 	= 	true;
				}
				CursorManager.removeBusyCursor();	
								  																
				return;		
			}
			
	public function stopVideoDemo():void{
				if ( ( innovideoStatus == 'playing' ) || (innovideoStatus == 'paused' )){
					switch(gplayerStack.selectedIndex) {
					case 0:
						gameVidPlayer.stop();
						gameVidPlayer.close();
						innovideoStatus 	= 	'stopped';
						gameplayer_playbtn.visible 	= 	true;
						gameplayer_pausebtn.visible 	= 	false;
						playheadtext.text	=	'0:00-0:00';		
						vSlider.value		=	0;
					case 1:
						break;
					case 2:
						ytpGame.stopVideo();
						innovideoStatus = 'stopped';
						gameplayer_playbtn.visible 	= 	true;
						gameplayer_pausebtn.visible 	= 	false;
						
					}	
				}
		
			}
			
		
			
			public function pausevideoDemo():void{
				if ( ( innovideoStatus == 'playing' ) || (vPlayer.playing) ){
					gameVidPlayer.pause();
					innovideoStatus		= 'paused';	
					gameplayer_playbtn.visible 	= 	true;
					gameplayer_pausebtn.visible 	= 	false;
				}
				return;
			}	
					
			
	private function check_for_taboowords(userinput:String):Boolean{
		
		var i:int;
		for(i=0;i<demo_taboowords_array.length;i++)
		{
			if(demo_taboowords_array[i].toString().toUpperCase()==userinput.toUpperCase())
			   return true;
			
		}
		return false;
	}		
		
	// when user types a word and clicks Enter in the text box, the keyword is appended
	// to the Guess List ( text area )
		private function guessHandlerDemo(event:KeyboardEvent):void {
	
		      if ( event.keyCode == 13)
               	if ( userGuess.text != ''){
               		//if ( (userGuess.text == 'French') || (userGuess.text == 'Toast') ){
            		if ( check_for_taboowords(userGuess.text) ){
            			txtPassMsg.text= 'THATS A TABOO WORD !!';
            			userGuess.text = '';
            			txtPassMsg.visible = true;           			
            		}
            		else{
            			guessesList.text = guessesList.text + '\n' + userGuess.text ;
            			userGuess.text = '';
            			txtPassMsg.visible = false;       
            		}
            	}
       }
	
	
	// loads a video and the starts the timer 
	private function videoTimer():void{
		 countDownClock.init();
		 txtPassMsg.visible = false;
	//	 btnNext.enabled = false;
		 btnPass.enabled = true;
		 btnFlag.enabled = true;
		 userGuess.enabled = true;
		 guessesList.enabled = true;
	}
			
			public function updateGameVolume(value:Number):void {
				switch(gplayerStack.selectedIndex) {
					case 0:
						gameVidPlayer.volume = value;
						break;
					case 'YTB':   /// To be fixed for the volume
						ytpGame.volume = value*100;
						break;
				}
			}
		
	
	private function GameVideo_Play(action:String):void{
		if (  btnPass.label == 'Start' )
			return;
		if(action == 'play') {
			switch(gplayerStack.selectedIndex) {
				case 0:
					gameplayer_playbtn.visible = false;
					gameplayer_pausebtn.visible = true;
					gameVidPlayer.pause();
					break;
				case 1:
					break;
				case 2:
					gameplayer_playbtn.visible = false;
					gameplayer_pausebtn.visible = true;
					if(ytpGame.isPlayable)
						ytpGame.playVideo();
					else
						ytpGame.loadVideoById(ytpDemoVid);
					break;
			}
		} else if(action == 'pause') {
			switch(gplayerStack.selectedIndex) {
				case 0:
					gameplayer_playbtn.visible = true;
					gameplayer_pausebtn.visible = false;
					gameVidPlayer.play();
					break;
				case 1:
					break;
				case 2:
					gameplayer_playbtn.visible = true;
					gameplayer_pausebtn.visible = false;
					if(ytpGame.isPauseable)
						ytpGame.pauseVideo();
					else
						ytpGame.stopVideo();
					break;
			}
		}
	}

 
 private function videoOverDemo():void{
			if ( currentState == 'gamemoduledemo'){
				 gamePlayerPanel.title = 'Video'; 
				 gameplayer_playbtn.visible = true;
			 	 gameplayer_pausebtn.visible = false;
				 countDownClock.ticker.reset();
				 countDownClock.ticker.stop();
				 countDownClock.text = '2:30';
				 countDownClock.minutes = 2;
				 countDownClock.seconds = 30;			 
				 userGuess.text = '';
				 btnPass.label = 'Start';	
				 btnPass.toolTip=toolTip_startVideo;		 
				 guessesList.text = guessListText;
				 txtPassMsg.text= passMessageText;
				 txtPassMsg.visible = true;
				 
				 btnFlag.enabled = false;
				 userGuess.enabled = false;
				 guessesList.enabled = false;
			}
			else{
				innovideoStatus = 'stopped';
				showPlayBtn = true;
			}
		}
		
 