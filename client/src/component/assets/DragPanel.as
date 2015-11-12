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

package component.assets
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	
	import mx.containers.TitleWindow;
	import mx.core.SpriteAsset;
	import mx.core.UIComponent;
	import mx.events.FlexEvent;

	public class DragPanel extends TitleWindow
	{
		// Add the creationCOmplete event handler.
		public function DragPanel()
		{
			super();
			addEventListener(FlexEvent.CREATION_COMPLETE, creationCompleteHandler);
		}
		
		private var myTitleBar:UIComponent;
					
		private function creationCompleteHandler(event:Event):void
		{
			myTitleBar = titleBar;
			this.titleTextField.textColor = 0xFFFFFF;
			this.x=Math.random()*200;
			this.y=Math.random()*200;			
			// Add the resizing event handler.	
			addEventListener(MouseEvent.MOUSE_DOWN, resizeHandler);
			myTitleBar.addEventListener(MouseEvent.MOUSE_DOWN, tbMouseDownHandler);
			myTitleBar.addEventListener(MouseEvent.MOUSE_UP, tbMouseUpHandler);
		//	this.setStyle("roundedBottomCorners",false);
		//	this.styleChanged("roundedBottomCorners");
			
			
		}
		
		private var xOff:Number;
       	private var yOff:Number;
            
		private function tbMouseDownHandler(event:MouseEvent):void {
			
                xOff = event.currentTarget.mouseX;
                yOff = event.currentTarget.mouseY;
                parent.addEventListener(MouseEvent.MOUSE_MOVE, tbMouseMoveHandler);
                parent.setChildIndex(this,parent.numChildren-1); 
                
            }
            
       private function tbMouseMoveHandler(event:MouseEvent):void {
			
			// Compensate for the mouse pointer's location in the title bar.
			
			var tempX:int = parent.mouseX - xOff;
			x = tempX;
			
			var tempY:int = parent.mouseY - yOff;
			y = tempY;	
					
        }
        
        private function tbMouseUpHandler(event:MouseEvent):void {

           parent.removeEventListener(MouseEvent.MOUSE_MOVE, tbMouseMoveHandler);    
        
        }
		
		
		

		protected var minShape:SpriteAsset;
		protected var restoreShape:SpriteAsset;

		override protected function createChildren():void
		{
				super.createChildren();
			
			// Create the SpriteAsset's for the min/restore icons and 
			// add the event handlers for them.
			minShape = new SpriteAsset();
			minShape.addEventListener(MouseEvent.MOUSE_DOWN, minPanelSizeHandler);
			titleBar.addChild(minShape);

			restoreShape = new SpriteAsset();
			restoreShape.addEventListener(MouseEvent.MOUSE_DOWN, restorePanelSizeHandler);
			titleBar.addChild(restoreShape);
		}
			
		override protected function updateDisplayList(unscaledWidth:Number, unscaledHeight:Number):void
		{
			super.updateDisplayList(unscaledWidth, unscaledHeight);
			
			// Create invisible rectangle to increase the hit area of the min icon.
			minShape.graphics.clear();
			minShape.graphics.lineStyle(0, 0, 0);
			minShape.graphics.beginFill(0xFFFFFF, 0.0);
			minShape.graphics.drawRect(unscaledWidth - 52, 10, 8, 8);

			// Draw min icon.
			minShape.graphics.lineStyle(2,0xFFFFFF);
			minShape.graphics.beginFill(0xFFFFFF, 0.0);
			minShape.graphics.drawRect(unscaledWidth - 52, 16, 8, 2);
				
			// Draw restore icon.
			restoreShape.graphics.clear();
			restoreShape.graphics.lineStyle(2,0xFFFFFF);
			restoreShape.graphics.beginFill(0xFFFFFF, 0.0);
			restoreShape.graphics.drawRect(unscaledWidth - 37, 10, 8, 8);
			restoreShape.graphics.moveTo(unscaledWidth - 37, 13);
			restoreShape.graphics.lineTo(unscaledWidth - 29, 13);

			// Draw resize graphics if not minimzed.				
			//this.graphics.clear()
			if (isMinimized == false)
			{
				this.graphics.lineStyle(2,0xFFFFFF);
				this.graphics.moveTo(unscaledWidth - 11, unscaledHeight - 6)
				this.graphics.curveTo(unscaledWidth - 8, unscaledHeight - 8, unscaledWidth - 6, unscaledHeight - 11);						
				this.graphics.moveTo(unscaledWidth - 11, unscaledHeight - 9)
				this.graphics.curveTo(unscaledWidth - 10, unscaledHeight - 10, unscaledWidth - 9, unscaledHeight - 11);						
			}
		}
					
		private var myRestoreHeight:int;
		private var isMinimized:Boolean = false; 
					
		// Minimize panel event handler.
		private function minPanelSizeHandler(event:Event):void
		{
			if (isMinimized != true)
			{
				myRestoreHeight = height;	
				height = titleBar.height;
				isMinimized = true;	
				// Don't allow resizing when in the minimized state.
				removeEventListener(MouseEvent.MOUSE_DOWN, resizeHandler);
			}				
		}
		
		// Restore panel event handler.
		private function restorePanelSizeHandler(event:Event):void
		{
			if (isMinimized == true)
			{
				height = myRestoreHeight;
				isMinimized = false;	
				// Allow resizing in restored state.				
				addEventListener(MouseEvent.MOUSE_DOWN, resizeHandler);
			}
		}

		// Define static constant for event type.
		//public static const RESIZE_CLICK:String = "resizeClick";

		// Resize panel event handler.
		private var origWidth:int;
		private var origHeight:int;
		
		public  function resizeHandler(event:MouseEvent):void
		{
			// Determine if the mouse pointer is in the lower right 7x7 pixel
			// area of the panel. Initiate the resize if so.
			
			// Lower left corner of panel
			var lowerLeftX:Number = x + width; 
			var lowerLeftY:Number = y + height;
				
			// Upper left corner of 7x7 hit area
			var upperLeftX:Number = lowerLeftX-30;
			var upperLeftY:Number = lowerLeftY-30;
				
			// Mouse positionin Canvas
			var panelRelX:Number = event.localX + x;
			var panelRelY:Number = event.localY + y;

			// See if the mousedown is in the lower right 7x7 pixel area
			// of the panel.
			if (upperLeftX <= panelRelX && panelRelX <= lowerLeftX)
			{
				if (upperLeftY <= panelRelY && panelRelY <= lowerLeftY)
				{	
				
					
					event.stopPropagation();		
					
					origWidth = width;
					origHeight = height;
					xOff = parent.mouseX;
                	yOff = parent.mouseY;
					parent.addEventListener(MouseEvent.MOUSE_MOVE, resizePanel);
					parent.addEventListener(MouseEvent.MOUSE_UP, stopResizePanel);
					
				}
			}				
		}
		
		private function resizePanel(event:MouseEvent):void {
			
			if ((origWidth + (parent.mouseX - xOff)) > 250){
				width = origWidth + (parent.mouseX - xOff);	
			}
			
			if ((origHeight + (parent.mouseY - yOff)) > titleBar.height){
				height = origHeight + (parent.mouseY - yOff);
			}
					
		}
		
		private function stopResizePanel(event:MouseEvent):void {
			parent.removeEventListener(MouseEvent.MOUSE_MOVE, resizePanel);
				parent.removeEventListener(MouseEvent.MOUSE_UP, stopResizePanel);
		}
		
				
	}
}