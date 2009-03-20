<iframe style="border: 0px; display: block; position: absolute; 
                 left: 300px; top: 450px; width: 430px; height: 226px;" ></iframe>
<div id="modifieritem" style="background-color: #E4F7FD; border: 2px solid #2D729D;
-moz-border-radius: 6px; text-align: center; padding: 13px; z-index:
100; position: absolute; left: 300px; top: 450px; width: 400px; height: 206px;">
  <br />
  Modification de l'item "<mx:text id="oldNomItem" />" de la liste "<mx:text id="nomListe" />" :
  <br /><br />
  <mx:text id="formDeb" />
    <mx:hidden id="hidden" />
    <table summary="Modification d'un item">
    <tr><td style="text-align:right"><mx:text id="nomItem" /></td><td><mx:formField id="nomItemF" style="width:200px;" /></td></tr>
    <tr><td style="text-align:right"><mx:text id="placer" /></td><td><mx:select id="placerF" style="width:200px;" /></td></tr>
    <mx:bloc id="formType">
      <tr><td style="text-align:right"><mx:text id="type" /></td><td><mx:select id="typeF" style="width:200px;" /></td></tr>
    </mx:bloc id="formType">
    <mx:bloc id="formLibre">
      <tr><td style="text-align:right"><mx:text id="libre" /></td><td><mx:formField id="libreF" style="width:200px;" /></td></tr>
    </mx:bloc id="formLibre">
    <mx:bloc id="speDestConf">
      <tr><td style="text-align:right">Dest. PMSI :</td><td><mx:select id="destPMSI" style="width:200px;" /></td></tr>
      <tr><td style="text-align:right">Orientation :</td><td><mx:select id="Orientation" style="width:200px;" /></td></tr>
    </mx:bloc id="speDestConf">
    </table>
    <br />
    <table summary="Actions">
      <tr>
	<td>
	  <input name="Modifier"  type="image" src="images/modifier2.gif"   value="Modifier" style="border: 0px; background-color: transparent;"/>
	</td>
	<td class="boutons">
          <input name="Annuler"  type="image" src="images/annuler2.gif" value="Annuler" style="border: 0px; background-color: transparent;"/>
        </td>
        <mx:bloc id="supprimer">
	  <td>
	    <input name="Supprimer"  type="image" src="images/Supprimer.gif" value="Supprimer" style="border: 0px; background-color: transparent;"/>
          </td>
	</mx:bloc id="supprimer">
      </tr>
    </table>
    <br />
    <mx:select id="select" />
  </form>
</div>
