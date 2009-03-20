<div id="etatcivilpatient">
<table border="1">
  <tr>
    <td><mx:text id="sexe" /> <b><mx:text id="nom" /></b><mx:text id="etatUHCD" /><mx:text id="etiquettes" /><mx:text id="lienModPatient" /><mx:text id="lienQuestion" />
      <br />
      <mx:text id="naissance" /></td>
    <td class="hover"> <b>Médecin traitant :</b> <mx:text id="MedecinTraitant" />
      <a mXattribut="href:lienMedecinTraitant"><mx:image id="imgModifierMedecinTraitant"/></a></td>
  </tr>
  <tr>
    <td rowspan="2">
    <b>Adresse : </b><mx:text id="adresse" />
      <br />
      <b>Téléphone : </b><mx:text id="telephone" /> 
     
    </td>
    <td><b>Admission : </b><mx:text id="admission" /></td>
  </tr>
  <tr>
    <td class="hover"><b>Examen : </b><mx:text id="DateExamen" />
      <a mXattribut="href:lienDateExamen"><mx:image id="imgModifierDateExamen"/></a></td>
  </tr>
  <tr>
    <td class="hover"><b>A prévenir :</b> <mx:text id="Prevenir" />
      <a mXattribut="href:lienPrevenir"><mx:image id="imgModifierPrevenir"/></a></td>
    <td class="hover">  <b>Sortie : </b>      <mx:text id="DateSortie" />
      <mx:text id="retourRadio" />
      <a mXattribut="href:lienDateSortie"><mx:image id="imgModifierDateSortie"/>
    </a></td>
  </tr>
</table>
</div>

