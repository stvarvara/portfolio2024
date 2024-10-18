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
* Ce programme propose à deux joueurs de jouer à Puissance4. Le jeu de Puissance4 se joue à 2 et consiste à faire tomber à tour de rôle ses pions dans
* une grille de 6 lignes x 7 colonnes. Le but est d’être le premier à aligner 4 de ses pions
* soit horizontalement, soit verticalement, soit en diagonale.
*
*/

#include<stdio.h>
#include<stdlib.h>

/*****************************************************
*    LES PROTOTYPES DES FONCTIONS ET PROCÉDURES      *
*****************************************************/

void init();
void afficher();
int jouer(int);
int compter(int n, int lig, int col);
int t[7][8]; /** Le tableau qui va être la grille**/

/*****************************************************
*               PROGRAMME PRINCIPAL                  *
*****************************************************/

int main()
{
  int numero_joueur=2, k = 0; /** Le numéro de joueur et le compteur des jetons **/
  init();
  do
  {
    if(numero_joueur == 1)
    {
         numero_joueur = 2;
    }
    else 
    {
        numero_joueur = 1;
    }
    k = jouer(numero_joueur);
    afficher();
  } while( k != 4); /** on va jouer tant que  quelqu'un va avoir 4 jetons dans la même colonne, ou ligne ou diagonale **/
  printf("\n Partie terminee ! Le joueur %d a gagne !", numero_joueur);
  return EXIT_SUCCESS;
}
/*****************************************************
*         FONCTIONS ET PROCÉDURES UTILISÉES          *
*****************************************************/
/**
* \fn void init()
*
* \brief Procédure qui initialise le tableau de zéro.
* La procédure appele la procédure "afficher".
*/
void init()
{ int l,c; /** Le numéro de la ligne et le numéro de la colonne**/

  for(l=1; l<7; l++)
  { 
    for(c=1; c<8; c++)
    {
     t[l][c] = 0;
    }
  }
  afficher();
}

/**
*
* \fn void afficher()
*
* \brief Procédure qui affiche la grille de 6 lignes x 7 colonnes.
*
*/

void afficher()
{
  for (int l=6; l>=1; l--) /** Afficher les numéros des lignes **/
  { 
    printf("    -----------------------------  ");
    printf("\n %d  ", l); 
    for(int c=1; c<8; c++) /** Afficher les cases vides ou les jetons **/
    {
        if(t[l][c]==0)
        { 
         printf("|   ");
        } 
        else
        {
         printf("| %d ", t[l][c]);
        }
        
    }
    printf("|\n");
  }
  printf("    -----------------------------  ");
  printf("\n\n    ");
  for(int c=1; c<8; c++) /** Afficher les numéros des colonnes **/
  {
     printf("  %d ", c);
  }
  printf("\n");
}
/**
*
* \fn int jouer(int)
*
* \brief Fonction qui consiste le jeu principal. Elle demande tour à tour aux joueurs le numéro de la
* colonne et place les jetons (1 ou 2).
*
* \param n : le numero de joueur (1 ou 2) qui a le tour maintenant. 
*
* \return k : entier, le nombre de jetons; (4 si on a aligné 4 jetons, sinon 1 ou
* 2 ou 3 ou 0).
*
* La fonction appelle la fonctiom "compter". Consiste à vérifier si les données saisies sont correctes ( numéro
* de colonne - entier entre 1 et 7)
*
*/

int jouer(int n)
{ int col, lin, erreur=0; /** Les numéros de la colonne et de la ligne, le marqueur d'une erreur **/
  do
  { printf("\n\n\n\n\n\n Joueur %d  Colonne ? : ", n);
    scanf("%d", &col);
    if(col >= 1 && col <= 7) /**on doit vérifier si les données saisies sont correctes **/

    { for(lin=1; lin<7; lin++)
      { 
        if(t[lin][col] == 0)
         { t[lin][col] = n; 
           break;
         }
      }
      if(lin==7)
      { 
        printf("\n Erreur de saisie. Choisissez une autre colonne");
        erreur=1;
      }
      else
      {
        erreur=0;
      }
    }
    else /** si la case est déjà remplie **/
    { 
        printf("\n Erreur de saisie. Choisissez une autre colonne");
        erreur = 1;
    }
  } while(erreur == 1); /** on va redemander le même joueur tant que il saisit les données correctes **/
  int k =compter(n, lin, col);
  return k;
}

/**
*
* \fn int compter(int n, int lig, int col)
*
* \brief Fonction qui compte le nombre de jetons 1 / 2 consécutif dans la colonne col;
* compte le nombre de jetons 1 / 2 consécutif dans la ligne lig ;
* compte le nombre de jetons 1 / 2 consécutif dans la diagonale droite;
* compte le nombre de jetons 1 / 2 consécutif dans la diagonale gauche.
*
* \param n : le numero de joueur (1 ou 2) qui a le tour maintenant. 
*
* \param lig : entier, le numéro de la ligne où le joueur à place le jeton.
*
* \param col : entier, le numéro de la colonne où le joueur à place le jeton.
* 
* \return compt: entier, le nombre de jetons (4 si on a aligné 4 jetons, sinon 1 ou
* 2 ou 3 ou 0)
*
*
*/

int compter(int n, int lig, int col)
{ int compt;
  printf("\n");

  // pour compter le nombre de 1 / 2 consécutifs dans la colonne col
  compt=0;
  for(int l=1; l<7; l++)
  { 
    if(t[l][col] == n)
    { 
        compt++;
    }
    else if(t[l][col] == 0) 
    {
        break;
    }
    else
    {
         compt=0;
    }
  }
  if(compt==4)
  { 
    return compt;
  }


  // pour compter le nombre de 1 / 2 consécutifs dans la ligne lig
  compt=0;
  for(int c=1; c<8; c++)
  { 
    if(t[lig][c] == n)
    {
        compt++; 
    }
    else 
    {
        compt=0;
    }
    if(compt == 4)
    { 
      return compt;
    }
  }

  // pour compter le nombre de 1 / 2 consécutifs dans la diagonale droite
  compt=0;
  int l_debut, cl; // i numéro de ligne et j numéro de colonne
  if(lig<=col)
  { 
    l_debut = 1; 
    cl=col-lig+1;
  }
  else 
  { 
    l_debut = lig-col+1;
     cl=1;
  }
  for(int l=l_debut; l<7 && cl<=7; l++, cl++)
  { 
    if(t[l][col-lig+l] == n)
    {
        compt++;
    } 
    else 
    {
        compt=0;
    }
    if(compt == 4)
    {
      return compt;
    }
  }


 // pour compter le nombre de 1 / 2 consécutifs dans la diagonale gauche
  compt=0;
  if(lig+col<=7) 
  { 
    l_debut = 1; 
    cl=col-lig+1;
  }
  else 
  {
     l_debut = lig+col-7; 
     cl=7;
  }
  for(int l=l_debut; l<7 && cl>0; l++, cl--)
  { 
    if(t[l][col+lig-l] == n) 
    {
        compt++;
    }
    else
    {
        compt=0;
    }
    if(compt == 4)
    {
      return compt;
    }
  }
  return compt;
}
