#!/bin/bash

#ajout des droits d'execution
chmod +x vendeur_img.sh
#on lance le script qui convertit les images
./vendeur_img.sh $USER


for fichier in *.txt
do
    #ajout des droits d'execution
    chmod +x extraction.php
    #lance l'execution du script pour extraire les informations
    docker run --rm -ti -v /Docker/$USER/:/work sae103-php -f extraction.php "$fichier"

    #ajout des droits d'execution
    chmod +x qrcode.sh
    #creation du qrcode
    ./qrcode.sh $USER

    #recupere le nom de la region (sans l'extension)
    nom=${fichier::-4}

    #on enleve les espaces dans les noms (mene a des problemes avec le docker pdf)
    nom=$(echo $nom | tr ' ' "_")

    #lance le script pour creer la page puis on le met dans un fichier nommé <nom>.html
    docker run --rm -ti -v /Docker/$USER/:/work sae103-php -f region.php > "$nom".html

    #droit pour que les autre docker puisse lire les fichiers
    chmod +rwx "$nom".html

    #ajout des droits d'execution
    chmod +x Script_pdf.sh
    #lance le script pour la transformation en pdf
    ./Script_pdf.sh $USER "$nom"

    #on supprime le fichier html (on en a plus besoin)
    rm "$nom".html
done
#creation d'un dossier en .tar
tar czf pdf.tar pdf

#suppression des fichiers dont on a plus besoin
#les fichiers
rm qrcode.dat
rm comm.dat
rm texte.dat
rm tableau.dat
#les dossiers
rm -r imagemodif
rm -r pdf
rm *.png
