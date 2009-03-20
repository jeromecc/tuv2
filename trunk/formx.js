function formxjs_getvalue(iditem) {
	//Radio
	var eles = document.getElementsByName('FoRmX_'+iditem+'[]') ;

	if ( eles.length > 0 ) {
		for (i=0 ; i<eles.length ; i++) {
			if (eles[i].checked) {
				//alert(eles[i].value)
				return eles[i].value ;
			}
		}
	}
	var eles = document.getElementsByName('FoRmX_'+iditem) ;
	if ( eles.length >= 1 ) {
		return eles[0].value ;
	}
}

function formxjs_appears(iditem) {
	// dataHtml = document.getElementById('ligne_'+iditem).innerHTML ;
	 //document.getElementById('ligne_'+iditem).innerHTML = '' ;
	if ( BrowserDetect.browser == 'explorer' || BrowserDetect.browser == 'Explorer'  )
		document.getElementById('ligne_'+iditem).style.display = 'block' ;
	else
		document.getElementById('ligne_'+iditem).style.display = 'table-row' ;


	//debug pour bug avec opera ( ma faute car je n'arrive pas à le reproduire sur une page claire )
	selects = document.getElementById('ligne_'+iditem).getElementsByTagName('select');
	if (selects.length > 0 )
		for(i = 0 ; i < selects.length ; i++)
			{
				selects[i].style.visibility = 'visible';
			}

}

function formxjs_disappears(iditem) {
	document.getElementById('ligne_'+iditem).style.display = 'none';
}




function formxjs_invalue(val,iditem) {

	var eles = document.getElementsByName('FoRmX_'+iditem+'[]') ;
	if ( eles.length > 0 ) {
		for (i=0 ; i<eles.length ; i++) {
			if (eles[i].getAttribute('type')=='checkbox' && eles[i].checked) {
				//alert(eles[i].value)
				if ( val == eles[i].value )
					return true ;
			}
		}
	}
	return false ;
} 
 
 //-----------------------------------------
 //Fonctions auxilliaires
 //-----------------------------------------
 
 
 
 if (  typeof(getWindowHeight) != 'function') {function getWindowHeight() {
    var windowHeight=0;
    if (typeof(window.innerHeight)=='number') {
        windowHeight=window.innerHeight;
    }
    else {
     if (document.documentElement&&
       document.documentElement.clientHeight) {
         windowHeight = document.documentElement.clientHeight;
    }
    else {
     if (document.body&&document.body.clientHeight) {
         windowHeight=document.body.clientHeight;
      }
     }
    }
    return windowHeight;
}}
 

 if (  typeof(addZero) != 'function') { function addZero(ss) { 
	 s =  ss.toString() ;
	 if ( s.length == 1 )
	 {
		 return '0' + s ;
	 }
	 return s ;
 } }
 
 
 
if (  typeof(Scroller) != 'function') { function Scroller() {
   
   NS4 = (document.layers) ? 1 : 0;
   IE4 = (document.all) ? 1 : 0;
   W3C = (document.getElementById) ? 1 : 0;
   
   if ( W3C ) {
   	ele = document.getElementById('formX');
	docHeight = window.innerHeight;
	layerHeight = ele.offsetHeight;
	docHeight = getWindowHeight();
	zetop = ele.offsetTop + 30;
	window.scrollBy(0,zetop + layerHeight-docHeight);
	} else if ( NS4 ) {
   	ele = document.layers['formX'];
   	docHeight = document.height;
	layerHeight = ele.clip.height;
	zetop = 50 ;
	window.scrollBy(0,zetop + layerHeight-docHeight);
   } else {
	docHeight = document.body.offsetHeight;
	ele = document.all["formX"];
	layerHeight = ele.offsetHeight;
	window.scrollBy(0,zetop + layerHeight-docHeight);
	}}
   
   }


if (  typeof(BrowserDetect) != 'var')
{
var BrowserDetect = {
	init: function () {
		this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
		this.version = this.searchVersion(navigator.userAgent)
			|| this.searchVersion(navigator.appVersion)
			|| "an unknown version";
		this.OS = this.searchString(this.dataOS) || "an unknown OS";
	},
	searchString: function (data) {
		for (var i=0;i<data.length;i++)	{
			var dataString = data[i].string;
			var dataProp = data[i].prop;
			this.versionSearchString = data[i].versionSearch || data[i].identity;
			if (dataString) {
				if (dataString.indexOf(data[i].subString) != -1)
					return data[i].identity;
			}
			else if (dataProp)
				return data[i].identity;
			
		}
		return true ;
	},
	searchVersion: function (dataString) {
		var index = dataString.indexOf(this.versionSearchString);
		if (index == -1) return false ;
		return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
	},
	dataBrowser: [
		{ 	string: navigator.userAgent,
			subString: "OmniWeb",
			versionSearch: "OmniWeb/",
			identity: "OmniWeb"
		},
		{
			string: navigator.vendor,
			subString: "Apple",
			identity: "Safari"
		},
		{
			prop: window.opera,
			identity: "Opera"
		},
		{
			string: navigator.vendor,
			subString: "iCab",
			identity: "iCab"
		},
		{
			string: navigator.vendor,
			subString: "KDE",
			identity: "Konqueror"
		},
		{
			string: navigator.userAgent,
			subString: "Firefox",
			identity: "Firefox"
		},
		{
			string: navigator.vendor,
			subString: "Camino",
			identity: "Camino"
		},
		{		// for newer Netscapes (6+)
			string: navigator.userAgent,
			subString: "Netscape",
			identity: "Netscape"
		},
		{
			string: navigator.userAgent,
			subString: "MSIE",
			identity: "Explorer",
			versionSearch: "MSIE"
		},
		{
			string: navigator.userAgent,
			subString: "Gecko",
			identity: "Mozilla",
			versionSearch: "rv"
		},
		{ 		// for older Netscapes (4-)
			string: navigator.userAgent,
			subString: "Mozilla",
			identity: "Netscape",
			versionSearch: "Mozilla"
		}
	],
	dataOS : [
		{
			string: navigator.platform,
			subString: "Win",
			identity: "Windows"
		},
		{
			string: navigator.platform,
			subString: "Mac",
			identity: "Mac"
		},
		{
			string: navigator.platform,
			subString: "Linux",
			identity: "Linux"
		}
	]

};
BrowserDetect.init();
}






   
if (  typeof(montre_resume_formulaire) != 'function') { function montre_resume_formulaire ( evt, name ) {

if (IE4) {
    evt = window.event;
  }

  var currentX,		
      currentY,		
      x,		
      y,		
      docWidth,		
      docHeight,	
      layerWidth,	
      layerHeight,	
      ele;		

  if ( W3C ) {
  
    ele = document.getElementById(name);
    currentX = evt.clientX,
    currentY = evt.clientY - 10;
    docWidth =  window.innerWidth;
    docHeight = window.innerHeight;
    layerWidth = ele.offsetWidth;
    layerHeight = ele.offsetHeight;

  } else if ( NS4 ) {
    ele = document.layers[name];
    currentX = evt.pageX,
    currentY = evt.pageY;
    docWidth = document.width;
    docHeight = document.height;
    layerWidth = ele.clip.width;
    layerHeight = ele.clip.height;

  } else {	
  
    ele = document.all[name];
    currentX = evt.clientX,
    currentY = evt.clientY;
    docHeight = document.body.offsetHeight;
    docWidth = document.body.offsetWidth;
    layerWidth = 200;
    layerHeight = ele.offsetHeight;
  }

  if ( NS4 ) {
    ele.left = 10;
    ele.top = 300;
    ele.width = 770;
    ele.visibility = "show";
  } else {  
    ele.style.left = "10px";
    ele.style.top = "430px";
    ele.style.width = "770px";
    ele.style.visibility = "visible";
  }


} }