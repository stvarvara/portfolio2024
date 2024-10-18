<?php
require "../utils.php";
session_start();
$id_client = client_connected_or_redirect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //Récupérer toutes les variables
    $id_logement = $_POST["id_logement"];
    $date_debut = $_POST["dateDebut"];
    $date_fin = $_POST["dateFin"];
    $nb_occupant = $_POST["nombre_personnesDevis"];
    $taxe_sejour = $_POST["taxe"];
    $taxe_commission = ($_POST["frais"]);
    $taxe_comission_numeric = str_replace(',', '.', $taxe_commission);
    $prix_ht = $_POST["prix_ht"];
    $prix_ht_numeric = str_replace(',', '.', $prix_ht);
    $prix_ht_numeric = str_replace(' ', '', $prix_ht_numeric);
    $prix_ttc = $prix_ht_numeric * 1.1;
    $prix_total = floatval($prix_ttc) + floatval($taxe_comission_numeric) + floatval($taxe_sejour);
    $date_devis = date('Y-m-d');
    $nb_nuit = $_POST["nb_nuit"];


    // Requête pour vérifier les chevauchements dans la table _devis
    $sql_devis = 'SELECT * FROM sae._devis d';
    $sql_devis .= ' WHERE d.id_logement = ' . intval($id_logement);
    $sql_devis .= " AND ((d.date_debut >= '$date_debut' AND d.date_debut < '$date_fin') OR (d.date_fin > '$date_debut' AND d.date_fin <= '$date_fin'))";

    // Requête pour vérifier les chevauchements dans la table _reservation
    $sql_reservation = 'SELECT * FROM sae._reservation r';
    $sql_reservation .= ' WHERE r.id_logement = ' . intval($id_logement);
    $sql_reservation .= " AND ((r.date_debut >= '$date_debut' AND r.date_debut < '$date_fin') OR (r.date_fin > '$date_debut' AND r.date_fin <= '$date_fin'))";


    $devis = request($sql_devis, false);
    $reservation = request($sql_reservation, false);

    $taxe_comission_bdd = str_replace(',', '.', $taxe_commission);
    $taxe_sejour_bdd = str_replace(',', '.', $taxe_sejour);
    $prix_total_bdd = str_replace(',', '.', $prix_total);

    $prix_ttc_pour_les_nuits_bdd = str_replace(',', '.', $prix_ht_numeric * 1.1);


    //Si pas de réservation ni de devis on créer le devis
    if(count($devis)==0 && count($reservation)==0){
        $table = 'sae._devis';
        $columns = [
            'id_logement',
            'id_client',
            'date_devis',
            'date_debut',
            'date_fin',
            'nb_occupant',
            'taxe_sejour',
            'taxe_commission',
            'prix_ht',
            'prix_ttc',
            'prix_total'
        ];

        $values = [
            intval($id_logement),
            intval($id_client),
            $date_devis,
            $date_debut,
            $date_fin,
            floatval($nb_occupant),
            (float)$taxe_sejour_bdd,
            (float)$taxe_comission_bdd,
            (float)$prix_ht_numeric,
            (float)$prix_ttc_pour_les_nuits_bdd,
            floatval($prix_total_bdd)
        ];

        $insert_devis = insert($table, $columns, $values);
        $_SESSION["id_devis_en_cours"] = $insert_devis;
        $begin = new DateTime($date_debut);
        $end = new DateTime($date_fin);
        $end = $end->modify('+1 day');

        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval, $end);

        foreach ($daterange as $date) {
            $d = $date->format('Y-m-d');
            $sql = "INSERT INTO sae._calendrier (date, id_logement, statut, prix) VALUES ('$d', $id_logement, 'D', 0.0)
                ON CONFLICT (date, id_logement) DO UPDATE SET statut = 'D'";

            request($sql);
        }
    } else{
        if (isset($_SESSION["id_devis_en_cours"])){
            $insert_devis = intval($_SESSION["id_devis_en_cours"]);
        } else {
            header('Location: index.php');
            die;
        }
        //Message d'erreur car date indispo surement
    }
}

$sql = "SELECT d.*, u.photo_profile 
        FROM sae._devis d
        INNER JOIN sae._logement l ON l.id = d.id_logement
        INNER JOIN sae._utilisateur u ON u.id = l.id_proprietaire
        WHERE d.id = '$insert_devis'";
$reservation = request($sql, true);
// Vérification que la reservation existe puis qu'elle est bien associé au client connecté
if (!$reservation) {
    // // Redirection vers la page des réservations
    // header('Location: mes_reserv.php');
    // die();
}
// On recupère les données à afficher
else {
    /**
     * Fonction qui permet de modifier un numéro de mois en abréviation
     * 
     * param $date
     * resultat : String
     */
    function mois($date = null)
    {

        // Définir le tableau associatif des mois
        $arrayMonth = [
            1 => "jan", 2 => "fév", 3 => "mars",
            4 => "avr", 5 => "mai", 6 => "juin",
            7 => "juill", 8 => "août", 9 => "sept",
            10 => "oct", 11 => "nov", 12 => "dec"
        ];
        if (array_key_exists($date, $arrayMonth)) {
            $mois = $arrayMonth[$date];
        } else {
            return false;
        }
        return $mois;
    }

    $date1 = date_parse($reservation["date_debut"]);
    $date2 = date_parse($reservation["date_fin"]);
    $moisEnLettreDebut = mois($date1['month']);
    $moisEnLettreFin = mois($date2['month']);
    // Formation de la chaine du titre de la réservation 
    if ($moisEnLettreDebut != false && $moisEnLettreFin != false) {

        $dateReservation = $date1['day'] . " " . $moisEnLettreDebut . ". " . $date1['year'] . " - " . $date2['day'] . " " . $moisEnLettreFin . ". " . $date2['year'];
    }
    //  Récupération des valeurs du logement et propriétaire

    $idLogement = $reservation["id_logement"];
    $sql = "SELECT * from sae._logement where id=$idLogement";
    $logement = request($sql, true);
    $sql = "SELECT * from sae._utilisateur where id=$logement[id_proprietaire]";
    $proprio  = request($sql, true);
    $sql = "SELECT * from sae._adresse where id=$logement[id_adresse]";
    $adresse = request($sql, true);

    $sql = "SELECT * from sae._reservation_prix_par_nuit WHERE id_reservation=$reservation[id]";

    // Calcul et formatage des différents prix de la réservation

    $prixParNuit = request($sql, true);

    $prixHTTNuit = number_format(round($reservation["prix_ht"] / $nb_nuit, 2), 2, ",", "");

    $prixTTCnuit = number_format(round(($reservation["prix_ht"] / $nb_nuit) * 1.1, 2), 2, ",", "");
    $prixTTCnuit_numeric = number_format(round(($reservation["prix_ht"] / $nb_nuit) * 1.1, 2), 2, ".", "");
    $prixTTCnuit_numeric = str_replace(' ', '', $prixTTCnuit_numeric);
    $reservationPrixTTC = number_format($prixTTCnuit_numeric * $nb_nuit, 2, ",", "");

    $taxeSejour = number_format($reservation["taxe_sejour"], 2, ",", "");

    $total =  number_format($reservation["prix_total"], 2, ",", "");

    $comission = number_format($reservation["taxe_commission"], 2, ",", "");

    // Coordonnées de la réservation pour la carte
    $latitude = $adresse["latitude"];
    $longitude = $adresse["longitude"];

    $bis = $adresse["rep"] ?? "";
    $adresseRue = $adresse["numero"] . " " . $bis . " " . $adresse["nom_voie"] . ", " . $adresse["code_postal"] . ", " . $adresse["commune"];

    $sql = "SELECT * from sae._image where id_logement=$logement[id] and principale=true";

    $images = request($sql, true);

    // Langues maîtrisées par le propriétaire

    $sql = "SELECT *
        FROM sae._langue_proprietaire
        INNER JOIN sae._langue ON sae._langue_proprietaire.id_langue = sae._langue.id
        WHERE sae._langue_proprietaire.id_proprietaire = $proprio[id]";

    $languesProprio = request($sql, false);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/detailsreserv.css">

    <script src="https://kit.fontawesome.com/7f17ac2dfc.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <title>Devis</title>
</head>

<body>
    <div class="wrapper">
        <?php require_once "header.php" ?>
        <main class="main__container">
            <div class="detail-reservation__conteneur">
                <!--Haut de page des détails de la reservation selectionnée -->
                <div class="detail-reservation__entete">
                    <div>
                        <h1 class="entete__titre">Devis</h1> <!-- Récupérer la date -->
                    </div>
                    <div>
                        <div class="entete__textinfo1">
                            <p>Fait le</p>
                            <span class="couleur">16/04/2024</span>
                        </div>
                    </div>
                </div>
                <!-- Contenu principal des informations de la réservation -->
                <div class="detail-reservation__contenu">
                    <div class="detail-reservation__section1">
                        <img src="<?= htmlspecialchars("img" . $images['src']) ?>">
                        <div class="section1__article">
                            <div class="article__title">
                                <p class="gras"><?= $logement["titre"] ?></p>
                                <p class="gras"><?= $dateReservation ?></p>
                            </div>
                            <p><span class="gras">Hôte: </span><?= $proprio['prenom'] ?></p>
                            <p>
                        </div>

                    </div>
                    <div class="separation">
                    </div>
                    <div class="detail-reservation__section3">
                        <h3 class="section3__titre">Informations importantes</h3>
                        <div class="section3__informationoccupation">
                            <div class="informationoccupation__detail">
                                <div>
                                    <p class="gras">Arrivée</p>
                                    <p><?= (new DateTime($reservation["date_debut"]))->format('d/m/Y') ?>
                                    <p>
                                </div>
                                <div class="separation2"></div>
                                <div>
                                    <p class="gras">Départ</p>
                                    <p><?= (new DateTime($reservation["date_fin"]))->format('d/m/Y') ?></p>
                                </div>
                                <div class="separation2"></div>
                                <div>
                                    <p class="gras">Nombre de nuits</p>
                                    <p><?= $nb_nuit ?></p>
                                </div>
                                <div class="separation2"></div>
                                <div>
                                    <p class="gras">Occupant</p>
                                    <p><?= $reservation["nb_occupant"] ?></p>
                                </div>
                            </div>
                            <div class="informationoccupation__prix">
                                <p class=""><?= $prixTTCnuit ?>€ TTC par nuit </p>
                            </div>
                        </div>
                    </div>
                    <div class="separation">
                    </div>
                    <div class="detail-reservation__section4">
                        <h3 class="gras">Tarifs</h3>
                        <div class="partiemontant">
                            <div class="partiemontant__sanssoustitre">
                                <p>Location HT par nuit</p>
                                <p class="texteGras"><?= $prixHTTNuit ?> €</p>
                            </div>
                            <div class="partiemontant__soustitre">
                                <div>
                                    <p>Location TTC par nuit </p>
                                    <p class="texteGras"><?= $prixTTCnuit ?> €</p>
                                </div>
                                <p>TVA 10%</p>
                            </div>
                            <div class="partiemontant__soustitre">
                                <div>
                                    <p>Location TTC</p>
                                    <p class="texteGras"><?= $reservationPrixTTC ?> €</p>
                                </div>
                                <p>Prix par nuit TTC × <?= $nb_nuit ?> nuits</p>
                            </div>
                            <div class="partiemontant__soustitre">
                                <div>
                                    <p>Frais supplémentaires</p>

                                </div>
                                <p>Services supplémentaires d'hébergement</p>
                            </div>
                            <div class="partiemontant__sanssoustitre">
                                <p>Taxe de séjour (1€ × nuits x occupant)</p>
                                <p class="texteGras"><?= $taxeSejour ?> €</p>
                            </div>
                            <div class="partiemontant__sanssoustitre">
                                <p>1% de la commission de la plateforme</p>
                                <p class="texteGras"><?= $comission ?> €</p>
                            </div>
                            <div class="montantFinal">
                                <p>Montant Final TTC</p>
                                <p class="texteGras"><?= $total ?> €</p>
                            </div>

                        </div>
                    </div>
                    <div class="separation">
                    </div>
                    <div class="detail-reservation__section5">
                        <h3 class="texteGras">Informations supplémentaires</h3>
                        <p>Veuillez noter que ce montant total n'inclut pas les suppléments (par exemple, les lits d'appoint).</p>
                        <br>
                        <p>En cas de non-présentation ou d'annulation, une partie du montant est retenu si le délai de prévenance fixé par le vendeur dans les <a href="" class="couleur">conditions d’annulation</a> n’est pas respecté (le montant retenu peut être l’intégralité de la somme prépayée). Le montant non retenu est reversé immédiatement.</p>
                    </div>
                </div>
                <div class="info_fin_page">
                </div>
            </div>
                <div class="button_accept_devis">
                            <a href="detail_logement.php?id_devis_refus=<?php echo $insert_devis ?>&id=<?php echo $id_logement ?>">
                                <input type="button" id="declineButton" value="Refuser">
                            </a>
                            <!-- Corriger le passage de nb nuit en param si le temps-->
                            <a href="detail_reservation.php?id_devis=<?php echo $insert_devis; ?>&nb_nuit=<?php echo $nb_nuit ?>">
                                <input type="submit" name="acceptButton" id="acceptButton" value="Accepter">
                            </a>
                </div>
        </main>
        <?php require_once "footer.php" ?>
    </div>
    
</body>
</html>
