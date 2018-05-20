(function(doc,win){
	var docEL = doc.documentElement,
	resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
	recalc = function(){
		var clientWidth = docEL.clientWidth;
		if(!clientWidth) return;
		if(clientWidth >=750){
			clientWidth = 750;
		}
		if(clientWidth <=320){
			clientWidth = 320;
		}
		docEL.style.fontSize = 100 * (clientWidth/375) + 'px';
	};
	if(!doc.addEventListener) return;
	win.addEventListener(resizeEvt,recalc,false);
	doc.addEventListener('DOMContentLoaded',recalc,false);
})(document,window);
