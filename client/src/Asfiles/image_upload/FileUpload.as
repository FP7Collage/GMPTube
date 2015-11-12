

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

package Asfiles.image_upload {
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
		
    	public class FileUpload extends UIComponent {
    		
    	[Bindable] public var app:Tentube=Tentube(Application.application); 
    	
    	public var imageTypes:FileFilter 	= new FileFilter('Images (*.jpg, *.jpeg, *.gif,*.png)', '*.jpg; *.jpeg; *.gif; *.png');
		public var imagefilter:Array 		= new Array(imageTypes);
			
        // Hard-code the URL of the remote upload script.
        private var UPLOAD_URL:String = "";
        private var fr:FileReference;
        // Define reference to the upload ProgressBar component.
        private var pb:ProgressBar;
        // Define reference to the "Cancel" button which will immediately stop the upload in progress.
  		// private var btn:Button;
  		
        public var currphotoimage:String = new String();
        public function FileUpload() {
        }

        /**
         * Set references to the components, and add listeners for the SELECT,
         * OPEN, PROGRESS, and COMPLETE events.
         */
        public function init():void {
            // Set up the references to the progress bar and cancel button, which are passed from the calling script.

            // this.btn = btn;  cancel button
            // this.UPLOAD_URL = upload_url;
                        
         	this.UPLOAD_URL = app.ServerPath + app.graphData + "uploadphoto.php";	                    
            fr = new FileReference();
            fr.addEventListener(Event.SELECT, selectHandler);
            fr.addEventListener(Event.OPEN, openHandler);
            fr.addEventListener(ProgressEvent.PROGRESS, progressHandler);
            fr.addEventListener(Event.COMPLETE, completeHandler);
        }

        /**
         * Immediately cancel the upload in progress and disable the cancel button.
         */
    /*    public function cancelUpload():void {
            fr.cancel();
            pb.label = "UPLOAD CANCELLED";
            btn.enabled = false;
        }
	*/
        /**
         * Launch the browse dialog box which allows the user to select a file to upload to the server.
         */
        public function startUpload():void {
            fr.browse(imagefilter); 
        }

        /**
         * Begin uploading the file specified in the UPLOAD_URL constant.
         */
        private function selectHandler(event:Event):void {
            try{
            	var filesize:Number = event.target.size;
            	if ( (filesize/1000000) > 2 ){		//  max size is 2 mb
            		Alert.show('The maximum upload size is 2 Mb !','Toooo Big');
            		return;
            	}
            	
	            var params:URLVariables = new URLVariables();
    			//	this is the important part!!
      			// 	params._session_id = Properties.current.current_session;
	            currphotoimage = fr.name;
	            var request:URLRequest  = new URLRequest();
		        request.url				= UPLOAD_URL;
        		request.method 			= URLRequestMethod.POST;
        		fr.upload(request);
    		}
    		catch (error:Error){
        		trace("Unable to upload file.");
        		Alert.show("Error in uploading. Please contact the administrator",'Error !');
   			}    
        }

        /**
         * When the OPEN event has dispatched, change the progress bar's label 
         * and enable the "Cancel" button, which allows the user to abort the 
         * upload operation.
         */
        private function openHandler(event:Event):void {    
         //   btn.enabled = true;
        }

        /**
         * While the file is uploading, update the progress bar's status and label.
         */
        private function progressHandler(event:ProgressEvent):void {
       
        }

        /**
         * Once the upload has completed, change the progress bar's label and 
         * disable the "Cancel" button since the upload is already completed.
         */
        private function completeHandler(event:Event):void {
        	if ( this.id == 'registerImage' ){
        		this.parentDocument.btnImageUpload.visible	=	false;
        		this.parentDocument.userpic 			= app.ServerPath + 'GraphData/media/people/' + currphotoimage;
        	}
        	else if ( this.id == 'editImage' ){
        		this.parentDocument.imgPicture.source 	= app.ServerPath + 'GraphData/media/people/' + currphotoimage;
        	}
        	 
        	
          //  btn.enabled = false;
        }
    }
}