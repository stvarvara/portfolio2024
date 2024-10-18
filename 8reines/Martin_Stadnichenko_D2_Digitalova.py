import random
import math

class Plateau: #Genere la classe plateau qui représente le plateau de jeu
    def __init__(self, taille_plateau): #Initialise les variables qui vont etre utilise dans le plateau
        self.taille_plateau = taille_plateau #Correspond a la taille du plateau
        self.configurationReine = [random.randint(0, taille_plateau - 1) for _ in range(taille_plateau)] #Genere une conbinaison de reine aleatoire
    
    def estMenacer(self): #Permet de connaitre le nombre de reines qui se mencacent sur le plateau
        menaces = 0 #Nombre de menaces sur le plateau
        for i in range(self.taille_plateau): #Parcourt du plateau
            for j in range(i+1, self.taille_plateau):
                if self.configurationReine[i] == self.configurationReine[j] or abs(i - j) == abs(self.configurationReine[i] - self.configurationReine[j]): #Si il y a une reine qui menace une autre reine
                    menaces = menaces + 1    
        return menaces
    
    def deplaceReine(self, ligne, nouvelle_colonne): #Permet de deplacer une reine
        self.configurationReine[ligne] = nouvelle_colonne
    
    def __str__(self): #Genere un affichage complet du plateau
        plateau = []
        for i in range(self.taille_plateau):
            ligne = ['.'] * self.taille_plateau
            ligne[self.configurationReine[i]] = 'Q'
            plateau.append(''.join(ligne))  
        return '\n'.join(plateau)


class RechercheSolution: #Genere la classe RechercheSolution qui calcul et trouve une reponse pour l'algo
    def __init__(self, taille_plateau, initial_temperature, alpha, nb_iteration): #Initialise les variables qui vont etre utilise dans la resolution de l'algo
        self.taille_plateau = taille_plateau #Taille du plateau
        self.initial_temperature = initial_temperature #Degre d'echec de la configuration actuel
        self.alpha = alpha #Facteur de reduction de la temperature, ici 0.98 va correspondre a un certain degre et donc une certaine reponse
        self.nb_iteration = nb_iteration #	Nombre d'iteration a effectuer
        self.plateau_actuel = Plateau(self.taille_plateau) #Le plateau qui va etre crer pour accueillir notre solution
        self.menace_actuel = self.plateau_actuel.estMenacer() #Initialise le nombre de menaces au debut
    
    def getTemperature(self, k): #Calcul la temperature et donc le nombre de deplacement a effectuer de la combinaison actuel
        return self.initial_temperature * self.alpha ** k
    
    def main(self): #Methode qui lance la recherche de solution pour le probleme
        for k in range(self.nb_iteration): #Pour deplacer les reines jusqu'a la reussite d'une condition
            temperature = self.getTemperature(k)
            
            if self.menace_actuel == 0: #Si aucune reine ne menace une autre reine on affiche la solution
                return self.plateau_actuel
            
            #Permet de modifier la position d'une reine et de tester la nouvelle configuration
            ligne = random.randint(0, self.taille_plateau - 1)
            ancienne_colonne = self.plateau_actuel.configurationReine[ligne]
            nouvelle_colonne = random.randint(0, self.taille_plateau - 1)
            self.plateau_actuel.deplaceReine(ligne, nouvelle_colonne)
            nouvelle_menace = self.plateau_actuel.estMenacer()
            delta = nouvelle_menace - self.menace_actuel
            
            if delta < 0 or random.random() < math.exp(-delta / temperature): #Si le score de la nouvelle configuration est plus faible alors on peut mettre a jour la configuration actuelle
                self.menace_actuel = nouvelle_menace
            else: #Sinon on deplace la reine
                self.plateau_actuel.deplaceReine(ligne, ancienne_colonne)  
        return self.plateau_actuel #Renvoie le le plateau avec la solution a la fin de toutes les iterations

#Lance le programme pour effectuer la recherche de solution
sa = RechercheSolution(taille_plateau=8, initial_temperature=100, alpha=0.98, nb_iteration=10000)
solution = sa.main()
print(solution)