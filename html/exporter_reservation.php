<?php
require_once "../utils.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    if ($_GET['type'] == 1) { // Propriétaire
        $sql = "SELECT 
        sae._utilisateur.nom AS Nom_Client,
        sae._utilisateur.prenom AS Prenom_Client,
        sae._utilisateur.email AS Email_Client,
        sae._utilisateur.telephone AS Telephone_Client,
        sae._logement.titre AS Titre_Logement,
        sae._reservation.date_debut AS Date_Debut,
        sae._reservation.date_fin AS Date_Fin,
        sae._adresse.ville AS Ville
    FROM 
        sae._reservation
    INNER JOIN 
        sae._logement ON sae._reservation.id_logement = sae._logement.id
    INNER JOIN 
        sae._compte_client ON sae._reservation.id_client = sae._compte_client.id
    INNER JOIN 
        sae._utilisateur ON sae._compte_client.id = sae._utilisateur.id
    INNER JOIN
        sae._adresse ON sae._logement.id_adresse = sae._adresse.id
    WHERE 
        sae._logement.id_proprietaire = $id";
    } else { //Client
        $sql = "SELECT 
            sae._utilisateur.nom AS Nom_Proprietaire,
            sae._utilisateur.prenom AS Prenom_Proprietaire,
            sae._utilisateur.email AS Email_Proprietaire,
            sae._utilisateur.telephone AS Telephone_Proprietaire,
            sae._logement.titre AS Titre_Logement,
            sae._adresse.departement AS Departement,
            sae._adresse.ville AS Ville,
            sae._adresse.rue AS Rue,
            sae._reservation.date_debut AS Date_Debut,
            sae._reservation.date_fin AS Date_Fin
        FROM 
            sae._reservation
        INNER JOIN 
            sae._logement ON sae._reservation.id_logement = sae._logement.id
        INNER JOIN 
            sae._utilisateur ON sae._logement.id_proprietaire = sae._utilisateur.id
        INNER JOIN
            sae._adresse ON sae._logement.id_adresse = sae._adresse.id
        WHERE 
            sae._reservation.id_client = $id";
    }

    $results = request($sql);

    if ($results) {
        $filename = 'donnees.csv';

        $file = fopen($filename, 'w');
        fputcsv($file, array_keys($results[0]));
        foreach ($results as $row) {
            fputcsv($file, $row);
        }
        fclose($file);

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        readfile($filename);

        unlink($filename);
    } else {
        echo "Aucune donnée trouvée pour cet ID.";
    }
} else {
    echo "ID non spécifié dans la requête.";
}
