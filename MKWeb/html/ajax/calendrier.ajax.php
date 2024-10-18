<?php

// on fera la requete sql ici

require_once '../../utils.php';

$id = $_GET["id"];
$id = intval($id);

// Requête pour récupérer les réservations
$sql_reservations = 'SELECT r.date_debut, r.date_fin, \'reservation\' AS type 
                     FROM sae._reservation r 
                     WHERE r.id_logement = ' . $id . ' AND r.annulation = false';

// Requête pour récupérer les devis
$sql_devis = 'SELECT d.date_debut, d.date_fin, \'devis\' AS type 
              FROM sae._devis d 
              WHERE d.id_logement = ' . $id;

// Combiner les deux requêtes avec UNION
$sql_combined = $sql_reservations . ' UNION ' . $sql_devis;

$ret = request($sql_combined);

if ($ret === false) {
    print 'Erreur requête';
} else {
    $date = array();

    foreach ($ret as $val) {
        $date[] = array($val['date_debut'], $val['date_fin']);
    }

    print json_encode($date);
}
?>