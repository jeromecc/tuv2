<?php

/*
*******************************************************************************************
*************                                                                 *************
*************             Tableaux de bord : graphique                        *************
*************             G�n�ration d'un graphique                           *************
*************             Damien Borel                                        *************
*************             16.10.2004 13:15                                    *************
*************                                                                 *************
*******************************************************************************************
*/
class clJpGraph {

  function arh_graph ( $titre, $xtitre, $ytitre, $ydata, $legende, $couleurs, $x, $y, $fichier, $xdata, $angle, $couleurfond, $type, $couleurfondgraph='#FFFFFF', $format='' ){
    /*
    Cette fonction trace un graphique dans un fichier temporaire.
    $titre    => C'est le titre du graphique qui sera affich�.
    $xtitre   => Nom des unit�s en abscisse.
    $ytitre   => Nom des unit�s en ordonn�es.
    $ydata    => Tableau � deux dimensions contenant les diff�rentes courbes : un tableau de tableau avec
              les coordonn�es en ordonn�e des points de chaque courbe...
    $legende  => Un tableau contenant les l�gendes des courbes.
    $couleurs => Les couleurs des courbes : L'id�al est d'en avoir autant que de courbes.
    $x        => Largeur en pixels de l'image g�n�r�e.
    $y        => Hauteur en pixels de l'image g�n�r�e.
    $fichier  => Nom du fichier g�n�r�.
    $xdata    => Tableau contenant le nom des abscisses.
    $angle    => Ici, nous avon l'angle d'inclinaison des noms des indexes en abscisse.
    $couleurfond => La couleur qui encadre le graphique.
    $type     => Trois types de graphiques sont g�r�s : line, accbar, groupbar.
    $couleurfondgraph => La couleur de fond du graphique.
    */

    include_once ("modules/jpgraph/jpgraph.php");
    include_once ("modules/jpgraph/jpgraph_log.php");
    include_once ("modules/jpgraph/jpgraph_line.php");
    include_once ("modules/jpgraph/jpgraph_bar.php");
    
    // Cr�ation du graphique.
    $graph = new Graph ( $x, $y ) ;	
    $graph -> SetScale ( "textlin" ) ;
    $graph -> SetColor ( $couleurfondgraph ) ;
    // $graph -> SetShadow ( ) ;
    // R�glage de la marge autour du graphique ( G, D, H, B ).
    $graph -> img -> SetMargin ( 55, 20, 20, 70 ) ;
    $graph -> SetMarginColor ( $couleurfond ) ;
    //    $graph->SetBackgroundImage((defined('URLIMGXHAM')?URLIMGXHAM:'images/xham.jpg'),BGIMG_FILLFRAME);

    if ( isset ( $ydata ) ) {
      if ( $type == "accbar" ) {
	// Cr�ation des diff�rentes courbes et ajout des donn�es au graph.
	for ( $i = 0 ; $i < count ( $ydata ) ; $i++ ) {
	  $barplot[$i] = new BarPlot ( $ydata[$i] ) ;
	  $barplot[$i] -> SetLegend ( $legende[$i] ) ;
	  //$barplot[$i] -> SetFillColor( $couleurs[$i] ) ;
	  $barplot[$i] -> SetFillGradient( $couleurs[$i], "lightsteelblue", GRAD_VER ) ;
	}
	$accbar = new AccBarPlot ( $barplot ) ;
	$graph -> Add ( $accbar ) ;
      } else if ( $type == "groupbar" ) {
	// Creation des diff�rentes courbes et ajout des donn�es au graph.
	$j = 0 ;
	for ( $i = 0 ; $i < count ( $ydata ) ; $i++ ) {
	  // On cr�e un histogramme seulement si le tableau contient des donn�es.
	  if ( $this->existe ( $ydata[$i] ) ) {
	    $barplot[$j] = new BarPlot ( $ydata[$i] ) ;
	    $barplot[$j] -> SetLegend ( $legende[$i] ) ;
	    //$barplot[$j] -> SetFillColor( $couleurs[$i] ) ;
	    $barplot[$j] -> SetFillGradient( $couleurs[$i], "lightsteelblue", GRAD_VER ) ;
	    if ( $format ) {
	      $barplot[$j]->value->SetFormat($format,70);
	      $barplot[$j]->value->SetFont(FF_ARIAL,FS_NORMAL,9);
	      $barplot[$j]->value->SetColor("blue");
	      $barplot[$j]->value->SetAngle(90);
	      $barplot[$j]->value->Show();
	    }
	    $j++ ;
	  }
	}
	// On v�rifie qu'il y a au moins un histogramme � tracer.
	if ( isset ( $barplot ) AND count ( $barplot ) ) {
	  $accbar = new GroupBarPlot ( $barplot ) ;
	  $graph -> Add ( $accbar ) ;
	  // Dans le cas contraire, on fait simule la pr�sence d'une courbe vide.
	} else {
	  $lineplot[0] = new LinePlot ( $ydata[0] ) ;
	  $graph -> Add ( $lineplot[0] ) ;
	}


      } else if ( $type == "line" ) {
	// Cr�ation des diff�rentes courbes et ajout des donn�es au graph.
	for ( $i = 0 ; $i < count ( $ydata ) ; $i++ ) {
	  /*print "**************************Courbe $i***********************" ;
	      newfct ( gen_affiche_tableau, $ydata[$i] ) ;    */
	  $lineplot[$i] = new LinePlot ( $ydata[$i] ) ;
	  $graph -> Add ( $lineplot[$i] ) ;
	  $lineplot[$i] -> SetWeight ( 1 ) ;
	  $lineplot[$i] -> SetLegend ( $legende[$i] ) ;
	  $lineplot[$i] -> SetColor( $couleurs[$i] ) ;
	}
      }
    }
    
    // Configuration des noms � afficher.
    $graph -> title -> SetFont ( FF_ARIAL ) ;
    $graph -> title -> Set ( "$titre" ) ;
    $graph -> xaxis -> SetFont ( FF_ARIAL ) ;
    $graph -> xaxis -> SetTickLabels ( $xdata ) ;
    $graph -> xaxis -> SetLabelAngle ( $angle ) ;
    $graph -> xaxis -> title -> SetFont ( FF_ARIAL ) ;
    $graph -> xaxis -> title -> Set ( "$xtitre" ) ;
    $graph -> yaxis -> SetFont ( FF_ARIAL ) ;
    $graph -> yaxis -> title -> SetFont ( FF_ARIAL ) ;
    $graph -> yaxis -> title -> Set ( "$ytitre" ) ;
    $graph -> legend -> Pos ( 0.02, 0.1, "right", "center" ) ;
    
    if ( isset ( $ydata ) )
      // Cr�ation de l'image dans un fichier temporaire.
	$graph -> Stroke( URLCACHE."$fichier" );
  }
  
  // Petite fonction qui retourne vrai si le tableau pass� en argument a au moins
  // un point non nul.
  function existe ( $tableau ) {
    for ( $i = 0 ; $i < count ( $tableau ) ; $i++ )
      if ( $tableau[$i] ) return $tableau[$i] ;
  }
}
  
?>