<form method="post" action="index.php">
  <table>
    <tr>
      <th>Personnel</th>
      <th>Informations</th>
      <th>Destinations</th>
    </tr>
    <tr>
      <td><table class="sansstyle"><tr><td style="text-align: left;"><b>Médecin</b></td><td><mx:select id="selectMedecin" />
                                   <mx:text id="textMedecin" /></td></tr></table></td>
      <td><table class="sansstyle"><tr><td style="text-align: left;"><b>Catégories</b></td><td><mx:select id="selectCategorieRecours" />
                                   <mx:text id="textCategorieRecours" /></td></tr></table></td>
      <td><table class="sansstyle"><tr><td style="text-align: left;"><b>Souhaitée</b></td><td><mx:select id="selectDestinationSouhaitee" />
                                   <mx:text id="textDestinationSouhaitee" /></td></tr></table></td>
    </tr>
    <tr>
      <td><table class="sansstyle"><tr><td style="text-align: left;"><b>I.D.E.</b></td><td><mx:select id="selectIde" />
                                   <mx:text id="textIde" /></td></tr></table></td>
      <td><table class="sansstyle"><tr><td style="text-align: left;"><mx:bloc id="recours"><b>Recours</b></mx:bloc id="recours"></td><td><mx:select id="selectRecours" />
                                   <mx:text id="textRecours" /></td></tr></table></td>
      <td><table class="sansstyle"><tr><td style="text-align: left;"><b>Attendue</b></td><td><mx:select id="selectDestinationAttendue" />
                                   <mx:text id="textDestinationAttendue" /></td></tr></table></td>
    </tr>
    <tr>
	<mx:bloc id="GestionModeAdmission">
        <td><table class="sansstyle"><tr><td style="text-align: left;"><b>Mode adm</b></td><td><mx:select id="selectModeAdmission" />
              <mx:text id="textModeAdmission" /></td></tr></table></td>
	</mx:bloc id="GestionModeAdmission">
      <td><table class="sansstyle"><tr><td style="text-align: left;"><b>Gravité</b></td><td><mx:select id="selectCodeGravite" />
                                   <mx:text id="textCodeGravite" /></td></tr></table></td>
	<mx:bloc id="GestionProvenance">
      		<td><table class="sansstyle"><tr><td style="text-align: left;"><b>Provenance</b></td><td><mx:select id="selectProvenance" />
                                   <mx:text id="textProvenance" /></td></tr></table></td>
        </mx:bloc id="GestionProvenance">
    </tr> 
    <tr>
	<mx:bloc id="GestionAdresseur">
        	<td><table class="sansstyle"><tr><td style="text-align: left;"><b>Adresseur</b></td><td><mx:select id="selectAdresseur" />
                <mx:text id="textAdresseur" /></td></tr></table></td>
      </mx:bloc id="GestionAdresseur">
      <td><table class="sansstyle"><tr><td style="text-align: left;"><b>Salle</b></td><td><mx:select id="selectSalleExamen" />
                                   <mx:text id="textSalleExamen" /></td></tr></table></td>
	<mx:bloc id="GestionCCMU">
        <td><table class="sansstyle"><tr><td style="text-align: left;"><b>CCMU</b></td><td><mx:select id="selectCCMU" />
                                   <mx:text id="textCCMU" /></td></tr></table></td>
      </mx:bloc id="GestionCCMU">
    </tr>
      <tr>
    	<mx:bloc id="GestionTraumato">
        <td><table class="sansstyle"><tr><td style="text-align: left;"><b>Traumato</b></td><td><mx:select id="selectTraumato" />
                                   <mx:text id="textTraumato" /></td></tr></table></td>
    	</mx:bloc id="GestionTraumato">
	<mx:bloc id="GestionGEMSA">
        <td><table class="sansstyle"><tr><td style="text-align: left;"><b>GEMSA</b></td><td><mx:select id="selectGEMSA" />
                                   <mx:text id="textGEMSA" /></td></tr></table></td>
      	</mx:bloc id="GestionGEMSA">
        <td></td>
        <td></td>
      </tr>
  </table>
  <mx:hidden id="hidden" />
</form>
