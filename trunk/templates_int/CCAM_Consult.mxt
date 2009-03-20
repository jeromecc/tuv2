<iframe style="border: 0px; display: block; position: absolute; 
                 left: 3px; top: 80px; width: 980px; height: 400px;" ></iframe>
<div id="detaildiagsactes">
	<table>
	<tr>
		<th><h3>Consultations spécialisées</h3></th>
	</tr>
	<tr>
		<td align="center"><b>Patient : </b><mx:text id="infosPatient"/></td>
	</tr>
	</table><p>
	
	<form method="post" action="index.php">
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
					<td><mx:select id="idListeSelection1"/></td>
				</tr>
				<mx:bloc id="consultBlocGauche">
				<tr>
					<td valign="top"><mx:select id="listeGauche"/></td>
				</tr>
				</mx:bloc id="consultBlocGauche">
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
				<mx:bloc id="consultBlocDroite">
				<table width="100%" class="actes">
				<tr class="actes">
					<th class="actes" width="25%">Spécialité</th>
					<th class="actes">Consultant</th>
					<th class="actes" width="30%">Action</th>
				</tr>
				
				<mx:bloc id="aucunConsult">
				<tr>
					<td align="center" colspan="3">
						Aucune consultation spécialisée
					</td>
				</tr>
				</mx:bloc id="aucunConsult">
				
				<mx:bloc id="consultCotes">
				<tr class="actes">
					<td align="center" valign="top"><mx:text id="speConsult"/></td>
					<td align="center" valign="top"><mx:text id="nomConsultant"/></td>
					<td align="center" valign="top">
						<mx:bloc id="actionSuppr">
						  <input type="submit" name="supprimerConsult" value="<mx:text id="idConsultSuppr"/>" style="background-image:url(images/Supprimer.gif); border:0px; background-color:transparent; color: red; width: 52px; font-size: 0px; height: 38px; cursor: hand; cursor: pointer;">
						</mx:bloc id="actionSuppr">
					</td>
				</tr>
				</mx:bloc id="consultCotes">
				</table>
				</mx:bloc id="consultBlocDroite">
			</td>
		</tr>
		</table>
	</form>
	</center>
</div>
