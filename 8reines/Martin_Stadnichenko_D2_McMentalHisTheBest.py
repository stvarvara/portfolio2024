import timeit

def estPossible(plateau, ligne, colonne): #Permet de savoir si une reine menace une autre reine
    """
    On entre en parametre un plateau et la ligne ainsi que la colonne ou on souhaite jouer notre reine
    Et la fonction nous renvoie True si la position souhaite n'est pas menace sinon renvoie False 
    """
    #Si 2 reines sont sur la meme colonnes alors renvoie False
    for lig in range(ligne):
        if plateau[lig][colonne] == "R":
            return False
 
    #Si 2 reines sont sur la meme diagonales alors renvoie False
    (lig, col) = (ligne, colonne)
    while lig >= 0 and col >= 0:
        if plateau[lig][col] == "R":
            return False
        lig = lig - 1
        col = col - 1
 
    #Si 2 reines sont sur la meme diagonales alors renvoie False
    (lig, col) = (ligne, colonne)
    while lig >= 0 and col < len(plateau):
        if plateau[lig][col] == "R":
            return False
        lig = lig - 1
        col = col + 1
 
    return True
 
def afficheSolution(plateau, taille): #Permet de faire un affichage d'une possibilité
    """
    On entre en parametre le plateau aussi dit le dictionnaire et la fonction nous affiche notre plateau de jeu 
    avec les differentes reines placées
    """

    for i in range(taille):
        chaine = " "
        ligne = ""
        for j in range(taille):
            chaine = chaine + plateau[i][j] + " | "
            ligne = ligne + "---+"
        ligne = ligne[:-1]
        chaine = chaine[:-2]
        chaine = chaine + " "
        print(chaine)
        print(ligne)
    print("\n")
 
 
def nReine(plateau, ligne):
    """
    Le fonction prends en paramtre le plateau ainsi que la cle ou nous voulons jouer notre reine
    Si possible la fonction va placer une reine et s'appeler récursivement afin de calculer les differentes possibilite
    """
    
    global nb_solutions

    if ligne == len(plateau): #Si il y a autant de reine que de ligne alors on a place toutes les reines et on s'arrete
        afficheSolution(plateau, len(plateau)) #Mettre en commentaire l'affichage permet d'avoir l'affichage du temps d'execution plus rapidement
        nb_solutions += 1  # Incrémente le nombre de solutions
        return False
 
    for i in range(len(plateau)): #Permet de parcourir le plateau et de placer une reine si la position n'est pas menace
        if estPossible(plateau, ligne, i): #Si 2 reines ne se menacent pas
            plateau[ligne][i] = "R" #On place la reine sur une position valide
            nReine(plateau, ligne + 1) #On appel la fonction de maniere recursive pour la ligne suivante
            plateau[ligne][i] = " " #Remplace les caracteres par des - pour une case vide

def main(): #Fonction principal lancant tout le programme
    """
    La fonction ne prends pas de parametre mais permet d'initialiser un dictionnaire puis de jouer avec celui-ci
    """
    
    global nb_solutions
    
    taille = 8 #Donne la taille du tableau / Valeur a changer en fonction de la taille de palteau souhaiter
    plateau = [[" " for x in range(taille)] for y in range(taille)] #Initialise le tableau d'une taille de NxN (2 dimensions)
    nReine(plateau, 0) #Lance la recherche de solution

nb_solutions = 0

#Appel timeit de main permettant de connaitre le temps d'execution de notre fonction sur un nombre donnée d'iteration
temps_execution = timeit.timeit("main()", setup="from __main__ import main", number=1) #Changer le number pour influencer le nombre d'iteration
#Si on veut le nombre de solution pour 1 iteration alors number doit etre mit à 1 sinon le nombre de reponse ne sera pas coherante et ne correspondera juste au nombre total d'iteration
print("Temps d'exécution de la fonction main : {:.6f} secondes".format(temps_execution)) #Affiche le temps d'execution de la fonction
print("Nombre de solutions trouvées : ", nb_solutions)  # Affiche le nombre de solutions trouvées