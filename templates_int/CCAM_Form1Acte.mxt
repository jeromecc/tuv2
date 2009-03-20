<div id="form1Acte">
<mx:hidden id="hidden2"/>
<mx:hidden id="hidden3"/>
<table class="form1acte">
  <tr>
    <td colspan="2" align=center><b><mx:text id="titreEnCours"/></b></td>
  </tr>
  <tr>
    <td><mx:text id="titreCodeActe"/></th>
    <td><mx:text id="codeActe"/></th>
  </tr>
  <tr>
    <td valign="top"><mx:text id="titreLibActe"/></td>
    <td><mx:formField id="libActe"/>
        <mx:text id="libVisuActe"/>
        <mx:text id="libActe2"/></td>
  </tr>
  <tr>
    <td><mx:text id="titreQte"/></td>
    <td><mx:formField id="qte"/></td>
  </tr>
  <tr>
    <td><mx:text id="titrePeriodicite"/></td>
    <td><mx:select id="periodicite"/></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><mx:text id="confirmSuppr"/></td>
  </tr>
 <mx:bloc id="NGAP">
  <tr>
    <td colspan="2" align=center><mx:text id="formNGAP"/></td>
  </tr>
  </mx:bloc id="NGAP">
    <td colspan="2" align=center><mx:bloc id="validerActe">
        	<input name="OK" type="image" value="OK" src="images/Ok.gif" style="border: 0px; background-color: transparent;">
        </mx:bloc id="validerActe">
    	<mx:bloc id="annulerActe">
        	<input name="annuler" type="image" value="annuler" src="images/annuler2.gif" style="border: 0px; background-color: transparent;">
        </mx:bloc id="annulerActe"></td>
  </tr>
</table>
</div>
