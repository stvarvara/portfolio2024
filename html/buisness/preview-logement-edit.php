<?php 
    session_start();
    require_once "../../utils.php";

    $id_proprio = buisness_connected_or_redirect();
    $base_tarif = $_POST['prixht'];
    $min_jour = $_POST['dureeloc'];
    $delai_res = $_POST['delaires'];

    $query_hote = "select prenom, nom, photo_profile from sae._utilisateur inner join sae._logement on sae._utilisateur.id = $id_proprio;";
    $rep_hote = request($query_hote)[0];

    $query_langue = "select langue from sae._langue_proprietaire
    inner join sae._langue on sae._langue_proprietaire.id_langue = sae._langue.id
    WHERE sae._langue_proprietaire.id_proprietaire = $id_proprio;";

    $titre_logement =  $_POST['titre'] ;
    $ville = $_POST['commune'];
    $departement = $_POST['departement'];
    $accroche = $_POST['accroche'];
    $categorie = $_POST['categorie'];
    $type = $_POST['type'];
    $surface = $_POST['surface'];
    $nb_personne = $_POST['nbpersonne'];
    $nb_chambre = $_POST['chambre'];
    $nb_lit_simple =  $_POST['simple'];
    $nb_lit_double = $_POST['double'];
    $description = $_POST['description'];
    $nom_hote = $rep_hote['nom'];
    $prenom_hote = $rep_hote['prenom'];
    $source = $rep_hote['photo_profile'];

    $rep_langue = request($query_langue);

    $idsAmenagementStrings = implode(",", array_map('intval', $_SESSION["form_data"]["amenagements"]));
    $queryAmenagement = "SELECT amenagement FROM sae._amenagement  WHERE id IN ($idsAmenagementStrings)";    
    
    $liste_amenagement = request($queryAmenagement,false);
    $liste_langue = [];
    foreach($rep_langue as $cle => $langues){
        foreach($langues as $cle => $langue){
            $liste_langue[] = $langue;
        }
    }

    $liste_activite = [];
    foreach($_POST["activite"] ?? [] as $cle => $activite){
        $activite = explode(";;" ,$activite);
        $query_activite = "select perimetre from sae._distance
        where sae._distance.id =".$activite[1].";";
        $liste_activite[$activite[0]] =  request($query_activite, true)["perimetre"];
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/logement.css">
    <title><?= $titre_logement?></title>
    <link rel="stylesheet" href="../css/preview.css">
    <script src="https://kit.fontawesome.com/7f17ac2dfc.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>
<body>
    <div class="wrapper">
        <?php     include "header.php";?>
        <main class="main">
            <div class="main__container logement">
                <div class="logement__top">
                    <div class="logement__nom">
                        <div class="nom">
                            <h1 id="logement__nom"><?php echo  $titre_logement?></h1>
                        </div>
                        <div class="partager">
                            <img src="../img/share.webp" alt="Partager">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=https://mkweb.ventsdouest.dev/detail_logement.php?id=<?= $id_logement?>" target="_blank">Partager</a>
                        </div>
                    </div>
                    <div class="logement__adr">
                        <h2 id="logement__adresse"><?php echo  $ville . ", " . $departement?></h2>
                        <a id="enregistrer" class="hover">Enregistrer</a>
                    </div>
                </div>
                <div class="logement__photos">
                    <div class="photo__grille" id="logement__photo__grille">
                        <?php foreach($_SESSION["form_images"] ?? [] as $key=>$photo) { ?>
                            <img src="<?="../img".$photo?>" alt="Photo logement">
                        <?php } ?>
                        <?php if(isset($_SESSION["form_data"]["img-preview"])){
                            foreach($_SESSION["form_data"]["img-preview"] as $src) {?>
                            <img src="<?="../img".$src?>" alt="Photo logement">

                         <?php   }
                        } ?>
                    </div>
                </div>
                <div class="logement-container">
                    <div class="logement__details">
                        <div class="details__top">
                            <div class="details__nom"><h2 id="log__nom"><?php echo  $titre_logement?></h2> <h2 id="log__details"><?php echo  $accroche?></h2></div>
                            <div class="details__features" id="features">
                                <div class="feature"><?= $categorie?></div>
                                <div class="feature"><?php echo  $type?></div>
                                <div class="feature"><?php echo  $surface?> m²</div>
                                <?php if ($nb_personne == 1) { ?>
                                    <div class="feature">1 voyageur</div>
                                <?php } else if ($nb_personne > 1) { ?>
                                    <div class="feature"><?php echo  $nb_personne?> voyageurs</div>
                                <?php } ?>
                                <div class="feature"><?php echo  $nb_chambre?> chambres</div>
                                <?php if ((!empty($nb_lit_simple)) && ($nb_lit_simple > 1)) { ?>
                                    <div class="feature"><?php echo  $nb_lit_simple?> lits simples</div>
                                <?php } else if ((!empty($nb_lit_simple)) && ($nb_lit_simple == 1)) { ?>
                                    <div class="feature">1 lit simple</div>
                                <?php } ?>
                                <?php if ((!empty($nb_lit_double)) && ($nb_lit_double > 1)) { ?>
                                    <div class="feature"><?php echo  $nb_lit_double?> lits simples</div>
                                <?php } else if ((!empty($nb_lit_double)) && ($nb_lit_double == 1)) { ?>
                                    <div class="feature">1 lit double</div>
                                <?php } ?>
                                
                                
                            </div>
                            <h3>Ce logement vous propose</h3>
                            <div class="logement__proposNote">
                                <?php if (empty($liste_amenagement)) { ?> 
                                    <div class="proposition">Ce logement ne propose aucun aménagement</div>
                                <?php } else { ?>
                                    <div class="logement__propose">
                                        <ul>
                                            <?php foreach($liste_amenagement as $a) { ?>
                                                <li class="proposition"><?php echo $a["amenagement"] ?></li>
                                            <?php } ?>
                                        </ul>
                                        <a href="">Conditions d’annulation</a>
                                    </div>
                                <?php }?>  
                            </div>
                        </div>
                        <div class="apropos">
                            <h3>À propos de ce logement</h3>
                            <p id="logement__descipt">
                                <?php echo  $description?>
                            </p>
                            <button id="decouvrir">Découvrir plus</button>
                        </div>
                        <div class="hote">
                            <div class="hote__info">
                                <img src="../img/<?php echo $source?>" alt="Hôte" id="hote__photo">
                                <div class="hote__main">
                                    <div class="hote__nom">
                                        <h3>Hôte: <span id="hote__nm"><?php echo  $prenom_hote?></span></h3>
                                    </div>
                                    <div class="hote__langues">
                                        <i class="fa-solid fa-earth-americas" style="color: #222222;"></i>
                                        <ul>
                                            <?php foreach($liste_langue as $l) { ?>
                                                <li class="proposition"><?php echo $l ?></li>
                                            <?php } ?> 
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="asavoir">
                                <h2>À savoir</h2>
                                <a href="">Conditions de séjour dans сe logement</a>
                                <h3>Moyens de paiement acceptés : PayPal, Carte bancaire</h3>
                            </div>
                        </div>
                        <div class="environs">
                            
                            <div class="environs__map" id="environs__map">
                                <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
                            </div> 
                            <script>
                                var ville = "<?php echo $ville; ?>";
                                var opencageUrl = "https://api.opencagedata.com/geocode/v1/json?q=" + encodeURIComponent(ville) + "&key=90a3f846aa9e490d927a787facf78c7e";
                                console.log(ville);
                                fetch(opencageUrl)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.results.length > 0) {
                                            afficherCommuneSurMap(data.results[0].geometry.lat, data.results[0].geometry.lng);
                                            console.log(data);
                                        } else {
                                            console.error("La ville à afficher n'est pas valide.");
                                        }
                                    })
                                    .catch(error => {
                                        console.error("Erreur lors de la requête de géocodage:", error);
                                    });

                                function afficherCommuneSurMap(lat, lng) {
                                    var map = L.map('environs__map').setView([lat, lng], 9); 
                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        attribution: '© OpenStreetMap contributors'
                                    }).addTo(map);

                                    L.marker([lat, lng]).addTo(map).bindPopup('Le logement est ici !');
                                }
                            </script>
                            <div class="environs__details">
                                <h3>Environs de l'établissement</h3>
                                <?php if (empty($liste_activite)) { ?> 
                                    <div class="environs__ligne">Il n'y a rien à proxmité.</div>
                                <?php } else { ?>
                                    <?php foreach($liste_activite as $act => $distance) { ?>
                                        <div class="environs__ligne">
                                            <p class="environ"><?php echo $act?></p>
                                            <p class="dest"><?php echo $distance?></p>
                                        </div>
                                    <?php } ?>
                                <?php }?>  
                                
                            </div>
                        </div>
                    </div>
            </div>
            </div>
        </main>
        <div class="loading__modal">
            <span class="loader"></span>
        </div>
        <?php include_once 'footer.php'; ?>
    </div>
    
    
    <script src="../js/preview-logement-edit.js"></script>
</body>
</html>