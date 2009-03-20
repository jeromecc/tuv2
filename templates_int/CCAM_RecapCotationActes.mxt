<div id="recapcotation">
	<center>
	<form method="post" action="index.php">
		<mx:hidden id="hidden"/>
		<mx:hidden id="hidden2"/>
    
		<mx:bloc id="afficheActesDiag">
		<table width=100%>
			<tr>
				<th><mx:text id="titreFormulaire"/>
					
				</th>
			    <td class="hover"><mx:formField id="DetailDiagsActes" style="border: 0px; background-color: transparent;"/></td>
			</tr>
		</table>
		<div style="height:5px;"></div>

		<table width=100%>
			<tr>
				<th width="20%">Code diagnostic</th>
				<th>Libellé diagnostic</th>
			</tr>
			
			<mx:bloc id="aucunDiag">
			<tr>
				<td align="center" colspan="2">
					<div id="erreurs_ccam">Aucun diagnostic saisi</div>
				</td>
			</tr>
			</mx:bloc id="aucunDiag">
			
			<mx:bloc id="diagCotes">
			<tr>
				<td align="center" valign="top"><mx:text id="codeActe"/></td>
				<td valign="top"><mx:text id="libActe"/></td>
			</tr>
			</mx:bloc id="diagCotes">
		</table><div style="height:5px;"></div>
		<mx:bloc id="afficheActe">
		<table width=100%>
			<tr>
				<th width="20%">Code acte</th>
				<th>Libellé acte</th>
				<th>Médecin</th>
			</tr>
			
			<mx:bloc id="aucunActe">
			<tr>
				<td align="center" colspan="2">
					<div id="erreurs_ccam">Aucun acte saisi</div>
				</td>
			</tr>
			</mx:bloc id="aucunActe">
			
			<mx:bloc id="actesCotes">
			<tr>
				<td align="center" valign="top"><mx:text id="codeActe"/></td>
				<td valign="top"><mx:text id="libActe"/>
					<div id="complementActe">
						<ul>
						<mx:text id="complementActeLibQte"/> <mx:text id="complementActeQte"/>
						<br><mx:text id="complementActeLibPeriod"/>
							<mx:text id="complementActePeriod"/>
						</ul>
					</div>
				</td>
				<td valign="top"><mx:text id="medecin"/></td>
			</tr>
			</mx:bloc id="actesCotes">
		</table>
		</mx:bloc id="afficheActe">
		</mx:bloc id="afficheActesDiag">
		<div style="height:5px;"></div>
		
		<mx:bloc id="afficheConsult">
		<table width=100%>
			<tr>
				<th>Consultations spécialisées</th>
			    <td class="hover"><mx:formField id="DetailConsult" style="border: 0px; background-color: transparent;" /></td>
			</tr>
		</table>
		<div style="height:5px;"></div>
		<table width=100%>
			<tr>
				<th>Spécialité</th>
				<th>Consultant</th>
			</tr>
			
			<mx:bloc id="aucunActe">
			<tr>
				<td align="center" colspan="2">
					Aucune consultation spécialisée
				</td>
			</tr>
			</mx:bloc id="aucunActe">
			
			<mx:bloc id="actesCotes">
			<tr>
				<td align="center" valign="top"><mx:text id="specialite"/></td>
				<td align="center" valign="top"><mx:text id="nomConsultant"/>
				</td>
			</tr>
			</mx:bloc id="actesCotes">
		</table>
		</mx:bloc id="afficheConsult">
		
	</form>
	</center>
</div>
