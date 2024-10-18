<?php
require "../../utils.php";
session_start();
$id_client = buisness_connected_or_redirect();
$idreservation = $_GET["id"];
$sql = "SELECT sae._reservation.*, sae._utilisateur.photo_profile FROM sae._reservation 
INNER JOIN sae._logement ON sae._logement.id = sae._reservation.id_logement 
INNER JOIN sae._utilisateur ON sae._utilisateur.id = sae._logement.id_proprietaire 
WHERE sae._reservation.id=$idreservation";
$reservation = request($sql, true);
// Vérification que la reservation existe puis qu'elle est bien associé au client connecté
// On recupère les données à afficher
    /**
     * Fonction qui permet de modifier un numéro de mois en abréviation
     * 
     * param $date
     * resultat : String
     */

     $id_client = $reservation['id_client'];
     $info_client = request("
         SELECT u.nom, u.prenom, u.telephone ,a.commune, a.code_postal, a.numero, a.nom_voie, a.pays
         FROM sae._compte_client c
         INNER JOIN sae._utilisateur u ON c.id = u.id
         INNER JOIN sae._adresse a ON u.id_adresse = a.id
         WHERE c.id = '$id_client'
     ", true);
     

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

    $prixHTTNuit = number_format(round($reservation["prix_ht"] / $prixParNuit["nb_nuit"], 2), 2, ",", "");

    $prixTTCnuit = number_format(round($reservation["prix_ttc"] / $prixParNuit["nb_nuit"], 2), 2, ",", "");

    $reservationPrixTTC = number_format($reservation["prix_ttc"], 2, ",", "");

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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/detailsreserv.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">

    <script src="https://kit.fontawesome.com/7f17ac2dfc.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <title>Detail réservation</title>
</head>
<style>
    .detail-reservation__entete{
        padding-top: 2em;
    }
</style>
<body>
    <div class="wrapper">
        <?php require_once "./header.php" ?>
        <main class="main__container">
            <div class="detail-reservation__conteneur">
                <!--Haut de page des détails de la reservation selectionnée -->
                <div class="detail-reservation__entete">
                    <div>
                        <h1 class="entete__titre">Détails de la réservation</h1>
                    </div>
                    <div>
                        <div class="entete__textinfo1">
                            <p>Numéro de confirmation : </p>
                            <span class="couleur">1</span>
                        </div>
                        <div class="entete__textinfo2">
                            <p>Numéro de réservation :</p>
                            <span class="couleur"><?= $reservation["id"] ?></span>
                        </div>
                    </div>
                </div>
                <!-- Contenu principal des informations de la réservation -->
                <div class="detail-reservation__contenu">
                    <div class="detail-reservation__section1">
                        <img src="<?= htmlspecialchars("../img" . $images['src']) ?>">
                        <div class="section1__article">
                            <div class="article__title">
                                <p class="gras"><?= $logement["titre"] ?></p>
                                <p class="gras"><?= $dateReservation ?></p>
                            </div>
                            <p><span class="gras">Adresse: </span><?= $adresseRue ?></p>
                            <p><span class="gras">Coordonnées GPS: </span> N 048° 55.849, E 02° 16.963</p>
                            <p><span class="gras">Hôte: </span><?= $proprio['prenom'] ?></p>
                            <p><span class="gras">Téléphone: </span><?= $proprio['telephone'] ?></p>
                            <p>
                        </div>

                    </div>
                    <div class="separation">
                    </div>
                    <div class="hote">
                        <div class="hote__info">
                            <img src=<?= "../img/" . $reservation["photo_profile"] ?> alt="Hôte" id="hote__photo">
                            <div class="hote__main">
                                <div class="hote__nom">
                                    <h3>Client: <span id="hote__nm"><?= $info_client['prenom'] ?></span></h3>
                                </div>
                                <div class="hote__langues">
                                    <ul>
                                        <li><?php echo $info_client["telephone"] ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="asavoir">
                            <h2>À savoir</h2>
                            <a href="">Conditions de séjour dans сe logement</a>
                            <h3>Moyens de paiement acceptés : PayPal, carte bancaire</h3>
                        </div>
                        <div class="section2__article3" id="localisation">
                            <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
                        </div>
                        <!-- Carte qui situe la réservation -->
                        <script>
                            var lat = "<?php echo $latitude; ?>";
                            var lng = "<?php echo $longitude; ?>";
                            afficherCommuneSurMap(lat, lng);

                            function afficherCommuneSurMap(lat, lng) {
                                var map = L.map('localisation').setView([lat, lng], 9);
                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    attribution: '© OpenStreetMap contributors'
                                }).addTo(map);

                                L.marker([lat, lng]).addTo(map).bindPopup('Le logement est ici !');
                            }
                        </script>
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
                                    <p><?= $prixParNuit["nb_nuit"] ?></p>
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
                                <p>Prix par nuit TTC × <?= $prixParNuit["nb_nuit"] ?> nuits</p>
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
            <div class="telecharger">
                <img src="../img/downloads.webp" alt="Download" id="downloadImage">
                <p>
                    La version imprimable de votre confirmation contient toutes les informations importantes de votre réservation. Elle peut être utilisée lors de votre arrivée dans le logement. <br><br>
                    Pour la télécharger, <span class="couleur" id="downloadLink">cliquez ici.</span>
                </p>
            </div>
        </main>
        <?php require_once "./footer.php" ?>
    </div>
    <div id="pdf-content" hidden>
        <div class="container" id="container">
            <div class="invoice" id="invoice">
                <div class="row">
                    <div class="col-7">
                        <div class="header__logo">
                            <a href="./index.php" class="header__name">ALHaiZ Breizh</a> <br />
                            <p>
                            alhaizbreizh.contact@alhaizbreizh.com<br />
                            1 rue Edouard Branly<br />
                            22300 Lannion
                        </p>
                        </div>
                    </div>
                    <div class="col-5">
                        <h1 class="document-type display-4">FACTURE</h1>
                        <p class="text-right"><strong>Référence facture : <?php echo $reservation["id"] ?></strong></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-7">
                    </div>
                    <div class="col-5">
                        <p class="addressDriver">
                            <strong>Client</strong><br />
                            Réf. Client <em><?php echo $id_client ?></em><br />
                            <?php echo $info_client["nom"] . " " . $info_client["prenom"] ?><br />
                            <?php echo $info_client["numero"] . " " . $info_client["nom_voie"] ?><br />
                            <?php echo $info_client["commune"] . " " . $info_client["code_postal"] . " " . $info_client["pays"] ?>
                        </p>
                    </div>
                </div>
                <p class="addressDriver">
                            <strong>Propriétaire</strong><br />
                            Réf. Propriétaire <em><?php echo $proprio["id"]?></em><br />
                            <?php echo $proprio["nom"] . " " . $proprio["prenom"] ?><br />
                            <?php echo $proprio["telephone"] ?> <br />
                            <?php echo $proprio["email"] ?>
                </p>
                <br />
                <br />
                <p class="logement">Logement <em>(Ref. <?php echo $logement["id"] ?>)</em> : <strong> <?php echo $logement["titre"] ?> </strong><br />
                <?php echo $dateReservation?> - Nombre d'occupant : <strong><?php echo $reservation["nb_occupant"] ?></strong><br />
                     <?php echo $adresseRue ?>
                </p>
                <br />
                <br />
                <br />
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>TVA</th>
                            <th class="text-right">Total HT</th>
                            <th class="text-right">Total TTC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Nuitée</td>
                            <td>10%</td>
                            <?php $total_HT = floatval($prixHTTNuit) * floatval($prixParNuit["nb_nuit"])?>
                            <td class="text-right"><?php echo $total_HT?> €</td>
                            <td class="text-right"><?php echo $reservationPrixTTC ?>€</td>
                        </tr>
                        <tr>
                            <td>Frais (1%) </td>
                            <td>20%</td>
                            <?php $comission_HT = floatval($comission) * 0.8 ?>
                            <td class="text-right"><?php echo $comission_HT ?> €</td>
                            <td class="text-right"><?php echo $comission?> €</td>
                        </tr>
                        <tr>
                            <td>Taxe de séjour</td>
                            <td> - </td>
                            <td class="text-right"> - </td>
                            <td class="text-right"> <?php echo $taxeSejour ?></td>
                        </tr>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-8"></div>
                    <div class="col-4">
                        <table class="table table-sm text-right">
                            <tr>
                                <td><strong>Total HT</strong></td>
                                <td class="text-right"><?php echo $total_HT ?> €</td>
                            </tr>
                            <tr>
                                <td>TVA 20%</td>
                                <?php $TVA_total = (floatval($reservationPrixTTC) - floatval($total_HT)) + (floatval($comission) - floatval($comission_HT))?>
                                <td class="text-right"><?php echo $TVA_total ?> €</td>
                            </tr>
                            <tr>
                                <td><strong>Total TTC</strong></td>
                                <td class="text-right"><?php echo $total ?> €</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const downloadImage = document.getElementById("downloadImage");
        const downloadLink = document.getElementById("downloadLink");

        function generatePDF() {
            var pdfContent = document.getElementById("pdf-content").innerHTML;
            var windowObject = window.open();

            windowObject.document.write('<html><head><title>Invoice</title>');
            windowObject.document.write('<style>');
            windowObject.document.write('body { background: #ccc; padding: 30px; font-size: 0.9em;  font-family: "Plus Jakarta San", sans-serif;}');
            windowObject.document.write('  { font-size: 1em; }');
            windowObject.document.write('.row { display: flex; flex-wrap: nowrap; }');
            windowObject.document.write('.col-7, .col-5, .col-8, .col-4 { position: relative; width: 100%; }');
            windowObject.document.write('.col-7 { flex: 0 0 58.333333%; max-width: 58.333333%; }');
            windowObject.document.write('.col-5 { flex: 0 0 41.666667%; max-width: 41.666667%; }');
            windowObject.document.write('.col-8 { flex: 0 0 66.666667%; max-width: 66.666667%; }');
            windowObject.document.write('.col-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }');
            windowObject.document.write('.logo { width: 4cm; }');
            windowObject.document.write('.document-type { text-align: right; color: #444; }');
            windowObject.document.write('.conditions { font-size: 0.7em; color: #666; }');
            windowObject.document.write('.bottom-page { font-size: 0.7em; }');
            windowObject.document.write('.header__logo { display: flex; flex-direction: column;}');
            windowObject.document.write('.header__name { color: #5669FF; text-transform: lowercase; font-size: 2em; letter-spacing: 3px; font-weight: 700; }');
            windowObject.document.write('table { width: 100%; margin-bottom: 1rem; color: #212529; border-collapse: collapse; }');
            windowObject.document.write('.table th, .table td { padding: 0.75rem; vertical-align: top; border-top: 1px solid #dee2e6; }');
            windowObject.document.write('.table td { text-align: center;}');
            windowObject.document.write('.table-striped tbody tr:nth-of-type(odd) { background-color: rgba(0, 0, 0, 0.05); }');
            windowObject.document.write('.text-right { text-align: right !important; }');
            windowObject.document.write('.table-sm td, .table-sm th { padding: 0.3rem; }');
            windowObject.document.write('.logement {line-height: 1.5 }');
            windowObject.document.write('@page { margin: 0; }')
            windowObject.document.write('</style>');
            windowObject.document.write('</head><body>');
            windowObject.document.write(pdfContent);
            windowObject.document.write('</body></html>');

            windowObject.document.close();
            windowObject.focus();
            windowObject.print();
            windowObject.close();
        }

        downloadImage.addEventListener("click", generatePDF);
        downloadLink.addEventListener("click", generatePDF);
    });
</script>