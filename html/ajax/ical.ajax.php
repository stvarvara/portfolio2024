<?php

require_once '../../utils.php';

$action = $_GET['action'] ?? '';

if ($action === 'update') {
    $data = array();
    $all_logements = array();
    $token = $_GET['token'];

    
    $sql = "SELECT it.date_debut, it.date_fin, proprietaire FROM sae._ical_token it WHERE it.token = '$token'";
    $date = request($sql, 1);

 
    $sql = "
        SELECT 
            l.id, l.titre, i.src 
        FROM 
            sae._ical_token_logements itl
        JOIN 
            sae._logement l ON itl.logement = l.id
        LEFT JOIN 
            sae._image i ON l.id = i.id_logement AND i.principale = true
        WHERE 
            itl.token = '$token'
    ";
    $data_logement = request($sql);

    foreach ($data_logement as $val) {
        $data[] = array(
            'titre' => $val['titre'],
            'img' => $val['src'],
            'id_logement' => $val['id'],
        );
    }

 
    $sql = "
        SELECT 
            l.id, l.titre, i.src 
        FROM 
            sae._logement l
        LEFT JOIN 
            sae._image i ON l.id = i.id_logement AND i.principale = true  
    ";
    $sql .= " WHERE id_proprietaire = {$date['proprietaire']}";
    $all_logements_result = request($sql);

    foreach ($all_logements_result as $val) {
        $all_logements[] = array(
            'titre' => $val['titre'],
            'img' => $val['src'],
            'id_logement' => $val['id'],
        );
    }
    
    
    $response = array_merge($date, ['logement' => $data], ['all_logement' => $all_logements]);


    print json_encode($response);
}

if ($action == 'create'){
    $id = $_GET['id'];
    $sql = "
        SELECT 
            l.id, l.titre, i.src 
        FROM 
            sae._logement l
        LEFT JOIN 
            sae._image i ON l.id = i.id_logement AND i.principale = true  
    ";
    $sql .= " WHERE id_proprietaire = {$id}";
    $all_logements_result = request($sql);

    foreach ($all_logements_result as $val) {
        $all_logements[] = array(
            'titre' => $val['titre'],
            'img' => $val['src'],
            'id_logement' => $val['id'],
        );
    }
    print json_encode(array('logement' => $all_logements));

}

if ($action == 'delete'){
    $token = $_GET['token'];
    
    $sql = 'DELETE FROM sae._ical_token_logements WHERE token = '  . '\'' . $token . '\'';
    
    request($sql);
    $sql = 'DELETE FROM sae._ical_token WHERE token = ' . '\'' . $token . '\'';
 
    
    request($sql);


}

?>
