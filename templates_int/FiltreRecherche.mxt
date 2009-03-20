<div id="filtrerecherche">
  <center>
    <h4>Filtre de recherche</h4>
    <form method="post" action="index.php">
      <mx:hidden id="hidden" />
      <table>
	<tr>
	  <td class="titreLP" onmouseover="return overlib('<!--[if lte IE 6.5]><iframe></iframe><![endif]-->&nbsp;&nbsp;En fonction de l\'appelation dans votre &eacute;tablissement, il s\'agit de l\'identifiant suivant : ILP, IPP, IDU...')" onmouseout="return nd();">Identifiant du patient : </td><td><mx:formField id="valeurILP" class="LP" /></td>
	  <td class="titreLP" onmouseover="return overlib('<!--[if lte IE 6.5]><iframe></iframe><![endif]-->&nbsp;&nbsp;Recherche sur le num&eacute;ro de s&eacute;jour du patient')" onmouseout="return nd();">Num&eacute;ro de s&eacute;jour : </td><td><mx:formField id="valeurSej" class="LP" /></td>
	  <td class="titreLP" onmouseover="return overlib('<!--[if lte IE 6.5]><iframe></iframe><![endif]-->&nbsp;&nbsp;Ici, la recherche se fait sur l\'identifiant interne du terminal. Ce champs de recherche est destin&eacute; au service informatique.')" onmouseout="return nd();">Identifiant terminal : </td><td><mx:formField id="valeurIDP" class="LP" /></td>
	</tr>
	<tr>
	  <td class="titreLP" onmouseover="return overlib('<!--[if lte IE 6.5]><iframe></iframe><![endif]-->&nbsp;&nbsp;Recherche sur le nom')" onmouseout="return nd();">Nom : </td><td><mx:formField id="valeurNom" class="LP" /></td>
	  <td class="titreLP" onmouseover="return overlib('<!--[if lte IE 6.5]><iframe></iframe><![endif]-->&nbsp;&nbsp;Recherche sur le prenom')" onmouseout="return nd();">Pr&eacute;nom : </td><td><mx:formField id="valeurPrenom" class="LP" /></td>
	  <td class="titreLP" onmouseover="return overlib('<!--[if lte IE 6.5]><iframe></iframe><![endif]-->&nbsp;&nbsp;Recherche sur la date de sortie')" onmouseout="return nd();">Date de sortie (jj-mm-aaaa) :</td><td> <mx:formField id="valeurDate" class="LP" /></td>
	</tr>
  <tr>
    <td class="titreLP" onmouseover="return overlib('<!--[if lte IE 6.5]><iframe></iframe><![endif]-->&nbsp;&nbsp;Recherche sur le nom du m&eacute;decin')" onmouseout="return nd();">M&eacute;decin : </td><td><mx:select id="valeurMedecin" class="LP" /></td>
	  <td class="titreLP" onmouseover="return overlib('<!--[if lte IE 6.5]><iframe></iframe><![endif]-->&nbsp;&nbsp;Recherche sur le nom de l\'IDE')" onmouseout="return nd();">I.D.E. : </td><td><mx:select id="valeurIDE" class="LP" /></td>
	  <td class="titreLP" onmouseover="return overlib('<!--[if lte IE 6.5]><iframe></iframe><![endif]-->&nbsp;&nbsp;Recherche sur la date d\'admission')" onmouseout="return nd();">Date d'admission (jj-mm-aaaa) : </td><td><mx:formField id="valeurDateAdm" class="LP" /></td>
	</tr>
  <tr>
    <td class="titreLP" onmouseover="return overlib('<!--[if lte IE 6.5]><iframe></iframe><![endif]-->&nbsp;&nbsp;Recherche sur la destination confirmée')" onmouseout="return nd();">Dest. confirmée : </td><td><mx:select id="valeurDestConf" class="LP" /></td>
    <td class="titreLP" onmouseover="return overlib('<!--[if lte IE 6.5]><iframe></iframe><![endif]-->&nbsp;&nbsp;Recherche sur le formulaire')" onmouseout="return nd();">Formulaire : </td><td><mx:select id="valeurFormulaire" class="LP" /></td>
    <td></td>
  </tr>
      </table>
      <br/>
      <input type="submit" value="Appliquer" name="Appliquer" />
      <mx:text id="message" />
    </form>
  </center>
  <br />
</div>
