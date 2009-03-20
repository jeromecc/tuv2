<iframe style="border: 0px; display: block; position: absolute; 
                 left: 3px; top: 80px; width: 980px; height: 400px;" ></iframe>
<div id="detaildiagsactes">
<form method="post" action="index.php">
  <table>
	<tr>
		<th colspan="2"><h3><mx:text id="titreFormulaire"/></h3></th>
	</tr>
	<tr>
		<td align="right"><b>Patient :</b></td><td><mx:text id="nomPatient"/></td>
	</tr>
	<tr>
		<td align="right"><b>Localisation :</b></td><td><mx:text id="sallePatient"/></td>
	</tr>
	<tr>
  	<td align="right"><b>Lésions multiples :</b></td>
    <td ><mx:bloc id="lesionMultiple">
    	<mx:checker id="btn" style="border:0px; background-color: transparent;"/><mx:text id="libelle"/>
    	</mx:bloc id="lesionMultiple">
    </td>
  </tr>
	</table><p>
	
	
		<mx:hidden id="hidden"/>
    
		<mx:bloc id="informations">
		<div id="informations">
		  <mx:text id="infos" />
		</div>
		</mx:bloc id="informations">
		<mx:bloc id="erreurs">
		<div id="erreurs">
		  <mx:text id="errs" />
		</div>
		</mx:bloc id="erreurs">
		
		<table>
		<tr><th><mx:text id="titreDispo"/></th>
			<td></td>
			<th><mx:text id="titreAffecte"/></th></tr>
	   	<tr><td>
				<table>
        <tr>
					<td align="right"><mx:text id="titreSelection0"/></td>
					<td><mx:select id="idListeSelection0"/></td>
				</tr>
				<tr>
					<td align="right"><mx:text id="titreSelection1"/></td>
					<td><mx:select id="idListeSelection1"/></td>
				</tr>
				<mx:bloc id="actesBlocGauche">
				<!-- DBDEB1 : Le select est changé en input hidden
				<tr>
					<td align="right">Anesthésiste</td>
					<td><mx:select id="anesthesiste"/></td>
				</tr>
				-->
				<mx:hidden id="anesthesiste" />
				<!-- DBFIN1 -->
        <tr>
					<td align="right"><mx:text id="titreSelection2"/></td>
					<td><mx:select id="idListeSelection2"/></td>
				</tr>
        <tr>
					<td></td>
					<td valign="top"><mx:select id="listeGauche"/></td>
				</tr>
				</mx:bloc id="actesBlocGauche">
				<mx:bloc id="actesCORA">
        <tr><td>&nbsp;</td><td></td></tr>
				<tr><td>&nbsp;</td><td></td></tr>
				<tr><td></td><td><center><b>Appel de CORA Recueil</b> :<br><input name="cora" type="image" value="cora" src="images/cora.gif" style="border: 0px; background-color: transparent;"></center></td></tr>
				</mx:bloc id="actesCORA">
        </table>
			</td>
			
			<td align="center" valign="top">
				<table width="100%">
				<tr align="center"><td>
					<mx:bloc id="flDroite">
				    <input name="aDroite" type="image" value="aDroite" src="images/valider2.gif" style="border: 0px; background-color: transparent;">
				    </mx:bloc id="flDroite">
				</td></tr>
				<tr align="center"><td>
					<mx:bloc id="flSortir">
				    <input name="sortir" type="image" value="sortir" src="images/ValiderQuitter.gif" style="border: 0px; background-color: transparent;">
				    </mx:bloc id="flSortir">
				</td></tr>
				<tr align="center"><td>
					<a mXattribut="href:lienQuitter">
					<mx:image id="imgQuitter"/>
				</td></tr>
				</table>
		    </td>
			
			<td>
				<table width="100%" class="actes">
				<mx:bloc id="diagnostics">
				<tr class="actes">
					<th class="actes" width="15%">Date</th>
          <th class="actes" width="25%">Code diagnostic</th>
					<th class="actes">Libellé diagnostic</th>
					<th class="actes" width="30%">Action</th>
				</tr>
				
				<mx:bloc id="aucunDiag">
				<tr>
					<td align="center" colspan="4">
						<div id="erreurs_ccam">Aucun diagnostic saisi</div>
					</td>
				</tr>
				</mx:bloc id="aucunDiag">
				
				<mx:bloc id="diagCotes">
				<tr class="actes">
				  <td align="center" valign="top"><mx:text id="dateActe"/></td>
					<td align="center" valign="top"><mx:text id="codeActe"/></td>
					<td valign="top"><mx:text id="libActe"/></td>
					<td align="center" valign="top">
						<mx:bloc id="action">
	   					<input name="actualiserListe" type="image" 
							value="<mx:text id="codeActe"/>" src="images/ActuListe.gif" style="border: 0px; background-color: transparent;" />
						<input type="submit" name="supprimerActe" value="<mx:text id="codeActe"/>" style="background-image:url(images/Supprimer.gif); border:0px; background-color:transparent; color: red; width: 52px; font-size: 0px; height: 38px; cursor: hand; cursor: pointer;">
						</mx:bloc id="action">
					</td>
				</tr>
				</mx:bloc id="diagCotes">
				</table>
				</mx:bloc id="diagnostics">
				<p>
				
				<mx:bloc id="actesBlocDroite">
				<table width="100%" class="actes">
				<tr class="actes">
					<th class="actes" width="15%">Date</th>
          <th class="actes" width="25%">Code acte</th>
					<th class="actes">Libellé acte</th>
					<th class="actes">Médecin</th>
					<th class="actes" width="30%">Action</th>
				</tr>
				
				<mx:bloc id="aucunActe">
				<tr>
					<td align="center" colspan="4">
						<div id="erreurs_ccam">Aucun acte saisi</div>
					</td>
				</tr>
				</mx:bloc id="aucunActe">
				
				<mx:bloc id="actesCotes">
				<tr class="actes">
					<td align="center" valign="top"><mx:text id="dateActe"/></td>
          <td align="center" valign="top"><mx:text id="codeActe"/></td>
					<td valign="top"><mx:text id="libActe"/>
					<td valign="top"><mx:text id="medecin"/></td>
						<!--- <div id="complementActe">
							<ul>
							<mx:text id="complementActeLibQte"/> <mx:text id="complementActeQte"/>
							<mx:text id="complementActeLibPeriod"/>
								<mx:text id="complementActePeriod"/>
							</ul>
						</div> --->
					</td>
					<td align="center" valign="top">
						<!--- <mx:bloc id="actionModif">
						<input name="modifierActe" type="image" 
						 	value="<mx:text id="codeActe"/>" src="images/Complement.gif" style="border: 0px; background-color: transparent;">
	   					</mx:bloc id="actionModif"> --->
						
						<!---<mx:formField id="supprimerActe"/>--->
            <mx:bloc id="actionSuppr">
            <input type="submit" name="supprimerActe" value="<mx:text id="codeActe"/>" style="background-image:url(images/Supprimer.gif); border:0px; background-color:transparent; color: red; width: 52px; font-size: 0px; height: 38px; cursor: hand; cursor: pointer;">
						  						
						</mx:bloc id="actionSuppr"> 
					</td>
				</tr>
				</mx:bloc id="actesCotes">
				</table>
				</mx:bloc id="actesBlocDroite"><p>
				
				<!--- <mx:bloc id="anesthesie">
				<table width="100%" class="actes">
				<tr class="actes">
					<th class="actes">Anesthésie</th>
					<th class="actes" width="30%">Action</th>
				</tr>
				<tr>
					<td>Anesthésiste : <mx:text id="nomanesthesiste"/></td>
				</tr>
				
				<mx:bloc id="ligneModificateur">
				<tr>
					<td><mx:text id="libModificateur"/></td>
					<th><mx:checker id="CCModificateur"/></th>
				</tr>
				</mx:bloc id="ligneModificateur">
				
				<tr>
				 	<td colspan="2" align="center">
						<mx:formField id="imgOK" style="border: 0px; background-color: transparent;"/> 
						<mx:formField id="imgAnnul" style="border: 0px; background-color: transparent;"/>
					</td>
				</tr>
				</table>
				</mx:bloc id="anesthesie"> --->
				
			</td>
		</tr>
		</table>
	</form>
	<mx:text id="fenetreFermerCora"/>
	</center>
</div>
