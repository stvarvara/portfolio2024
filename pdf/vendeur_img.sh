#!/bin/bash

mkdir -p imagemodif
for fichier in $(ls images/*.svg)
do
    #supprime le chemin
    fichier=${fichier:(-7)}
    #garde le code de l'image
    nouvfichier=${fichier::3}
    #commande docker
    docker container run --rm -ti -v /Docker/"$1"/:/work -w /work sae103-imagick "magick images/$fichier -resize 230x230 -colorspace Gray -shave 15x15 "$nouvfichier".png"
done
