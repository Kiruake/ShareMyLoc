<?php

// Paramètres de connexion à la base de données
$serveur = "localhost";
$utilisateur = "root";
$motDePasse = "root";
$baseDeDonnees = "sharemyloc";

// Connexion à la base de données
$connexion = new mysqli($serveur, $utilisateur, $motDePasse, $baseDeDonnees);

// Vérification de la connexion
if ($connexion->connect_error) {
    die("Échec de la connexion à la base de données : " . $connexion->connect_error);
}

?>
