<div id="detaildiagsactes">
	<table>
	<tr>
		<th><h3><mx:text id="titreFormulaire"/></h3></th>
		<td align="right">
		</td>
	</tr>
	</table>
	
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
					<td align="right"><mx:text id="titreSelection0"/></td>
					<td><mx:select id="idListeSelection0"/></td>
				</tr>
				<tr>
					<td align="right"><mx:text id="titreSelection1"/></td>
					<td><mx:select id="idListeSelection1"/></td>
				</tr>
				<mx:bloc id="actesBlocGauche">
				<tr>
					<td align="right"><mx:text id="titreSelection2"/></td>
					<td><mx:select id="idListeSelection2"/></td>
				</tr>
				<tr>
					<td></td>
					<td valign="top"><mx:select id="listeGauche"/></td>
				</tr>
				<tr>
					<td>Anesth�siste</td>
					<td><mx:select id="anesthesiste"/></td>
				</tr>
				</mx:bloc id="actesBlocGauche">
				</table>
			</td>
			
			<td align="center" valign="top">
				<table width="100%">
				<tr align="center"><td>
					<mx:bloc id="flDroite">
				    <input name="aDroite" type="image" value="aDroite" src="images/valider2.gif" style="border: 0px; background-color: #F3FFFF;">
				    </mx:bloc id="flDroite">
				</td></tr>
				<tr align="center"><td>
					<mx:bloc id="flSortir">
				    <input name="sortir" type="image" value="sortir" src="images/ValiderQuitter.gif" style="border: 0px; background-color: #F3FFFF;">
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
					<th class="actes" width="25%">Code diagnostic</th>
					<th class="actes">Libell� diagnostic</th>
					<th class="actes" width="30%">Action</th>
				</tr>
				
				<mx:bloc id="aucunDiag">
				<tr>
					<td align="center" colspan="3">
						<div id="erreurs_ccam">Aucun diagnostic saisi</div>
					</td>
				</tr>
				</mx:bloc id="aucunDiag">
				
				<mx:bloc id="diagCotes">
				<tr class="actes">
					<td align="center" valign="top">
						<mx:text id="codeActe"/>
					</td>
					<td valign="top">
						<mx:text id="libActe"/>
					</td>
					<td align="center" valign="top">
						<mx:bloc id="action">
	   					<input name="actualiserListe" type="image" 
							value="<mx:text id="codeActe"/>" src="images/ActuListe.gif" style="border: 0px; background-color: #F3FFFF;">
						<input name="supprimerActe" type="image" 
							value="<mx:text id="codeActe"/>" src="images/Supprimer.gif" style="border: 0px; background-color: #F3FFFF;">
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
					<th class="actes" width="25%">Code acte</th>
					<th class="actes">Libell� acte</th>
					<th class="actes" width="30%">Action</th>
				</tr>
				
				<mx:bloc id="aucunActe">
				<tr>
					<td align="center" colspan="3">
						<div id="erreurs_ccam">Aucun acte saisi</div>
					</td>
				</tr>
				</mx:bloc id="aucunActe">
				
				<mx:bloc id="actesCotes">
				<tr class="actes">
					<td align="center" valign="top"><mx:text id="codeActe"/></td>
					<td valign="top"><mx:text id="libActe"/>
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
						 	value="<mx:text id="codeActe"/>" src="images/Complement.gif" style="border: 0px; background-color: #F3FFFF;">
	   					</mx:bloc id="actionModif"> --->
						
						<mx:bloc id="actionSuppr">
						<input name="supprimerActe" type="image" 
							value="<mx:text id="codeActe"/>" src="images/Supprimer.gif" style="border: 0px; background-color: #F3FFFF;">
						</mx:bloc id="actionSuppr">
					</td>
				</tr>
				</mx:bloc id="actesCotes">
				</table>
				</mx:bloc id="actesBlocDroite"><p>
				
				<mx:bloc id="anesthesie">
				<table width="100%" class="actes">
				<tr class="actes">
					<th class="actes">Anesth�sie</th>
					<th class="actes" width="30%">Action</th>
				</tr>
				<tr>
					<td>Anesth�siste : <mx:text id="nomanesthesiste"/></td>
				</tr>
				
				<mx:bloc id="ligneModificateur">
				<tr>
					<td><mx:text id="libModificateur"/></td>
					<th><mx:checker id="CCModificateur"/></th>
				</tr>
				</mx:bloc id="ligneModificateur">
				
				<tr>
				 	<td colspan="2" align="center">
						<mx:formField id="imgOK" style="border: 0px; background-color: #F3FFFF;" /> 
						<mx:formField id="imgAnnul" style="border: 0px; background-color: #F3FFFF;" />
					</td>
				</tr>
				</table>
				</mx:bloc id="anesthesie">
				
			</td>
		</tr>
		</table>
	</form>
	</center>
	<!--[if lte IE 6.5]><iframe></iframe><![endif]-->
</div>
