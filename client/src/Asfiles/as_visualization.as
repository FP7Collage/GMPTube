

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
import com.adobe.flex.extras.controls.springgraph.Graph;
import com.adobe.flex.extras.controls.springgraph.Item;

import component.ConnectToGroupMembers;
import component.ExpandNPopup;
import component.intensityPopup;

import flash.events.ContextMenuEvent;
import flash.events.Event;
import flash.events.MouseEvent;
import flash.events.TimerEvent;
import flash.net.URLVariables;
import flash.ui.ContextMenu;
import flash.utils.Timer;

import mx.collections.ArrayCollection;
import mx.controls.Alert;
import mx.controls.Menu;
import mx.core.UIComponent;
import mx.events.FlexEvent;
import mx.managers.PopUpManager;
import mx.rpc.events.ResultEvent;

/***************************************************************
*************** CODE FOR SPRING GRAPH VISUALIZER  **************
***************************************************************/





[Bindable]
public var myGraph:Graph=new Graph();
public  var tempnodeshistory:Array;
public  var tempedgeshistory:Array;
public  var nodesstatus:Array = new Array()
public  var nodeshistory:Array = new Array();
// for the selected member in connect to all member in the group
public var Globalconnecttoallmembername:String;
public var edgeshistory:Array = new Array();
public var statuscount:int = 0;
public var historycount:int = 0;
public var showcount:int = 0;
//to generate id's for new edges
public static var NEWEDGE_ID:int=999;
public var fullGraph:Graph=new Graph();		
public var labnodesarraycta:Array=new Array();
public var lablinksarraycta:Array=new Array();
public var cta:int=0;

//Variables for Drawing new edges
public var boolLinkDraw:Boolean=false;
public var LinkSrcX:Number;
public var LinkSrcY:Number;
public var SrcUID:Object=new Object();	
public var DestUID:Object=new Object();
public var SrcItem:XML=new XML();
public var DestItem:XML=new XML(); 	
//		public var tempUIComponent:UIComponent=new UIComponent();

public var myPattern:RegExp = new RegExp("-","g"); 
//These Matrices will contain the xml file data.
public var NodeTypeArray:Array=new Array();
public var LinkTypeArray:Array=new Array();
public var NodesArray:Array=new Array();
public var LinksArray:Array=new Array();
public var ValidNodePairsArray:Array=new Array();
public var Valid_DestNodes:Array;
public var Node_Id_Uid:Array=new Array();


//used for viewing profile,in FlexItem,PeopleProfile,Profile
public var Vbox_Ids:Array=new Array();
public var Vbox_Refs:Array=new Array();
private var normalrigidity:Number = 0.25;
private var edgerigidity  :Number = 0.05;

public var timeLimit:Number;





//public var timer_edgelistener:Timer=new Timer(2000,1);
//public var currentItem_SetTimer:Timer=new Timer(100,1);

public var timer_edgelistener:Timer=new Timer(100,1);
public var currentItem_SetTimer:Timer=new Timer(1,1);


//Continue_Draw,EndEdgeDraw,Finish_Draw,Push_Edge functions are for drawing edges dynamically.
public var simTimer:Timer = new Timer(1000,1);
//		public var simTimer2:Timer = new Timer(500,1);
public var linecolor:uint=new uint();			//linecolor & linkname will be set in FlexItem.mxml 
public var linkname:String=new String();		//when the user begins the link
public var x_pos:int;
public var y_pos:int;
public var inside_node:Boolean=false; 	//this variable will be true when mouse pointer is inside a node
//while drawing an edge.
public var starttime:String=new String();
public var endtime:String=new String();

/* 
the function will check for boolLinkdraw.if it is true,it will clear the graphics of UIComponent
and draw a new line from the new x,y coods of node(nodes move constantly) to  
a. current mouse location (if mouse is outside a node
b. to another point very near to current mouseloc (if mouse inside a node- see inside_node:boolean)  */

public function Continue_Draw():void
{
	if (nodeMenu!=null){
		nodeMenu.hide();
	}
	if (boolLinkDraw==false){
		return;
	}
	tempUIComponent.graphics.clear();
	LinkSrcX=SrcUID.x+(SrcUID.width/2);
	LinkSrcY=SrcUID.y+(SrcUID.height/2) ;
	tempUIComponent.graphics.moveTo(LinkSrcX,LinkSrcY);
	tempUIComponent.graphics.lineStyle(2, linecolor ,1);
	if (inside_node==true){
		var a1:int=LinkSrcX;
		var b1:int=LinkSrcY;
		var a2:int=springgraph.contentMouseX;
		var b2:int=springgraph.contentMouseY;
		Push_Edge(a1,b1,a2,b2);
	}
	else {
		x_pos=springgraph.contentMouseX;
		y_pos=springgraph.contentMouseY;
	}
	
	tempUIComponent.graphics.lineTo(x_pos,y_pos);
	tempUIComponent.graphics.endFill();	
	tempUIComponent.addEventListener(MouseEvent.CLICK,EndEdgeDraw);
}

/* When the user clicks while dragging the edge or after the user clicks on a node
this function is called.It clears the UIComponents' graphics,sets the boolean variable 
false, and removes the event listener. */
public function EndEdgeDraw(e:MouseEvent):void{
	if (boolLinkDraw){
		boolLinkDraw=false;
		tempUIComponent.graphics.clear();
		tempUIComponent.removeEventListener(MouseEvent.CLICK,EndEdgeDraw);
	}
}

/* 	To push the edge backward on drawing a new edge.  		      	*/
private function Push_Edge(a1:int,b1:int,a2:int,b2:int):void
{
	if( (  (a2-a1)> 0 ) && ( (b2-b1)<= 0 ) ) 
	{	x_pos=a2-5;
		y_pos=b2+5; 
	}
	else if( ((a2-a1) >= 0) && ((b2-b1) > 0) )  
	{	x_pos=a2-5;
		y_pos=b2-5; 
	} 
	else if( ((a2-a1) < 0) && ((b2-b1) >= 0) )  
	{	x_pos=a2+5;
		y_pos=b2-5 ; 
	}
	else if( ((a2-a1) <= 0) && ((b2-b1) < 0) )  
	{	x_pos=a2+5;
		y_pos=b2+5 ; 
	}
	return;
}

//returns true if the link exists already
public function LinkAlreadyExists(frmID:String,toID:String):Boolean{
	var edgetype:String=linkname;
	var edges:Object=new Object();
	edges=fullGraph._edges[frmID];
	for each(var edge:Object in edges )
	for each(var edgeinfo:Object in edge.link ){
		if (edgeinfo.name==linkname) {
			if (edgeinfo.SourceID==frmID){
				if (edgeinfo.DestID==toID)
				return true;
			}
		}
	}
	return false;
}

public function listenFn(ev:FlexEvent):void{
	modifyTime(0);
	springgraph.removeEventListener(FlexEvent.UPDATE_COMPLETE,listenFn);
}
/*  The function called whenever the user clicks on a node while drawing an edge
it first checks whether an edge of same type exists between the node pair.
if not,then it creates an edge,adds it to
a. myGraph		:	Graph
b. fullGraph	:	Graph
c. LinksArray	: 	Array		
d. Neighbours	:	Array	*/

public var newedge_urlvars:URLVariables ;

public function tagFinish_Draw():void
{      	      	
	EndEdgeDraw( new MouseEvent(MouseEvent.CLICK) );
	//check if an edge already exists.of the same type.and nodes. 
	var fromID:String=(SrcItem.@id).toString();
	var toID:String=(DestItem.@id).toString();
	if ( LinkAlreadyExists(fromID,toID) ){
		Alert.show('Link already exists','Error !');
		//	var errorWindow:box_errorwindow=box_errorwindow(PopUpManager.createPopUp(this,box_errorwindow,false));
		//	errorWindow.errorText.text="Link already exists.";
		return;
	}
	
	newedge_urlvars			= 	new URLVariables()		
	newedge_urlvars.edgename= 	"Relates To";
	newedge_urlvars.fromID  = 	fromID;
	newedge_urlvars.toID  	= 	toID;	
	splitxml(newedge_urlvars);
	return;
}




public function Finish_Draw():void
{      	      	
	EndEdgeDraw( new MouseEvent(MouseEvent.CLICK) );
	//check if an edge already exists.of the same type.and nodes. 
	var fromID:String=(SrcItem.@id).toString();
	var toID:String=(DestItem.@id).toString();
	if ( LinkAlreadyExists(fromID,toID) ){
		Alert.show(linkAlreadyM,errM);
		//	var errorWindow:box_errorwindow=box_errorwindow(PopUpManager.createPopUp(this,box_errorwindow,false));
		//	errorWindow.errorText.text="Link already exists.";
		return;
	}
	
	newedge_urlvars			= 	new URLVariables()		
	newedge_urlvars.edgename= 	"Knows";
	newedge_urlvars.fromID  = 	fromID;
	newedge_urlvars.toID  	= 	toID;	
	splitxml(newedge_urlvars);
	return;
}


private function splitxml(variables:URLVariables):void{        
	variables.action	=   "addedge";		
	changehttp.url 		= 	ServerPath + graphData + "change.php";	
	changehttp.request 	= 	variables;		
	changehttp.addEventListener(ResultEvent.RESULT,newedgeId);							 
	changehttp.send();
}	  

private function newedgeId(r:ResultEvent):void{
	try{
		if ( r.result.rsp.message  == "Success" ) {
			changehttp.removeEventListener(ResultEvent.RESULT,newedgeId);
			var edgeid:String 	= r.result.rsp.id;
			var edgename:String = newedge_urlvars.edgename;
			var fromID:String 	= newedge_urlvars.fromID;
			var toID:String 	= newedge_urlvars.toID;	
			
			var NewEdgeXml:String=	new String('<Edge id="' + edgeid + '" name ="' + edgename + '" fromID="' + fromID + '" toID="' + toID + '" creationtime="' + (r.result.rsp.creationtime.toString()).replace(myPattern,'/') + '" intensity="2" edgecolor="0x' + linecolor.toString(16) + '"/>'); //' + linecolor.toString() + '"/>');
			var edge:XML		=	new XML(NewEdgeXml);
			
			var linkdata:Object	=	new Object();
			linkdata.id			=	edgeid;  
			linkdata.name		=	newedge_urlvars.edgename;
			linkdata.tooltip	=	'knows';
			linkdata.SourceID	=	newedge_urlvars.fromID;
			linkdata.DestID		=	newedge_urlvars.toID;				
			linkdata.edgecolor	= 	"0x" + linecolor.toString(16);
		//	linkdata.edgecolor.alpha = 0.2
			
			linkdata.intensity	=	2;
			linkdata.creationtime = (r.result.rsp.creationtime.toString()).replace(myPattern,'/');
			//					Alert.show(linkdata.creationtime);
			var fromItem:Item	=	myGraph.find(fromID);
			var toItem:Item		=	myGraph.find(toID);
			myGraph.link(fromItem, toItem, linkdata);		// myGraph updated
			fullGraph.link(fromItem, toItem, linkdata);		// fullGraph updated
			
			var tlinkxml:XML	=new XML(NewEdgeXml);
			LinksArray.push(tlinkxml);						// LinksArray updated	
			
			//updating in Neighbours array for both Source and Destination indexes.
			var friends:Array=Neighbours[fromID];
			if (friends==null)
			friends=new Array();
			if ( friends.indexOf(toID) == -1)
			friends.push(toID);
			
			friends=Neighbours[toID];
			if (friends==null)
			friends=new Array();
			if ( friends.indexOf(fromID) == -1)
			friends.push(fromID);
			
			
			timer_edgelistener.start();
			new_current_id=toID;
			currentItem_SetTimer.reset();
			currentItem_SetTimer.start();
			
			
			tlinkxml=null;	
			NewEdgeXml=null;
			linkdata=null;	
		}
	}
	catch(e:Error){
		trace('Edge was not saved successfully in server','Error !');
		trace(e.name);
		trace(e.message);

		// take care
	}
}




public function ConfigureListeners(e:TimerEvent):void{
	if ( springgraph.edges_drawn == false ) 
	return;
	springgraph.edges_drawn = false;
	timer_edgelistener.stop();
	timer_edgelistener.reset();

	for each (var child:UIComponent in myGraph.edgeRef){
		
		var Edgeid:String = new String();
		Edgeid=myGraph.EdgeUid_EdgeId[child.uid];
		trace("\nEdgeid="+Edgeid);
		for each(var edge:XML in LinksArray){
			if (Edgeid==edge.@id) {
				if(edge.@name.toString()=="Knows"){
					if ((edge.@fromID.toString() == Univ_LoginId1)||( edge.@toID.toString() == Univ_LoginId1) ) {
						var popupitems:Array= new Array(); 
						var cmexpand:ContextMenuItem 	= 	new ContextMenuItem(changeIntensityM, true);
						var cmstop:ContextMenuItem   	=   new ContextMenuItem(deleteEdgeM, true);
						//  var cmfocus:ContextMenuItem   	=   new ContextMenuItem("Focus", true);	
						
						cmstop.separatorBefore   = false;
						//cmfocus.separatorBefore  = false;
						
						cmexpand.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, intensityHandler);
						cmstop.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, edgeDelHandler);
						//cmfocus.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, focushandler);
						
						popupitems.push(cmexpand);
						//popupitems.push(cmfocus);
						popupitems.push(cmstop);
						
						var cm:ContextMenu = new ContextMenu();
						cm.hideBuiltInItems();
						cm.customItems = popupitems;
						cm.addEventListener(ContextMenuEvent.MENU_SELECT,getUid);
						child.contextMenu = cm;
						//	child.addEventListener(MouseEvent.CLICK,EdgePopUp);
					}
				}
				else if(edge.@name.toString()=="Has Rated"){
					var popupitems:Array= new Array(); 
				  var cmrating:ContextMenuItem 	= 	new ContextMenuItem(viewRatingM, true);
						
						//var cmstop:ContextMenuItem   	=   new ContextMenuItem("Delete Edge", true);
						//  var cmfocus:ContextMenuItem   	=   new ContextMenuItem("Focus", true);	
						
						//cmstop.separatorBefore   = false;
						//cmfocus.separatorBefore  = false;
						
						cmrating.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, ratinghandler);
						//cmstop.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, edgeDelHandler);
						//cmfocus.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, focushandler);
						
						popupitems.push(cmrating);
						//popupitems.push(cmfocus);
						//popupitems.push(cmstop);
						
						var cm:ContextMenu = new ContextMenu();
						cm.hideBuiltInItems();
						cm.customItems = popupitems;
						cm.addEventListener(ContextMenuEvent.MENU_SELECT,getUid);
						child.contextMenu = cm;
						//	child.addEventListener(MouseEvent.CLICK,EdgePopUp);
				}
			}
		}
	}
} 


public function ConfigurelabListeners(e:TimerEvent):void{
	
	springgraph.edges_drawn = false;
	timer_edgelistener.stop();
	timer_edgelistener.reset();

	for each (var child:UIComponent in fullGraph.edgeRef){
		
		var Edgeid:String = new String();
		Edgeid=myGraph.EdgeUid_EdgeId[child.uid];
		trace("\nEdgeid="+Edgeid);
		for each(var edge:XML in LinksArray){
			if (Edgeid==edge.@id) {
				/*
				if(edge.@name.toString()=="Knows"){
					if ((edge.@fromID.toString() == Univ_LoginId1)||( edge.@toID.toString() == Univ_LoginId1) ) {
						var popupitems:Array= new Array(); 
						var cmexpand:ContextMenuItem 	= 	new ContextMenuItem("Change Intensity", true);
						var cmstop:ContextMenuItem   	=   new ContextMenuItem("Delete Edge", true);
						//  var cmfocus:ContextMenuItem   	=   new ContextMenuItem("Focus", true);	
						
						cmstop.separatorBefore   = false;
						//cmfocus.separatorBefore  = false;
						
						cmexpand.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, intensityHandler);
						cmstop.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, edgeDelHandler);
						//cmfocus.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, focushandler);
						
						popupitems.push(cmexpand);
						//popupitems.push(cmfocus);
						popupitems.push(cmstop);
						
						var cm:ContextMenu = new ContextMenu();
						cm.hideBuiltInItems();
						cm.customItems = popupitems;
						cm.addEventListener(ContextMenuEvent.MENU_SELECT,getUid);
						child.contextMenu = cm;
						//	child.addEventListener(MouseEvent.CLICK,EdgePopUp);
					}
				}
				else 
				*/if(edge.@name.toString()=="Has Rated"){
					var popupitems:Array= new Array(); 
					var cmrating:ContextMenuItem 	= 	new ContextMenuItem(viewRatingM, true);
						
						//var cmstop:ContextMenuItem   	=   new ContextMenuItem("Delete Edge", true);
						//  var cmfocus:ContextMenuItem   	=   new ContextMenuItem("Focus", true);	
						
						//cmstop.separatorBefore   = false;
						//cmfocus.separatorBefore  = false;
						
						cmrating.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, ratinghandler);
						//cmstop.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, edgeDelHandler);
						//cmfocus.addEventListener(ContextMenuEvent.MENU_ITEM_SELECT, focushandler);
						
						popupitems.push(cmrating);
						//popupitems.push(cmfocus);
						//popupitems.push(cmstop);
						
						var cm:ContextMenu = new ContextMenu();
						cm.hideBuiltInItems();
						cm.customItems = popupitems;
						cm.addEventListener(ContextMenuEvent.MENU_SELECT,getUid);
						child.contextMenu = cm;
						//	child.addEventListener(MouseEvent.CLICK,EdgePopUp);
				}
			}
		}
	}
} 


public var UIDEdgeSelected:String=new String();
private var intenpop:intensityPopup;
private var expandNPop:ExpandNPopup;
//private var groupPopUp : ConnectToGroupMembers;


public function showExpandPopUp(targetItem :Item):void{
	
	var val:int;
	// set the item which has been right clicked so that it can be referece when we come back from pop up
	current_node_rightClickItem = targetItem;	
	expandNPop =  ExpandNPopup(PopUpManager.createPopUp(this,ExpandNPopup,true));
	PopUpManager.centerPopUp(expandNPop);
}

private var current_connectToAll_Member : Item;

   private var groupPopUp : ConnectToGroupMembers;
public function connectToAllPopUp(targetItem :Item):void{
		    current_connectToAll_Member = targetItem;
	
       groupPopUp = ConnectToGroupMembers(PopUpManager.createPopUp(this,ConnectToGroupMembers,true));
 	    PopUpManager.centerPopUp(groupPopUp);
 	    
			
		return;	
}

public function cancelConnectToAllMembers():void{
	PopUpManager.removePopUp(groupPopUp);
}


public function calculateConnectToAllMembers(grpName:String,intensityValue:Number):void{
	PopUpManager.removePopUp(groupPopUp);
	
			var connectToAll:HTTPService 	= 	new HTTPService();
			connectToAll.url = 	ServerPath + 'GraphData/connectToAll.php';
			connectToAll.useProxy			=	false;
			connectToAll.resultFormat		=	'object';
		    connectToAll.method			=   'post';
		     //connectToAll.BusyCursor =
		    				
		   //current_node_rightClickItem.
		    var memberName : String;
		    memberName = current_connectToAll_Member.data.@id;
		    Globalconnecttoallmembername=memberName;
		    
		   		
			var params:Object=new Object();
				params.groupName= grpName;
				params.memberName=memberName;
				params.intensityValue=intensityValue;
				
				connectToAll.addEventListener(ResultEvent.RESULT,connectToAll_Response);
				connectToAll.send(params);
	
		
}
 
 	public function	connectToAll_Response(r:ResultEvent):void{
			try{
					var responseMessage:String;
			   		responseMessage	=	r.result.rsp.message;
			   		
			   		if(responseMessage=='Success'){
			   			//  Alert.show(relUpdatedM);
						  Alert.show(relUpdatedM);
						// refreshing the data and refreshing the network screen
						// need to update my graph and need to update full graph
			   			//onAppCreationComplete();
			   			cta=1;
			   			loadnetwork.send();	
			   			labnodesarraycta=NodesArray;
			   			lablinksarraycta=LinksArray;				
						
			   		}
			   		else{
			   			
			   			Alert.show("Http Service Failed");
			   				
			   		}
			 	}
			 	catch(e:Error){
			 		trace('ConnectToALL: An unexpected error had occured. Please contact the administrator','Error !');
			 	}
		}
	
 
  

public function updateExpandDepth(value:Number):void{
	PopUpManager.removePopUp(expandNPop);
	
	
	
	// Current_Item is the item for which the node has to be extended	
	// opennodes of current-item
	// and open nodes of its n-1 neighbours
	
	//opennodesN(Current_Item,value);	
	opennodesN(current_node_rightClickItem,value);
	//trace('end of the update expand function');
	
	return;
}

//expand N
public function opennodesN(targetItem :Item,degree:Number):void{
	
	if(degree==0)
	return;
	else
	{	
	
	var targetVbox:FlexItem ;
	targetVbox=FlexItem(Node_Id_Uid[targetItem.id]);
	
	// to check if it is aldready expanded
	/*
	if ( targetVbox != null ) {
		if(targetVbox.showbutton == true)
		return;
	}
	*/
	var BaseIds:Array=new Array();
	var queryarray:Array=new Array();
	// this way we start the history from beginning
	if(showcount != historycount){
		clearhistory();
		nodesstatus[statuscount] = "expand";
		statuscount = statuscount + 1;
	}
	// save the existing graph
	if (historycount == 0) {
		saveprevious();
		historycount = historycount + 1;
		showcount = showcount + 1;
		backbutton.visible = true;
	}
	
	// call the neighbourhood function
	queryarray.push("All@");
	BaseIds.push( targetItem.id );
	
	
	Neighbourhood(BaseIds,queryarray,true);
	
	
	
	drawNodesEdges();
	new_current_id = targetItem.id;
	queryarray=null;
	BaseIds=null;
	myGraph.changed();
	springgraph.addEventListener(FlexEvent.UPDATE_COMPLETE,listenFn);
	//				changeGraph(timeLimit);
	//				modifyTime(0);
	/*  				simTimer2.reset();
	simTimer2.start();  */
	saveprevious();
	historycount = historycount + 1;
	showcount = showcount + 1;
	backbutton.visible = true;
	//recursion
	var targetitemNeighbours:Array;
	targetitemNeighbours=fullGraph.neighbour_Ids(targetItem.id );
	trace(targetitemNeighbours);
	for each (var myObj:String in targetitemNeighbours ) 
		{
			
			//node_ID=myObj.id;
			var item:Item=fullGraph.find(myObj);
			opennodesN(item,degree-1);
		}

	
	
	return;
	}//end of base case else
}
// for displaying the rating popup
private function ratinghandler(evt:ContextMenuEvent):void{
	//	UIDEdgeSelected		= evt.contextMenuOwner.uid;
	var val:String;
	var time:String;
	var user:String;
	var Edgeid:String = new String();
	Edgeid=myGraph.EdgeUid_EdgeId[UIDEdgeSelected];
	for each(var edge:XML in LinksArray){
		if (Edgeid==edge.@id) {
			val = edge.@value.toString();
			time= edge.@creationtime.toString();
			user= edge.@fromID.toString();
		}
	}
	
	var ratingpopup:box_edgepopup = box_edgepopup(PopUpManager.createPopUp(this,box_edgepopup,true));
	ratingpopup.label2.text = time;
	ratingpopup.label3.text = user;
	ratingpopup.label4.text = val;
	PopUpManager.centerPopUp(ratingpopup);
}


private function intensityHandler(evt:ContextMenuEvent):void{
	//	UIDEdgeSelected		= evt.contextMenuOwner.uid;
	var val:int;
	var Edgeid:String = new String();
	Edgeid=myGraph.EdgeUid_EdgeId[UIDEdgeSelected];
	for each(var edge:XML in LinksArray){
		if (Edgeid==edge.@id) {
			val = parseInt(edge.@intensity.toString());
		}
	}
	
	intenpop = intensityPopup(PopUpManager.createPopUp(this,intensityPopup,true));
	intenpop.radiogroup1.selectedValue = val;
	PopUpManager.centerPopUp(intenpop);
}

public function setIntensity(value:Number):void{
	var Edgeid:String = new String();
	var SrcNodeid:String=new String();
	var EndNodeid:String=new String();
	Edgeid=myGraph.EdgeUid_EdgeId[UIDEdgeSelected];
	
	for each(var edge:XML in LinksArray){
		if (Edgeid==edge.@id) {
			SrcNodeid=edge.@fromID;
			EndNodeid=edge.@toID;
			break;
		}
	}
	var obj:Object=myGraph._edges[SrcNodeid];
	var obj1:Object=obj[EndNodeid];
	for each(var obj2:Object in obj1.link) {
		if(obj2.id==Edgeid){
			obj2.intensity=value;
			break;
		}
	}
	myGraph.changed();	
	
	var count:int=0;			
	for each( edge in LinksArray){	
		if (Edgeid==edge.@id) {
			LinksArray[count].@intensity=value.toString();
			var variables:URLVariables 	= new URLVariables();
			variables.action			= 'changeintensity';
			variables.id        		= Edgeid;
			variables.intensity			= value.toString();
			changehttp.url          	= ServerPath + graphData + "change.php";	
			changehttp.request 			= variables;	
			changehttp.send();   				  				
			break;
		}
		count+=1;					//LinksArray changed 
	}
	
	
	SrcNodeid=null; 
	EndNodeid=null;
	Edgeid=null;
	PopUpManager.removePopUp(intenpop);			
} 

private function edgeDelHandler(evt:ContextMenuEvent):void{
	//	UIDEdgeSelected		= evt.contextMenuOwner.;
	var Edgeid:String=new String();
	Edgeid=myGraph.EdgeUid_EdgeId[UIDEdgeSelected];
	delete myGraph.EdgeUid_EdgeId[UIDEdgeSelected];
	Delete_SingleEdge(Edgeid,true);
}





private function getUid(evt:ContextMenuEvent):void{
	var dis:UIComponent;
	dis = UIComponent(evt.contextMenuOwner);
	UIDEdgeSelected = dis.uid;
}

public var myMenu:Menu;
public var nodeMenu:Menu= new Menu(); 

//We find the edgetype.If it is "knows" then we add Change Intensity menu to the
//pop up box.

/* 		public function EdgePopUp(e:MouseEvent):void{
var PopupItems:ArrayCollection = new ArrayCollection();
var inten:int;
UIDEdgeSelected		=	e.currentTarget.uid;
var Edgeid:String	=	new String();
var EdgeType:String	=	new String();
Edgeid				=	myGraph.EdgeUid_EdgeId[UIDEdgeSelected];
for each(var edge:XML in LinksArray){
if (Edgeid==edge.@id) {
if ( (edge.@fromID == Univ_LoginId) || ( edge.@toID == Univ_LoginId) ){
inten		=	edge.@intensity.toString();
EdgeType	=	edge.@name.toString();
break;
}
else 
return;
}
}

if ( EdgeType == "Knows" ){
var pop1:XML =  <menuitem label="Change Intensity"/>;  
var intensityvalues: XML = 
<root>
</root>;
for(var i:int=1;i<=5;i++){
var boolval:String=new String("false");
if (i==inten){
boolval="true";
}
var tmp_str:String=new String('<menuitem label="' + i + '"type="check" toggled="' + boolval + '"/>');
intensityvalues.appendChild(tmp_str);
tmp_str=null;
boolval=null;
}
pop1.appendChild(intensityvalues);
PopupItems.addItem(pop1);
var pop2:XML 	=  <menuitem label="Delete Edge"/>;
PopupItems.addItem(pop2);
myMenu			=	new Menu();
myMenu			= 	Menu.createMenu(this,PopupItems,false);
myMenu.labelField=	"@label";
myMenu.addEventListener(MenuEvent.ITEM_CLICK, MenuClickInfo);
myMenu.show(e.stageX, e.stageY);	
}
EdgeType=	null;
Edgeid	=	null;
return;
} */



/*  		public function MenuClickInfo(m:MenuEvent):void {
var edgepopselection:String=m.item.@label;
var Edgeid:String=new String();
switch(edgepopselection)
{
case "Delete Edge":
Edgeid=myGraph.EdgeUid_EdgeId[UIDEdgeSelected];
delete myGraph.EdgeUid_EdgeId[UIDEdgeSelected];
Delete_SingleEdge(Edgeid,true);
break;	

default:	
//user selects one of the intensities.
/*  Intensity change has to be reflected in the following variables:
1. myGraph		: Graph
2. fullGraph	: Graph
3. LinksArray	: Array				*/
/*   		 		var SrcNodeid:String=new String();
var EndNodeid:String=new String();
Edgeid=myGraph.EdgeUid_EdgeId[UIDEdgeSelected];

for each(var edge:XML in LinksArray){
if (Edgeid==edge.@id) {
SrcNodeid=edge.@fromID;
EndNodeid=edge.@toID;
break;
}
}
var obj:Object=myGraph._edges[SrcNodeid];
var obj1:Object=obj[EndNodeid];
for each(var obj2:Object in obj1.link) {
if(obj2.id==Edgeid){
obj2.intensity=edgepopselection;
break;
}
}
myGraph.changed();	

var count:int=0;			
for each( edge in LinksArray){	
if (Edgeid==edge.@id) {
LinksArray[count].@intensity=edgepopselection;
var variables:URLVariables 	= new URLVariables();
variables.action			= 'changeintensity';
variables.id        		= Edgeid;
variables.intensity			= edgepopselection;
changehttp.url          	= ServerPath + graphData + "change.php";	
changehttp.request 			= variables;	
changehttp.send();   				  				
break;
}
count+=1;					//LinksArray changed 
}


SrcNodeid=null; 
EndNodeid=null;
break;		
}
Edgeid=null;
}  */


/* This is called when the user clicks Delete Edge on the Edge popup. The user can delete only the edges
which start from him . On delete edge,data has to be changed in 
1. myGraph						:Graph			unlink
2. LinksArray					:Array			delete
3. fullGraph					:Graph			unlink
4. springgraph.drawingSurface	:UIComponent	removeChild
5. myGraph.edgeRef 				:Array			delete
6. myGraph.EdgeUid_EdgeId		:Array			delete
7. Neighbours 					:Array			delete
8. Parents						:Array			delete
*/
public function Delete_SingleEdge(edgeId:String,permanent:Boolean):void{	
	var SrcNodeid:String=new String();
	var EndNodeid:String=new String();
	var index:Number=-1;
	var friends:Array;
	var first:Array;
	var last:Array;
	
	for each(var edge:XML in LinksArray){
		index+=1;
		if (edgeId==edge.@id) {
			SrcNodeid=edge.@fromID;
			EndNodeid=edge.@toID;	   		 				
			break;
		}	
	}
	
	friends = LinksArray;
	first=friends.splice(0,index);
	last=friends.splice(1,friends.length-1);
	friends=first.concat(last);
	LinksArray = friends;					//LinksArray Updated
	
	// *****************************************
	
	if ( permanent == true ){ 
		//then we delete the edge permanently from all variables.update in server
		var variables:URLVariables 	= new URLVariables();
		variables.action			= 	'deleteedge';
		variables.id        		= 	edgeId;
		changehttp.url          	= 	ServerPath + graphData + "change.php";	
		changehttp.request 			= 	variables;	
		changehttp.send();   				//delete edge in server 
		
		//delete in Neighbours array in both source and destination indexes.			
		friends=Neighbours[SrcNodeid];
		index=friends.indexOf(EndNodeid);
		first=friends.splice(0,index);
		last=friends.splice(1,friends.length-1);
		friends=first.concat(last);
		Neighbours[SrcNodeid]=friends;
		
		friends=Neighbours[EndNodeid]; 
		index=friends.indexOf(SrcNodeid);
		first=friends.splice(0,index);
		last=friends.splice(1,friends.length-1);
		friends=first.concat(last);
		Neighbours[EndNodeid]=friends;	
		
		fullGraph.unlink(edgeId,SrcNodeid,EndNodeid);		//deleted in fullGraph
		
		
		// *****************************************
	}
	
	//delete in parents Array.	1`st find the index to be deleted and then delete it.
	index=0;
	for each(var info:Array in parents){
		if ( info[2] == edgeId )
		break;
		index+=1;
	}
	friends=parents; 
	first=friends.splice(0,index);
	last=friends.splice(1,friends.length-1);
	friends=first.concat(last);
	parents=friends;
	
	//1st we clear the graphics component of the edge and then delete the edge.
	var disp:UIComponent;
	disp = UIComponent(myGraph.edgeRef[edgeId]);
	if(disp!=null){
		disp.graphics.clear();
		if(springgraph.drawingSurface.contains(disp)==true){
			springgraph.drawingSurface.removeChild(disp);
		}		//removed from springgraph
	}
	delete myGraph.EdgeUid_EdgeId[disp.uid];			//delete in edge uid to id reference
	delete myGraph.edgeRef[edgeId];						//delete in edge id to component reference
	myGraph.unlink(edgeId,SrcNodeid,EndNodeid);			//deleted in myGraph
	
	SrcNodeid=null;
	EndNodeid=null;
	if(evalinfoButton.label=="Show Evaluation Info");
	{
		removelabedges();
	}
}

//The InitializeMatrices() function will retrieve the node types,edge types,nodes,edges from xml variable
//and store them in Arrays.
public function InitializeMatrices(externalData:XML):void{
	UpdateNodeTypeMatrix(externalData);
	UpdateLinkTypeMatrix(externalData);
	UpdateNodesMatrix(externalData);
	UpdateLinksMatrix(externalData);
}
public function UpdateNodesMatrix(externalData:XML):void{
	NodesArray=new Array();
	for each (var node: XML in externalData.descendants("Node")) {
		NodesArray.push(node);
	}
}
public function UpdateLinksMatrix(externalData:XML):void{
	LinksArray=new Array();
	for each (var link: XML in externalData.descendants("Edge")) {
		LinksArray.push(link);
	}
}
public function UpdateNodeTypeMatrix(externalData:XML):void{
	NodeTypeArray=new Array();
	for each (var node: XML in externalData.descendants("NodeType")) {
		NodeTypeArray.push(node);
	}
}

public function UpdateLinkTypeMatrix(externalData:XML):void{
	LinkTypeArray=new Array();
	ValidNodePairsArray=new Array();
	for each (var edge: XML in externalData.descendants("EdgeType")) {
		LinkTypeArray.push(edge);		
		var allpairs:String=edge.@validnodepairs;
		var allpairsarray:Array = allpairs.split(/,/); 
		var temp_array:Array=new Array();
		temp_array.push( (edge.@name).toString());
		for (var i:int=0;i<allpairsarray.length;i++){
			var singlepair:String=allpairsarray[i];
			var validarray:Array=singlepair.split(/-/);
			temp_array.push(validarray[0]);
			temp_array.push(validarray[1]);			
		}
		ValidNodePairsArray[edge.@name]=temp_array;
	}
} 


/* How Add new node works:
On clicking Add New Node button,the button calls the AddNewNodeForm.mxml .
The user fills out the form,and on Saving,the item to be saved(with its data,
id,etc. is generated and sent back to AddNewNode(item:Item) function, which adds
the new item to graph variable and the data provider of SpringGraph.
we also need to add the new node in externalData:xml variable.
*/	
public function AddNewNode(item:Item,str:String):void{
	myGraph.add(item);							//adding to myGraph
	//		externalData.Nodes.appendChild(str);		//adding to externalData variable
	var temp_nodedata:XML=new XML(str);
	NodesArray.push(temp_nodedata);				//adding to NodesArray
	temp_nodedata=null;
	//var NewNodeXml:String=new String('<Edge id="45" name ="develops" fromID="' + fromID + '" toID="' + toID + '" intensity="1" arrow="1"  edgecolor="#7758FF" />');
	//UpdateNodeTypeMatrix();externalData.Edges.appendChild(NewEdgeXml);	
}

public function updateGraph(e:Event):void{
	modifyTime(0);
}

public function changeGraph(limit:Number):void{
	var disp:UIComponent;
	for each(var edge:XML in LinksArray){
		var debugg1:Number= Date.parse(edge.@creationtime);
		var debugg2:String = edge.@id;
		if(Date.parse(edge.@creationtime)>limit){
			disp = UIComponent(myGraph.edgeRef[edge.@id]);
			if(disp != null){
				if(springgraph.drawingSurface.contains(disp)==true){
					springgraph.drawingSurface.removeChild(disp);
				}
			}
		}
		else{
			disp = UIComponent(myGraph.edgeRef[edge.@id]);
			if(disp != null){
				if(springgraph.drawingSurface.contains(disp)==false){
					springgraph.drawingSurface.addChild(disp);
				}
			}
		}
	}
	for each(var node:XML in NodesArray){
		var isValid:Boolean = false;
		for each(var obj1:Object in myGraph._edges[node.@id] ) {
			for each(var linkData:Object in obj1.link){
				if((Date.parse(linkData.creationtime)<=limit) || (linkData.creationtime=="0000/00/00 00:00:00")){
					isValid = true;
				}
			}
		}
		var targetVbox:FlexItem ;
		targetVbox=FlexItem(Node_Id_Uid[node.@id.toString()]);
		if ( targetVbox != null ){
			targetVbox.showNode = isValid;
		}
	}
}

public function modifyTime(amount:Number):void {
	timeLimit = timeLimit - amount;
	if(timeLimit > (new Date()).getTime()) {
		//Alert.show(youCanNotSeeFM);
		Alert.show(youCanNotSeeFM);
		
		setCurrentTime();
		return;
	}
	machineTime.text = (new Date(timeLimit)).toString();
	changeGraph(timeLimit);
}

public function backOne():void {
	modifyTime(3600000);
}
public function backTwo():void {
	modifyTime(86400000);
}
public function backThree():void {
	modifyTime(2592000000);
}

public function forwardOne():void {
	modifyTime(-3600000);
}
public function forwardTwo():void {
	modifyTime(-86400000);
}
public function forwardThree():void {
	modifyTime(-2592000000);
}

public function setCurrentTime():void{
	var currentDate:Date = new Date();
	timeLimit = currentDate.getTime();
	machineTime.text = currentDate.toString();
	changeGraph(timeLimit);
}
/*  		public function timeChanged():void{
var currentDate:Date = new Date();
var limit:Number = currentDate.getTime();
limit = limit - (timeSlider.value*3600000);
changeGraph(limit);
} */

public function startAutoSim():void {
	if(autoSim.label == 'PauseTimeLine'){
		simTimer.stop();
		autoSim.label = 'ContinueTimeLine';
		return;
	}
	if(autoSim.label == 'ContinueTimeLine'){
		autoSim.label='PauseTimeLine';
		simTimer.reset();
		simTimer.start();
		return;
	}
	var disp:UIComponent;
	for each(var edge:XML in LinksArray){
		disp = UIComponent(myGraph.edgeRef[edge.@id]);
		if(disp != null){
			if(springgraph.drawingSurface.contains(disp)==true){
				springgraph.drawingSurface.removeChild(disp);
			}
		}	
	}
	for each(var node:XML in NodesArray){
		var targetVbox:FlexItem ;
		targetVbox=FlexItem(Node_Id_Uid[node.@id.toString()]);
		if ( targetVbox != null ){
			targetVbox.showNode = false;
		}
	}
	simTimer.addEventListener(TimerEvent.TIMER_COMPLETE,nextNodeDisplay);
	autoSim.label='PauseTimeLine';
	simTimer.start();
}

public function nextNodeDisplay(t:TimerEvent): void {
	var least:Number = (new Date()).getTime();
	var isEnd:Boolean = true;
	var disp:UIComponent;
	for each(var edge:XML in LinksArray){
		if(Date.parse(edge.@creationtime)<least){
			disp = UIComponent(myGraph.edgeRef[edge.@id]);
			if(disp != null){
				if(springgraph.drawingSurface.contains(disp)==false){
					least = Date.parse(edge.@creationtime);
					isEnd = false;
				}
			}
		}
	}
	if(isEnd == false){
		modifyTime(timeLimit - least);
		simTimer.reset();
		simTimer.start();
	}
	else{
		autoSim.label = 'AutoTimeLine';
		simTimer.removeEventListener(TimerEvent.TIMER_COMPLETE,nextNodeDisplay);
	}
}

public function Delete_Node(nodeId:String):void{
	//also need to delete the vbox reference in Node_Id_Uid array.check it 
	
	var index:Number=-1;
	var friends:Array;
	var first:Array;
	var last:Array;
	var item: Item = myGraph.find( nodeId );  	
	
	for each(var node:XML in NodesArray){
		index+=1;
		if (item.id==node.@id){
			break;
		}
	}
	
	friends = NodesArray;
	first=friends.splice(0,index);
	last=friends.splice(1,friends.length-1);
	friends=first.concat(last);
	NodesArray = friends;					//NodesArray Updated
	
	
	/* delete externalData.Nodes.Node;
	for each (var node1: XML in NodesArray) {
	externalData.Nodes.appendChild(node1);
	} */
	
	//To delete all the edges related to the node
	for each(var obj1:Object in myGraph._edges[item.id] ) {
		for each(var linkData:Object in obj1.link){
			Delete_SingleEdge(linkData.id,false) ;							
		}	
	}
	
	/* delete externalData.Edges.Edge;
	for each (var edge1 in LinksArray) {
	externalData.Edges.appendChild(edge1);
	} */
	
	
	//deleting from myGraph variable
	myGraph.remove(item);
	
	item=null; 
	
}



//	public var LoginUserId:String=new String();
public var Current_Item:Item=new Item();

public var current_node_rightClickItem = new Item();

public var Neighbours:Array;
[Bindable] public var people_list:Array;
// changed for login home	
[Bindable] public var people_listXMLListCollection:ArrayCollection;

[Bindable] public var videos_list:Array;	
[Bindable] public var tags_list:Array;	

[Bindable] public var sorted_videos_list:Array;	
[Bindable] public var rating_based:Array;
[Bindable] public var timesseen_based:Array;
[Bindable] public var VideosListByName:Array;




/* Neighbours array stores the node id's with which a particluar node id 
is related ( all the nodes,irrespective of whether the node is soucre or
destination ) Competence nodes cannot have neighbours.
e.g: Neighbours[Albert1]= [Eleni2,Rachel3,jose8] 
means ther r links frm albert1 to eleni2,rachel3 and jose8/
if ther is a link frm eleni2 to albert1,then its specified only in her neighbours*/	

/* 			public function ReadyToLoad(event:Event):void {

}*/


public function LoadVideoNetwork(vid:String): void {
	//showparticularedgetype.addEventListener(DropdownEvent.CLOSE,getedgetype);
	springgraph.dataProvider = myGraph;
	UpdateNodeTypeMatrix(Innoxml);
	UpdateLinkTypeMatrix(Innoxml);
	
	Current_Item = fullGraph.find(vid) ;
	if ( Current_Item == null ){
		Alert.show('Information about video is not found. Please contact administrator','Error !');
		return;
	} 
	myGraph.add(Current_Item);	
	NodesArray.push( Current_Item.data );	
	
	Neighbourhood([vid],["All@"],isFirst);
	isFirst = false;	   		
	drawNodesEdges();
	
	currentItem_SetTimer.addEventListener(TimerEvent.TIMER_COMPLETE, setCurrentItem);
	new_current_id = vid;
	currentItem_SetTimer.reset();
	currentItem_SetTimer.start();
	setCurrentTime();
	
	
		
}

 


public function LoadNetwork(): void {
	//showparticularedgetype.addEventListener(DropdownEvent.CLOSE,getedgetype);
	springgraph.dataProvider = myGraph;
	UpdateNodeTypeMatrix(Innoxml);
	UpdateLinkTypeMatrix(Innoxml);
	
	Current_Item = fullGraph.find(Univ_LoginId) ;
	if ( Current_Item == null ){
		Alert.show('Information about user is not found. Please contact administrator','Error !');
		return;
	} 
	myGraph.add(Current_Item);	
	NodesArray.push( Current_Item.data );	
	
	Neighbourhood([Univ_LoginId],["All@"],isFirst);
	isFirst = false;	   		
	drawNodesEdges();
	
	currentItem_SetTimer.addEventListener(TimerEvent.TIMER_COMPLETE, setCurrentItem);
	new_current_id = Univ_LoginId;
	currentItem_SetTimer.reset();
	currentItem_SetTimer.start();
	setCurrentTime();
	//	drawedgetypes( 'Knows' );	
}


//to add the nodes,edges which r not in display.
public function ShowFullNetwork():void{		
	for each(var item:Item in fullGraph.nodes){
		if (myGraph.find(item.id) ==null)
		myGraph.add(item);
	}
	for each(var node1:Object in fullGraph._edges){
		var index:Number=1;
		for each(var node2:Object in node1){
			for each(var linkdata:Object in node2.link) {
				if(  myGraph.edgeRef[linkdata.id] == null ){
					if ( myGraph.getData(linkdata.SourceID,linkdata.id) == null ) {
						var item1:Item=myGraph.find(linkdata.SourceID);
						var item2:Item=myGraph.find(linkdata.DestID);
						myGraph.link(item1,item2,linkdata);							
						var NewEdgeXml:String=new String('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" tooltip ="' + linkdata.tooltip + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity + '" edgecolor="' + linkdata.edgecolor + '"creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') +'"/>'); 
						var tlinkxml:XML=new XML(NewEdgeXml);
						LinksArray.push(tlinkxml);							
					}
				}
				index+=1;
			}
		}
	} 
	NewEdgeXml=null;
	tlinkxml=null;
	return;
}


//This function will create nodes and edges from the data in NodesArray and parents.
public function drawNodesEdges():void{
	for each(var node:XML in NodesArray){
		if ( myGraph.nodes[node.@id] == null ){
			var item:Item = fullGraph.find(node.@id);
			myGraph.add(item);
		}
	}
	//	NumofNodes.text = NodesArray.length + " items visible";
	myGraph.changed();
	for each (var edgeinfo:Array in parents){
		var data:Object=new Object();
		if ( myGraph.getData(edgeinfo[1], edgeinfo[2]) == null ){
			data=fullGraph.getData(edgeinfo[1], edgeinfo[2]);
			var fromItem:Item=myGraph.find(data.SourceID);
			var toItem:Item=myGraph.find(data.DestID);
			myGraph.link(fromItem, toItem, data);
		}
		data=null;
	}
	for each( item in myGraph.nodes){	
		
		//var myGraphneighbours:Array ;
		
		var myGraphneighbours:Array = myGraph.neighbour_Ids(item.id); 
		//var fullGraphneighbours:Array ;
		var fullGraphneighbours:Array = fullGraph.neighbour_Ids(item.id);
		if ( myGraphneighbours.length == fullGraphneighbours.length){	
			var targetVbox:FlexItem ;
			targetVbox=FlexItem(Node_Id_Uid[item.id.toString()]);
			if ( targetVbox != null ) {
				targetVbox.showbutton = true;
			}
		}
		
	}
	myGraph.changed();
	timer_edgelistener.addEventListener(TimerEvent.TIMER_COMPLETE, ConfigureListeners);
	timer_edgelistener.reset();
	timer_edgelistener.start();
}


/* 1st remove the glow to the current_central item and its friends.
then set the glow to the nodeId(Id) vbox and set it as Central_Item 
then get its neighbours from myGraph variable and set them a yellow glow*/

public var colored_friends:Array=new Array();
public var new_current_id:String=new String();

public function setCurrentItem(t:TimerEvent):void{
	//removing glow for prev current item and its neighbours
	var curr_item:UIComponent=new UIComponent();
	try{
		curr_item=UIComponent(Node_Id_Uid[Current_Item.id]);
	  		} catch(Err:Error) {curr_item=null;}
	        if ( curr_item != null ) {
		        curr_item.filters=null;
		        for each(var nodeId:String in colored_friends){
		        	curr_item=new UIComponent();
		       	 	curr_item=UIComponent(Node_Id_Uid[nodeId]);
		       	 	if ( curr_item != null )
					curr_item.filters=null;
		        }
	        }
	        
	        //glow for new item and its neighbours	
			Current_Item=fullGraph.find(new_current_id);				
			var targetVbox:UIComponent=new UIComponent();
			targetVbox=UIComponent(Node_Id_Uid[new_current_id]);
			if ( targetVbox != null){
				targetVbox.filters=[new GlowFilter(0xff33,0.5, 8, 8, 5, 5)];	
				colored_friends=new Array();
				for each( var friend:Object in myGraph.neighbors(new_current_id) ) {
					if ( friend.link[1].SourceID==new_current_id )
					colored_friends.push(friend.link[1].DestID);
					else
					colored_friends.push(friend.link[1].SourceID);
				}
				for each( nodeId in colored_friends){
					targetVbox=new UIComponent();
					targetVbox=UIComponent(Node_Id_Uid[nodeId]);
					if ( targetVbox != null)
					targetVbox.filters=[new GlowFilter(0xffff99,0.5, 8, 8, 5, 5)];
				}
			} 
			currentItem_SetTimer.stop();
			currentItem_SetTimer.start();		
			/* 		curr_item=null;
			targetVbox=null; */
}



public function validate(linkData:Object,nodeid:String,criteria:Array):Boolean
{
	/* 	 format of 'parents' array parents([child,parent,linkid])
	eg. parent[0]=["Eleni4","Albert1","5"]
	the node with id Eleni4 was added to the network coz of some action on node with id
	Albert1.the id of edge between the two nodes is 4	
	<NodeType name="People"/>
	<NodeType name="Videos"/>
	<NodeType name="Tags" />	 */
	
	//check whether the edge is already in the network.the 'parents' array becomes null only when
	//the user does not want to retain the already existing nodes and edges.in that case,
	//all the edges will pass thru this for loop.
	for each(var arr:Array in parents){
		if ( arr[2]==linkData.id )
		return false;
	}
	
	var item:Item=fullGraph.find(nodeid);
	switch ( criteria[0])
	{
		case "All":
		return true;
		case "People":
		if (item.data.@nodetype=="People")
		if ( (criteria[1] == 'All') || (criteria[1] == linkData.name.toString()) )
		return true;
		break;			
		case "Videos":
		if (item.data.@nodetype=="Videos")
		if ( (criteria[1] == 'All') || (criteria[1] == linkData.name.toString()) )
		return true;  
		break;
		case "Tags":
		if (item.data.@nodetype=="Tags")
		if ( (criteria[1] == 'All') || (criteria[1] == linkData.name.toString()) )
		return true;
		break;
	}
	return false;
}

public var parents:Array=new Array();



/*  
function Neighbourhood
parameters	
BaseIDs:
the node id's on which action is to take place	 			
*/
public function Neighbourhood(BaseIDs:Array,queryarray:Array,retain:Boolean):void{
	if(queryarray.length==0)
	return;
	var querydegree:String=new String(queryarray.pop());
	var criteria:Array=querydegree.split(/@/);
	
	var loc_parents:Array=new Array();
	var ExistingNodesID:Array=new Array();
	var ExistingEdgesID:Array=new Array(); 
	
	if(retain){
		for each (var node: XML in NodesArray) {
			ExistingNodesID.push((node.@id).toString());
		}
		for each (var edge: XML in LinksArray) {
			ExistingEdgesID.push( (edge.@id).toString());
		}
	}
	else{
		for each (edge in LinksArray) {
			var disp:UIComponent;
			disp = UIComponent(myGraph.edgeRef[edge.@id]);
			if(disp != null){
				disp.graphics.clear();
				if(springgraph.drawingSurface.contains(disp)==true){
					springgraph.drawingSurface.removeChild(disp);
				}
			}	
		}
		Node_Id_Uid=new Array();
		NodesArray=new Array();
		LinksArray=new Array();
		myGraph.empty();   			
		var local_current_item:Item=new Item(new_current_id);
		local_current_item=fullGraph.find(new_current_id);
		try{
			myGraph.add( local_current_item );
			
			NodesArray.push(local_current_item.data);	
			ExistingNodesID.push((local_current_item.data.@id).toString());
	   			} catch(Err:Error) {}
	   			parents=new Array();
	}
	
	
	var NewNodesID:Array=new Array();
	var NewEdgesID:Array=new Array(); 
	tempnodeshistory = new Array();
	tempedgeshistory = new Array();
	
	
	if (nodesstatus[statuscount-1] == "focusin"){
	}
	
	else{
		nodesstatus[statuscount] = "expand";
		statuscount = statuscount + 1;
	}
	
	for each(var curnodeID:String in BaseIDs){
		var myNeighbours:Object;
		myNeighbours=fullGraph.neighbors(curnodeID);
		var NewNodesID_len:Number=NewNodesID.length;
		var NewEdgesID_len:Number=NewEdgesID.length;
		
		for each (var myObj:Object in myNeighbours ) 
		{
			var num_edges:Number=myObj.num_edges;
			var count:int=1;
			var index:Number;
			var index_existing:Number;
			while(count<=num_edges){
				if ( myObj.link[count].SourceID == 	curnodeID){
					//curnodeId is the source of this link
					if ( validate(myObj.link[count],myObj.link[count].DestID,criteria) ){
						//parents([child,parent,linkid])
						
						//to add node
						index=NewNodesID.indexOf(myObj.link[count].DestID);
						index_existing=ExistingNodesID.indexOf(myObj.link[count].DestID);
						if( (index==-1) && (index_existing == -1 ) ){
							NewNodesID.push(myObj.link[count].DestID);
						}
						//to add edge
						index_existing=ExistingEdgesID.indexOf(myObj.link[count].id);
						index=NewEdgesID.indexOf(myObj.link[count].id);
						if( (index==-1) && (index_existing==-1) ){
							NewEdgesID.push(myObj.link[count].id);
							loc_parents.push([myObj.link[count].DestID,myObj.link[count].SourceID,myObj.link[count].id]);
						}
					}	
				}
				else {	//curNodeId is the destination of this link
					if ( validate(myObj.link[count],myObj.link[count].SourceID,criteria) ){
						
						//to add node
						index=NewNodesID.indexOf(myObj.link[count].SourceID);
						index_existing=ExistingNodesID.indexOf(myObj.link[count].SourceID);
						if( (index==-1) && (index_existing == -1 ) ){
							NewNodesID.push(myObj.link[count].SourceID);
							
						}
						//to add edge
						index_existing=ExistingEdgesID.indexOf(myObj.link[count].id);
						index=NewEdgesID.indexOf(myObj.link[count].id);
						if( (index==-1) && (index_existing==-1) ){
							NewEdgesID.push(myObj.link[count].id);
							loc_parents.push([myObj.link[count].SourceID,myObj.link[count].DestID,myObj.link[count].id]);
						}
					}
				}
				count=count+1;
			}	
		}
	}			
	
	
	
	for each ( var node_ID:String in NewNodesID) {
		if ( myGraph.find(node_ID) == null ) {
			var item:Item=fullGraph.find(node_ID);
			if (nodesstatus[statuscount-1] != "focusin")
			{
				tempnodeshistory.push(item.id);
			} 
			NodesArray.push(item.data);
			
		}
	}
	
	
	for each (var edge_ID:String in NewEdgesID) {
		for each( var edge1:Array in loc_parents){
			if (edge1[2]==edge_ID){
				var data1:Object=new Object();
				data1=fullGraph.getData(edge1[0], edge_ID);
				
			//	var NewEdgeXml:String=new String('<Edge id="' + data1.id + '" name ="' + data1.name + '" fromID="' + data1.SourceID + '" toID="' + data1.DestID + '" intensity="' + data1.intensity + '"tooltip = "'+data1.tooltip+'" edgecolor="' + data1.edgecolor + '"creationtime = "'+(data1.creationtime.toString()).replace(myPattern,'/')+'"/>'); 
				var NewEdgeXml:String=new String('<Edge id="' + data1.id + '" name ="' + data1.name  + '" fromID="' + data1.SourceID + '" toID="' + data1.DestID + '" intensity="' + data1.intensity + '" tooltip="'+data1.tooltip+'" edgecolor="' + data1.edgecolor + '" creationtime="'+(data1.creationtime.toString()).replace(myPattern,'/')+'"/>');
				var tlinkxml:XML=new XML(NewEdgeXml);
				if (nodesstatus[statuscount-1] != "focusin")
				{
					tempedgeshistory.push(tlinkxml);
				}
				LinksArray.push(tlinkxml);
				NewEdgeXml=null; 
				data1=null;
			}
		}
	}
	
	for each(var data:Array in loc_parents)
	parents.push(data);
	
	Neighbourhood(NewNodesID,queryarray,true);  	
	return;
}  

public function IsParent(id:String):Boolean{
	for each(var data:Array in parents)
	if ( data[1]==id)
	return true;
	return false;
}

//parent[dest,src,id]



//	public var traversed_ids:Array=new Array();
//	public var all_paths:Array=new Array();
//	public var PATHCOUNT:Number=0;




/** pops up the about box **/
/*
private function PopAboutBox(): void {
	var aboutWindow:IFlexDisplayObject = new IFlexDisplayObject();
	PopUpManager.addPopUp(aboutWindow, this, true);		
	PopUpManager.centerPopUp(aboutWindow);		
}

*/

/*	private function showMyNetwork():void{
for each (var edge:XML in LinksArray) {
var disp:UIComponent;
disp = UIComponent(myGraph.edgeRef[edge.@id]);
disp.graphics.clear();
springgraph.drawingSurface.removeChild(disp);	
}
springgraph.empty();

Node_Id_Uid=new Array();
NodesArray=new Array();
LinksArray=new Array();
myGraph.empty();   			
var Current_Item: Item = new Item(LoginUserId);
var data:XML= new XML();
var datastr:String=new String('<Node id="' + data.@id + '" name="' + data.@name + '" nodetype="' + data.@nodetype + '" picture="' + data.@picture + '" />');
var nodedata:XML=new XML(datastr);
Current_Item.data=nodedata;	
NodesArray.push(nodedata);	
myGraph.add(Current_Item);
NodesArray.push(nodedata);	

Neighbourhood([LoginUserId],["All@"],true);
drawNodesEdges();
//		setCurrentItem(LoginUserId);

data=null;
datastr=null;
nodedata=null; 
}	*/


public var SearchWindowOpen:Boolean = false;
public var searchwin:box_searchwindow;
public function ShowSearchWindow():void{
	if ( SearchWindowOpen == true )
	return;
	searchwin	=	box_searchwindow(PopUpManager.createPopUp(this,box_searchwindow,false));
	searchwin.x	=	325;
	searchwin.y	=	85;
	SearchWindowOpen = true;
} 


public function activateTimeLine():void{
	if(timeLineBox.visible==false){
		timeLineBox.visible=true;
	}
}	

/* This function creates two arrays 
1. ProgrammesArray	with all Programme names
2. CompsArray 	with all Competence names

and Iedgecolor:uint which will be used to store the color of 'has attended' edge.
When we form a new edge,we use this information in CreateProfile() function
*/

/*    			[Bindable]
public var ProgrammesArray:Array=new Array();
[Bindable]
public var CompsArray:Array=new Array();
*/	








/* this function is called on CreationComplete
it gets the name of the xml file(stored in parameters) which has the latest network details
and requests it. */


// executed when a node is double clicked
public function godDoubleClick( targetItem :Item ):void{
	var targetVbox:FlexItem ;
	targetVbox=FlexItem(Node_Id_Uid[targetItem.id]);
	if ( targetVbox != null ) {
		if(targetVbox.showbutton == true)
		return;
	}
	if(retaincheck.selected == false)
	{
		focusnode(targetItem);
		return;
	}
	if(showcount != historycount){
		clearhistory();
		nodesstatus[statuscount] = "expand";
		statuscount = statuscount + 1;
	}
	if (historycount == 0){ 
		saveprevious();
		historycount = historycount + 1;
		showcount    = showcount + 1;
		if (showcount != 0)
		backbutton.visible = true;
	}		   
	var myNeighbours:Array = myGraph.neighbour_Ids ( targetItem.id ) ;       
	var BaseIds:Array=new Array();
	var queryarray:Array=new Array();
	queryarray.push("All@");	
	BaseIds.push( targetItem.id );
	Neighbourhood(BaseIds,queryarray,retaincheck.selected);
	drawNodesEdges();		
	new_current_id = targetItem.id;
	//	setCurrentItem(new TimerEvent(TimerEvent.TIMER_COMPLETE));
	queryarray=null;
	BaseIds=null;	
	myGraph.changed();
	springgraph.addEventListener(FlexEvent.UPDATE_COMPLETE,listenFn);
	//    		changeGraph(timeLimit);
	/*  			simTimer2.reset();
	simTimer2.start();  */
	saveprevious();
	historycount = historycount + 1;
	showcount    = showcount + 1;
	if (showcount != 0)
	backbutton.visible = true; 			
	return;
}







private function ChangeArrowSetting():void{
	if (chkArrow.selected == true) 
	chkArrow.toolTip= toolTip_removeArrow;
	else
	chkArrow.toolTip= toolTip_showArrow;
	return;
}

private function newWin(Url:String):void {
	var urlRequest:URLRequest = new URLRequest(Url);
	navigateToURL(urlRequest, '_blank');
}




/** functions related to fullscreen event in spring_view state	- BEGIN **/

private function InitializeFullScreenHandler(evt:Event):void{
	
//	loadCSS();
	//initializeTextVariables(Application.application.parameters.tubeName);
	Application.application.stage.addEventListener(FullScreenEvent.FULL_SCREEN, fullScreenHandler);
}

private function fullScreenHandler(evt:FullScreenEvent):void {
	if ( (currentState == 'spring_view') || (currentState == 'ProfileView') ){  			 		
		if (evt.fullScreen) {                	
			btnFullscreen.visible=false;
			btnNormalscreen.visible=true;
			controlpanelwindow.y= controlpanelwindow.y + 100;
			btn_show_controlpanel.y = btn_show_controlpanel.y + 100;
			// Do something specific here if we switched to full screen mode. 
		} else {
			btnFullscreen.visible=true;
			btnNormalscreen.visible=false;
			controlpanelwindow.y= controlpanelwindow.y - 100;
			btn_show_controlpanel.y = btn_show_controlpanel.y - 100;
			//   Do something specific here if we switched to normal mode. 
		}
	} else if( (currentState == 'new_ui') || (currentState == 'full_screen') ) {
		if(!evt.fullScreen) {
			currentState = 'new_ui';
		}
	}
}

private function toggleFullScreen():void {
	try {
		switch (Application.application.stage.displayState) {
			case StageDisplayState.FULL_SCREEN:            	
			//   If already in full screen mode, switch to normal mode. 
			Application.application.stage.displayState = StageDisplayState.NORMAL; 
			break;
			default:
			//    If not in full screen mode, switch to full screen mode. 
			Application.application.stage.displayState = StageDisplayState.FULL_SCREEN;     
			break;
		}
	} catch (err:SecurityError) {
		// ignore
	}
} 

/** functions related to fullscreen event in spring_view state	-- END **/































// when the history back button is clicked
// We first reduce the showcount by 1 and check the corresponding nodesstatus , if the status is expand
// then delete the nodes in the nodeshistory[showcount] and edges from edgeshistory[showcount] , if status is delete
// then add the nodes and edges in the nodeshistory[showcount] and edges from edgeshistory[showcount] from the
// current network , if Focusin we delete the current network and add the nodes and edges in the
// nodeshistory[showcount] and edges from edgeshistory[showcount]


public function showbackhistory():void{
	
	showcount = showcount - 1;
	
	if(showcount == 1)
	backbutton.visible= false;
	if(showcount != historycount )
	forwardbutton.visible= true;
	
	// if the showcount is expand delete the nodes from myGraph
	if (nodesstatus[showcount] == "expand")
	{
		for each(var edge:XML in edgeshistory[showcount]){
			var linkdata:Object=new Object();
			linkdata.id=edge.@id.toString();
			linkdata.SourceID=edge.@fromID.toString();
			linkdata.DestID=edge.@toID.toString();
			Delete_SingleEdge(linkdata.id,false);
		}
		
		
		for each(var id:String in nodeshistory[showcount]){
			var item:Item = myGraph.find(id);
			Delete_Node( item.id );
		}
		
	}
	// if the status is delete draw the nodes and edges
	else if (nodesstatus[showcount] == "delete")
	{
		for each( id in nodeshistory[showcount]){
			item = fullGraph.find(id);
			if (myGraph.find(item.id) ==null){
				myGraph.add(item);
				NodesArray.push(item.data);
			}
		}
		
		myGraph.changed();
		for each( edge in edgeshistory[showcount]){
			linkdata = new Object();
			linkdata.id=edge.@id.toString();
			linkdata.name=edge.@name.toString();
			linkdata.SourceID=edge.@fromID.toString();
			linkdata.DestID=edge.@toID.toString();
			linkdata.edgecolor= edge.@edgecolor.toString();
			linkdata.intensity=edge.@intensity.toString();
			linkdata.arrow =edge.@arrow.toString();
			linkdata.creationtime = (edge.@creationtime.toString()).replace(myPattern,'/');
			var fromItem:Item=myGraph.find(linkdata.SourceID);
			var toItem:Item=myGraph.find(linkdata.DestID);
			myGraph.link(fromItem, toItem, linkdata);
			LinksArray.push(edge);
		}
		
		
	}
	// we check the status and if it is focusoutcoffee we reduce the rigidity to 0.05 and redraw the nodes and edges
	else if (nodesstatus[showcount] == "focusoutedge") {
		
		if (nodesstatus[showcount-2] == "focusoutedge"){
			
			springgraph.forceDirectedLayout.newRigidity = edgerigidity;
		}
		else{
			springgraph.forceDirectedLayout.newRigidity = normalrigidity;
		}
		
		for each ( edge in LinksArray) {
			var disp:UIComponent;
			disp = UIComponent(myGraph.edgeRef[edge.@id]);
			if(disp != null){
				disp.graphics.clear();
				if(springgraph.drawingSurface.contains(disp)==true){
					springgraph.drawingSurface.removeChild(disp);
				}
			}
		}
		
		Node_Id_Uid=new Array();
		NodesArray=new Array();
		LinksArray=new Array();
		parents=new Array();
		myGraph.empty();
		for each( id in nodeshistory[showcount-1]){
			item = fullGraph.find(id);
			if (myGraph.find(item.id) ==null){
				myGraph.add(item);
				NodesArray.push(item.data);
			}
		}
		
		
		for each( edge in edgeshistory[showcount-1]){
			linkdata =new Object();
			linkdata.id =edge.@id.toString();
			linkdata.name =edge.@name.toString();
			linkdata.SourceID =edge.@fromID.toString();
			linkdata.DestID =edge.@toID.toString();
			linkdata.edgecolor = edge.@edgecolor.toString();
			linkdata.intensity =edge.@intensity.toString();
			linkdata.arrow =edge.@arrow.toString();
			linkdata.creationtime=(edge.@creationtime.toString()).replace(myPattern,'/');
			fromItem =myGraph.find(linkdata.SourceID);
			toItem =myGraph.find(linkdata.DestID);
			myGraph.link(fromItem, toItem, linkdata);
			LinksArray.push(edge);
		}
		showcount = showcount - 1;
	}
	
	// if the status is focusin or focusout
	
	else {
		// the case with focusout
		if (nodesstatus[showcount-2] == "focusoutedge") {
			springgraph.forceDirectedLayout.newRigidity = edgerigidity;
		}
		// the case with focusin
		else if(nodesstatus[showcount-2] == "focusoutedge"){
			springgraph.forceDirectedLayout.newRigidity = edgerigidity;
		}
		else{
			springgraph.forceDirectedLayout.newRigidity = normalrigidity;
		}
		
		for each ( edge in LinksArray) {
			disp = UIComponent(myGraph.edgeRef[edge.@id]);
			if(disp != null){
				disp.graphics.clear();
				if(springgraph.drawingSurface.contains(disp)==true){
					springgraph.drawingSurface.removeChild(disp);
				}
			}
		}
		Node_Id_Uid=new Array();
		NodesArray=new Array();
		LinksArray=new Array();
		parents=new Array();
		myGraph.empty();
		
		for each( id in nodeshistory[showcount-1]){
			item = fullGraph.find(id);
			if (myGraph.find(item.id) ==null){
				myGraph.add(item);
				NodesArray.push(item.data);
			}
		}
		
		
		for each( edge in edgeshistory[showcount-1]){
			linkdata = new Object();
			linkdata.id = edge.@id.toString();
			linkdata.name = edge.@name.toString();
			linkdata.SourceID = edge.@fromID.toString();
			linkdata.DestID = edge.@toID.toString();
			linkdata.edgecolor= edge.@edgecolor.toString();
			linkdata.intensity= edge.@intensity.toString();
			linkdata.arrow = edge.@arrow.toString();
			linkdata.creationtime=(edge.@creationtime.toString()).replace(myPattern,'/');
			fromItem = myGraph.find(linkdata.SourceID);
			toItem = myGraph.find(linkdata.DestID);
			myGraph.link(fromItem, toItem, linkdata);
			LinksArray.push(edge);
		}
		
		showcount = showcount - 1;
		
	}
	
	
	checkshowbutton();
	
	if(showcount == 1)
	backbutton.visible= false;
	if(showcount != historycount )
	forwardbutton.visible= true;
	return;
}


// when forward historybutton is clicked
// We check the nodesstatus , if the status is delete
// then delete the nodes in the nodeshistory[showcount] and edges from edgeshistory[showcount] , if expand is the status
// then add the nodes and edges in the nodeshistory[showcount] and edges from edgeshistory[showcount] from the
// current network , if focusin increase the showcount by 1 and the increased showcount is the searched for
// focusout and focusoutcoffee draws the nodes in the nodeshistory[showcount] and edges from edgeshistory[showcount] but the
// difference lies in setting the rigidity

public function showforwardhistory():void{
	
	
	if(showcount == 0) {
		showcount = showcount + 1;
	}
	// if the status is focusin increase the showcount by 1
	if (nodesstatus[showcount] == "focusin") {
		showcount = showcount + 1;
		if(showcount == historycount) {
			saveprevious();
			forwardbutton.visible = false;
		}
	}
	
	// if showcount is expand add the nodea and edges in myGraph
	if (nodesstatus[showcount] == "expand") {
		for each(var id:String in nodeshistory[showcount]){
			item = fullGraph.find(id);
			if (myGraph.find(item.id) ==null){
				myGraph.add(item);
				NodesArray.push(item.data);
			}
		}
		
		myGraph.changed();
		
		for each(var edge:XML in edgeshistory[showcount]){
			
			var linkdata:Object=new Object();
			linkdata.id=edge.@id.toString();
			linkdata.name=edge.@name.toString();
			linkdata.SourceID=edge.@fromID.toString();
			linkdata.DestID=edge.@toID.toString();
			linkdata.edgecolor= edge.@edgecolor.toString();
			linkdata.intensity=edge.@intensity.toString();
			linkdata.arrow =edge.@arrow.toString();
			linkdata.creationtime=(edge.@creationtime.toString()).replace(myPattern,'/');
			var fromItem:Item=myGraph.find(linkdata.SourceID);
			var toItem:Item=myGraph.find(linkdata.DestID);
			myGraph.link(fromItem, toItem, linkdata);
			LinksArray.push(edge);
		}
		showcount = showcount + 1;
		
	}
	
	// if the status is focusout draw the nodes and edges from nodeshistory
	
	else if (nodesstatus[showcount] == "focusout")
	{
		springgraph.forceDirectedLayout.newRigidity = normalrigidity;
		for each ( edge in LinksArray) {
			var disp:UIComponent;
			disp = UIComponent(myGraph.edgeRef[edge.@id]);
			if(disp != null){
				disp.graphics.clear();
				if(springgraph.drawingSurface.contains(disp)==true){
					springgraph.drawingSurface.removeChild(disp);
				}
			}
		}
		Node_Id_Uid=new Array();
		NodesArray=new Array();
		LinksArray=new Array();
		parents=new Array();
		myGraph.empty();
		
		for each( id in nodeshistory[showcount]){
			var item:Item = fullGraph.find(id);
			if (myGraph.find(item.id) ==null){
				myGraph.add(item);
				NodesArray.push(item.data);
			}
		}
		
		myGraph.changed();
		
		for each( edge in edgeshistory[showcount]){
			
			linkdata =new Object();
			linkdata.id=edge.@id.toString();
			linkdata.name=edge.@name.toString();
			linkdata.SourceID=edge.@fromID.toString();
			linkdata.DestID=edge.@toID.toString();
			linkdata.edgecolor= edge.@edgecolor.toString();
			linkdata.intensity=edge.@intensity.toString();
			linkdata.arrow =edge.@arrow.toString();
			linkdata.creationtime=(edge.@creationtime.toString()).replace(myPattern,'/');
			fromItem =myGraph.find(linkdata.SourceID);
			toItem =myGraph.find(linkdata.DestID);
			myGraph.link(fromItem, toItem, linkdata);
			LinksArray.push(edge);
		}
		
		showcount = showcount + 1;
		
	}
	// if the status is focusoutcoffee draw the nodes and edges from nodeshistory but set the rigidity is 0.05
	else if (nodesstatus[showcount] == "focusoutedge")
	{
		springgraph.forceDirectedLayout.newRigidity = edgerigidity;
		for each ( edge in LinksArray) {
			disp = UIComponent(myGraph.edgeRef[edge.@id]);
			if(disp !=null){
				disp.graphics.clear();
				if(springgraph.drawingSurface.contains(disp)==true){
					springgraph.drawingSurface.removeChild(disp);
				}
			}
		}
		Node_Id_Uid=new Array();
		NodesArray=new Array();
		LinksArray=new Array();
		parents=new Array();
		myGraph.empty();
		
		for each(id in nodeshistory[showcount]){
			item = fullGraph.find(id);
			if (myGraph.find(item.id) ==null){
				myGraph.add(item);
				NodesArray.push(item.data);
			}
		}
		
		myGraph.changed();
		for each( edge in edgeshistory[showcount]){
			
			linkdata=new Object();
			linkdata.id=edge.@id.toString();
			linkdata.name=edge.@name.toString();
			linkdata.SourceID=edge.@fromID.toString();
			linkdata.DestID=edge.@toID.toString();
			linkdata.edgecolor= edge.@edgecolor.toString();
			linkdata.intensity=edge.@intensity.toString();
			linkdata.arrow =edge.@arrow.toString();
			linkdata.creationtime=(edge.@creationtime.toString()).replace(myPattern,'/');
			fromItem =myGraph.find(linkdata.SourceID);
			toItem =myGraph.find(linkdata.DestID);
			myGraph.link(fromItem, toItem, linkdata);
			LinksArray.push(edge);
		}
		
		showcount = showcount + 1;
		
	}
	// if the statuscount is delete then delete the nodes and edges in the nodeshistory and edgeshistory from myGraph
	else
	{
		for each( edge in edgeshistory[showcount]){
			linkdata=new Object();
			linkdata.id=edge.@id.toString();
			Delete_SingleEdge(linkdata.id,false);
		}
		
		for each( id in nodeshistory[showcount]){
			item = myGraph.find(id);
			Delete_Node( item.id )
		}
		myGraph.changed();
		showcount = showcount + 1;
	}
	
	// we then check for the nodes that are fully expanded and show the red circle on the side
	
	checkshowbutton();
	
	if(showcount != 1)
	backbutton.visible= true;
	if(showcount == historycount )
	forwardbutton.visible = false;
	
	
	
	return;
}


// called form searchwindow to save the search

public function savecurrentdisplay():void{
	tempnodeshistory = new Array();
	tempedgeshistory = new Array();
	for each(var node:XML in NodesArray){
		tempnodeshistory.push(node.@id);
	}
	
	for each(var edge:XML in LinksArray){
		tempedgeshistory.push(edge);
	}
	
	nodesstatus[statuscount] = "expand";
	showcount = showcount + 1;
	statuscount = statuscount + 1;
	backbutton.visible = false;
	
	
}
// we call this function to save the new added/deleted nodes
public function saveprevious():void{
	
	nodeshistory[historycount] = tempnodeshistory;
	edgeshistory[historycount] = tempedgeshistory;
	
}



// this function is executed when focus is clicked
// Deletes the existing network and focus the selected item , show the item and its neighbours
// We call the neighbourhood function with the base id as the selected item , the query array as People@all
// retain as false



public function focusnode(targetItem :Item):void{		
	if (historycount == 0) {
		saveprevious();
		historycount = historycount + 1;
		showcount = showcount + 1;
		backbutton.visible = true;
	}
	
	// start history from beginning by deleting the previous entries
	if(showcount != historycount) {
		clearhistory();
		nodesstatus[statuscount] = "focusin";
		statuscount = statuscount + 1;
		// save the current network
		for each(var node:XML in NodesArray){
			tempnodeshistory.push(node.@id);
		}
		for each( var edge:XML in LinksArray){
			tempedgeshistory.push(edge);
		}
		saveprevious();
		historycount = historycount + 1;
		showcount = showcount + 1;
		backbutton.visible = true;
	}
	
	
	// now we keep a seperate status called focusin because for focus we store the whole myGraph content
	else
	{
		tempnodeshistory = new Array();
		tempedgeshistory = new Array();
		nodesstatus[statuscount] = "focusin";
		statuscount = statuscount + 1;
		for each(node in NodesArray){
			tempnodeshistory.push(node.@id);
		}
		for each( edge in LinksArray){
			tempedgeshistory.push(edge);
		}
		saveprevious();
		historycount = historycount + 1;
		showcount = showcount + 1;
		backbutton.visible = true;
	}
	
	// call the neighbourbood function with retain as false
	
	var BaseIds:Array=new Array();
	var queryarray:Array=new Array();
	queryarray.push("All@");
	BaseIds.push( targetItem.id );
	Neighbourhood(BaseIds,queryarray,false);
	drawNodesEdges();
	new_current_id = targetItem.id;
	queryarray=null;
	BaseIds=null;
	myGraph.changed();
	springgraph.addEventListener(FlexEvent.UPDATE_COMPLETE,listenFn);
	//		changeGraph(timeLimit);
	//		modifyTime(0);
	/*  			simTimer2.reset();
	simTimer2.start();  */
	tempnodeshistory = new Array();
	tempedgeshistory = new Array();
	for each( node  in NodesArray){
		tempnodeshistory.push(node.@id);
	}
	
	for each( edge  in LinksArray){
		tempedgeshistory.push(edge);
	}
	
	saveprevious();
	nodesstatus[statuscount] = "focusout";
	statuscount = statuscount + 1;
	historycount = historycount + 1;
	showcount = showcount + 1;
	backbutton.visible = true;
	return;
}



public function displayAllRelationShips(targetItem:Item):void {
	
	//myGraph
	
	for each(var node1:Object in fullGraph._edges){
		var index:Number=1;
		for each(var node2:Object in node1){
			for each(var linkdata:Object in node2.link) {
				if(  myGraph.edgeRef[linkdata.id] == null ){
					if ( myGraph.getData(linkdata.SourceID,linkdata.id) == null ) {
						var item1:Item=myGraph.find(linkdata.SourceID);
						var item2:Item=myGraph.find(linkdata.DestID);
					
						// if the nodes exist then only add the edge	
							if( ((item1 != null)&&(item2 !=null)) ) {
								myGraph.link(item1,item2,linkdata);							
							//	var NewEdgeXml:String=new String('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity + '" edgecolor="' + linkdata.edgecolor + '"creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') +'"/>'); 
								var NewEdgeXml:String=new String('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" tooltip ="' + linkdata.tooltip + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity + '" edgecolor="' + linkdata.edgecolor + '"creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') +'"/>');
								var tlinkxml:XML=new XML(NewEdgeXml);
								LinksArray.push(tlinkxml);
							}
												
					}
				}
				index+=1;
			}
		}
	} 
	NewEdgeXml=null;
	tlinkxml=null;
	return;
			
	
}



// this function is executed when the user clicks on the expand
// We expand the neighbours on top of the existing network. We also show the red button
// on the node to imply it is fully expanded
// We call the neighbourhood function with the base id as the selected item , the query array as People@all
// retain as true
// Input -- the node item
public function opennodes(targetItem :Item):void{
	
	var targetVbox:FlexItem ;
	targetVbox=FlexItem(Node_Id_Uid[targetItem.id]);
	
	// to check if it is aldready expanded
	if ( targetVbox != null ) {
		if(targetVbox.showbutton == true)
		return;
	}
	var BaseIds:Array=new Array();
	var queryarray:Array=new Array();
	// this way we start the history from beginning
	if(showcount != historycount){
		clearhistory();
		nodesstatus[statuscount] = "expand";
		statuscount = statuscount + 1;
	}
	// save the existing graph
	if (historycount == 0) {
		saveprevious();
		historycount = historycount + 1;
		showcount = showcount + 1;
		backbutton.visible = true;
	}
	
	// call the neighbourhood function
	queryarray.push("All@");
	BaseIds.push( targetItem.id );
	Neighbourhood(BaseIds,queryarray,true);
	drawNodesEdges();
	new_current_id = targetItem.id;
	queryarray=null;
	BaseIds=null;
	myGraph.changed();
	springgraph.addEventListener(FlexEvent.UPDATE_COMPLETE,listenFn);
	//				changeGraph(timeLimit);
	//				modifyTime(0);
	/*  				simTimer2.reset();
	simTimer2.start();  */
	saveprevious();
	historycount = historycount + 1;
	showcount = showcount + 1;
	backbutton.visible = true;
	return;
}





// executed when stop displaying is selected on the right click option of the nodes
public function deletenodes(targetItem :Item):void{
	
	// flush the history and start from first
	if(showcount != historycount){
		clearhistory();
		nodesstatus[statuscount] = "delete";
		statuscount = statuscount + 1;
		for each(var nodedata:XML in NodesArray){
			tempnodeshistory.push(nodedata.@id);
		}
		for each(var edge:XML in LinksArray){
			tempedgeshistory.push(edge);
		}
		saveprevious();
		historycount = historycount + 1;
		showcount = showcount + 1;
		backbutton.visible = true;
	}
	
	else if (historycount != 0) {
		nodesstatus[statuscount] = "delete";
		statuscount = statuscount + 1;
	}
	else {
		saveprevious();
		historycount = historycount + 1;
		showcount = showcount + 1;
		backbutton.visible = true;
	}
	tempnodeshistory = new Array();
	tempedgeshistory = new Array();
	
	// this is the part where the neighbours of the selected node is found and deleted if they have no other neighbours
	
	var myNeighbours:Array = myGraph.neighbour_Ids ( targetItem.id ) ;
	// close neighbours.
	var node_deletion_markers:Array=new Array();
	var only_edge_deletion_markers:Array=new Array();
	// We hide the showbutton on each of the current items neigbour
	for each ( var friend:String in myNeighbours ){
		var targetVbox:FlexItem ;
		targetVbox=FlexItem(Node_Id_Uid[friend]);
		if ( targetVbox != null ) {
			targetVbox.showbutton = false;
		}
		
		// If the neigbour of a current item has only one neighbour then this node has to be deleted
		var second_friend:Array = myGraph.neighbour_Ids ( friend );
		if ( second_friend.length == 1 ) {
			node_deletion_markers.push( friend ); //because 'friend' has only one edge.
			var item:Item =fullGraph.find(friend);
			tempnodeshistory.push(item.id);
		}
		
	}
	
	
	
	
	tempnodeshistory.push(targetItem.id);
	
	
	
	//a node is marked for deletion,only if it has no other links.
	for each ( var node:String in node_deletion_markers ){
		Delete_Node( node ) ;
	}
	
	Delete_Node(targetItem.id);
	saveprevious();
	if(historycount == 1) {
		nodesstatus[statuscount] = "delete";
		statuscount = statuscount + 1;
	}
	historycount = historycount + 1;
	showcount = showcount + 1;
	backbutton.visible = true;
	
}



public function clearhistory():void{
	backbutton.visible = false;
	forwardbutton.visible = false;
	nodeshistory = new Array();
	edgeshistory = new Array();
	nodesstatus  = new Array();
	historycount = 0;
	showcount    = 0;
	statuscount  = 0;
	tempnodeshistory = new Array();
	tempedgeshistory = new Array();
	//   Node_Id_Uid=new Array();
	//	NodesArray=new Array();
	//	parents=new Array();
}



public function addshortProfile(supercomp:UIComponent):void{
	PopUpManager.addPopUp(supercomp, this, false);	
	PopUpManager.centerPopUp(supercomp);
}












/**	VARIABLES AND FUNCTIONS FOR MAKING THE NODES SMALLER	**/	



private var tcanvas_height:Number = 60 ;
private var tcanvas_width:Number = 124;		
private var timage_height:Number = 36 ;	
private var timage_width:Number = 35 ;
private var timage_y:Number = 0 ;		
private var tbut_height:Number = 8 ;		
private var tbut_width:Number = 8 ;
private var tbut_y:Number = 4 ;
private var ttext_height:Number =19 ;
private var ttext_width:Number = 116;
private var ttext_y:Number = 36 ;
private var tfontSize:Number = 10 ;
private var tzoom_width_to:Number = 1 ;
private var tzoom_height_to:Number = 1 ;



//variables for flexItem binding, to change the size of nodes.
[Bindable]
public var canvas_height:Number = 50 ;
[Bindable]
public var canvas_width:Number;
[Bindable]
public var image_height:Number = 36 ;
[Bindable]
public var image_width:Number = 35 ;
/* [Bindable]
public var image_x:Number = 43.5 ; */
[Bindable]
public var image_y:Number = 0 ;

[Bindable]
public var but_height:Number = 8 ;
[Bindable]
public var but_width:Number = 8 ;
/* 	[Bindable]
public var but_x:Number = 68 ; */
[Bindable]
public var but_y:Number = 2 ;

[Bindable]
public var text_height:Number =19 ;
[Bindable]
public var text_width:Number ;
/* [Bindable]
public var text_x:Number = 3 ; */
[Bindable]
public var text_y:Number = 36 ;
[Bindable]
public var fontSize:Number = 10 ;

[Bindable]
public var zoom_width_to:Number = 1 ;
[Bindable]
public var zoom_height_to:Number = 1 ;


private function ChangeNodeSize():void{
	if ( nodeSizeSlider.value == 1 )
	chkZoom.visible = false;
	else
	chkZoom.visible = true;	
	
	myGraph.changed();
	
	canvas_height = tcanvas_height * nodeSizeSlider.value ;
	canvas_width = tcanvas_width * nodeSizeSlider.value;
	
	image_height = timage_height * nodeSizeSlider.value ;
	image_width = timage_width * nodeSizeSlider.value ;
	image_y = timage_y * nodeSizeSlider.value ;
	
	but_height = tbut_height * nodeSizeSlider.value ;
	but_width = tbut_width * nodeSizeSlider.value ;
	but_y = tbut_y * nodeSizeSlider.value ;
	
	text_height = ttext_height * nodeSizeSlider.value ;
	text_width = ttext_width * nodeSizeSlider.value;
	text_y = ttext_y * nodeSizeSlider.value ;
	fontSize = tfontSize * nodeSizeSlider.value ;
	
	if ( chkZoom.selected == true ){
		zoom_height_to 	=  tzoom_height_to / nodeSizeSlider.value;
		zoom_width_to 	=  tzoom_width_to / nodeSizeSlider.value;
	}
	myGraph.changed();
}

private function ChangeZoomSetting():void{
	if ( chkZoom.selected == true ){
		zoom_height_to 	=  tzoom_height_to / nodeSizeSlider.value;
		zoom_width_to 	=  tzoom_width_to / nodeSizeSlider.value;
		chkZoom.toolTip=clickTSZOfM;
	}
	else {
		zoom_height_to = 1;
		zoom_width_to = 1;
		chkZoom.toolTip=clickTSZOnM;
	}
	myGraph.changed();
}









/*  */



// When the user clicks on a particular edgetype from the combo box on the control panel
/*
private function getedgetype(e:Event):void{
	if (ComboBox(e.target).selectedIndex.toString() != "-1")
	drawedgetypes( showparticularedgetype.selectedLabel );
}

*/



// when a particular relation is selected from the combo box in the control panel
// We first store the current network before deleting it , so that it can be shown in the back button
// After storing them in the history we delete the current network and draw all the nodes and edges corresponding
// to the edgename selected. Depending on the edgename we also set the rigidity
// Input -- edgename

public function drawedgetypes(edgename:String):void{
	
	
	springgraph.forceDirectedLayout.newRigidity = 0.05;
	// if there is no history stored we store the previous network .
	
	if (historycount == 0)
	{ 
		saveprevious();
		historycount = historycount + 1;
		showcount    = showcount + 1;
		backbutton.visible = true;
	}
	// if showcount and history count do not match start history from the beginning
	if(showcount != historycount){
		clearhistory();
	}     
	else
	{
		tempnodeshistory = new Array();
		tempedgeshistory = new Array();
	}
	
	
	// we store the status as focusin because for this status we redraw the graph with nodes and edges from nodeshistory and edgeshistory respectively
	nodesstatus[statuscount] = "focusin";
	statuscount = statuscount + 1;
	for each(node in NodesArray){
		//  var item:Item=fullGraph.find(node.@id);
		tempnodeshistory.push(node.@id);
	}
	for each(edge in LinksArray){
		tempedgeshistory.push(edge);
	}
	saveprevious();
	historycount = historycount + 1;
	showcount    = showcount + 1;
	backbutton.visible = true;
	
	
	// we delete the network that was present previously 
	// first delete all the UI Components
	for each (var edge:XML in LinksArray) {
		var disp:UIComponent;
		disp = UIComponent(myGraph.edgeRef[edge.@id]);
		if(disp !=null){
			disp.graphics.clear();
			if(springgraph.drawingSurface.contains(disp)==true){
				springgraph.drawingSurface.removeChild(disp);
			}
		}	
	}
	Node_Id_Uid=new Array();
	NodesArray=new Array();
	LinksArray=new Array();
	myGraph.empty();  			
	parents=new Array();
	springgraph.empty();
	
	// we go through the fullGraph which contains all the nodes and edges and find the edges 
	//corresponding to the selected edgename and add the nodes to the Graph and  NodesArray if not aldready present in the myGraph
	// and then draw the links and add them to LinksArray
	
	for each(var node1:Object in fullGraph._edges){
		for each(var node2:Object in node1){
			for each(var linkdata:Object in node2.link) {
				if(  myGraph.edgeRef[linkdata.id] == null && linkdata.name == edgename ){
					if ( myGraph.getData(linkdata.SourceID,linkdata.id) == null ) {
						
						var item1:Item=fullGraph.find(linkdata.SourceID);
						if (myGraph.find(item1.id) ==null)
						myGraph.add(item1);
						NodesArray.push(item1.data)
						var item2:Item=fullGraph.find(linkdata.DestID);
						if (myGraph.find(item2.id) ==null)
						myGraph.add(item2);
						NodesArray.push(item2.data);
						myGraph.link(item1,item2,linkdata);							
					//	var NewEdgeXml:String=new String('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity + '"arrow="'+linkdata.arrow +'" edgecolor="' + linkdata.edgecolor + '"creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') + '"/>'); 
						var NewEdgeXml:String=new String('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" tooltip ="' + linkdata.tooltip + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity + '"arrow="'+linkdata.arrow +'" edgecolor="' + linkdata.edgecolor + '"creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') + '"/>');
						var tlinkxml:XML=new XML(NewEdgeXml);
						LinksArray.push(tlinkxml);							
					}
				}
				
			}
		}
	} 
	
	// after all the nodes and edges have been added we store the nodes and edges 
	//in the nodeshistory and edgeshistory and save the status to be focusout normally and focusoutcoffee for coffeegroups
	
	tempnodeshistory = new Array();
	tempedgeshistory = new Array();
	for each(var node:XML in NodesArray){
		//  item = fullGraph.find(node.@id);
		tempnodeshistory.push(node.@id);
	}
	
	for each( edge in LinksArray){
		tempedgeshistory.push(edge);
	}
	
	saveprevious();
	
	nodesstatus[statuscount] = "focusoutedge";
	
	statuscount = statuscount + 1;
	historycount = historycount + 1;
	showcount    = showcount + 1;
	backbutton.visible = true;
	springgraph.dataProvider = myGraph;
	NewEdgeXml=null;
	tlinkxml=null;
}



private function checkshowbutton():void{
	// for all the nodes that are fully expanded show the red button
	for each(var item:Item in myGraph.nodes){			
		var myGraphneighbours:Array ;
		myGraphneighbours = new Array(myGraph.neighbour_Ids(item.id));
		var fullGraphneighbours:Array ;
		fullGraphneighbours = new Array(fullGraph.neighbour_Ids(item.id));
		if ( myGraphneighbours[0].length == fullGraphneighbours[0].length){
			var targetVbox:FlexItem ;
			targetVbox=FlexItem(Node_Id_Uid[item.id.toString()]);
			if ( targetVbox != null ) {
				targetVbox.showbutton = true;
			}
		}
		else{
			targetVbox=FlexItem(Node_Id_Uid[item.id.toString()]);
			if ( targetVbox != null ) {
				targetVbox.showbutton = false;
			}
		}
	}
}



public var select_centre:Boolean	=	false;
public var centre_id:String		=	new String();


private function showSpringgraph():void{
	if ( select_centre == false )
	return;
	
	clearhistory();
	Node_Id_Uid		=	new Array();
	NodesArray		=	new Array();
	parents			=	new Array();
	select_centre	=	false;
	
	/* 	for each ( var data:XML in videos_list ){
	if ( data.@name.toString()	==	centre_name )
	centre_id	=	data.@id.toString();
	} */
	
	new_current_id	=	centre_id;
	
	Neighbourhood([centre_id],["All@"],true);	   		
	drawNodesEdges();
	
	currentItem_SetTimer.addEventListener(TimerEvent.TIMER_COMPLETE, setCurrentItem);
	currentItem_SetTimer.reset();
	currentItem_SetTimer.start();	
	return;
}
// select relationships popup
public var SelrelWindowOpen:Boolean = false;
public var selrelwin:box_selrelwindow;
public function ShowSelrelWindow():void{
	if ( SelrelWindowOpen == true )
	return;
	selrelwin	=	box_selrelwindow(PopUpManager.createPopUp(this,box_selrelwindow,false));
	selrelwin.x	=	20;
	selrelwin.y	=	180;
	SelrelWindowOpen = true;
} 


//for drawing multiple edges at once
public function drawmultedgetypes(edgenames:Array):void{
	
	
	springgraph.forceDirectedLayout.newRigidity = 0.05;
	// if there is no history stored we store the previous network .
	
	if (historycount == 0)
	{ 
		saveprevious();
		historycount = historycount + 1;
		showcount    = showcount + 1;
		backbutton.visible = true;
	}
	// if showcount and history count do not match start history from the beginning
	if(showcount != historycount){
		clearhistory();
	}     
	else
	{
		tempnodeshistory = new Array();
		tempedgeshistory = new Array();
	}
	
	
	// we store the status as focusin because for this status we redraw the graph with nodes and edges from nodeshistory and edgeshistory respectively
	nodesstatus[statuscount] = "focusin";
	statuscount = statuscount + 1;
	for each(node in NodesArray){
		//  var item:Item=fullGraph.find(node.@id);
		tempnodeshistory.push(node.@id);
	}
	for each(edge in LinksArray){
		tempedgeshistory.push(edge);
	}
	saveprevious();
	historycount = historycount + 1;
	showcount    = showcount + 1;
	backbutton.visible = true;
	
	
	// we delete the network that was present previously 
	// first delete all the UI Components
	for each (var edge:XML in LinksArray) {
		var disp:UIComponent;
		disp = UIComponent(myGraph.edgeRef[edge.@id]);
		if(disp !=null){
			disp.graphics.clear();
			if(springgraph.drawingSurface.contains(disp)==true){
				springgraph.drawingSurface.removeChild(disp);
			}
		}	
	}
	Node_Id_Uid=new Array();
	NodesArray=new Array();
	LinksArray=new Array();
	myGraph.empty();  			
	parents=new Array();
	springgraph.empty();
	
	// we go through the fullGraph which contains all the nodes and edges and find the edges 
	//corresponding to the selected edgename and add the nodes to the Graph and  NodesArray if not aldready present in the myGraph
	// and then draw the links and add them to LinksArray
	
	for each(var node1:Object in fullGraph._edges){
		for each(var node2:Object in node1){
			for each(var linkdata:Object in node2.link) {
				if(  myGraph.edgeRef[linkdata.id] == null && edgenames.indexOf(linkdata.name) != -1){
					if ( myGraph.getData(linkdata.SourceID,linkdata.id) == null ) {
						
						var item1:Item=fullGraph.find(linkdata.SourceID);
						if (myGraph.find(item1.id) ==null)
						myGraph.add(item1);
						NodesArray.push(item1.data)
						var item2:Item=fullGraph.find(linkdata.DestID);
						if (myGraph.find(item2.id) ==null)
						myGraph.add(item2);
						NodesArray.push(item2.data);
						myGraph.link(item1,item2,linkdata);							
					//	var NewEdgeXml:String=new String('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity + '"arrow="'+linkdata.arrow +'" edgecolor="' + linkdata.edgecolor + '"creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') + '"/>'); 
						var NewEdgeXml:String=new String('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" tooltip ="' + linkdata.tooltip + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity + '"arrow="'+linkdata.arrow +'" edgecolor="' + linkdata.edgecolor + '"creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') + '"/>');
						var tlinkxml:XML=new XML(NewEdgeXml);
						LinksArray.push(tlinkxml);							
					}
				}
				
			}
		}
	} 
	
	// after all the nodes and edges have been added we store the nodes and edges 
	//in the nodeshistory and edgeshistory and save the status to be focusout normally and focusoutcoffee for coffeegroups
	
	tempnodeshistory = new Array();
	tempedgeshistory = new Array();
	for each(var node:XML in NodesArray){
		//  item = fullGraph.find(node.@id);
		tempnodeshistory.push(node.@id);
	}
	
	for each( edge in LinksArray){
		tempedgeshistory.push(edge);
	}
	
	saveprevious();
	
	nodesstatus[statuscount] = "focusoutedge";
	
	statuscount = statuscount + 1;
	historycount = historycount + 1;
	showcount    = showcount + 1;
	backbutton.visible = true;
	springgraph.dataProvider = myGraph;
	NewEdgeXml=null;
	tlinkxml=null;
}
// for adding more edges and ralationships after connecting to all the members of a group
public function addmorecta():void{
		
		//append new nodes to myGraph
		for each(var node:Object in labnodesarraycta)
		{
			for each(var linkdata:Object in fullGraph.neighbors(node.@id.toString())) {
				for each(var link:Object in linkdata.link)
				{
			if(link.name=="Knows")
								{
				if(  myGraph.edgeRef[link.id] == null ){
					//if ( myGraph.getData(link.SourceID,link.id) == null ) {
						var item1:Item=myGraph.find(link.SourceID);
						var item2:Item=myGraph.find(link.DestID);
					
						// if the nodes exist then only add the edge	
						
							if(item1 == null && link.DestID==Globalconnecttoallmembername) {
								item1=fullGraph.find(link.SourceID);
								myGraph.add(item1);
								NodesArray.push(item1.data);
								/*
								myGraph.link(item1,item2,link);							
								var NewEdgeXml:String=new String('<Edge id="' + link.id + '" name ="' + link.name + '" fromID="' + link.SourceID + '" toID="' + link.DestID + '" intensity="' + link.intensity + '" edgecolor="' + link.edgecolor + '"creationtime="' + (link.creationtime.toString()).replace(myPattern,'/') +'"/>'); 
								var tlinkxml:XML=new XML(NewEdgeXml);
								LinksArray.push(tlinkxml);
								*/
							}
							else
							 if(item2==null && link.SourceID==Globalconnecttoallmembername)
							{
								item2=fullGraph.find(link.DestID);
								myGraph.add(item2);
								NodesArray.push(item2.data);
								/*
								myGraph.link(item1,item2,link);							
								var NewEdgeXml:String=new String('<Edge id="' + link.id + '" name ="' + link.name + '" fromID="' + link.SourceID + '" toID="' + link.DestID + '" intensity="' + link.intensity + '" edgecolor="' + link.edgecolor + '"creationtime="' + (link.creationtime.toString()).replace(myPattern,'/') +'"/>'); 
								var tlinkxml:XML=new XML(NewEdgeXml);
								LinksArray.push(tlinkxml);
								*/
							}
												
					//}
				}
				
			}
		}
		}
		}
		//now display the has rated,has commented and has discussed relationships
		displayAllctaRelationShips();
		myGraph.changed();
		springgraph.dataProvider=myGraph;
		springgraph.edges_drawn = true;
		//timer_edgelistener.addEventListener(TimerEvent.TIMER_COMPLETE, ConfigurelabListeners);
		//timer_edgelistener.reset();
		//timer_edgelistener.start();
		
		// to remove the existing links using links array
		/*
		for each ( edge in LinksArray) {
			var disp:UIComponent;
			disp = UIComponent(myGraph.edgeRef[edge.@id]);
			if(disp != null){
				disp.graphics.clear();
				if(springgraph.drawingSurface.contains(disp)==true){
					springgraph.drawingSurface.removeChild(disp);
				}
			}
		}
		*/
		/*
	Node_Id_Uid=new Array();
	NodesArray=new Array();
	LinksArray=new Array();
	myGraph.empty();  			
	parents=new Array();
	springgraph.empty();
	
	//formation of new myGraph to be displayed in springgraph
	for each(var item:Item in labGraph.nodes){
		if (myGraph.find(item.id) ==null)
		myGraph.add(item);
	}
	for each(var node1:Object in labGraph._edges){
		for each(var node2:Object in node1){
			for each(var linkdata:Object in node2.link) {
				if(  myGraph.edgeRef[linkdata.id] == null){
					if ( myGraph.getData(linkdata.SourceID,linkdata.id) == null ) {
						
						var item1:Item=labGraph.find(linkdata.SourceID);
						if (myGraph.find(item1.id) ==null)
						myGraph.add(item1);
						NodesArray.push(item1.data)
						var item2:Item=labGraph.find(linkdata.DestID);
						if (myGraph.find(item2.id) ==null)
						myGraph.add(item2);
						NodesArray.push(item2.data);
						myGraph.link(item1,item2,linkdata);							
						var NewEdgeXml:String=new String('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity + '"arrow="'+linkdata.arrow +'" edgecolor="' + linkdata.edgecolor + '"creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') + '"/>'); 
						var tlinkxml:XML=new XML(NewEdgeXml);
						LinksArray.push(tlinkxml);							
					}
				}
				
			}
		}
	} 
	myGraph.changed();
	springgraph.dataProvider=myGraph;
	*/
	}	
		
	public function displayAllctaRelationShips():void {
	
	//myGraph
	
	for each(var node1:Object in fullGraph._edges){
		var index:Number=1;
		for each(var node2:Object in node1){
			for each(var linkdata:Object in node2.link) {
				//if(  myGraph.edgeRef[linkdata.id] == null ){
				//	if ( myGraph.getData(linkdata.SourceID,linkdata.id) == null ) {
						var item1:Item=myGraph.find(linkdata.SourceID);
						var item2:Item=myGraph.find(linkdata.DestID);
					
						// if the nodes exist then only add the edge	
							if( ((linkdata.SourceID ==Globalconnecttoallmembername && item2 !=null)||(linkdata.DestID ==Globalconnecttoallmembername && item1 != null)) ) {
								if(linkdata.name=="Knows")
								{
								if ( myGraph.getData(linkdata.SourceID,linkdata.id) != null ) 
								{
									myGraph.unlink(linkdata.id,linkdata.SourceID,linkdata.DestID);
								}
								myGraph.link(item1,item2,linkdata);							
							//	var NewEdgeXml:String=new String('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity + '" edgecolor="' + linkdata.edgecolor + '"creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') +'"/>'); 
								var NewEdgeXml:String=new String('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" tooltip="' + linkdata.tooltip + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity + '" edgecolor="' + linkdata.edgecolor + '"creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') +'"/>');
								var tlinkxml:XML=new XML(NewEdgeXml);
								LinksArray.push(tlinkxml);
								}
							}
												
					//}
				//}
				index+=1;
			}
		}
	} 
	NewEdgeXml=null;
	tlinkxml=null;
	return;
	
	
			
	
}

//These are the new function added for the display relationships 

			/**************************************/
			/** 	VARIABLES FOR DISPLAY THING 	 **/
			/**************************************/	
//The functions selrelnodes and neighbourhood2 are modified versions of the focusnode funtion
//They are effective when we have to focus on one or more nodes simultaneously 
// For example if you focus on a node it becomes the centre(the base) and everything revolves around it
// But in case of selrelnodes you can have more than one centre nodes
// Currently it is not used anywhere
public function Selectrelnodes():void{	
  				
				if (historycount == 0) {
					saveprevious();
					historycount = historycount + 1;
					showcount = showcount + 1;
					backbutton.visible = true;
	}
	
	// start history from beginning by deleting the previous entries
				if(showcount != historycount) {
					clearhistory();
					nodesstatus[statuscount] = "disprelation";
					statuscount = statuscount + 1;
		// save the current network
				for each(var node:XML in NodesArray){
				tempnodeshistory.push(node.@id);
				}
				for each( var edge:XML in LinksArray){
					tempedgeshistory.push(edge);
				}
				saveprevious();
				historycount = historycount + 1;
				showcount = showcount + 1;
				backbutton.visible = true;
				}
	
	
	// now we keep a seperate status called focusin because for focus we store the whole myGraph content
				else
				{
				tempnodeshistory = new Array();
				tempedgeshistory = new Array();
				nodesstatus[statuscount] = "disprelation";
				statuscount = statuscount + 1;
				for each(node in NodesArray){
				tempnodeshistory.push(node.@id);
				}
				for each( edge in LinksArray){
					tempedgeshistory.push(edge);
				}
				saveprevious();
				historycount = historycount + 1;
				showcount = showcount + 1;
				backbutton.visible = true;
				}
	
	// call the neighbourbood function with retain as false
	
				var BaseIds:Array=new Array();
			var queryarray:Array=new Array();
			queryarray.push("All@");
			var BaseIds2:Array=new Array();
			for each(var curritemxml:XML in people_list)
			{
				var crtitem:Item = new Item();
  				crtitem = fullGraph.find(curritemxml.@id);
				new_current_id = curritemxml.@id;
				BaseIds.push( crtitem.id );
				BaseIds2.push( curritemxml.@id );
				
				
			}
			for each(var curritemxml2:XML in videos_list)
			{
				var crtitem:Item = new Item();
  				crtitem = fullGraph.find(curritemxml2.@id);
				new_current_id = curritemxml2.@id;
				BaseIds.push( crtitem.id );
				BaseIds2.push( curritemxml2.@id );
				
				
			}
			
			
			Neighbourhood2(BaseIds,queryarray,false,BaseIds2);
			drawNodesEdges();
			//new_current_id = targetItem.id;
			queryarray=null;
			BaseIds=null;
			myGraph.changed();
			springgraph.addEventListener(FlexEvent.UPDATE_COMPLETE,listenFn);
	//		changeGraph(timeLimit);
	//		modifyTime(0);
	/*  			simTimer2.reset();
	simTimer2.start();  */
	tempnodeshistory = new Array();
	tempedgeshistory = new Array();
	for each( node  in NodesArray){
		tempnodeshistory.push(node.@id);
	}
	
	for each( edge  in LinksArray){
		tempedgeshistory.push(edge);
	}
	
	saveprevious();
	nodesstatus[statuscount] = "disprelation";
	statuscount = statuscount + 1;
	historycount = historycount + 1;
	showcount = showcount + 1;
	backbutton.visible = true;
	return;
}

public function Neighbourhood2(BaseIDs:Array,queryarray:Array,retain:Boolean,BaseIDs2:Array):void{
	if(queryarray.length==0)
	return;
	var querydegree:String=new String(queryarray.pop());
	var criteria:Array=querydegree.split(/@/);
	
	var loc_parents:Array=new Array();
	var ExistingNodesID:Array=new Array();
	var ExistingEdgesID:Array=new Array(); 
	
	if(retain){
		for each (var node: XML in NodesArray) {
			ExistingNodesID.push((node.@id).toString());
		}
		for each (var edge: XML in LinksArray) {
			ExistingEdgesID.push( (edge.@id).toString());
		}
	}
	else{
		for each (edge in LinksArray) {
			var disp:UIComponent;
			disp = UIComponent(myGraph.edgeRef[edge.@id]);
			if(disp != null){
				disp.graphics.clear();
				if(springgraph.drawingSurface.contains(disp)==true){
					springgraph.drawingSurface.removeChild(disp);
				}
			}	
		}
		Node_Id_Uid=new Array();
		NodesArray=new Array();
		LinksArray=new Array();
		myGraph.empty();  
		
		
		// Here  
		for each (var currntitem:String  in BaseIDs2)	
		{		
		var local_current_item:Item=new Item(currntitem);
		local_current_item=fullGraph.find(currntitem);
		try{
			myGraph.add( local_current_item );
			
			NodesArray.push(local_current_item.data);	
			ExistingNodesID.push((local_current_item.data.@id).toString());
	   			} catch(Err:Error) {}
	   			parents=new Array();
	}}
	
	
	var NewNodesID:Array=new Array();
	var NewEdgesID:Array=new Array(); 
	tempnodeshistory = new Array();
	tempedgeshistory = new Array();
	
	
	if (nodesstatus[statuscount-1] == "focusin"){
	}
	
	else{
		nodesstatus[statuscount] = "expand";
		statuscount = statuscount + 1;
	}
	
	for each(var curnodeID:String in BaseIDs){
		var myNeighbours:Object;
		myNeighbours=fullGraph.neighbors(curnodeID);
		var NewNodesID_len:Number=NewNodesID.length;
		var NewEdgesID_len:Number=NewEdgesID.length;
		
		for each (var myObj:Object in myNeighbours ) 
		{
			var num_edges:Number=myObj.num_edges;
			var count:int=1;
			var index:Number;
			var index_existing:Number;
			var flag:Boolean = false;
			while(count<=num_edges){
				if ( myObj.link[count].SourceID == 	curnodeID){
					//curnodeId is the source of this link
					if ( validate(myObj.link[count],myObj.link[count].DestID,criteria) ){
						//parents([child,parent,linkid])
						
						//to add node
						index=NewNodesID.indexOf(myObj.link[count].DestID);
						index_existing=ExistingNodesID.indexOf(myObj.link[count].DestID);
						
						/* for each(var obj1:Object in BaseIDs)
		 				{
		 					if(obj1 == myObj.link[count].DestID)
		 					{flag = true;
		 					break;}
		 				}
						if( (index==-1) && (index_existing == -1 ) && (flag == true)) */
						if( (index==-1) && (index_existing == -1 ) )
						{
							
						for each(var str:String in relnarray)
		 				{
		 				if(myObj.link[count].name == str)
						{
						NewNodesID.push(myObj.link[count].DestID);	
						break;
							
		 				}}
						}
						//to add edge 
						index_existing=ExistingEdgesID.indexOf(myObj.link[count].id);
						index=NewEdgesID.indexOf(myObj.link[count].id);
						
						
						/* if( (index==-1) && (index_existing==-1)&& (flag == true) ) */
						if( (index==-1) && (index_existing==-1))
						{
							
						for each(var str1:String in relnarray)
		 				{
		 				if(myObj.link[count].name == str1)
						{
						NewEdgesID.push(myObj.link[count].id);
						loc_parents.push([myObj.link[count].DestID,myObj.link[count].SourceID,myObj.link[count].id]);
							
						break;	
		 				}}
						}
					}	
				flag = false;	
				}
				else {	//curNodeId is the destination of this link
					if ( validate(myObj.link[count],myObj.link[count].SourceID,criteria) ){
						
						//to add node
						index=NewNodesID.indexOf(myObj.link[count].SourceID);
						index_existing=ExistingNodesID.indexOf(myObj.link[count].SourceID);
						/* for each(var obj1:Object in BaseIDs)
		 				{
		 					if(obj1 == myObj.link[count].SourceID)
		 					{
		 					flag = true;
		 					break;
		 					}
		 				} */
						if( (index==-1) && (index_existing == -1 ) ){
							
						for each(var str2:String in relnarray)
		 				{
		 				if(myObj.link[count].name == str2)
						{
						NewNodesID.push(myObj.link[count].SourceID);
						break;	
		 				}}	
						}
						//to add edge
						index_existing=ExistingEdgesID.indexOf(myObj.link[count].id);
						index=NewEdgesID.indexOf(myObj.link[count].id);
						
						if( (index==-1) && (index_existing==-1) ){
							
						for each(var str3:String in relnarray)
		 				{
		 				if(myObj.link[count].name == str3)
		 				{
		 				NewEdgesID.push(myObj.link[count].id);
						loc_parents.push([myObj.link[count].SourceID,myObj.link[count].DestID,myObj.link[count].id]);
						break;	
		 				}}
						}
					}
				}
				count=count+1;
			}	
		}
	}			
	
	
	
	for each ( var node_ID:String in NewNodesID) {
		if ( myGraph.find(node_ID) == null ) {
			var item:Item=fullGraph.find(node_ID);
			if (nodesstatus[statuscount-1] != "focusin")
			{
				tempnodeshistory.push(item.id);
			} 
			NodesArray.push(item.data);
			
		}
	}
	
	
	for each (var edge_ID:String in NewEdgesID) {
		for each( var edge1:Array in loc_parents){
			if (edge1[2]==edge_ID){
				var data1:Object=new Object();
				data1=fullGraph.getData(edge1[0], edge_ID);
				
			//	var NewEdgeXml:String=new String('<Edge id="' + data1.id + '" name ="' + data1.name + '" fromID="' + data1.SourceID + '" toID="' + data1.DestID + '" intensity="' + data1.intensity + '"tooltip = "'+data1.tooltip+'" edgecolor="' + data1.edgecolor + '"creationtime = "'+(data1.creationtime.toString()).replace(myPattern,'/')+'"/>'); 
				var NewEdgeXml:String=new String('<Edge id="' + data1.id + '" name ="' + data1.name  + '" fromID="' + data1.SourceID + '" toID="' + data1.DestID + '" intensity="' + data1.intensity + '" tooltip="'+data1.tooltip+'" edgecolor="' + data1.edgecolor + '" creationtime="'+(data1.creationtime.toString()).replace(myPattern,'/')+'"/>');
				var tlinkxml:XML=new XML(NewEdgeXml);
				if (nodesstatus[statuscount-1] != "focusin")
				{
					tempedgeshistory.push(tlinkxml);
				}
				LinksArray.push(tlinkxml);
				NewEdgeXml=null; 
				data1=null;
			}
		}
	}
	
	for each(var data:Array in loc_parents)
	parents.push(data);
	
	Neighbourhood(NewNodesID,queryarray,true);  	
	return;
}  



// This function is a modified version of drawmultedgetypes this is called when we have to initiallise
//selective relationships.....is called by display relationships
public function drawmultedgetypes2(edgenames:Array):void{
	
	
	springgraph.forceDirectedLayout.newRigidity = 0.05;
	// if there is no history stored we store the previous network .
	
	if (historycount == 0)
	{ 
		saveprevious();
		historycount = historycount + 1;
		showcount    = showcount + 1;
		backbutton.visible = true;
	}
	// if showcount and history count do not match start history from the beginning
	if(showcount != historycount){
		clearhistory();
	}     
	else
	{
		tempnodeshistory = new Array();
		tempedgeshistory = new Array();
	}
	
	
	// we store the status as focusin because for this status we redraw the graph with nodes and edges from nodeshistory and edgeshistory respectively
	nodesstatus[statuscount] = "focusin";
	statuscount = statuscount + 1;
	for each(node in NodesArray){
		//  var item:Item=fullGraph.find(node.@id);
		tempnodeshistory.push(node.@id);
	}
	for each(edge in LinksArray){
		tempedgeshistory.push(edge);
	}
	saveprevious();
	historycount = historycount + 1;
	showcount    = showcount + 1;
	backbutton.visible = true;
	
	
	// we delete the network that was present previously 
	// first delete all the UI Components
	for each (var edge:XML in LinksArray) {
		var disp:UIComponent;
		disp = UIComponent(myGraph.edgeRef[edge.@id]);
		if(disp !=null){
			disp.graphics.clear();
			if(springgraph.drawingSurface.contains(disp)==true){
				springgraph.drawingSurface.removeChild(disp);
			}
		}	
	}
	Node_Id_Uid=new Array();
	NodesArray=new Array();
	LinksArray=new Array();
	myGraph.empty();  			
	parents=new Array();
	springgraph.empty();
	
	// we go through the fullGraph which contains all the nodes and edges and find the edges 
	//corresponding to the selected edgename and add the nodes to the Graph and  NodesArray if not aldready present in the myGraph
	// and then draw the links and add them to LinksArray
			
			//var queryarray:Array=new Array();
			//queryarray.push("All@");
			var BaseIds3:Array=new Array();
			for each(var curritemxml:XML in people_list)
			{
				var crtitem:Item = new Item();
  				crtitem = fullGraph.find(curritemxml.@id);
				new_current_id = curritemxml.@id;
				
				if( curritemxml.@state == "checked")
				{
				var str:String = new String();
				str = (curritemxml.@id).toString();
				BaseIds3.push(str) ;
				
				}
			}
			for each(var curritemxml2:XML in videos_list)
			{
				var crtitem:Item = new Item();
  				crtitem = fullGraph.find(curritemxml2.@id);
				new_current_id = curritemxml2.@id;
				if( curritemxml2.@state == "checked")
				BaseIds3.push((curritemxml2.@id).toString()) ;
				
				
			}
	
	
	for each(var node1:Object in fullGraph._edges){
		for each(var node2:Object in node1){
			for each(var linkdata:Object in node2.link) {
				if(  myGraph.edgeRef[linkdata.id] == null && edgenames.indexOf(linkdata.name) != -1){
					if ( myGraph.getData(linkdata.SourceID,linkdata.id) == null ) {
						if(BaseIds3.indexOf(linkdata.SourceID as String)!= -1 || BaseIds3.indexOf(linkdata.DestID as String)!= -1)
						{
						var item1:Item=fullGraph.find(linkdata.SourceID);
						if (myGraph.find(item1.id) ==null)
						myGraph.add(item1);
						NodesArray.push(item1.data)
						var item2:Item=fullGraph.find(linkdata.DestID);
						if (myGraph.find(item2.id) ==null)
						myGraph.add(item2);
						NodesArray.push(item2.data);
						myGraph.link(item1,item2,linkdata);							
					//	var NewEdgeXml:String=new String('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity + '"arrow="'+linkdata.arrow +'" edgecolor="' + linkdata.edgecolor + '"creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') + '"/>'); 
						var NewEdgeXml:String=new String('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" tooltip ="' + linkdata.tooltip + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity + '"arrow="'+linkdata.arrow +'" edgecolor="' + linkdata.edgecolor + '"creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') + '"/>');
						var tlinkxml:XML=new XML(NewEdgeXml);
						LinksArray.push(tlinkxml);
						}							
					}
				}
				
			}
		}
	} 
	
	// after all the nodes and edges have been added we store the nodes and edges 
	//in the nodeshistory and edgeshistory and save the status to be focusout normally and focusoutcoffee for coffeegroups
	
	tempnodeshistory = new Array();
	tempedgeshistory = new Array();
	for each(var node:XML in NodesArray){
		//  item = fullGraph.find(node.@id);
		tempnodeshistory.push(node.@id);
	}
	
	for each( edge in LinksArray){
		tempedgeshistory.push(edge);
	}
	
	saveprevious();
	
	nodesstatus[statuscount] = "focusoutedge";
	
	statuscount = statuscount + 1;
	historycount = historycount + 1;
	showcount    = showcount + 1;
	backbutton.visible = true;
	springgraph.dataProvider = myGraph;
	NewEdgeXml=null;
	tlinkxml=null;
}
			/**************************************/
			/** 	VARIABLES FOR DISPLAY THING 	 **/
			/**************************************/	


	
	