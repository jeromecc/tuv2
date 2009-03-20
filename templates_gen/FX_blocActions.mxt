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
    <tr><th>Libellé</th><th>Modifié le</th><th>Créateur</th><th>Statut</th></tr>
</mx:bloc id="titre">

<mx:bloc id="padetitre">
    <tr><td colspan='2'>Pas de formulaires</td></tr>
</mx:bloc id="padetitre">

<tr>
  <td>
   <input name="act_nouveau"  type="image" src="images/modifier.png"   value="act_nouveau"  
onmouseover="return overlib('Remplir un formulaire');" onmouseout="return nd();" mXattribut="onclick:newact_code" /></td>
   </tr>

   <form name="FoRmXcase" method="POST" action="index.php">
    <mx:bloc id="actions">
    <mx:text id="divers" />
      <tr>
    <td><span  mXattribut="onclick:code" class="formxcase_libelle" mXattribut="onmouseover:codemouseover" mXattribut="onmouseout:codemouseout" ><mx:text id="libelle" /></span>&nbsp;&nbsp;  </td>
    
    <td><mx:text id="dermodif" /></td>
    
        <td><mx:text id="author" /></td>
    
    <td>  <i><mx:text id="statut" /></i>
    
    <mx:bloc id="frem">
    <input name="act_delete"  type="image" src="images/trash.gif"   value="act_delete" mXattribut="onclick:code" />
    </mx:bloc id="frem">
    
    <mx:bloc id="fed">
    <input name="act_delete"  type="image" src="images/reparer.gif"   value="act_delete" mXattribut="onclick:code" />
    </mx:bloc id="fed">
    
    </td>
    
    </tr>
   </mx:bloc id="actions">
   
   
  <mx:hidden id="hidden" />
  <input name="FormX_ext_goto_" type="hidden" value="">
  </form>
</table>
</div>