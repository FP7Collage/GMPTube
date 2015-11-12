package com.NewsActionscript
{
    import flash.display.*;
    import flash.events.*;
    import flash.geom.*;
    import flash.net.*;
    import flash.text.TextFormat;
    import flash.utils.*;
    
    /**
    * Ticker Class
    * 
    * Description:
    * main application file for flash ticker
    * loads config xml builds individual text
    * fields for each character and animates
    * each across the screen
    * 
    * @author Colin Light
    */ 
    public class Displaytext extends Sprite
    {
        private var YPOS:Number = 1;
        
        private static const DEFAULTFPS:Number = 21;
        private static const DEFAULTDISTANCEPERFRAME:Number = 1;
        private static const DEFAULTBACKGROUNDCOLOUR:Number = 0x000000;
        private static const TEXTSPACING:Number = 40;
        private static const FONT:String = "Arial";
        private static const FONTSIZE:Number = 25;
        private static const FONTCOLOUR:Number = 0xff0000;
        private static const FONTBOLD:Boolean = true;
        private static const FONTITALIC:Boolean = false;
        
        private var textSpacingAmount:Number;
        private var _framesPerSecond:Number;
        private var distancePerFrame:Number;
        private var backgroundColour:Number = DEFAULTBACKGROUNDCOLOUR;
        private var fontValue:String = FONT;
        private var fontSizeValue:Number = FONTSIZE;
        private var fontColourValue:Number = FONTCOLOUR;
        private var fontBoldValue:Boolean = FONTBOLD;
        private var fontItalicValue:Boolean = FONTITALIC;
        private var animationTimer:Timer;
        private var titleSpriteList:Array = new Array();
        private var tickerTextFormat:TextFormat;
        private var wordHolder:Sprite = new Sprite();
        private var background:Sprite = new Sprite();
        private var leftSideMask:Sprite = new Sprite();
        private var rightSideMask:Sprite = new Sprite();
        
        /**
        * constructor
        */ 
        public function Displaytext()
        {
            framesPerSecond = Displaytext.DEFAULTFPS;
            distancePerFrame = Displaytext.DEFAULTDISTANCEPERFRAME;
            textSpacingAmount = Displaytext.TEXTSPACING;
            
            //this.addEventListener(Event.ADDED_TO_STAGE, onAddToStage );
          
        }
        
        /**
        * ticker has been add to stage
        */ 
        public function onAddToStage(event:Event):void
        {
            this.removeEventListener(Event.ADDED_TO_STAGE, onAddToStage );
            stage.addEventListener(Event.RESIZE, onResize);
        }
        
        /**
        * set frames per second
        * @param fps:Number
        */ 
        public function set framesPerSecond(fps:Number):void
        {
            _framesPerSecond = 1000/fps;
            if(animationTimer) animationTimer.delay = framesPerSecond;
        }
        /**
        * get frames per second
        */
        public function get framesPerSecond():Number
        {
            return _framesPerSecond;
        }

        /**
        * init application
        */ 
        public function initApp(configUrlStr:String):void
        {
            var configUrl:URLRequest = new URLRequest(configUrlStr);
            var xmlLoader:URLLoader = new URLLoader();
            xmlLoader.dataFormat = URLLoaderDataFormat.TEXT;
            xmlLoader.addEventListener(Event.COMPLETE, onConfigXmlLoaded );
            xmlLoader.addEventListener(IOErrorEvent.IO_ERROR, onErrorLoadingConfig );
            try
            {
                xmlLoader.load(configUrl); 
            }
            catch(error:Error)
            {
                trace("Unable to load URL");
            }
            
        }
        
        /**
        * parse data and build ticker text elements
        */ 
        public function buildTickerView(configXML:XML):void
        {
            //create a timer instance for animtation updates
            animationTimer = new Timer(framesPerSecond);
            animationTimer.addEventListener(TimerEvent.TIMER, onAnimationTimerUpdate );
            
            var configXML:XML = new XML(configXML);
           
            
            distancePerFrame = 1;
           
            var loadFPS:Number = 21;
            framesPerSecond = (loadFPS > 0) ? loadFPS:DEFAULTFPS;
            //textSpacingAmount = Number(configXML.tickerconfig.items.@spacing);
            textSpacingAmount = 40;
            fontValue = "Times New Roman";
            fontSizeValue =15;
            fontColourValue = 0x800080;
            trace(configXML.tickerconfig.textformat.@textColour);
            fontBoldValue = true;
            fontItalicValue = false;
            backgroundColour = 0xffffff;
            var partnerList:XMLList = configXML.item;
            trace("partnerList: " + partnerList.length());
            //create a background colour sprite
            background.graphics.beginFill(backgroundColour);
            background.graphics.drawRect(0, 0, stage.width , stage.height);
            this.addChildAt(background, 0);
            //create ticker text format
            tickerTextFormat = new TextFormat(fontValue, fontSizeValue, fontColourValue, 
                                                                    fontBoldValue, fontItalicValue);
           
            
           
            var xpos:Number = 0;
            var subDomain:String = configXML.partnerProfile.subdomain;
            //http:// + subdomain + friction.tv + linkurl
            
           
            for(var i:int = 0; i<partnerList.length(); i++)
            {	
            	
            	
                //create each ticker link
                var linkXML:XML = partnerList[i];
                if(linkXML.isactive == 0)
                {
                	break;
                }
				var rdFormat:TextFormat = new TextFormat("Times New Roman", 11, 0x000000);
				var rdFormat1:TextFormat = new TextFormat("Times New Roman", 13, 0xffa500);
				
                var titleText:String = linkXML.title;
                //var link:String = "http://www.friction.tv/" ;
                var Text2:String = linkXML.desc;
                var Textsub:String = Text2.substr(0,80);
                var Textsub2:String = Text2.substr(40,80);
                //add a space on for italic text
                if (fontItalicValue) titleText = titleText + " ";
                //var textItem:TickerTextView = new TickerTextView(titleText, link, tickerTextFormat, Text2);
                var textItem:Displaytextview = new Displaytextview(titleText, tickerTextFormat, Text2);
                var textItem2:Displaytextview = new Displaytextview(Textsub, rdFormat1, Text2);
                
       
                var textItem3:Displaytextview = new Displaytextview( "....Click to read More", rdFormat, Text2);
                
                textItem.addEventListener(Displaytextview.MOUSEOVERLINK, onMouseOverLink );
                textItem.addEventListener(Displaytextview.MOUSEOUTLINK, onMouseLeaveLink );
                textItem2.addEventListener(Displaytextview.MOUSEOVERLINK, onMouseOverLink );
                textItem2.addEventListener(Displaytextview.MOUSEOUTLINK, onMouseLeaveLink );
                textItem3.addEventListener(Displaytextview.MOUSEOVERLINK, onMouseOverLink );
                textItem3.addEventListener(Displaytextview.MOUSEOUTLINK, onMouseLeaveLink );
                
                textItem.x = 5;
                textItem2.x = 5;
                textItem3.x = 5;
                
                
                textItem.y = YPOS;
                YPOS = YPOS + textItem.height;
                textItem2.y = YPOS ;
                YPOS = YPOS + textItem2.height;
                textItem3.y = YPOS ;
                YPOS = YPOS + textItem3.height ; 
                YPOS =YPOS +20;
                
               
                this.addChild(textItem);
                this.addChild(textItem2);
                this.addChild(textItem3);
                //this.addChild(textItem2);
                titleSpriteList.push(textItem);
                titleSpriteList.push(textItem2);
                titleSpriteList.push(textItem3);
                //add ticker tool tip to each text item
                //toolTipper.addToolTip(textItem,"Read more", toolTipFormat);
                
            }
            this.addChild(wordHolder);
            //this.addChild(toolTipper);
            
            
            //start animation
            animationTimer.start();
        }
        
        /**
        * resize events, update background
        */
        private function onResize(event:Event):void
        {
            background.width = stage.stageWidth;
            background.height = stage.stageHeight;
        } 
        
        /**
        * config loaded, parse into an xml object and build the ticker
        */ 
        private function onConfigXmlLoaded(event:Event):void
        {
            var configXML:XML = new XML(event.target.data );
            
            trace(configXML);
            buildTickerView(configXML);
        }
        
        /**
        * error loading config xml
        */
        private function onErrorLoadingConfig(event:ErrorEvent):void
        {
            trace( " onErrorLoadingConfig: " + event);
        }
        
        /**
        * animation timer update event, updates position of ticker text
        */ 
        private function onAnimationTimerUpdate(event:TimerEvent):void
        {
            
            for(var i:int = 0; i<titleSpriteList.length; i++)
            {
                var titleText:Displaytextview = titleSpriteList[i];
                if(titleText.y <=0)
                {if(YPOS <300)
                {titleText.y = 300;}
                else
                {
                titleText.y = YPOS;
                }}
                
                titleText.y = titleText.y - distancePerFrame;
                
                //if the letter has moved off the edge of the screen
                if((titleText.x + titleText.width) < 15)
                
                {
                    if(i==0)
                    {
                       titleText.x = this.width; 
                    }else{
                       titleText.x = titleSpriteList[i-1].x + titleSpriteList[i-1].width + 
                                                                                textSpacingAmount; 
                    }
                }
            }
        }
        
        /**
        * mouse over link text, stop animation
        */ 
        private function onMouseOverLink(event:Event):void
        {
            animationTimer.stop();
        }
        
        /**
        * mouse leave link text, restart animation
        */ 
        private function onMouseLeaveLink(event:Event):void
        {
            animationTimer.start();
        }
    }
}
