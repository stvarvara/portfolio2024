<?php
session_start();
require_once "../utils.php";

// Vérification de la session et récupération de l'ID client connecté
$id = client_connected_or_redirect();

// Requête SQL pour récupérer les réservations du client
$query = "SELECT sae._reservation.id, sae._reservation.id_logement, sae._reservation.date_annulation, sae._reservation.prix_total, sae._logement.titre, sae._reservation.date_debut, sae._reservation.date_fin, sae._adresse.commune, img.*
FROM sae._reservation 
INNER JOIN sae._logement ON sae._reservation.id_logement = sae._logement.id
INNER JOIN sae._adresse ON sae._logement.id_adresse = sae._adresse.id
INNER JOIN sae._image img ON sae._reservation.id_logement = img.id_logement AND img.principale = true
WHERE id_client = $id";

// Exécution de la requête SQL pour obtenir les résultats
$results = request($query, false);
// Fonction pour récupérer le statut de la réservation et sa classe de couleur
function getColorChipForDate($dateD, $dateF,$dateA)
{
    global $current_date;

    if ($dateA === null && $current_date < $dateD) {
        $status = "À venir";
        $status_class = "green";
        $status_short = "venir";
    } elseif ($dateA !== null) {
        $status = "Annulée";
        $status_class = "red";
        $status_short = "annu";
    } elseif($current_date >= $dateD && $current_date <= $dateF) {
        $status = "En cours";
        $status_class = "green";
        $status_short = "cours";
    }elseif($dateF < $current_date){
        $status = "Passée";
        $status_class = "green";
        $status_short = "pass";
    }

    return array("status" => $status, "status_class" => $status_class, "status_short" => $status_short);
}

// Initialisation des compteurs de réservations à venir, en cours et annulées
$avenir = 0;
$encours = 0;
$pass = 0;
$annulé = 0;


// Vérification des résultats et calcul des statistiques de réservations
if (!empty($results)) {
    foreach ($results as $result) {
        $date_debut = $result["date_debut"];
        $date_fin = $result["date_fin"];
        $date_annulation = $result["date_annulation"];
        $statusChip = getColorChipForDate($date_debut,$date_fin,$date_annulation);

        if ($statusChip["status"] === "À venir") {
            $avenir++;
        } elseif ($statusChip["status"] === "Passée") {
            $pass++;
        } elseif ($statusChip["status"] === "Annulée") {
            $annulé++;
        }else{
            $encours++;
        }
    }
}


// Fonction pour formater une date avec mois abrégé
function formatDateWithShortMonth($date)
{
    $shortMonths = array(
        "01" => "janv.", "02" => "févr.", "03" => "mars", "04" => "avr.", 
        "05" => "mai", "06" => "juin", "07" => "juil.", "08" => "août", 
        "09" => "sept.", "10" => "oct.", "11" => "nov.", "12" => "déc."
    );
    $dateObj = new DateTime($date);
    $day = $dateObj->format('d');
    $month = $dateObj->format('m');
    $year = $dateObj->format('Y');
    $shortMonth = $shortMonths[$month];
    return $day . ' ' . $shortMonth . ' ' . $year;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes réservations</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/logement.css">
    <link rel="stylesheet" href="css/mes_reserv.css">
    <script src="https://kit.fontawesome.com/7f17ac2dfc.js" crossorigin="anonymous"></script>
    <style>
        .card__reserv {
            display: none;
        }
    </style>
</head>

<body class="page">
    <div class="wrapper">
        <?php require_once 'header.php'; ?>
        <main class="main">
            <div class="main__container reserv">
                <div class="mes__reserv__titre">
                    <div class="page__tilte">
                        <h1>Mes réservations</h1>
                    </div>
                    <div class="tabs">
                            <input type="radio" id="radio-1" name="tabs" checked />
                            <label class="tab" for="radio-1" data-category="venir">À venir<span class="notification"><?php echo $avenir; ?></span></label>
                            <input type="radio" id="radio-2" name="tabs" />
                            <label class="tab" for="radio-2" data-category="cours">En cours<span class="notification"><?php echo $encours; ?></span></label>
                            <input type="radio" id="radio-3" name="tabs" />
                            <label class="tab" for="radio-3" data-category="pass">Passée<span class="notification"><?php echo $pass; ?></span></label>
                            <input type="radio" id="radio-4" name="tabs" />
                            <label class="tab" for="radio-4" data-category="annu">Annulée<span class="notification"><?php echo $annulé; ?></span></label>
                            <span class="glider"></span>
                        </div>
                </div>
                <?php if (empty($results)) { ?>
                    <div class="mes__reserv__empty">
                        <h4>Vous n'avez pas encore de réservations</h4>
                    </div>
                <?php } else {
                    foreach ($results as $result) {
                        $statusChip = getColorChipForDate($result["date_debut"], $result["date_fin"],$result["date_annulation"]);
                        $statusClass = strtolower($statusChip["status_short"]);

                ?>
                        <a href="detail_reservation.php?id=<?php echo $result["id"] ?>">
                        <div class="card__reserv <?php echo $statusClass; ?>">
                                <img src=<?= "img/" . $result["src"] ?> alt=<?= $result["alt"] ?>>
                                <div class="mes__reserv__cont_desc_prix">
                                    <div class="mes__reserv__description">
                                        <h4><?php echo $result["titre"] ?></h4>
                                        <div class="mes_reserv__numero">
                                            <h4>Numéro de réservation : </h4>
                                            <h4><?php echo $result["id"] ?></h4>
                                        </div>
                                        <div class="mes__reserv__date">
                                            <h4><?php echo formatDateWithShortMonth($result["date_debut"]) ?> – <?php echo formatDateWithShortMonth($result["date_fin"]) ?></h4>
                                            <i class="fa-solid fa-circle" style="color: #222222;"></i>
                                            <h4><?php echo $result["commune"] ?></h4>
                                        </div>
                                        <p class="<?php echo $statusChip["status_class"] ?>"><?php echo $statusChip["status"] ?></p>
                                    </div>
                                    <div class="mes__reserv__prix">
                                        <h4 class="mes__reserv__prix_color"><?php echo str_replace('.', ',', $result["prix_total"]) . "€"; ?></h4>                                    </div>
                                </div>
                            </div>
                        </a>
                <?php }
                } ?>
            </div>
        </main>
        <?php require_once 'footer.php'; ?>
    </div>
    <script src="js/script.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Fonction pour afficher les réservations en fonction de la catégorie sélectionnée
            function showReservations(category) {
                document.querySelectorAll('.card__reserv').forEach(function(card) {
                    if (card.classList.contains(category)) {
                        card.style.display = 'flex'; // Affiche la carte de réservation
                    } else {
                        card.style.display = 'none'; // Masque la carte de réservation
                    }
                });
            }

            // Afficher les réservations de la catégorie par défaut (Confirmée)
            showReservations('venir');

            // Ajouter un gestionnaire d'événements pour chaque onglet
            document.querySelectorAll('.tab').forEach(function(tab) {
                tab.addEventListener('click', function() {
                    var category = tab.getAttribute('data-category');
                    showReservations(category); // Appel de la fonction pour afficher les réservations selon la catégorie
                });
            });
        });
    </script>
</body>

</html>