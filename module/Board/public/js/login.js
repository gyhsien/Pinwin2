require([
	"dojo/query",
	"dojo/dom-form",
	"dojo/html",
	"dojo/_base/json",
	"dojo/on",
	"dojox/validate",
	"dojo/request/xhr",
	"dijit/Dialog",
	"dojox/validate/check",
	"dojox/validate/web",
	"dojo/NodeList-traverse",
	"dojo/NodeList-dom",
	"dojo/NodeList-data",
	"dojo/domReady!"
],function(
	query,
	domForm,
	html,
	_json,
	on, 
	validate,
	xhr,
	Dialog
) {
	
	var form = query('#loginForm');
	
	
	query('.help-block').style({display:'none'});
	
	//Dialog init
	var xhrAlert = new Dialog({draggable:false, style: "width:240px;"});
	xhrAlert.placeAt(query('body')[0]);
	
	
	form.on("submit", function(event){		
		event.preventDefault();
		event.stopPropagation();
		
		query('.form-group').removeClass('has-error');
		query('.form-group').removeClass('has-success');
		query('.help-block').style({display:'none'});
		
		var results = validate.check(event.target, {
			required: [ "account", "password", "captcha" ],
			constraints:{
				"captcha":[validate.isText,{length:5}]
			}
		});
		
		
		var misses = results.getMissing(), errors = results.getInvalid();
		
		if(misses.length > 0)
		{
			for(misskey in misses)
			{
				var help_block = query('#'+misses[misskey]).parent('.input-group').next('.help-block');
				var message = _json.fromJson(help_block.attr('data-message'));
				if(message.missing) {html.set(help_block[0], message.missing);}
				help_block.parent('.form-group').addClass('has-error');
				help_block.style({display:'block'});
			}
		}
		
		if(errors.length > 0)
		{
			for(errorkey in errors)
			{
				var help_block = query('#'+errors[errorkey]).parent('.input-group').next('.help-block');
				var message = _json.fromJson(help_block.attr('data-message'));
				if(message.inVaild) {html.set(help_block[0], message.inVaild);}
				help_block.parent('.form-group').addClass('has-error');
				help_block.style({display:'block'});
			}
			
		}
		
		if(results.isSuccessful())
		{
			
			xhr.post('login/login',{
				handleAs:'json',
				data:domForm.toObject(query('#loginForm')[0])
			}).then(function(response){
				
				if(response.stasus)
				{
				
				}else{
					var captcha_img = query('img[src*="webservice/captcha"]')[0];
					on.emit(captcha_img, "click", {bubbles: true, cancelable: true});
					xhrAlert.set({title:'Error', content:response.message});
					xhrAlert.show();
				}
				
				//console.log(response);
			}, function(error){
				xhrAlert.set({title:'Error', content:xhrAlert.errorMessage});
				xhrAlert.show();
			});
						
			
		}
	});
	
	loadOverlay.endLoading();
});
