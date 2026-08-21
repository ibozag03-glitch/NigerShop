<?php
require dirname(__Dir__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';
require dirname(__Dir__) . DIRECTORY_SEPARATOR . 'config/config.php';

use App\Core\Database;


// use the factory to create a Faker\Generator instance
$faker = Faker\Factory::create('fr_FR');

chargerEnv(NS_REP . '.env');

$DB_HOST = $_ENV["DB_HOST"];
$DB_NAME = $_ENV["DB_NAME"];
$DB_USER = $_ENV["DB_USER"];
$DB_PWD = $_ENV["DB_PASS"];

Database::init($DB_HOST, $DB_NAME, $DB_USER, $DB_PWD);

$devDB = 
    "DROP DATABASE IF EXISTS NS_dev_db;
    CREATE DATABASE NS_dev_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
    use NS_dev_db
";

Database::query($devDB);

$sqlTables = 
    "CREATE TABLE users (
        user_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        firstname VARCHAR(50) NOT NULL,
        lastname VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL,
        address VARCHAR(255) NOT NULL,
        phone VARCHAR(25) NOT NULL,
        birth_date VARCHAR(25) NOT NULL,
        role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
        inscription_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        modification_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        status ENUM('active','blocked') NOT NULL DEFAULT 'active',
        CONSTRAINT users_PK PRIMARY KEY (user_id),
        CONSTRAINT users_email_up UNIQUE (email)
    )ENGINE=InnoDB;
    
    CREATE TABLE categories (
        category_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        slug VARCHAR(255) NOT NULL,
        name VARCHAR(255) NOT NULL,
        descriptions VARCHAR(500) NOT NULL,
        CONSTRAINT categories_PK PRIMARY KEY (category_id),
        CONSTRAINT categories_slug_up UNIQUE (slug),
        CONSTRAINT categories_name_up UNIQUE (name)
    )ENGINE=InnoDB;

    CREATE TABLE annonces (
        annonce_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        category_id INT UNSIGNED NOT NULL,
        owner_id INT UNSIGNED NOT NULL,
        slug VARCHAR(255) NOT NULL,
        title VARCHAR(255) NOT NULL,
        descriptions TEXT NOT NULL,
        price DECIMAL(10,2) UNSIGNED NOT NULL CHECK(price > 0),
        state ENUM('new','good condition','correct','used') NOT NULL,
        product_image VARCHAR(255) NOT NULL,
        creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        modification_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        status ENUM('active','sold','expired') NOT NULL DEFAULT 'active',
        CONSTRAINT annonces_PK PRIMARY KEY (annonce_id),
        CONSTRAINT annonces_slug_up UNIQUE (slug),
        CONSTRAINT annonces_owner_id_FK FOREIGN KEY (owner_id) REFERENCES users (user_id) ON DELETE CASCADE,
        CONSTRAINT annonces_category_id_FK FOREIGN KEY (category_id) REFERENCES categories (category_id) ON DELETE CASCADE
    )ENGINE=InnoDB;

    CREATE TABLE annonces_by_categories (
        annonce_id INT UNSIGNED NOT NULL,
        category_id INT UNSIGNED NOT NULL,
        CONSTRAINT annonces_by_categories_PK PRIMARY KEY (annonce_id, category_id),
        CONSTRAINT annonces_by_categories_annonce_id_FK FOREIGN KEY (annonce_id) REFERENCES annonces (annonce_id) ON DELETE CASCADE,
        CONSTRAINT annonces_by_categories_catergory_id_FK FOREIGN KEY (category_id) REFERENCES categories (category_id) ON DELETE CASCADE
    )ENGINE=InnoDB;
";

Database::query($sqlTables);

$sqlInsertion = "
    INSERT INTO users (firstname, lastname, email, password, address, phone, birth_date, role)
    VALUES (:firstname, :lastname, :email, :password, :address, :phone, :birth_date, 'admin');
";

// $inscription_date = new DateTime();

// dd($inscription_date);

$params = [
    "firstname" => "leamsi",
    "lastname" => "ibozag",
    "email" => "leamsiibozag@gmail.com",
    "password" => "AzertyQwerty123!",
    "address" => "Somewhere here at my location In The Wolrd",
    "phone" => "+277 86 61 20 16",
    "birth_date" => "07/02/2007",
];

// $query = $pdo->prepare($sqlInsertion);
// $query->execute($params);