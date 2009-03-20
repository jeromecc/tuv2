<div id="fichepatient">
  <table>
    <tr>
      <td>
        <div id="etatcivilpatient">
          <mx:text id="etatcivil" />
        </div>
      </td>
      <td rowspan="3">
	<mx:text id="appelsContextuels" />
      </td>
    </tr>
    <mx:text id="transfert" />
    <tr>
      <td>
        <div id="informationspatient">
          <mx:text id="informations" />
        </div>
      </td>
    </tr>
  </table>
  <mx:text id="bmr" />
  <table>
    <tr>
      <td>
        <mx:text id="iframe1"/>
        <div id="historiquepatient">
          <mx:text id="historique" />
        </div>
        <mx:text id="iframe2"/>
      </td>
      <td>
        <div id="historiquedocspatient">
          <mx:text id="historiquedocs" />
        </div>
      </td>
      <td>
      	<mx:text id="iframe3"/>
	<div id="documentspatient">
          <mx:text id="documents" />
        </div>
        <div style="height:5px;"></div>
        <div id="messagespatient">
          <mx:text id="messages" />
        </div>
        <mx:text id="iframe4"/>
      </td>
      <td>
	<mx:text id="iframe5" />
  	<!--- Intégration du module de cotation CCAM --->
        <div id="ccam">
	  <mx:text id="cotationCCAM" />
	</div>
	<mx:text id="iframe6" />
      </td>
      <td>
  	<mx:text id="iframe7"/>
	<mx:text id="blocnote" />	    
        <mx:text id="iframe8"/>
	<div style="height:5px;"></div>
          <mx:text id="formx" />
      </td>
    </tr>  
    <tr>
      <td colspan=5>
 	<mx:text id="iframe9" />
      </td>
    </tr>
  </table>
  <mx:text id="iframe"/>
  <mx:text id="uhcd"/>
</div>
<mx:text id="fenetreBloquante" />
