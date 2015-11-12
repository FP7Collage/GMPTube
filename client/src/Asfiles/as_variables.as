

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
			
			import com.adobe.flex.extras.controls.springgraph.Item;
			
			import flash.display.*;
			import flash.events.*;
			import flash.net.*;
			import flash.utils.Timer;
			
			import mx.effects.easing.*;
			
		
			[Bindable] public var Univ_LoginId:String = new String();
			
			[Bindable] public var Univ_LoginItem:Item = new Item();

			public var minuteTimer:Timer = new Timer(5000, 1);			
			public var fmsaddress:String = new String();
		
			[Bindable] public var adminenter:Boolean	=	false;
			[Bindable] public var adminMode:Boolean     =   false;
			[Bindable] public var userplaying:Boolean	=	false;
			
			[Bindable] public var adminstatus:Boolean	=	false;
			
			[Bindable] public var passwd:String;
			[Bindable] public var id_selectedbyeadmin:String;
    		
    		    					
			[Bindable] public var EnableCompetences:Boolean;
			[Bindable] public var Styling2:String;
			/**************************************/
			/** 	VARIABLES FOR NEWS THING 	 **/
			/**************************************/
    		import com.NewsActionscript.Updatingnews;

 			import flash.display.*;
            import flash.events.*;
            import flash.net.*;
            import flash.text.TextField;
            import flash.text.TextFieldAutoSize;
            import flash.text.TextFormat;
            import flash.utils.*;
            import mx.rpc.events.ResultEvent;
			import mx.rpc.http.mxml.HTTPService;
            
            import mx.containers.ControlBar;
    		import mx.containers.Panel;
    		import mx.containers.Canvas;
    		import mx.containers.VBox;
    		import mx.controls.Button;
    		import mx.controls.Label;
   			import mx.controls.Spacer;
    		import mx.core.*;
    		import mx.managers.PopUpManager;
			import mx.controls.Alert;
			import Newsdisplay.*;
			
    	//	[Bindable]public var app:Tentube	=	Tentube(Application.application);
    		
    		[Bindable]
    		public var opennews:Updatingnews = new Updatingnews("a","s","s",true);	
    		
    		[Bindable]  public var boxy:Panel = new Panel(); 
    		[Bindable]
    		public var id_tck:String;
    		[Bindable]
    		public var title_tck:String;
    		[Bindable]
    		public var desc_tck:String;
    		[Bindable]
    		public var isactive_tck:Boolean;
    		//public var Panup:Panelupdator = new Panelupdator();
    		public var Panup:Canvas = new Canvas();
    		public var newsupdater:updatenews = new updatenews();	
    		[Bindable] public var newsadminenable:Boolean;
    		[Bindable] public var boolstylereturn:String = "./ui/themes/obsidian/inno.css";
    		
    		/**************************************/
			/** 	VARIABLES FOR NEWS THING 	 **/
			/**************************************/	
			
			
		public var pictClick:Profile_click = new Profile_click();
			public function openPictureDisp():void
            {
            	
            var app11:Tentube=Tentube(Application.application);
					if (! app11.isLoggedIn()){
 			      			app11.forceLogin();
 			      			return;
 						}
			

            	
            pictClick = Profile_click(PopUpManager.createPopUp(this,Profile_click,false));
            pictClick.x = 250;
            pictClick.y = 200;
            }
            public function Closethispop3(evt:MouseEvent):void{  
			PopUpManager.removePopUp(pictClick);
			pictClick.vcLocal.attachCamera(null);
		
			} 
	
  				/**************************************/
			/** 	VARIABLES FOR DISPLAY THING 	 **/
			/**************************************/	
			private function islab1():Boolean{
				if(Application.application.parameters.tubeName=="InnoTube")
					return true;
				else
					return false;
		}
			public var dispnode:DispRelationships = new DispRelationships();
			public function openPopDisp():void
            {
            dispnode = DispRelationships(PopUpManager.createPopUp(this,DispRelationships,false));
            dispnode.x = 25;
            dispnode.y = 200;
            
            
            if(islab1())
            dispnode.height = 500;
            else
            dispnode.height = 350;
            }
			public function Closethispop2(evt:MouseEvent):void{  
			PopUpManager.removePopUp(this);
		
			} 
			//public var app:Tentube=Tentube(Application.application);
  			public var usernodelist:XML = new XML(<Node state='unchecked' name="Node"  />);
  			
  			public var knowField:String = "Knows";
			public var submitField:String = "Has submitted";
			public var seenField:String = "Has seen";
			public var tagsField:String = "Has tags";
			public var prevField:String = "Is previous version";
			public var connectedField:String = "Is Connected To";
			public var interestField:String = "Has Interest";
			public var givenField:String = "Has Given";
			public var involvedField:String = "Has been Involved in";
			public var commentedField:String = "Has Commented";
			public var relatesField:String = "Relates To";
			public var ratesField:String = "Has Rated";
			
			
			public  var relnarray:Array= new Array();	
			
			
			/**************************************/
			/** 	VARIABLES FOR DISPLAY THING 	 **/
			/**************************************/
			 public function getTagname(tagid:Object):String
        {		var tagstring:String = new String("");
        		for each(var node1:XML in Application.application.Innoxml.descendants("Nodes"))
        		for each(var node2:XML in node1.descendants("Node"))
   			{	
   				var str:String = tagid.@interests.toString();
   				var intarray:Array 	= 	str.split(',');
   				for each(var str1:String in intarray)
   				{
   				if(str1 == node2.@id.toString())
   				{	
   					if(tagstring =="")
   					tagstring = node2.@name.toString();
   					else
   					tagstring = tagstring + ',' + node2.@name.toString();
   				
   				}
   				}				
   			}
   			return tagstring;
        }
        public function getTagname2(tagid:Object):String
        {		var tagstring:String = new String("");
        		for each(var node1:XML in Application.application.Innoxml.descendants("Nodes"))
        		for each(var node2:XML in node1.descendants("Node"))
   			{	
   				var str:String = tagid.@tags.toString();
   				var intarray:Array 	= 	str.split(',');
   				for each(var str1:String in intarray)
   				{
   				if(str1 == node2.@id.toString())
   				{	
   					if(tagstring =="")
   					tagstring = node2.@name.toString();
   					else
   					tagstring = tagstring + ',' + node2.@name.toString();
   				
   				}
   				}				
   			}
   			return tagstring;
        }
        public function getTagname3(tagid:Object):String
        {		var tagstring:String = new String("");
        		for each(var node1:XML in Application.application.Innoxml.descendants("Nodes"))
        		for each(var node2:XML in node1.descendants("Node"))
   			{	
   				var str:String = tagid.int.toString();
   				var intarray:Array 	= 	str.split(',');
   				for each(var str1:String in intarray)
   				{
   				if(str1 == node2.@id.toString())
   				{	
   					if(tagstring =="")
   					tagstring = node2.@name.toString();
   					else
   					tagstring = tagstring + ',' + node2.@name.toString();
   				
   				}
   				}				
   			}
   			return tagstring;
        }
        public function colorreturn():uint{
        	if(Styling2 == 'innotube2')
        	return 0x000000;
        	else return 0xffffff;
        
        }
        /* public function stylereturn():String{
        	if(Styling2 == 'innotube2')
        	return "./ui/themes/obsidian/inno.css";
        	else
        	return "./ui/themes/obsidian/main.css";
        
        } */
        public function colorreturn2():uint{
        	if(Styling2 == 'innotube2')
        	{return 0x000000 ;}
        	else 
        	return 0xffffff;
        
        }
