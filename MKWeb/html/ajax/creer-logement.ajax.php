<?php
require_once '../../utils.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}   

  if (isset($_SESSION["form_data"])){

      $adresse = [
        "pays" => $_SESSION["form_data"]["pays"],
        "region" => $_SESSION["form_data"]["region"],
        "departement" => $_SESSION["form_data"]["departement"],
        "code_postal" => $_SESSION["form_data"]["cp"],
        "commune" => $_SESSION["form_data"]["commune"],
        "numero" => $_SESSION["form_data"]["num_voie"],
        "nom_voie" => $_SESSION["form_data"]["voie"],
        "complement_1" => empty($_SESSION["form_data"]["comp1"]) ? null : $_SESSION["form_data"]["comp1"],
        "complement_2" => empty($_SESSION["form_data"]["comp2"]) ? null : $_SESSION["form_data"]["comp2"],
        "complement_3" => empty($_SESSION["form_data"]["comp3"]) ? null : $_SESSION["form_data"]["comp3"],
        "latitude" => $_SESSION["form_data"]["latitude"],
        "longitude" => $_SESSION["form_data"]["longitude"]
    ];

    if (!buisness_connected()){
      print json_encode(["err" => "forbidden"]);
    }

    $logement = [
        "titre" => $_SESSION["form_data"]["titre"],
        "id_proprietaire" => buisness_connected_or_redirect(),
        "id_adresse" => insert("sae._adresse", array_keys($adresse), array_values($adresse)),
        "id_categorie" => $_SESSION["form_data"]["categorie"],
        "id_type" => $_SESSION["form_data"]["type"],
        "surface" => $_SESSION["form_data"]["surface"],
        "nb_chambre" => $_SESSION["form_data"]["chambre"],
        "nb_lit_simple" => $_SESSION["form_data"]["simple"],
        "nb_lit_double" => $_SESSION["form_data"]["double"],
        "accroche" => $_SESSION["form_data"]["accroche"],
        "description" => $_SESSION["form_data"]["description"],
        "nb_max_personne" => $_SESSION["form_data"]["nbpersonne"],
        "base_tarif" => $_SESSION["form_data"]["prixht"],
        "periode_preavis" => $_SESSION["form_data"]["preavis"],
        "en_ligne" => $_SESSION["form_data"]["statut"],
        "duree_min_res" => $_SESSION["form_data"]["dureeloc"],
        "delai_avant_res" => $_SESSION["form_data"]["delaires"],
    ];

    $id_logement = insert("sae._logement", array_keys($logement), array_values($logement));

    if (isset($_SESSION["form_data"]["amenagements"])){
        foreach($_SESSION["form_data"]["amenagements"] as $amenagement){
            insert("sae._amenagement_logement", ["id_logement", "id_amenagement"], [$id_logement, $amenagement], false);
        }
    }

    
    if (isset($_SESSION["form_data"]["activite"])){
        foreach($_SESSION["form_data"]["activite"] as $activite){
            $activite = explode(";;" ,$activite);
            insert("sae._activite_logement", ["id_logement", "activite", "id_distance"], [$id_logement, $activite[0], $activite[1]], false);
        }
    }

    $uploads_dir = "../img/logement/$id_logement";
    if (!is_dir($uploads_dir)){
        mkdir($uploads_dir, 0777, true);
    }


    if (isset($_SESSION["form_images"])){
        foreach ($_SESSION["form_images"] as $key => $img) {
            $exp = explode("/", $img);
            $name = end($exp);
            rename("../img/".$img, "../img/logement/$id_logement/$name");
            if ($key === 0){
                insert("sae._image", ["src", "principale", "alt", "id_logement"], ["/logement/$id_logement/$name", "true", "Image du Logement", "$id_logement"], false);
            } else {
                insert("sae._image", ["src", "principale", "alt", "id_logement"], ["/logement/$id_logement/$name", "false", "Image du Logement", "$id_logement"], false);
            }
        }
    }

    unset($_SESSION['form_data']);
    unset($_SESSION['form_images']);
    $id_proprio = buisness_connected_or_redirect();
    $dirname = "../img/tmp/$id_proprio";
    array_map('unlink', glob("$dirname/*.*"));
    rmdir($dirname);
    
    print json_encode(["err" => false, "id" => $id_logement]);
    die;
} 

print json_encode(["err" => "invalid data"]);

?>