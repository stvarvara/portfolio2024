<?php
session_start();
require_once "../utils.php";

if (isset($_SESSION["client_id"])) {
    redirect();
}

$status = false;
$passwordMismatch = false;
$emailExists = false;
$mailInvalid = false;
$passwordLengthInvalid = false;
$allow = true;
$dateMin = date('Y') - 16 . '-01-01';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adresse = [
        "pays" => $_POST["pays"],
        "region" => $_POST["region"],
        "departement" => $_POST["departement"],
        "code_postal" => $_POST["code"],
        "commune" => $_POST["commune"],
        "nom_voie" => $_POST["rue"],
        "numero" => $_POST["numero"],
        "complement_1" => !empty($_POST["complement1"]) ? $_POST["complement1"] : null,
        "complement_2" => !empty($_POST["complement2"]) ? $_POST["complement2"] : null,
        "complement_3" => !empty($_POST["complement3"]) ? $_POST["complement3"] : null
    ];

    $util = [
        "nom" => $_POST["nom"],
        "prenom" => $_POST["prenom"],
        "pseudo" => $_POST["pseudo"],
        "date_naissance" => (new DateTime($_POST["date_naissance"]))->format("Y-m-d"),
        "civilite" => $_POST["civilite"],
        "telephone" => $_POST["telephone"],
        "email" => $_POST["email"],
        "mot_de_passe" => $_POST["mot_de_passe"],
        "photo_profile" => "img/anonymus.webp"
    ];
    $pseudo=$util['pseudo'];

    $mot_de_passe2 = $_POST['mot_de_passe2'];

    // Vérification des champs requis
    if (
        empty($util["nom"]) || empty($util["prenom"]) || empty($util["pseudo"]) || empty($util["date_naissance"]) ||
        empty($util["telephone"]) || empty($util["email"]) || empty($util["mot_de_passe"]) ||
        empty($mot_de_passe2) || empty($adresse["pays"]) || empty($adresse["region"]) ||
        empty($adresse["departement"]) || empty($adresse["commune"]) || empty($adresse["code_postal"]) ||
        empty($adresse["nom_voie"])
    ) {
        $status = true;
    }

    if (!filter_var($util["email"], FILTER_VALIDATE_EMAIL)) {
        $mailInvalid = true;
        $allow = false;
    }

    $email = $util["email"];
    $query = "SELECT COUNT(*) AS count FROM sae._utilisateur WHERE email = '$email'";
    $result = request($query, true);
    if ($result && $result["count"] > 0) {
        $emailExists = true;
        $allow = false;
    }
    if (strlen($util["mot_de_passe"]) < 8) {
        $passwordLengthInvalid = true;
        $allow = false;
    }

    // Vérification de la correspondance des mots de passe
    if ($util["mot_de_passe"] !== $mot_de_passe2) {
        $passwordMismatch = true;
        $allow = false;
    }
    if ($allow) {
        // Hachage du mot de passe
        $util["mot_de_passe"] = password_hash($util["mot_de_passe"], PASSWORD_DEFAULT);

        // Insertion de l'adresse
        $adresse_columns = array_keys($adresse);
        $adresse_values = array_values($adresse);
        $adresse_id = insert("sae._adresse", $adresse_columns, $adresse_values);



        if ($adresse_id) {
            $util["id_adresse"] = $adresse_id;
            $user_columns = array_keys($util);
            $user_values = array_values($util);
            $user_id = insert('sae._utilisateur', $user_columns, $user_values);

            if ($user_id) {
                if (isset($_FILES["photo_profil"]["tmp_name"]) && $_FILES["photo_profil"]["tmp_name"] !== "") {
                    $extension = pathinfo($_FILES["photo_profil"]['name'], PATHINFO_EXTENSION);
                    $photo_path = "img/compte/profile_$pseudo.$extension";
                    $photo_path_bdd = "/compte/profile_$pseudo.$extension";
                    move_uploaded_file($_FILES["photo_profil"]["tmp_name"], $photo_path);
                } else {
                    $extension = pathinfo("img/anonymus.webp", PATHINFO_EXTENSION);
                    $photo_path = "img/compte/profile_anonymous.$extension";
                    $photo_path_bdd = "/compte/profile_anonymous.$extension";
                }

                request("UPDATE sae._utilisateur SET photo_profile = '$photo_path_bdd' WHERE id = $user_id");


                insert('sae._compte_client', ["id"], [$user_id], false);
                $_SESSION["client_id"] = $user_id;
                $_SESSION["photo_user"] = $photo_path_bdd;
                redirect();
                exit;
            }
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/connect.css">
    <title>Créer un compte</title>
    <script src="https://kit.fontawesome.com/7f17ac2dfc.js" crossorigin="anonymous"></script>
</head>

<body class="page">
    <div class="wrapper">
        <?php require_once "header.php" ?>
        <main class="main">
            <div class="main__container">
                <div class="connect-container">
                    <div class="connect__from">
                        <h1>Créer un compte <img src="img/hello.webp" alt="Hello"></h1>
                        <p>Plongez dans l'authenticité bretonne en choisissant parmi une gamme de logements uniques.</p>
                        <form id="createAccountForm" enctype="multipart/form-data" method="post">
                            <div class="connect__input__ligne">
                                <div class="connect__input">
                                    <label for="connect__name">Nom</label>
                                    <input type="text" name="nom" id="connect__name" placeholder="Votre nom" required oninput="this.value = this.value.replace(/[^a-zA-Z\s-]/g, '');">
                                </div>
                                <div class="connect__input">
                                    <label for="connect__surname">Prénom</label>
                                    <input type="text" name="prenom" id="connect__surname" placeholder="Votre prénom"
                                        required oninput="this.value = this.value.replace(/[^a-zA-Z\s-]/g, '');">
                                </div>
                            </div>
                            <div class="connect__input__ligne">
                                <div class="connect__input">
                                    <label for="connect__username">Pseudo</label>
                                    <input type="text" name="pseudo" id="connect__username" placeholder="Votre pseudo"
                                        required>
                                </div>
                                <div class="connect__input">
                                    <label for="connect__birthdate">Date de naissance</label>
                                    <input type="date" name="date_naissance" id="connect__birthdate" required max="<?php echo $dateMin; ?>">
                                </div>
                            </div>
                            <div class="connect__input__ligne">
                                <div class="connect__input">
                                    <label for="connect__phone">Téléphone portable</label>
                                    <input type="tel" name="telephone" id="connect__phone" placeholder="Votre numéro"
                                        required oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                </div>
                                <div class="connect__input">
                                    <label for="connect__gender">Civilité</label>
                                    <select name="civilite" id="connect__gender" required>
                                        <option value="Mr">Mr</option>
                                        <option value="Mme">Mme</option>
                                    </select>
                                </div>
                            </div>
                            <div class="connect__input">
                                <label for="connect__profile">Photo de profil</label>
                                <input type="file" name="photo_profil" id="connect__profile" accept="image/*">
                            </div>

                            <div class="connect__input connect__input__add">
                                <label for="connect__pass">Adresse de facturation</label>
                                <div class="connect__input__ligne">
                                    <div class="rel">
                                         <input type="text" name="pays" id="connect__pays" placeholder="Pays" required oninput="this.value = this.value.replace(/[^a-zA-Z\s-]/g, '');">
                                    <div id="autocomplete-list-pays" class="autocomplete-suggestions"></div>
                                    </div>
                                   <div class="rel">
                                        <input type="text" name="region" id="connect__region" placeholder="Région" required oninput="this.value = this.value.replace(/[^a-zA-Z\s-]/g, '');">
                                        <div id="autocomplete-list-region" class="autocomplete-suggestions"></div>
                                   </div>
                                    <div class="rel">
                                        <input type="text" name="departement" id="connect__departement" placeholder="Département" required oninput="this.value = this.value.replace(/[^a-zA-Z\s-]/g, '');">
                                        <div id="autocomplete-list-departement" class="autocomplete-suggestions"></div>
                                    </div>
                                    <div class="rel">
                                        <input type="text" name="commune" id="connect__ville" placeholder="Ville" required oninput="this.value = this.value.replace(/[^a-zA-Z\s-]/g, '');">
                                        <div id="autocomplete-list-ville" class="autocomplete-suggestions"></div>
                                    </div>
                                    <input type="text" name="code" id="connect__code" placeholder="Code postal" required oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                    <input type="number" name="numero" id="connect__numero" placeholder="Numéro de rue" required>
                                    <input type="text" name="rue" id="connect__rue" placeholder="Nom de la rue" required oninput="this.value = this.value.replace(/[^a-zA-Z\s-]/g, '');">
                                </div>
                                <div class="connect__input__ligne">
                                    <input type="text" name="complement1" id="connect__complement1"
                                        placeholder="Complément">
                                    <input type="text" name="complement2" id="connect__complement2"
                                        placeholder="Complément">
                                    <input type="text" name="complement3" id="connect__complement3"
                                        placeholder="Complément">
                                </div>
                            </div>
                            <div class="connect__input">
                                <label for="connect__email">Adresse e-mail</label>
                                <input type="email" name="email" id="connect__email"
                                    placeholder="Ex : exemple@domaine.com" required>
                            </div>
                            <div class="connect__input">
                                <label for="connect__pass">Mot de passe</label>
                                <input type="password" name="mot_de_passe" id="connect__pass"
                                    placeholder="Au moins 8 caractères" required>
                            </div>
                            <div class="connect__input">
                                <label for="connect__pass2">Entrez le mot de passe à nouveau</label>
                                <input type="password" name="mot_de_passe2" id="connect__pass2"
                                    placeholder="Confirmez le mot de passe" required>
                            </div>
                            <?php if ($passwordMismatch): ?>
                                <p class="login_invalid">Les mots de passe ne correspondent pas !</p>
                            <?php endif; ?>
                            <?php if ($emailExists): ?>
                                <p class="login_invalid">Cette adresse e-mail est déjà utilisée.</p>
                            <?php endif; ?>
                            <?php if ($status): ?>
                                <p class="login_invalid">Tous les champs obligatoires doivent être remplis.</p>
                            <?php endif; ?>
                            <?php if ($mailInvalid): ?>
                                <p class="login_invalid">Adresse e-mail invalide</p>
                            <?php endif; ?>
                            <?php if ($passwordLengthInvalid): ?>
                                <p class="login_invalid">Le mot de passe doit contenir au moins 8 caractères.</p>
                            <?php endif; ?>
                            <input type="submit" class="hover" value="Continuer">
                        </form>
                        <p class="p_ligne">Ou</p>
                        <p style="align-self: center; text-align:center;">Vous possédez déjà un compte ? <a href="login.php"
                                style="color: #5669FF;">Se connecter</a></p>
                    </div>
                    <div class="slider">
                        <div class="slides">
                            <div class="slide"><img src="../img/slider1.webp" alt="Slide 1"></div>
                            <div class="slide"><img src="../img/slider2.webp" alt="Slide 2"></div>
                            <div class="slide"><img src="../img/slider3.webp" alt="Slide 3"></div>
                            <div class="slide"><img src="../img/slider4.webp" alt="Slide 4"></div>
                        </div>
                        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                        <a class="next" onclick="plusSlides(1)">&#10095;</a>
                    </div>

                </div>
            </div>
        </main>
        <?php require_once "footer.php" ?>
    </div>
    <script src="../js/creer_compte.js"></script>
</body>

</html>