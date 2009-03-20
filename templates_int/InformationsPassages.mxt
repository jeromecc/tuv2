<div id="infospassages">
  <form method="post" action="index.php">
    <table>
      <tr>
        <td>
          <b><mx:text id="nbPresents" /></b> <mx:text id="presents" /> (
	  <b><mx:text id="nbVus" /></b> <mx:text id="vus" />,
	  <b><mx:text id="nbUHCD" /></b> <mx:text id="UHCD" />,
          <b><mx:text id="nbNonVus" /></b> <mx:text id="nonVus" />)
        </td>
        <td>
          <b><mx:text id="nbAttendus" /></b>  <mx:text id="attendus" />
        </td>
        <td>
          <font color="red">aujourd'hui : 
          <b><mx:text id="nbPassages" /></b> <mx:text id="passages" /></font>
        </td>
        <mx:bloc id="historique">
          <td>
            Montrer l'historique séjours & courriers 
            <mx:checker id="histo" onClick="reload(this.form)"  />
            <mx:hidden id="hidden" />
          </td>
        </mx:bloc id="historique">
	<mx:bloc id="ajouter">
	  <td>
	    <input name="Ajouter"  type="image" src="images/ajouter2.gif"   value="Ajouter" style="border: 0px; background-color: transparent;" />
          </td>
	</mx:bloc id="ajouter">
      </tr>
    </table>
    <mx:hidden id="hidden" />
  </form>
</div>
