drop schema if exists transmusicales CASCADE; create schema transmusicales;
set schema 'transmusicales';

CREATE TABLE _annee( 
    an INT PRIMARY KEY
);

CREATE TABLE _pays(
    nom_p VARCHAR(30) PRIMARY KEY
);

CREATE TABLE _ville(
    nom_v VARCHAR(30) PRIMARY KEY,
    nom_p VARCHAR(30) NOT NULL,

    CONSTRAINT ville_fk_pays FOREIGN KEY(nom_p) REFERENCES _pays(nom_p) 
);

CREATE TABLE _lieu(
    id_lieu VARCHAR(10),
    nom_lieu VARCHAR(30) NOT NULL, 
    accesPMR BOOLEAN NOT NULL, 
    capacite_max INT NOT NULL,
    type_lieu VARCHAR(15) NOT NULL,
    nom_v VARCHAR(30) NOT NULL, 

    CONSTRAINT lieu_pk PRIMARY KEY(id_lieu),
    CONSTRAINT lieu_fk_ville FOREIGN KEY(nom_v) REFERENCES _ville(nom_v) 
);

CREATE TABLE _formation(
    libelle_formation VARCHAR(30) PRIMARY KEY 
);

CREATE TABLE _edition(
    nom_edition VARCHAR(30) PRIMARY KEY,
    annee_edition INT NOT NULL,

    CONSTRAINT edition_fk_annee FOREIGN KEY(annee_edition) REFERENCES _annee(an) 
);

CREATE TABLE _type_musique(
    type_m VARCHAR(20) PRIMARY KEY 
);

CREATE TABLE _concert(
    no_concert VARCHAR(20),
    titre VARCHAR(20) NOT NULL,
    resume_c VARCHAR(50),
    duree INT NOT NULL,
    tarif FLOAT NOT NULL,
    type_m VARCHAR(20) NOT NULL,
    nom_edition VARCHAR(30) NOT NULL,
	  CONSTRAINT concert_pk PRIMARY KEY(no_concert),
    CONSTRAINT concert_fk_type_musique FOREIGN KEY(type_m) REFERENCES _type_musique(type_m),
    CONSTRAINT concert_fk_edition FOREIGN KEY(nom_edition) REFERENCES _edition(nom_edition)
);

CREATE TABLE _groupe_artiste( 
    id_groupe_artiste VARCHAR(10),
    nom_groupe_artiste VARCHAR(20) NOT NULL, 
    site_web VARCHAR(20),
    annee_debut INT NOT NULL,
    sortie_discographie INT NOT NULL,
    pays_origine VARCHAR(30) NOT NULL,
    type_principal VARCHAR(20),
    CONSTRAINT groupe_artiste_pk PRIMARY KEY (id_groupe_artiste),
    CONSTRAINT groupe_artiste_fk_type_musique FOREIGN KEY(type_principal) REFERENCES _type_musique(type_m),   
    CONSTRAINT groupe_artiste_fk_annee FOREIGN KEY (annee_debut) REFERENCES _annee(an),
    CONSTRAINT groupe_artiste_fk_annee2 FOREIGN KEY (sortie_discographie) REFERENCES _annee(an),
    CONSTRAINT groupe_artiste_fk_pays FOREIGN KEY (pays_origine) REFERENCES _pays(nom_p)
);
CREATE TABLE _representation( 
    numero_representation VARCHAR(10),
    heure VARCHAR(10) NOT NULL,
    date_representation DATE NOT NULL,
    id_lieu VARCHAR(10) NOT NULL,
    no_concert VARCHAR(20) NOT NULL,
    id_groupe_artiste VARCHAR(10),  
	  CONSTRAINT representation_pk PRIMARY KEY(numero_representation),
    CONSTRAINT representation_fk_lieu FOREIGN KEY(id_lieu) REFERENCES _lieu(id_lieu),
    CONSTRAINT representation_fk_concert FOREIGN KEY(no_concert) REFERENCES _concert(no_concert),
    CONSTRAINT representation_fk_groupe_artiste FOREIGN KEY(id_groupe_artiste) REFERENCES _groupe_artiste(id_groupe_artiste)
);

CREATE TABLE _type_ponctuel(
    id_groupe_artiste VARCHAR(10), 
    type_ponctuel VARCHAR(20),
    CONSTRAINT type_ponctuel_pk PRIMARY KEY(id_groupe_artiste , type_ponctuel) , 
    CONSTRAINT type_ponctuel_fk_type_musique FOREIGN KEY(type_ponctuel) REFERENCES _type_musique(type_m),   
    CONSTRAINT type_ponctuel_fk_groupe_artiste FOREIGN KEY(id_groupe_artiste) REFERENCES _groupe_artiste(id_groupe_artiste)
);



CREATE TABLE _a_pour( 
    id_groupe_artiste VARCHAR(10),
    formation_groupe_artiste VARCHAR(20),
    CONSTRAINT a_pour_pk PRIMARY KEY(id_groupe_artiste , formation_groupe_artiste), 
    CONSTRAINT a_pour_fk_formation FOREIGN KEY(formation_groupe_artiste) REFERENCES _formation(libelle_formation),
    CONSTRAINT a_pour_fk_groupe_artiste FOREIGN KEY(id_groupe_artiste ) REFERENCES _groupe_artiste(id_groupe_artiste )
);
commit;

