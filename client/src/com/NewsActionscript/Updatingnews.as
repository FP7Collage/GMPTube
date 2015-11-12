package com.NewsActionscript
{
    import flash.display.*;
    import flash.events.*;
    import flash.net.*;
    import flash.utils.*;
    
    import mx.containers.ControlBar;
    import mx.containers.HBox;
    import mx.containers.Panel;
    import mx.containers.VBox;
    import mx.controls.Button;
    import mx.controls.Label;
    import mx.controls.Spacer;
    import mx.core.*;
    import mx.managers.PopUpManager;
    import Newsdisplay.*;
   	       
    
    /**
    * TickerTextView Class
    * 
    * Description:
    * creates a text field to hold link text and
    * opens a link url 
    * 
    * @author Colin Light
    */ 
    
    public class Updatingnews extends Sprite
    {
        public static const MOUSEOVERLINK:String = "mouseoverlink";
        public static const MOUSEOUTLINK:String = "mouseoutlink";
        private var idstr:String;
        private var titlestr:String;
        private var descstr:String;
        private var active1:Boolean;
        
        private var panel:Panel = new Panel();
		private    var vb:VBox = new VBox();
		private var label:Label = new Label();
		private var uic:UIComponent = new UIComponent();
		public var box:HBox = new HBox();
         
         // private    var textInput:TextInput = new TextInput();

        private    var cb:ControlBar = new ControlBar();
        private    var s:Spacer = new Spacer();
        private    var btnupdate:Button = new Button();
        private    var btndelete:Button = new Button();
        public var deletenews:Newsdeleter = new Newsdeleter ();
         
        public var newsAgent:DisplayNews = new DisplayNews();	        
		
             
        /**
        * Constructor
        * 
        * @param text:String -link textField text
        * @param linkUrl:String
        * @param textFormat:TextFormat
        */ 
        public function Updatingnews(id:String, title:String, description:String, active:Boolean)
        {
        		idstr = id;
        		titlestr = title;
        		descstr = description;
        		active1 = active;
        
        		var btn:Button = new Button();
                var btndelete:Button = new Button();
                
                btn.label = "Edit";
                btndelete.label = "Delete";
                box.addChild(btn);
                box.addChild(btndelete);
                this.addChild(box);
        		btn.addEventListener(MouseEvent.CLICK, newsupdate1);
                btndelete.addEventListener(MouseEvent.CLICK, newsdelete1);
        }
            //build title
       
       
        private function newsupdate1(event:MouseEvent):void 
        
        {
            	Application.application.id_tck = idstr;
            	Application.application.title_tck = titlestr;
            	Application.application.desc_tck = descstr;
            	Application.application.isactive_tck = active1;
            	Application.application.newsupdater = updatenews(PopUpManager.createPopUp(Application.application.upperBox,updatenews,false));
            	Application.application.newsupdater.alpha = 2;
               //Application.application.newsupdate(idstr, titlestr, descstr);
               
             
         } 
		
		
		
		private function newsdelete1(event:MouseEvent):void 
        {
            	Application.application.id_tck = idstr;
            	Application.application.title_tck = titlestr;
            	deletenews = Newsdeleter(PopUpManager.createPopUp(Application.application.upperBox,Newsdeleter,false));
            	deletenews.x = 200;
            	deletenews.y = 350;
               
             
            } 
            public function newsopen(event:MouseEvent):void
            {	
            	newsAgent = DisplayNews(PopUpManager.createPopUp(Application.application.upperBox,DisplayNews,false));
            	
            	newsAgent.width = 545;
				newsAgent.height= 380;
				newsAgent.x = 30;
				newsAgent.y = 110;
				newsAgent.horizontalScrollPolicy = "off";
                newsAgent.onCreated(event);
               
            } 
    }
}
