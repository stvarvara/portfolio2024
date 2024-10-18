<?php
$fic_txt = fopen('texte.dat', 'r');
$line = fgets($fic_txt);
$code = substr($line, 5, 6);
fclose($fic_txt);

$conf = file("region.conf");
foreach ($conf as $region){
    if (substr($region, 0, 6) == $code){
        $info_reg = explode(";",$region);
        $nom_region = $info_reg[1];
        $superficie =  $info_reg[2];
        $pop = $info_reg[3];
        $nb_dep = $info_reg[4];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:regular,500,600,700,800,900" rel="stylesheet" />
    <title><?php echo $nom_region; ?></title>
</head>
<body>
    <page size="A4" id="pagecouverture"> <!--Page couverture avec le nom, l'info principales et le logo-->
        <div id="maininf">
            <h1><?php echo $nom_region; ?></h1> <!--Le nom de la région-->
            <h3><?php echo $pop; ?> d'habitants</h3><!--La population-->
            <h3><?php echo $superficie; ?> km<sup>2</sup></h3><!--La superficie-->
            <h3><?php echo $nb_dep; ?> départements</h3><!--La quantité de départements-->
        </div>
        <div id="logo"><!--Le logo de la région-->
            <img src="<?php echo "logos/$code"; ?>.png" alt="logo">
        </div>
    </page>
    <page size="A4" id="page1">  <!--Première page avec les textes et le tableau-->
        <h1>Résultats trimestriels 04-2022</h1><!--Le titre de la page-->
        <div class="text"><!--Section avec le texte-->
            <?php
                $texte_fic = file("texte.dat");
                $debut_txt = false;

                foreach($texte_fic as $line){
                    if ($debut_txt == true){
                        if (strtoupper(substr($line,0,9))=='FIN_TEXTE'){
                            $debut_txt = false;
                            echo "</p>";
                        }
                        else{
                            echo "$line";
                        }
                    }
                    elseif ((substr($line,0,11)=='DEBUT_TEXTE')||(substr($line,0,12)=='Début_texte')){
                        $debut_txt = true;
                        echo "<p>";
                    }
                    elseif (strtoupper(substr($line,0,6))=='TITRE='){
                        $line = substr($line,6);
                        echo "<h2>$line</h2>";
                    }
                    elseif (strtoupper(substr($line,0,11))=='SOUS_TITRE='){
                        $line = substr($line,11);
                        echo "<h3>$line</h3>";
                    }
                }
            ?>
        </div>
        <div class="footerdelapage"><!--La date et l'heure de la création du fichier-->
            <h4><?php echo date('d-m-Y H:i'); ?></h4>
        </div>
    </page>
    <page size="A4" id="page1">
        <table><!--Le tableau avec les résultats-->
            <caption>Les résultats</caption><!--Le nom du tableau-->
            <tr><!--Première ligne avec les noms de colonnes-->
              <th>Produit</th>
              <th>Ventes du trimestre</th>
              <th>CA</th>
              <th>Ventes du trimestre année précédente</th>
              <th>CA du trimestre année précédente</th>
              <th>Evolution de CA</th>
            </tr>
            <?php
            $fic_tab = file("tableau.dat");
            foreach ($fic_tab as $line){
                echo "<tr>";
                $tab_prod = explode(",",$line);
                foreach ($tab_prod as $indice => $info){
                    if ($indice == array_key_last($tab_prod)){
                        $info = round($info, 2);
                        if ($info < 0){
                            echo "<td style=\"color:red;\"><b>";
                        }
                        else{
                            echo "<td style=\"color:green;\"><b>";
                        }
                        $info = abs($info);
                        echo "$info</b></td>";
                    }
                    else{
                        echo "<td>$info</td>";
                    }
                }
                echo "</tr>";
            }
            ?>
          </table>
        <div class="footerdelapage"><!--La date et l'heure de la création du fichier-->
            <h4><?php echo date('d-m-Y H:i'); ?></h4>
        </div>
    </page>
    <page size="A4" id="page2"><!--Deuxième page avec les meilleurs vendeurs-->
        <h1>Nos meilleurs vendeurs du trimestre</h1><!--Le titre-->
        <div id="galerie"><!--La galerie -->
            <?php
                $fic_comm = file("comm.dat");
                foreach ($fic_comm as $vendeur){
                    echo "<div class=\"topvend\">";
                    $tab_vendeur = explode(" ", $vendeur);
                    $code_vendeur = substr($tab_vendeur[1], 0, 3);
                    $code_vendeur = strtolower($code_vendeur);
                    echo "<img src=\"$code_vendeur.png\" alt=\"$code_vendeur\">";
                    $nom_vendeur = substr($tab_vendeur[1], 4) . " " . $tab_vendeur[2];
                    echo "<h2>$nom_vendeur</h2>";
                    echo "<h3>$tab_vendeur[4] €</h3>";
                    echo "</div>";
                }
            ?>
        </div>
        <div class="footerdelapage"><!--La date et l'heure de la création du fichier-->
            <h4><?php echo date('d-m-Y H:i'); ?></h4>
        </div>
    </page>
    <page size="A4" id="page3"><!--Troisième page avec le lien et QR-code-->
        <h1>Site de la société</h1><!--Le titre -->
        <div id="site">
            <a href="https://bigbrain.biz/<?php echo $code; ?>">https://bigbrain.biz/<?php echo $code; ?></a><!--Le lien vers le site de la région-->
            <img src="qrcode.png" alt="qrcode"><!--Le QR-code vers le site de la région -->
        </div>
        <div class="footerdelapage"><!--La date et l'heure de la création du fichier-->
            <h4><?php echo date('d-m-Y H:i'); ?></h4>
        </div>
    </page>
</body>
</html>