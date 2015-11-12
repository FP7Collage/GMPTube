package {
	
	import flash.media.Video;
	import flash.events.*;
    import flash.net.FileFilter;
    import flash.net.FileReference;
    import flash.net.URLRequest;
    import flash.net.URLRequestMethod;
    import flash.net.URLVariables;
    
    import mx.controls.Alert;
    import mx.controls.ProgressBar;
    import mx.core.Application;
    import mx.core.UIComponent;
    
	
	public class VideoContainer extends UIComponent
	{
        private var _video:Video;
        
		public function VideoContainer()
		{
			super();
			addEventListener(Event.RESIZE, resizeHandler);
			
		}

        public function set video(video:Video):void
        {
            if (_video != null)
            {
                removeChild(_video);
            }

			_video = video;

           	if (_video != null)
            {
	            _video.width = width;
                _video.height = height;
                addChild(_video);
            }
        }

        private function resizeHandler(event:Event):void
        {
            if (_video != null)
            {
               _video.width = width;
               _video.height = height;
            }
        }
	
	}
}

