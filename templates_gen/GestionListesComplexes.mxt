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
<mx:text id="formItems" />
<div id="listes">
  <table summary="Mise en forme">
    <tr>
      <td>
        <mx:bloc id="listeListes">
          <form method="post" action="index.php">
          <mx:hidden id="hidden" />
          <mx:text id="nomListe" /> 
	  <a mXattribut="href:lienAjouter"><mx:image id="imgAjouter"/></a> 
          <a mXattribut="href:lienReparer"><mx:image id="imgReparer"/></a>
          <br />
          <mx:select id="select" />
	  </form>
  	  <mx:text id="form" />
        </mx:bloc id="listeListes">
      </td>
      <td>
        <mx:bloc id="listeItems">
          <form method="post" action="index.php">
          <mx:hidden id="hidden" />
          <mx:text id="nomListe" /> 
	  <a mXattribut="href:lienAjouter"><mx:image id="imgAjouter"/></a>
          <a mXattribut="href:lienReparer"><mx:image id="imgReparer"/></a>
          <br />
          <mx:select id="select" />
	  </form>
  	  <mx:text id="form" />
        </mx:bloc id="listeItems">
      </td>
    </tr>
  </table>
<br />
</div>