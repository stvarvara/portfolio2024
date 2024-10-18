## Le script prend en paramètres le nom du fichier et son login 


# Lance le docker
mkdir -p pdf
docker container run --rm -ti -v /Docker/"$1"/:/work sae103-html2pdf "html2pdf "$2".html pdf/"$2".pdf"
