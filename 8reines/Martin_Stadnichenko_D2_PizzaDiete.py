import timeit

def affichePlateau(plateau): #Permet de faire un affichage d'un plateau de jeu
    """
    On entre en parametre le plateau aussi dit le dictionnaire et la fonction nous affiche notre plateau de jeu 
    avec les differentes reines placées
    """
    
    for cle in plateau.keys():
        chaine = " "
        ligne = ""
        for val in plateau[cle]:
            chaine = chaine + str(val) + " | "
            ligne = ligne + "---+"
        ligne = ligne[:-1]
        chaine = chaine[:-2]
        chaine = chaine + " "
        print(chaine)
        print(ligne)
    print("\n")

def peutPoser(plateau, cle, val): #Permet de savoir si une reine menace une autre reine
    """
    On entre en parametre un plateau et la cle ainsi que la valeur ou on souhaite jouer notre reine (ses coordonnées)
    Et la fonction nous renvoie True si la position souhaite n'est pas menace sinon renvoie False
    """

    #Si 2 reines sont sur la meme colonnes alors renvoie False
    for key in plateau:
        if plateau[key][val] == "R":
            return False
    
    #Si 2 reines sont sur la meme diagonales alors renvoie False
    (key, value) = (cle, val)
    while (key>=0 and value>=0):
        if plateau[key][value] == "R":
            return False
        key = key - 1
        value = value - 1
    
    #Si 2 reines sont sur la meme diagonales alors renvoie False
    (key, value) = (cle, val)
    while (key>=0 and value<len(plateau)):
        if plateau[key][value] == "R":
            return False
        key = key - 1
        value = value + 1
    
    return True

def Reine(plateau, cle): #Fonction permettant de placer les reines selon leur possible validite
    """
    Le fonction prends en paramtre le plateau ainsi que la cle ou nous voulons jouer notre reine
    Si possible la fonction va placer une reine et s'appeler récursivement afin de calculer les differentes possibilite
    """
    
    if cle == len(plateau): #Si il y a autant de reine que de ligne alors on a place toutes les reines et on s'arrete
        affichePlateau(plateau) #Mettre en commentaire l'affichage permet d'avoir l'affichage du temps d'execution plus rapidement
        return False
    
    for i in range(len(plateau)): #Permet de parcourir le plateau et de placer une reine si la position n'est pas menace
        if peutPoser(plateau, cle, i): #Si 2 reines ne se menacent pas
            plateau[cle][i] = "R" #On place la reine sur une position valide
            Reine(plateau, cle + 1) #On appel la fonction de maniere recursive pour la ligne suivante
            plateau[cle][i] = " " #Remplace les caracteres par des - pour une case vide

def main(): #Fonction principal lancant tout le programme
    """
    La fonction ne prends pas de parametre mais permet d'initialiser un dictionnaire puis de jouer avec celui-ci
    """
    
    taille = 8 #Donne la taille du tableau / Valeur a changer en fonction de la taille de palteau souhaiter
    d = {} #Créer le dictionnaire contenant le plateau
    for i in range(taille): 
        d[i] = [" " for x in range(taille)] #On initialise les valeurs du dictionnaire
    Reine(d, 0) #Lance la recherche de solution

#Appel timeit de main permettant de connaitre le temps d'execution de notre fonction sur un nombre donnée d'iteration
temps_execution = timeit.timeit("main()", setup="from __main__ import main", number=2000) #Changer le number pour influencer le nombre d'iteration
print("Temps d'exécution de la fonction main : {:.6f} secondes".format(temps_execution)) #Affiche le temps d'execution de la fonction
    
