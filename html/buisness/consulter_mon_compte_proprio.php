<?php
session_start();
require_once "../../utils.php";

function clean_input($data)
{
    return htmlspecialchars(trim($data));
}

function validateIBAN($iban)
{
    $iban = strtoupper(str_replace(" ", "", $iban));
    $iban_length = [
        "AL" => 28,
        "AD" => 24,
        "AT" => 20,
        "AZ" => 28,
        "BH" => 22,
        "BY" => 28,
        "BE" => 16,
        "BA" => 20,
        "BR" => 29,
        "BG" => 22,
        "CR" => 22,
        "HR" => 21,
        "CY" => 28,
        "CZ" => 24,
        "DK" => 18,
        "DO" => 28,
        "EG" => 29,
        "SV" => 28,
        "EE" => 20,
        "FO" => 18,
        "FI" => 18,
        "FR" => 27,
        "GE" => 22,
        "DE" => 22,
        "GI" => 23,
        "GR" => 27,
        "GL" => 18,
        "GT" => 28,
        "HU" => 28,
        "IS" => 26,
        "IQ" => 23,
        "IE" => 22,
        "IL" => 23,
        "IT" => 27,
        "JO" => 30,
        "KZ" => 20,
        "XK" => 20,
        "KW" => 30,
        "LV" => 21,
        "LB" => 28,
        "LI" => 21,
        "LT" => 20,
        "LU" => 20,
        "MT" => 31,
        "MR" => 27,
        "MU" => 30,
        "MD" => 24,
        "MC" => 27,
        "ME" => 22,
        "NL" => 18,
        "MK" => 19,
        "NO" => 15,
        "PK" => 24,
        "PS" => 29,
        "PL" => 28,
        "PT" => 25,
        "QA" => 29,
        "RO" => 24,
        "LC" => 32,
        "SM" => 27,
        "ST" => 25,
        "SA" => 24,
        "RS" => 22,
        "SC" => 31,
        "SK" => 24,
        "SI" => 19,
        "ES" => 24,
        "SE" => 24,
        "CH" => 21,
        "TL" => 23,
        "TN" => 24,
        "TR" => 26,
        "UA" => 29,
        "AE" => 23,
        "GB" => 22,
        "VG" => 24,
    ];

    $country_code = substr($iban, 0, 2);
    if (
        !array_key_exists($country_code, $iban_length) ||
        strlen($iban) != $iban_length[$country_code]
    ) {
        return false;
    }

    $iban = substr($iban, 4) . substr($iban, 0, 4);
    $iban = preg_replace_callback(
        "/[A-Z]/",
        function ($match) {
            return ord($match[0]) - 55;
        },
        $iban
    );

    $checksum = intval($iban[0]);
    for ($i = 1, $len = strlen($iban); $i < $len; $i++) {
        $checksum = intval($checksum . $iban[$i]) % 97;
    }

    return $checksum === 1;
}

function validateBIC($bic)
{
    return preg_match(
        '/^[A-Za-z]{4}[A-Za-z]{2}[A-Za-z0-9]{2}([A-Za-z0-9]{3})?$/',
        $bic
    );
}

function validateAccountHolder($holder)
{
    return !empty($holder) && is_string($holder);
}

if (isset($_POST["valider"]) && $_POST["action"] == "update") {
    $date_fin = (new DateTime($_POST["date_fin"]))->format("Y-m-d");
    $date_debut = (new DateTime($_POST["date_debut"]))->format("Y-m-d");
    $token = $_POST["token"];
    $id_logements = $_POST["check_logement"] ?? [];

    $sql =
        'UPDATE sae._ical_token SET date_debut = \'' .
        $date_debut .
        '\', date_fin = \'' .
        $date_fin .
        '\' WHERE token = \'' .
        $token .
        '\'';

    request($sql);

    $alreadyIn = explode("/", $_POST["alreadyIn"]) ?? [];

    //LOGEMENT A SUPPRIMER
    $valuesNotInIdLogements = array_diff($alreadyIn, $id_logements);

    $valuesNotInIdLogements = array_values($valuesNotInIdLogements);
    if (!empty($valuesNotInIdLogements[0])) {
        foreach ($valuesNotInIdLogements as $id) {
            $sql =
                "DELETE FROM sae._ical_token_logements WHERE logement = " . $id;

            request($sql);
        }
    }
    $logementToInsert = array_filter(
        $id_logements,
        fn($v) => !in_array($v, $alreadyIn)
    );
    $logementToInsert = array_values($logementToInsert);
    if (!empty($logementToInsert[0])) {
        foreach ($logementToInsert as $id) {
            $sql = "INSERT INTO sae._ical_token_logements VALUES ";
            $sql .= '(\'' . $token . '\', ' . $id . ")";

            request($sql);
        }
    }
}
if (isset($_POST["valider"]) && $_POST["action"] == "create") {
    $date_fin = (new DateTime($_POST["date_fin"]))->format("Y-m-d");
    $date_debut = (new DateTime($_POST["date_debut"]))->format("Y-m-d");
    $user_id = (int) $_SESSION["business_id"];
    $id_logements = $_POST["check_logement"] ?? [];
    $sql = "SELECT sae.generate_ical_token_for_user(" . $user_id;
    $sql .= ', \'' . $date_debut . '\',\'' . $date_fin . '\') as token;';
    $res = request($sql, 0);
    $token = $res[0]["token"];
    foreach ($id_logements as $id) {
        $sql = "INSERT INTO sae._ical_token_logements VALUES ";
        $sql .= '(\'' . $token . '\', ' . $id . ")";
        request($sql);
    }
}

if (isset($_POST["valider-api"]) && $_POST["action"] == "update") {
    $data = [];
    $api = $_POST["api"];
    $bin = "0000";

    foreach ($_POST["check_logement"] as $val) {
        $data[] = explode("/", $val)[1];
    }

    if (in_array("admin", $data)) {
        $bin[0] = "1";
    }
    if (in_array("indispo", $data)) {
        $bin[1] = "1";
    }
    if (in_array("planning", $data)) {
        $bin[2] = "1";
    }
    if (in_array("lister", $data)) {
        $bin[3] = "1";
    }

    $sql =
        " UPDATE sae._api_keys SET permission = " . bindec($bin) . "::BIT(4)";
    $sql .= ' WHERE key = \'' . $api . '\'';
    request($sql);
}

if (isset($_POST["valider-api"]) && $_POST["action"] == "create") {
    $user_id = (int) $_SESSION["business_id"];
    $data = [];
    $api = $_POST["api"];
    $bin = "0000";
    foreach ($_POST["check_logement"] as $val) {
        $data[] = explode("/", $val)[1];
    }
    if (in_array("admin", $data)) {
        $bin[0] = "1";
    }
    if (in_array("indispo", $data)) {
        $bin[1] = "1";
    }
    if (in_array("planning", $data)) {
        $bin[2] = "1";
    }
    if (in_array("lister", $data)) {
        $bin[3] = "1";
    }

    $sql = "SELECT sae.add_api_key_for_proprietor(" . $user_id;
    $sql .= ', \'' . $bin . '\') as api;';

    $res = request($sql, 0);
    //$api = $res[0]['api'];
}

$id_utilisateur = buisness_connected_or_redirect();
$query_utilisateur = "select nom, prenom, pseudo, commune, pays, region, departement,
    numero, nom_voie, civilite, photo_profile, email, telephone, date_naissance, mot_de_passe, iban, bic, titulaire, complement_1, complement_2, complement_3, _adresse.code_postal, id_adresse,sae._carte_identite.piece_id_recto, sae._carte_identite.piece_id_verso
    from sae._utilisateur
    inner join sae._adresse on sae._adresse.id = sae._utilisateur.id_adresse
    inner join sae._compte_proprietaire on sae._compte_proprietaire.id = sae._utilisateur.id
    inner join sae._carte_identite on sae._carte_identite.id_propr= sae._compte_proprietaire.id
    where sae._utilisateur.id = $id_utilisateur;";
$rep_utilisateur = request($query_utilisateur, true);
$id = $id_utilisateur;
$id_add = $rep_utilisateur["id_adresse"];

$recto_photo = $rep_utilisateur["piece_id_recto"];
$verso_photo = $rep_utilisateur["piece_id_verso"];

$recto = "../img$recto_photo";
$verso = "../img$verso_photo";

$prenom = $rep_utilisateur["prenom"];
$nom = $rep_utilisateur["nom"];
$ville = $rep_utilisateur["commune"];
$region = $rep_utilisateur["region"];
$departement = $rep_utilisateur["departement"];
$numero = $rep_utilisateur["numero"];
$voie = $rep_utilisateur["nom_voie"];
$pays = $rep_utilisateur["pays"];
$email = $rep_utilisateur["email"];
$telephone = $rep_utilisateur["telephone"];
$mdp = $rep_utilisateur["mot_de_passe"];
$civilite = $rep_utilisateur["civilite"];
$date_naissance = $rep_utilisateur["date_naissance"];
$pseudo = $rep_utilisateur["pseudo"];
$code_postal = $rep_utilisateur["code_postal"];

$complement1 = $rep_utilisateur["complement_1"];
$complement2 = $rep_utilisateur["complement_2"];
$complement3 = $rep_utilisateur["complement_3"];

$bic = $rep_utilisateur["bic"];
$iban = $rep_utilisateur["iban"];
$titulaire = $rep_utilisateur["titulaire"];

$ibanInvalid = false;
$bicInvalid = false;
$accountHolderInvalid = false;

if ($rep_utilisateur["civilite"] == "Mr") {
    $genre = "Homme";
} elseif ($rep_utilisateur["civilite"] == "Mme") {
    $genre = "Femme";
} else {
    $genre = "Autre";
}
$src_photo = $rep_utilisateur["photo_profile"];
$passwordVerify = false;
$passwordLengthInvalid = false;
$passwordMismatch = false;
$dateMin = date("Y") - 16 . "-01-01";
$mailInvalid = false;
$emailExists = false;
$allow = true;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["form_type"])) {
        $form_type = $_POST["form_type"];
        switch ($form_type) {
            case "infos_personnelles":
                $nom = empty($_POST["nom"])
                    ? $rep_utilisateur["nom"]
                    : clean_input($_POST["nom"]);
                $prenom = empty($_POST["prenom"])
                    ? $rep_utilisateur["prenom"]
                    : clean_input($_POST["prenom"]);
                $pseudo = empty($_POST["pseudo"])
                    ? $prenom . strtoupper("$nom[0]")
                    : clean_input($_POST["pseudo"]);
                $civilite = empty($_POST["genre"])
                    ? $rep_utilisateur["civilite"]
                    : clean_input($_POST["genre"]);
                $date_naissance = empty($_POST["date_naissance"])
                    ? $rep_utilisateur["date_naissance"]
                    : (new DateTime($_POST["date_naissance"]))->format("Y-m-d");
                $telephone = empty($_POST["telephone"])
                    ? $rep_utilisateur["telephone"]
                    : clean_input($_POST["telephone"]);
                $email = empty($_POST["email"])
                    ? $rep_utilisateur["email"]
                    : clean_input($_POST["email"]);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $mailInvalid = true;
                    $allow = false;
                }
                if ($email != $rep_utilisateur["email"]) {
                    $query = "SELECT COUNT(*) AS count FROM sae._utilisateur WHERE email = '$email'";
                    $result = request($query, true);
                    if ($result && $result["count"] > 0) {
                        $emailExists = true;
                        $allow = false;
                    }
                }

                if ($allow) {
                    request(
                        "UPDATE sae._utilisateur SET nom = '$nom', prenom = '$prenom', pseudo = '$pseudo', civilite = '$civilite', date_naissance = '$date_naissance', telephone = '$telephone', email = '$email' WHERE id =  $id_utilisateur"
                    );
                }

                break;

            case "adresse":
                $pays = empty($_POST["pays"])
                    ? $rep_utilisateur["pays"]
                    : clean_input($_POST["pays"]);
                $region = empty($_POST["region"])
                    ? $rep_utilisateur["region"]
                    : clean_input($_POST["region"]);
                $departement = empty($_POST["departement"])
                    ? $rep_utilisateur["departement"]
                    : clean_input($_POST["departement"]);
                $ville = empty($_POST["commune"])
                    ? $rep_utilisateur["commune"]
                    : clean_input($_POST["commune"]);
                $code_postal = empty($_POST["code_postal"])
                    ? $rep_utilisateur["code_postal"]
                    : clean_input($_POST["code_postal"]);
                $voie = empty($_POST["rue"])
                    ? $rep_utilisateur["nom_voie"]
                    : clean_input($_POST["rue"]);
                $numero = empty($_POST["numero"])
                    ? $rep_utilisateur["numero"]
                    : clean_input($_POST["numero"]);
                $complement1 = empty($_POST["complement1"])
                    ? $rep_utilisateur["complement_1"]
                    : clean_input($_POST["complement1"]);
                $complement2 = empty($_POST["complement2"])
                    ? $rep_utilisateur["complement_2"]
                    : clean_input($_POST["complement2"]);
                $complement3 = empty($_POST["complement3"])
                    ? $rep_utilisateur["complement_3"]
                    : clean_input($_POST["complement3"]);

                request(
                    "UPDATE sae._adresse SET pays = '$pays', region = '$region', departement = '$departement', commune = '$ville', code_postal = '$code_postal', nom_voie = '$voie', numero = '$numero', complement_1 = '$complement1', complement_2 = '$complement2', complement_3 = '$complement3' WHERE id =  $id_add"
                );

                break;

            case "photo":
                if (
                    isset($_FILES["photo_profile"]["tmp_name"]) &&
                    $_FILES["photo_profile"]["tmp_name"] !== ""
                ) {
                    $extension = pathinfo(
                        $_FILES["photo_profile"]["name"],
                        PATHINFO_EXTENSION
                    );
                    $photo_path =
                        "../img/compte/profile_" .
                        $rep_utilisateur["pseudo"] .
                        "." .
                        $extension;
                    $photo_path_bdd =
                        "/compte/profile_" .
                        $rep_utilisateur["pseudo"] .
                        "." .
                        $extension;

                    if (
                        move_uploaded_file(
                            $_FILES["photo_profile"]["tmp_name"],
                            $photo_path
                        )
                    ) {
                        $sql = "UPDATE sae._utilisateur SET photo_profile = '$photo_path_bdd' WHERE id = $id_utilisateur";
                        request($sql, false);

                        $_SESSION["business_photo"] = $photo_path_bdd;

                        header("Location: consulter_mon_compte_proprio.php");
                        exit();
                    } else {
                        break;
                    }
                } else {
                    break;
                }

            case "motdepas":
                $current_mdp = clean_input($_POST["mdp"]);
                $new_mdp = empty($_POST["new_mdp"])
                    ? $rep_utilisateur["mot_de_passe"]
                    : clean_input($_POST["new_mdp"]);
                $new_mdp2 = empty($_POST["new_mdp2"])
                    ? $rep_utilisateur["mot_de_passe"]
                    : clean_input($_POST["new_mdp2"]);
                if (
                    password_verify(
                        $current_mdp,
                        $rep_utilisateur["mot_de_passe"]
                    )
                ) {
                    if ($new_mdp === $new_mdp2) {
                        if (strlen($new_mdp) >= 8) {
                            $new_hashed_mdp = password_hash(
                                $new_mdp,
                                PASSWORD_DEFAULT
                            );
                            request(
                                "UPDATE sae._utilisateur SET mot_de_passe = '$new_hashed_mdp' WHERE id = $id_utilisateur"
                            );
                        } else {
                            $passwordLengthInvalid = true;
                        }
                    } else {
                        $passwordMismatch = true;
                    }
                } else {
                    $passwordVerify = true;
                }
                break;

            case "identite":
                $photo_recto_uploaded = false;
                $photo_verso_uploaded = false;

                // Traiter la photo recto
                if (
                    isset($_FILES["photo_recto"]["tmp_name"]) &&
                    $_FILES["photo_recto"]["tmp_name"] !== ""
                ) {
                    $extension_recto = pathinfo(
                        $_FILES["photo_recto"]["name"],
                        PATHINFO_EXTENSION
                    );
                    $photo_path_recto =
                        "../img/piece/$id" .
                        "_" .
                        $nom .
                        "_recto" .
                        "." .
                        $extension_recto;
                    $photo_path_recto_bdd =
                        "/piece/$id" .
                        "_" .
                        $nom .
                        "_recto" .
                        "." .
                        $extension_recto;

                    if (
                        move_uploaded_file(
                            $_FILES["photo_recto"]["tmp_name"],
                            $photo_path_recto
                        )
                    ) {
                        $sql = "UPDATE sae._carte_identite SET piece_id_recto = '$photo_path_recto_bdd' WHERE id = $id";
                        request($sql, false);
                        $photo_recto_uploaded = true;
                    }
                }

                // Traiter la photo verso
                if (
                    isset($_FILES["photo_verso"]["tmp_name"]) &&
                    $_FILES["photo_verso"]["tmp_name"] !== ""
                ) {
                    $extension_verso = pathinfo(
                        $_FILES["photo_verso"]["name"],
                        PATHINFO_EXTENSION
                    );
                    $photo_path_verso =
                        "../img/piece/$id" .
                        "_" .
                        $nom .
                        "_verso" .
                        "." .
                        $extension_verso;
                    $photo_path_verso_bdd =
                        "/piece/$id" .
                        "_" .
                        $nom .
                        "_verso" .
                        "." .
                        $extension_verso;

                    if (
                        move_uploaded_file(
                            $_FILES["photo_verso"]["tmp_name"],
                            $photo_path_verso
                        )
                    ) {
                        $sql = "UPDATE sae._carte_identite SET piece_id_verso = '$photo_path_verso_bdd' WHERE id = $id";
                        request($sql, false);
                        $photo_verso_uploaded = true;
                    }
                }

                // Si l'une des photos a été correctement téléchargée, rediriger l'utilisateur
                if ($photo_recto_uploaded || $photo_verso_uploaded) {
                    header("Location: consulter_mon_compte_proprio.php");
                    exit();
                }
                break;

            case "paiment":
                $iban = empty($_POST["iban"])
                    ? $rep_utilisateur["iban"]
                    : clean_input($_POST["iban"]);
                $bic = empty($_POST["bic"])
                    ? $rep_utilisateur["bic"]
                    : clean_input($_POST["bic"]);
                $titulaire = empty($_POST["titulaire"])
                    ? $rep_utilisateur["titulaire"]
                    : clean_input($_POST["titulaire"]);
                if (!validateIBAN($iban)) {
                    $ibanInvalid = true;
                    $allow = false;
                }

                if (!validateBIC($bic)) {
                    $bicInvalid = true;
                    $allow = false;
                }

                if (!validateAccountHolder($titulaire)) {
                    $accountHolderInvalid = true;
                    $allow = false;
                }
                if ($allow) {
                    $sql = "UPDATE sae._compte_proprietaire SET iban = '$iban', bic = '$bic', titulaire = '$titulaire' WHERE id = $id_utilisateur";
                    request($sql, false);
                    header("Location: consulter_mon_compte_proprio.php");
                    exit();
                } else {
                    break;
                }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/mon_compte.css">
    <link rel="stylesheet" href="../css/toast.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <title>Mon Compte</title>
    <script src="https://kit.fontawesome.com/7f17ac2dfc.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="wrapper">
        <?php include "./header.php"; ?>
        <main class="main__container">
            <div class="detail_mon_compte__conteneur">
                <div class="header_info_compte">
                    <h2>Mon Compte</h2>
                    <div class="identifiant_client">
                        <h3 id="identifiant_client">Identifiant propriétaire : </h3>
                        <h3><?= " " . $id_utilisateur ?></h3>
                    </div>
                </div>


                <div class="compte_form">
                    <div class="info_perso_conteneur">
                        <h3>Informations personnelles</h3>
                        <!-- <?php if ($emailExists): ?>
                                <p class="login_invalid">Cette adresse e-mail est déjà utilisée.</p>
                            <?php endif; ?>
                            <?php if ($mailInvalid): ?>
                                <p class="login_invalid">Adresse e-mail invalide</p>
                            <?php endif; ?> -->
                        <form method="POST" action="">
                            <input type="hidden" name="form_type" value="infos_personnelles">
                            <div class="ligne">
                                <div class="compte__input">
                                    <label for="compte__nom">Nom</label>
                                    <input type="text" name="nom" required="required" id="compte__nom" value="<?= $nom ?>"
                                        placeholder="Votre nom"
                                        oninput="this.value = this.value.replace(/[^a-zA-Z\s']/g, '');">
                                </div>
                                <div class="compte__input">
                                    <label for="compte__prenom">Prénom</label>
                                    <input type="text" required="required" name="prenom" id="compte__prenom" value="<?= $prenom ?>"
                                        placeholder="Votre prénom"
                                        oninput="this.value = this.value.replace(/[^a-zA-Z\s']/g, '');">
                                </div>

                                <div class="compte__input">
                                    <label for="compte__pseudo">Pseudo</label>
                                    <input type="text" name="pseudo" id="compte__pseudo" value="<?= $pseudo ?>"
                                        placeholder="Votre pseudo">
                                </div>
                            </div>
                            <div class="ligne">
                                <div class="compte__input">
                                    <label for="genre">Civilité</label>
                                    <select id="genre" name="genre">
                                        <option value="Mr" <?php if (
                                            $civilite == "Mr"
                                        ) {
                                            echo "selected";
                                        } ?>>Homme</option>
                                        <option value="Mme" <?php if (
                                            $civilite == "Mme"
                                        ) {
                                            echo "selected";
                                        } ?>>Femme
                                        </option>
                                    </select>
                                </div>
                                <div class="compte__input">
                                    <label for="compte__date_naissance">Date de naissance</label>
                                    <input required="required" type="date" name="date_naissance" id="compte__date_naissance"
                                        value="<?= $date_naissance ?>" max="<?php echo $dateMin; ?>">
                                </div>
                                <div class="compte__input">
                                    <label for="compte__telephone">Téléphone portable</label>
                                    <input required="required" type="text" name="telephone" id="compte__telephone" value="<?= $telephone ?>"
                                        placeholder="Votre numéro"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                </div>
                            </div>
                            <div class="ligne">
                                <div class="compte__input">
                                    <label for="compte__email">Adresse e-mail</label>
                                    <input required="required" type="email" name="email" id="compte__email" value="<?= $email ?>"
                                        placeholder="Ex : exemple@domaine.com">
                                </div>
                                <input class="sauvegarde" type="submit" value="Enregistrer">
                            </div>
                        </form>
                    </div>




                    <div class="adresse_conteneur">
                        <h3>Adresse de facturation</h3>

                        <form method="POST" action="">
                            <input type="hidden" name="form_type" value="adresse">
                            <div class="ligne">
                                <div class="compte__input">
                                    <label for="compte__pays">Pays</label>
                                    <input required="required" type="text" name="pays" id="compte__pays" value="<?= $pays ?>" placeholder="Votre pays" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">
                                    <div id="autocomplete-list-pays" class="autocomplete-suggestions"></div>
                                </div>
                                <div class="compte__input">
                                    <label for="compte__region">Région</label>
                                    <input required="required" type="text" name="region" id="compte__region" value="<?= $region ?>" placeholder="Votre région" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">
                                    <div id="autocomplete-list-regions" class="autocomplete-suggestions"></div>
                                </div>
                            </div>
                            <div class="ligne">
                                <div class="compte__input">
                                    <label for="compte__departement">Département</label>
                                    <input required="required" type="text" name="departement" id="compte__departement" value="<?= $departement ?>" placeholder="Votre département" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">
                                    <div id="autocomplete-list-departement" class="autocomplete-suggestions"></div>
                                </div>
                                <div class="compte__input">
                                    <label for="compte__ville">Ville</label>
                                    <input required="required" type="text" name="commune" id="compte__ville" value="<?= $ville ?>" placeholder="Votre ville" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">
                                    <div id="autocomplete-list-ville" class="autocomplete-suggestions"></div>
                                </div>
                                <div class="compte__input">
                                    <label for="code_postal">Code postal</label>
                                    <input required="required" type="text" name="code_postal" id="compte__code_postal" value="<?= $code_postal ?>" placeholder="Votre code postal" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                </div>
                            </div>
                            <div class="ligne">
                                <div class="compte__input">
                                    <label for="compte__rue">Nom de la rue</label>
                                    <input required="required" type="text" name="rue" id="compte__rue" value="<?= $voie ?>"
                                        placeholder="Votre rue"
                                        oninput="this.value = this.value.replace(/[^a-zA-Z\s']/g, '');">
                                </div>
                                <div class="compte__input">
                                    <label for="compte__rue">Numéro de rue</label>
                                    <input required="required" type="number" name="numero" id="compte__rue_numero" value="<?= $numero ?>"
                                        placeholder="Numéro de votre rue"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                </div>
                            </div>
                            <div class="ligne">
                                <div class="compte__input">
                                    <label for="compte__complement">Complément d'adresse</label>
                                    <input type="text" name="complement1" id="compte__complement1"
                                        value="<?= $complement1 ?>" placeholder="Complément">
                                </div>
                                <div class="compte__input">
                                    <label for="compte__complement">Complément d'adresse</label>
                                    <input type="text" name="complement2" id="compte__complement2"
                                        value="<?= $complement2 ?>" placeholder="Complément">
                                </div>
                            </div>
                            <div class="ligne">
                                <div class="compte__input">
                                    <label for="compte__complement">Complément d'adresse</label>
                                    <input type="tel" name="complement3" id="compte__complement3"
                                        value="<?= $complement3 ?>" placeholder="Complément">
                                </div>
                                <input class="sauvegarde" type="submit" value="Enregistrer">
                            </div>
                        </form>
                    </div>

                    <div class="ensemble_flex ensemble_flex-proprio">
                        <div class="champs_verticals">
                            <form method="post" class="photo_conteneur photo_conteneur-proprio" id="photo_client"
                                enctype="multipart/form-data">
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
                            <form class="mdp_conteneur" id="mdp_client" method="post">
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
                                            <input type="password" id="new__mdp" name="new_mdp"
                                                placeholder="Au moins 8 caractères">
                                        </div>
                                        <div class="compte__input">
                                            <label for="compte__mdp">Confirmez le mot de passe :</label>
                                            <input type="password" id="new__mdp2" name="new_mdp2">
                                        </div>

                                    </div>
                                </div>
                                <?php if ($passwordVerify): ?>
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
                            <div id="tokens">
                    <input type="hidden" name="action" value="delete">
                            <div class="token_conteneur">
                                <div class="modal_sup">
                                    <div class="modal" id="modal">
                                        <div class="modal-content">
                                            <p id="text-content">Êtes-vous sûr de vouloir supprimer ?</p>
                                            <div class="modal-actions">

                                                <button type="button" id="cancelBtn">Annuler</button>
                                                <button type="button" name="supp_token" id="confirmBtn">Supprimer</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <form action="" method="POST">
                                    <div class="modal_enreg" id="modal_enreg">
                                        <input type="hidden" name="alreadyIn" id="alreadyIn">
                                        <input type="hidden" name="token" id="token">
                                        <input type="hidden" name="action" id="action" value="">
                                        <div class="modal-content">
                                            <span class="close" id="closeModalBtn">&times;</span>
                                            <h3>Modifier Token</h3>
                                            <div class="dates">
                                                <label for="date_debut">Date début
                                                    <input required="required" type="date" id="date_debut" name="date_debut">
                                                </label>
                                                <label for="date_fin">Date fin
                                                    <input required="required" type="date" id="date_fin" name="date_fin">
                                                </label>
                                            </div>
                                            <div>
                                                <label>
                                                    <input type="checkbox" id="selectAllCheckbox"> Sélectionner tous les logements
                                                </label>
                                            </div>
                                            <div class="logements-container" id="logementsContainer"></div>

                                            <div class="buttons">
                                                <button type="button" class="open-modal-btn" id="closeBtn" style="background-color: #dc3545;">Annuler</button>
                                                <button type="submit" name="valider" class="open-modal-btn">Valider</button>
                                            </div>

                                        </div>
                                </form>


                            </div>
                            <h3>Calendrier iCal</h3>
                            <p>Configurez vos calendirer au format iCal.</p>
                            <form action="" method="post">
                                <?php
                                $sql =
                                    "SELECT token FROM sae._ical_token where proprietaire =" .
                                    $_SESSION["business_id"];
                                $res = request($sql);
                                foreach ($res as $token): ?>
                                    <div class="token ">
                                            <input class="token_id" type="password" value="<?= $token[
                                                "token"
                                            ] ?>" readonly="readonly">
                                        <div class="action">
                                            <div class="cross"><i class="fas fa-times"></i></div>
                                            <div class="copier"><i class="fas fa-copy"></i></div>
                                            <div class="modifier"><i class="fas fa-pencil-alt"></i></div>
                                            <div class="eyes"><i class="fas fa-eye"></i></div>
                                        </div>
                                    </div>
                                <?php endforeach;
                                ?>
                                <button type="button" id="generer_token"  class="sauvegarde"value="Générer Token">Générer Token</button>
                            </form>
                    </div>
                    
                    
                </div>
                        </div>
                        <div class="champs_verticals">
                            <form class="identite_conteneur" id="identite_client" method="post"
                                enctype="multipart/form-data">
                                <input type="hidden" name="form_type" value="identite">
                                <h3>Vérification de l'identité</h3>
                                <p> Enregistrez les détails de votre passeport pour vérifier votre
                                    identité. Choisissez des photos recto-verso.</p>
                                <div class="input__ligne">
                                    <div class="connect__input">
                                        <label for="connect__recto">Photo du recto</label>
                                        <input type="file" name="photo_recto" id="connect__recto" accept="image/*">
                                        <img src="<?= $recto ?>" alt="Photo du recto" class="ident_photo">
                                    </div>
                                    <div class="connect__input">
                                        <label for="connect__verso">Photo du verso</label>
                                        <input type="file" name="photo_verso" id="connect__verso" accept="image/*">
                                        <img src="<?= $verso ?>" alt="Photo du verso" class="ident_photo">
                                    </div>
                                </div>
                                <input class="sauvegarde" type="submit" value="Enregistrer" style="width:30%;">

                            </form>
                            <form class="paiment_conteneur" id="paiment_client" method="post">
                                <input type="hidden" name="form_type" value="paiment">
                                <h3>Informations de versement</h3>
                                <p>Ajoutez votre RIB </p>
                                <div class="connect__input">
                                    <input type="text" name="iban" id="connect__iban" placeholder="IBAN" required
                                        value="<?= $iban ?>">
                                </div>
                                <div class="connect__input">
                                    <input type="text" name="bic" id="connect_bic" placeholder="BIC" required
                                        value="<?= $bic ?>">
                                </div>
                                <div class="ligne">
                                    <div class="connect__input">
                                        <input type="text" name="titulaire" id="connect_titulaire"
                                            placeholder="Titulaire" required value="<?= $titulaire ?>">
                                    </div>
                                    <input class="sauvegarde" type="submit" value="Enregistrer" style="width:30%;">
                                </div>
                                <?php if ($ibanInvalid): ?>
                                    <p class="login_invalid">IBAN invalide.</p>
                                <?php endif; ?>
                                <?php if ($bicInvalid): ?>
                                    <p class="login_invalid">BIC invalide.</p>
                                <?php endif; ?>
                                <?php if ($accountHolderInvalid): ?>
                                    <p class="login_invalid">Titulaire du compte invalide.</p>
                                <?php endif; ?>
                            </form>

                            <div id="api">
                    <input type="hidden" name="action" value="delete">
                    <div class="token_conteneur">
                                <form action="" method="POST">
                                    <div class="modal_enreg" id="modal_enreg-api">
                                        
                                        <input type="hidden" name="api" id="api-key">
                                        <input type="hidden" name="action" id="action-api" value="">
                                        <div class="modal-content">
                                            <span class="close" id="closeModalBtn-api">&times;</span>
                                            <h3>Modifier API</h3>
                                            
                                            <div>
                                                <label>
                                                    <input type="checkbox" id="selectAllCheckbox-api"> Sélectionner tous les droits
                                                </label>
                                            </div>
                                            <div class="droits-container" id="droitsContainer"></div>

                                            <div class="buttons">
                                                <button type="button" class="open-modal-btn" id="closeBtn-api" style="background-color: #dc3545;">Annuler</button>
                                                <button type="submit" name="valider-api" class="open-modal-btn">Valider</button>
                                            </div>

                                        </div>
                                </form>
                                </div>
                                <h3>Clé API</h3>
                                <p>Configurez vos clé(s) API.</p>
                            <form action="" method="post">
                                <?php
                                $sql =
                                    "SELECT key FROM sae._api_keys where proprietaire =" .
                                    $_SESSION["business_id"];
                                $res = request($sql);
                                foreach ($res as $token): ?>
                                    <div class="token ">
                                            <input class="api_id" type="password" value="<?= $token[
                                                "key"
                                            ] ?>" readonly="readonly">
                                        <div class="action">
                                            <div class="cross-api"><i class="fas fa-times"></i></div>
                                            <div class="copier-api"><i class="fas fa-copy"></i></div>
                                            <div class="modifier-api"><i class="fas fa-pencil-alt"></i></div>
                                            <div class="eyes-api"><i class="fas fa-eye"></i></div>
                                        </div>
                                    </div>
                                <?php endforeach;
                                ?>
                                <button type="button" id="generer_api" class="sauvegarde" value="Générer clé API">Générer clé API</button>
                            </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../js/consulter_mon_compte.js"></script>
    <script src="../js/toast.js"></script>
</body>
<?php include "./footer.php";
$serverName =
    $_SERVER["SERVER_NAME"] .
    ":" .
    $_SERVER["SERVER_PORT"] .
    "/ical/ical.php?token=";

$business_id = $_SESSION["business_id"];
print <<<EOT
    <script>
        const BUSINESS_ID = '{$business_id}';
        const SERVER_NAME = '{$serverName}';
    </script>
EOT;
?>
    
    <script src='../js/mon_compte.js'></script>
    </body>
</html>