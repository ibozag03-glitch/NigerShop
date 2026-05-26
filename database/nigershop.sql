DROP DATABASE IF EXISTS NigerShop;
CREATE DATABASE NigerShop CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE NigerShop;

-- ----------------------------
-- Table: users
-- ----------------------------
CREATE TABLE users (
    id_user INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    telephone VARCHAR(25) NOT NULL,
    date_naissance DATE NOT NULL,
    role_user ENUM('user','admin') NOT NULL DEFAULT 'user',
    date_inscription TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    statut ENUM('active','blocked') NOT NULL DEFAULT 'active',
    CONSTRAINT users_PK PRIMARY KEY (id_user),
    CONSTRAINT user_email_up UNIQUE (email)
)ENGINE=InnoDB;


-- ----------------------------
-- Table: catégorie
-- ----------------------------
CREATE TABLE categories (
    id_categorie INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    descriptions VARCHAR(500) NOT NULL,
    CONSTRAINT categories_PK PRIMARY KEY (id_categorie),
    CONSTRAINT categories_nom_up UNIQUE (nom)
)ENGINE=InnoDB;


-- ----------------------------
-- Table: annonces
-- ----------------------------
CREATE TABLE annonces (
    id_annonce INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_categorie INT UNSIGNED NOT NULL,
    id_proprietaire INT UNSIGNED NOT NULL,
    titre VARCHAR(150) NOT NULL,
    descriptions TEXT NOT NULL,
    prix DECIMAL(10,2) UNSIGNED NOT NULL CHECK(prix > 0),
    etat ENUM('neuf','bon_Etat','correct','occasion') NOT NULL,
    image_Produit VARCHAR(255) NOT NULL,
    date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    statut ENUM('publiee','vendue','supprimee') NOT NULL DEFAULT 'publiee',
    CONSTRAINT annonces_PK PRIMARY KEY (id_annonce),
    CONSTRAINT annonces_id_proprietaire_FK FOREIGN KEY (id_proprietaire) REFERENCES users (id_user) ON DELETE CASCADE,
    CONSTRAINT annonces_id_categorie_FK FOREIGN KEY (id_categorie) REFERENCES categories (id_categorie) ON DELETE CASCADE
)ENGINE=InnoDB;


-- ----------------------------
-- Table: discussions
-- ----------------------------
CREATE TABLE discussions (
    id_discussion INT UNSIGNED NOT NULL AUTO_INCREMENT,
    date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modif TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    id_annonce INT UNSIGNED NOT NULL,
    id_acheteur INT UNSIGNED NOT NULL,
    id_vendeur INT UNSIGNED NOT NULL,
    statut ENUM('active','closed','blocked') NOT NULL DEFAULT 'active',
    last_message TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT discussions_PK PRIMARY KEY (id_discussion),
    CONSTRAINT discussions_id_annonce_FK FOREIGN KEY (id_annonce) REFERENCES annonces (id_annonce) ON DELETE CASCADE,
    CONSTRAINT discussions_id_acheteur_FK FOREIGN KEY (id_acheteur) REFERENCES users (id_user) ON DELETE CASCADE,
    CONSTRAINT discussions_id_vendeur_FK FOREIGN KEY (id_vendeur) REFERENCES users (id_user) ON DELETE CASCADE,
    CONSTRAINT discussions_maj UNIQUE (id_annonce, id_acheteur, id_vendeur)
)ENGINE=InnoDB;


-- ----------------------------
-- Table: messages
-- ----------------------------
CREATE TABLE messages (
    id_message INT UNSIGNED NOT NULL AUTO_INCREMENT,
    contenu TEXT NOT NULL,
    date_envoie TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    vue BOOLEAN NOT NULL DEFAULT FALSE,
    id_discussion INT UNSIGNED NOT NULL,
    id_expediteur INT UNSIGNED NOT NULL,
    id_destinataire INT UNSIGNED NOT NULL,
    CONSTRAINT messages_PK PRIMARY KEY (id_message),
    CONSTRAINT messages_id_discussion_FK FOREIGN KEY (id_discussion) REFERENCES discussions (id_discussion) ON DELETE CASCADE,
    CONSTRAINT messages_id_expediteur_FK FOREIGN KEY (id_expediteur) REFERENCES users (id_user) ON DELETE CASCADE,
    CONSTRAINT messages_id_destinataire_FK FOREIGN KEY (id_destinataire) REFERENCES users (id_user) ON DELETE CASCADE
)ENGINE=InnoDB;


-- ----------------------------
-- Table: favoris
-- ----------------------------
CREATE TABLE favoris (
    id_annonce INT UNSIGNED NOT NULL,
    id_user INT UNSIGNED NOT NULL,
    date_ajout TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT favoris_PK PRIMARY KEY (id_annonce, id_user),
    CONSTRAINT favoris_id_annonce_FK FOREIGN KEY (id_annonce) REFERENCES annonces (id_annonce) ON DELETE CASCADE,
    CONSTRAINT favoris_id_user_FK FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE CASCADE
)ENGINE=InnoDB;