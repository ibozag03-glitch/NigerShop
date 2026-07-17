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
$DB_PWD = '';//$_ENV["DB_PWD"];

$pdo = Database::getPDO($DB_HOST, $DB_NAME, $DB_USER, $DB_PWD);

$sqlDB = 
    "DROP DATABASE IF EXISTS NS_dev_db;
    CREATE DATABASE NS_dev_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
    use NS_dev_db
";

$pdo->query($sqlDB);

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
";

$pdo->query($sqlTables);

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

$query = $pdo->prepare($sqlInsertion);
$query->execute($params);