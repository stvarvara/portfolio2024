<?php 
    session_start();
    require_once "../utils.php";
    function clean_input($data) {
        return htmlspecialchars(trim($data));
    }
   

    $id_utilisateur =  client_connected_or_redirect();

    $query_utilisateur = "select nom, prenom, pseudo, commune, pays, region, departement,
    numero, code_postal, nom_voie, civilite, photo_profile, email, telephone, date_naissance, mot_de_passe, complement_1, complement_2, complement_3, id_adresse
    from sae._utilisateur
    inner join sae._adresse on sae._adresse.id = sae._utilisateur.id_adresse
    where sae._utilisateur.id = $id_utilisateur;";
    $rep_utilisateur = request($query_utilisateur, true);
    $id_add=$rep_utilisateur['id_adresse'];
    
    //$id = $rep_utilisateur['id'];
    $prenom = $rep_utilisateur['prenom'];
    $nom = $rep_utilisateur['nom'];
    $ville = $rep_utilisateur['commune'];
    $region = $rep_utilisateur['region'];
    $departement = $rep_utilisateur['departement'];
    $numero = $rep_utilisateur['numero'];
    $voie = $rep_utilisateur['nom_voie'];
    $pays = $rep_utilisateur['pays'];
    $email = $rep_utilisateur['email'];
    $telephone = $rep_utilisateur['telephone'];
    $mdp = $rep_utilisateur['mot_de_passe'];
    $civilite = $rep_utilisateur['civilite'];
    $date_naissance = $rep_utilisateur['date_naissance'];
    $pseudo = $rep_utilisateur['pseudo'];
    $code_postal= $rep_utilisateur['code_postal'];

    $complement1 = $rep_utilisateur['complement_1'];
    $complement2 = $rep_utilisateur['complement_2'];
    $complement3 = $rep_utilisateur['complement_3'];

    if ($rep_utilisateur['civilite'] == "Mr"){
        $genre = "Homme";
    } else if ($rep_utilisateur['civilite'] == "Mme"){
        $genre = "Femme";
    } else {
        $genre = "Autre";
    }
    $src_photo = $rep_utilisateur['photo_profile'];
    $passwordVerify=false;
    $passwordLengthInvalid=false;
    $passwordMismatch=false;
    $dateMin = date('Y') - 16 . '-01-01';
    $mailInvalid = false;
    $emailExists = false;
    $allow=true;

    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if (isset($_POST['form_type'])){
            $form_type = $_POST['form_type'];
            switch ($form_type){
                case 'infos_personnelles':
                    $nom = empty($_POST['nom']) ? $rep_utilisateur['nom'] : clean_input($_POST['nom']);
                    $prenom = empty($_POST['prenom']) ? $rep_utilisateur['prenom'] : clean_input($_POST['prenom']);
                    $pseudo = empty($_POST['pseudo']) ? $rep_utilisateur['pseudo'] : clean_input($_POST['pseudo']);
                    $civilite = empty($_POST['genre']) ? $rep_utilisateur['civilite'] : clean_input($_POST['genre']);
                    $date_naissance = empty($_POST['date_naissance']) ? $rep_utilisateur['date_naissance'] : (new DateTime($_POST["date_naissance"]))->format("Y-m-d");
                    $telephone = empty($_POST['telephone']) ? $rep_utilisateur['telephone'] : clean_input($_POST['telephone']);
                    $email = empty($_POST['email']) ? $rep_utilisateur['email'] : clean_input($_POST['email']);
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $mailInvalid = true;
                        $allow=false;
                    }
                    if ($email!=$rep_utilisateur['email']) {
                        $query = "SELECT COUNT(*) AS count FROM sae._utilisateur WHERE email = '$email'";
                        $result = request($query, true);
                        if ($result && $result["count"] > 0) {
                            $emailExists = true;
                            $allow=false;
                        }
                    }

                    if ($allow) {
                        request("UPDATE sae._utilisateur SET nom = '$nom', prenom = '$prenom', pseudo = '$pseudo', civilite = '$civilite', date_naissance = '$date_naissance', telephone = '$telephone', email = '$email' WHERE id =  $id_utilisateur");
                    }

                    break;
                

                case 'adresse':
                    $pays = empty($_POST['pays']) ? $rep_utilisateur['pays'] : clean_input($_POST['pays']);
                    $region = empty($_POST['region']) ? $rep_utilisateur['region'] : clean_input($_POST['region']);
                    $departement = empty($_POST['departement']) ? $rep_utilisateur['departement'] : clean_input($_POST['departement']);
                    $ville = empty($_POST['commune']) ? $rep_utilisateur['commune'] : clean_input($_POST['commune']);
                    $code_postal = empty($_POST['code_postal']) ? $rep_utilisateur['code_postal'] : clean_input($_POST['code_postal']);
                    $voie = empty($_POST['rue']) ? $rep_utilisateur['nom_voie'] : clean_input($_POST['rue']);
                    $numero = empty($_POST['numero']) ? $rep_utilisateur['numero'] : clean_input($_POST['numero']);
                    $complement1 = empty($_POST['complement1']) ? $rep_utilisateur['complement_1'] : clean_input($_POST['complement1']);
                    $complement2 = empty($_POST['complement2']) ? $rep_utilisateur['complement_2'] : clean_input($_POST['complement2']);
                    $complement3 = empty($_POST['complement3']) ? $rep_utilisateur['complement_3'] : clean_input($_POST['complement3']);
                    
                    request("UPDATE sae._adresse SET pays = '$pays', region = '$region', departement = '$departement', commune = '$ville', code_postal = '$code_postal', nom_voie = '$voie', numero = '$numero', complement_1 = '$complement1', complement_2 = '$complement2', complement_3 = '$complement3' WHERE id =  $id_add");

                    break;

                
                    case 'photo':
                        if (isset($_FILES["photo_profile"]["tmp_name"]) && $_FILES["photo_profile"]["tmp_name"] !== "") {
                            $extension = pathinfo($_FILES["photo_profile"]['name'], PATHINFO_EXTENSION);
                            $photo_path = "img/compte/profile_" . $rep_utilisateur['pseudo'] . "." . $extension;
                            $photo_path_bdd = "/compte/profile_" . $rep_utilisateur['pseudo'] . "." . $extension;
        
                            if (move_uploaded_file($_FILES["photo_profile"]["tmp_name"], $photo_path)) {
                                $sql = "UPDATE sae._utilisateur SET photo_profile = '$photo_path_bdd' WHERE id = $id_utilisateur";
                                request($sql, false);
        
                                $_SESSION["photo_user"] = $photo_path_bdd;
        
                                header('Location: consulter_mon_compte.php');
                                exit();
                            } else {
                                echo "Erreur lors du téléchargement du fichier.";
                            }
                        }
                        break;
                    


                case 'motdepas':
                    $current_mdp = clean_input($_POST['mdp']);
                    $new_mdp = empty($_POST['new_mdp']) ? $rep_utilisateur['mot_de_passe'] : clean_input($_POST['new_mdp']);
                    $new_mdp2 = empty($_POST['new_mdp2']) ? $rep_utilisateur['mot_de_passe'] : clean_input($_POST['new_mdp2']);
                    if (password_verify($current_mdp, $rep_utilisateur['mot_de_passe'])){
                        if ($new_mdp === $new_mdp2){
                            if (strlen($new_mdp) >= 8){
                                $new_hashed_mdp = password_hash($new_mdp, PASSWORD_DEFAULT);
                                request("UPDATE sae._utilisateur SET mot_de_passe = '$new_hashed_mdp' WHERE id = $id_utilisateur");

                            }else{
                                $passwordLengthInvalid=true;

                            }

                        }else{
                            $passwordMismatch=true;

                        }
                    }else{
                        $passwordVerify=true;
                    }
                    break;
            }
        }
    }

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/mon_compte.css">
    <title>Mon Compte</title>
    <script src="https://kit.fontawesome.com/7f17ac2dfc.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="wrapper">
    <?php     include "./header.php"; ?>
        <main class="main__container">
            <div class="detail_mon_compte__conteneur">
                <div id="loading-overlay" style="display: none;">
              <div class="loader"></div>
            </div>
                <div class="header_info_compte">
                    <h2>Mon Compte</h2>
                    <div class ="identifiant_client"><h3 id="identifiant_client">Identifiant client : </h3><h3><?= "  " . $id_utilisateur ?></h3></div>
                </div>
              
                <div class="compte_form">
                    <div class="info_perso_conteneur">
                        <h3>Informations personnelles</h3>
                        <?php if ($emailExists): ?>
                                <p class="login_invalid">Cette adresse e-mail est déjà utilisée.</p>
                            <?php endif; ?>
                            <?php if ($mailInvalid): ?>
                                <p class="login_invalid">Adresse e-mail invalide</p>
                            <?php endif; ?>
                        <form method="POST" action="">
                            <input type="hidden" name="form_type" value="infos_personnelles">
                            <div class="ligne">
                            <div class="compte__input">
                                    <label for="compte__nom">Nom</label>
                                    <input type="text" name="nom" id="compte__nom" value="<?= $nom ?>" placeholder="Votre nom" oninput="this.value = this.value.replace(/[^a-zA-Z\s']/g, '');">
                                </div>
                                <div class="compte__input">
                                    <label for="compte__prenom">Prénom</label>
                                    <input type="text" name="prenom" id="compte__prenom" value="<?= $prenom ?>"  placeholder="Votre prénom" oninput="this.value = this.value.replace(/[^a-zA-Z\s']/g, '');">
                                </div>
                                
                                <div class="compte__input">
                                    <label for="compte__pseudo">Pseudo</label>
                                    <input type="text" name="pseudo" id="compte__pseudo" value="<?= $pseudo ?>" placeholder="Votre pseudo" >
                                </div>
                            </div>
                            <div class="ligne">
                                <div class="compte__input">
                                    <label for="genre">Civilité</label>
                                    <select id="genre" name="genre" >
                                        <option value="Mr" <?php if ($civilite == "Mr") echo 'selected'; ?>>Homme</option>
                                        <option value="Mme" <?php if ($civilite == "Mme") echo 'selected'; ?>>Femme</option>
                                    </select>
                                </div>
                                <div class="compte__input">
                                    <label for="compte__date_naissance">Date de naissance</label>
                                    <input type="date" name="date_naissance" id="compte__date_naissance" value ="<?=$date_naissance?>" max="<?php echo $dateMin;?>" >
                                </div>
                                <div class="compte__input">
                                    <label for="compte__telephone">Téléphone portable</label>
                                    <input type="text" name="telephone" id="compte__telephone" value="<?= $telephone ?>" placeholder="Votre numéro" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                </div>
                            </div>
                            <div class="ligne">
                                <div class="compte__input">
                                    <label for="compte__email">Adresse e-mail</label>
                                    <input type="email" name="email" id="compte__email" value="<?= $email ?>" placeholder="Ex : exemple@domaine.com" >
                                </div>
                                <input class="sauvegarde" type="submit" value="Enregistrer">
                            </div>
                        </form>
                    </div>
                    

                
                    <div class= "adresse_conteneur">
                        <h3>Adresse de facturation</h3>

                        <form method="POST" action="">
                            <input type="hidden" name="form_type" value="adresse">
                            <div class="ligne">
                                <div class="compte__input">
                                    <label for="compte__pays">Pays</label>
                                    <input type="text" name="pays" id="compte__pays" value="<?= $pays ?>" placeholder="Votre pays" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">
                                    <div id="autocomplete-list-pays" class="autocomplete-suggestions"></div>
                                </div>
                                <div class="compte__input">
                                    <label for="compte__region">Région</label>
                                    <input type="text" name="region" id="compte__region" value="<?= $region ?>" placeholder="Votre région" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">
                                    <div id="autocomplete-list-regions" class="autocomplete-suggestions"></div>
                                </div>
                            </div>
                            <div class="ligne">
                                <div class="compte__input">
                                    <label for="compte__departement">Département</label>
                                    <input type="text" name="departement" id="compte__departement" value="<?= $departement ?>" placeholder="Votre département" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">
                                    <div id="autocomplete-list-departement" class="autocomplete-suggestions"></div>
                                </div>
                                <div class="compte__input">
                                    <label for="compte__ville">Ville</label>
                                    <input type="text" name="commune" id="compte__ville" value="<?= $ville ?>" placeholder="Votre ville" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">
                                    <div id="autocomplete-list-ville" class="autocomplete-suggestions"></div>
                                </div>
                                <div class="compte__input">
                                    <label for="code_postal">Code postal</label>
                                    <input type="text" name="code_postal" id="compte__code_postal" value="<?= $code_postal ?>" placeholder="Votre code postal" oninput="this.value = this.value.replace(/[^0-9]/g, '');" readonly>
                                </div>
                            </div>
                            <div class="ligne">
                                <div class="compte__input">
                                    <label for="compte__rue">Nom de la rue</label>
                                    <input type="text" name="rue" id="compte__rue" value="<?= $voie ?>" placeholder="Votre rue" oninput="this.value = this.value.replace(/[^a-zA-Z\s']/g, '');">
                                </div>
                                <div class="compte__input">
                                    <label for="compte__rue">Numéro de rue</label>
                                    <input type="number" name="numero" id="compte__rue_numero" value="<?= $numero ?>" placeholder="Numéro de votre rue" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                </div>
                            </div>
                            <div class="ligne">
                                <div class="compte__input">
                                    <label for="compte__complement">Complément d'adresse</label>
                                    <input type="text" name="complement1" id="compte__complement1"  value= "<?= $complement1?>" placeholder="Complément" >
                                </div>
                                <div class="compte__input">
                                    <label for="compte__complement">Complément d'adresse</label>
                                    <input type="text" name="complement2" id="compte__complement2" value= "<?= $complement2?>" placeholder="Complément" >
                                </div>
                            </div>
                            <div class="ligne">
                                <div class="compte__input">
                                    <label for="compte__complement">Complément d'adresse</label>
                                    <input type="tel" name="complement3" id="compte__complement3" value= "<?= $complement3?>" placeholder="Complément" >
                                </div>
                                <input class="sauvegarde" type="submit" value="Enregistrer">
                            </div>
                        </form>
                    </div>
                    <div class="ensemble_flex">
                        <form method="post" class= "photo_conteneur" id="photo_client" enctype="multipart/form-data">
                            <input type="hidden" name="form_type" value="photo">
                            <h3>Votre photo de profil</h3>
                            <img src="/img/<?= $src_photo ?>" alt="photo de profil de l'utilisateur">
                            <div class="changer_photo">
                                <label for="photo_profile">Changer la photo</label>
                                <div class="ligne">
                                    <input type="file" id="photo_profile" name="photo_profile" accept="image/*"> 
                                    <input class="sauvegarde" type="submit" value="Enregistrer">
                                </div>
                            </div>
                        </form>
                        <form class= "mdp_conteneur" id="mdp_client" method="post"> 
                            <input type="hidden" name="form_type" value="motdepas"> 
                            <h3>Modifier le mot de passe</h3>
                            <div class="compte__input">
                                <label for="compte__mdp">Mot de passe actuel :</label>
                                <div class="ligne">
                                    <input type="password" id="compte__mdp" name="mdp">
                                </div>
                            </div>
                            <div class="changer__mdp">
                                <div class="ligne">
                                    <div class="compte__input">
                                        <label for="compte__mdp">Nouveau mot de passe :</label>
                                        <input type="password" id="new__mdp" name="new_mdp" placeholder="Au moins 8 caractères">
                                    </div>
                                    <div class="compte__input">
                                        <label for="compte__mdp">Confirmez le mot de passe :</label>
                                        <input type="password" id="new__mdp2" name="new_mdp2" >
                                    </div>

                                </div>
                            </div>
                            <?php if($passwordVerify):?>
                                <p class="login_invalid">Mot de passe ivalide !</p>
                            <?php endif; ?>
                            <?php if ($passwordMismatch): ?>
                                <p class="login_invalid">Les mots de passe ne correspondent pas !</p>
                            <?php endif; ?>
                            <?php if ($passwordLengthInvalid): ?>
                                <p class="login_invalid">Le mot de passe doit contenir au moins 8 caractères.</p>
                            <?php endif; ?>
                            <input class="sauvegarde" type="submit" value="Enregistrer" style="width:30%;">

                        </form>
                    </div>  
                </div>
            </div>
        </main>
        <?php include "footer.php"; ?>
    </div>
    <script src="js/consulter_mon_compte.js"></script>
</body>
</html>
