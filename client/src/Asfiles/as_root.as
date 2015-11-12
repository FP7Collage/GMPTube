

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




//V0.2

		/**
		 *  Here i will give a short note of all the .as ( actionscript ) files, we have
		 *   
		 * 
		 * as_root.as
		 * 		- This file, contains the most important, basic functions of the application.
		 * 		  the login, logoff functions, loading the network, connecting to fms, events for 
		 * 		  switching to various states are handled here.
		 * 
		 * as_admin.as
		 * 		- contains functions related to administrator features. the delete video 
		 * 		  and delete comment functions are handled there 
		 *		- deleting a tag, user are handled in adminpanel.mxml form
		 * 
		 * as_game.as
		 * 		- handles few functions related to starting the game
		 *  	- major part of game related functions are in form_gamepage.mxml
		 * 
		 * as_videochannel.as
		 * 		- all functions related to video ( playing, pause, stop, video getting over ),
		 * 			their event listeners 
		 * as_variables.as
		 * 		- here we import all classes, needed for application and few core variables 
		 * 		  are declared 
		 *
		 * as_visualization.as
		 * 		- contains all the functions related to network part, the search functionality, history,
		 * 			double click, right click, everythin.
		 * 		
		 * 
		 *  */
		 
		 
		 
		 
		
		
		
	// ActionScript file
		import com.adobe.flex.extras.controls.springgraph.Graph;
		import com.adobe.flex.extras.controls.springgraph.Item;
		import com.util.QueryString;
		
		import component.chatWindow;
		
		import flash.display.DisplayObject;
		import flash.events.Event;
		import flash.events.MouseEvent;
		import flash.events.TimerEvent;
		import flash.net.URLRequest;
		import flash.net.URLVariables;
		import flash.net.navigateToURL;
		import flash.utils.Timer;
		
		import mx.collections.ArrayCollection;
		import mx.containers.TitleWindow;
		import mx.controls.Alert;
		import mx.controls.Label;
		import mx.controls.LinkButton;
		import mx.core.Application;
		import mx.core.UIComponent;
		import mx.events.FlexEvent;
		import mx.managers.PopUpManager;
		import mx.rpc.events.ResultEvent;
		import mx.utils.ObjectUtil;
 		
		private var qs:QueryString;	
		public var loginWindow:TitleWindow;
		public var plWindow:TitleWindow;
		
		public var styleSheet:StyleSheet;
		
		public var startVideoTimer:Timer = new Timer(5000, 1);	
		
		
		
				
		public function openPublicLink(vdata: Object):void{	        	
	        	 plWindow = TitleWindow(PopUpManager.createPopUp(this, MyPublicLinkForm, true));
             //     plWindow.title="Copy Direct Link to Video";
		          plWindow.setStyle("borderAlpha", 0.9);
		          PopUpManager.centerPopUp(plWindow);
	        }
	        
	        
		
		public function forceLogin():void{
		      loginWindow = TitleWindow(PopUpManager.createPopUp(this, MyLoginForm, true));
                  loginWindow.title="Enter Login Information";
		          loginWindow.setStyle("borderAlpha", 0.9);
		          PopUpManager.centerPopUp(loginWindow);
		          
		}
		
		public function isLoggedIn():Boolean{
			if(Univ_LoginId == "anonymous@gmail.com"){
				return false;
			}else{
				return true;
			}
			
		}
		
		
		
		
				   

// start of sorting date string functions, used for sorting the videos by submitted date //
            private function date_sortCompareFunc(itemA:Object, itemB:Object):int {
                /* Date.parse() returns an int, but
                   ObjectUtil.dateCompare() expects two
                   Date objects, so convert String to
                   int to Date. */
                var dateA:Date = new Date(Date.parse(itemA.dob));
                var dateB:Date = new Date(Date.parse(itemB.dob));
                return ObjectUtil.dateCompare(dateA, dateB);
            }

            private function date_dataTipFunc(item:Object):String {
           //     return dateFormatter.format(item.dob);
              return "";
            }

// end of sorting date functions		
		
		
		

	/**	the displayprofile() function in form_detailedprofile.mxml always reads this variable
		 when it is called. Then it shows the profile in that screen.
		 why it is done like this: sometimes when the user clicks detailed profile from network
		 visualizer, then before all the text boxes are created, the function tries to access
		 their text property., so, we get an error. **/		
		[Bindable]public var detailedprofileId:String	=	new String();
		
		[Bindable] public var isEditState:Boolean=false;
		
		/** stores the list of interests when the application is started **/
		[Bindable]public var interestslist:Array		=	new Array();
		//[Bindable] public var app:Tentube=Tentube(Application.application);
		
		/**This function puts the Related Links and the Attachments of the current
		 * video into their respective components in the client ui. 
		*/
		public function putLinks():void {
			/**Get the links from the data of the current video*/
			var extLinks:String = playingvideo.data.@externalLinks.toString();
			var docLinks:String = playingvideo.data.@docLinks.toString();
			
			/**Split the links' string to get the links as arrays*/
			var extLinksArray:Array = extLinks.length > 0 ? extLinks.split("\r"): new Array();
			var docLinksArray:Array = docLinks.length > 0 ? docLinks.split("|") : new Array();
			
			var j:int; //temp variable used for looping
			
			/**Clear the UI component that displays the Related Material*/
		    linksVBox.removeAllChildren();
		    
		    /**Set title for links
		    * 'Links:' if there are one or more links
		    * 'No Links Attached' otherwise
		    */
		    
		    var makeRed : Boolean = false;
		    
		    var label_links:Label=new Label();
		    if(extLinksArray.length > 0){
				label_links.text=linksM;
				//linksVBox.setStyle("color","RED");				
			//	vCRTab.getTabAt(1).setStyle("color","0xFFAAAA");
			makeRed = true;
		    }
			else{
				label_links.text=noLinksAttM;
			}
			label_links.setStyle("color","RED");
			linksVBox.addChild(label_links);
			
			/**Add the links in a loop to the UI component*/
			for(j=0;j<extLinksArray.length;j++){
				var tempLink1:LinkButton = new LinkButton();
				tempLink1.setStyle("textSelectedColor",'yellow');
				tempLink1.setStyle("textRollOverColor",'blue');
				tempLink1.setStyle("color",'black');
				tempLink1.label = extLinksArray[j];
				/**Register the click event on these linkButtons*/
				tempLink1.addEventListener(MouseEvent.CLICK,goToURL);
				linksVBox.addChild(tempLink1);
			}
			
			/**Set title for Documents
		    * 'Attached Documents:' if there are one or more links
		    * 'No Documents attached' otherwise
		    */
			var label:Label=new Label();
			if(docLinksArray.length > 0){
				//linksVBox.setStyle("color","RED");
			//	vCRTab.getTabAt(1).setStyle("color","0xFFAAAA");
		     	makeRed = true;			
				label.text=attDocM;
			}
			else{
				label.text=noDocattM;
			}
			label.setStyle("color","RED");
			linksVBox.addChild(label);
			
			if(makeRed){
				vCRTab.getTabAt(1).setStyle("color","0xFFAAAA");
			}else{
				vCRTab.getTabAt(1).setStyle("color","white");
			}
			
			/**Add the document links in a loop to the UI component*/
			for(j=0;j<docLinksArray.length;j++)
			{ 
				var tempLink2:LinkButton = new LinkButton();
				tempLink2.setStyle("textSelectedColor",'blue');
				tempLink2.setStyle("textRollOverColor",'red');
				tempLink2.setStyle("color",'black');
				tempLink2.label = docLinksArray[j];
				tempLink2.addEventListener(MouseEvent.CLICK,goToDOC);
				linksVBox.addChild(tempLink2);
			}
			/**Return*/
		}
		
		/**To open a new window and open the link link
		 */
		public function goToURL(m:MouseEvent):void {
			var lim:int = m.currentTarget.label.search("--");
			var urlStr:String ;
			if(lim==-1)
				urlStr=m.currentTarget.label;
			else
				urlStr=m.currentTarget.label.substring(lim+2);
			var webPageURL:URLRequest = new URLRequest( urlStr );
  		    navigateToURL(webPageURL, '_blank');
		}
		
		/**
		 * To open a new window and open the document link
		 */
		public function goToDOC(m:MouseEvent):void {
			var urlstr:String = ServerPath + graphData + "upload/"+m.currentTarget.label;
			var webPageURL:URLRequest = new URLRequest( urlstr );
			navigateToURL(webPageURL, '_blank');
		}
		
		
		
		
		/**
		 * This function initializes all the important variables like switches,
		 * etc mostly the ones taken from 
		 */
		public function initializeTextVariables(tubeName:String):void{
			//  var demo_taboowords_array:Array;
			var i:int;
			
			Alert.noLabel = v1041;
            Alert.yesLabel = v1040;

				
			uploadError = newxml.uploadError;
    		tubeShortName =	newxml.tubeShortName;
    		
			tubeLabel = newxml.tubeLabel;
			useFMS = newxml.useFMS;
			camera_bandwidth=newxml.camera_bandwidth;
			camera_quality=newxml.camera_quality;
			camera_fps=newxml.camera_fps;
		//	camera_width = newxml.camera_width;
		//	camera_height = newxml.camera_height;
			//newxml.camera_fps;
			if(individual_invite = (newxml.individualplayerinvite == 'true') ? true : false){
				
			}
			else{
				individual_invite=false;
			}
			
			
			individual_invite = (newxml.individualplayerinvite == 'true') ? true : false;
			
			useFirst = (newxml.logos.useFirst == 'true') ? true : false;
			useSecond = (newxml.logos.useSecond == 'true') ? true : false;
			useThird = (newxml.logos.useThird == 'true') ? true : false;
			XpertumEnabled = (newxml.xpertumEnabled == 'true') ? true : false;
			
			firstLogo=newxml.logos.firstLogo;
			secondLogo=newxml.logos.secondLogo;
			thirdLogo = newxml.logos.thirdLogo;
			firstUrl = newxml.logos.firstUrl;
			secondUrl = newxml.logos.secondUrl;
			thirdUrl = newxml.logos.thirdUrl;
			
			if(newxml.tubeShortName=="eGovTube"){				
				use4 = true;
				use5 = true;
				use6 = true;
				RIEnabled = true;
				RIUrl = newxml.RIUrl;
				ROUrl = newxml.ROUrl;
				EGOVUrl = newxml.EGOVUrl; 
				
			}
			
						
									
			/*
			if(newxml.styling != null) {
				styling = newxml.styling.toString();
				switch(styling) {
					case 'tentube':
						// TEMPORARILY BOTH STYLES ARE SAME 'HERE' ...SHARAT
						StyleManager.loadStyleDeclarations('./ui/themes/obsidian/main.swf');
						break;
					case 'innotube':
						StyleManager.loadStyleDeclarations('./ui/themes/obsidian/main.swf');
						break;
					default:
						Alert.show('|'+newxml.styling+'|', 'Unknown Styling');
				}
			} else {
				StyleManager.loadStyleDeclarations('./ui/themes/obsidian/main.swf');
				Alert.show('No Styling given. Using default', 'Styling');
				styling = '';
			}
			*/
			/*
			if(useFiat==false)
				applicationcontrolbar1.removeChildAt(1);
				
			if(useInsead==false)
				applicationcontrolbar1.removeChildAt(0);
			*/
			
			// load the labronova url if it is innotube
			if(Application.application.parameters.tubeName=="InnoTube")
			{
				laboranovaideasurl=newxml.laboranovaideasurl;
				laboranovausersurl=newxml.laboranovausersurl;
				laboranovaevalurl =newxml.laboranovaevalurl;
				laboranovarepourl =newxml.laboranovarepourl;
			}
			
			
			
			if(newxml.hasOwnProperty("EnableRegisteration")){
			       EnableRegisteration =  (newxml.EnableRegisteration == 'true') ? true : false;
			}else{
				EnableRegisteration = true;
			}
			
			if(newxml.hasOwnProperty("EnableGame")){
			       EnableGame =  (newxml.EnableGame == 'true') ? true : false;
			}else{
				EnableGame = true;
			}
			
			if(newxml.hasOwnProperty("enableLinkedIn")){
			       enableLinkedIn =  (newxml.enableLinkedIn == 'true') ? true : false;
			}else{
				enableLinkedIn = true;
			}
		
		
			    	
				
			
			if(newxml.hasOwnProperty("EnableVideoChat")){
			      EnableVideoChat =  (newxml.EnableVideoChat == 'true') ? true : false;
			}else{
				EnableVideoChat = true;
			}
			
			/*
			if(newxml.hasOwnProperty("AnonymousAccess")){
			      AnonymousAccess =  (newxml.AnonymousAccess == 'true') ? true : false;
			}else{
				AnonymousAccess = false;
			}*/
			
			if(newxml.hasOwnProperty("EnableRecording")){
			      EnableRecording =  (newxml.EnableRecording == 'true') ? true : false;
			}else{
				EnableRecording = true;
			}	
			if(newxml.hasOwnProperty("EnableNews")){
			     Application.application.Newsbtn.visible =  (newxml.EnableNews == 'true') ? true : false;
			     newsadminenable = (newxml.EnableNews == 'true') ? true : false;
			    
			}else{
				Application.application.Newsbtn.visible = true;
				newsadminenable = true;				
			}if(newxml.hasOwnProperty("EnableTagCrowd")){
			     Application.application.tagCrowdBtn.visible =  (newxml.EnableTagCrowd == 'true') ? true : false;			     			    
			}else{
				Application.application.tagCrowdBtn.visible = true;
							
			}	
			//This is the code of the new competences enable
			
			if(newxml.hasOwnProperty("EnableCompetences")){
			      EnableCompetences =  (newxml.EnableCompetences == 'true') ? true : false;
			}else{
				EnableCompetences = true;	
			}		
			if(newxml.hasOwnProperty("styling")){
			      Styling2 =  newxml.styling ;
			}else{
				Styling2 = '';	
			}
		
			if(newxml.hasOwnProperty("tubeCode")){
			      tubeCode =  newxml.tubeCode ;
			}else{
				tubeCode = '';	
			}
	
			if(newxml.hasOwnProperty("vClassRoom")){
				  vCREnabled = newxml.vClassRoom.enable;;
			      vClassRoomUrl = newxml.vClassRoom.url;
			}else{
				vCREnabled = false;				
			}
			
			
		// For the pop ups	
		   if(newxml.hasOwnProperty("enableFirstPopUp")){
				  enableFirstPopUp = newxml.enableFirstPopUp.enable;
			      firstPopUpMessage = newxml.enableFirstPopUp.message;
			      secondPopUpMessage = newxml.enableFirstPopUp.secondmessage;
			}else{
				enableFirstPopUp = false;				
			}
		
			
			if(newxml.hasOwnProperty("PrivacyLink")){
				  privacyLinkUrl = newxml.PrivacyLink;			      
			}else{
				privacyLinkUrl = "";
			}
			
			if(newxml.hasOwnProperty("defaultPageState")){
				  defaultPageState = newxml.defaultPageState;			      
			}else{
			     defaultPageState = "login";			
			}
			
			if(newxml.hasOwnProperty("homePageLogo")){
				  homePageLogo = newxml.homePageLogo;			      
			}
			
			if(newxml.hasOwnProperty("homePageLogoUrl")){
				  homePageLogoUrl = newxml.homePageLogoUrl;			      
			}
			
			if(newxml.hasOwnProperty("firstChannel")){
					  firstChannel = newxml.firstChannel;			      
			}
			if(newxml.hasOwnProperty("secondChannel")){
					  secondChannel = newxml.secondChannel;			      
			}
			if(newxml.hasOwnProperty("thirdChannel")){
					  thirdChannel = newxml.thirdChannel;			      
			}
			if(newxml.hasOwnProperty("membersLabel")){
					  membersLabel = newxml.membersLabel;			      
			}
			
			if(newxml.hasOwnProperty("videoExtension")){
				  videoExtension = newxml.videoExtension;			      
			}else{
			     videoExtension = "*.flv;*.mp4;*.mov;*.mpeg;*.mpeg4;*.m4v;*.wmv";			
			}
			if(newxml.hasOwnProperty("conversionFormat")){
				  conversionFormat = newxml.conversionFormat;			      
			}else{
			     conversionFormat = ".flv";			
			}
		
		


			

			
			
						
		/* 	if (defaultPageState == 'login'){ 
		            lblHome.visible = false;
			} */
			
			
			
			
			/* helpLink = (newxml.contains('helpLink')) ? (newxml.helpLink) : 'http://www.calt.insead.edu/';
			helpMail = (newxml.contains('helpMail')) ? (newxml.helpMail) : 'pkmittal82@gmail.com';
			 */
			  helpLink = newxml.helpLink;
			  helpMail = newxml.helpMail;
			  
			popupAgent = (newxml.popupAgent == 'true') ? true : false;
			if(!popupAgent) welcAgent.close(null);
			tradeEnabled = (newxml.trade.enabled == 'true') ? true : false;
			tradeUrl = newxml.trade.url;
			privacyEnabled = (newxml.privacy == 'true') ? true : false;
			
			video_conferencePath=newxml.video_conferencePath;
			
			var tempstr:String = newxml.key;
			useKey = (tempstr.length>0 && tempstr.length<=10) ? 'true' : 'false';
 	 		ServerNameConstant=newxml.ServerName;
 	 		VideoPath=newxml.VideoPath;
			aboutUs = newxml.aboutUs;
			videoTypes_PanelLabel = newxml.videoTypes_PanelLabel;			
			videoCategory_CDLabel = newxml.videoCategory_CDLabel;
			videoCategory_CDOppLabel = newxml.videoCategory_CDOppLabel;
			videoCategory_CDExpertsLabel = newxml.videoCategory_CDExpertsLabel;
			
			if(newxml.hasOwnProperty("label_fourth_videoCategory")){
				label_fourth_videoCategory = newxml.label_fourth_videoCategory;
			     enableFourth_videoCategory = true;
			}else{
				enableFourth_videoCategory  = false;
			}
			
			if(newxml.hasOwnProperty("label_fifth_videoCategory")){
				label_fifth_videoCategory = newxml.label_fifth_videoCategory;
			     enableFifth_videoCategory = true;
			}else{
				enableFifth_videoCategory  = false;
			}
			
			if(newxml.hasOwnProperty("label_sixth_videoCategory")){
				label_sixth_videoCategory = newxml.label_sixth_videoCategory;
			     enableSixth_videoCategory = true;
			}else{
				enableSixth_videoCategory  = false;
			}	
						
			if(newxml.hasOwnProperty("channelIndex")){
				default_channelIndex = parseInt(newxml.channelIndex);			     
			}else{
				default_channelIndex = 0;
			}
			
			
			
			
						
			demoVideoId = newxml.demoVideoId;
			demo_taboowords_for_display="Taboo Words\n\n";
			taboowords = newxml.taboowords.toString();
			demo_taboowords_array=taboowords.split(",");
			for(i=0;i<demo_taboowords_array.length;i++) {
				demo_taboowords_for_display=demo_taboowords_for_display+demo_taboowords_array[i].toString()+"\n";
			}
			gamepage_title=newxml.gamepage_title;
			gameDescription=newxml.gameDescription;
			if(newxml.hasOwnProperty("nodeWidth")){
				canvas_width = newxml.nodeWidth;
			}
			else{
				canvas_width = 62;
			}
			if(newxml.hasOwnProperty("nodeNameWidth")){
				text_width = newxml.nodeNameWidth;
			}
			else{
				text_width = 58;
			}
			
			//video_catagories.push(""); 
			video_catagories.push(videoCategory_CDLabel);	
			video_catagories.push(videoCategory_CDOppLabel);
			video_catagories.push(videoCategory_CDExpertsLabel);
			
			// To add a new category on demand
			if(enableFourth_videoCategory){
				video_catagories.push(label_fourth_videoCategory);
			  }
			if(enableFifth_videoCategory){
				video_catagories.push(label_fifth_videoCategory);
			  }
			if(enableSixth_videoCategory){
				video_catagories.push(label_sixth_videoCategory);
			  }
			  
			  
			  
			// anything can run before because of http request so we are not assigniing currenbt state here
			if((actionName =="innoPass")|| (actionName =="viewProfile")|| (actionName =="ByPassLogin")  || (actionName =="viewVideo") || (AnonymousAccess == true) ) {
				// do nothing
				
			}else if(actionName =="playvideoByPassLogin2"){
			//		currentState    = 'new_ui';
			   
	     	}else if (actionName =="playmyvideo"){
	     			currentState = 'login';
	     		   loginForm.init();
	     	}
	     	
	     	else{ 			
		//	currentState = 'login';
		 		currentState = defaultPageState
		 			if (currentState == 'home'){
		 				AnonymousAccess = true;		 				
		 				passedUserName = "anonymous@gmail.com";
						passedKey = "lab1"; 				
						Univ_LoginId    = passedUserName;
						Univ_LoginId1	= passedUserName;	
					 				
		 				onAppCreationComplete();		 		
		 			}else if (currentState == 'login'){
			  			 loginForm.init();
					}
			}
		}
		
		
		public function cleanUp():void{
			//Alert.show("Are You Sure");
			var t_info:Object 	= 	new Object();
  			t_info.action 		= 	'updatelog';
  			t_info.actiontype 	= 	'Logout';
  			t_info.takenby 		=  	Univ_LoginId;
  			t_info.takenon 		= 	tubeShortName;
  			
  			send_log_msg(t_info);
		}
		
		
		 private var urlLoader:URLLoader;
		   private function loadCSS():void {
                urlLoader = new URLLoader();
                urlLoader.addEventListener(Event.COMPLETE, urlLoader_complete);
                urlLoader.load(new URLRequest("styles.css"));
            }
           private function urlLoader_complete(evt:Event):void {
                var css:String = URLLoader(evt.currentTarget).data;
                // Convert text to style sheet.
                styleSheet = new StyleSheet();
                styleSheet.parseCSS(css);
                // Set the style sheet.
              //  textArea.styleSheet = styleSheet;
            } 
		
		public function load_constants():void
		{
			
		
        ServerPath = Application.application.parameters.servername + Application.application.parameters.tubepath;
		
		loadconstants.url			= ServerPath + graphData + "loadconstants.php";
		loadconstants.send();
		removeChild(helpPanel);
		//removeChild(chatCanvas);
		removeChild(playCanvas);
		try {
		if(contains(agentWindow))
			removeChild(agentWindow);
		if(contains(tradeWindow))
			removeChild(tradeWindow);

/*		if(contains(popWindow1))
			removeChild(popWindow1);
	*/	
			
		} 
		
		
		catch(err:Error) {}
		//simTimer2.addEventListener(TimerEvent.TIMER_COMPLETE,listenFn);
		}
		
		
		/**  called on creation complete of main application */
			// for laboranova single sign 
	    private var nologin :String;		
		private	var innoUserName: String;			
		private var vId:String = new String();
		private var vUserName:String = new String();
		public var actionName:String = new String();
		public var passedUserName:String = new String();
		public var passedKey:String = new String();
		public var passed_videoID:String=new String();
		public var videoPlay_set:Boolean=new Boolean();
		
		public function preload():void{
			
				
			qs = new QueryString();
			tempEv = new ItemClickEvent(ItemClickEvent.ITEM_CLICK);
    		tempEv.label = Network_ItemLabel ;
			isFirst = true;
			
			
			innoUserName = Application.application.parameters.username;
			actionName = qs.parameters.actionName;
			vUserName = qs.parameters.vUserName;
			vId = qs.parameters.vId;
			nologin = qs.parameters.nologin;
			passed_videoID=qs.parameters.videoid;
			passedUserName = qs.parameters.userName;
			passedKey = qs.parameters.key;
			
			
			// Start For testing
			
			
			// end for testing
			
			
			
			//// to login directy with the anonymous acccount
			
			if(nologin==null){
				nologin = Application.application.parameters.nologin;
			}
			
			if(innoUserName==null){
				innoUserName = Application.application.parameters.innoUserName;
			}
			if(actionName==null){
				actionName = Application.application.parameters.actionName;
			}
			if(vUserName==null){
				vUserName = Application.application.parameters.vUserName;
			}
			if(vId==null){
				vId = Application.application.parameters.vId;
			}
			
			if(passed_videoID==null){
				passed_videoID = Application.application.parameters.videoid;
			}
			if(passedUserName==null){
				passedUserName = Application.application.parameters.userName;
			}
			if(passedKey==null){
				passedKey = Application.application.parameters.key;
			}			
			
			if(nologin == "yes"){
			   AnonymousAccess = true;
			}else{
				AnonymousAccess = false;
			}
						    
						
			if(actionName=="playvideoByPassLogin2"){
									
				Univ_LoginId    = passedUserName;
				Univ_LoginId1	= passedUserName;
			}
			else if(actionName=="playvideo"){
				passed_videoID=qs.parameters.videoid;
			}		
		
	     	
						
			else if(actionName=="ByPassLogin"){
				
				Univ_LoginId    = passedUserName;
				Univ_LoginId1	= passedUserName;
			}else if(actionName=="ByPassShowNetwork"){
								
				Univ_LoginId    = passedUserName;
				Univ_LoginId1	= passedUserName;
			}else if(AnonymousAccess == true){
				passedUserName = "anonymous@gmail.com";
				passedKey = "lab1"; 				
				Univ_LoginId    = passedUserName;
				Univ_LoginId1	= passedUserName;				
			}
			
								
			ServerPath 			= 	Application.application.parameters.servername + Application.application.parameters.tubepath;									
			fmsaddress 			= 	Application.application.parameters.fmsname;
								
			if ((actionName == "ByPassLogin")) {
				var bypasslogin:HTTPService 	= 	new HTTPService();
				bypasslogin.url					= 	ServerPath + 'GraphData/groupmanager.php';
				bypasslogin.useProxy			=	false;
				bypasslogin.resultFormat		=	'object';
				bypasslogin.method				=   'post';
				
				var params:Object=new Object();
				params.action='directlogin';
				params.username=passedUserName;
				params.key=passedKey;
				
				bypasslogin.addEventListener(ResultEvent.RESULT,directlogin_function);
				bypasslogin.send(params);
			}else if (actionName == "ByPassShowNetwork"){
				var ByPassShowNetwork:HTTPService 	= 	new HTTPService();
				ByPassShowNetwork.url					= 	ServerPath + 'GraphData/groupmanager.php';
				ByPassShowNetwork.useProxy			=	false;
				ByPassShowNetwork.resultFormat		=	'object';
				ByPassShowNetwork.method				=   'post';
				
				var params:Object=new Object();
				params.action='directlogin';
				params.username=passedUserName;
				params.key=passedKey;
				
				ByPassShowNetwork.addEventListener(ResultEvent.RESULT,ByPassShowNetwork_Response);
				ByPassShowNetwork.send(params);
			} else if (tubeShortName = "GMPTubeN16"){
				
			}
			
			else if(actionName=="playvideoByPassLogin2"){
											
				var bypasslogin1:HTTPService 	= 	new HTTPService();
				bypasslogin1.url					= 	ServerPath + 'GraphData/groupmanager.php';
				bypasslogin1.useProxy			=	false;
				bypasslogin1.resultFormat		=	'object';
				bypasslogin1.method				=   'post';
				
				var params1:Object=new Object();
				params1.action='directlogin';
				params1.username=passedUserName;
				params1.key=passedKey;
				
				bypasslogin1.addEventListener(ResultEvent.RESULT,directlogin_function);
				bypasslogin1.send(params1);
			}
			else if(actionName=="playvideo"){
				var bypasslogin2:HTTPService 	= 	new HTTPService();
				bypasslogin2.url					= 	ServerPath + 'GraphData/groupmanager.php';
				bypasslogin2.useProxy			=	false;
				bypasslogin2.resultFormat		=	'object';
				bypasslogin2.method				=   'post';
				
				var params2:Object=new Object();
				params2.action='directlogin';
				params2.username=passedUserName;
				params2.key=passedKey;
				
				bypasslogin2.addEventListener(ResultEvent.RESULT,directlogin_function);
				bypasslogin2.send(params2);
			}else if ((actionName=="innoPass")&& (innoUserName != "" )&& (innoUserName != null)) {
				//actionName = "innoPass";
				Univ_LoginId    = innoUserName;
			 	Univ_LoginId1	= innoUserName;			 	  
			    currentState    = 'new_ui';
			     onAppCreationComplete();
			 }else if ((actionName=="viewProfile")&& (innoUserName != "" )&& (innoUserName != null)) {
				Univ_LoginId    = innoUserName;
			 	Univ_LoginId1	= innoUserName;
			 	currentState    = 'new_ui';
			 	detailedprofileId = vUserName;
			 	 onAppCreationComplete();			 	  
		        // currentState    = 'profilingdim';
			    
			 }else if ((actionName=="viewVideo")&& (innoUserName != "" )&& (innoUserName != null)) {
				Univ_LoginId    = innoUserName;
			 	Univ_LoginId1	= innoUserName;
			 	currentState    = 'new_ui';
			 	onAppCreationComplete();			 	  
		         //currentState    = 'new_ui';
			    
			 }
			 
			
			else{
				currentState		= 	'empty';
			}
			
			
			
			//	gamealerttimer.addEventListener(TimerEvent.TIMER_COMPLETE,removegamealert);
		}
		
		
		
		public function	ByPassShowNetwork_Response(r:ResultEvent):void{
			try{
					var responseMessage:String;
			   		responseMessage	=	r.result.rsp.message;
			   		
			   		if(responseMessage=='Success'){
			   			    currentState    = 'new_ui';
			   				onAppCreationComplete();
			   							   				   							
						
			   		}
			   		else{
			   			
			   			Alert.show('sorry',responseMessage);
			   			currentState	= 	'login';	
			   		}
			 	}
			 	catch(e:Error){
			 		trace('An unexpected error had occured. Please contact the administrator','Error !');
			 	}
		}
		
			
			
		public function directlogin_function(r:ResultEvent):void{
			try{
					var responseMessage:String;
			   		responseMessage	=	r.result.rsp.message;
			   		
			   		if(responseMessage=='Success'){
			   			currentState    = 'new_ui';
			   	// commented temporarily to test		
						onAppCreationComplete();
					
						
			   		}
			   		else{
			   			
			   			Alert.show('sorry',responseMessage);
			   			currentState	= 	'login';	
			   		}
			 	}
			 	catch(e:Error){
			 		trace('An unexpected error had occured. Please contact the administrator','Error !');
			 	}
		}
		
		private function clickLogo ():void {
			
			if (Application.application.defaultPageState == "home"){			
				    pausevideo();
	    			Application.application..currentState='home';
	    		}
	    		return;
		}
		
		/** Exit the session, Stop any videos playing and reload the page		*/ 		         	
       	private function Session_Logoff():void{
  			var t_info:Object 	= 	new Object();
  			t_info.action 		= 	'updatelog';
  			t_info.actiontype 	= 	'Logout';
  			t_info.takenby 		=  	Univ_LoginId;
  			t_info.takenon 		= 	tubeShortName;
  			
  			send_log_msg(t_info);
  			
  			// just check, if any videos are being played, and stop it
  			
  		/*	
  			try{
  				if ( YouTubeloader != null ){
					YouTubeloader.unload();	
					YouTubeloader =  null;
				}
				if ( pg_gamemodule.GameYouTubeloader != null ){
					pg_gamemodule.GameYouTubeloader.unload();	
					pg_gamemodule.GameYouTubeloader =  null;
				}
  			}
  			catch(e:Error){
  				// bye baba !! :)
  			}
  			
  		*/	
  			var urlAddress :String;  			
  			urlAddress = Application.application.parameters.servername + Application.application.parameters.tubepath + Application.application.parameters.tubeName + ".html" ;
  			  			
  			//var urlRequest:URLRequest = new URLRequest( "javascript:history.go(0)" );
  			var urlRequest:URLRequest = new URLRequest( urlAddress );
  			
  			navigateToURL( urlRequest,"_top" );
  			
  			/*
  			
  			if(defaultPageState == "home"){
  				AnonymousAccess = true;		 				
		 				passedUserName = "anonymous@gmail.com";
						passedKey = "lab1"; 				
						Univ_LoginId    = passedUserName;
						Univ_LoginId1	= passedUserName;
						display_user_name = 'Anonymous';
  				Application.application.currentState ='home'
  			}else{
  				Application.application.currentState = 'login';
  			}
  			
  			*/
  		
  		
  			t_info = null;	 
       	}
       	
       	
       
			
		/** all the log actions stored in useractions table pass through this function 
		 *  we store the login, logout actions only. **/
		/* this function is copied in the InnoForum project... please make any changes there also.*/
		public function send_log_msg(info:Object):void{
			var variables:URLVariables = new URLVariables();
			variables.action  		= info.action;
			variables.actiontype  	= info.actiontype;
			variables.actionnewtype = info.actionnewtype;
			variables.takenby  		= info.takenby;
			variables.takenon 		= info.takenon;
			variables.entity 		= info.entity;
			update_log.url 		= ServerPath + graphData + "useractions.php";
			update_log.request 	= variables;
			update_log.send();
			return; 
		}
	  
	   /**  its called often to reload the network data from server **/
		public function onAppCreationComplete():void{									
			loadnetwork.url			= ServerPath + graphData + "loadnetwork_ssk.php";	
			loadnetwork.send();	
			  minuteTimer.stop(); // minutetimer is for checking game invites.
			  minuteTimer.reset();   
			  minuteTimer.addEventListener(TimerEvent.TIMER, onTick);   	
			  minuteTimer.start();
			//fmsConnect();    
  
		}
		
		public function refreshAllData():void{
			
			onAppCreationComplete();				
			// caling the welcome agent and reco agent to calculate things
	   		welcAgent.refreshDetails();	   						
	   		// setTimeout(app.welcAgent.refreshDetails,10000);	   						
	   		helpBoxId.refreshRecoAgent();
  							
		}
		
		public function refreshRelationShips():void{
			loadnetwork.url			= ServerPath + graphData + "refreshTagRelations.php";	
			loadnetwork.send();
			
		}
		
		
		/**The following 13 variables are used by the chat.
		  usersTimer - to repetitively send a HTTPrequest for online users list to the server.
		  messagetimer - to repetitively send a HTTPrequset for new messages from server.
		  isChatMin - controls whether the chat window is open or not.
		  latesttime - is the timestamp of the latest message received from server. It is sent as a
		  				post variable when checking for new messages, so that server knows beyond what 
		  				time to look for messages.
		  faultcount - it is the number of times the users or messages HTTP request fails continously.
		  				we repeat the request for 5 times, before informing the user of failure.
		  isFirstTime - this variable is used to handle the special case when the user does not have any
		  				value for latesttime(when just logging in) and the server sends in a start time
		  				whose value we set as latesttime.
		  isDataChanged - this variable is set if there is a change in the new online users list when 
		  				compared to the old list, so that we dont change dataprovider for the list
		  				unnecessarily.
		  onlineUsers - This array is reinitialized and populated with new users after every user list load
		  activeChats -  This is an associative array where the objects in this array are indexed by their
		  				ids. Each object in the array contains 3 properties - 
		  				 id - id of the user with whom the chat is being stored.
		  				 chat - the chat that has taken place so far
		  				 window - a reference to the window component of that chat.
		  activeChatUsers - This variable was being used in an earlier implementation. Now redundant.
		  activeChatWindow - it is the collection of windows that have to made visible when entering 
		  * 				the various states. Basically, the list of active personal chat windows.
		**/ 
		
		
		public var usersTimer:Timer=new Timer(500,0);//runs once and calls the sendUsersRequest function on expiry.
		public var messageTimer:Timer=new Timer(500,0);//runs once and calls the sendMessageRequest function on expiry.
		
		[Bindable]public var isChatMin:Boolean=false;
		public var latestTime:String="";
		private var faultCount:int = 0;
		private var isFirstTime:Boolean = true;
		public var isDataChanged:Boolean = false;
		[Bindable] public var onlineUsers:Array=new Array();
		[Bindable] public var latestonlineUsers:Array=new Array();
		public var activeChats:Array=new Array();
		public var activeChatUsers:Array = new Array();
		public var activeChatWindows:ArrayCollection=new ArrayCollection();

/*This function is called when userstimer expires. It sends an http request for online users list.
When the response to the loadonlineusers http request comes, onlineUsersLoad() function is called.
*/ 
		private function sendUsersRequest(t:TimerEvent):void{
			var variables:URLVariables=new URLVariables();
			variables.id=Univ_LoginId1;
			loadOnlineUsers.request=variables;
			loadOnlineUsers.send();
		}
		
/* This function is called when the messagetimer expires. It sends an http request for new messaged.		
When the response to the loadNewMessage http request comes, newMessageLoad() function is called.
*/
		private function sendMessageRequest(t:TimerEvent):void{
			var variables:URLVariables=new URLVariables();
			variables.id=Univ_LoginId1;
			variables.time=latestTime;
			loadNewMessage.request=variables;
			loadNewMessage.send();
		}
		
       	/** event listener for loadnetwork:httpservice, we get the data and pass to LoadVideos()  */
		public function loadnetwork_showresponse():void{
			
			togglebuttonbar1.enabled = true;
			usersTimer.addEventListener(TimerEvent.TIMER,sendUsersRequest);
			messageTimer.addEventListener(TimerEvent.TIMER,sendMessageRequest);
			usersTimer.reset();
			usersTimer.start();
			messageTimer.reset();
			messageTimer.start();
			
			xmldata	=	loadnetwork.lastResult;
			loadnetwork.clearResult();
		    Innoxml = 	new XML(xmldata);
//		    Alert.show(Innoxml.@Graph.@Competences,"testing");

            /// order is important.. just to chnage the state in case of innopass
			if (actionName=='innoPass'){
				currentState    = 'new_ui';
			}else if (actionName=='viewProfile'){
				currentState    = 'profilingdim';
			}else if (actionName=='viewVideo'){
			currentState    = 'new_ui';
			} else if (actionName=='playvideoByPassLogin2'){	
				currentState    = 'new_ui';				
			} 
			
			
			
			
		    LoadVideos(Innoxml);
		    		    
		    
		    
		    edgeList_Xml = Innoxml.descendants("Edge");
		    		    		    
		    
		    competencesArray=new Array();
		    competenceTypesArray=new Array();
		    competenceCategories=new Array();
		    var i:int;
		    i=0;
		    
		    // load groups	    
		    //grpList_Xml = Innoxml.descendants("Groups");
		    
		    groupList = new Array();
		    for each (var gnode: XML in Innoxml.descendants("group")) {
   				 groupList.push(gnode.@name.toString());
   		   		 i=i+1;
   			}
   			
   			i =0;		    
			for each (var node1: XML in Innoxml.descendants("Competence")) {
   				competencesArray.push(node1);
   		//		Alert.show(competencesArray[i].@name,"future name");
   				i=i+1;
   			}
   			i=0;
   			for each(var node2:XML in Innoxml.descendants("CompetenceType"))
   			{
   				competenceTypesArray.push(node2);
   				competenceCategories.push(node2.@name.toString());
   			//	Alert.show(competenceTypesArray[i].@name,"Type name");
   				i=i+1;   				
   			}
   			
   			if(isFirstTime == true){ 
   			for each(var node3:XML in Innoxml.descendants("Time")){
   				latestTime = node3.@value.toString();
   			}
   			isFirstTime=false;
   			}
   			
   			// get the service names
   			servTypeList = new Array();			
				for each (var node1: XML in Innoxml.descendants("serv")) {
				servTypeList.push(node1);
   			   }
   			   
   			// get the resource names
   			resTypeList = new Array();			
				for each (var node1: XML in Innoxml.descendants("res")) {
				resTypeList.push(node1);
   			   }
   	
   			
   	
   			
   			
            if(currentState=='profilingdim')
            {
            	tentube_profile.displayprofile(new FlexEvent(FlexEvent.ADD));
            	// added for viewing the list when action is viewProfile
            	tentube_profile.tileProfiles.dataProvider= people_list;
            }
            
            
            if(actionName=='playvideo' || actionName=="playvideoByPassLogin2" || actionName=="playmyvideo"  ){
            	
                    if(firstPlay == false){
            						  startVideoTimer.stop(); 
			  						  startVideoTimer.reset();
		    						  startVideoTimer.start();
			  						 startVideoTimer.addEventListener(TimerEvent.TIMER, checkClick  );
                    }
			  	  firstPlay = true;
			  						 
			
			}
			if(actionName=='viewVideo'){
				
					//startLoading(vId);
				//startLoadingSingleClick(vId);
				startLoadingSingleClick(vId);
			}	
					
			if (actionName=='ByPassShowNetwork'){
						currentState    = 'spring_view';
			   			centre_id=Univ_LoginId1;
						 new_current_id=Univ_LoginId1;
						 LoadNetwork();
						showSpringgraph();
			}			
							
			
			
			if(Application.application.Univ_LoginId1=="anonymous@gmail.com"){
				lblLogout.visible = false;
			}else{
				lblLogout.visible = true;
			}
			// displaying of usre name on the home page			
			Application.application.display_user_name = getUserName( Application.application.Univ_LoginId1);
			
			
			
			
			if(loadnetwork.url==(ServerPath + graphData + "loadnetwork_lab.php"))
			formlabgraph();
			// to display the remaining edges and nodes to the graph after connecting to all in the group
			if(cta==1)
			{
			addmorecta();
			cta=0;
			}
			
			
			// Show the pop up
			if(!firstPopUpShown){
				if(showFirstPopUp){
				  openFirstPopUp();
			      firstPopUpShown = true;
				}
			}
			
			
			
        }
        
        public function getUserName(userId :String):String{
  			var userName: String;
  			
  			userName = "";
  			
  			
  			for each ( var ppl:XML in Application.application.people_list ){
  				if ( ppl.@id.toString().toLowerCase() == userId.toLowerCase() ){
  					userName =  ppl.@name.toString();
  				}
  			}
  			  			 
  			return userName ;
  		}
        



  		private function onlineUsersLoad():void{
  			var userXml:XML=new XML(loadOnlineUsers.lastResult);
  			loadOnlineUsers.clearResult();
  			
  			//Array of objects each representing info about an online user.
   			onlineUsers = new Array();
  			for each(var node:XML in userXml.descendants("User")){
  			var user1:Object=new Object();
  				user1.id=node.@id.toString();
  				user1.name=people_id_name[node.@id.toString()];
  				onlineUsers.push(user1);
  			}
  			

  			if(tentube_vidcon != null)
  				//tentube_vidcon.cononlineUsersList.dataProvider = new ArrayCollection(onlineUsers);
  			tentube_vidcon.onlineUsersList.dataProvider = new ArrayCollection(onlineUsers);
  			//onlineUsersListgame.dataProvider=new ArrayCollection(onlineUsers);

  			// Update the Chat label with the number of online users... SSK
			var temp:Object = (togglebuttonbar1.dataProvider as ArrayCollection).getItemAt(3);
			var newTemp: String = Conferencing_ItemLabel + ((onlineUsers.length > 0) ? (" "+ '[' + onlineUsers.length.toString() + ']') : (''));
			if(temp.label != newTemp) {
				temp.label = newTemp;
				(togglebuttonbar1.dataProvider as ArrayCollection).setItemAt(temp, 3);
			}
  			
  			//Now we restart the timer to repeat the whole process of loading users again after 7 seconds.
  			
  			usersTimer.delay = 500;
     			usersTimer.reset();
  				usersTimer.start(); 
   
		}
		
		
/* This function is called whenever we enter any of the channel, network, profile states.
This function adds all active personal chat windows to the base state, so as to make sure that
they are visible at the top of everything.*/
		public function addChildren():void{
			addChild(helpPanel);
			//addChild(chatCanvas);
			addChild(playCanvas);
			addChild(agentWindow);
			addChild(tradeWindow);
		//	addChild(popWindow1);
			for each(var ob:Object in activeChatWindows){
				addChild(ob as DisplayObject);
			}
		}
		
/* This function is called whenever we leave any of the above states, so that they can be readded later
so that they are visible at the top.*/
		public function removeChildren():void{
			if(contains(helpPanel))
			removeChild(helpPanel);
			//if(contains(chatCanvas))
			//removeChild(chatCanvas);
			if(contains(playCanvas))
			removeChild(playCanvas);
			if(contains(agentWindow))
			removeChild(agentWindow);
			if(contains(tradeWindow))
			removeChild(tradeWindow);
			
	/*		if(contains(popWindow1))
			removeChild(popWindow1);
			
		*/	
			
			for each(var ob:Object in activeChatWindows){
				removeChild(ob as DisplayObject);
			}
		}
		
		
/*This function is called each time for each new message loaded from the server. This checks if there
is a chat history in this login session already for the user who sent the message. If so, then the 
message is added to its chat window if available, if not a new window is created with the chat history.

However if this is the first time in this session that this user sent a personal message, then
a new object corresponding to this user's chat is stored in the activeChats variable and a new window
is launched and the window reference is also stored in the activeChatWindows variable so that it 
gets launched in other states also.*/
		public function startChat(sel:Object):void{
//			var sel:Object = onlineUsersList.selectedItem;
			if(activeChats.hasOwnProperty(sel.id.toString())){
				if(activeChats[sel.id.toString()].window==null){
					var newChat:chatWindow = new chatWindow();
					newChat.chatHistory = activeChats[sel.id.toString()].chat.toString();
					newChat.chatWith = sel.id.toString();
					newChat.chatTitle = v1039 + " "+ people_id_name[sel.id.toString()];
					activeChats[sel.id.toString()].window = newChat;
					this.addChild(newChat);
					activeChatWindows.addItem(newChat);
				}
			}
			else{
				var newChat1:chatWindow = new chatWindow();
				newChat1.chatHistory = "";
				newChat1.chatWith = sel.id.toString();
				newChat1.chatTitle = v1039 + " "+ people_id_name[sel.id.toString()];
				var temp:Object = new Object();
				temp.chat = "";
				temp.id = sel.id.toString();
				temp.window = newChat1;
				activeChats[sel.id.toString()] = temp;
				this.addChild(newChat1);
				activeChatWindows.addItem(newChat1);
			}
		}
		
		
//This function was used earlier but now is not used anywhere. So feel free to delete it if needed.
		public function addChatUser(user:Object):void{
			if(activeChats.hasOwnProperty(user.id.toString()))
				return;
			var userData:Object = new Object();
			userData.name = user.name;
			userData.id = user.id;
			userData.chat = "";
			activeChats[userData.id.toString()]=activeChatUsers.length;
			activeChatUsers.push(userData);
			//	activeUsersList.dataProvider=activeChatUsers;
		}
		

// This is called whenever the user sends a public chat message. The destination id for public messages
//is "all@".
		public function sendMessage0():void{
			if(tentube_vidcon.chatInput.text=="") {return;}
			var tx:String = tentube_vidcon.chatInput.text;
			tentube_vidcon.chatInput.text = "";
			//chatInput.htmlText="";
			sendChatMessage(tx,'all');
			tentube_vidcon.chatText.text=tentube_vidcon.chatText.text+ people_id_name[Univ_LoginId1] +" : "+tx+"\n";
			
			// highlight the Talk
			togglebuttonbar1.selectedIndex = 3;
		}
		

//This is called when the user sends any kind of message. txt is the message to send, amd id is the
//destination id(which is "all@" in case of public chat messages.
		public function sendChatMessage(txt:String,id:String):void{
			if(txt==""){return;}
//			activeUsersList.selectedItem.chat=activeUsersList.selectedItem.chat.toString()+ "\n"+ people_id_name[Univ_LoginId1] +":\n"+ chatInput.text+"\n" ;
//			activeChats[activeUsersList.selectedItem.id.toString()].chat = activeChats[activeUsersList.selectedItem.id.toString()].chat.toString() + chatInput.text+"\n" ;
//			activeUsersList.dataProvider=activeChatUsers;
//			chatText.text=chatText.text+"\n"+ people_id_name[Univ_LoginId1] +":\n"+chatInput.text+"\n";
//			var txt:String= chatInput.text;
//			chatInput.text="";
			var variables:URLVariables=new URLVariables();
			variables.fromid=Univ_LoginId1;
			if(id=="all"){
				variables.fromid = variables.fromid + "@";
			}
			variables.toid=id;
			variables.message=txt;
			sendMessage.request=variables;
			sendMessage.send();
			
			public_chatlog(id);
					
						
		}
		
		public var public_chatinitiated:Number=0;
	
		
		
				
		public function public_chatlog(id:String):String{
			
			if(id=="all" && public_chatinitiated==0){
					var variables:URLVariables = new URLVariables();
					variables.action='updatelog';
					variables.actiontype='publicChatInitiated';
					variables.takenby=Univ_LoginId1;
					variables.takenon='Chat';
					
					update_log.url 		= ServerPath + graphData + "useractions.php";
					update_log.request 	= variables;
					update_log.send();
					public_chatinitiated=1;
			}
			
			return "test" ;
		}

//This function is invoked when the server sends back a status for a message that was sent.
		public function sentMessageStatus():void{
			try{
  			var status:String=sendMessage.lastResult.rsp.status.toString();
  			if(status.search("Success")==-1){
  				faultCount=0;
/*   				var i:int=0;
  				for each(var ob:Object in activeChatUsers){
  					if(ob.id.toString()==sendMessage.lastResult.rsp.id.toString()){
  						activeChatUsers[i].chat=activeChatUsers[i].chat.toString()+"Sorry user offline\n";
  					}
  				}
  			//	activeChats[sendMessage.lastResult.rsp.id.toString()].chat = activeChats[sendMessage.lastResult.rsp.id.toString()].chat.toString() + "Sorry user offline\n";
				activeUsersList.dataProvider=activeChatUsers; */
  			}
  			sendMessage.clearResult();
  			}catch(e:Error){
  				sendMessage.clearResult();
  			}
 			return;			
		}
		
		private function minimizeChat():void{
			isChatMin=false;
		//	tentube_profile.upLeft.visible=true;
//			chatCanvas.width=22;
//			chatCanvas.height=27;
		}
		
		public function maximizeChat():void{
//			chatCanvas.width=354;
//			chatCanvas.height=209;
			isChatMin=true;
		//	tentube_profile.upLeft.visible=false;
		//	chatCanvas.width=18;
		//	chatCanvas.height=23;
		}
		
		public function setScrollPosition():void{
			tentube_vidcon.chatText.verticalScrollPosition=tentube_vidcon.chatText.maxVerticalScrollPosition;
		}
		

		private function newMessageLoad():void{
			try{
				var msgData:Object=loadNewMessage.lastResult;
  			loadNewMessage.clearResult();
  			var messagesXml:XML=new XML(msgData);
//  			var selIndex:int=activeUsersList.selectedIndex;
  			var isNewMessage:Boolean=false;
  			var index:int=-1;
			for each(var node:XML in messagesXml.descendants("Message")){
					if(node.@time.toString()>latestTime){
						latestTime = node.@time.toString();
					}
					var nodeid:String = node.@id.toString();
					// Now check if fromid terminates with @, if so a public message.
					if(nodeid.charAt(nodeid.length-1)=="@"){
						nodeid = nodeid.substr(0,nodeid.length-1);
						if(nodeid!=Univ_LoginId1)
						tentube_vidcon.chatText.text = tentube_vidcon.chatText.text + people_id_name[nodeid]+" : "+ node.@text.toString()+"\n";
						
						// High Light the Talk Button
					      togglebuttonbar1.selectedIndex = 3;
						
						
						continue; 
					}
					var userData:Object = new Object();
					userData.name = people_id_name[node.@id.toString()];
					userData.id = node.@id.toString();
					startChat(userData);
/* 					userData.chat = "";
					activeChats[userData.id.toString()]=activeChatUsers.length;
//					activeUsersList.dataProvider.addItem
					activeChatUsers.push(userData);		 */
				
/* 				index = activeChats[node.@id.toString()];
				activeChatUsers[index].chat=activeChatUsers[index].chat +"\n"+people_id_name[node.@id.toString()]+":\n"+ node.@text.toString()+"\n"; */
				activeChats[node.@id.toString()].window.chatHistory = activeChats[node.@id.toString()].window.chatHistory+"\n"+people_id_name[node.@id.toString()]+":\n"+ node.@text.toString()+"\n";
				isNewMessage=true;  //no longer used anywhere. might be useful later.
			}
			
/* 			activeUsersList.dataProvider=activeChatUsers;
			activeUsersList.selectedIndex=selIndex; */
			messageTimer.reset();
/* 			if(currentState=='profilingdim'){
				messageTimer.delay=1000;
			}
			else{
				messageTimer.delay=5000;
			} */
			messageTimer.delay = 2000;
			messageTimer.start();

				
				
			}catch(e:Error){
				trace( "Error: newMessageLoad " + e.message);
				 
			}
			
		}

		
		private function addDragFn():void{
			//tentube_vidcon.chatCanvas.addEventListener(MouseEvent.MOUSE_DOWN,downDragListener0);
			playCanvas.addEventListener(MouseEvent.MOUSE_DOWN,downDragListener0);
	//		chatInput.addEventListener(KeyboardEvent.KEY_DOWN,keyDownListener);
		}
		
		private function downDragListener0(m:MouseEvent):void{
			//Alert.show(m.target.toString(),'tt');
			try{
				if(m.target.className=="Canvas"|| m.target.className=="Label"){
					playCanvas.startDrag();
				}
			}catch(er:Error){
				playCanvas.startDrag();
			}
		}
		
/* 		private function keyDownListener(key:KeyboardEvent):void{
			if(key.charCode == 13){
				sendMessage0();
			}
		}		 */
		
		//drag listener for Reco panel.
		private function addDragListener():void{
			helpPanel.addEventListener(MouseEvent.MOUSE_DOWN,downDragListener);
			agentWindow.addEventListener(MouseEvent.MOUSE_DOWN,welcDragger);
			tradeWindow.addEventListener(MouseEvent.MOUSE_DOWN,trdDragger);
		//	popWindow1.addEventListener(MouseEvent.MOUSE_DOWN,pop1Dragger);
			
		}
		
		private function downDragListener(m:MouseEvent):void{
			//Alert.show(m.target.toString(),'tt');
			try{
				if(m.target.className!="ScrollThumb"){
					helpPanel.startDrag();
				}
			}catch(er:Error){
				helpPanel.startDrag();
			}
		}
		
		private function welcDragger(m:MouseEvent):void{
			//Alert.show(m.target.toString(),'tt');
			try{
				if(m.target.className!="ScrollThumb"){
					agentWindow.startDrag();
				}
			}catch(er:Error){
				agentWindow.startDrag();
			}
		}
   		
   		private function trdDragger(m:MouseEvent):void{
			//Alert.show(m.target.toString(),'tt');
			try{
				if(m.target.className!="ScrollThumb"){
					tradeWindow.startDrag();
				}
			}catch(er:Error){
				tradeWindow.startDrag();
			}
		}
		
	/*  private function pop1Dragger(m:MouseEvent):void{
			//Alert.show(m.target.toString(),'tt');
			try{
				if(m.target.className!="ScrollThumb"){
				   popWindow1.startDrag();
				}
			}catch(er:Error){
				popWindow1.startDrag();
			}
		}
   		
   	*/	
        public function loadconstants_showresponse():void{
			xmldata_constants	=	loadconstants.lastResult;
			loadconstants.clearResult();
		    newxml = 	new XML(xmldata_constants);
		     initializeTextVariables(Application.application.parameters.tubeName);
		     //preload();
		    
		}
			   
		
        /** Event handler for toggle button bar located at the top.	*/
	   		   		                 		   		               
        private function homebtnhandler(label:String):void {
			var t_info:Object 	= new Object();
        	weeklyReportPanel.visible=false;
        	switch ( currentState ) {
        		case ProfileView_ItemLabel:
        		// test higlight  
        			profilebox.removeAllChildren();
	    			for each ( var uiCom:UIComponent in Vbox_Refs )
	    				PopUpManager.removePopUp(uiCom);
					Vbox_Refs 	= new Array();
					Vbox_Ids 	= new Array();
	        		break;
        	}
        	if ( SearchWindowOpen ) {
				PopUpManager.removePopUp(searchwin);
				SearchWindowOpen = false;
			}
        	
        	// for the select relationships
				if ( SelrelWindowOpen ){
					PopUpManager.removePopUp(selrelwin);
					SelrelWindowOpen = false;
				}
			switch( label ){
				case RecoAgent_ItemLabel:
					Application.application.moreBox.selectedIndex = 0;
					// for reco agent
					helpBoxId.agentrecommendations.send();
				      helpPanel.visible=true;
				      helpPanel.height=214;
                      helpBoxId.visible=true;
                      showId.visible=false;
                      closeId.visible=true;
				    break;
				case Channel_ItemLabel:
					Application.application.moreBox.selectedIndex = 0;
	      				t_info.action		= "updatelog";	
	      				t_info.actiontype 	= 'Channel';
	      				t_info.takenby 		=  Univ_LoginId1 ;  
	      				t_info.takenon 		= Application.application.parameters.tubeName;
	      				send_log_msg( t_info );	     							
					if((currentState=='gamemoduledemo')&&( ( innovideoStatus == 'playing' ) || (innovideoStatus == 'paused' ))){
						gameVidPlayer.stop();
						gameVidPlayer.close();
						if ( YouTubeloader != null ){
							YouTubeloader.unload();	
							YouTubeloader =  null;
							YouTubeloader = new Loader();
						}
					}
					Univ_LoginId = Univ_LoginId1;
					currentState			=	'new_ui';
					attachButtonVisible=true;
//					setChildIndex(helpBoxId,numChildren-1);
	//				Alert.show(numChildren.toString());
					break;
				case Network_ItemLabel:
				
					if (! Application.application.isLoggedIn()){
					Application.application.forceLogin();
					return;
				}
		
					Application.application.moreBox.selectedIndex = 0;
	      				t_info.action		= "updatelog";	
	      				t_info.actiontype 	= 'Network';
	      				t_info.takenby 		=  Univ_LoginId1 ;  
	      				t_info.takenon 		= Application.application.parameters.tubeName;
	      				send_log_msg( t_info );	     							
					if((currentState=='gamemoduledemo')&&( ( innovideoStatus == 'playing' ) || (innovideoStatus == 'paused' ))){
						gameVidPlayer.stop();
						gameVidPlayer.close();
						if ( YouTubeloader != null ){
							YouTubeloader.unload();	
							YouTubeloader =  null;
							YouTubeloader = new Loader();
						}
					}
		//			stopVideo();
					pausevideo();
					currentState			=	'spring_view';
					if(isFirst){
						centre_id=Univ_LoginId1;
						new_current_id=Univ_LoginId1;
							LoadNetwork();
							showSpringgraph();
					}
		//			setChildIndex(helpBoxId,numChildren-1);
		//			Alert.show(numChildren.toString());
					break;
				case Profiles_ItemLabel:
								
				if (! Application.application.isLoggedIn()){
					Application.application.forceLogin();
					return;
				}
				
				
					Application.application.moreBox.selectedIndex = 0;
	      				t_info.action		= "updatelog";
	      				t_info.actiontype 	= 'Profiles';
	      				t_info.takenby 		=  Univ_LoginId1;
	      				t_info.takenon 		= Application.application.parameters.tubeName;
	      				send_log_msg( t_info );
					if((currentState=='gamemoduledemo')&&( ( innovideoStatus == 'playing' ) || (innovideoStatus == 'paused' ))){
						gameVidPlayer.stop();
						gameVidPlayer.close();
						if ( YouTubeloader != null ){
							YouTubeloader.unload();
							YouTubeloader =  null;
							YouTubeloader = new Loader();
						}
					}
		//			stopVideo();
					pausevideo();
					detailedprofileId 		= 	Univ_LoginId;
					Univ_LoginId = Univ_LoginId1;
					currentState			=	'profilingdim';
					if(isDataChanged == true){
						isDataChanged = false;
						tentube_profile.tileProfiles.dataProvider= people_list;
					}
					usersTimer.reset();
					usersTimer.delay=1;
					usersTimer.start();

               // there was a error in opening an profile and this line has been commented to fix this
  			//		tentube_vidcon.onlineUsersList.dataProvider=onlineUsers;
  			
  					//onlineUsersListgame.dataProvider=onlineUsers;
		//			setChildIndex(helpBoxId,numChildren-1);
	//				Alert.show('',numChildren.toString());
	
					break;
				case Tengame_ItemLabel:
				Application.application.moreBox.selectedIndex = 0;
				       if (!(EnableGame)){
				       	Alert.show(gameDisabledM);
				       	break;
				       }
				
	      				t_info.action		= "updatelog";
	      				t_info.actiontype 	= 'Game';
	      				t_info.takenby 		=  Univ_LoginId1;
	      				t_info.takenon 		= Application.application.parameters.tubeName;
	      				send_log_msg( t_info );
					if((currentState=='gamemoduledemo')){
						break;
					}
					stopVideo();
					//pausevideo();
					Univ_LoginId = Univ_LoginId1;
					currentState			=	'gamemodule';
					//setChildIndex(helpBoxId,numChildren-1);
					break;
				case Admin_ItemLabel:
				Application.application.moreBox.selectedIndex = 0;
	      				t_info.action		= "updatelog";	
	      				t_info.actiontype 	= 'Admin';
	      				t_info.takenby 		=  Univ_LoginId1 ;  
	      				t_info.takenon 		= Application.application.parameters.tubeName;
	      				send_log_msg( t_info );	     							
					Univ_LoginId = Univ_LoginId1;
					if(adminMode==false)
					{
                  	  AdminLoginScreen();
                    }
                    else
                    {
                    	//adminMode is used for checking whether user has already entered the administrator key so that when he tries to gointo admin mode then he will not be asked the key
                    	    adminenter 		= 	true;
                    	    attachButtonVisible=false;
							stopVideo();
							currentState	=	'adminmode';
                    }
		//			setChildIndex(helpBoxId,numChildren-1);
		//			Alert.show(numChildren.toString());
					return;
					break;
					
					
				case Groups_ItemLabel:
								Application.application.moreBox.selectedIndex = 0;
						currentState='groupmode';
						break;
						
			    case Help_ItemLabel:
			                Application.application.moreBox.selectedIndex = 0;
			    			helpclicked();
			    			break;
			    			
			   case v1072:
			        Application.application.moreBox.selectedIndex = 0;
			     	navigateToURL(new URLRequest(vClassRoomUrl), '_blank');
			             break; 			
			    			
			    case v1062:
			        Application.application.RIBox.selectedIndex = 0;
			     	navigateToURL(new URLRequest(EGOVUrl), '_blank');
			             break;
			             
			    case v1063:
			         Application.application.RIBox.selectedIndex = 0;
			         navigateToURL(new URLRequest(ROUrl), '_blank');
			         break;
			         
			    case v1064:
			    	Application.application.RIBox.selectedIndex = 0;
			        navigateToURL(new URLRequest(RIUrl), '_blank');
			        break;
			        
			    
			     
			     			             			
				default:
					if(label.substr(0,Conferencing_ItemLabel.length) == Conferencing_ItemLabel) {
						Univ_LoginId = Univ_LoginId1;
						if ((Univ_LoginId == "anonymous@gmail.com")|| (Univ_LoginId == "guest@guest.com")){
						//	Alert.show(v1076);
							forceLogin();
							Application.application.moreBox.selectedIndex = 0;
							break;
						} 
						
						Application.application.moreBox.selectedIndex = 0;
						currentState='pubconference';
					} else {
						Alert.show('Unknown label:' + label, 'Error');
					}
			}
			
			adminenter = false;
			return;
        }    
  		
  		
  		
	        // getting online users count
	        
	    	public var no_usersTimer:Timer=new Timer(5000,0); 
	        
	        public function initnoofusers():void{
	        	no_usersTimer.addEventListener(TimerEvent.TIMER,getnoofusers_online);
            	no_usersTimer.reset();
            	no_usersTimer.start();
	        }
	        public function getnoofusers_online(t:TimerEvent):void{
	        	getnoofusersHTTP.send();
	        }
	        
	        public function update_noofusers():void{
				var responseMessage:String;
				responseMessage = getnoofusersHTTP.lastResult.rsp.message.toString();
				
				if(responseMessage==''){
					Alert.show('Unable to Load Some data from server !!');
					return;
				}
				
				//Alert.show('no of users',responseMessage);
				
				Conferencing_ItemLabel="Chat[" + responseMessage + "]";
	        }
	        
	        
	        // above code to be included......
           
		[Bindable] public var most_viewed_Xml:XMLList;
		[Bindable] public var cd_general_Xml:XMLList;
		[Bindable] public var cd_oppurtunity_Xml:XMLList;
		[Bindable] public var cd_experts_Xml:XMLList;
		
		[Bindable] public var sixth_videoCategory_Xml:XMLList;
		[Bindable] public var fifth_videoCategory_Xml:XMLList;
		[Bindable] public var fourth_videoCategory_Xml:XMLList;
		
		[Bindable] public  var helpProfileResults:XMLList;
		[Bindable] public  var helpVideoResults:XMLList;
		public var people_name_id:Array	=	new Array();
		[Bindable] public var people_id_name:Array;
		public var Innoxml:XML ;
		public var newxml:XML ;
		[Bindable] public var dataloaded:Boolean	=	false;
		
		// list of edges
		[Bindable] public var edgeList_Xml:XMLList;
	    [Bindable] public var grpList_Xml:XMLList; 
	
		    
       private function sortRating( results:Array): Array {
       	
       	var sortedResults:Array	=new Array();
		var i : int;		
		i=0;
		
			for each (var viddata5:XML in results){
				var rating5:Number=viddata5.@grandrating.toString().charCodeAt(0) - 48;
					if(rating5==5){
							//sortedResults=sortedResults+viddata5;
							sortedResults[i] = viddata5;
							  i = i +1;
								}
					}
		    for each (var viddata4:XML in results){
				var rating4:Number=viddata4.@grandrating.toString().charCodeAt(0) - 48;
					if(rating4 >= 4 && rating4 < 5){
							//sortedResults=sortedResults+viddata4;
							sortedResults[i] = viddata4;
							 i = i + 1;
								}
					}
			for each (var viddata3:XML in results){
				var rating3:Number=viddata3.@grandrating.toString().charCodeAt(0) - 48;
						if(rating3 >= 3 && rating3 < 4){
							sortedResults[i] = viddata3;
							 i = i + 1;
							
							}
					}
			for each (var viddata2:XML in results){
				var rating2:Number=viddata2.@grandrating.toString().charCodeAt(0) - 48;
						if(rating2 >= 2 && rating2 < 3){
							sortedResults[i] = viddata2;
							 i = i + 1;
							
						}
					}
			for each (var viddata1:XML in results){
				var rating1:Number=viddata1.@grandrating.toString().charCodeAt(0) - 48;
					if(rating1 >= 1 && rating1 < 2){
							sortedResults[i] = viddata1;
							 i = i + 1;
							
							}
					}
			for each (var viddata0:XML in results){
						var rating0:Number=viddata0.@grandrating.toString().charCodeAt(0) - 48;
						if(rating0 >= 0 && rating0 < 1){
							sortedResults[i] = viddata0;
							 i = i + 1;
						}
					}
					
				
       	return sortedResults;
       }
		
		
	public	function clone(source:Object):*
	{
    var myBA:ByteArray = new ByteArray();
    myBA.writeObject(source);
    myBA.position = 0;
    return(myBA.readObject());
	}


		/** we load the xml data into 
		 * 	1. graph variable  
		 * 	2. Xml lists ( cd_general_Xml, cd_oppurtunity_Xml and cd_experts_Xml )
		 *  3. arrays ( people_list, videos_list, tags_list )
		 */
		 [Bindable] public var app:Tentube=Tentube(Application.application);
		 
		public function LoadVideos(Innoxml:XML):void{
										
			CursorManager.setBusyCursor();			
			fullGraph=Graph.fromXML(Innoxml, ["Node","Edge","fromID","toID"]);
			
			Univ_LoginItem  = fullGraph.find(Univ_LoginId);
		/* 	Alert.show(Univ_LoginItem.data.@grandscore,"Grand Score"); */
		
		    most_viewed_Xml  = new XMLList();
	 		cd_general_Xml		=	new XMLList();
			cd_oppurtunity_Xml	=	new XMLList();
			cd_experts_Xml		=	new XMLList();
			fourth_videoCategory_Xml = 	new XMLList();
			fifth_videoCategory_Xml = 	new XMLList();
			sixth_videoCategory_Xml = 	new XMLList();
			
			people_list 		= 	new Array();
			videos_list 		= 	new Array();
			tags_list 			= 	new Array();
			
			isDataChanged = true;
			
			//this pushes all the nodes.
			
	   		NodesArray		=	new Array();
	   	//	LinksArray		=	new Array();
	   	//	Node_Id_Uid		=	new Array();
	   		Neighbours		=	new Array();
			people_name_id 	= 	new Array();
			people_id_name = new Array();
			for each(var nodeitem:Item in fullGraph.nodes ){
				var index:String=nodeitem.id;
				Neighbours[index]=new Array();
				// * filling the neighbours array
				for each(var obj:Object in fullGraph._edges[index] ){
					if ( obj.link[1].SourceID==index )
						Neighbours[index].push(obj.link[1].DestID);	
					else
						Neighbours[index].push(obj.link[1].SourceID);	
				}
				
				var t_dataobject:Object;
				switch( (nodeitem.data.@nodetype).toString() ){				
					case 'People':
						people_list.push( nodeitem.data );	
						people_name_id[(nodeitem.data.@name).toString()] = nodeitem.id;
						people_id_name[nodeitem.id.toString()] = (nodeitem.data.@name).toString();
					break;
					
					case 'Videos':	
						videos_list.push( nodeitem.data );							
						break;
					case 'Tags':
						tags_list.push(nodeitem.data);
					break;	
				}
			}
	
			people_list	=	people_list.sortOn("@name",Array.CASEINSENSITIVE);
			videos_list	=	videos_list.sortOn("@name",Array.CASEINSENSITIVE);
			tags_list	=	tags_list.sortOn("@name",Array.CASEINSENSITIVE);
			
		    VideosListByName = clone(videos_list);
		    
	
		    
		    
					  
			// just check out if the video list is not getting changed
			videos_list = videos_list.sortOn("@timesseen",[Array.DESCENDING | Array.NUMERIC]);
		  rating_based = sortRating(videos_list);
			 	 		
				
		sorted_videos_list = videos_list;
		sorted_videos_list = sorted_videos_list.sortOn("@submitttime",Array.DESCENDING);
			
			for each ( var videodata:XML in sorted_videos_list ){
			//for each ( var videodata:XML in videos_list ){
			
				switch( videodata.@category.toString() ){
					case videoCategory_CDLabel:
						cd_general_Xml 		= cd_general_Xml 		+ videodata;
						break;
					case videoCategory_CDOppLabel:
						cd_oppurtunity_Xml 	= cd_oppurtunity_Xml 	+ videodata;
						break;
					case videoCategory_CDExpertsLabel:
						cd_experts_Xml 		= cd_experts_Xml 		+ videodata;
						break;	
					case label_fourth_videoCategory:
					      fourth_videoCategory_Xml = fourth_videoCategory_Xml 	+ videodata;
						break;
					case label_fifth_videoCategory:
					      fifth_videoCategory_Xml = fifth_videoCategory_Xml 	+ videodata;
						break;
					case label_sixth_videoCategory:
					      sixth_videoCategory_Xml = sixth_videoCategory_Xml 	+ videodata;
						break;
						
						
						
				}
			}
			
			
		// have to be careful in the order of sorting as it gets changed becasue objects are getting changed dynamically
		// sorting the  video based on times seen
			timesseen_based = videos_list.sortOn("@timesseen",[Array.DESCENDING | Array.NUMERIC]);
					
			if (defaultPageState != "home"){
						Curr_Video 		= 	cd_general_Xml[0];
						if ( playingvideo == null || playingvideo.data == null ) {
							CursorManager.removeBusyCursor();
							panel2.title 	= videoPlayerM;
							}
						else{
							CursorManager.removeBusyCursor();
							panel2.title 	= playingvideo.data.@name.toString();
							}
			
			}else{  
			  CursorManager.removeBusyCursor();
			}
			
			// changed for login home	
			people_listXMLListCollection=new ArrayCollection(people_list);
		
			//generalVideos.selectedIndex = 0;		
			dataloaded		=	true;
			
			
			// you can set the welcome and reco message here...
			
			
			
			// starting of new code added on 27th march
		//	helpBoxId.showRecommendedVideos();
		//	helpBoxId.showRecommendedProfiles();
			//ending  of new code added on 27nth march 		
	   	
	   		return;
		}
	

		

		
	/**																	**/
	/**					FUNCTIONS RELATED TO FMS 						**/
		
		
		
		// if displayconnstatus == true, Connection success message is displayed to user.
		public var displayconnstatus:Boolean = false;
		public var fms_nc:NetConnection = new NetConnection();
		
		public function fmsConnect():void{
			if ( fms_nc.connected ){
				return;
				//fms_nc.close();
			}
			try{
			fms_nc 					= new NetConnection();
			fms_nc.objectEncoding 	= flash.net.ObjectEncoding.AMF0;
			fms_nc.addEventListener(NetStatusEvent.NET_STATUS,onConnect);
			fms_nc.connect(fmsaddress);
			return;
			}catch (e:Error){
		//		Alert.show("fms " + e.message);
			}
		}
		
		private function onConnect(status:Object) : void{
			//CursorManager.removeBusyCursor();
			try {
				if ( displayconnstatus == true ) {
					switch (status.info.code) {
						case 'NetConnection.Connect.Success':		// connection ok
							Alert.show('Flash media server','Connection succeeded');
							break;
					
						case 'NetConnection.Connect.Closed':
							Alert.show('Flash media server','Connection closed successfully');
							break;
						
						case 'NetConnection.Connect.Failed':
							Alert.show("Failed to connect to Media server. Click YES to try again", "Connection Failure",Alert.YES | Alert.NO, this, fms_alertListener, null, Alert.YES);
							break;
					
						case 'NetConnection.Connect.Rejected':
							Alert.show("Connection request rejected may be due to lack of permissions. Click YES to try again", "Connection Rejected",Alert.YES | Alert.NO, this, fms_alertListener, null, Alert.YES);
							break;
					
						case 'NetConnection.Connect.AppShutdown':
							Alert.show("Could not connect because the specified application is shutting down. Click YES to try again", "Connection Error",Alert.YES | Alert.NO, this, fms_alertListener, null, Alert.YES);
							break;
					
						case 'NetConnection.Connect.InvalidApp':
							Alert.show("The specified application name is invalid. Click YES to try again", "Connection Error",Alert.YES | Alert.NO, this, fms_alertListener, null, Alert.YES);
							break;
					
						case 'NetConnection.Call.BadVersion':
							Alert.show("Packet encoded in unidentified format. Connection failed. Click YES to try again", "Connection Error",Alert.YES | Alert.NO, this, fms_alertListener, null, Alert.YES);
							break;
					
						case 'NetConnection.Call.Failed':
							Alert.show("Bad Server. Connection could not be successful. Please try again. Click YES to try again", "Connection Failure",Alert.YES | Alert.NO, this, fms_alertListener, null, Alert.YES);
							break;
					
						case 'NetConnection.Call.Prohibited':
							Alert.show("An Action Message Format (AMF) operation id prevented for security reasons. Click YES to try again", "Connection Prohibited",Alert.YES | Alert.NO, this, fms_alertListener, null, Alert.YES);
							break;
						
						default:
							Alert.show("Error Type: \"" + status.info.code + "\" Click YES to try again", "Connection Error",Alert.YES | Alert.NO, this,fms_alertListener, null, Alert.YES);
							break;
					}
				}
			}
			catch (e:Error){
				trace("Unable to connect to Media server. Click YES to try again", "Connection Error",Alert.YES | Alert.NO, this,fms_alertListener, null, Alert.YES);
			}
			
			displayconnstatus = false;
			CursorManager.removeBusyCursor();	
			return;			
		}
		
		private function fms_alertListener(eventObj:CloseEvent):void {      
	     	var variables:URLVariables = new URLVariables();
			if (eventObj.detail==Alert.YES) {
				displayconnstatus = true;						   
				fmsConnect();
			}		
			else if(eventObj.detail==Alert.NO) {	
				// do nothing
			}
		}
		
		private function adminreqFault():void{
			trace("adminFault()");
		}
				private function loadpeopleFault():void{
			trace("loadpeopleFault()");
		}
		private function loadinterestsFault():void{
			trace("loadinterestsFault()");
		}
		private function loadCompetencesFault():void{
			trace("loadCompetencesFault()");
		}
		private function setinviteFault():void{
			trace("setinviteFault()");
		}
		private function checkinviteFault():void{
			trace("checkinviteFault()");
		}
		private function changehttpFault():void{
			trace("changehttpFault()");
		}
	
		private function loadnetworkFault():void{
			trace("loadnetworkFault()");
		}
		private function connectToAllFault():void{
			trace("connectToAllFault()");
		}
		
		private function loadconstantsFault():void{
			trace("loadconstantsFault()");
			//Alert.show("Error in loading constants!!");
		}
		private function updatelogFault():void{
			trace("updatelogFault()");
		}
		
		private function onlineUsersFault():void{
			trace("There was a fault in online users load");
  			usersTimer.reset();
  			usersTimer.delay = 7000;
			usersTimer.start(); 

		}
		private function checkMessageFault():void{
			trace("There was an error in checking messages");
			messageTimer.reset();
			messageTimer.delay = 2000;
			messageTimer.start();
		}
		
		private function sendMessageFault():void{
			if(faultCount<5){
				sendMessage.send();
				faultCount++;
			}
			else{
				faultCount=0;
			}
		}
		
		public function removeDupli(tempArr:Array):Array
{
var obj={}, i=tempArr.length,arr:Array=[],t:String;
while(i--)t=tempArr[i],obj[t]=t;for(i in obj)if(i != "-")arr.push(obj[i]);
return arr;
}


public function showRecoAgent():void{
                   	helpPanel.visible=true;
				      helpPanel.height=214;
                      helpBoxId.visible=true;
                      showId.visible=false;
                      closeId.visible=true;
}


	