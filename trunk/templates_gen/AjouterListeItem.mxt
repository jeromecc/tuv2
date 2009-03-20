<iframe style="border: 0px; display: block; position: absolute; 
                 left: 300px; top: 450px; width: 430px; height: 236px;" ></iframe>
<div id="ajouteritem" style="background-color: #E4F7FD; border: 2px solid #2D729D;
-moz-border-radius: 6px; text-align: center; padding: 13px; z-index:
100; position: absolute; left: 300px; top: 450px; width: 400px; height: 206px;">
  <br />
  Ajouter un item à la liste "<mx:text id="nomListe" />" :
  <br /><br />
  <mx:text id="formDeb" />
    <mx:hidden id="hidden" />
    <table summary="Mise en page">
    <tr><td><mx:text id="nomItem" /></td><td><mx:formField id="nomItemF"  style="width:200px;" /></td></tr>
    <tr><td><mx:text id="placer" /></td><td><mx:select id="placerF" style="width:200px;" /></td></tr>
    <mx:bloc id="formType">
      <tr><td><mx:text id="type" /></td><td><mx:select id="typeF" style="width:200px;" /></td></tr>
    </mx:bloc id="formType">
    <mx:bloc id="formLibre">
      <tr><td><mx:text id="libre" /></td><td><mx:formField id="libreF" style="width:200px;" /></td></tr>
    </mx:bloc id="formLibre">
    <mx:bloc id="speDestConf">
      <tr><td style="text-align:right">Dest. PMSI :</td><td><mx:select id="destPMSI" style="width:200px;" /></td></tr>
      <tr><td style="text-align:right">Orientation :</td><td><mx:select id="Orientation" style="width:200px;" /></td></tr>
    </mx:bloc id="speDestConf">
    </table>
    <br />
    <table summary="Actions" class="boutons">
      <tr>
	<td>
	  <input name="Valider" type="image" value="Valider" src="images/Ok.gif" style="border: 0px; background-color: transparent;"/>
	</td>
	<td>
	  <input name="Annuler" type="image" value="Annuler" src="images/annuler2.gif" style="border: 0px; background-color: transparent;"/>
	</td>
      </tr>
    </table>
    <br />
  </form>
</div>
