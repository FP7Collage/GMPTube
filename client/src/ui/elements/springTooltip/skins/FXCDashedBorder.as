/*
    FXCDashedBorder
    
    version 0.1
    Dashed border skin, making use of GraphicsUtils-build build by Ely GreenField
    
    Created by Maikel Sibbald
    info@flexcoders.nl
    http://labs.flexcoders.nl
    
    Free to use.... just give me some credit
*/
package ui.elements.springTooltip.skins{
	import flash.display.CapsStyle;
	
	import mx.graphics.Stroke;
	import mx.skins.RectangularBorder;
	
	import qs.utils.GraphicsUtils;

	public class FXCDashedBorder extends RectangularBorder{
		public var pattern:Array = [4,4,4,4];
		public var points:Array = [];
		
		override protected function updateDisplayList(unscaledWidth:Number, unscaledHeight:Number):void{
			super.updateDisplayList(unscaledWidth, unscaledHeight);
			
			var borderThickness:Number 	= this.getStyle("borderThickness");
			var borderColor:Number 		= this.getStyle("borderColor");
			var backgroundColor:Number 	= this.getStyle("backgroundColor");
			var backgroundAlpha:Number 	= this.getStyle("backgroundAlpha");
			var correction:Number 		= borderThickness/4;
			
			points.push(
				{x:0,y:0},
				{x:this.width-correction,y:0},
				{x:this.width-correction,y:this.height-correction},
				{x:0,y:this.height-correction},
				{x:0,y:0}
				);
			
			this.graphics.clear();
			this.graphics.beginFill(backgroundColor);
			this.graphics.drawRect(0,0,this.width,this.height);
			this.graphics.endFill();
			this.alpha = backgroundAlpha;	
			
			if(borderThickness > 0){
				GraphicsUtils.drawDashedPolyLine(this.graphics,new Stroke(borderColor,borderThickness,1,true,"normal",CapsStyle.NONE),pattern,points);
			}
		}
		
	}
}