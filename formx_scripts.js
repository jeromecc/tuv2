function Scroller() {
   
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
	}
   
   }
   
function montre_resume_formulaire ( evt, name ) {

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


}