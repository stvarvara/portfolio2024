<?php

require_once '../../utils.php';

define('FRAIS',1.01);
define('TAUX',1);



$id = $_GET["id"];
$sql = 'SELECT base_tarif FROM sae._logement';
$sql .= ' WHERE id = ' . $id;
$res = request($sql,1);
$base_tarif = $res['base_tarif'];

$nombre_personne = (int) $_GET['nombrePersonne'];
$reservArrDate = new DateTime($_GET['dateDebut']);
$reservDepDate = new DateTime($_GET['dateFin']);
    
$interval = $reservArrDate->diff($reservDepDate);   
$base_tarif = $res['base_tarif'];
$jour = $interval->days + 1;
                                    

$nuit = empty($jour) ? 0 : $jour - 1;
$prix_ht = $base_tarif * (empty($nuit) ? 1 : $nuit) ;//* $nombre_personne;
$prix_ttc = $prix_ht * 1.10;
$frais = ($prix_ttc * 0.01) * 1.2;
$taxe = $nuit * $nombre_personne;



$prix_total = $prix_ttc + $frais + $taxe;

//TODO update base



//$frais = ($prix_ht * FRAIS) - $prix_ht;
//$taxe = $nuit * TAUX * $nombre_personne;
                          
//$prix_ttc = $prix_ht + $frais + $taxe;

$response = array(
    'base_tarif' =>  number_format($base_tarif,2,',',' '),
    'prix_ht' => number_format($prix_ht,2,',',' ' ),
    'prix_ttc'=>number_format($prix_ttc,2,',',' ' ),
    'frais' =>number_format($frais,2,',',' ' ),
    'taxe'=>number_format($taxe,2,',',' ' ),
    'nombre_jour' => $jour,
    'nombre_nuit'=> $nuit,
    'prix_total_ttc'=> number_format($prix_total,2,',',' ' ),
);

print json_encode($response);

?>