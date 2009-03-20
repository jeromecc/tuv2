<center><b>Gestion des listes de valeurs</b></center><p>
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
<div id="listesccam">
  <table>
	<tr>
      <mx:bloc id="liste">
      <mx:text id="tr" />
      <mx:text id="td" />
        <form method="post" action="index.php">
        <mx:hidden id="hidden" />
        <mx:text id="nomListe" /> 
	<a mXattribut="href:lienAjouter"><mx:image id="imgAjouter"/></a>
        <a mXattribut="href:lienReparer"><mx:image id="imgReparer"/></a>
        <br />
        <mx:select id="select" />
	</form>
	<mx:text id="form" />
      </td>
      </mx:bloc id="liste">
    </tr>
  </table>
</div>