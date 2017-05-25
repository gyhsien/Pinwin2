require([
	"dojo/dom-attr",
	"dojo/query",
	"dojo/dom-form",
	"dojo/on",
	"dojo/request/xhr",
	"pinwin/Tools",
	"dojo/domReady!"
],function(
	domAttr,
	query,
	domForm,
	on,
	xhr,
	Tools
) {
	
	var form = query('#passwordForm');
	var passwordNode = query('input[name="password"]')[0];
	
	form.on("submit", function(event){
		event.preventDefault();
		event.stopPropagation();
		var valid = Tools.isValidPassword(domAttr.get(passwordNode, 'value'));
		if(valid)
		{
			xhr.post(form[0].getAttribute('action'), {
				handleAs:'json',			
				data:domForm.toObject(form[0])
			}).then(function(response){
				if(response.status)
				{
					location.reload();
				}
			}, function(error){
				
			});
		}
	});
	
	query('button:first-child').on('click', function(){
		domAttr.set(passwordNode, 'value', Tools.passwordGenerator());
	});
	
	loadOverlay.endLoading();
});
