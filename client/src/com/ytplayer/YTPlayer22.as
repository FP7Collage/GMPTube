/*
Copyright 2009 Google Inc.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

     http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/



package com.ytplayer {
	
  import flash.events.Event;
  import flash.events.IOErrorEvent;
  import flash.net.URLLoader;
  import flash.net.URLRequest;
  import flash.net.URLVariables;
  import flash.system.Security;
  
  import mx.containers.VBox;
  import mx.controls.SWFLoader;

  public class YTPlayer22 extends VBox {
    // Member variables.
    private var isWidescreen:Boolean;
    private var player:Object;
    private var playerLoader:SWFLoader;
    private var youtubeApiLoader:URLLoader;

    // CONSTANTS.
    private static const DEFAULT_VIDEO_ID:String = "0QRO3gKj3qw";
    private static const PLAYER_URL:String =
        "http://www.youtube.com/apiplayer?version=3";
    private static const SECURITY_DOMAIN:String = "http://www.youtube.com";
    private static const YOUTUBE_API_PREFIX:String =
        "http://gdata.youtube.com/feeds/api/videos/";
    private static const YOUTUBE_API_VERSION:String = "2";
    private static const YOUTUBE_API_FORMAT:String = "5";
    private static const WIDESCREEN_ASPECT_RATIO:String = "widescreen";
    private static const QUALITY_TO_PLAYER_WIDTH:Object = {
      small: 320,
      medium: 640,
      large: 854,
      hd720: 1280
    };
    private static const STATE_ENDED:Number = 0;
    private static const STATE_PLAYING:Number = 1;
    private static const STATE_PAUSED:Number = 2;
    private static const STATE_CUED:Number = 5;
    
    
    // See YouTube documentation for meanings
	public static var UNLOADED:Number = -2; // The player SWF is not yet loaded
 	public static  var UNSTARTED:Number = -1;
	public static  var ENDED:Number = 0;
	public static  var PLAYING:Number = 1;
	public static var PAUSED:Number = 2;
	public static var BUFFERING:Number = 3;
	public static  var CUED:Number = 5;
	
    public var video_id:String = DEFAULT_VIDEO_ID;
    
    public function get monitorPlayback ():Boolean {
    	return true;
    }
    public function set monitorPlayback (b:Boolean):void {
    //	player.setMonitorPlayback(b);
    }
    
    public function get maintainAspectRatio ():Boolean {
    	return false;
    }
        
    public function set maintainAspectRatio (b:Boolean):void {
    //	player.setMaintainAspectRatio(b);
    }
    
    public function get duration():Number {
      return player.getDuration();
    }

   public function get volume():Number {
      return player.getVolume();
    }
    
    	public function set volume (v:Number):void {
         player.setVolume(v);
    }
    
    public function get seek():Number {
    //  return player.getSeek();
        return 0;
    }
    
    	public function set seek (v:Number):void {
      //   player.setSeek(v);
    }
  
     public function get currentTime():Number {
      return player.getCurrentTime();
    }

   public function get playerState():Number {
      return player.getPlayerState();
    }
    
    public function get isPlayable ():Boolean {
				return player.getPlayerState() == CUED || player.getPlayerState() == PAUSED;
			}

	public function get isPauseable ():Boolean {
				return player.getPlayerState() == PLAYING;
	}
    
    
    public function loadVideoById (videoId:String):void {
    	
    	player.loadVideoById(videoId);
    }
    
    	public function clearVideo ():void {
				player.clearVideo();
			}
		

    public function YTPlayer22():void {
      // Specifically allow the chromless player .swf access to our .swf.
      Security.allowDomain(SECURITY_DOMAIN);
      Security.allowDomain( 'youtube.com' );
	  Security.allowDomain( 's.ytimg.com' );
	  Security.allowDomain( 'i.ytimg.com' );
      

      setupPlayerLoader();
      setupYouTubeApiLoader();
    }

    private function setupPlayerLoader():void {
      playerLoader = new SWFLoader();
      playerLoader.addEventListener(Event.INIT, playerLoaderInitHandler);
      playerLoader.load(PLAYER_URL);
    }

    private function playerLoaderInitHandler(event:Event):void {
      addChild(playerLoader);
      playerLoader.content.addEventListener("onReady", onPlayerReady);
      playerLoader.content.addEventListener("onError", onPlayerError);
      
      
      //playerLoader.content.addEventListener("onStateChange", onPlayerStateChange);
       playerLoader.content.addEventListener("onPlaybackQualityChange",   onVideoPlaybackQualityChange);
    }

    private function setupYouTubeApiLoader():void {
      youtubeApiLoader = new URLLoader();
      youtubeApiLoader.addEventListener(IOErrorEvent.IO_ERROR,
                                        youtubeApiLoaderErrorHandler);
      youtubeApiLoader.addEventListener(Event.COMPLETE,
                                        youtubeApiLoaderCompleteHandler);
    }

    private function youtubeApiLoaderCompleteHandler(event:Event):void {
      var atomData:String = youtubeApiLoader.data;

      // Parse the YouTube API XML response and get the value of the
      // aspectRatio element.
      var atomXml:XML = new XML(atomData);
      var aspectRatios:XMLList = atomXml..*::aspectRatio;

      isWidescreen = aspectRatios.toString() == WIDESCREEN_ASPECT_RATIO;

      // Cue up the video once we know whether it's widescreen.
      // Alternatively, you could start playing instead of cueing with
      // player.loadVideoById(videoIdTextInput.text);
      player.cueVideoById(video_id);
    }

    public function cueVideo(video_id:String=null):void {
      this.video_id = video_id || this.video_id;
      var request:URLRequest = new URLRequest(YOUTUBE_API_PREFIX + video_id);

      var urlVariables:URLVariables = new URLVariables();
      urlVariables.v = YOUTUBE_API_VERSION;
      urlVariables.format = YOUTUBE_API_FORMAT;
      request.data = urlVariables;

      try {
        youtubeApiLoader.load(request);
      } catch (error:SecurityError) {
        trace("A SecurityError occurred while loading", request.url);
      }
    }
    
    public function playVideo():void {
    	player.playVideo();
    }
    
    public function pauseVideo():void {
    	player.pauseVideo();
    }
    
    public function stopVideo():void {
    	player.stopVideo();
    }

    private function youtubeApiLoaderErrorHandler(event:IOErrorEvent):void {
      trace("Error making YouTube API request:", event);
    }

    private function onPlayerReady(event:Event):void {
      player = playerLoader.content;

      //cueButton.enabled = true;
    }

    private function onPlayerError(event:Event):void {
      trace("Player error:", Object(event).data);
    }

    private function onVideoPlaybackQualityChange(event:Event):void {
      trace("Current video quality:", Object(event).data);
      resizePlayer(Object(event).data);
    }
    

    private function resizePlayer(qualityLevel:String):void {
    	//player.setSize(580, 400);
    	 
    
      var newWidth:Number = QUALITY_TO_PLAYER_WIDTH[qualityLevel] || 640;
      var newHeight:Number;

      if (isWidescreen) {
        // Widescreen videos (usually) fit into a 16:9 player.
        newHeight = newWidth * 9 / 16;
      } else {
        // Non-widescreen videos fit into a 4:3 player.
        newHeight = newWidth * 3 / 4;
      }

      trace("isWidescreen is", isWidescreen, ". Size:", newWidth, newHeight);
      player.setSize(newWidth, newHeight);

      // Center the resized player on the stage.
      player.x = (stage.stageWidth - newWidth) / 2;
      player.y = (stage.stageHeight - newHeight) / 2;
      
      player.x = player.y = 0;
    }
    
    
    }
    
  }  

