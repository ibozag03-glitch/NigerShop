DROP DATABASE IF EXISTS NigerShop;
CREATE DATABASE NigerShop CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE NigerShop;


-- ----------------------------
-- Table: users
-- ----------------------------
CREATE TABLE users (
    user_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(25) NOT NULL,
    birth_date DATE NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    inscription_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modification_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('active','blocked') NOT NULL DEFAULT 'active',
    CONSTRAINT users_PK PRIMARY KEY (user_id),
    CONSTRAINT users_email_up UNIQUE (email)
)ENGINE=InnoDB;


-- ----------------------------
-- Table: categories
-- ----------------------------
CREATE TABLE categories (
    category_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    descriptions VARCHAR(500) NOT NULL,
    CONSTRAINT categories_PK PRIMARY KEY (category_id),
    CONSTRAINT categories_slug_up UNIQUE (slug),
    CONSTRAINT categories_name_up UNIQUE (name)
)ENGINE=InnoDB;


-- ----------------------------
-- Table: annonces
-- ----------------------------
CREATE TABLE annonces (
    annonce_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_id INT UNSIGNED NOT NULL,
    owner_id INT UNSIGNED NOT NULL,
    slug VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    descriptions TEXT NOT NULL,
    price DECIMAL(10,2) UNSIGNED NOT NULL CHECK(price > 0),
    state ENUM('neuf','bon Etat','correct','occasion') NOT NULL,
    state ENUM('active','sold','expired') NOT NULL,
    product_image VARCHAR(255) NOT NULL,
    creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modification_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('publiee','vendue','supprimee') NOT NULL DEFAULT 'publiee',
    CONSTRAINT annonces_PK PRIMARY KEY (annonce_id),
    CONSTRAINT annonces_slug_up UNIQUE (slug),
    CONSTRAINT annonces_owner_id_FK FOREIGN KEY (owner_id) REFERENCES users (user_id) ON DELETE CASCADE,
    CONSTRAINT annonces_category_id_FK FOREIGN KEY (category_id) REFERENCES categories (category_id) ON DELETE CASCADE
)ENGINE=InnoDB;


-- ----------------------------
-- Table: annonces by categories
-- ----------------------------
CREATE TABLE annonces_by_categories (
    annonce_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    CONSTRAINT annonces_by_categories_PK PRIMARY KEY (annonce_id, category_id),
    CONSTRAINT annonces_by_categories_annonce_id_FK FOREIGN KEY (annonce_id) REFERENCES annonces (annonce_id) ON DELETE CASCADE,
    CONSTRAINT annonces_by_categories_catergory_id_FK FOREIGN KEY (category_id) REFERENCES categories (category_id) ON DELETE CASCADE
)ENGINE=InnoDB;


-- ----------------------------
-- Table: Favorites
-- ----------------------------
CREATE TABLE favorites (
    annonce_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    add_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT favorites_PK PRIMARY KEY (annonce_id, user_id),
    CONSTRAINT favorites_annonce_id_FK FOREIGN KEY (annonce_id) REFERENCES annonces (annonce_id) ON DELETE CASCADE,
    CONSTRAINT favorites_user_id_FK FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE
)ENGINE=InnoDB;


-- ----------------------------
-- Table: discussions
-- ----------------------------
CREATE TABLE discussions (
    discussion_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modification_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    annonce_id INT UNSIGNED NOT NULL,
    customer_id INT UNSIGNED NOT NULL,
    seller_id INT UNSIGNED NOT NULL,
    status ENUM('active','closed','blocked') NOT NULL DEFAULT 'active',
    last_message TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT discussions_PK PRIMARY KEY (discussion_id),
    CONSTRAINT discussions_annonce_id_FK FOREIGN KEY (annonce_id) REFERENCES annonces (annonce_id) ON DELETE CASCADE,
    CONSTRAINT discussions_customer_id_FK FOREIGN KEY (customer_id) REFERENCES users (user_id) ON DELETE CASCADE,
    CONSTRAINT discussions_seller_id_FK FOREIGN KEY (seller_id) REFERENCES users (user_id) ON DELETE CASCADE,
    CONSTRAINT discussions_upd UNIQUE (annonce_id, customer_id, seller_id)
)ENGINE=InnoDB;


-- ----------------------------
-- Table: messages
-- ----------------------------
CREATE TABLE messages (
    message_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    content TEXT NOT NULL,
    send_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    seen BOOLEAN NOT NULL DEFAULT FALSE,
    discussion_id INT UNSIGNED NOT NULL,
    sender_id INT UNSIGNED NOT NULL,
    receiver_id INT UNSIGNED NOT NULL,
    CONSTRAINT messages_PK PRIMARY KEY (message_id),
    CONSTRAINT messages_discussion_id_FK FOREIGN KEY (discussion_id) REFERENCES discussions (discussion_id) ON DELETE CASCADE,
    CONSTRAINT messages_sender_id_FK FOREIGN KEY (sender_id) REFERENCES users (user_id) ON DELETE CASCADE,
    CONSTRAINT messages_receiver_id_FK FOREIGN KEY (receiver_id) REFERENCES users (user_id) ON DELETE CASCADE
)ENGINE=InnoDB;