(function( factory ) {
	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define(['pinwin/Tools'], factory );
	} else {
		// Browser globals
		factory();
	}
}(function() {
	return {
		passwordGenerator : function( len ){
            var length = (len)?(len):(12);
            var string = "abcdefghijklmnopqrstuvwxyz"; //to upper
            var numeric = '0123456789';
            var punctuation = '!@#$%^&*()_+~`|}{[]\:;?><,./-=';
            var password = "";
            var character = "";
            while( password.length < length ) {
                entity1 = Math.ceil(string.length * Math.random()*Math.random());
                entity2 = Math.ceil(numeric.length * Math.random()*Math.random());
                entity3 = Math.ceil(punctuation.length * Math.random()*Math.random());
                hold = string.charAt( entity1 );
                hold = (entity1%2==0)?(hold.toUpperCase()):(hold);
                character += hold;
                character += numeric.charAt( entity2 );
                character += punctuation.charAt( entity3 );
                password = character;
            }
            return password;
		},
		
		isValidPassword : function(password)
		{
            var passRegExp = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{12,}$/;
            if(!password.match(passRegExp)) {
                return false;
            }
            return true;
		}
	}
}));