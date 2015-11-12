package ui.elements.spinner{
	import mx.containers.Canvas;
	import mx.events.FlexEvent;
	import flash.utils.Timer;
	import flash.events.TimerEvent;
	import mx.effects.Fade;
	import mx.core.UIComponent;
	import mx.styles.StyleManager;
	import mx.styles.CSSStyleDeclaration;
	
	
	/**
	 * Creates a spinning "loader" component that is sort of an indeterminate progress bar.
	 * @author jhawkes
	 * 
	 */
	public class Spinner extends UIComponent {
		private var fadeTimer:Timer;
		private var _isPlaying:Boolean = false;
		
		private var _tickColor:uint = 0x33CC66;
		private var _numTicks:int = 16;
		private var _size:Number = 20;
		private var _tickWidth:Number = 2;
		private var _speed:int = 1000;
		[Bindable] public var fadeSpeed:int = -1;
		
		
		public function Spinner() {
			super();
			
			createChildren();
		}

		/**
		 * Override the createChildren method to draw all the little ticks.
		 * 
		 */		
		override protected function createChildren():void {
			super.createChildren();
			
			var radius:Number = size / 2;
			var angle:Number = 2 * Math.PI / _numTicks; // The angle between each tick
			var tickWidth:Number = (_tickWidth != -1) ? _tickWidth : size / 10;
			
			var currentAngle:Number = 0;
			for (var i:int = 0; i < _numTicks; i++) {
				
				var xStart:Number = radius + Math.sin(currentAngle) * ((_numTicks + 2) * tickWidth / 2 / Math.PI);
				var yStart:Number = radius - Math.cos(currentAngle) * ((_numTicks + 2) * tickWidth / 2 / Math.PI);
				var xEnd:Number = radius + Math.sin(currentAngle) * (radius - tickWidth);
				var yEnd:Number = radius - Math.cos(currentAngle) * (radius - tickWidth);
				
				var t:Tick = new Tick(xStart, yStart, xEnd, yEnd, tickWidth, tickColor);
					t.alpha = 0.1;
					
				this.addChild(t);
				
				currentAngle += angle;
			}
		}
		
		/**
		 * Pause the animation, redraw the ticks with updated properties, and start the animation again.
		 */
		protected function redraw():void {
			// Find out whether it's playing so we can restart it later if we need to
			var wasPlaying:Boolean = _isPlaying;
			
			// stop the spinning
			stop();
			
			// Remove all children
			for (var i:int = numChildren - 1; i >= 0; i--) {
				removeChildAt(i);
			}
			
			// Re-create the children
			createChildren();
			
			// Start the spinning again if it was playing when this function was called.
			if (wasPlaying) {
				play();
			}
		}
		
		/**
		 * Begin the circular fading of the ticks.
		 */
		public function play():void {
			if (! _isPlaying) {
				fadeTimer = new Timer(speed / _numTicks, 0);
				// Anonymous functions are especially useful as simple event handlers
				fadeTimer.addEventListener(TimerEvent.TIMER, function (e:TimerEvent):void {
					var tickNum:int = int(fadeTimer.currentCount % _numTicks);
					
					var tickFade:Fade = new Fade(getChildAt(tickNum));
						tickFade.alphaFrom = 1.0;
						tickFade.alphaTo = 0.1;
						tickFade.duration = (fadeSpeed != -1) ? fadeSpeed : speed * 6 / 10;
						tickFade.play();
				});
				fadeTimer.start();
				_isPlaying = true;
			}
		}
		
		/**
		 * Stop the spinning.
		 */
		public function stop():void {
			if (fadeTimer.running) {
				_isPlaying = false;
				fadeTimer.stop();
			}
		}
		
		/**
		 * Set the height and width based on the size of the spinner. This should be more robust, but oh well.
		 */
		override protected function measure():void {
			super.measure();
			
			width = _size;
			height = _size;
		}
		
		/**
		 * The overall diameter of the spinner; also the height and width.
		 */
		[Bindable]
		public function set size(value:Number):void {
			if (value != _size) {
				_size = value;
				redraw();
				measure();
			}
		}
		
		public function get size():Number {
			return _size;
		}
		
		/**
		 * The number of 'spokes' on the spinner.
		 */
		[Bindable]
		public function set numTicks(value:int):void {
			if (value != _numTicks) {
				_numTicks = value;
				redraw();
			}
		}
		
		public function get numTicks():int {
			return _numTicks;
		}
		
		/**
		 * The width of the 'spokes' on the spinner.
		 */
		[Bindable]
		public function set tickWidth(value:int):void {
			if (value != _tickWidth) {
				_tickWidth = value;
				redraw();
			}
		}
		
		public function get tickWidth():int {
			return _tickWidth;
		}
		
		/**
		 * The duration (in milliseconds) that it takes for the spinner to make one revolution.
		 */
		[Bindable]
		public function set speed(value:int):void {
			if (value != _speed) {
				_speed = value;
				fadeTimer.stop();
				fadeTimer.delay = value / _numTicks;
				fadeTimer.start();
			}
		}
		
		public function get speed():int {
			return _speed;
		}
		
		/**
		 * The color of the ticks in the spinner. This really should be a style property.
		 */
		[Bindable]
		public function set tickColor(value:uint):void {
			if (value != _tickColor) {
				_tickColor = value;
				redraw();
			}
		}
		
		public function get tickColor():uint {
			return _tickColor;
		}
	}
}