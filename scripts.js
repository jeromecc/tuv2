
function loadScripts() {
cacherSelect();
}

function reload( form ){form.submit();}
window.onload=montre;

function montre(id) {
    var d = document.getElementById(id);
    for (var i = 1; i<=10; i++) {
        if (document.getElementById('s'+i)) {document.getElementById('s'+i).style.display='none';}
	}    
    if (d) {d.style.display='block';}
}

function cache(id) {
  var d = document.getElementById(id);
  if (d) {d.style.display='none';}
}

function show(id) {
  var d = document.getElementById(id);
  if (d) {d.style.display='block';}
}


NS4 = (document.layers) ? 1 : 0;
IE4 = (document.all) ? 1 : 0;
W3C = (document.getElementById) ? 1 : 0;	


function hide ( name ) {
  if (W3C) {
    document.getElementById(name).style.visibility = "hidden";
  } else if (NS4) {
    document.layers[name].visibility = "hide";
  } else {
    document.all[name].style.visibility = "hidden";
  }
}

function unhide ( name ) {
  if (W3C) {
    document.getElementById(name).style.visibility = "visible";
  } else if (NS4) {
    document.layers[name].visibility = "show";
  } else {
    document.all[name].style.visibility = "visible";
  }
}


function opacity(id, opacStart, opacEnd, millisec) {
    var speed = Math.round(millisec / 100);
    var timer = 0;

    if(opacStart > opacEnd) {
        for(i = opacStart; i >= opacEnd; i--) {
            setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
            timer++;
        }
    } else if(opacStart < opacEnd) {
        for(i = opacStart; i <= opacEnd; i++)
            {
            setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
            timer++;
        }
    }
}

function changeOpac(opacity, id) {
    var object = document.getElementById(id).style;
    object.opacity = (opacity / 100);
    object.MozOpacity = (opacity / 100);
    object.KhtmlOpacity = (opacity / 100);
    object.filter = "alpha(opacity=" + opacity + ")";
} 

function shiftOpacity(id, millisec) {
    if ( millisec == undefined ) var millisec = 700 ;
    if(document.getElementById(id).style.opacity == 0) {
        opacity(id, 0, 100, millisec);
        document.getElementById(id).style.display = 'block';
    } else {
        opacity(id, 100, 0, millisec);
        setTimeout(function(){document.getElementById(id).style.display = 'none';},millisec);
    }
} 

function fadein ( id ) {
    opacity(id, 0, 100, 800 );
}

function fadeout ( id ) {
    opacity(id, 100, 0, 800 );
}

function getWindowHeight() {
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
}




//objet de detection de navigateur
//http://www.quirksmode.org/js/detect.html
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
	},
	searchVersion: function (dataString) {
		var index = dataString.indexOf(this.versionSearchString);
		if (index == -1) return;
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


function cacherSelect() {
if ( BrowserDetect.browser != 'Explorer' || BrowserDetect.version != '6' )
	return ;
eletest1 = 	document.getElementById("newform_blocvalid");
eletest2 = 	document.getElementById("formX");
eletest3 = 	document.getElementById("ajoutermini");

if(! eletest1 && ! eletest2 && ! eletest3)
	return ;
	
var ele = document.getElementById("informationspatient");
var eles = ele.getElementsByTagName("select");
for(var i=0;i<eles.length;i++) {
	eles[i].style.visibility="hidden";
 }
var ele = document.getElementById("transfertpatient");
var eles = ele.getElementsByTagName("select");
for(var i=0;i<eles.length;i++) {
	eles[i].style.visibility="hidden";
 } 
}



