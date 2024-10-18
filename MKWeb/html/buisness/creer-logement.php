<?php 
    session_start();
    require "../../utils.php"; 
    buisness_connected_or_redirect();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/creer-logement.css">
    <link rel="stylesheet" href="../css/toast.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <title>Nouveau Logement</title>
    <script src="https://kit.fontawesome.com/7f17ac2dfc.js" crossorigin="anonymous"></script>
</head>
<body class="page">
    <div class="wrapper">
        <?php require_once "header.php"; ?>
        <main class="main__container creer-logement">
            <div class="top">
                <div>
                    <h1 class="entete__titre">Ajouter un logement</h1>
                </div>
                <i id="reset" class="fa-solid fa-rotate"></i>

            </div>
            
            <form id="nv-logement" action="preview-logement.php" method="POST" enctype="multipart/form-data">
                <section>
                    <div class="top">
                        <h2>Informations générales</h2>
                    </div>
                    <div class="field-container">
                        <div class="info_gen__input">
                            <label for="titre">Titre</label>
                            <input type="text" id="titre" name="titre" placeholder="ex: Villa pieds dans la mer" required value="<?=$_SESSION["form_data"]["titre"] ?? "";?>">
                        </div>
                        <div class="info_gen__input">
                            <label for="categorie">Catégorie</label>
                            <select name="categorie" id="categorie" placeholder="" required>
                                <option value="" disabled <?=!isset($_SESSION["form_data"]["categorie"])?>>Catégorie Logement</option>
                                <?php foreach(request("SELECT * FROM sae._categorie_logement") as $cat){ ?>
                                    <option value=<?= $cat["id"]?> <?=isset($_SESSION["form_data"]["categorie"]) && $_SESSION["form_data"]["categorie"] == $cat["id"] ? "selected" : ""?>><?=$cat["categorie"]?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="info_gen__input">
                            <label for="titre">Type</label>
                            <select name="type" id="type" required>
                                <option value="" disabled <?=!isset($_SESSION["form_data"]["type"])?>>Type de Logement</option>
                                <?php foreach(request("SELECT * FROM sae._type_logement") as $cat){ ?>
                                    <option value=<?= $cat["id"]?> <?=isset($_SESSION["form_data"]["type"]) && $_SESSION["form_data"]["type"] == $cat["id"] ? "selected" : ""?>><?=$cat["type"]?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="info_gen__input">
                            <label for="surface">Surface</label>
                            <input type="text" id="surface" name="surface" placeholder="Surface en m²" required value="<?=$_SESSION["form_data"]["surface"] ?? "";?>">
                        </div>
                        
                        <div class="info_gen__input">
                            <label for="chambre">Nombre de chambres</label>
                            <input type="number" id="chambre" name="chambre" placeholder="Saisissez" required value="<?=$_SESSION["form_data"]["chambre"] ?? "";?>">
                        </div>

                        <div class="info_gen__input">
                            <label for="simple">Nombre de lit</label>
                            <div class="input__input">
                                <input type="number" id="simple" name="simple" placeholder="Simple" required value="<?=$_SESSION["form_data"]["simple"] ?? "";?>">
                                <input type="number" id="double" name="double" placeholder="Double" required value="<?=$_SESSION["form_data"]["double"] ?? "";?>">
                            </div>
                        </div>

                        <div class="full-size">
                            <div class="info_gen__input">
                                <label for="accroche">Accroche</label>
                                <textarea id="accroche" name="accroche" placeholder="Saisir descriptif" required><?=htmlspecialchars($_SESSION["form_data"]["titre"] ?? "");?></textarea>
                            </div>

                            <div class="info_gen__input">
                                <label for="description">Descriptif détaillé</label>
                                <textarea id="description" name="description" placeholder="Saisir descriptif" required><?=htmlspecialchars($_SESSION["form_data"]["description"] ?? "");?></textarea>
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
                                <option value="Finistère" <?=isset($_SESSION["form_data"]["departement"]) && $_SESSION["form_data"]["departement"] == "Finistère" ? "selected" : ""?>>Finistère</option>
                                <option value="Morbihan" <?=isset($_SESSION["form_data"]["departement"]) && $_SESSION["form_data"]["departement"] == "Morbihan" ? "selected" : ""?>>Morbihan</option>
                                <option value="Ille-et-Vilaine" <?=isset($_SESSION["form_data"]["departement"]) && $_SESSION["form_data"]["departement"] == "Ille-et-Vilaine" ? "selected" : ""?>>Ille-et-Vilaine</option>
                                <option value="Côtes-d'Armor" <?=isset($_SESSION["form_data"]["departement"]) && $_SESSION["form_data"]["departement"] == "Côtes-d'Armor" ? "selected" : ""?>>Côtes-d'Armor</option>
                            </select>
                        </div>

                        <div class="info_gen__input">
                            <label for="commune">Commune</label>
                            <input type="text" id="commune" name="commune" placeholder="Saisissez" required value="<?=$_SESSION["form_data"]["simple"] ?? "";?>">
                        </div>

                        <div class="info_gen__input">
                            <label for="">Code Postal</label>
                            <input type="number" id="cp" name="cp" placeholder="29400" required value="<?=$_SESSION["form_data"]["cp"] ?? "";?>">
                        </div>

                        <input type="hidden" id="latitude" name="latitude" placeholder="Latitude" required>
                        <input type="hidden" id="longitude" name="longitude" placeholder="Longitude" required>

                        <div class="info_gen__input adresse">
                            <label for="voie">Voie</label>
                            <input type="text" id="voie" name="voie" placeholder="Nom de voie" required value="<?=$_SESSION["form_data"]["voie"] ?? "";?>">
                        </div>

                        <div class="info_gen__input">
                            <label for="num_voie">Numéro Voie</label>
                            <div class="input__input">
                                <input type="number" id="num_voie" name="num_voie" placeholder="12" required value="<?=$_SESSION["form_data"]["num_voie"] ?? "";?>">
                            </div>
                        </div>

                        <div class="info_gen__input adresse">
                            <label for="comp1">Complément 1</label>
                            <input type="text" id="comp1" name="comp1" placeholder="Saisir complément" value="<?=$_SESSION["form_data"]["comp1"] ?? "";?>">
                        </div>

                        <div class="info_gen__input adresse">
                            <label for="comp2">Complément 2</label>
                            <input type="text" id="comp2" name="comp2" placeholder="Saisir complément" value="<?=$_SESSION["form_data"]["comp2"] ?? "";?>">
                        </div>

                        <div class="info_gen__input adresse">
                            <label for="comp3">Complément 3</label>
                            <input type="text" id="comp3" name="comp3" placeholder="Saisir complément" value="<?=$_SESSION["form_data"]["comp3"] ?? "";?>">
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
                            <input type="number" id="nbpersonne" name="nbpersonne" placeholder="6" required value="<?=$_SESSION["form_data"]["nbpersonne"] ?? "";?>">
                        </div>
                        
                        <div class="info_gen__input">
                            <label for="prixht">Prix HT</label>
                            <input type="text" id="prixht" name="prixht" placeholder="123.5" required value="<?=$_SESSION["form_data"]["prixht"] ?? "";?>">
                        </div>

                        <div class="info_gen__input">
                                <label for="dureeloc">Durée minimum de location</label>
                                <input type="number" id="dureeloc" name="dureeloc" placeholder="Saisissez" required value="<?=$_SESSION["form_data"]["dureeloc"] ?? "";?>">
                            </div>

                        <div class="info_gen__input">
                            <label for="delaires">Délai minimum réservation avant l'arrivée </label>
                            <input type="number" id="delaires" name="delaires" placeholder="Saisissez" required value="<?=$_SESSION["form_data"]["delaires"] ?? "";?>">
                        </div>

                        <div class="info_gen__input">
                            <label for="delaires">Délai d'annulation </label>
                            <input type="number" id="preavis" name="preavis" placeholder="Saisissez" required value="<?=$_SESSION["form_data"]["preavis"] ?? "";?>">
                        </div>


                        <div class="info_gen__input">
                            <label for="statut">Statut du logement</label>
                            <select name="statut" id="statut" placeholder="" required>
                                <option value="" disabled <?=!isset($_SESSION["form_data"]["statut"])?>>Choisir</option>
                                <option value="1" <?=isset($_SESSION["form_data"]["statut"]) && $_SESSION["form_data"]["statut"] ? "selected" : ""?>>En ligne</option>
                                <option value="0" <?=isset($_SESSION["form_data"]["statut"]) && !$_SESSION["form_data"]["statut"] ? "selected" : ""?>>Hors ligne</option>
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
                            <input type="checkbox" name="amenagements[]" value=<?=$ame["id"]?> <?=$checked?>>
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
                                    <button type="button" class="btn__remove">X</button>
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
                            <button type="button" class="ajouter hover green" id="ajouter__amenagement">Ajouter</button>
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
                            <?php if (empty($_SESSION["form_images"])) {?>
                            <p>Aucune image chargé.</p>
                            <?php } else {
                                foreach($_SESSION["form_images"] as $img){?>
                                    <div>
                                        <button type="button" class="btn__remove">X</button>
                                        <img class="img_preview" src="<?="../img".$img?>"></img>
                                        <input type="hidden" name="img-loaded[]" value="<?=$img?>">
                                    </div>
                            <?php    }
                            }?>
                        </div>
                    </div>
                    <input type="file" id="image-input" accept=".jpg,.jpeg,.png,.webp" hidden multiple>
                    <input type="button" class="hover green" value="Ajouter une image" onclick="document.getElementById('image-input').click();" />
                </section>
                <div class="buttons">
                    <button id="form__preview" class="envoyer hover pink">
                        Prévisualiser
                    </button>

                    <button id="form__submit" class="envoyer hover">
                        Enregistrer
                    </button>
                </div>
                    
                </form>
        </main>
        <div class="loading__modal">
            <span class="loader"></span>
        </div>
        <?php require_once "footer.php"; ?>
    </div>
    <script src="../js/toast.js"></script>
    <script src="../js/creer-logement.js"></script>
</body>
