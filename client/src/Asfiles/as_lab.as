

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
//import com.adobe.flex.extras.controls.springgraph.Graph;
//import com.adobe.flex.extras.controls.springgraph.Item;
//V0.2
import com.adobe.flex.extras.controls.springgraph.Item;

import mx.controls.Alert;
import mx.core.UIComponent;
import mx.rpc.events.FaultEvent;
import mx.rpc.events.ResultEvent;
public var labnodesarray:Array=new Array();
public var lablinksarray:Array=new Array();
	public var labGraph:Graph=new Graph();	
public var repomyGraph:Graph=new Graph();
public var mylabGraph:Graph=new Graph();
[Bindable] public var LabEvalResult:Object = new Object;
[Bindable] public var LabIdeaResult:Object = new Object;	
[Bindable] public var LabUserResult:Object = new Object;
[Bindable] public var LabXML :XML=<Graph> 
    		<loginId></loginId> 
    		<NodeTypes> 
    		<NodeType name = "People" /> 
    		<NodeType name = "Tags" /> 
    		<NodeType name = "Videos" />  
    		</NodeTypes> 
    		<EdgeTypes>
    		<EdgeType name = "Has seen" edgecolor = "0xcc6633" validnodepairs= "People-Videos"/>
    		<EdgeType name = "Has submitted" edgecolor = "0xcccccc" validnodepairs= "People-Videos"/>
    		<EdgeType name = "Has Tags" edgecolor = "0xccff99" validnodepairs= "Videos-Tags"/>
    		<EdgeType name = "Is previous version" edgecolor = "0xcc99" validnodepairs= "Videos-Videos"/>
    		<EdgeType name = "Is Connected To" edgecolor = "0x3366cc" validnodepairs= "Videos-Videos"/>
    		<EdgeType name = "Knows" edgecolor = "0xffcc00" validnodepairs= "People-People"/> 
    		</EdgeTypes> 
    		<Nodes>
    		 <Node id = "Rakesh.lalwani@insead.edu" name = "Rakesh" alias = "Rakesh alias" nodetype= "People" company = "INSEAD,Europe Campus" title = "Mr." nationality = "Indian" picture = "http://labs.calt.insead.edu/prototyping/Tentube/GraphData/media/people/Rakesh.jpg" invitedby = "pkmittal82@gmail.com" url = "" interests = "competence,Violin,Media,Programming,Movies,Networking,jumping" profile = "" emailid= "Rakesh.lalwani@insead.edu" loggedin = "0" location = "Fontainebleau" grandscore = "2300" jointime = "2008-01-15 15:27:57" timesvisited = "831" lastvisit = "2009-03-18 08:14:15" lastaccessed = "2009-03-18 08:18:12" competences="403?70,302?20,204?100,107?60,108?40,109?20,110?60,111?90"/> 
    		 <Node id = "a.b.c@d.e.f" name = "test user" alias = "test user alias" nodetype= "People" company = "insead" title = "testing" nationality = "" picture = "http://labs.calt.insead.edu/prototyping/Tentube/Testing/GraphData/media/people/default_people.jpg" invitedby = "vallappan.arun@gmail.com" url = "" interests = "Education" profile = "" emailid= "a.b.c@d.e.f" loggedin = "0" location = "fontainebleau" grandscore = "0" jointime = "2008-06-19 13:20:12" timesvisited = "1" lastvisit = "2008-06-19 13:20:23" lastaccessed = "2008-06-19 13:20:28" competences="107?20"/> 
    		 <Node id = "a1@yahoo.com" name = "Test1" alias = "Test1" nodetype= "People" company = "Test1" title = "Test1" nationality = "Test" picture = "http://labs.calt.insead.edu/prototyping/Tentube/Testing/GraphData/media/people/default_people.jpg" invitedby = "shrikantsharat.k@gmail.com" url = "" interests = " Edutainement Games,Crisis Management" profile = "" emailid= "a1@yahoo.com" loggedin = "1" location = "Test" grandscore = "0" jointime = "2009-02-16 19:33:26" timesvisited = "2" lastvisit = "2009-02-17 14:51:49" lastaccessed = "2009-02-17 15:01:09" competences="101?40,103?70"/> 
    		 <Node id = "a@gmail.com" name = "Atgentive" alias = "Atgentive alias" nodetype= "People" company = "INSEAD" title = "" nationality = "" picture = "http://labs.calt.insead.edu/prototyping/Tentube/Testing/GraphData/media/people/logo.jpg" invitedby = "" url = "" interests = "userinterests" profile = "Atgentive Test" emailid= "a@gmail.com" loggedin = "0" location = "France" grandscore = "0" jointime = "2008-08-21 14:20:27" timesvisited = "1" lastvisit = "2008-08-21 14:21:01" lastaccessed = "2008-08-21 14:21:12" competences="103?50,102?60"/> 
    		 <Node id = "albert.angehrn@insead.edu" name = "Albert angehrn" alias = "Albert angehrn alias" nodetype= "People" company = "Insead" title = "Director,CALT" nationality = "Swiss" picture = "http://fw-wwwcalt3.insead.edu/prototyping/InnoTubeProject/Testing/GraphData/media/people/oldalbert.jpg" invitedby = "jose.pietri@insead.edu" url = "http://www.calt.insead.edu/?pagename=Albert%20A.%20Angehrn" interests = "Networking,EIS,Simulations,Gaming,AI,Learning" profile = "Professor at INSEAD and Director of the Centre for Advanced Learning Technologies." emailid= "albert.angehrn@insead.edu" loggedin = "1" location = "Fontainebleau,CEDEX,France" grandscore = "3620" jointime = "0000-00-00 00:00:00" timesvisited = "798" lastvisit = "2008-10-10 10:40:18" lastaccessed = "2008-10-10 10:40:42" competences="101?80,102?80,103?80,104?50,105?50,106?30,111?80,201?80,202?80,203?80,204?80,301?80,302?80,303?80,304?80,401?80,402?80,403?80,404?50,405?70,406?50,501?70"/>
    		  <Node id = "gen@gen.com" name = "gen" alias = "gen" nodetype= "People" company = "gen" title = "gen" nationality = "" picture = "http://labs.calt.insead.edu/prototyping/tentube/testing/GraphData/media/people/default_people.jpg" invitedby = "testing@testing.com" url = "" interests = "test8" profile = "" emailid= "gen@gen.com" loggedin = "0" location = "gen" grandscore = "0" jointime = "2009-05-06 11:52:04" timesvisited = "0" lastvisit = "0000-00-00 00:00:00" lastaccessed = "0000-00-00 00:00:00" competences="101?50"/> 
    		  <Node id = "j@j.com" name = "j" alias = "j" nodetype= "People" company = "j" title = "j" nationality = "" picture = "http://labs.calt.insead.edu/prototyping/tentube/testing/GraphData/media/people/default_people.jpg" invitedby = "tt" url = "" interests = "justatest" profile = "" emailid= "j@j.com" loggedin = "0" location = "j" grandscore = "0" jointime = "2009-05-06 12:22:57" timesvisited = "0" lastvisit = "0000-00-00 00:00:00" lastaccessed = "0000-00-00 00:00:00" competences="101?50"/>
			<Node 
			id = "Video437" 
			name = " v2" 
			category = "" 
			nodetype= "Videos" 
			comments = "" 
			tags = "" 
			grandrating = "1" 
			submittedby = "" 
			submitttime = "2009-04-16 15:20:48" 
			picture = "" 
			url = "" 
			authors = "" 
			versionnum = "2" 
			parentvideo="" 
			previousversion = "" 
			nextversion = "" 
			inspiredby = "" 
			timesseen = "0" 
			lastsseen = "0000-00-00 00:00:00" 
			timesrated = "1" 
			timesplayed = "0" 
			source = "" 
			externalLinks = "" 
			docLinks = "" 
			description = "" privacy = "#EVERYBODY#" />
			<Node 
			id = "Video438" 
			name = " v2" 
			category = "" 
			nodetype= "Videos" 
			comments = "" 
			tags = "" 
			grandrating = "1" 
			submittedby = "" 
			submitttime = "2009-04-16 15:23:32" 
			picture = "" 
			url = "" 
			authors = "" 
			versionnum = "2" 
			parentvideo="" 
			previousversion = "" 
			nextversion = "" 
			inspiredby = "" 
			timesseen = "0" 
			lastsseen = "0000-00-00 00:00:00" 
			timesrated = "1" 
			timesplayed = "0" 
			source = "" 
			externalLinks = "" 
			docLinks = "" 
			description = "" privacy = "#EVERYBODY#" /> 
			</Nodes>
			
			<Edges> 
			<Edge id = "28676" name = "Knows"  tooltip= "knows" fromID = "Rakesh.lalwani@insead.edu"  toID = "a.b.c@d.e.f"  intensity = "5"  edgecolor = "0xffcc00" creationtime = "2007-11-14 00:00:00" /> 
			<Edge id = "28678" name = "Has seen"  tooltip= "has seen" fromID = "albert.angehrn@insead.edu"  toID = "Video437"  intensity = "4"  edgecolor = "0xffcc00" creationtime = "2007-09-10 00:00:00" />
			</Edges>
			</Graph>

;
/*

			*/
[Bindable] public var LabResultidea:Object = new Object;	
[Bindable] public var LabXMLidea :XML;
 private var prevItem: Item;
 private var itemCount: int = 0;


public function labEvalFault(f:FaultEvent):void{
		trace("Labronova Fault"+ f.message);
		Alert.show("EvalInfoService Failed");
	}
			
public function labIdeaFault(f:FaultEvent):void{
		trace("Labronova Fault"+ f.message);
		Alert.show("IdeaInfoService Failed");
	}
	
public function labUserFault(f:FaultEvent):void{
		trace("Labronova Fault"+ f.message);
		Alert.show("UserInfoService Failed");
	}
// call functions depending on show or hide
public function evalinfo():void{
	if(evalinfoButton.label=="Show Evaluation Info")
	{
		showinfo();
		evalinfoButton.label="Hide Evaluation Info";
	}
	else					
	{
		{
		hideinfo();
		evalinfoButton.label="Show Evaluation Info";
	}
	}
					
					return;
}
// for labronova repository data.. change the label to hide or show
/*
public function repoinfo():void{
	if(repoButton.label=="Show Repository Info")
	{
		repomyGraph=myGraph;
		showrepoinfo();
		repoButton.label="Hide Repository Info";
	}
	else					
	{
		{
		hiderepoinfo();
		repoButton.label="Show Repository Info";
	}
	}
					
					return;
}
*/

public function hiderepoinfo():void{
					//loadnetwork.url			= ServerPath + graphData + "loadnetwork_ssk.php";	
					//loadnetwork.send();;
					//lablinksarray=LinksArray;
					removelabedges();
					/*
					for each(var node:Object in LinksArray)
					{
						if(node.@name=="Has Rated" || node.@name=="Has Discussed" || node.@name=="Has Commented")
						{
						Delete_SingleEdge(node.@id.toString(),false);
						}
					}
				*/	

fullGraph=Graph.fromXML(Innoxml , ["Node","Edge","fromID","toID"]);
		// to remove the existing links using links array
		
		for each (var edge:XML in LinksArray) {
			var disp:UIComponent;
			disp = UIComponent(myGraph.edgeRef[edge.@id]);
			if(disp != null){
				disp.graphics.clear();
				if(springgraph.drawingSurface.contains(disp)==true){
					springgraph.drawingSurface.removeChild(disp);
				}
			}
		}
	detailedprofileId=Univ_LoginId;
	viewNet();
	/*
	Node_Id_Uid=new Array();
	NodesArray=new Array();
	LinksArray=new Array();
	myGraph.empty();  			
	parents=new Array();
	springgraph.empty();
	myGraph=repomyGraph;
	//formation of new myGraph to be displayed in springgraph
	/*
	for each(var item:Item in fullGraph.nodes){
		if (myGraph.find(item.id) !=null)
		myGraph.add(item);
	}
	*/
	/*
	for each(var node1:Object in fullGraph._edges){
		for each(var node2:Object in node1){
			for each(var linkdata:Object in node2.link) {
				if(  myGraph.edgeRef[linkdata.id] != null){
					if ( myGraph.getData(linkdata.SourceID,linkdata.id) != null ) {
						
						var item1:Item=fullGraph.find(linkdata.SourceID);
						if (myGraph.find(item1.id) ==null)
						myGraph.add(item1);
						NodesArray.push(item1.data)
						var item2:Item=fullGraph.find(linkdata.DestID);
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
public function hideinfo():void{
					loadnetwork.url			= ServerPath + graphData + "loadnetwork_ssk.php";	
					loadnetwork.send();;
					//lablinksarray=LinksArray;
					removelabedges();
					/*
					for each(var node:Object in LinksArray)
					{
						if(node.@name=="Has Rated" || node.@name=="Has Discussed" || node.@name=="Has Commented")
						{
						Delete_SingleEdge(node.@id.toString(),false);
						}
					}
				*/	
}
public function removelabedges():void{
					//loadnetwork.url			= ServerPath + graphData + "loadnetwork_ssk.php";	
					//loadnetwork.send();;
					//lablinksarray=LinksArray;
					for each(var node:Object in LinksArray)
					{
							//if(link.name=="Has Commented" || link.name=="Has Discussed" || link.name=="Has Rated" || link.name=="Has given" || link.name=="Has been involved in" || link.name=="Has been assessed in")	{
						if(node.@name=="Has Rated" || node.@name=="Has Discussed" || node.@name=="Has Commented" || node.@name=="Has given" || node.@name=="Has been involved in" || node.@name=="Has been assessed in")
						{
						Delete_SingleEdge(node.@id.toString(),false);
						}
					}
					
}

public function showrepoinfo():void{
	
						// call the learnweb service
						//var LabServer:String;
						//var searchKeyWord :String;
					
						//searchKeyWord = vSearch.text;
					//	LearnWebServer ="http://repository.it.fmi.uni-sofia.bg:8080/";		
					//	LabServer = "http://labs.calt.insead.edu/prototyping/lab/";				
					//	LabServer = "http://localhost/prototyping/lab/";
					//	LabServer = "http://labs.calt.insead.edu/prototyping/InnoTubeProject/GraphData/";
						
						//evalinfoService.url	= 	LabServer+ "evaluation_outcomes.xml";
						evalinfoService.url	= 	laboranovaevalurl;
						evalinfoService.useProxy =	false;
					    evalinfoService.resultFormat =	'e4x';
					    evalinfoService.method	=   'GET';		
						evalinfoService.addEventListener(ResultEvent.RESULT,lab_ProcessResults);
						evalinfoService.send();
						

					
					return;
}
public function lab_ProcessResults(r:ResultEvent):void{
				try{
					//var LearnWebXML :XML;
			   		LabEvalResult	=	evalinfoService.lastResult;
			   		//LabXML = new XML(LabResult);
			   		/*
			   		var i:int =0;
			
				for each(var node:XML in app.LearnWebXML.descendants("resource"))
				{
					
					if(node.canonical.dc.type=="Video")
					{
					i=1;
					}
				}
				if(i==1)
			   			showLearnWeb(app.LabXML);
			   	//	trace(LearnWebXML);
			   	   //Alert.show(LearnWebXML.toXMLString());
			   					   		
			   		*/
			 	
						//var LabServer:String;
						//LabServer = "http://labs.calt.insead.edu/prototyping/lab/";	
						ideainfoService.url	= 	laboranovaideasurl;
						ideainfoService.useProxy =	false;
					    ideainfoService.resultFormat =	'e4x';
					    ideainfoService.method	=   'GET';		
						ideainfoService.addEventListener(ResultEvent.RESULT,labidea_ProcessResults);
						ideainfoService.send();	
			 	}
			 	catch(e:Error){
			 		trace('Errors in Lab Eval Process XML Results','Error !');
			 	}
			 	
			 	return;
		}
		
		public function labidea_ProcessResults(r:ResultEvent):void{
				try{
					//var LearnWebXML :XML;
			   		LabIdeaResult	=	ideainfoService.lastResult;
			   		
			   		//var LabServer:String;
						//LabServer = "http://labs.calt.insead.edu/prototyping/lab/";	
						userinfoService.url	= 	laboranovausersurl;
						userinfoService.useProxy =	false;
					    userinfoService.resultFormat =	'e4x';
					    userinfoService.method	=   'GET';		
						userinfoService.addEventListener(ResultEvent.RESULT,labuser_ProcessResultsnew);
						userinfoService.send();	
			   		
			   		
						
			 	}
			 	catch(e:Error){
			 		trace('Errors in Lab Idea XML Results','Error !');
			 	}
			 	
			 	return;
		}
		
		
		public function labuser_ProcessResultsnew(r:ResultEvent):void{
				try{
					//var LearnWebXML :XML;
					
					var ratingToolTip :String;
					var commentToolTip : String;
					var discussedToolTip : String;
					var descText : String;
					var commentText : String;
					
					var myPattern:RegExp = /"/g ;  

				
			   		LabUserResult	=	userinfoService.lastResult;
			   
			   		var RepoIdeaXML:XML = new XML(LabIdeaResult);
			   		var RepoUserXML:XML = new XML(LabUserResult);
			   		var i:int =0;
			   		var li:Object;
					var InnoxmlRepo:XML=Innoxml;
					
					
				//var node1:XML=LabXML.descendants("idea");
				//var node2:XML=LabXML.descendants("idea");"+ 
						
				var LabXMLstring="<Graph><loginId></loginId><NodeTypes><NodeType name = \"People\" /><NodeType name = \"Evaluation\" /><NodeType name = \"Ideas\" /><NodeType name = \"Videos\" /></NodeTypes><EdgeTypes><EdgeType name = \"Has submitted\" edgecolor = \"0x550055\" validnodepairs= \"People-Ideas\"/><EdgeType name = \"Has been assessed in\" edgecolor = \"0x8B4513\" validnodepairs= \"Ideas-Evaluation\"/><EdgeType name = \"Has commented\" edgecolor = \"0xeeddee\" validnodepairs= \"People-Ideas\"/><EdgeType name = \"Has rated\" edgecolor = \"0xaaffaa\" validnodepairs= \"People-Ideas\"/></EdgeTypes><Nodes>";
    		 	// append the xml in innoxml
    		 	LabXMLstring="<NodeType name = \"Evaluation\" />";
    		 	li=new XML(LabXMLstring);
    		 	InnoxmlRepo.NodeTypes.appendChild(li);
    		 	LabXMLstring="<NodeType name = \"Ideas\" />";
    		 	li=new XML(LabXMLstring);
    		 	InnoxmlRepo.NodeTypes.appendChild(li);
    		 	LabXMLstring="<EdgeType name = \"Has rated\" edgecolor = \"0xaaffaa\" validnodepairs= \"People-Ideas\"/>";
    		 	li=new XML(LabXMLstring);
    		 	InnoxmlRepo.EdgeTypes.appendChild(li);
    		 	LabXMLstring="<EdgeType name = \"Has commented\" edgecolor = \"0xeeddee\" validnodepairs= \"People-Ideas\"/>";
    		 	li=new XML(LabXMLstring);
    		 	InnoxmlRepo.EdgeTypes.appendChild(li);
    		 	LabXMLstring="<EdgeType name = \"Has been assessed in\" edgecolor = \"0x8B4513\" validnodepairs= \"Ideas-Evaluation\"/>";
    		 	li=new XML(LabXMLstring);
    		 	InnoxmlRepo.EdgeTypes.appendChild(li);
    		 	LabXMLstring="<EdgeType name = \"Has given\" edgecolor = \"0x550055\" validnodepairs= \"People-Ideas\"/>";
    		 	li=new XML(LabXMLstring);
    		 	InnoxmlRepo.EdgeTypes.appendChild(li);
    		 	LabXMLstring="<EdgeType name = \"Has been involved in\" edgecolor = \"0xff0000\" validnodepairs= \"People-Evaluation\"/>";
    		 	li=new XML(LabXMLstring);
    		 	InnoxmlRepo.EdgeTypes.appendChild(li);
    		 	var LabXMLstringedges:String="<Edges>";
			//create the user nodes
				for each(var node:XML in RepoUserXML.descendants("user"))
				{
					LabXMLstring ="<Node id = \"User" + node.@id + "\"";
					LabXMLstring =LabXMLstring + " created = \"" + node.@created + "\"";
					LabXMLstring =LabXMLstring + " islab = \"true\"";
					LabXMLstring =LabXMLstring + " name = \"" + node.@first_name + "\"";
					LabXMLstring =LabXMLstring + " nodetype= \"People\"";  
    		 		LabXMLstring =LabXMLstring + " picture = \"http://labs.calt.insead.edu/prototyping/Tentube/Testing/GraphData/media/people/default_people.jpg\"";
    		 		LabXMLstring =LabXMLstring + " description = \"\"/>"; 
					li=new XML(LabXMLstring);
					InnoxmlRepo.Nodes.appendChild(li);
				}
			 // create the ideas nodes and the "user has submitted the idea"
				for each(var node:XML in RepoIdeaXML.descendants("idea"))
				{
					LabXMLstring ="<Node id = \"Idea" + node.@id + "\"";
					LabXMLstring =LabXMLstring + " created = \"" + node.@created + "\"";
					LabXMLstring =LabXMLstring + " islab = \"true\"";
					LabXMLstring =LabXMLstring + " name = \"" + node.@title + "\"";
					LabXMLstring =LabXMLstring + " nodetype= \"Ideas\"";  
    		 		LabXMLstring =LabXMLstring + " picture = \"http://labs.calt.insead.edu/prototyping/InnoTubeProject/media/images.jpeg\"";
    		 		
    		 	    descText = node.@description;
    		 	    descText = descText.replace(myPattern,"");
    		 		
    		 		LabXMLstring =LabXMLstring + " description = \""+ descText + "\"/>";
    		 	//	LabXMLstring =LabXMLstring + " description = \"" + "\"/>";
    		 		 
					li=new XML(LabXMLstring);
					InnoxmlRepo.Nodes.appendChild(li);
					for each(var nodei:XML in node.creators.descendants("creator"))
					{
						//<Edge id = "28676" name = "Knows"  tooltip= "knows" fromID = "Rakesh.lalwani@insead.edu"  toID = "a.b.c@d.e.f"  intensity = "5"  edgecolor = "0xffcc00" creationtime = "2007-11-14 00:00:00" /> 
			
						LabXMLstringedges ="<Edge id = \"Idea" + node.@id + "User" +nodei.@id +"\"";
						LabXMLstringedges =LabXMLstringedges + " islab = \"true\"";
						LabXMLstringedges =LabXMLstringedges + " tooltip = \"has given\"";
						LabXMLstringedges =LabXMLstringedges + " name = \"Has given\"";
						LabXMLstringedges =LabXMLstringedges + " fromID= \"User" + nodei.@id +"\"";  
    					LabXMLstringedges =LabXMLstringedges + " toID = \"Idea" +node.@id + "\"";
    					LabXMLstringedges =LabXMLstringedges + " intensity = \"1\"";
						LabXMLstringedges =LabXMLstringedges + " edgecolor = \"0x550055\"";
						//important field
						LabXMLstringedges =LabXMLstringedges + " creationtime = \"" +node.@created + "\"/>";	
						li=new XML(LabXMLstringedges);
						InnoxmlRepo.Edges.appendChild(li);
					}
					// load has commented
					for each(var nodeii:XML in node.comments.descendants("comment"))
					{
						//<Edge id = "28676" name = "Knows"  tooltip= "knows" fromID = "Rakesh.lalwani@insead.edu"  toID = "a.b.c@d.e.f"  intensity = "5"  edgecolor = "0xffcc00" creationtime = "2007-11-14 00:00:00" /> 
		
						LabXMLstringedges ="<Edge id = \"Idea" + node.@id + "User" +nodeii.@creator_id +"\"";
						LabXMLstringedges =LabXMLstringedges + " islab = \"true\"";
												
						commentText = nodeii.@comment_text;
    		 	    	commentText = commentText.replace(myPattern,"");
    		 									
						commentToolTip = "has commented the idea (comment:" + commentText + " timestamp:"+ nodeii.@created + ")";
						LabXMLstringedges =LabXMLstringedges + " tooltip = \"" + commentToolTip + "\"" ;
						LabXMLstringedges =LabXMLstringedges + " name = \"Has commented\"";
						LabXMLstringedges =LabXMLstringedges + " fromID= \"User" + nodeii.@creator_id +"\"";  
    					LabXMLstringedges =LabXMLstringedges + " toID = \"Idea" +node.@id + "\"";
    					LabXMLstringedges =LabXMLstringedges + " intensity = \"1\"";
						LabXMLstringedges =LabXMLstringedges + " edgecolor = \"0xeeddee\"";
						LabXMLstringedges =LabXMLstringedges + " creationtime = \"" +nodeii.@created + "\"/>";	
						li=new XML(LabXMLstringedges);
						InnoxmlRepo.Edges.appendChild(li);
					}
					
					//load has rated
					for each(var nodeiii:XML in node.ratings.descendants("rating"))
					{
						//<Edge id = "28676" name = "Knows"  tooltip= "knows" fromID = "Rakesh.lalwani@insead.edu"  toID = "a.b.c@d.e.f"  intensity = "5"  edgecolor = "0xffcc00" creationtime = "2007-11-14 00:00:00" /> 
			
						LabXMLstringedges ="<Edge id = \"Idea" + node.@id + "User" +nodeiii.@evaluator_id +"\"";
						LabXMLstringedges =LabXMLstringedges + " islab = \"true\"";
						ratingToolTip = "has rated the idea (rating:" + nodeiii.@rating_value + " timestamp:"+ nodeiii.@created + ")"; 
						LabXMLstringedges =LabXMLstringedges + " tooltip = \"" + ratingToolTip+ "\"" ;
						LabXMLstringedges =LabXMLstringedges + " name = \"Has rated\"";
						LabXMLstringedges =LabXMLstringedges + " fromID= \"User" + nodeiii.@evaluator_id +"\"";  
    					LabXMLstringedges =LabXMLstringedges + " toID = \"Idea" +node.@id + "\"";
    					LabXMLstringedges =LabXMLstringedges + " intensity = \"1\"";
						LabXMLstringedges =LabXMLstringedges + " edgecolor = \"0xaaffaa\"";
						LabXMLstringedges =LabXMLstringedges + " creationtime = \"" +nodeiii.@created + "\"/>";	
						li=new XML(LabXMLstringedges);
						InnoxmlRepo.Edges.appendChild(li);
					}
					
					
					for each(var nodeiiii:XML in node.evaluation_outcomes.descendants("evaluation_outcome"))
					{
						//<Edge id = "28676" name = "Knows"  tooltip= "knows" fromID = "Rakesh.lalwani@insead.edu"  toID = "a.b.c@d.e.f"  intensity = "5"  edgecolor = "0xffcc00" creationtime = "2007-11-14 00:00:00" /> 
			
						LabXMLstringedges ="<Edge id = \"Idea" + node.@id + "Evaluation" +nodeiiii.@id +"\"";
						LabXMLstringedges =LabXMLstringedges + " islab = \"true\"";
						LabXMLstringedges =LabXMLstringedges + " tooltip = \"has been assessed in\"";
						LabXMLstringedges =LabXMLstringedges + " name = \"Has been assessed in\"";
						LabXMLstringedges =LabXMLstringedges + " fromID= \"Idea" + node.@id +"\"";  
    					LabXMLstringedges =LabXMLstringedges + " toID = \"EvalOut" +nodeiiii.@id + "\"";
    					LabXMLstringedges =LabXMLstringedges + " intensity = \"1\"";
						LabXMLstringedges =LabXMLstringedges + " edgecolor = \"08B4513\"";
						LabXMLstringedges =LabXMLstringedges + " creationtime = \"" +node.@created + "\"/>";	
						li=new XML(LabXMLstringedges);
						InnoxmlRepo.Edges.appendChild(li);
					
						LabXMLstring ="<Node id = \"EvalOut" + nodeiiii.@id + "\"";
						LabXMLstring =LabXMLstring + " created = \"" + nodeiiii.@created + "\"";
						LabXMLstring =LabXMLstring + " islab = \"true\"";
						LabXMLstring =LabXMLstring + " name = \"" + nodeiiii.@subcriterion + "\"";
						LabXMLstring =LabXMLstring + " evaltype= \"" + nodeiiii.@evaluation_type + "\"";
						
						LabXMLstring =LabXMLstring + " nodetype= \"Evaluation\"";  
    		 			LabXMLstring =LabXMLstring + " picture = \"http://labs.calt.insead.edu/prototyping/InnoTubeProject/media/eval.jpeg\"";
    		 			LabXMLstring =LabXMLstring + " description = \"\"/>"; 
						li=new XML(LabXMLstring);
						InnoxmlRepo.Edges.appendChild(li);
					
						for each(var nodeiiiii:XML in nodeiiii.participating_users.descendants("participating_user"))
					{
						//<Edge id = "28676" name = "Knows"  tooltip= "knows" fromID = "Rakesh.lalwani@insead.edu"  toID = "a.b.c@d.e.f"  intensity = "5"  edgecolor = "0xffcc00" creationtime = "2007-11-14 00:00:00" /> 
			
						LabXMLstringedges ="<Edge id = \"EvalOut" + nodeiiii.@id + "User" +nodeiiiii.@id +"\"";
						LabXMLstringedges =LabXMLstringedges + " islab = \"true\"";
						LabXMLstringedges =LabXMLstringedges + " tooltip = \"has been involved in\"";
						LabXMLstringedges =LabXMLstringedges + " name = \"Has been involved in\"";
						LabXMLstringedges =LabXMLstringedges + " fromID= \"User" + nodeiiiii.@id +"\"";  
    					LabXMLstringedges =LabXMLstringedges + " toID = \"EvalOut" +nodeiiii.@id + "\"";
    					LabXMLstringedges =LabXMLstringedges + " intensity = \"1\"";
						LabXMLstringedges =LabXMLstringedges + " edgecolor = \"0xff0000\"";
						LabXMLstringedges =LabXMLstringedges + " creationtime = \"" +nodeiiii.@created + "\"/>";	
						li=new XML(LabXMLstringedges);
						InnoxmlRepo.Edges.appendChild(li);
					
					}
					
					}
			/*		for each(var nodeEval:XML in node.ratings.descendants("evaluation_outcome"))
					{
						//<Edge id = "28676" name = "Knows"  tooltip= "knows" fromID = "Rakesh.lalwani@insead.edu"  toID = "a.b.c@d.e.f"  intensity = "5"  edgecolor = "0xffcc00" creationtime = "2007-11-14 00:00:00" /> 
			
						LabXMLstring =LabXMLstring + "<Edge id = \"Idea" + node.@id + "User" +nodeii.@evaluator_id +"\"";
						LabXMLstring =LabXMLstring + " tooltip = \"has rated\"";
						LabXMLstring =LabXMLstring + " name = \"Has rated\"";
						LabXMLstring =LabXMLstring + " fromID= \"User" + nodeiii.@evaluator_id +"\"";  
    					LabXMLstring =LabXMLstring + " toID = \"Idea" +node.@id + "\"";
    					LabXMLstring =LabXMLstring + " intensity = \"1\"";
						LabXMLstring =LabXMLstring + " edgecolor = \"0xaaffaa\"";
						LabXMLstring =LabXMLstring + " creationtime = \"" +nodeii.@created + "\"/>";	
					}
			
			*/
					
					
					
				}
				
				
				
				
				//LabXMLstring =LabXMLstring + "</Nodes>"+LabXMLstringedges+"</Edges></Graph>";
			//LabXML=new XML(LabXMLstring);
			//labGraph=Graph.fromXML(LabXML , ["Node","Edge","fromID","toID"]);	
			fullGraph=Graph.fromXML(InnoxmlRepo , ["Node","Edge","fromID","toID"]);
		// to remove the existing links using links array
		
		for each (var edge:XML in LinksArray) {
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
	parents=new Array();
	springgraph.empty();
	
	var NewEdgeXml:String;
	var tlinkxml:XML;
	
	//formation of new myGraph to be displayed in springgraph
	var item1:Item;
	var item2:Item
	/*
	for each(var item:Item in fullGraph.nodes){
		if (item.data.@islab ==true)
		myGraph.add(item);
	}
	*/	
	for each(var node1:Object in fullGraph._edges){
		for each(var node2:Object in node1){
			for each(var linkdata:Object in node2.link) {
				//item1=fullGraph.find(linkdata.id);
				//if( item1.data.@islab==true){
					if ( myGraph.getData(linkdata.SourceID,linkdata.id) == null ) {
						//item1
						 item1=fullGraph.find(linkdata.SourceID);
						if (item1.data.@islab==true)
						{
						myGraph.add(item1);
						NodesArray.push(item1.data)
						}
						item2 = fullGraph.find(linkdata.DestID);
						if (item2.data.@islab==true)
						{
						myGraph.add(item2);
						NodesArray.push(item2.data);
						}
						if (item1.data.@islab==true ||  item2.data.@islab==true)
						{
						
					     if(linkdata != null){
					     	try{		
						   		myGraph.link(item1,item2,linkdata);							
								NewEdgeXml  = new String('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" tooltip ="' + linkdata.tooltip + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity + '" arrow="'+linkdata.arrow +'" edgecolor="' + linkdata.edgecolor + '" creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') + '"/>');
								tlinkxml = new XML(NewEdgeXml);									
								LinksArray.push(tlinkxml);
					     	}catch(e:Error){
					     		trace('Errors in linking in mygraph','Error !' + e.message );
					     		trace('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" tooltip ="' + linkdata.tooltip + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity + '" arrow="'+linkdata.arrow +'" edgecolor="' + linkdata.edgecolor + '" creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') + '"/>');
			 					
			 				}
					     
						}else{
							
							trace("linkdata is null");
						}
													
					  }
					}
				//}
				
			}
		}
	} 
	myGraph.changed();
	springgraph.dataProvider=myGraph;
				 				 	
						
			 	}
			 	catch(e:Error){
			 		trace('Errors in Lab Users XML Results','Error !' + e.message + " " + e.getStackTrace());
			 	}
			 	
			 	return;
		}
		
		/*
public function showinfo():void{
				try{
					//var LearnWebXML :XML;
			   		//LabResultidea	=	ideainfoService.lastResult;
			   		//LabXMLidea = new XML(LabResultidea);
			   		// from the graph
			   		loadnetwork.url			= ServerPath + graphData + "loadnetwork_lab.php";	
					loadnetwork.send();	
			   		labnodesarray=NodesArray;
			   		lablinksarray=LinksArray;
			   		/*
			   		// to populate nodesarray with the current nodes
		for each( id in nodeshistory[showcount]){
			var item:Item = fullGraph.find(id);
			if (myGraph.find(item.id) ==null){
				//myGraph.add(item);
				NodesArray.push(item.data);
			}
		}
		//tp populate linksarray with the existing links
			
			for each(var edge:XML in edgeshistory[showcount]){
			var linkdata =new Object();
			linkdata.id =edge.@id.toString();
			linkdata.name =edge.@name.toString();
			linkdata.SourceID =edge.@fromID.toString();
			linkdata.DestID =edge.@toID.toString();
			linkdata.edgecolor = edge.@edgecolor.toString();
			linkdata.intensity =edge.@intensity.toString();
			linkdata.arrow =edge.@arrow.toString();
			linkdata.creationtime=(edge.@creationtime.toString()).replace(myPattern,'/');
			var fromItem:Item =myGraph.find(linkdata.SourceID);
			var toItem:Item =myGraph.find(linkdata.DestID);
			//myGraph.link(fromItem, toItem, linkdata);
			LinksArray.push(edge);
		}
			   		*/
			   		//formlabgraph();
			   		//labGraph=Graph.fromXML(LabXML , ["Node","Edge","fromID","toID"]);
					//formgraph();
					/* var item: Item = new Item(new Number(++itemCount).toString());
                	labGraph.add(item);
                	if(prevItem != null)
                	labGraph.link(item, prevItem,null);
                	prevItem = item;
                	*/
        	/*		
			 		}
			 	catch(e:Error){
			 		trace('Errors in Lab XML Results','Error !');
			 	}
			 	
			 	return;
		}
		
		*/
		
	/* 	public function labuser_ProcessResults(r:ResultEvent):void{
				try{
					//var LearnWebXML :XML;
			   		LabUserResult	=	userinfoService.lastResult;
			   
			   		var RepoIdeaXML:XML = new XML(LabIdeaResult);
			   		var RepoUserXML:XML = new XML(LabUserResult);
			   		var i:int =0;
				//var node1:XML=LabXML.descendants("idea");
				//var node2:XML=LabXML.descendants("idea");
				var LabXMLstring="<Graph><loginId></loginId><NodeTypes><NodeType name = \"People\" /><NodeType name = \"Evaluation\" /><NodeType name = \"Ideas\" /><NodeType name = \"Videos\" /></NodeTypes><EdgeTypes><EdgeType name = \"Has given\" edgecolor = \"0x550055\" validnodepairs= \"People-Ideas\"/><EdgeType name = \"Has been assessed in\" edgecolor = \"0x8B4513\" validnodepairs= \"Ideas-Evaluation\"/><EdgeType name = \"Has commented\" edgecolor = \"0xeeddee\" validnodepairs= \"People-Ideas\"/><EdgeType name = \"Has rated\" edgecolor = \"0xaaffaa\" validnodepairs= \"People-Ideas\"/></EdgeTypes><Nodes>";
    		 	var LabXMLstringedges:String="<Edges>";
			//create the user nodes
				for each(var node:XML in RepoUserXML.descendants("user"))
				{
					LabXMLstring =LabXMLstring + "<Node id = \"User" + node.@id + "\"";
					LabXMLstring =LabXMLstring + " created = \"" + node.@created + "\"";
					LabXMLstring =LabXMLstring + " name = \"" + node.@first_name + "\"";
					LabXMLstring =LabXMLstring + " nodetype= \"People\"";  
    		 		LabXMLstring =LabXMLstring + " picture = \"http://labs.calt.insead.edu/prototyping/Tentube/Testing/GraphData/media/people/default_people.jpg\"";
    		 		LabXMLstring =LabXMLstring + " description = \"\"/>"; 
					
				}
			 // create the ideas nodes and the "user has submitted the idea"
				for each(var node:XML in RepoIdeaXML.descendants("idea"))
				{
					LabXMLstring =LabXMLstring + "<Node id = \"Idea" + node.@id + "\"";
					LabXMLstring =LabXMLstring + " created = \"" + node.@created + "\"";
					LabXMLstring =LabXMLstring + " name = \"" + node.@title + "\"";
					LabXMLstring =LabXMLstring + " nodetype= \"Ideas\"";  
    		 		LabXMLstring =LabXMLstring + " picture = \"http://labs.calt.insead.edu/prototyping/InnoTubeProject/media/images.jpeg\"";
    		 		LabXMLstring =LabXMLstring + " description = \""+ node.@description + "\"/>"; 
					
					for each(var nodei:XML in node.creators.descendants("creator"))
					{
						//<Edge id = "28676" name = "Knows"  tooltip= "knows" fromID = "Rakesh.lalwani@insead.edu"  toID = "a.b.c@d.e.f"  intensity = "5"  edgecolor = "0xffcc00" creationtime = "2007-11-14 00:00:00" /> 
			
						LabXMLstringedges =LabXMLstringedges + "<Edge id = \"Idea" + node.@id + "User" +nodei.@id +"\"";
						LabXMLstringedges =LabXMLstringedges + " tooltip = \"has given\"";
						LabXMLstringedges =LabXMLstringedges + " name = \"Has given\"";
						LabXMLstringedges =LabXMLstringedges + " fromID= \"User" + nodei.@id +"\"";  
    					LabXMLstringedges =LabXMLstringedges + " toID = \"Idea" +node.@id + "\"";
    					LabXMLstringedges =LabXMLstringedges + " intensity = \"1\"";
						LabXMLstringedges =LabXMLstringedges + " edgecolor = \"0x550055\"";
						LabXMLstringedges =LabXMLstringedges + " creationtime = \"" +nodei.@created + "\"/>";	
					}
					// load has commented
					for each(var nodeii:XML in node.comments.descendants("comment"))
					{
						//<Edge id = "28676" name = "Knows"  tooltip= "knows" fromID = "Rakesh.lalwani@insead.edu"  toID = "a.b.c@d.e.f"  intensity = "5"  edgecolor = "0xffcc00" creationtime = "2007-11-14 00:00:00" /> 
			
						LabXMLstringedges =LabXMLstringedges + "<Edge id = \"Idea" + node.@id + "User" +nodeii.@creator_id +"\"";
						LabXMLstringedges =LabXMLstringedges + " tooltip = \"has commented\"";
						LabXMLstringedges =LabXMLstringedges + " name = \"Has commented\"";
						LabXMLstringedges =LabXMLstringedges + " fromID= \"User" + nodeii.@creator_id +"\"";  
    					LabXMLstringedges =LabXMLstringedges + " toID = \"Idea" +node.@id + "\"";
    					LabXMLstringedges =LabXMLstringedges + " intensity = \"1\"";
						LabXMLstringedges =LabXMLstringedges + " edgecolor = \"0xeeddee\"";
						LabXMLstringedges =LabXMLstringedges + " creationtime = \"" +nodeii.@created + "\"/>";	
					}
					//load has rated
					for each(var nodeiii:XML in node.ratings.descendants("rating"))
					{
						//<Edge id = "28676" name = "Knows"  tooltip= "knows" fromID = "Rakesh.lalwani@insead.edu"  toID = "a.b.c@d.e.f"  intensity = "5"  edgecolor = "0xffcc00" creationtime = "2007-11-14 00:00:00" /> 
			
						LabXMLstringedges =LabXMLstringedges + "<Edge id = \"Idea" + node.@id + "User" +nodeii.@evaluator_id +"\"";
						LabXMLstringedges =LabXMLstringedges + " tooltip = \"has rated\"";
						LabXMLstringedges =LabXMLstringedges + " name = \"Has rated\"";
						LabXMLstringedges =LabXMLstringedges + " fromID= \"User" + nodeiii.@evaluator_id +"\"";  
    					LabXMLstringedges =LabXMLstringedges + " toID = \"Idea" +node.@id + "\"";
    					LabXMLstringedges =LabXMLstringedges + " intensity = \"1\"";
						LabXMLstringedges =LabXMLstringedges + " edgecolor = \"0xaaffaa\"";
						LabXMLstringedges =LabXMLstringedges + " creationtime = \"" +nodeii.@created + "\"/>";	
					}
					
					for each(var nodeiiii:XML in node.evaluation_outcomes.descendants("evaluation_outcome"))
					{
						//<Edge id = "28676" name = "Knows"  tooltip= "knows" fromID = "Rakesh.lalwani@insead.edu"  toID = "a.b.c@d.e.f"  intensity = "5"  edgecolor = "0xffcc00" creationtime = "2007-11-14 00:00:00" /> 
			
						LabXMLstringedges =LabXMLstringedges + "<Edge id = \"Idea" + node.@id + "Evaluation" +nodeiiii.@id +"\"";
						LabXMLstringedges =LabXMLstringedges + " tooltip = \"has been assessed in\"";
						LabXMLstringedges =LabXMLstringedges + " name = \"Has been assessed in\"";
						LabXMLstringedges =LabXMLstringedges + " fromID= \"Idea" + node.@id +"\"";  
    					LabXMLstringedges =LabXMLstringedges + " toID = \"EvalOut" +nodeiiii.@id + "\"";
    					LabXMLstringedges =LabXMLstringedges + " intensity = \"1\"";
						LabXMLstringedges =LabXMLstringedges + " edgecolor = \"08B4513\"";
						LabXMLstringedges =LabXMLstringedges + " creationtime = \"" +nodeii.@created + "\"/>";	
					
						LabXMLstring =LabXMLstring + "<Node id = \"EvalOut" + node.@id + "\"";
						LabXMLstring =LabXMLstring + " created = \"" + node.@created + "\"";
						LabXMLstring =LabXMLstring + " name = \"" + node.@title + "\"";
						LabXMLstring =LabXMLstring + " nodetype= \"Evaluation\"";  
    		 			LabXMLstring =LabXMLstring + " picture = \"http://labs.calt.insead.edu/prototyping/InnoTubeProject/media/eval.jpeg\"";
    		 			LabXMLstring =LabXMLstring + " description = \"\"/>"; 
					}
					
					
					
				}
				
				
				
				
				LabXMLstring =LabXMLstring + "</Nodes>"+LabXMLstringedges+"</Edges></Graph>";
			LabXML=new XML(LabXMLstring);
			labGraph=Graph.fromXML(LabXML , ["Node","Edge","fromID","toID"]);	
			fullGraph=labGraph;
		// to remove the existing links using links array
		
		for each (var edge:XML in LinksArray) {
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
			 	
						
			 	}
			 	catch(e:Error){
			 		trace('Errors in Lab XML Results','Error !');
			 	}
			 	
			 	return;
		}
		
	 */	
public function showinfo():void{
				try{
					//var LearnWebXML :XML;
			   		//LabResultidea	=	ideainfoService.lastResult;
			   		//LabXMLidea = new XML(LabResultidea);
			   		// from the graph
			   		loadnetwork.url			= ServerPath + graphData + "loadnetwork_lab.php";	
					loadnetwork.send();	
			   		labnodesarray=NodesArray;
			   		lablinksarray=LinksArray;
			   		/*
			   		// to populate nodesarray with the current nodes
		for each( id in nodeshistory[showcount]){
			var item:Item = fullGraph.find(id);
			if (myGraph.find(item.id) ==null){
				//myGraph.add(item);
				NodesArray.push(item.data);
			}
		}
		//tp populate linksarray with the existing links
			
			for each(var edge:XML in edgeshistory[showcount]){
			var linkdata =new Object();
			linkdata.id =edge.@id.toString();
			linkdata.name =edge.@name.toString();
			linkdata.SourceID =edge.@fromID.toString();
			linkdata.DestID =edge.@toID.toString();
			linkdata.edgecolor = edge.@edgecolor.toString();
			linkdata.intensity =edge.@intensity.toString();
			linkdata.arrow =edge.@arrow.toString();
			linkdata.creationtime=(edge.@creationtime.toString()).replace(myPattern,'/');
			var fromItem:Item =myGraph.find(linkdata.SourceID);
			var toItem:Item =myGraph.find(linkdata.DestID);
			//myGraph.link(fromItem, toItem, linkdata);
			LinksArray.push(edge);
		}
			   		*/
			   		//formlabgraph();
			   		//labGraph=Graph.fromXML(LabXML , ["Node","Edge","fromID","toID"]);
					//formgraph();
					/* var item: Item = new Item(new Number(++itemCount).toString());
                	labGraph.add(item);
                	if(prevItem != null)
                	labGraph.link(item, prevItem,null);
                	prevItem = item;
                	*/
        			
			 		}
			 	catch(e:Error){
			 		trace('Errors in Lab ShowInfo Results','Error !');
			 	}
			 	
			 	return;
		}
	
	public function formlabgraph():void{
		
		//append new nodes to myGraph
		for each(var node:Object in labnodesarray)
		{
			for each(var linkdata:Object in fullGraph.neighbors(node.@id.toString())) {
				for each(var link:Object in linkdata.link)
				{
	//		if(link.name=="Has Commented" || link.name=="Has Discussed" || link.name=="Has Rated")	{
	if(link.name=="Has Commented" || link.name=="Has Discussed" || link.name=="Has Rated" || link.name=="Has given" || link.name=="Has been involved in" || link.name=="Has been assessed in")	{
		
	
				if(  myGraph.edgeRef[link.id] == null ){
					//if ( myGraph.getData(link.SourceID,link.id) == null ) {
						var item1:Item=myGraph.find(link.SourceID);
						var item2:Item=myGraph.find(link.DestID);
					
						// if the nodes exist then only add the edge	
						
							if(item1 == null) {
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
							 if(item2==null)
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
				
			} // if edge loop
		}
		}
		}
		//now display the has rated,has commented and has discussed relationships
		displayAlllabRelationShips();
		myGraph.changed();
		springgraph.dataProvider=myGraph;
		springgraph.edges_drawn = true;
		timer_edgelistener.addEventListener(TimerEvent.TIMER_COMPLETE, ConfigurelabListeners);
		timer_edgelistener.reset();
		timer_edgelistener.start();
		
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
		
	public function displayAlllabRelationShips():void {
	
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
	if(linkdata.name=="Has Commented" || linkdata.name=="Has Discussed" || linkdata.name=="Has Rated" || linkdata.name=="Has given" || linkdata.name=="Has been involved in" || linkdata.name=="Has been assessed in")	
								{
								myGraph.link(item1,item2,linkdata);							
							//	var NewEdgeXml:String=new String('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity + '" edgecolor="' + linkdata.edgecolor + '"creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') +'"/>'); 
								var NewEdgeXml:String=new String('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" tooltip ="' + linkdata.tooltip + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity + '" edgecolor="' + linkdata.edgecolor + '"creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') +'"/>');
								var tlinkxml:XML=new XML(NewEdgeXml);
								LinksArray.push(tlinkxml);
								}
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
	
	
		
	public function formgraph():void{
		
		//tp populate linksarray with the existing links
			for each(var edge:XML in edgeshistory[showcount]){
			linkdata =new Object();
			linkdata.id =edge.@id.toString();
			linkdata.name =edge.@name.toString();
			linkdata.SourceID =edge.@fromID.toString();
			linkdata.DestID =edge.@toID.toString();
			linkdata.edgecolor = edge.@edgecolor.toString();
			linkdata.intensity =edge.@intensity.toString();
			linkdata.arrow =edge.@arrow.toString();
			linkdata.creationtime=(edge.@creationtime.toString()).replace(myPattern,'/');
			var fromItem:Item =myGraph.find(linkdata.SourceID);
			var toItem:Item =myGraph.find(linkdata.DestID);
			myGraph.link(fromItem, toItem, linkdata);
			LinksArray.push(edge);
		}
		// to remove the existing links using links array
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
						//var NewEdgeXml:String=new String('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity + '"arrow="'+linkdata.arrow +'" edgecolor="' + linkdata.edgecolor + '"creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') + '"/>'); 
						var NewEdgeXml:String=new String('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" tooltip ="' + linkdata.tooltip + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity + '"arrow="'+linkdata.arrow +'" edgecolor="' + linkdata.edgecolor + '"creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') + '"/>');
						var tlinkxml:XML=new XML(NewEdgeXml);
						LinksArray.push(tlinkxml);							
					}
				}
				
			}
		}
	} 
	myGraph.changed();
	springgraph.dataProvider=myGraph;
	}	
		
		
public function islab():Boolean{
				if(Application.application.parameters.tubeName=="InnoTube")
					return true;
				else
					return false;
		}

//new Functions are added here. these are the functiosn for the newer repo button which we have just created

import mx.rpc.events.ResultEvent;
import mx.rpc.http.mxml.HTTPService;
[Bindable] public var laboranovarepourl:String;
private var reposervice:HTTPService 	= 	new HTTPService();
[Bindable] public var LabRepoResult:Object = new Object;

/*
public function repoinfo2():void{
	if(repoButton2.label== "Repository")
	{
		repomyGraph=myGraph;
		showrepoinfo2();
		repoButton2.label= "Hide Repository";
	}
	else					
	{
		{
		hiderepoinfo();
		repoButton2.label= "Repository";
	}
	}
					
					return;
}

*/

public function showrepoinfo2():void{
	reposervice.url = laboranovarepourl;
	reposervice.useProxy =	false;
	reposervice.resultFormat =	'e4x';
	reposervice.method	=   'GET';		
	reposervice.addEventListener(ResultEvent.RESULT,repouser_ProcessResultsnew);
	reposervice.send();
	return;
}



public function repouser_ProcessResultsnew(r:ResultEvent):void{

		var ratingToolTip :String;
		var commentToolTip : String;
		var discussedToolTip : String;
		var descText : String;
		var commentText : String;
		var myPattern:RegExp = /"/g ;  
							
			   		//LabUserResult	=	userinfoService.lastResult;
			   
		LabRepoResult	=	reposervice.lastResult;
		var RepoLabXML:XML = new XML(LabRepoResult);		
		var repoid:XMLList = XMLList(RepoLabXML.ideas);
		var repous:XMLList =  XMLList(RepoLabXML.users) ;
		var RepoIdeaXML:XML= repoid[0] ;
		var RepoUserXML:XML = repous[0];
		var i:int =0;
		var li:Object;
		var InnoxmlRepo:XML=Innoxml;
					
				var LabXMLstring="<Graph><loginId></loginId><NodeTypes><NodeType name = \"People\" /><NodeType name = \"Evaluation\" /><NodeType name = \"Ideas\" /><NodeType name = \"Videos\" /></NodeTypes><EdgeTypes><EdgeType name = \"Has submitted\" edgecolor = \"0x550055\" validnodepairs= \"People-Ideas\"/><EdgeType name = \"Has been assessed in\" edgecolor = \"0x8B4513\" validnodepairs= \"Ideas-Evaluation\"/><EdgeType name = \"Has commented\" edgecolor = \"0xeeddee\" validnodepairs= \"People-Ideas\"/><EdgeType name = \"Has rated\" edgecolor = \"0xaaffaa\" validnodepairs= \"People-Ideas\"/></EdgeTypes><Nodes>";
    		 	// append the xml in innoxml
    		 	LabXMLstring="<NodeType name = \"Evaluation\" />";
    		 	li=new XML(LabXMLstring);
    		 	InnoxmlRepo.NodeTypes.appendChild(li);
    		 	LabXMLstring="<NodeType name = \"Ideas\" />";
    		 	li=new XML(LabXMLstring);
    		 	InnoxmlRepo.NodeTypes.appendChild(li);
    		 	LabXMLstring="<EdgeType name = \"Has rated\" edgecolor = \"0xaaffaa\" validnodepairs= \"People-Ideas\"/>";
    		 	li=new XML(LabXMLstring);
    		 	InnoxmlRepo.EdgeTypes.appendChild(li);
    		 	LabXMLstring="<EdgeType name = \"Has commented\" edgecolor = \"0xeeddee\" validnodepairs= \"People-Ideas\"/>";
    		 	li=new XML(LabXMLstring);
    		 	InnoxmlRepo.EdgeTypes.appendChild(li);
    		 	LabXMLstring="<EdgeType name = \"Has been assessed in\" edgecolor = \"0x8B4513\" validnodepairs= \"Ideas-Evaluation\"/>";
    		 	li=new XML(LabXMLstring);
    		 	InnoxmlRepo.EdgeTypes.appendChild(li);
    		 	LabXMLstring="<EdgeType name = \"Has given\" edgecolor = \"0x550055\" validnodepairs= \"People-Ideas\"/>";
    		 	li=new XML(LabXMLstring);
    		 	InnoxmlRepo.EdgeTypes.appendChild(li);
    		 	LabXMLstring="<EdgeType name = \"Has been involved in\" edgecolor = \"0xff0000\" validnodepairs= \"People-Evaluation\"/>";
    		 	li=new XML(LabXMLstring);
    		 	InnoxmlRepo.EdgeTypes.appendChild(li);
    		 	var LabXMLstringedges:String="<Edges>";
			//create the user nodes
				for each(var node:XML in RepoUserXML.descendants("user"))
				{
					LabXMLstring ="<Node id = \"User" + node.@id + "\"";
					LabXMLstring =LabXMLstring + " created = \"" + node.@created + "\"";
					LabXMLstring =LabXMLstring + " islab = \"true\"";
					LabXMLstring =LabXMLstring + " name = \"" + node.@first_name + "\"";
					LabXMLstring =LabXMLstring + " nodetype= \"People\"";  
    		 		LabXMLstring =LabXMLstring + " picture = \"http://labs.calt.insead.edu/prototyping/Tentube/Testing/GraphData/media/people/default_people.jpg\"";
    		 		LabXMLstring =LabXMLstring + " description = \"\"/>"; 
					li=new XML(LabXMLstring);
					InnoxmlRepo.Nodes.appendChild(li);
				}
			 // create the ideas nodes and the "user has submitted the idea"
				for each(var node:XML in RepoIdeaXML.descendants("idea"))
				{
					LabXMLstring ="<Node id = \"Idea" + node.@id + "\"";
					LabXMLstring =LabXMLstring + " created = \"" + node.@created + "\"";
					LabXMLstring =LabXMLstring + " islab = \"true\"";
					LabXMLstring =LabXMLstring + " name = \"" + node.@title + "\"";
					LabXMLstring =LabXMLstring + " nodetype= \"Ideas\"";  
    		 		LabXMLstring =LabXMLstring + " picture = \"http://labs.calt.insead.edu/prototyping/InnoTubeProject/media/images.jpeg\"";
    		 		
    		 	    descText = node.@description;
    		 	    descText = descText.replace(myPattern,"");
    		 		
    		 		LabXMLstring =LabXMLstring + " description = \""+ descText + "\"/>";
    		 	//	LabXMLstring =LabXMLstring + " description = \"" + "\"/>";
    		 		 
					li=new XML(LabXMLstring);
					InnoxmlRepo.Nodes.appendChild(li);
					for each(var nodei:XML in node.creators.descendants("creator"))
					{
						//<Edge id = "28676" name = "Knows"  tooltip= "knows" fromID = "Rakesh.lalwani@insead.edu"  toID = "a.b.c@d.e.f"  intensity = "5"  edgecolor = "0xffcc00" creationtime = "2007-11-14 00:00:00" /> 
			
						LabXMLstringedges ="<Edge id = \"Idea" + node.@id + "User" +nodei.@id +"\"";
						LabXMLstringedges =LabXMLstringedges + " islab = \"true\"";
						LabXMLstringedges =LabXMLstringedges + " tooltip = \"has given\"";
						LabXMLstringedges =LabXMLstringedges + " name = \"Has given\"";
						LabXMLstringedges =LabXMLstringedges + " fromID= \"User" + nodei.@id +"\"";  
    					LabXMLstringedges =LabXMLstringedges + " toID = \"Idea" +node.@id + "\"";
    					LabXMLstringedges =LabXMLstringedges + " intensity = \"1\"";
						LabXMLstringedges =LabXMLstringedges + " edgecolor = \"0x550055\"";
						//important field
						LabXMLstringedges =LabXMLstringedges + " creationtime = \"" +node.@created + "\"/>";	
						li=new XML(LabXMLstringedges);
						InnoxmlRepo.Edges.appendChild(li);
					}
					// load has commented
					for each(var nodeii:XML in node.comments.descendants("comment"))
					{
						//<Edge id = "28676" name = "Knows"  tooltip= "knows" fromID = "Rakesh.lalwani@insead.edu"  toID = "a.b.c@d.e.f"  intensity = "5"  edgecolor = "0xffcc00" creationtime = "2007-11-14 00:00:00" /> 
		
						LabXMLstringedges ="<Edge id = \"Idea" + node.@id + "User" +nodeii.@creator_id +"\"";
						LabXMLstringedges =LabXMLstringedges + " islab = \"true\"";
												
						commentText = nodeii.@comment_text;
    		 	    	commentText = commentText.replace(myPattern,"");
    		 									
						commentToolTip = "has commented the idea (comment:" + commentText + " timestamp:"+ nodeii.@created + ")";
						LabXMLstringedges =LabXMLstringedges + " tooltip = \"" + commentToolTip + "\"" ;
						LabXMLstringedges =LabXMLstringedges + " name = \"Has commented\"";
						LabXMLstringedges =LabXMLstringedges + " fromID= \"User" + nodeii.@creator_id +"\"";  
    					LabXMLstringedges =LabXMLstringedges + " toID = \"Idea" +node.@id + "\"";
    					LabXMLstringedges =LabXMLstringedges + " intensity = \"1\"";
						LabXMLstringedges =LabXMLstringedges + " edgecolor = \"0xeeddee\"";
						LabXMLstringedges =LabXMLstringedges + " creationtime = \"" +nodeii.@created + "\"/>";	
						li=new XML(LabXMLstringedges);
						InnoxmlRepo.Edges.appendChild(li);
					}
					
					//load has rated
					for each(var nodeiii:XML in node.ratings.descendants("rating"))
					{
						//<Edge id = "28676" name = "Knows"  tooltip= "knows" fromID = "Rakesh.lalwani@insead.edu"  toID = "a.b.c@d.e.f"  intensity = "5"  edgecolor = "0xffcc00" creationtime = "2007-11-14 00:00:00" /> 
			
						LabXMLstringedges ="<Edge id = \"Idea" + node.@id + "User" +nodeiii.@evaluator_id +"\"";
						LabXMLstringedges =LabXMLstringedges + " islab = \"true\"";
						ratingToolTip = "has rated the idea (rating:" + nodeiii.@rating_value + " timestamp:"+ nodeiii.@created + ")"; 
						LabXMLstringedges =LabXMLstringedges + " tooltip = \"" + ratingToolTip+ "\"" ;
						LabXMLstringedges =LabXMLstringedges + " name = \"Has rated\"";
						LabXMLstringedges =LabXMLstringedges + " fromID= \"User" + nodeiii.@evaluator_id +"\"";  
    					LabXMLstringedges =LabXMLstringedges + " toID = \"Idea" +node.@id + "\"";
    					LabXMLstringedges =LabXMLstringedges + " intensity = \"1\"";
						LabXMLstringedges =LabXMLstringedges + " edgecolor = \"0xaaffaa\"";
						LabXMLstringedges =LabXMLstringedges + " creationtime = \"" +nodeiii.@created + "\"/>";	
						li=new XML(LabXMLstringedges);
						InnoxmlRepo.Edges.appendChild(li);
					}
					
					
					for each(var nodeiiii:XML in node.evaluation_outcomes.descendants("evaluation_outcome"))
					{
						//<Edge id = "28676" name = "Knows"  tooltip= "knows" fromID = "Rakesh.lalwani@insead.edu"  toID = "a.b.c@d.e.f"  intensity = "5"  edgecolor = "0xffcc00" creationtime = "2007-11-14 00:00:00" /> 
			
						LabXMLstringedges ="<Edge id = \"Idea" + node.@id + "Evaluation" +nodeiiii.@id +"\"";
						LabXMLstringedges =LabXMLstringedges + " islab = \"true\"";
						LabXMLstringedges =LabXMLstringedges + " tooltip = \"has been assessed in\"";
						LabXMLstringedges =LabXMLstringedges + " name = \"Has been assessed in\"";
						LabXMLstringedges =LabXMLstringedges + " fromID= \"Idea" + node.@id +"\"";  
    					LabXMLstringedges =LabXMLstringedges + " toID = \"EvalOut" +nodeiiii.@id + "\"";
    					LabXMLstringedges =LabXMLstringedges + " intensity = \"1\"";
						LabXMLstringedges =LabXMLstringedges + " edgecolor = \"08B4513\"";
						LabXMLstringedges =LabXMLstringedges + " creationtime = \"" +node.@created + "\"/>";	
						li=new XML(LabXMLstringedges);
						InnoxmlRepo.Edges.appendChild(li);
					
						LabXMLstring ="<Node id = \"EvalOut" + nodeiiii.@id + "\"";
						LabXMLstring =LabXMLstring + " created = \"" + nodeiiii.@created + "\"";
						LabXMLstring =LabXMLstring + " islab = \"true\"";
						LabXMLstring =LabXMLstring + " name = \"" + nodeiiii.@subcriterion + "\"";
						LabXMLstring =LabXMLstring + " evaltype= \"" + nodeiiii.@evaluation_type + "\"";
						LabXMLstring =LabXMLstring + " nodetype= \"Evaluation\"";  
    		 			LabXMLstring =LabXMLstring + " picture = \"http://labs.calt.insead.edu/prototyping/InnoTubeProject/media/eval.jpeg\"";
    		 			LabXMLstring =LabXMLstring + " description = \"\"/>"; 
						li=new XML(LabXMLstring);
						InnoxmlRepo.Edges.appendChild(li);
					
						for each(var nodeiiiii:XML in nodeiiii.participating_users.descendants("participating_user"))
					{
						//<Edge id = "28676" name = "Knows"  tooltip= "knows" fromID = "Rakesh.lalwani@insead.edu"  toID = "a.b.c@d.e.f"  intensity = "5"  edgecolor = "0xffcc00" creationtime = "2007-11-14 00:00:00" /> 
			
						LabXMLstringedges ="<Edge id = \"EvalOut" + nodeiiii.@id + "User" +nodeiiiii.@id +"\"";
						LabXMLstringedges =LabXMLstringedges + " islab = \"true\"";
						LabXMLstringedges =LabXMLstringedges + " tooltip = \"has been involved in\"";
						LabXMLstringedges =LabXMLstringedges + " name = \"Has been involved in\"";
						LabXMLstringedges =LabXMLstringedges + " fromID= \"User" + nodeiiiii.@id +"\"";  
    					LabXMLstringedges =LabXMLstringedges + " toID = \"EvalOut" +nodeiiii.@id + "\"";
    					LabXMLstringedges =LabXMLstringedges + " intensity = \"1\"";
						LabXMLstringedges =LabXMLstringedges + " edgecolor = \"0xff0000\"";
						LabXMLstringedges =LabXMLstringedges + " creationtime = \"" +nodeiiii.@created + "\"/>";	
						li=new XML(LabXMLstringedges);
						InnoxmlRepo.Edges.appendChild(li);
					
					}
					
					}
			
								
				}
								
				
				
			fullGraph=Graph.fromXML(InnoxmlRepo , ["Node","Edge","fromID","toID"]);
		// to remove the existing links using links array
		
		for each (var edge:XML in LinksArray) {
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
	parents=new Array();
	springgraph.empty();
	
	//formation of new myGraph to be displayed in springgraph
	var item1:Item;
	/*
	for each(var item:Item in fullGraph.nodes){
		if (item.data.@islab ==true)
		myGraph.add(item);
	}
	*/
	for each(var node1:Object in fullGraph._edges){
		for each(var node2:Object in node1){
			for each(var linkdata:Object in node2.link) {
				//item1=fullGraph.find(linkdata.id);
				//if( item1.data.@islab==true){
					if ( myGraph.getData(linkdata.SourceID,linkdata.id) == null ) {
						
						var item1:Item=fullGraph.find(linkdata.SourceID);
						if (item1.data.@islab==true)
						{
						myGraph.add(item1);
						NodesArray.push(item1.data)
						}
						var item2:Item=fullGraph.find(linkdata.DestID);
						if (item2.data.@islab==true)
						{
						myGraph.add(item2);
						NodesArray.push(item2.data);
						}
						if (item1.data.@islab==true ||  item2.data.@islab==true)
						{
						 	
						try{
							if (linkdata !=null){
						//		var NewEdgeXml:String=new String('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" tooltip ="' + linkdata.tooltip + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity + '" arrow="'+linkdata.arrow +'" edgecolor="' + linkdata.edgecolor + '" creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') + '"/>');	
								var NewEdgeXml:String=new String('<Edge id="' + linkdata.id + '" name ="' + linkdata.name + '" tooltip ="' + linkdata.tooltip + '" fromID="' + linkdata.SourceID + '" toID="' + linkdata.DestID + '" intensity="' + linkdata.intensity +'" edgecolor="' + linkdata.edgecolor + '" creationtime="' + (linkdata.creationtime.toString()).replace(myPattern,'/') + '"/>');
								myGraph.link(item1,item2,linkdata);							
								
								var tlinkxml:XML=new XML(NewEdgeXml);
								LinksArray.push(tlinkxml);
							}
						}catch(e:Error){
							trace("Error in process result new");
							trace(NewEdgeXml);
						}							
					}
					}
				//}
				
			}
		}
	} 
	myGraph.changed();
	springgraph.dataProvider=myGraph;	

			return;
	}
		
			
		
