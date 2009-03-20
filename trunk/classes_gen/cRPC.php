<?

// Classe       : cMLLP
// Version      : 1.0
// Date         : 10-06-07
// Auteur       : A.Falanga (CH Brignoles)
// Utilisation  : Protocole de transport RPC (Client<->Serveur) avec le client local (Mobidoc)

class cMLLP {
    public $host;
    public $port;
    public $socktimeout;
    
    public $wACK;

    public function __construct($cIP,$cPort, $cTimeout) {
        $this->host = $cIP;
        $this->port = $cPort;
        $this->socktimeout = $cTimeout;
        set_time_limit($cTimeout);        
        // Trame ACK standard (Ne pas modifier)
        $this->wACK = "MSA|AA|0"; 
    }
    
    public function __destruct() {
        // echo "Destructeur\n";
    }
    
    public function SetNetworkParams($socks, $timeout) {
        socket_set_option( $socks,  SOL_SOCKET, SO_SNDTIMEO, 
        array( "sec"=>$timeout, // Timeout en seconds
               "usec"=>0        // Timeout en microsecondes !!!
        ));
           
        socket_set_option( $socks,  SOL_SOCKET, SO_RCVTIMEO, 
        array( "sec"=>$timeout, // Timeout en seconds
               "usec"=>0        // Timeout en microsecondes !!!
        ));   
    
    }
       
    public function SendMessage($message) {
    
        $start = chr(11);
        $stop  = chr(28); $stop1 = chr(13);
        $crlf  = chr(13);
    
        $message=$start.$message.$stop.$stop1;

        if (($socket = socket_create(AF_INET, SOCK_STREAM, 0)) == false) { $result = "NACK-0-".socket_strerror(socket_last_error()); return $result; }
        $this->SetNetworkParams($socket, $this->socktimeout); 
        if (($result = socket_connect($socket, $this->host, $this->port))== false) {$result = "NACK-2-".socket_strerror(socket_last_error()); return $result; } 
        if ((socket_write($socket, $message, strlen($message)))== false)  {$result = "NACK-3-".socket_strerror(socket_last_error()); return $result; } 
		if (($result = socket_read ($socket, 1024))==false)  {$result = "NACK-4-".socket_strerror(socket_last_error()); eko ( $result) ;return $result; } 
		// eko ( $result ) ;
        socket_shutdown($socket, 2);
        socket_close($socket); 

        $result = trim($result); 
        $result = substr($result, 0, strlen($result)-1);
        
        $pos = strpos($result, $this->wACK, 0);
        
        if ($pos==0) { $result = "NACK"; } else { $result = "ACK"; }

        return $result;
    }
}

 
class cRPC {
    
    public $aMLLP;
    
    // Paramètre $cIP       : Adresse IP de la machine ou se trouve le serveur RPC
    // Paramètre $cPort     : Port utilisé pour la communication RPC (En général 1024)   
    // Paramètre $cTimeout  : Timeout en seconde (communication RPC)   
    public function __construct($cIP,$cPort,$cTimeout) {       
        $this->aMLLP = new cMLLP($cIP, $cPort, $cTimeout);
    }
    
    public function __destruct() {
        //$this->aMLLP->free;
        // echo "Destructeur\n";
    }
    
    // Appel de Cora
    // Version à utiliser DANS le contexte du TU (Requêtes dans la base du TU exécutés coté client pour extraite les informations)
    // Paramètre $idpatient : identifiant interne du terminal
    // Paramètre $idmedecin : identifiant du médecin   
    // Paramètre $mode      : Mode (E pour Externe ou R pour Actes et Diag )   
    public function OpenCora($idpatient, $idmedecin, $mode) {
      $message = '<?xml version="1.0" encoding="windows-1252"?>';
      $message = $message."<root><cora><idpatient>".$idpatient."</idpatient><idmedecin>".$idmedecin."</idmedecin><mode>".$mode."</mode></cora></root>"; 
      $ack = $this->aMLLP->SendMessage($message);
      // $this->aMLLP->free;        
      return $ack;
    }

    
    // Paramètre $idu       : Identifiant du patient (IPP)
    // Paramètre $idpass    : Identifiant du séjour patient (NSEJ)
    // Paramètre $iduf      : UF de l'exécution de l'actes (pour le praticien indiqué dans médecin)
    // Paramètre $idmedecin : Identifiant du médecin
    // Paramètre $mode      : Mode (E pour Externe ou R pour Actes et Diag )   
    public function OpenCora2($idu,$idpass,$iduf, $idmedecin, $mode) {
      $message = '<?xml version="1.0" encoding="windows-1252"?>';
      $message = $message."<root><cora2><idu>".$idu."</idu><idpass>".$idpass."</idpass><iduf>".$iduf."</iduf><idmedecin>".$idmedecin."</idmedecin><mode>".$mode."</mode></cora2></root>";
      //$message = $message."<root><cora2><idu>".$idu."</idu><idpass>".$idpass."</idpass><iduf>".$iduf."</iduf><mode>".$mode."</mode></cora2></root>";
      $ack = $this->aMLLP->SendMessage($message);
      // $this->aMLLP->free;        
      return $ack;
    }
    
    // 2007-01-31
    // Appel g�n�rique.
    public function Open ( $type, $idu, $idpass, $iduf, $idmedecin, $mode, $pass ) {
      $message = '<?xml version="1.0" encoding="windows-1252"?>';
      $message = $message."<message><type>".$type."</type><idu>".$idu."</idu><idpass>".$idpass."</idpass><iduf>".$iduf."</iduf><idmedecin>".$idmedecin."</idmedecin><pass>".$pass."</pass><mode>".$mode."</mode></message>";
      $ack = $this->aMLLP->SendMessage($message);
      // $this->aMLLP->free;        
      return $ack;
    }
    
    // 2007-01-31 : Damien Borel
    public function OpenCora3 ( $idu, $idpass, $iduf, $idmedecin, $mode ) {
      $message = '<?xml version="1.0" encoding="windows-1252"?>';
      $message = $message."<message><type>cora</type><idu>".$idu."</idu><idpass>".$idpass."</idpass><iduf>".$iduf."</iduf><idmedecin>".$idmedecin."</idmedecin><pass></pass><mode>".$mode."</mode></message>";
      $ack = $this->aMLLP->SendMessage($message);
      // $this->aMLLP->free;        
      return $ack;
    }
    
    // 2007-01-31 : Damien Borel
    public function OpenClinicom ( $idu, $idpass, $iduf, $idmedecin, $pass, $mode ) {
      $message = '<?xml version="1.0" encoding="windows-1252"?>';
      $message = $message."<message><type>clinicom</type><idu>".$idu."</idu><idpass>".$idpass."</idpass><iduf>".$iduf."</iduf><idmedecin>".$idmedecin."</idmedecin><mode>".$mode."</mode><pass>".$pass."</pass></message>";
      $ack = $this->aMLLP->SendMessage($message);
      // $this->aMLLP->free;        
      return $ack;
    }
    
    // 10-01-08 (AF)
    // Param�tre $idu        : Identifiant du patient (IPP)
    public function OpenMobidoc($idu ) {
      $message = '<?xml version="1.0" encoding="windows-1252"?>';
      $message = $message."<root><mobidoc><idpatient>".$idu."</idpatient></mobidoc></root>";
      $ack = $this->aMLLP->SendMessage($message);
      // $this->aMLLP->free;
      return $ack;
    }

}

?>
