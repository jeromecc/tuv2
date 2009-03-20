<iframe style="border: 0px; display: block; position: absolute; 
                 left: 9px; top: 56px; width: 950px; height: 500px;" id="documentsimprIF"></iframe>
<div id="documentsimpr">
  <center>
  <div id="documentsimprtitre">
    <table summary="Titre Documents">
      <tr><th>Choix des documents à imprimer</th><th class="droite"><br /></th></tr>
    </table>
  </div>
  <form method="post" action="index.php" target="_blank">
    <table summary="Liste documents">
      <mx:bloc id="categorie">
        <tr><td colspan="1" align="center" class="categorie"><mx:text id="titre" /></td>
        <mx:bloc id="documents">
           <mx:text id="tdo" /><mx:checker id="c" style="border:0px; background-color: transparent;"/><mx:text id="doc" /><mx:text id="tdf" />
        </mx:bloc id="documents">
      </mx:bloc id="categorie">
    </table>
    <table summary="Actions">
      <tr><td style="text-align: center; border:0px; background-color: transparent;">
        <input name="Imprimer" type="image" src="images/imprimer2.gif" value="Imprimer" style="border: 0px; background-color: transparent;"/>
        <mx:hidden id="hidden1" />
        </form>
	<a href="#"><img name="Fermer" alt="Fermer" src="images/fermer2.gif" style="border: 0px; background-color: transparent;"/ onClick="cache('documentsimpr');cache('documentsimprIF');" /></a>
      </td></tr>
    </table>
  </center>
</div>
