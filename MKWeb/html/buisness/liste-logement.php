<?php 
    session_start();
    require "../../utils.php"; 
   
    $id = buisness_connected_or_redirect();

    $query ="SELECT * FROM sae._logement WHERE id_proprietaire=$id";
    
    $logements = request($query, false);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des logements</title>
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/liste-logements.css">
    <script src="../js/logements.js"></script>
    <script src="https://kit.fontawesome.com/7f17ac2dfc.js" crossorigin="anonymous"></script>
</head>
<body class="page">
    <div class="wrapper">
        <?php require_once "header.php"; ?>
        <main class="main">
            <div class="main__container logements">
                <div class="titreLogement">
                    <div>
                        <h1>Mes Logements</h1>
                    </div>
                    <a href="creer-logement.php" class="ajouter hover">
                        <i class="fa-regular fa-plus"></i>
                        <p>Ajouter</p>
                    </a>
                </div>
                <?php if(empty($logements)){ ?>
                    <div class="mes__reserv__empty">
                        <h4>Vous n'avez pas encore de logements enregistrés</h4>
                    </div>
                    <?php } else {
                        foreach($logements as $logement){
                            // Récupération des informations en lien avec le logement
                            $query = "SELECT * from sae._image where id_logement=$logement[id] and principale=true";
                            $image = request($query,true);
                            $query = "SELECT categorie from sae._categorie_logement where id=$logement[id_categorie]";
                            $labelCategorie = request($query,true);
                            $query = "SELECT type from sae._type_logement where id=$logement[id_type]";
                            $labelType = request($query,true);
                            $query = "SELECT * from sae._calendrier where id_logement=$logement[id]";
                            $dates = request($query,false);
                            $chiffreInfoLogement = $labelCategorie["categorie"]." · ".$logement["surface"]." m² · ".$labelType["type"]." · ".$logement["nb_max_personne"]." voyageurs";
                            $query = "SELECT commune,region,nom_voie,numero,code_postal FROM sae._adresse where id=$logement[id_adresse]";
                            $adresse = request($query,true);
                            $adresseComplete = $adresse["numero"]." ".$adresse["nom_voie"].", ".$adresse["code_postal"].", ".$adresse["commune"].", ".$adresse["region"];
                            // On map pour stocker seulement toutes les dates d'un logement
                            $dateLogement = array_map(function($div){
                                return [$div["date"],$div["statut"]];
                            },$dates);
                            $testDate = false;                        
                            ?>
                                <div class="card_logement">
                                    <div class="card_main">
                                        <div class="info_logement">
                                            <img class="" src="<?="../img".$image["src"]?>">
                                            <div class="actionInfo">
                                                <div class="detaille_logement">
                                                    <div class="conteneur_info">
                                                        <p class="titre_logement"><?=$logement["titre"]?></p>
                                                        <!-- On vérifie le statut d'occupation et la mise en ligne -->
                                                        <div class="actifInactif">
                                                            <?php  if($logement["en_ligne"]==1){
                                                                ?>
                                                                <div class="Actif">
                                                                    <p>Actif</p>
                                                                </div>
                                                            <?php } else{ ?>
                                                                <div class="Inactif">
                                                                    <p>Inactif</p>
                                                                </div>
                                                            <?php }  ?>
                                                            <?php
                                                            // On test la présence d'une date de réservation sur le logement et son statut R
                                                            foreach($dateLogement as $date){
                                                                if($date[0]==date("Y-m-d") && $date[1]=="R"){ 
                                                                    $testDate = true;
                                                                    ?>
                                                                    <div class="Inactif">
                                                                        <p>Occupé</p>
                                                                    </div>
                                                            <?php break;  } }
                                                                if($testDate==false){
                                                            ?>
                                                                <div class="Actif">
                                                                    <p>Libre</p>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <p id="complement1"><?=$chiffreInfoLogement?></p>
                                                    <p id="complement2"><?=$adresseComplete?></p>
                                                </div>
                                                
                                                <div class="actions">
                                                    
                                                    <div class="edit hover blue">
                                                        <a href="modifier-logement.php?id=<?=$logement["id"]?>">
                                                            <img src="../img/edit.png" alt="image pour éditer un logement " title="edit">
                                                        </a>
                                                    </div>
                                                    </a>
                                                </div> 
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                </div>
                            <?php
                        }
                    }?>
                     <div class="alert hidden">
                
                        <p id="messageEdit"> </p>
                
                    </div>
            </div>
           
        </main>
    </div>
</body>
</html>