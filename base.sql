

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

CREATE TABLE produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    reference VARCHAR(255) NOT NULL,
    collect VARCHAR(255) NOT NULL,
    prixAchat DECIMAL(10, 2) NOT NULL,
    prixAtout DECIMAL(10, 2) NOT NULL,
    prixStella DECIMAL(10, 2) NOT NULL,
    categorie VARCHAR(255) NOT NULL,
    codeBarre VARCHAR(255),
    matiere VARCHAR(255) NOT NULL
 
);

CREATE TABLE ancienproduits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    reference VARCHAR(255) NOT NULL,
    collect VARCHAR(255) NOT NULL,
    prixAchat DECIMAL(10, 2) NOT NULL,
    prixAtout DECIMAL(10, 2) NOT NULL,
    prixStella DECIMAL(10, 2) NOT NULL,
    categorie VARCHAR(255) NOT NULL,
    codeBarre VARCHAR(255),
    matiere VARCHAR(255) NOT NULL
 
);

CREATE TABLE matieres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);

CREATE TABLE collections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL
);