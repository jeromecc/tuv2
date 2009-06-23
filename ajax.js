var wait = 0 ;

// Attente lors des requêtes ajax.
function setWait ( div ) {
    var mod = document.getElementById ( div ) ;
    var left = mod.offsetLeft ;
    var top = mod.offsetTop ;
    var width = mod.offsetWidth ;
    var height = mod.offsetHeight ;
    var img = '<img src="images/wait2.gif" alt="Chargement en cours..." style="position: absolute; left:'+(width-left-30)+'px; top: 10px; filter:alpha(opacity=100); -moz-opacity:1; opacity: 1;"/>' ;
    wait = 1 ;
    setDiv ( 'wait', '<div style="position: absolute; left: '+left+'px; top: '+top+'px; width:'+width+'px; height: '+height+'px; background-color: #CCCCCC; filter:alpha(opacity=50); -moz-opacity:0.5; opacity: 0.5; z-index:100;">'+img+'</div>') ;
}

function unsetWait ( ) {
    wait = 0 ;
    setDiv ( 'wait', '' ) ;
}

// ************************************************************** //
// ************************ Modification ************************ //
// ************************************************************** //

// Préparation des informations à transmettre pour afficher la fenêtre de modification.
function modPrepare ( str ) {
    var mod = document.getElementById ( 'mod' ) ;
    //document.onclick = position ;
    //position ;
    //alert ( page_y ) ;
    mod.style.display = "block" ;
    mod.style["top"] = page_y+"px" ;
    mod.style["left"] = page_x+"px" ;
    mod.style["left"] = "100px" ;
    mod.style.zIndex = "100" ;
}



// Affichage de la fenêtre de modification.
function mod ( result, idDiv ) {
    if (result.readyState == 4) {
        setDiv ( idDiv, result.responseText);
        request('index.php?navi=QWpheHxtb2RFbnRyeUpT',null,'modJS') ;
    } else {
        setDiv ( idDiv, '<br/><img src="images/wait2.gif" alt="Chargement en cours..." /><br/>Chargement en cours...<br/>' ) ;
    }
}

// Préparation des informations à transmettre pour afficher la fenêtre de modification.
function modJSPrepare ( str ) {
    
}

// Javascript à exécuter.
function modJS ( result, idDiv ) {
    if (result.readyState == 4) {
        //alert ( result.responseText ) ;
        eval ( result.responseText ) ;
    } else {
        //setDiv ( idDiv, '<br/><img src="images/wait2.gif" alt="Chargement en cours..." /><br/>Chargement en cours...<br/>' ) ;
    }
}

// ************************************************************** //
// ************************ Export V2 *************************** //
// ************************************************************** //

function setTraitementPrepare ( ) {
    
}

function setTraitement ( ) {

}

function inverserTraitement ( id, i, partie ) {
    var bgc = document.getElementById('idpatient'+i+id).style['background'] ;
    //alert ( bgc.substr(0,3) ) ;
    if ( bgc.substr(0,3) == 'red' ) {
        document.getElementById('idpatient'+i+id).style['background'] = 'green' ;
        document.images['img'+i+id].src='images/gg.gif';
        setDiv('text'+i+id,'La partie '+partie+' de cet export a &eacute;t&eacute; trait&eacute;e.');
        //alert ('La partie '+partie+' de cet export a &eacute;t&eacute; trait&eacute;e.');
    } else {
        document.getElementById('idpatient'+i+id).style['background'] = 'red' ;
        document.images['img'+i+id].src='images/dd.gif';
        setDiv('text'+i+id,'La partie '+partie+' de cet export n\'est pas trait&eacute;e.');
        //alert('La partie '+partie+' de cet export n\'est pas trait&eacute;e.');
    }
}

// ************************************************************** //
// ************************* Fusions **************************** //
// ************************************************************** //

function fusionPrepare ( ) {
    setWait ( 'navigation' ) ;
    cache('showFusions');
}

function fusion ( result ) {
    if (result.readyState == 4) {
        setDiv ( 'listeFusions', result.responseText);
        unsetWait ( 'navigation' ) ;
    }
}


// ************************************************************** //
// ************************** Radios **************************** //
// ************************************************************** //

var bn = false ;

function activerCommentaireRadio  ( enr ) {
	if ( bn && enr ) {
		bn = false ;
		document.getElementById('notetext').style.background='#EEEEEE';
                document.getElementById('notemod').style.border='1px solid #EEEEEE';
		setDiv ( 'noteactions', '' ) ;
		enregistrerCommentaireRadio ( ) ;
	} else {
		bn = true ;
		document.getElementById('notetext').style.background='#FFFFFF';
		document.getElementById('notemod').style.border='1px solid red';
		setDiv ( 'noteactions', '[Cliquez ici pour enregistrer]' ) ;
	}
}

function enregistrerCommentaireRadio ( ) {
	var note = document.getElementById('notetext').value ;
	var idradio = document.getElementById('idradio').value ;
    request('index.php?navi=QWpheHxzZXRDb21SYWRpbw==&ajax=1&note='+note+'&idradio='+idradio,null,'') ;
}


// Prépare les résultats de la recherche à envoyer.
function getRadiosPrepare ( ) {
    var typeListe = document.forms.typeListeD.elements['typeListe'].options[document.forms.typeListeD.elements['typeListe'].selectedIndex].value ;
    var data = "typeListe="+typeListe ;
    //alert ( data ) ;
    setWait ( 'navigation' ) ;
    return data ;
}

// Affichage des résultats.
function getRadios ( result ) {
    if (result.readyState == 4) {
        //alert(result.responseText+' '+document.forms.searchForm.search.value);
        setDiv ( 'listeRadios', result.responseText);
        //sortables_init ( ) ;
        unsetWait ( 'navigation' ) ;
    } else {
        //setDiv ( 'listeRadios', '<br/><img src="images/wait.gif" alt="Chargement en cours..." /><br/>Chargement en cours...<br/>' ) ;
    }
}

// Préparation
function setRadiologuePrepare ( ) {
    var adeli = document.forms.radiologues.elements['radiologue'].options[document.forms.radiologues.elements['radiologue'].selectedIndex].value ;
    var data = "adeli="+adeli ;
    //alert ( data ) ;
    return data ;
}

// Rafraichissement.
function setRadiologue ( result ) {
    if (result.readyState == 4) {
        //alert(result.responseText+' '+document.forms.searchForm.search.value);
        setDiv ( 'RadioCCAM', result.responseText);
    } else {
        setDiv ( 'RadioCCAM', '<br/><img src="images/wait.gif" alt="Chargement en cours..." /><br/>Chargement en cours...<br/>' ) ;
    }
}

// Préparation
function setAnesthesiePrepare ( ) {
    var anes = document.getElementById ( 'anesthesie' ).options[document.getElementById ( 'anesthesie' ).selectedIndex].value ;
    var data = "anesthesie="+anes ;
    //alert ( data ) ;
    return data ;
}

// Rafraichissement.
function setAnesthesie ( result ) {
    if (result.readyState == 4) {
        //alert(result.responseText+' '+document.forms.searchForm.search.value);
        setDiv ( 'debug', result.responseText);
    } else {
        //setDiv ( 'RadioCCAM', '<br/><img src="images/wait.gif" alt="Chargement en cours..." /><br/>Chargement en cours...<br/>' ) ;
    }
}


// Préparation
function setModificateursRadioPrepare ( i ) {
    var B = document.getElementById ( 'B'+i ).checked ;
    var C = document.getElementById ( 'C'+i ).checked ;
    var D = document.getElementById ( 'D'+i ).checked ;
    var Y = document.getElementById ( 'Y'+i ).checked ;
    var Z = document.getElementById ( 'Z'+i ).checked ;
    var data = "B="+B+"&C="+C+"&D="+D+"&Y="+Y+"&Z="+Z ;
    //alert ( data ) ;
    return data ;
}

// Rafraichissement.
function setModificateursRadio ( result ) {
    if (result.readyState == 4) {
        //alert(result.responseText+' '+document.forms.searchForm.search.value);
        //setDiv ( 'listeRadios', result.responseText);
    } else {
        //setDiv ( 'RadioCCAM', '<br/><img src="images/wait.gif" alt="Chargement en cours..." /><br/>Chargement en cours...<br/>' ) ;
    }
}

// Préparation
function addActeRadioPrepare ( ) {
    var acte = document.forms.listeCCAM.elements['acte'].options[document.forms.listeCCAM.elements['acte'].selectedIndex].value ;
    var data = "acte="+acte+"&addActe=1" ;
    //alert ( data ) ;
    return data ;
}

// Rafraichissement.
function addActeRadio ( result ) {
    if (result.readyState == 4) {
        //alert(result.responseText+' '+document.forms.searchForm.search.value);
        setDiv ( 'listeActesRadio', result.responseText);
    } else {
        setDiv ( 'listeActesRadio', '<br/><img src="images/wait.gif" alt="Chargement en cours..." /><br/>Chargement en cours...<br/>' ) ;
    }
}

// Préparation
function addActeRadioBisPrepare ( ) {
    var acte = document.forms.listeCCAM.elements['acte'].options[document.forms.listeCCAM.elements['acte'].selectedIndex].value ;
    var data = "acte="+acte+"&addActe=1" ;
    //alert ( data ) ;
    return data ;
}

// Rafraichissement.
function addActeRadioBis ( result ) {
    if (result.readyState == 4) {
        //alert(result.responseText+' '+document.forms.searchForm.search.value);
        setDiv ( 'mod', result.responseText);
    } else {
        setDiv ( 'mod', '<br/><img src="images/wait.gif" alt="Chargement en cours..." /><br/>Chargement en cours...<br/>' ) ;
    }
}

// Préparation
function delActeRadioPrepare ( ) {
    var data = "acte=" ;
    //alert ( data ) ;
    return data ;
}

// Rafraichissement.
function delActeRadio ( result ) {
    if (result.readyState == 4) {
        //alert(result.responseText+' '+document.forms.searchForm.search.value);
        setDiv ( 'listeActesRadio', result.responseText);
    } else {
        setDiv ( 'listeActesRadio', '<br/><img src="images/wait.gif" alt="Chargement en cours..." /><br/>Chargement en cours...<br/>' ) ;
    }
}

// Prépare les résultats de la recherche à envoyer.
function reloadBandeauPrepare ( ) {
    var typeListe = document.forms.typeListeD.elements['typeListe'].options[document.forms.typeListeD.elements['typeListe'].selectedIndex].value ;
    var data = "typeListe="+typeListe ;
    //alert ( data ) ;
    return data ;
}

// Affichage des résultats.
function reloadBandeau ( result ) {
    if (result.readyState == 4) {
        setDiv ( 'informationsRadio', result.responseText);
    } else {
        setDiv ( 'informationsRadio', '<br/><img src="images/wait.gif" alt="Chargement en cours..." /><br/>Chargement en cours...<br/>' ) ;
    }
}

// Prépare les résultats de la recherche à envoyer.
function valDateRadiosPrepare ( ) {
    var valDate = document.forms.valEtape.elements['date'].options[document.forms.valEtape.elements['date'].selectedIndex].value ;
    var data = "valDate="+valDate+"&typeDate="+document.forms.valEtape.typeDate.value ;
    //alert ( data ) ;
    return data ;
}

// Affichage des résultats.
function valDateRadios ( result ) {
    if (result.readyState == 4) {
        setDiv ( 'mod', result.responseText);
        request('index.php?navi=QWpheHxnZXRSYWRpb3M=',null,'getRadios') ;
        request('index.php?navi=QWpheHxyZWxvYWRCYW5kZWF1',null,'reloadBandeau') ;
    } else {
        setDiv ( 'mod', '<br/><img src="images/wait.gif" alt="Chargement en cours..." /><br/>Chargement en cours...<br/>' ) ;
    }
}

// Prépare les résultats de la recherche à envoyer.
function annDateRadiosPrepare ( ) {
    var data = "typeDate="+document.forms.annEtape.typeDate.value ;
    //alert ( data ) ;
    return data ;
}

// Affichage des résultats.
function annDateRadios ( result ) {
    if (result.readyState == 4) {
        setDiv ( 'mod', result.responseText);
        request('index.php?navi=QWpheHxnZXRSYWRpb3M=',null,'getRadios') ;
        request('index.php?navi=QWpheHxyZWxvYWRCYW5kZWF1',null,'reloadBandeau') ;
    } else {
        setDiv ( 'mod', '<br/><img src="images/wait.gif" alt="Chargement en cours..." /><br/>Chargement en cours...<br/>' ) ;
    }
}


// ************************************************************** //
// ************************ Export V2 *************************** //
// ************************************************************** //


// Prépare les résultats de la recherche à envoyer.
function getExportV2Prepare ( ) {
    var date = document.getElementById ( 'listeDates' ).options[document.getElementById ( 'listeDates' ).selectedIndex].value ;
    var data = "dt_sortie="+date ;
    //alert ( data ) ;
    return data ;
}

// Affichage des résultats.
function getExportV2 ( result ) {
    if (result.readyState == 4) {
        setDiv ( 'listeExportV2', result.responseText);
    } else {
        setDiv ( 'listeExportV2', '<br/><img src="images/wait.gif" alt="Chargement en cours..." /><br/>Chargement en cours...<br/>' ) ;
    }
}


// ************************************************************** //
// ************************ Recherche *************************** //
// ************************************************************** //

// Prépare les résultats de la recherche à envoyer.
function getPatientsPrepare ( ) {
    var sexe = document.forms.creationPatient.elements['sexe'].options[document.forms.creationPatient.elements['sexe'].selectedIndex].value ;
    var prenom = document.forms.creationPatient.prenom.value ;
    var nom = document.forms.creationPatient.nom.value ;
    var data = "nom="+nom+"&prenom="+prenom+"&sexe="+sexe ;
    //alert ( data ) ;
    return data ;
}

// Affichage des résultats.
function getPatients ( result ) {
    if (result.readyState == 4) {
        //alert(result.responseText+' '+document.forms.searchForm.search.value);
        setDiv ( 'recherchePatients', '<br/>'+result.responseText);
        sortables_init ( ) ;
    } else {
        setDiv ( 'recherchePatients', '<br/><img src="images/wait.gif" alt="Chargement en cours..." /><br/>Chargement en cours...<br/>' ) ;
    }
}

// Requête HTTP.
function request ( strURL, strSubmit, strResultFunc ) {

    
    setDiv ( strResultFunc, '<br/><img src="images/wait2.gif" alt="Chargement en cours..." /><br/>Chargement en cours...<br/>' ) ;
    //alert ( strResultFunc ) ;
    var http_request = false;
    if (window.XMLHttpRequest) { // Mozilla, Safari,...
        http_request = new XMLHttpRequest();
        if (http_request.overrideMimeType) {
            http_request.overrideMimeType('text/xml');
            // Voir la note ci-dessous � propos de cette ligne
        }
    } else if (window.ActiveXObject) { // IE
        try {
            http_request = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                http_request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
        }
    }
    
    
    
    if (!http_request) {
        alert('Abandon :( Impossible de cr�er une instance XMLHTTP');
        return false;
    }
    
    //alert ( strSubmit ) ;
    if ( strResultFunc ) {
        eval('var data = ' +strResultFunc + 'Prepare' + '(strSubmit);');
    } else {
        var data='' ;
    }
    
    http_request.open('POST', strURL, true);
    http_request.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    http_request.send(data); 
    http_request.onreadystatechange = function() {
       if (http_request.readyState == 4) {
       //alert ( http_request.responseText ) ;
       }
        eval(strResultFunc + '(http_request,strResultFunc);');
        
    }
    
}



// Réecriture du contenu d'un DIV.
var ns4 = (document.layers)? true:false;
var ie4 = (document.all)? true:false;
var dom = (document.getElementById)? true:false;

function setDiv(ID,Content) {
    if (dom) {
        var elem = document.getElementById ( ID ) ;
        if ( elem ) {
            elem.innerHTML = Content;
        }
        return;
    }
    if (ie4) {
        document.all[ID].innerHTML = Content;
        return;
    }
    if (ns4) {
        with (eval('document.'+ID+'.document')) {
            open();
            write(Content);
            close();
        }
        return;
    }
}


function requestForm ( strURL, idform,iddiv  ) {
    
    var data = datapost(idform);
    //alert ( data ) ;
    request ( strURL, data, iddiv );
}


function getFormParent(id) {
var forms = document.getElementsByTagName('form');
for (var i=0 ; i<forms.length ; i++) {
        form = forms[i];
        var eles = form.getElementsByTagName('input');
        for (var j=0 ; j<eles.length ; j++) {
                ele = eles[j];
                if( ele && ele.id==id) {
                        return form.id;
                }
        }
}

}


function datapost(idform) {
    if ( ! idform ) {
        res = datapost ( "creation" ) ;
        if ( ! res )
            res = datapost ( "modification" ) ;
    } else {
        var formulaire = document.getElementById(idform) ;
        if ( formulaire ) {
            var champs = formulaire.getElementsByTagName('input');
            var res = "ajax=1" ;
            for (var i=0 ; i<champs.length ; i++) {
                ele=champs[i];
                res += "&"+ele.name+"="+ele.value ;
            }
            var champs = document.getElementById(idform).getElementsByTagName('select');
            for (var i=0 ; i<champs.length ; i++) {
                ele=champs[i];
                res += "&"+ele.name+"="+ele.value ;
            }
        }
    }
    return res;
}


// Capture en temps réel des coordonnées du curseur.
function position ( evt ) {
	if ( ie4 ) {
        evt = window.event ;	
	    screen_x = evt.clientX ;
	    screen_y = evt.clientY ;
	    page_x = screen_x + document.documentElement.scrollLeft ;
	    page_y = screen_y + document.documentElement.scrollTop ;
	} else {
        if(!evt) evt = window.event ;	
	    screen_x = evt.clientX ;
	    screen_y = evt.clientY ;
	    page_x = evt.pageX ;
	    page_y = evt.pageY ;
	}
}

var tempo ;
