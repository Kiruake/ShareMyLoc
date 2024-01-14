<?php

// Paramètres de connexion à la base de données
$serveur = "588ud.myd.infomaniak.com";
$utilisateur = "588ud_shareUser";
$motDePasse = "Toto1234@";
$baseDeDonnees = "588ud_shareData";

// Connexion à la base de données
$connexion = new mysqli($serveur, $utilisateur, $motDePasse, $baseDeDonnees);

// Vérification de la connexion
if ($connexion->connect_error) {
    die("Échec de la connexion à la base de données : " . $connexion->connect_error);
}

?>
