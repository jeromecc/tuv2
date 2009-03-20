<dd id="s10">
  <form method="post" action="index.php">
    <mx:bloc id="connecte">
      Bienvenue <mx:text id="uid" /><br />
      <center><input type="submit" value="Déconnexion" name="Déconnexion" class="boutton" /></center>
      <mx:hidden id="hidden" />
    </mx:bloc id="connecte">
    <mx:bloc id="codeacces">
      Pour continuer, veuillez saisir votre code d'accès.
      <table summary="Authentification">
        <tr><td>Code :</td><td><mx:formField id="codeacces" /></td></tr>
      </table>
      <center>
        <input type="submit" value="Envoyer" name="AuthentificationDemandee" class="boutton"/>
        <br />
      </center>
      <mx:hidden id="hidden" />
      </form>
    </mx:bloc id="codeacces">
    <mx:bloc id="normal">
      Bienvenue <mx:text id="uid" />,<br />
      Authentification : 
      <table summary="Authentification">
        <tr>
          <td>
            Login :
          </td>
          <td>
            <mx:formField id="login" />
          </td>
        </tr>
        <tr>
          <td>
            Password :
          </td>
          <td>
            <mx:formField id="password" />
          </td>
        </tr>
      </table>
      <center>
        <input type="submit" value="Envoyer" name="AuthentificationDemandee" class="boutton" />
        <input type="submit" value="Déconnexion" name="Déconnexion"  class="boutton" />
        <br />
        <mx:text id="changerpassword" />
      </center>
      <mx:hidden id="hidden" />
    </mx:bloc id="normal">
  </form>
</dd>
