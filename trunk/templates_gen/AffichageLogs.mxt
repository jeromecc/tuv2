<div id="affichageLogs">
  <center>
  <h4>Affichage des logs</h4>
  <br />
  <form method="get" action="index.php">  
    <table summary="Filtres">
      <tr>
        <td class="titre">Type :</td>
        <td><mx:select id="listeTypes" class="form" /></td>
        <td class="titre">Adresse IP :</td>
        <td><mx:select id="listeIP" class="form" /></td>
        <td>ou <mx:formField id="filtreIP" /></td>
      </tr>
      <tr>
        <td class="titre">Description :</td>
        <td><mx:formField id="filtreDescription" /></td>
        <td class="titre">Utilisateur :</td>
        <td><mx:select id="listeUtilisateurs" class="form" /></td>
        <td>ou <mx:formField id="filtreUtilisateur" /></td>
      </tr>
      <tr>
        <td class="titre">Cible :</td>
        <td><mx:formField id="filtreCible" class="form" /></td>
        <td class="titre">Date entre :</td>
        <td><mx:select id="dateMin"class="form"  /></td>
        <td class="droite">et <mx:select id="dateMax" class="form" /></td>
      </tr>
    </table>
    <br />
    Nombre de logs à afficher : <mx:select id="nbResultats" /> <input type="submit" name="Rechercher" value="Rechercher" /> 
    <mx:hidden id="hidden" />
  </form>
  <br />
  <br />
  <br />
  <mx:text id="resultats" /> 
  <br />
  </center>
</div>