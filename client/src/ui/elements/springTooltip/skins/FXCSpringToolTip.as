/*
    FXCSpringToolTip
    
    HTML supported tooltip, I just love it when code comes together
    Now with the spring tooltip feature;
    
    version 0.1
    -	spring feature added
     
    Created by Maikel Sibbald
    info@flexcoders.nl
    http://labs.flexcoders.nl
    
    Free to use.... just give me some credit
*/
package ui.elements.springTooltip.skins{
    
    import flash.events.Event;
    
    import mx.controls.ToolTip;
    import mx.core.Application;
    import mx.core.UITextField;
    import mx.skins.halo.ToolTipBorder;

    public class FXCSpringToolTip extends FXCHTMLToolTip{
    	private var relx:Number;
		private var rely:Number;
		private var vx:Number			= 0;
		private var vy:Number			= 0;
		
		public var spring:Number 		= 0.07;
		public var friction:Number 		= 0.9;
	
       	public function FXCSpringToolTip():void{
        	super();
        	this.setSpring();
        }
        
        private function setSpring():void {
			this.addEventListener(Event.ENTER_FRAME, this.updateSpring);
		}
		
		private function updateSpring(event:Event):void {
			if(this.parent != null){
				var mouseX:Number = Application.application.mouseX;
				var mouseY:Number = Application.application.mouseY;
				
				var mouseObject:Object = new Object();
				mouseObject.x = mouseX;
				mouseObject.y = mouseY;
				
				this.springTo(this.parent as ToolTip, mouseObject);
			}
		} 
		
		public function springTo(obj1:ToolTip, obj2:Object):void{
			var screenWidth:Number = Application.application.screen.width;
			var screenHeight:Number = Application.application.screen.height;
			
			var ax:Number = (obj2.x - obj1.x) * spring;
			var ay:Number = (obj2.y - obj1.y) * spring;
			
			this.vx += ax;
			this.vy += ay;
			this.vx *= friction;
			this.vy *= friction;
			
			var xBounds:Boolean = (((obj1.x + parent.width) < screenWidth)&&(obj1.x + vx > 0));
			var yBounds:Boolean = (((obj1.y + parent.height) < screenHeight)&&(obj1.y + vy > 0));
			
			if (xBounds && yBounds) {
				obj1.x += vx;
				obj1.y += vy;
			} else if (yBounds) {
				obj1.y += vy;
				relx = obj2.x - parent.x;
			} else if (xBounds) {
				obj1.x += vx;
				rely = obj2.y - parent.y;
			} else {
				relx = obj2.x - parent.x;
				rely = obj2.y - parent.y;
			}
		}

    }
}