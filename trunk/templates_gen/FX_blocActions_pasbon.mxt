<style>
.formxcase_libelle {
cursor:pointer ; cursor:hand;
}
.formxcase_libelle:hover {
color:#AA149B;
}

</style>


<table style="border:none;"><tr><td>

<table>
  <tr><th colspan='2' >Actions</th></tr>

<mx:bloc id="titre">
    <tr><th>Libellé</th><th>Statut</th></tr>
</mx:bloc id="titre">

<mx:bloc id="padetitre">
    <tr><td colspan='2'>Aucune action</td></tr>
</mx:bloc id="padetitre">


 
  <form name="FoRmXcase" method="POST" action="index.php">
  <mx:bloc id="actions">
    <tr>
    <td><span  mXattribut="onclick:code" class="formxcase_libelle"><mx:text id="libelle" /></span>&nbsp;&nbsp;  </td>
    <td>  <i><mx:text id="statut" /></i>
    
    <mx:bloc id="frem">
    <input name="act_delete"  type="image" src="images/trash.gif"   value="act_delete" mXattribut="onclick:code" />
    </mx:bloc id="frem">
    
    </td>
    </tr>
  </mx:bloc id="actions">
  <tr>
  <td>
   <input name="act_nouveau"  type="image" src="images/modifier.png"   value="act_nouveau"  onmouseover="show(event,'nv_action')" onmouseout="hide('nv_action')" mXattribut="onclick:newact_code" /></td>
   </tr>
   
  <mx:hidden id="hidden" />
  <input name="FormX_ext_goto_" type="hidden" value="">
  </form>
</table>



</td></tr>
<tr height="3px"  style="background:#FFFFFF;"><td></td></tr>
<tr><td>

<table>
  <tr><th colspan='2' >Actions finies</th></tr>



<mx:bloc id="padetitre2">
    <tr><td colspan='2'>Aucune action finie.</td></tr>
</mx:bloc id="padetitre2">


 
  <form name="FoRmXcase" method="POST" action="index.php">
  <mx:bloc id="actions2">
    <tr>
    <td><span  mXattribut="onclick:code" class="formxcase_libelle"><mx:text id="libelle" /></span>&nbsp;&nbsp;  </td>
    <td>  
    
    
    
    </td>
    </tr>
  </mx:bloc id="actions2">
  <tr>
  <td>
   </td>
   </tr>
   
  <mx:hidden id="hidden" />
  <input name="FormX_ext_goto_" type="hidden" value="">
  </form>
</table>
</td></tr>
		</table>
