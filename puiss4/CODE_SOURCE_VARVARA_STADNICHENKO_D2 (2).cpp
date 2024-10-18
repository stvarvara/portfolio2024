/**
*
* \brief Programme de jeu de Puissance4.
*
* \author Varvara Stadnichenko D1.2
*
* \version 1.0
*
* \date 30 octobre 2022
*
* Le jeu de Puissance4 se joue à 2 et consiste à faire tomber à tour de rôle ses pions dans
* une grille de 6 lignes x 7 colonnes. Le but est d’être le premier à aligner 4 de ses pions
* soit horizontalement, soit verticalement, soit en diagonale.
*
*/

#include<stdio.h>
#include<stdlib.h>
#include<stdbool.h>
/*****************************************************
*                  LES CONSTANTES                    *
*****************************************************/

/**
* \def PION_A
*
* \brief constante pour le pion du joueur 1.
*/
const char PION_A = 'X';
/**
* \def PION_B
*
* \brief constante pour le pion du joueur 2.
*/
const char PION_B = 'O';
/**
* \def VIDE
*
* \brief constante pour remplir le tableau avec les espaces.
*/
const char VIDE = ' ';
/**
* \def INCONNU
*
* \brief constante pour le vainqueur qui est inconnu avant la fin de jeu.
*/
const char INCONNU = ' ';
/**
* \def NBLIG
*
* \brief constante pour le nombre des lignes.
*/
#define NBLIG 6
/**
* \def NBCOL
*
* \brief constante pour le nombre des colonnes .
*/
#define NBCOL 7
/**
* \def COLONNE_DEBUT
*
* \brief constante pour le numero de la colonne au-dessus de laquelle le pion va etre place au debut.
*/
const int COLONNE_DEBUT = NBCOL/2;
/**
* \def Grille
*
* \brief constante pour le type de tableau qui va etra notre grille pour le jeu.
*/
typedef int Grille [NBLIG][NBCOL];

/*****************************************************
*    LES PROTOTYPES DES FONCTIONS ET PROCEDURES      *
*****************************************************/
void initGrille(Grille);
void afficher(Grille, char, int);
bool grillePleine(Grille);
void jouer(Grille, char, int*, int*);
int choisirColonne(Grille, char, int);
int trouverLigne(Grille, int);
bool estVainqueur(Grille, int, int);
void finDePartie(char);
/*****************************************************
*               PROGRAMME PRINCIPAL                  *
*****************************************************/
int main()
{
  char vainqueur; /** le joueur qui a gagne **/
  int ligne; /** le numéro de la ligne **/
  int colonne; /** le numéro de la colonne **/
  Grille g; /** la grille qui est represante par le tableau**/
  initGrille(g);
  vainqueur=INCONNU;
  afficher(g, PION_A, COLONNE_DEBUT);
  while ((vainqueur==INCONNU)&&(!(grillePleine(g)))) /** on va jouer tant que quelqu'un a gagne **/
    {
      jouer(g, PION_A, &ligne, &colonne);
      afficher(g, PION_B, COLONNE_DEBUT);
      if (estVainqueur(g, ligne, colonne))
      {
        vainqueur=PION_A;
      }
      else if (!(grillePleine(g))) /** pour changer le joueur **/
        {
          jouer(g, PION_B, &ligne, &colonne);
          afficher(g, PION_A, COLONNE_DEBUT);
           if (estVainqueur(g, ligne, colonne))
              {
                  vainqueur=PION_B;
              }
        }
    }
    finDePartie(vainqueur); /** afficher le vainqueur **/
}
/*****************************************************
*         FONCTIONS ET PROCEDURES UTILISEES          *
*****************************************************/
/**
* \fn void initGrille(Grille g)
*
* \brief Procedure qui initialise la grille en affectant la constante VIDE à chacun de ses éléments.
*
* \param g: grille, represente la grille de jeu
*
*/
void initGrille(Grille g)
{
  int i,j;

  for(i=0; i<NBLIG; i++)
  {
    for(j=0; j<NBCOL; j++)
    g[i][j] = VIDE;
  }
}
/**
* \fn void afficher(Grille g, char pion, int colonne)
*
* \brief Procedure qui réalise l’affichage à l’écran du contenu de la grille avec les pions déjà joués. Cette procédure
* affiche aussi, au-dessus de la grille, le prochain pion à tomber : il sera affiché au-dessus de la
* colonne dont le numéro est donné en paramètre. Cette procédure commencera par effacer l’écran.
*
* \param g: grille, represente la grille de jeu
*
* \param pion: caractère, représente le pion à afficher au-dessus de la grille
* \param colonne: entier, représente l’indice de la colonne au-dessus de laquelle le pion doit être affiché
*/
void afficher(Grille g, char pion, int colonne)
{
  /*pour afficher le pion qui va tomber */
  printf("\n\n    ");
  for(int b=0; b<colonne; b++)
  printf("  %c ", VIDE);
  printf("  %c ", pion);
  printf("\n");

  for (int a=NBLIG-1; a>=0; a--)
  { printf("    -----------------------------  ");
    printf("\n %d  ", a+1); /** pour afficher les numéro des lignes **/
    for(int b=0; b<NBCOL; b++) printf("| %c ", g[a][b]);
    printf("|\n");
  }
  printf("    -----------------------------  ");
  printf("\n\n    ");
  for(int b=0; b<NBCOL; b++) printf("  %d ", b+1); /** pour afficher les numéro des colonnes  **/
  printf("\n");
}
/**
* \fn bool grillePleine(Grille g)
*
* \brief Fonction qui teste si toutes les cases de la grille sont occupées ou non.
*
* \param g: grille, represente la grille de jeu
* \return bool: VRAI si toutes les cases de la grille sont occupées par les pions, FAUX sinon.
*/
bool grillePleine(Grille g)
{
  int i, j, compt=0;
  for(i=0; i<NBLIG; i++)
  {
    for(j=0; j<NBCOL; j++)
     if(g[i][j] == VIDE)
      compt++;
  }
  if(compt ==0)
  {
     return true;
  }
  return false;
}
/**
* \fn void jouer(Grille g, char pion, int *ligne, int *colonne)
*
* \brief Procedure qui permet à un joueur de jouer son pion. La procédure fait appel à choisirColonne, afin que le
* joueur indique la colonne dans laquelle il veut jouer ; puis fait appel à trouverLigne pour définir
* la case où ajouter le pion.
* \param g: grille, represente la grille de jeu
* \param pion: caractère, correspond au pion à jouer
* \param ligne: entier, correspond à la ligne où est tombé le pion
* \param colonne:  entier, correspond à la colonne où va tomber le pion                            
*/
void jouer(Grille g, char pion, int *ligne, int *colonne)
{
  *colonne = choisirColonne(g, pion, *colonne);
  *ligne = trouverLigne(g, *colonne);
  g[*ligne][*colonne] = pion;
}
/**
* \fn int choisirColonne(Grille g, char pion, int colonn)
*
* \brief Un joueur voit son pion au-dessus de la grille et cette fonction doit lui permettre de "déplacer"
* son pion d’une colonne vers la gauche (par la touche ‘q’) ou d’une colonne vers la droite (par la
* touche ‘d’). Après chaque déplacement, la grille est réaffichée. Le joueur peut finalement
* choisir la colonne où il souhaite faire tomber son pion (par la touche ESPACE).
*
* \param g: grille, represente la grille de jeu
* \param pion: caractère, représente le pion à tester et à placer                                              
* \param colonne:  colonne de départ (celle au-dessus de laquelle se trouve le pion initialement)
* \return entier : indice de la colonne choisie par le joueur
*/
int choisirColonne(Grille g, char pion, int colonn)
{
  int lig;
  char dir, retour;
  do
  {
    colonn = COLONNE_DEBUT;
    printf("\n\n Pour deplacer le pion tapez g, d ou espace %c  ? : ", pion);
    scanf("%c%c", &dir, &retour);
    while(dir != ' ')
    { if(dir == 'g')
      { if (colonn == 0) printf("\n On est au bout gauche du tableau, il faut revenir vers la droite");
        else colonn--;
        afficher(g, pion, colonn);
      }
      else if(dir == 'd')
           { if (colonn == NBCOL-1) printf("\n On est au bout droit du tableau, il faut revenir vers la gauche");
             else colonn++;
             afficher(g, pion, colonn);
           }
      scanf("%c%c", &dir, &retour);
    }
    lig = trouverLigne(g, colonn);
      if(lig != -1) return colonn;
      else
      {
        printf("\n Choix impossible, colonne pleine, choisir une autre colonne");
      }

  } while(lig == -1);
}
/**
* \fn int trouverLigne(Grille g, int colonne)
*
* \brief Consiste à trouver la première case non occupée de la colonne. Si la colonne est pleine, la
* fonction retourne -1
*
* \param g: grille, represente la grille de jeu
* \param colonne:  entier, indice de la colonne dans laquelle le pion doit tomber
* \return entier : indice de la ligne où le pion devra être ajouté ou -1 si la colonne est pleine
*/
int trouverLigne(Grille g, int colonne)
{
    int i;
      for(i=0; i<NBLIG; i++)
      {
        if(g[i][colonne] == VIDE)
        {
          return i;
        }
      }
    printf("\n Choix impossible, colonne pleine, choisir une autre colonne"); // le joueur va rejouer
    return -1;
}
/**
* \fn bool estVainqueur(Grille g, int ligne, int colonne)
*
* \brief Indique si le pion situé dans la case repérée par les paramètres ligne et colonne a gagné la partie,
* c’est-à-dire s’il y a une ligne, une colonne ou une diagonale formée d’au moins 4 de ses pions (la
* ligne et la colonne passées en paramètres correspondent à la case où le joueur vient de jouer,
* c’est-à-dire la case à partir de laquelle il faut rechercher 4 pions successifs identiques)
*
* \param g: grille, represente la grille de jeu
* \param colonne:  entier, indice de la colonne à partir de laquelle rechercher une série de
*  4 pions successifs identiques                                                                                       
* \param ligne:  entier, indice de la ligne à partir de laquelle rechercher une série de
*  4 pions successifs identiques                                                                                        
  \return booléen : VRAI s’il y a 4 identiques successifs à partir de la case indiquée, FAUX sinon                      
*/
bool estVainqueur(Grille g, int ligne, int colonne)
{
  int compteur;
  char pion = g[ligne][colonne];
  // pour compter le nombre de X / O consécutifs dans la colonne col                                    
  compteur = 0;
  for(int i=0; i<NBLIG; i++)
  { if(g[i][colonne] == pion) {compteur++; }
    else  compteur = 0; // on reprend le comptage a 0
    if(compteur == 4)
    { printf("\nGAGNE 4 pions sur la colonne %d\n", colonne+1);
      return true;
    }
  }
  // pour compter le nombre de X / O consécutifs dans la ligne lig                                      
  compteur = 0;
  for(int j=0; j<NBCOL; j++)
  { if(g[ligne][j] == pion) {compteur++; }
    else compteur = 0; // on reprend le comptage a 0
    if(compteur == 4)
    { printf("\nGAGNE 4 pions sur la ligne %d\n", ligne+1);
      return true;
    }
  }
  // pour compter le nombre de X / O consécutifs dans la diagonale droite                             
  compteur=0;
  int i_debut, j; // i numero de ligne et j numero de colonne
  if(ligne<=colonne) { i_debut = 0; j=colonne-ligne;}
  else { i_debut = ligne-colonne; j=0;}
  for(int i=i_debut; i<NBLIG && j<NBCOL; i++, j++)
  {
    if(g[i][j] == pion) {compteur++;}
    else compteur = 0;
    if(compteur == 4)
    { printf("\nGAGNE 4 pions sur la DD\n");
      return true;
    }
  }
 // pour compter le nombre de X / O consécutifs dans la diagonale gauche                                
  compteur=0;
  if(ligne+colonne<NBCOL) { i_debut = 0; j=colonne+ligne; }
  else { i_debut = ligne+colonne-NBCOL+1; j=NBCOL-1;  }
  for(int i=i_debut; i<NBLIG && j>=0; i++, j--)
  {
    if(g[i][j] == pion) {compteur++;
                      }
    else compteur = 0;
    if(compteur == 4)
    { printf("\nGAGNE 4 pions sur la DG\n");
      return true;
    }
  }
return false;
}
/**
* \fn void finDePartie(char pion)
*
* \brief Affiche le résultat d’une partie lorsqu’elle est terminée.
*
* \param pion: caractère qui représente le pion gagnant (PION_A ou PION_B) ou bien VIDE si
* match nul
*
*/
void finDePartie(char pion){
    printf("\n Vainqueur : %c", pion);
}
