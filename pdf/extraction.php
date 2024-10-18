<?php
    // Vérifie si un nom de fichier a été donné en argument
    if ($argc < 2) {
        echo "Error: No input file specified.\n";
        exit(1);
    }

    // on recupere le parametre d'entree (nom du fichier)
    $fichier = file($argv[1]);
    $recupere = FALSE;


    // On crée un fichier "tableau.dat" en mode écriture
    $fic_conf = fopen('tableau.dat', 'w');

    

    /*
    *
    *       PARTIE TABLEAU
    *
    */

    // on parcours les lignes du fichier
    foreach ($fichier as $indice => $line){
        if($recupere){
            // on regarde si c'est la fin des informations a recuperer
            if (strtoupper(substr($line, 0, 9)) == 'FIN_STATS'){
                $recupere = FALSE;
            }
            // sinon on les recupere
            else{
                // on enleve "Prod #" qui est devant
                $line = substr($line,6);

                // on creer un tableau
                $tab_info = explode(",", $line);
                // calcul du % d'evolution du CA
                $evo = ((intval($tab_info[2])-intval($tab_info[4]))/intval($tab_info[4]))*100;
                //pour enlever le retour à la ligne et ainsi rajouter la % du CA à la fin
                $line = str_replace("\n","", $line);
                // on l'ajoute a notre ligne
                $line .= ',' . $evo . "\n";
                // on l'ecrit dans le fichier
                fwrite($fic_conf, $line);
            }
        }

        else{
            // on fait une copie de la ligne actuelle
            $chaine_convertit = $line;

            // on remplace les accents
            $chaine_convertit = preg_replace('#é#', 'e', $chaine_convertit);
            $chaine_convertit = preg_replace('#É#','E', $chaine_convertit);

            // on met la chaine en majuscule
            $chaine_convertit = strtoupper($chaine_convertit);

            // on regarde s'il s'agit du mot cle en prenant seulement les 11 premier caracteres
            if (substr($chaine_convertit, 0, 11) == "DEBUT_STATS"){
                // si oui on recupere les prochaines lignes
                $recupere = TRUE;
            }
        }
    }
    // on ferme le fichier
    fclose($fic_conf);


    /*
    *
    *       PARTIE TEXTE
    *
    */


    // On crée un fichier "texte.dat" et "qrcode.dat" en mode écriture
    $fic_conf = fopen('texte.dat','w');
    $qrcode = fopen('qrcode.dat', 'w');
    // Variable indiquant si on se trouve dans la section de texte à extraire ou non
    $trouve = false;
    
    // On parcourt chaque ligne du fichier
    foreach ($fichier as $line){

        // Si on trouve "FIN_TEXTE", on sort de la section de texte à extraire
        if (strtoupper(substr($line,0,9))=='FIN_TEXTE'){
            $trouve = false;
            fwrite($fic_conf, "FIN_TEXTE\n");
        }
        // Si on se trouve dans la section de texte à extraire, on écrit la ligne dans "texte.dat"
        elseif ($trouve){
            fwrite($fic_conf, $line);
        }
        // Si on trouve dans "CODE=", on convertit le code en majuscules et on l'écrit dans "texte.dat" et de l'url dans "qrcode.dat"
        elseif (strtoupper(substr($line,0,5)) =='CODE='){
            fwrite($fic_conf, "CODE=");
            fwrite($fic_conf, strtoupper(substr($line,5)));
            
            fwrite($qrcode, "https://bigbrain.biz/");
            fwrite($qrcode, strtoupper(substr($line,5)));
        }
        // Si on trouve "TITRE=", on écrit le titre dans "texte.dat"
        elseif (strtoupper(substr($line,0,6))=='TITRE='){
            fwrite($fic_conf, "TITRE=");
            fwrite($fic_conf, substr($line,6)); 
        }
        // Si on trouve "SOUS_TITRE=", on écrit le sous-titre dans "texte.dat"
        elseif (strtoupper(substr($line,0,11))=='SOUS_TITRE='){
            fwrite($fic_conf, "SOUS_TITRE=");
            fwrite($fic_conf, substr($line,11)); 
        }
        // Si on trouve "DEBUT_TEXTE" ou "Début_texte", on entre dans la section de texte à extraire
        elseif ((substr($line,0,11)=='DEBUT_TEXTE')||(substr($line,0,12)=='Début_texte')){
            $trouve = true;
            fwrite($fic_conf, "DEBUT_TEXTE\n");
        }
    }
    // on ferme les documents
    fclose($fic_conf);
    fclose($qrcode);

    /*
    *
    *       PARTIE MEILLEURS
    *
    */

    // prends en argument un fichier d'entree
    $input_filename = $argv[1];

    // initialise le tableau de vendeurs
    $vendeurs = array();

    // Ouvre le fichier d'entree
    $input = fopen($input_filename, 'r');

    // Li l'entree du fichier ligne par ligne
    while (($line = fgets($input)) !== false) {

        // Met en majuscules
        $line = strtoupper($line);
        // Verifie si dans cette ligne il contient le string "MEILLEURS"
        if (strpos($line, "MEILLEURS") !== false) {
            // Enleve les accents
            $line = iconv('UTF-8', 'US-ASCII//TRANSLIT', $line);

            // Remplace "MEILLEURS" par un string vide
            $line = str_replace("MEILLEURS:", "", $line);

            // Decompose la ligne en morceaux
            $lignes = explode(',', $line);

            // Extrait le nom et le chiffre d'affaire pour chaque morceau
            foreach ($lignes as $ligne) {
                list($nom, $chiffreAffaire) = explode('=', $ligne);
                // Rempli le nom et le chiffre d'affaire de chaque morceau dans le tableau $vendeurs
                $vendeurs[$nom] = (int) $chiffreAffaire;
            }
        }
    }

    // Trie le tableau $vendeurs par valeur par ordre descendant
    arsort($vendeurs);

    // Ouvre le fichier de sortie
    $output = fopen("comm.dat", 'w');

    // Affiche les 3 meilleurs vendeurs
    $i = 1;

    foreach ($vendeurs as $nom => $chiffreAffaire) {
        // SI le nom possède un / alors on garde que la partie après le nom
        $pos = strpos($nom, '/');
        if ($pos !== false) {
            $nom = substr($nom, $pos + 1);
        }
        // Extrait le nom et le prenom
        $prenom_nom = explode(" ", $nom);
        // Verifie si un il reste un / dans le prenom
        $prenom = explode("/", $prenom_nom[0]);
        // Attribut le code aux 2 permiers caractere du prenom et au 1er caractere du nom
        if (isset($prenom[1])) {
            $code = substr($prenom[1], 0, 1) . substr($prenom_nom[1], 0, 1);
        } else {
            $code = substr($prenom[0], 0, 2) . substr($prenom_nom[1], 0, 1);
        }
        // Attribut chaque ligne a son classement, son code puis son nom et prenom et son chiffre d'affaire
        $aff_ligne = "$i. $code/$nom : $chiffreAffaire" . "K EUR\n";
        fwrite($output, $aff_ligne);
        $i++;
        if ($i > 3) {
            break;
        }
    }

    // Ferme les fichiers
    fclose($input);
    fclose($output);
?>