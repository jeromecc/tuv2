<?php

// Titre  : Classe Etiquettes
// Auteur : Damien Borel <dborel@ch-hyeres.fr>
// Date   : 03 Mai 2007

// Description : 
// Gestion de l'impression des étiquettes du terminal des urgences.

class clEtiquettes {

// Attributs de la classe.


// Constructeur.
    function __construct ( $etab='', $pat ) {
    // Récupération du patient.
	$this->patient = $pat ;
	// Appel du module d'édition spécifique à l'établissement.
	switch ( $etab ) {
	    case 'Saint Tropez':
		$this->genSaintTropez ( ) ;
		break ;
	    case 'Pertuis':
		$this->genPertuis ( ) ;
		break ;
	    case 'Carpentras':
		$this->genCarpentras ( ) ;
		break ;
	    case 'Carpentras2':
		$this->genCarpentras2 ( ) ;
		break ;
	    case 'Salon':
		$this->genSalon ( ) ;
		break ;
	}
    }

    // Génération des étiquettes pour Saint Tropez
    function genSaintTropez ( ) {

    # paramètres
	$mg=15; // Marge de gauche =>  initial 17
	$mh=11; // Marge du haut =>  initial 9
	$md=15; // Marge de droite
	$mb=9; // Marge du bas

	$largeur_etiquette=60; // largeur_etiquette =>  initial 60
	$espace_etiquettes=11; // =>  initial 7
	$nb_ligne_etiquettes=8;
	$nb_etiquette_ligne=3;

	// Préparation du document PDF.
	$pdf=new FPDF('P','mm','A4');
	$pdf->Open();
	$pdf->SetLeftMargin($mg);
	$pdf->SetTopMargin($mh);
	$pdf->SetAutoPageBreak( 1 ,0);
	$pdf->AddPage();
	// Gestion des fonts
	$pdf -> AddFont ( 'code39', '', 'IDAutomation_Code_39.php' ) ;
	$pdf->SetFont('times','',8);

	// Préparation des informations.
	$nom = $this->patient->getNom() ;
	$prenom = $this->patient->getPrenom() ;
	$date = new clDate ( $this->patient->getDateNaissance ( ) ) ;
	$duree = new clDuree ( ) ;
	$dateN = $date->getDate ( "d/m/Y") ;
	$dateN .= " (".$duree->getAge ( $date->getTimestamp ( ) ).")" ;
	if ( $this->patient->getSexe ( ) == "F" ) {
	    $sexe = "féminin" ;
	    $e = "e" ;
	} elseif ( $this->patient->getSexe ( ) == "M" ) {
	    $sexe = "masculin" ;
	    $e = "" ;
	} else {
	    $sexe = "indéterminé" ;
	    $e = "" ;
	}
	$date -> setDate ( $this->patient->getDateAdmission ( ) ) ;
	$le = $date -> getDate ( "d/m/Y H:i" ) ;
	$ipp = $this->patient->getILP ( ) ;

	for ($i = 1; $i <= $nb_ligne_etiquettes; $i++) {
	// Nom
	    $pdf->Cell($largeur_etiquette,3,"Nom : $nom",0,0,L);
	    $pdf->Cell($espace_etiquettes,3,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,3,"Nom : $nom",0,0,L);
	    $pdf->Cell($espace_etiquettes,3,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,3,"Nom : $nom",0,1,L);

	    // Prenom
	    $pdf->Cell($largeur_etiquette,3,"Prénom : $prenom",0,0,L);
	    $pdf->Cell($espace_etiquettes,3,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,3,"Prénom : $prenom",0,0,L);
	    $pdf->Cell($espace_etiquettes,3,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,3,"Prénom : $prenom",0,1,L);

	    // Naissance
	    $pdf->Cell($largeur_etiquette,3,"Né$e le : $dateN",0,0,L);
	    $pdf->Cell($espace_etiquettes,3,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,3,"Né$e le : $dateN",0,0,L);
	    $pdf->Cell($espace_etiquettes,3,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,3,"Né$e le : $dateN",0,1,L);

			/*
			// Nom de jeune fille
	        $pdf->Cell($largeur_etiquette,3,"NJF :",0,0,L);
	    	$pdf->Cell($espace_etiquettes,3,"",0,0,L);
	        $pdf->Cell($largeur_etiquette,3,"NJF :",0,0,L);
	    	$pdf->Cell($espace_etiquettes,3,"",0,0,L);
	    	$pdf->Cell($largeur_etiquette,3,"NJF :",0,1,L);
	    	*/

	    // Sexe
	    $pdf->Cell($largeur_etiquette,3,"Sexe : $sexe",0,0,L);
	    $pdf->Cell($espace_etiquettes,3,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,3,"Sexe : $sexe",0,0,L);
	    $pdf->Cell($espace_etiquettes,3,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,3,"Sexe : $sexe",0,1,L);

	    // Date admission
	    $pdf->Cell($largeur_etiquette,3,"Le : $le",0,0,L);
	    $pdf->Cell($espace_etiquettes,3,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,3,"Le : $le",0,0,L);
	    $pdf->Cell($espace_etiquettes,3,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,3,"Le : $le",0,1,L);

			/*
	        // Affichage code barre
	        $pdf->SetFont('code39','',12);
	        $pdf->Cell($largeur_etiquette,19,"*$ipp*",0,0,C);
	    	$pdf->Cell($espace_etiquettes,3,"",0,0,L);
	        $pdf->Cell($largeur_etiquette,19,"*$ipp*",0,0,C);
	    	$pdf->Cell($espace_etiquettes,3,"",0,0,L);
	    	$pdf->Cell($largeur_etiquette,19,"*$ipp*",0,1,C);
	        $pdf->SetFont('times','',8);

			// Affichage IPP
			$pdf->Cell($largeur_etiquette,0,"IPP : ",0,0,L);
	    	$pdf->Cell($espace_etiquettes,0,"",0,0,L);
	        $pdf->Cell($largeur_etiquette,0,"IPP : ",0,0,L);
	    	$pdf->Cell($espace_etiquettes,0,"",0,0,L);
	    	$pdf->Cell($largeur_etiquette,0,"IPP : ",0,1,L);
			*/

	    $pdf->SetFont('times','',8);
	    $pdf->Cell("8",18,"IPP : ",0,0,L);
	    $pdf->SetFont('code39','',10);
	    $pdf->Cell($largeur_etiquette-8,18,"*$ipp*",0,0,L);
	    $pdf->Cell($espace_etiquettes,3,"",0,0,L);
	    $pdf->SetFont('times','',8);
	    $pdf->Cell("8",18,"IPP : ",0,0,L);
	    $pdf->SetFont('code39','',10);
	    $pdf->Cell($largeur_etiquette-8,18,"*$ipp*",0,0,L);
	    $pdf->Cell($espace_etiquettes,3,"",0,0,L);
	    $pdf->SetFont('times','',8);
	    $pdf->Cell("8",18,"IPP : ",0,0,L);
	    $pdf->SetFont('code39','',10);
	    $pdf->Cell($largeur_etiquette-8,18,"*$ipp*",0,1,L);

	    $pdf->SetFont('times','',8);

	    // inter _etiquettes
	    $pdf->Cell(1,2.4,"",0,1);
	}


	$pdf->Output();
    }

    // Génération des étiquettes pour Pertuis
    function genPertuis ( ) {
	global $options ;

	# paramètres
	$mg=6; // Marge de gauche =>  initial 17
	$mh=4; // Marge du haut =>  initial 9
	$md=15; // Marge de droite
	$mb=0; // Marge du bas

	$largeur_etiquette=40; // largeur_etiquette =>  initial 60
	$espace_etiquettes=15; // =>  initial 7
	$nb_ligne_etiquettes=4;
	$nb_etiquette_ligne=5;

	// Préparation du document PDF.
	$pdf=new FPDF('L','mm','A4');
	$pdf->Open();
	$pdf->SetLeftMargin($mg);
	$pdf->SetTopMargin($mh);
	$pdf->SetAutoPageBreak( 1 ,0);
	$pdf->AddPage();
	// Gestion des fonts
	$pdf -> AddFont ( 'code39', '', 'IDAutomation_Code_39.php' ) ;
	$pdf->SetFont('times','',12);

	// Préparation des informations.
	$nom = strtoupper($this->patient->getNom()) ;
	$prenom = strtoupper($this->patient->getPrenom()) ;
	$date = new clDate ( $this->patient->getDateNaissance ( ) ) ;
	$duree = new clDuree ( ) ;
	$dateN = $date->getDate ( "d/m/Y") ;
	$dateN .= " (".$duree->getAge ( $date->getTimestamp ( ) ).")" ;
	if ( $this->patient->getSexe ( ) == "F" ) {
	    $sexe = "Féminin" ;
	    $e = "e" ;
	} elseif ( $this->patient->getSexe ( ) == "M" ) {
	    $sexe = "Masculin" ;
	    $e = "" ;
	} else {
	    $sexe = "Indéterminé" ;
	    $e = "" ;
	}
	$date -> setDate ( $this->patient->getDateAdmission ( ) ) ;
	$le = $date -> getDate ( "d/m/Y H:i" ) ;
	$ipp = $this->patient->getILP ( ) ;
	$uf = $this->patient->getUF ( ) ;
	if ( $uf == $options->getOption ( 'numUFexec' ) ) $loc = '(URGENCES)' ; else $loc = '(UHCD)' ;
	$tel = $this->patient->getTel ( ) ;
	$adresse = $this->patient->getAdresse ( ) ;
	$cpv = $this->patient->getCodePostal ( ). " " . $this->patient->getVille ( ) ;
	$prev = $this->patient->getPrevenir ( ) ;

	// Ligne 1
	$pdf->Cell(4*$largeur_etiquette,4,"NUM : $ipp",0,0,L);
	$pdf->Cell(4*$espace_etiquettes,4,"",0,0,L);
	$pdf->Cell($largeur_etiquette,4,"$nom",0,1,L);

	// Ligne 2
	$pdf->Cell(4*$largeur_etiquette,4,"Le : $le",0,0,L);
	$pdf->Cell(4*$espace_etiquettes,4,"",0,0,L);
	$pdf->Cell($largeur_etiquette,4,"$prenom",0,1,L);

	// Ligne 3
	$pdf->Cell(4*$largeur_etiquette,4,"UF : $uf $loc",0,0,L);
	$pdf->Cell(4*$espace_etiquettes,4,"",0,0,L);
	$pdf->Cell($largeur_etiquette,4,"Né$e : $dateN",0,1,L);

	// Ligne 4
	$pdf->Cell(4*$largeur_etiquette,4,"Nom : $nom",0,0,L);
	$pdf->Cell(4*$espace_etiquettes,4,"",0,0,L);
	$pdf->Cell($largeur_etiquette,4,"Sexe : $sexe",0,1,L);

	// Ligne 5
	$pdf->Cell(4*$largeur_etiquette,4,"Prénom : $prenom",0,0,L);
	$pdf->Cell(4*$espace_etiquettes,4,"",0,0,L);
	$pdf->Cell($largeur_etiquette,4,"Le : $le",0,1,L);

	// Ligne 6
	$pdf->Cell(4*$largeur_etiquette,4,"Sexe : $sexe",0,0,L);
	$pdf->Cell(4*$espace_etiquettes,4,"",0,0,L);
	$pdf->Cell($largeur_etiquette,4,"NUM : $ipp",0,1,L);

	// Ligne 7
	$pdf->Cell(4*$largeur_etiquette,4,"Né$e : $dateN",0,0,L);
	$pdf->Cell(4*$espace_etiquettes,4,"",0,0,L);
	$pdf->Cell($largeur_etiquette,4,"URGENCES",0,1,L);

	// Saut
	$pdf->Cell(1,6,"",0,1);

	// Ligne  8
	$pdf->Cell(4*$largeur_etiquette,4,"Tél : $tel",0,0,L);
	$pdf->Cell(4*$espace_etiquettes,4,"",0,0,L);
	$pdf->Cell($largeur_etiquette,4,"$nom",0,1,L);

	// Ligne 9
	$pdf->Cell(4*$largeur_etiquette,4,"Adresse : $adresse",0,0,L);
	$pdf->Cell(4*$espace_etiquettes,4,"",0,0,L);
	$pdf->Cell($largeur_etiquette,4,"$prenom",0,1,L);

	// Ligne 10
	$pdf->Cell(4*$largeur_etiquette,4,"CP / Ville : $cpv",0,0,L);
	$pdf->Cell(4*$espace_etiquettes,4,"",0,0,L);
	$pdf->Cell($largeur_etiquette,4,"Né$e : $dateN",0,1,L);

	// Ligne 11
	$pdf->Cell(4*$largeur_etiquette,4,"A prévenir : $prev",0,0,L);
	$pdf->Cell(4*$espace_etiquettes,4,"",0,0,L);
	$pdf->Cell($largeur_etiquette,4,"Sexe : $sexe",0,1,L);

	// Ligne 12
	$pdf->Cell(4*$largeur_etiquette,4,"",0,0,L);
	$pdf->Cell(4*$espace_etiquettes,4,"",0,0,L);
	$pdf->Cell($largeur_etiquette,4,"Le : $le",0,1,L);

	// Ligne 13
	$pdf->Cell(4*$largeur_etiquette,4,"",0,0,L);
	$pdf->Cell(4*$espace_etiquettes,4,"",0,0,L);
	$pdf->Cell($largeur_etiquette,4,"NUM : $ipp",0,1,L);

	// Ligne 14
	$pdf->Cell(4*$largeur_etiquette,4,"",0,0,L);
	$pdf->Cell(4*$espace_etiquettes,4,"",0,0,L);
	$pdf->Cell($largeur_etiquette,4,"URGENCES",0,1,L);

	// Saut
	$pdf->Cell(1,6,"",0,1);

	for ($i = 1; $i <= $nb_ligne_etiquettes; $i++) {
	// Nom
	    $pdf->Cell($largeur_etiquette,4,"$nom",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"$nom",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"$nom",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"$nom",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"$nom",0,1,L);

	    // Prenom
	    $pdf->Cell($largeur_etiquette,4,"$prenom",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"$prenom",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"$prenom",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"$prenom",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"$prenom",0,1,L);

	    // Naissance
	    $pdf->Cell($largeur_etiquette,4,"Né$e : $dateN",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Né$e : $dateN",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Né$e : $dateN",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Né$e : $dateN",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Né$e : $dateN",0,1,L);

			/*
			// Nom de jeune fille
	        $pdf->Cell($largeur_etiquette,4,"NJF :",0,0,L);
	    	$pdf->Cell($espace_etiquettes,4,"",0,0,L);
	        $pdf->Cell($largeur_etiquette,4,"NJF :",0,0,L);
	    	$pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    	$pdf->Cell($largeur_etiquette,4,"NJF :",0,1,L);
	    	*/

	    // Sexe
	    $pdf->Cell($largeur_etiquette,4,"Sexe : $sexe",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Sexe : $sexe",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Sexe : $sexe",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Sexe : $sexe",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Sexe : $sexe",0,1,L);

	    // Date admission
	    $pdf->Cell($largeur_etiquette,4,"Le : $le",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Le : $le",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Le : $le",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Le : $le",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Le : $le",0,1,L);

	    // IPP
	    $pdf->Cell($largeur_etiquette,4,"NUM : $ipp",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"NUM : $ipp",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"NUM : $ipp",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"NUM : $ipp",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"NUM : $ipp",0,1,L);


	    // Date admission
	    $pdf->Cell($largeur_etiquette,4,"URGENCES",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"URGENCES",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"URGENCES",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"URGENCES",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"URGENCES",0,1,L);

	    // inter _etiquettes
	    $pdf->Cell(1,6.3,"",0,1);
	}


	$pdf->Output();
    }

    // Génération des étiquettes pour Pertuis
    function genCarpentras ( ) {
	global $options ;

	# paramètres
	$mg=5; // Marge de gauche =>  initial 17
	$mh=4; // Marge du haut =>  initial 9
	$md=15; // Marge de droite
	$mb=9; // Marge du bas

	$largeur_etiquette=42; // largeur_etiquette =>  initial 60
	$espace_etiquettes=13; // =>  initial 7
	$nb_ligne_etiquettes=4;
	$nb_etiquette_ligne=5;

	// Préparation du document PDF.
	$pdf=new FPDF('L','mm','A4');
	$pdf->Open();
	$pdf->SetLeftMargin($mg);
	$pdf->SetTopMargin($mh);
	$pdf->SetAutoPageBreak( 1 ,0);
	$pdf->AddPage();
	// Gestion des fonts
	$pdf -> AddFont ( 'code39', '', 'IDAutomation_Code_39.php' ) ;
	$pdf->SetFont('times','',12);

	// Préparation des informations.
	$nom = strtoupper($this->patient->getNom()) ;
	$prenom = strtoupper($this->patient->getPrenom()) ;
	$date = new clDate ( $this->patient->getDateNaissance ( ) ) ;
	$duree = new clDuree ( ) ;
	$dateN = $date->getDate ( "d/m/Y") ;
	$dateN .= " (".$duree->getAge ( $date->getTimestamp ( ) ).")" ;
	if ( $this->patient->getSexe ( ) == "F" ) {
	    $sexe = "Féminin" ;
	    $e = "e" ;
	} elseif ( $this->patient->getSexe ( ) == "M" ) {
	    $sexe = "Masculin" ;
	    $e = "" ;
	} else {
	    $sexe = "Indéterminé" ;
	    $e = "" ;
	}
	$date -> setDate ( $this->patient->getDateAdmission ( ) ) ;
	$le = $date -> getDate ( "d/m/Y H:i" ) ;
	$led = $date -> getDate ( "d/m/Y" ) ;
	$leh = $date -> getDate ( "H:i" ) ;
	$ipp = $this->patient->getILP ( ) ;
	$nsej = $this->patient->getNSej ( ) ;
	$uf = $this->patient->getUF ( ) ;
	$sexe = $this->patient->getSexe ( ) ;
	if ( $uf == $options->getOption ( 'numUFexec' ) ) $loc = '(URGENCES)' ; else $loc = '(UHCD)' ;
	$tel = $this->patient->getTel ( ) ;
	$adresse = $this->patient->getAdresse ( ) ;
	$cpv = $this->patient->getCodePostal ( ). " " . $this->patient->getVille ( ) ;
	$prev = $this->patient->getPrevenir ( ) ;
	$medt = $this->patient->getMedecinTraitant ( ) ;
	$modet = $this->patient->getMedecinTraitant ( ) ;
	$adresseur = $this->patient->getAdresseur ( ) ;
	$modeadm = $this->patient->getModeAdmission ( ) ;


	// Grosse étiquette
	$pdf->Cell(20,4,"",0,0,L);
	// Ligne 1
	$pdf->Cell(4*$largeur_etiquette,5,"CENTRE HOSPITALIER DE CARPENTRAS",0,1,L);
	// Ligne 2
	$pdf->Cell(11,5,"Date : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(23,5,"$led",0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(13,5,"Heure : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(27,5,"$leh",0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(21,5,"N° Patient : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(20,5,"$ipp",0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(18,5,"N° URG : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(20,5,"$nsej",0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(18,5,"N° ARCHIVE : ",0,1,L);
	// Ligne 3
	$pdf->Cell(11,5,"Nom : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(63,5,strtoupper($nom),0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(13,5,"Nom naissance : ",0,1,L);
	// Ligne 4
	$pdf->Cell(16,5,"Prénom : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(58,5,ucfirst(strtolower($prenom)),0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(12,5,"Sexe : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(29,5,"$sexe",0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(12,5,"N° S.S : ",0,1,L);
	// Ligne 5
	$pdf->Cell(17,5,"Né(e) le : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(57,5,$dateN,0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(12,5,"A : ",0,1,L);
	// Ligne 6
	$pdf->Cell(17,4,"Adresse : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(57,4,$adresse,0,1,L);
	$pdf->SetFont('times','',12);
	// Ligne 5
	$pdf->Cell(115,4,"",0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(9,4,"Tél : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(20,4,$tel,0,1,L);
	$pdf->SetFont('times','',12);
	// Ligne 6
	$pdf->Cell(17,4,"",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(98,4,$cpv,0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(9,4,"Pays : ",0,1,L);
	// Ligne 7
	$pdf->Cell(17,5,"Nom du tuteur : ",0,1,L);
	// Ligne 8
	$pdf->Cell(37,5,"Personne à prévenir : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(98,5,$prev,0,1,L);
	$pdf->SetFont('times','',12);
	// Ligne 9
	$pdf->Cell(23,5,"Adressé par : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(51,5,$adresseur,0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(30,5,"Médecin traitant : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(98,5,$medt,0,1,L);
	$pdf->SetFont('times','',12);
	// Ligne 10
	$pdf->Cell(34,5,"Mode de transport : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(98,5,$modeadm,0,1,L);
	$pdf->SetFont('times','',12);

	// Saut
	$pdf->Cell(1,5.8,"",0,1);
	$pdf->SetFont('times','',9);
	for ($i = 1; $i <= $nb_ligne_etiquettes; $i++) {
	// Entrée
	    $pdf->Cell($largeur_etiquette,4,"Entrée du : $led à $leh",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Entrée du : $led à $leh",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Entrée du : $led à $leh",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Entrée du : $led à $leh",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Entrée du : $led à $leh",0,1,L);

	    // D/Nais
	    $pdf->Cell($largeur_etiquette,4,"D/nais : $dateN",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"D/nais : $dateN",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"D/nais : $dateN",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"D/nais : $dateN",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"D/nais : $dateN",0,1,L);

	    // Nom

	    $pdf->Cell($largeur_etiquette-30,4,"Nom : ",0,0,L);
	    $pdf->SetFont('times','B',9);
	    $pdf->Cell(43,4,strtoupper($nom),0,0,L);
	    $pdf->SetFont('times','',9);
	    $pdf->Cell($largeur_etiquette-30,4,"Nom : ",0,0,L);
	    $pdf->SetFont('times','B',9);
	    $pdf->Cell(43,4,strtoupper($nom),0,0,L);
	    $pdf->SetFont('times','',9);
	    $pdf->Cell($largeur_etiquette-30,4,"Nom : ",0,0,L);
	    $pdf->SetFont('times','B',9);
	    $pdf->Cell(43,4,strtoupper($nom),0,0,L);
	    $pdf->SetFont('times','',9);
	    $pdf->Cell($largeur_etiquette-30,4,"Nom : ",0,0,L);
	    $pdf->SetFont('times','B',9);
	    $pdf->Cell(43,4,strtoupper($nom),0,0,L);
	    $pdf->SetFont('times','',9);
	    $pdf->Cell($largeur_etiquette-30,4,"Nom : ",0,0,L);
	    $pdf->SetFont('times','B',9);
	    $pdf->Cell(43,4,strtoupper($nom),0,1,L);
	    $pdf->SetFont('times','',9);

	    // Prénom
	    $pdf->Cell($largeur_etiquette,4,"Prénom : ".ucfirst(strtolower($prenom)),0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Prénom : ".ucfirst(strtolower($prenom)),0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Prénom : ".ucfirst(strtolower($prenom)),0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Prénom : ".ucfirst(strtolower($prenom)),0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Prénom : ".ucfirst(strtolower($prenom)),0,1,L);

	    // NJF
	    $pdf->Cell($largeur_etiquette,4,"NJF : ",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"NJF : ",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"NJF : ",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"NJF : ",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"NJF : ",0,1,L);

	    // N° SS
	    $pdf->Cell($largeur_etiquette,4,"N° SS : ",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"N° SS : ",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"N° SS : ",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"N° SS : ",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"N° SS : ",0,1,L);

	    // inter _etiquettes
	    $pdf->Cell(1,11,"",0,1);
	}


	$pdf->Output();
    }

    // Génération des étiquettes pour Pertuis
    function genCarpentras2 ( ) {
	global $options ;

	# paramètres
	$mg=3; // Marge de gauche =>  initial 17
	$mh=13; // Marge du haut =>  initial 9
	$md=0; // Marge de droite
	$mb=11; // Marge du bas

	$largeur_etiquette=70; // largeur_etiquette =>  initial 60
	$espace_etiquettes=0; // =>  initial 7
	$nb_ligne_etiquettes=11;
	$nb_etiquette_ligne=3;
	$hauteur_etiquette=25;

	// Préparation du document PDF.
	$pdf=new FPDF('P','mm','A4');
	$pdf->Open();
	$pdf->SetLeftMargin($mg);
	$pdf->SetTopMargin($mh);
	$pdf->SetAutoPageBreak( 1 ,0);
	$pdf->AddPage();
	// Gestion des fonts
	$pdf -> AddFont ( 'code39', '', 'IDAutomation_Code_39.php' ) ;
	$pdf->SetFont('times','',12);

	// Préparation des informations.
	$nom = strtoupper($this->patient->getNom()) ;
	$prenom = strtoupper($this->patient->getPrenom()) ;
	$date = new clDate ( $this->patient->getDateNaissance ( ) ) ;
	$dateN = $date->getDate ( "d/m/Y") ;
	if ( $this->patient->getSexe ( ) == "F" ) {
	    $sexe = "F" ;
	    $e = "e" ;
	} elseif ( $this->patient->getSexe ( ) == "M" ) {
	    $sexe = "M" ;
	    $e = "" ;
	} else {
	    $sexe = "Ind." ;
	    $e = "" ;
	}
	$date -> setDate ( $this->patient->getDateAdmission ( ) ) ;
	$led = $date -> getDate ( "d/m/Y" ) ;
	$ipp = $this->patient->getILP ( ) ;
	$nsej = $this->patient->getNSej ( ) ;
	$uf = $this->patient->getUF ( ) ;

	for ($i = 0; $i < $nb_ligne_etiquettes; $i++) {
	    for ($j = 0; $j < $nb_etiquette_ligne; $j++) {
		$l = $j*$largeur_etiquette + $mg;
		$h = $i*$hauteur_etiquette + $mh;
		$le = $largeur_etiquette - $mg;
		$pdf->setY($h);
		$pdf->setX($l);
		$pdf->setFont('times', '', 9 ) ;
		$pdf->Cell($le,4,"Dossier : ",0,0,L);
		$pdf->setY($h);$pdf->setX($l+17);$pdf->setFont('times','b',9);

		$pdf->cell($le - 10, 4, $nsej, 0, 0, L);
		//$pdf->setFont('times', '', 9 ) ;
		$pdf->setY($h);
		$pdf->setX($l);
		$pdf->cell($le - 10, 4, "U", 0, 0, R);


		$pdf->setY($h + 4);
		$pdf->setX($l);
		$pdf->setFont('times', '', 9 ) ;
		$pdf->cell($le,4,$nom . " " . $prenom,0,0,L);

		$pdf->setY($h + 9);
		$pdf->setX($l);
		$pdf->cell($le,4,"Né$e le : $dateN",0,0,L);

		$pdf->setY($h + 9);
		$pdf->setX($l);
		$pdf->cell($le - 10, 4, "Sexe : $sexe", 0, 0, R);

		$pdf->setY($h + 13);
		$pdf->setX($l);
		$pdf->cell($le,4,"Le : $led",0,0,L);

		$pdf->setY($h + 13);
		$pdf->setX($l);
		$pdf->cell($le - 10, 4, "UF : $uf", 0, 0, R);

		$pdf->setY($h + 17);
		$pdf->setX($l);
		$pdf->cell($le - 10, 4, "NIP : $ipp", 0, 0, L);
	    }
	}


	$pdf->Output();
    }

    // Génération des étiquettes pour Salon
    function genSalon ( ) {
	global $options ;

	# paramètres
	$mg=5; // Marge de gauche =>  initial 17
	$mh=4; // Marge du haut =>  initial 9
	$md=15; // Marge de droite
	$mb=9; // Marge du bas

	$largeur_etiquette=42; // largeur_etiquette =>  initial 60
	$espace_etiquettes=13; // =>  initial 7
	$nb_ligne_etiquettes=4;
	$nb_etiquette_ligne=5;

	// Préparation du document PDF.
	$pdf=new FPDF('L','mm','A4');
	$pdf->Open();
	$pdf->SetLeftMargin($mg);
	$pdf->SetTopMargin($mh);
	$pdf->SetAutoPageBreak( 1 ,0);
	$pdf->AddPage();
	// Gestion des fonts
	$pdf -> AddFont ( 'code39', '', 'IDAutomation_Code_39.php' ) ;
	$pdf->SetFont('times','',12);

	// Préparation des informations.
	$nom = strtoupper($this->patient->getNom()) ;
	$prenom = strtoupper($this->patient->getPrenom()) ;
	$date = new clDate ( $this->patient->getDateNaissance ( ) ) ;
	$duree = new clDuree ( ) ;
	$dateN = $date->getDate ( "d/m/Y") ;
	$dateN .= " (".$duree->getAge ( $date->getTimestamp ( ) ).")" ;
	if ( $this->patient->getSexe ( ) == "F" ) {
	    $sexe = "Féminin" ;
	    $e = "e" ;
	} elseif ( $this->patient->getSexe ( ) == "M" ) {
	    $sexe = "Masculin" ;
	    $e = "" ;
	} else {
	    $sexe = "Indéterminé" ;
	    $e = "" ;
	}
	$date -> setDate ( $this->patient->getDateAdmission ( ) ) ;
	$le = $date -> getDate ( "d/m/Y H:i" ) ;
	$led = $date -> getDate ( "d/m/Y" ) ;
	$leh = $date -> getDate ( "H:i" ) ;
	$ipp = $this->patient->getILP ( ) ;
	$nsej = $this->patient->getNSej ( ) ;
	$uf = $this->patient->getUF ( ) ;
	$sexe = $this->patient->getSexe ( ) ;
	if ( $uf == $options->getOption ( 'numUFexec' ) ) $loc = '(URGENCES)' ; else $loc = '(UHCD)' ;
	$tel = $this->patient->getTel ( ) ;
	$adresse = $this->patient->getAdresse ( ) ;
	$cpv = $this->patient->getCodePostal ( ). " " . $this->patient->getVille ( ) ;
	$prev = $this->patient->getPrevenir ( ) ;
	$medt = $this->patient->getMedecinTraitant ( ) ;
	$modet = $this->patient->getMedecinTraitant ( ) ;
	$adresseur = $this->patient->getAdresseur ( ) ;
	$modeadm = $this->patient->getModeAdmission ( ) ;


	// Grosse étiquette
	$pdf->Cell(20,4,"",0,0,L);
	// Ligne 1
	$pdf->Cell(4*$largeur_etiquette,5,"CENTRE HOSPITALIER DE CARPENTRAS",0,1,L);
	// Ligne 2
	$pdf->Cell(11,5,"Date : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(23,5,"$led",0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(13,5,"Heure : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(27,5,"$leh",0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(21,5,"N° Patient : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(22,5,"$ipp",0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(18,5,"N° URG : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(22,5,"$nsej",0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(18,5,"N° ARCHIVE : ",0,1,L);
	// Ligne 3
	$pdf->Cell(11,5,"Nom : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(63,5,strtoupper($nom),0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(13,5,"Nom naissance : ",0,1,L);
	// Ligne 4
	$pdf->Cell(16,5,"Prénom : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(58,5,ucfirst(strtolower($prenom)),0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(12,5,"Sexe : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(29,5,"$sexe",0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(12,5,"N° S.S : ",0,1,L);
	// Ligne 5
	$pdf->Cell(17,5,"Né(e) le : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(57,5,$dateN,0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(12,5,"A : ",0,1,L);
	// Ligne 6
	$pdf->Cell(17,4,"Adresse : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(57,4,$adresse,0,1,L);
	$pdf->SetFont('times','',12);
	// Ligne 5
	$pdf->Cell(115,4,"",0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(9,4,"Tél : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(20,4,$tel,0,1,L);
	$pdf->SetFont('times','',12);
	// Ligne 6
	$pdf->Cell(17,4,"",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(98,4,$cpv,0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(9,4,"Pays : ",0,1,L);
	// Ligne 7
	$pdf->Cell(17,5,"Nom du tuteur : ",0,1,L);
	// Ligne 8
	$pdf->Cell(37,5,"Personne à prévenir : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(98,5,$prev,0,1,L);
	$pdf->SetFont('times','',12);
	// Ligne 9
	$pdf->Cell(23,5,"Adressé par : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(51,5,$adresseur,0,0,L);
	$pdf->SetFont('times','',12);
	$pdf->Cell(30,5,"Médecin traitant : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(98,5,$medt,0,1,L);
	$pdf->SetFont('times','',12);
	// Ligne 10
	$pdf->Cell(34,5,"Mode de transport : ",0,0,L);
	$pdf->SetFont('times','B',12);
	$pdf->Cell(98,5,$modeadm,0,1,L);
	$pdf->SetFont('times','',12);

	// Saut
	$pdf->Cell(1,5.8,"",0,1);
	$pdf->SetFont('times','',9);
	for ($i = 1; $i <= $nb_ligne_etiquettes; $i++) {
	// Entrée
	    $pdf->Cell($largeur_etiquette,4,"Entrée du : $led à $leh",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Entrée du : $led à $leh",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Entrée du : $led à $leh",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Entrée du : $led à $leh",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Entrée du : $led à $leh",0,1,L);

	    // D/Nais
	    $pdf->Cell($largeur_etiquette,4,"D/nais : $dateN",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"D/nais : $dateN",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"D/nais : $dateN",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"D/nais : $dateN",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"D/nais : $dateN",0,1,L);

	    // Nom

	    $pdf->Cell($largeur_etiquette-30,4,"Nom : ",0,0,L);
	    $pdf->SetFont('times','B',9);
	    $pdf->Cell(43,4,strtoupper($nom),0,0,L);
	    $pdf->SetFont('times','',9);
	    $pdf->Cell($largeur_etiquette-30,4,"Nom : ",0,0,L);
	    $pdf->SetFont('times','B',9);
	    $pdf->Cell(43,4,strtoupper($nom),0,0,L);
	    $pdf->SetFont('times','',9);
	    $pdf->Cell($largeur_etiquette-30,4,"Nom : ",0,0,L);
	    $pdf->SetFont('times','B',9);
	    $pdf->Cell(43,4,strtoupper($nom),0,0,L);
	    $pdf->SetFont('times','',9);
	    $pdf->Cell($largeur_etiquette-30,4,"Nom : ",0,0,L);
	    $pdf->SetFont('times','B',9);
	    $pdf->Cell(43,4,strtoupper($nom),0,0,L);
	    $pdf->SetFont('times','',9);
	    $pdf->Cell($largeur_etiquette-30,4,"Nom : ",0,0,L);
	    $pdf->SetFont('times','B',9);
	    $pdf->Cell(43,4,strtoupper($nom),0,1,L);
	    $pdf->SetFont('times','',9);

	    // Prénom
	    $pdf->Cell($largeur_etiquette,4,"Prénom : ".ucfirst(strtolower($prenom)),0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Prénom : ".ucfirst(strtolower($prenom)),0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Prénom : ".ucfirst(strtolower($prenom)),0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Prénom : ".ucfirst(strtolower($prenom)),0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"Prénom : ".ucfirst(strtolower($prenom)),0,1,L);

	    // NJF
	    $pdf->Cell($largeur_etiquette,4,"NJF : ",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"NJF : ",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"NJF : ",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"NJF : ",0,0,L);
	    $pdf->Cell($espace_etiquettes,4,"",0,0,L);
	    $pdf->Cell($largeur_etiquette,4,"NJF : ",0,1,L);

	    // N° SS
	    $pdf->Cell(9,4,"NSej : ",0,0,L);
	    $pdf->Cell(46,4,"$nsej",0,0,L);
	    $pdf->Cell(9,4,"NSej : ",0,0,L);
	    $pdf->Cell(46,4,"$nsej",0,0,L);
	    $pdf->Cell(9,4,"NSej : ",0,0,L);
	    $pdf->Cell(46,4,"$nsej",0,0,L);
	    $pdf->Cell(9,4,"NSej : ",0,0,L);
	    $pdf->Cell(46,4,"$nsej",0,0,L);
	    $pdf->Cell(9,4,"NSej : ",0,0,L);
	    $pdf->Cell(46,4,"$nsej",0,1,L);


	    // inter _etiquettes
	    $pdf->Cell(1,11,"",0,1);
	}


	$pdf->Output();
    }

}

?>