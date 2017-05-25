require([
	"dojo/parser",
	"dijit/layout/LayoutContainer", 
	"dijit/layout/ContentPane", 
	"dijit/Tooltip", 
	"dijit/layout/TabContainer", 
	"dojo/store/Memory" ,
	"dijit/tree/ObjectStoreModel", 
	"dijit/Tree", 
	"dijit/MenuBar", 
	"dijit/PopupMenuItem", 
	"dijit/Menu", 
	"dijit/MenuItem", 
	"dojo/domReady!"
	], 
	function(parser) {
		parser.parse();		
		loadOverlay.endLoading();
	
});