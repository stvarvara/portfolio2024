<?php 
    session_start();
    require "../../utils.php"; 
    buisness_connected_or_redirect();

    $id = $_GET["id"];
    // Récupération des informations liées au logement
    // pour remplir les inputs du formulaire de modification

    $query = "SELECT * from sae._logement 
    INNER JOIN sae._adresse on sae._adresse.id=sae._logement.id_adresse 
    /*INNER JOIN sae._reservation on sae._reservation.id_logement=sae._logement.id*/
    INNER JOIN sae._categorie_logement on sae._categorie_logement.id = sae._logement.id_categorie
    INNER JOIN sae._type_logement on sae._type_logement.id = sae._logement.id_type 
    INNER JOIN sae._image on sae._image.id_logement=sae._logement.id
    WHERE sae._logement.id=$id";

    $logement = request($query,true);

    $queryAmenagements = "SELECT id
    FROM sae._amenagement 
    INNER JOIN  sae._amenagement_logement ON sae._amenagement_logement.id_amenagement = sae._amenagement.id
    WHERE sae._amenagement_logement.id_logement = $id";

    $amenagements = request($queryAmenagements,false);

    $idAmenagement = array_map(function($element){
        return $element["id"];
    },$amenagements);

    $queryActivite = "SELECT sae._activite_logement.* from sae._activite_logement 
    where sae._activite_logement.id_logement=$id";
    
  

    $activites = request($queryActivite,false);


    $queryImage = "SELECT sae._image.* from sae._image 
    INNER JOIN sae._logement on sae._logement.id = sae._image.id_logement
    WHERE sae._logement.id=$id";

    $images = request($queryImage,false);

    unset($_SESSION["form_data"]);
    unset($_SESSION["form_images"]);

    // Réinitialiser les activités dans la session

    $_SESSION["form_data"]["id"] = $_GET["id"];

    $_SESSION["form_data"]["id_adresse"] = $logement["id_adresse"];


    
    $_SESSION["form_data"]["activite"] = [];

    $_SESSION["form_images"] = [];

    $_SESSION["form_data"]["description"] = "";

    $_SESSION["form_data"]["accroche"] = "";

    // Ajouter les nouvelles activités dans la session
    foreach ($activites as $act) {
        $_SESSION["form_data"]["activite"][] = $act["activite"] . ";;" . $act["id_distance"].";;".$act["id"];
    }

   



    
    

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/modifier-logement.css">
    <title>Modifier Logement</title>
    <script src="https://kit.fontawesome.com/7f17ac2dfc.js" crossorigin="anonymous"></script>
</head>
<body class="page">
    <div class="wrapper">
        <?php require_once "header.php"; ?>
        <main class="main__container creer-logement">
            
            <?php if(empty($logement)){  ?>
                <div class="logement__empty">
                    <h4>Le logement que vous tentez de modifier n'existe plus</h4>
                </div>
           <?php } else { ?>
            <div class="top">
                <div>
                    <h1 class="entete__titre">Modifier un logement</h1>
                </div>
                <i id="reset" class="fa-solid fa-rotate"></i>

            </div>
            <form id="edit-logement" action="preview-logement-edit.php" method="POST" enctype="multipart/form-data">
                
                <input type="hidden" value="<?=$_SESSION["form_data"]["id"]?>" name="id">
                <input type="hidden" value="<?=$logement["id_adresse"]?>" name="id_adresse">
                 <section>
                    <div class="top">
                        <h2>Informations générales</h2>
                    </div>
                    <div class="field-container">
                        <div class="info_gen__input">
                            <label for="titre">Titre</label>
                            <input value="<?=$logement["titre"]?>" type="text" id="titre" name="titre" placeholder="ex: Villa pieds dans la mer" required value="<?=$_SESSION["form_data"]["titre"] ?? "";?>">
                        </div>
                        <div class="info_gen__input">
                            <label for="categorie">Catégorie</label>
                            <select name="categorie" id="categorie" placeholder="" required>
                                <option value="<?=$logement["categorie"]?>" disabled <?=!isset($_SESSION["form_data"]["categorie"])?>>Catégorie Logement</option>
                                <?php foreach(request("SELECT * FROM sae._categorie_logement") as $cat){ ?>
                                    <!-- Selectionne la catégorie du logement -->
                                    <?php if($logement["id_categorie"]==$cat["id"]){ ?>
                                        <option selected value=<?= $cat["id"]?> <?=isset($_SESSION["form_data"]["categorie"]) && $_SESSION["form_data"]["categorie"] == $cat["id"] ? "selected" : ""?>><?=$cat["categorie"]?></option>
                                    <?php } else { ?>
                                        <option value=<?= $cat["id"]?> <?=isset($_SESSION["form_data"]["categorie"]) && $_SESSION["form_data"]["categorie"] == $cat["id"] ? "selected" : ""?>><?=$cat["categorie"]?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="info_gen__input">
                            <label for="titre">Type</label>
                            <select name="type" id="type" required>
                                <option default_value="<?=$logement["type"]?>" disabled <?=!isset($_SESSION["form_data"]["type"])?>>Type de Logement</option>
                                <?php foreach(request("SELECT * FROM sae._type_logement") as $cat){ ?>
                                    <!-- Selectionne le type du logement -->
                                    <?php  if($logement["id_type"]==$cat["id"]){
                                        ?>
                                            <option selected value=<?= $cat["id"]?> <?=isset($_SESSION["form_data"]["type"]) && $_SESSION["form_data"]["type"] == $cat["id"] ? "selected" : ""?>><?=$cat["type"]?></option>
                                        <?php } else { ?>
                                    <option value=<?= $cat["id"]?> <?=isset($_SESSION["form_data"]["type"]) && $_SESSION["form_data"]["type"] == $cat["id"] ? "selected" : ""?>><?=$cat["type"]?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="info_gen__input">
                            <label for="surface">Surface</label>
                            <input value="<?=$logement["surface"]?>" type="number" id="surface" name="surface" placeholder="Surface en m²" required value="<?=$_SESSION["form_data"]["surface"] ?? "";?>">
                        </div>
                        
                        <div class="info_gen__input">
                            <label for="chambre">Nombre de chambres</label>
                            <input value="<?=$logement["nb_chambre"]?>" type="number" id="chambre" name="chambre" placeholder="Saisissez" required value="<?=$_SESSION["form_data"]["chambre"] ?? "";?>">
                        </div>

                        <div class="info_gen__input">
                            <label for="simple">Nombre de lit</label>
                            <div class="input__input">
                                <input value="<?=$logement["nb_lit_simple"]?>" type="number" id="simple" name="simple" placeholder="Simple" required value="<?=$_SESSION["form_data"]["simple"] ?? "";?>">
                                <input value="<?=$logement["nb_lit_double"]?>" type="number" id="double" name="double" placeholder="Double" required value="<?=$_SESSION["form_data"]["double"] ?? "";?>">
                            </div>
                        </div>

                        <div class="full-size">
                            <div class="info_gen__input">
                                <label for="accroche">Accroche</label>
                                <textarea  id="accroche" name="accroche" placeholder="Saisir descriptif" required><?=htmlspecialchars($_SESSION["form_data"]["titre"] ?? "");?><?=$logement["accroche"]?></textarea>
                            </div>

                            <div class="info_gen__input">
                                <label for="description">Descriptif détaillé</label>
                                <textarea  id="description" name="description" placeholder="Saisir descriptif" required><?=htmlspecialchars($_SESSION["form_data"]["description"] ?? "");?><?=$logement["description"]?></textarea>
                            </div>
                        </div>
                        
                    </div>
                </section>

                <section>
                    <div class="top">
                        <h2>Adresse</h2>
                    </div>
                    <div class="field-container">
                        <div class="info_gen__input">
                            <label for="pays">Pays</label>
                            <input type="text" id="pays" name="pays" value="France" disabled required>
                        </div>
                        
                        <div class="info_gen__input">
                            <label for="region">Région</label>
                            <input type="text" id="region" name="region" value="Bretagne" disabled required>
                        </div>

                        <div class="info_gen__input">
                            <label for="departement">Département</label>
                            <select name="departement" id="departement" required>
                                <option value="" disabled <?=!isset($_SESSION["form_data"]["departement"]) ? "selected" : ""?>>Selectionner</option>
                                <option value="Finistère" <?php if($logement["departement"]=="Finistère"){echo "selected";} ?> <?=isset($_SESSION["form_data"]["departement"]) && $_SESSION["form_data"]["departement"] == "Finistère" ? "selected" : ""?>>Finistère</option>
                                <option value="Morbihan" <?php if($logement["departement"]=="Morbihan"){echo "selected";}?> <?=isset($_SESSION["form_data"]["departement"]) && $_SESSION["form_data"]["departement"] == "Morbihan" ? "selected" : ""?>> Morbihan</option>
                                <option value="Ille-et-Vilaine" <?php if($logement["departement"]=="Ille-et-Vilaine"){ echo "selected";}?> <?=isset($_SESSION["form_data"]["departement"]) && $_SESSION["form_data"]["departement"] == "Ille-et-Vilaine" ? "selected" : ""?>>Ille-et-Vilaine</option>
                                <option value="Côtes-d'Armor" <?php if($logement["departement"]=="Côtes-d'Armor"){ echo "selected";}?>  <?=isset($_SESSION["form_data"]["departement"]) && $_SESSION["form_data"]["departement"] == "Côtes-d'Armor" ? "selected" : ""?>>Côtes-d'Armor</option>
                            </select>
                        </div>

                        <div class="info_gen__input">
                            <label for="commune">Commune</label>
                            <input value="<?=$logement["commune"]?>" type="text" id="commune" name="commune" placeholder="Saisissez" required value="<?=$_SESSION["form_data"]["simple"] ?? "";?>">
                        </div>

                        <div class="info_gen__input">
                            <label for="">Code Postal</label>
                            <input value="<?=$logement["code_postal"]?>" type="number" id="cp" name="cp" placeholder="29400" required value="<?=$_SESSION["form_data"]["cp"] ?? "";?>">
                        </div>

                        <input type="hidden" id="latitude" name="latitude" placeholder="Latitude" required>
                        <input type="hidden" id="longitude" name="longitude" placeholder="Longitude" required>

                        <div class="info_gen__input adresse">
                            <label for="voie">Voie</label>
                            <input  value="<?=$logement["nom_voie"]?>" type="text" id="voie" name="voie" placeholder="Nom de voie" required value="<?=$_SESSION["form_data"]["voie"] ?? "";?>">
                        </div>

                        <div class="info_gen__input">
                            <label for="num_voie">Numéro Voie</label>
                            <div class="input__input">
                                <input value="<?=$logement["numero"]?>" type="number" id="num_voie" name="num_voie" placeholder="12" required value="<?=$_SESSION["form_data"]["num_voie"] ?? "";?>">
                            </div>
                        </div>

                        <div class="info_gen__input adresse">
                            <label for="comp1">Complément 1</label>
                            <input  value="<?=$logement["complement_1"] ?? ""?>" type="text" id="comp1" name="comp1" placeholder="Saisir complément" value="<?=$_SESSION["form_data"]["comp1"] ?? "";?>">
                        </div>

                        <div class="info_gen__input adresse">
                            <label for="comp2">Complément 2</label>
                            <input value="<?=$logement["complement_2"] ?? ""?>" type="text" id="comp2" name="comp2" placeholder="Saisir complément" value="<?=$_SESSION["form_data"]["comp2"] ?? "";?>">
                        </div>

                        <div class="info_gen__input adresse">
                            <label for="comp3">Complément 3</label>
                            <input value="<?=$logement["complement_3"] ?? ""?>" type="text" id="comp3" name="comp3" placeholder="Saisir complément" value="<?=$_SESSION["form_data"]["comp3"] ?? "";?>">
                        </div>
                    </div>
                </section>

                <section>
                    <div class="top">
                        <h2>Informations sur la réservation</h2>
                    </div>
                    <div class="field-container">
                        <div class="info_gen__input">
                            <label for="nbpersonne">Nombre max de personne</label>
                            <input value="<?=$logement["nb_max_personne"]?>"  type="number" id="nbpersonne" name="nbpersonne" placeholder="6" required value="<?=$_SESSION["form_data"]["nbpersonne"] ?? "";?>">
                        </div>
                        
                        <div class="info_gen__input">
                            <label for="prixht">Prix HT</label>
                            <input value="<?=$logement["base_tarif"]?>" type="number" id="prixht" name="prixht" placeholder="123.5" required value="<?=$_SESSION["form_data"]["prixht"] ?? "";?>">
                        </div>

                        <div class="info_gen__input">
                                <label for="dureeloc">Durée minimum de location</label>
                                <input value="<?=$logement["duree_min_res"]?>" type="number" id="dureeloc" name="dureeloc" placeholder="Saisissez" required value="<?=$_SESSION["form_data"]["dureeloc"] ?? "";?>">
                            </div>

                        <div class="info_gen__input">
                            <label for="delaires">Délai minimum réservation avant l'arrivée </label>
                            <input value="<?=$logement["delai_avant_res"]?>" type="number" id="delaires" name="delaires" placeholder="Saisissez" required value="<?=$_SESSION["form_data"]["delaires"] ?? "";?>">
                        </div>

                        <div class="info_gen__input">
                            <label for="delaires">Délai d'annulation </label>
                            <input  value="<?=$logement["periode_preavis"]?>" type="number" id="preavis" name="preavis" placeholder="Saisissez" required value="<?=$_SESSION["form_data"]["preavis"] ?? "";?>">
                        </div>


                        <div class="info_gen__input">
                            <label for="statut">Statut du logement</label>
                            <select name="statut" id="statut" placeholder="" required>
                                <option value="" disabled <?=!isset($_SESSION["form_data"]["statut"])?>>Choisir</option>
                                <option  <?php if($logement["en_ligne"]==1) { echo "selected";}?> value="1" <?=isset($_SESSION["form_data"]["statut"]) && $_SESSION["form_data"]["statut"] ? "selected" : ""?>>En ligne</option>
                                <option <?php if($logement["en_ligne"]==0) { echo "selected";}?> value="0" <?=isset($_SESSION["form_data"]["statut"]) && !$_SESSION["form_data"]["statut"] ? "selected" : ""?>>Hors ligne</option>
                            </select>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="top">
                        <h2>Aménagement(s)</h2>
                        <p>Ajoutez des aménagement présent dans votre logements.</p>
                    </div>
                    <div class="field-container check__list">
                        
                        <?php foreach(request("SELECT * FROM sae._amenagement") as $ame){ ?>
                            <div class="input__checkbox">
                            <?php $checked = isset($_SESSION["form_data"]["amenagements"]) && in_array($ame["id"], $_SESSION["form_data"]["amenagements"]) ? "checked" : ""; ?>
                            <input <?php if(in_array($ame["id"],$idAmenagement)){echo "checked";} ?> type="checkbox"  name="amenagements[]" value=<?=$ame["id"]?> <?=$checked?>>
                            <label><?=$ame["amenagement"]?></label>
                        </div>
                        <?php } ?>
                    </div>
                </section>

                <section>
                    <div class="top">
                        <h2>Environs du logement</h2>
                        <p>Ajoutez des activités disponibles à proximité de votre logement.</p>
                    </div>
                    <div class="field-container">
                        <?php $distances = request("SELECT * FROM sae._distance");?>
                        <div id="list__amenagement">
                        
                             <?php foreach($_SESSION["form_data"]["activite"] ?? [] as $activ){
                                $a = explode(";;", $activ);?>
                                <div>
                                    <p><?=$a[0]?></p>
                                    <span><?=$distances[$a[1]-1]["perimetre"]?></span>
                                    <input type="hidden" name="activite[]" value=<?=$activ?>>
                                    <button type="button" class="btn__remove_edit">X</button>
                                </div>
                                
                            <?php } ?>
                        </div>

                        <div class="amenagement__input">
                            <input type="text" id="name__amenagement">
                            <select id="distance__amenagement">
                            <?php foreach($distances as $distance){ ?>
                                <option value=<?= $distance["id"]?>><?=ucfirst($distance["perimetre"])?></option>
                            <?php } ?>
                            </select>
                            <button type="button" class="ajouter" id="ajouter__amenagement">Ajouter</button>
                        </div>
                    </div>
                </section>

                <section id="section__image">
                    <div class="top">
                        <h2>Photos</h2>
                        <p>Ajoutez des photos à votre logement.</p>
                    </div>
                    <div class="container">
                        <p>Previsualiation des images</p>
                        <div id="image-preview">
                            <?php if (empty($_SESSION["form_images"]) && empty($images)) {?>
                                <p>Aucune image chargé.</p>
                            <?php }
                            else if(!empty($images)){ foreach($images as $img) { ?>
                                <div>
                                        <button type="button" class="btn__remove">X</button>
                                        <img class="img_preview" src="<?="../img".$img["src"]?>"></img>
                                        <input type="hidden" name="img-preview[]" value="<?=$img["src"]?>">
                                    </div>   
                            <?php } } else  {?>
                            
                               <?php foreach($_SESSION["form_images"] as $img){?>
                            
                                <div>
                                    <button type="button" class="btn__remove">X</button>
                                    <img class="img_preview" src="<?="../img".$img?>"></img>
                                    <input type="hidden" name="img-loaded[]" value="<?=$img?>">
                                </div>
                            <?php }   } ?>
                            
                        </div>
                    </div>
                    <input type="file" id="image-input" accept=".jpg,.jpeg,.png,.webp" hidden multiple>
                    <input type="button" value="Ajouter une image" onclick="document.getElementById('image-input').click();" />
                </section>
                
                <div class="buttons">
                    <button id="form__preview" class="envoyer">
                        Prévisualiser
                    </button>

                    <button id="form__submit" class="envoyer">
                        Enregistrer
                    </button>
                </div>
                    
                </form>

              <?php }  ?>
        </main>
        <div class="loading__modal">
            <span class="loader"></span>
        </div>
        <?php require_once "footer.php"; ?>
    </div>
    <script src="../js/modifier-logement.js"></script>
</body>
