<div id="document">
  <center>
    <mx:bloc id="modText">
      <h4>Edition du document "<mx:text id="nomDocument" />" :</h4>
    </mx:bloc id="modText">
    <mx:bloc id="addText">
      <h4>Création d'un nouveau document</h4>
    </mx:bloc id="addText">
    <form method="post" action="index.php">
      <mx:hidden id="hidden" />
      <table summary="Formulaire document">
        <tr>
          <td class="col1">
            Catégorie du document :
          </td>
          <td style="text-align: left;">
            <mx:select id="listeCategories" />
          </td>
        </tr>
        <tr>
          <td class="col1">
            Nouvelle catégorie :
          </td>
          <td style="text-align: left;">
            <mx:formField id="newCategorie"  class="colorinput"/>
          </td>
        </tr>
        <tr>
          <td class="col1">
            Nom du document :
          </td>
          <td style="text-align: left;">
            <mx:formField id="nomDoc" class="colorinput"/>
          </td>
        </tr>
        <tr>
          <td class="col1">
            Contenu :
          </td>
          <td>
            <textarea name="contenu" style="font-size:12px; width: 700px; height: 300px;"><mx:text id="contenu" /></textarea>
          </td>
        </tr>
      </table>
      <br />
      <table summary="Actions">
        <tr>
          <td>
            <input name="Annuler"  type="image" src="images/annuler2.gif" value="Annuler" title="Annuler" style="border: 0px; background-color: transparent;" />
          </td>
          <td>
            <mx:bloc id="Modifier">
              <input name="Modifier"  type="image" src="images/modifier2.gif"   value="Modifier" title="Modifier" style="border: 0px; background-color: transparent;" />
            </mx:bloc id="Modifier">
          </td>
          <td>
            <mx:bloc id="Supprimer">
              <input name="Supprimer"  type="image" src="images/Supprimer.gif"  value="Supprimer" title="Supprimer" style="border: 0px; background-color: transparent;" />
            </mx:bloc id="Supprimer">
          </td>
          <td>
            <mx:bloc id="Ajouter">
              <input name="Ajouter"  type="image" src="images/ajouter2.gif"  value="Ajouter" title="Ajouter" style="border: 0px; background-color: transparent;" />
            </mx:bloc id="Ajouter">
          </td>
      </table>
    </form>
  </center>
</div>
<br />



<div align="center">Aide...
  <table summary="Aide" width="80%" border="1" cellpadding="2" cellspacing="0">
    <tr>
      <th background="images/fondTab.jpg" colspan="6">Donn&eacute;es du patient</th>
    </tr>
    <tr>
      <td>Nom Marital</td>
      <td>&lt;**NMA&gt;</td>
      <td>IPP</td>
      <td>&lt;**IPP&gt;</td>
      <td>Unit&eacute; fonctionnelle</td>
      <td>&lt;**UF&gt;</td>
    </tr>
    <tr>
      <td>Pr&eacute;nom</td>
      <td>&lt;**PRE&gt;</td>
      <td>S&eacute;jour</td>
      <td>&lt;**SEJ&gt;</td>
      <td>Cat&eacute;gorie de recours</td>
      <td>&lt;**CRE&gt;</td>
    </tr>
    <tr>
      <td>Date de naissance </td>
      <td>&lt;**DNA&gt;</td>
      <td>Adresse</td>
      <td>&lt;**ADD&gt;</td>
      <td>Motif de recours</td>
      <td>&lt;**MRE&gt;</td>
    </tr>
    <tr>
      <td>Sexe</td>
      <td>&lt;**SEX&gt;</td>
      <td>Code postal</td>
      <td>&lt;**CPO&gt;</td>
      <td>Code recours</td>
      <td>&lt;**COR&gt;</td>
    </tr>
    <tr>
      <td>Date d'admission </td>
      <td>&lt;**DTA&gt;</td>
      <td>Ville</td>
      <td>&lt;**VIL&gt;</td>
      <td>Dest. souhait&eacute;e</td>
      <td>&lt;**DES&gt;</td>
    </tr>
    <tr>
      <td>Heure d'admission</td>
      <td>&lt;**HEA&gt;</td>
      <td>IDE</td>
      <td>&lt;**IDE&gt;</td>
      <td>Dest. attendue</td>
      <td>&lt;**DAT&gt;</td>
    </tr>
    <tr>
      <td>M&eacute;decin urgentiste </td>
      <td>&lt;**MED&gt;</td>
      <td>Salle d'examen</td>
      <td>&lt;**SAL&gt;</td>
      <td>Cat&eacute;gorie de diagnotics</td>
      <td>&lt;**CDI&gt;</td>
    </tr>
    <tr>
      <td>Date &amp; heure de sortie </td>
      <td>&lt;**DHS&gt;</td>
      <td>CCMU</td>
      <td>&lt;**CCM&gt;</td>
      <td>Diagnostics</td>
      <td>&lt;**DIA&gt;</td>
    </tr>
    <tr>
      <td>Date &amp; heure d'examen</td>
      <td>&lt;**DHE&gt;</td>
      <td>Code gravit&eacute;</td>
      <td>&lt;**GRA&gt;</td>
      <td>Code Diagnostic</td>
      <td>&lt;**DIC&gt;</td>
    </tr>
  </table>
  <table summary="Aide" width="80%" border="1" cellpadding="2" cellspacing="0">
    <tr>
      <th background="images/fondTab.jpg" colspan="4">Style du texte</th>
    </tr>
    <tr>
      <td><strong>Style</strong></td>
      <td><strong>Balise</strong></td>
      <td><strong>Exemple</strong></td>
      <td><strong>R&eacute;sultat</strong></td>
    </tr>
    <tr>
      <td>Gras</td>
      <td>&lt;B&gt; &lt;/B&gt; </td>
      <td>Texte en &lt;B&gt;<strong>gras</strong>&lt;/B&gt;</td>
      <td>Texte en <strong>gras</strong></td>
    </tr>
    <tr>
      <td>Italique</td>
      <td>&lt;I&gt; &lt;/I&gt;</td>
      <td>Texte en &lt;I&gt;<em>italique</em>&lt;/I&gt;</td>
      <td>Texte en<em> italique</em></td>
    </tr>
    <tr>
      <td>Soulign&eacute;</td>
      <td>&lt;U&gt; &lt;/U&gt;</td>
      <td>Texte &lt;U&gt;<u>souligné</u>&lt;/U&gt;</td>
      <td>Texte <u>soulign&eacute;</u></td>
    </tr>
    <tr>
      <td>Retour &agrave; la lligne </td>
      <td>&lt;BR&gt;</td>
      <td>Voici un retour&lt;BR&gt; à la ligne</td>
      <td>Voici un retour<br>
      &agrave; la ligne </td>
    </tr>
  </table>
  <table summary="Aide" width="80%" border="1" cellpadding="2" cellspacing="0">
    <tr>
      <th background="images/fondTab.jpg" colspan="2" scope="col">Les styles peuvent se combiner</th>
    </tr>
    <tr>
      <td width="370">Texte en &lt;B&gt;&lt;I&gt;gras et
    italique&lt;/I&gt; &lt;/B&gt;</td>
      <td width="182">Texte en <b><i>gras et italique</i></b></td>
    </tr>
    <tr>
      <td>Texte &lt;U&gt;&lt;I&gt;souligné et
    italique&lt;/I&gt;&lt;/U&gt;</td>
      <td>Texte <i><u>souligné et italique</u></i></td>
    </tr>
    <tr>
      <td>Texte &lt;B&gt;&lt;U&gt;&lt;I&gt;en
    gras, souligné et italique&lt;/I&gt;&lt;/U&gt;&lt;/B&gt;</td>
      <td>Texte <b><i><u>en gras, souligné et
    italique</u></i></b></td>
    </tr>
  </table>
  <table summary="Aide" width="80%" border="1" cellpadding="2" cellspacing="0">
    <tr>
      <th background="images/fondTab.jpg" colspan="4" scope="col">Exemple</th>
    </tr>
    <tr>
      <td><strong>Source</strong></td>
      <td><strong>Résultat</strong></td>
    </tr>
    <tr>
      <td rowspan="9">Le patient
  &lt;B&gt;&lt;**NMA&gt;&lt;/B&gt; &lt;I&gt;&lt;**PRE&gt;&lt;/i&gt; a été admis
    au urgences le &lt;U&gt;&lt;**DTA&gt; à &lt;**HEA&gt;&lt;/U&gt;.</td>
      <td rowspan="9">Le patient <b>DUPONT</b> <i>Jean</i>
    a été admis aux urgences le <u>26/09/2003 à 18&nbsp;:</u></td>
    </tr>
  </table>
</div>
