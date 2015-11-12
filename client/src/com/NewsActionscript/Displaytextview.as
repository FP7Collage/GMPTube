package com.NewsActionscript
{

    
    import flash.display.Sprite;
    import flash.events.Event;
    import flash.events.MouseEvent;
    import flash.net.*;
    import flash.text.TextField;
   
    import flash.text.TextFieldAutoSize;
    import flash.text.TextFormat;
    
    import mx.containers.ControlBar;
    import mx.containers.Panel;
    import mx.containers.VBox;
    import mx.controls.Button;
    import mx.controls.Label;
    import mx.controls.TextArea
    import mx.controls.Spacer;
    import mx.core.*;
    import mx.managers.PopUpManager;
	
   	       
    
    /**
    * TickerTextView Class
    * 
    * Description:
    * creates a text field to hold link text and
    * opens a link url 
    * 
    * @author Colin Light
    */ 
    
    public class Displaytextview extends Sprite
    {
        public static const MOUSEOVERLINK:String = "mouseoverlink";
        public static const MOUSEOUTLINK:String = "mouseoutlink";
        private var link:String;
        
        private var panel:Panel = new Panel();
		private    var vb:VBox = new VBox();
		private var label:Label = new Label();
		private var uic:UIComponent = new UIComponent();
         
         // private    var textInput:TextInput = new TextInput();

          private    var cb:ControlBar = new ControlBar();
          private    var s:Spacer = new Spacer();
          private    var b1:Button = new Button();
          private var paneldisp:Panel = new Panel();
         // private var testPopUp:PanelPopUp = new PanelPopUp();
		 //private var testPopUp:TickerFormatTester;
                
		
             
        /**
        * Constructor
        * 
        * @param text:String -link textField text
        * @param linkUrl:String
        * @param textFormat:TextFormat
        */ 
        public function Displaytextview(text:String, textFormat:TextFormat, text2:String)
        {
            //build title
            var linkText:TextField = new TextField();
            linkText.mouseEnabled = false;
            linkText.multiline = true;
            linkText.width = 20;
           
            linkText.text = text;
            linkText.x = 0;
            linkText.y = 0;
            linkText.autoSize = TextFieldAutoSize.LEFT;
            linkText.selectable = false;
            linkText.wordWrap = false;
            linkText.setTextFormat(textFormat);
            linkText.cacheAsBitmap;
            this.addChild(linkText);
            label.text = text2;
            this.addEventListener(MouseEvent.CLICK, createPopUp );
            this.addEventListener(MouseEvent.MOUSE_OVER, onMouseOver );
            this.addEventListener(MouseEvent.MOUSE_OUT, onMouseLeave );
           	this.buttonMode = true;
           //uic.addChild(this);
            super();
            
        }
        
        
        
        /**
        * Pop up code(new)
        */ 
		
            
            private function createPopUp(event:MouseEvent):void {
             
            	var b1:Button = new Button();
            	var labelnews:Label = new Label();
            	var txtarea:TextArea = new TextArea();
            	txtarea.text = label.text;
            	labelnews = label;
            	b1.label = "Close";
            	//label.setStyle("color", blue);
            	b1.addEventListener(MouseEvent.CLICK, closePopUp);
            	
            	if(paneldisp.isPopUp == true)
            	{
            		paneldisp.removeAllChildren();
            		
              	
              	paneldisp.x = 300;
            	paneldisp.y = 300;
            	paneldisp.height = 500;
            	paneldisp.width = 550;
            	txtarea.width = paneldisp.width - 20;
            	txtarea.height = 420;
            	paneldisp.title = "News Description";
            	paneldisp.alpha = 2;
            	paneldisp.horizontalScrollPolicy = "off";
            	paneldisp.addChild(txtarea);
            	paneldisp.addChild(b1);
            	}
            	
        		else if(paneldisp.isPopUp == false)
        		{
        		
            	paneldisp = Panel(PopUpManager.createPopUp(Application.application.upperBox,Panel,true));
            	paneldisp.x = 300;
            	paneldisp.y = 300;
            	paneldisp.height = 500;
            	paneldisp.width = 550;
            	txtarea.width = paneldisp.width - 20;
            	txtarea.height = 420;
            	paneldisp.title = "News Description";
            	paneldisp.alpha = 2;
            	paneldisp.horizontalScrollPolicy = "off";
            	paneldisp.addChild(txtarea);
            	paneldisp.addChild(b1);
            	//Application.application.popupmaker(label);
               }}
               
               
        	private function closePopUp(event:MouseEvent):void {
              
             paneldisp.removeAllChildren();
             PopUpManager.removePopUp(paneldisp);	
            }
        /**
        * mouse rolled over item
        */
        private function onMouseOver(event:MouseEvent):void
        {
            this.dispatchEvent(new Event(Displaytextview.MOUSEOVERLINK));
        } 
        
        /**
        * mouse rolled out
        */
        private function onMouseLeave(event:MouseEvent):void
        {
            this.dispatchEvent(new Event(Displaytextview.MOUSEOUTLINK));
        } 
        
    }
}