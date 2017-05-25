var loadOverlay;
require(["dojo/dom-style", "dojo/_base/fx", "dojo/_base/declare",
		"dojo/dom" ], function(domStyle, fx, declare,
		dom) {
	var LoadOverlay = declare(null, {
		overlayWrapper : null,
		constructor : function() {
			this.overlayWrapper = dom.byId('loadingOverlayWrapper');
		},
		endLoading : function() {
			fx.fadeOut({
				node : this.overlayWrapper,
				onEnd : function(node) {
					domStyle.set(node, 'display', 'none');
				}
			}).play();
		}
	});

	loadOverlay = new LoadOverlay();
});