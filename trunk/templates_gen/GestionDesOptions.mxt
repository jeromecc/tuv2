<div id="options">
  <center>
  <br />
  <b>Gestion des options</b><a mXattribut="href:lienAjouter"><mx:image id="imgAjouter"/></a><br /><br />
      <form method="post" action="index.php">
      <mx:hidden id="hidden" />
      <mx:bloc id="categorie">	
        <table summary="Liste des options">
        <tr><th colspan="2"><mx:text id="titre" /></th><th class="droite"><a mXattribut="href:lienVoir"><mx:image id="imgVoir"/></a></th></tr>
        <div id="listeoptions">
          <mx:bloc id="option">
	    <mx:text id="ligne" />
              <td class="col1"><mx:text id="soustitre" /></td>
	      <td class="col2"><mx:text id="description" /></td>
	      <td class="col3">
	        <mx:bloc id="normal">
	          <mx:text id="valeur" />
	          <a mXattribut="href:lien"><mx:image id="imgModifier"/></a>
                  <a mXattribut="href:lienModifier2"><mx:image id="imgModifier2"/></a>
                  <a mXattribut="href:lienSupprimer"><mx:image id="imgSupprimer"/></a>
                </mx:bloc id="normal">
	            <mx:bloc id="form">
		          <mx:text id="valeur" />
                  <mx:hidden id="hidden" />
                  <input type="image" value="1" src="images/OOk.gif" name="Ok" alt="Valider" style="border: 0px; background-color: transparent;" />		
                  <input type="image" value="1" src="images/annuler2.gif" name="Annuler" alt="Annuler" style="border: 0px; background-color: transparent;" />
	            </mx:bloc id="form">
              </td>
	    </tr>
          </mx:bloc id="option">	  
	</div>
	</table>
    </mx:bloc id="categorie">
    </form>
    <br />
  </center>
</div>
<br />
