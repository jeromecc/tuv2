<div>
<style>
.boutton {
	font-family: Verdana, Arial, Helvetica, sans-serif; 
	font-size: 10px;
	border: #11AAEE solid 1px;
	background-color : #EEEEEE ;
}
.police {
font-family: Verdana, Arial, Helvetica, sans-serif; 
	font-size: 10px;
}
</style>
<h4>GESTION DES LISTES GENERALES</h4>
<mx:bloc id="ajouterListe">
  <hr size="1" style="background: #000000;" />
  <form method="post" action="index.php">
    <span>Ajouter la liste </span>
    <input type='txt' name='ajouter_liste' class="boutton" />
    <input type="submit" value="Ok" class="boutton" />
    <mx:hidden id="hidden2" />
  </form>
</mx:bloc id="ajouterListe">
<hr size="1" style="background: #000000;" />
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
<mx:text id="formAjouter" />
<div id="listesgenerales">
  <table summary="Liste des listes... ^^">
    <tr>
      <mx:bloc id="liste">
        <mx:text id="tr" />
        <mx:text id="td" />
        <mx:text id="formDeb" />
        <mx:hidden id="hidden" />
        <mx:text id="ancreListe" /> 
        <mx:text id="nomListe" /> 
	    <a mXattribut="href:lienAjouter"><mx:image id="imgAjouter" /></img></a>
        <a mXattribut="href:lienReparer"><mx:image id="imgReparer" /></img></a>
        <br />
        <mx:select id="select" />
	    </form>
	    <mx:text id="form" />
        </td>
      </mx:bloc id="liste">
    </tr>
  </table>
</div>
</div>