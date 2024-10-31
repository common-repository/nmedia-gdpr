(function(window){
  'use strict';

    // This function will contain all our code
   	function wpgdpr(){
   	var _thisWPGDPR = {};

    // testing helo function
   	_thisWPGDPR.hello = function() {
   		alert('hi');
   	}
    

   
    // We will add functions to our library here !
    return _thisWPGDPR;
    }


  // We need that our library is globally accesible, then we save in the window
    if(typeof(window.WPGDPR) === 'undefined'){
        window.WPGDPR = wpgdpr();
    }
})(window); // We send the window variable withing our function