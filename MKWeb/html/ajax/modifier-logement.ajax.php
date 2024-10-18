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
    $id_logement = $_SESSION["form_data"]["id"];
    $id_adresse = $_SESSION["form_data"]["id_adresse"];
    update("sae._adresse", array_keys($adresse), array_values($adresse),"id = $id_adresse");
    $logement = [
        "titre" => $_SESSION["form_data"]["titre"],
        "id_proprietaire" => buisness_connected_or_redirect(),
        "id_adresse" =>$id_adresse ,
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

    update("sae._logement", array_keys($logement), array_values($logement),"id=$id_logement");

    if (isset($_SESSION["form_data"]["amenagements"])){
        foreach($_SESSION["form_data"]["amenagements"] as $amenagement){
            $query="SELECT COUNT(*) FROM sae._amenagement_logement where id_logement=$id_logement and id_amenagement =$amenagement";
            $existAmenagementForLogement = request($query,false);
            $query = "SELECT id_amenagement from sae._amenagement_logement where id_logement=$id_logement";
            $amenagementLogement = request($query,false);
            $idAmenagementLogement = array_map(function($element){
                return $element["id_amenagement"];
            },$amenagementLogement);
            if($existAmenagementForLogement[0]["count"]==0){
                insert("sae._amenagement_logement", ["id_logement", "id_amenagement"], [$id_logement, $amenagement], false);
            }

            
            
            
        }
        foreach($idAmenagementLogement as $parcourId){
            if(!in_array($parcourId,$_SESSION["form_data"]["amenagements"])){
                $query="DELETE FROM sae._amenagement_logement WHERE id_logement=$id_logement and id_amenagement=$parcourId ";
                request($query,false);
        }
        }
    }

    
    if (isset($_SESSION["form_data"]["activite"])){
        $queryActivityLogement = "SELECT * from sae._activite_logement where id_logement=$id_logement";
        $activiteLogement = request($queryActivityLogement,false);
        $tableauActiviteForm=[];
        foreach($_SESSION["form_data"]["activite"] as $activite){
            $activite = explode(";;" ,$activite);
            if(!isset($activite[2])){
                insert("sae._activite_logement", ["id_logement", "activite", "id_distance"], [$id_logement, $activite[0], $activite[1]], false);
            }
            else{
                $tableauActiviteForm[] = $activite[2];
            }
            

        
        }
        foreach($activiteLogement as $act){
            if(!in_array($act["id"],$tableauActiviteForm)){
                $queryDeleteActivity = "DELETE FROM sae._activite_logement where id=$act[id]";
                request($queryDeleteActivity,false);
            }
        }
        
        
    }

    $uploads_dir = "../img/logement/$id_logement";
    if (!is_dir($uploads_dir)){
        mkdir($uploads_dir, 0777, true);
    }

    if(isset($_SESSION["form_data"]["img-preview"])){
        $query = "SELECT * from sae._image WHERE id_logement=$id_logement";
        $images = request($query,false);
        $listeSource = array_map(function($element){
            return $element["src"];
        },$images);
        foreach($images as $img) {
            if(!in_array($img["src"],$_SESSION["form_data"]["img-preview"])){
                $src = $img["src"];
                $query = "DELETE FROM sae._image WHERE src='$src'";
                unlink("../img".$src);
                request($query,false);
            }
        }
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

