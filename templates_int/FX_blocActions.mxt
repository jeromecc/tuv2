<style>
.formxcase_libelle {
cursor:pointer ; cursor:hand;
}
.formxcase_libelle:hover {
color:#AA149B;
}

</style>


<div id="bloc_formx">
<table>
  <tr><th colspan='2' >Formulaires et actions</th></tr>

<mx:bloc id="titre">
    <tr><th>Libelle</th><th>Date</th><th>Statut</th></tr>
</mx:bloc id="titre">

<mx:bloc id="padetitre">
    <tr><td colspan='2'>Pas de formulaires</td></tr>
</mx:bloc id="padetitre">

<tr>
  <td>
	<mx:bloc id="nouveauF"><input name="act_nouveau"  type="image" src="images/bonNew.png"   value="act_nouveau" onmouseover="return overlib('Remplir un formulaire');" onmouseout="return nd();" mXattribut="onclick:newact_code" /></mx:bloc id="nouveauF">
	<mx:bloc id="racbonradio"><input name="act_nouveau"  type="image" src="images/bonRadio.png"   value="act_nouveau" onmouseover="return overlib('Bon de radio/scanner/echo');" onmouseout="return nd();" mXattribut="onclick:newRadio" /></mx:bloc id="racbonradio">
	<mx:bloc id="racbonlabo"><input name="act_nouveau"  type="image" src="images/bonLabo.gif"   value="act_nouveau" onmouseover="return overlib('Bon de labo');" onmouseout="return nd();" mXattribut="onclick:newLabo" /></mx:bloc id="racbonlabo">
	<mx:bloc id="racboncs"><input name="act_nouveau"  type="image" src="images/bonCS.gif"   value="act_nouveau" onmouseover="return overlib('Bon de consultation sp&eacute;cialis&eacute;e');" onmouseout="return nd();" mXattribut="onclick:newCS" /></mx:bloc id="racboncs">
	<mx:bloc id="racbonradioradio"><input name="act_nouveau"  type="image" src="images/bonRadio.png"   value="act_nouveau" onmouseover="return overlib('Bon de Radio');" onmouseout="return nd();" mXattribut="onclick:newRadioRadio" /></mx:bloc id="racbonradioradio">
	<mx:bloc id="racbonradioscanner"><input name="act_nouveau"  type="image" src="images/bonScanner.png"   value="act_nouveau" onmouseover="return overlib('Bon de Scanner');" onmouseout="return nd();" mXattribut="onclick:newRadioScanner" /></mx:bloc id="racbonradioscanner">
	<mx:bloc id="racbonradioecho"><input name="act_nouveau"  type="image" src="images/bonEcho.png"   value="act_nouveau" onmouseover="return overlib('Bon d\'Echographie');" onmouseout="return nd();" mXattribut="onclick:newRadioEcho" /></mx:bloc id="racbonradioecho">
	<mx:bloc id="transfert"><input name="act_nouveau"  type="image" src="images/bonTransfert.gif"   value="act_nouveau" onmouseover="return overlib('Transfert du patient');" onmouseout="return nd();" mXattribut="onclick:newT" /></mx:bloc id="transfert">
</td>
   </tr>

   <form name="FoRmXcase" method="POST" action="index.php">
    <mx:bloc id="actions">
    <mx:text id="divers" />
      <tr>
    <td><span  mXattribut="onclick:code" class="formxcase_libelle" mXattribut="onmouseover:codemouseover" mXattribut="onmouseout:codemouseout" ><mx:text id="libelle" /></span>&nbsp;&nbsp;  </td>
    <td><mx:text id="dateForm" />
    </td>
    <td>  <i><mx:text id="statut" /></i>
    
    <mx:bloc id="frem">
    <input name="act_delete"  type="image" src="images/trash.gif"   value="act_delete" mXattribut="onclick:code" />
    </mx:bloc id="frem">
    
    <mx:bloc id="frep">
    
    <mx:text id="lienPrint" /><img src="images/imprimer.png" alt="Imprimer" /></a>
    </mx:bloc id="frep">
    
    <mx:bloc id="fed">
    <input name="act_delete"  type="image" src="images/reparer.gif"   value="act_delete" mXattribut="onclick:code" />
    </mx:bloc id="fed">
    
    </td>
    
    </tr>
   </mx:bloc id="actions">
   
   
  <mx:hidden id="hidden" />
  <input name="Formulaire2print" type="hidden" value="">
  <input name="FormX_ext_goto_" type="hidden" value="">

  <input name="FoRmX_selValid" type="hidden" value="">
  <input name="FoRmX_selValid_x" type="hidden" value="">
   
  <input name="FormX_to_open_" type="hidden" value=""> 
  <input name="ids" type="hidden" value="">
  </form>
</table>
</div>
