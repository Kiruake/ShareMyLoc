<?php
session_start();

// Inclure le fichier de connexion à la base de données
include('connexion.php');

// Vérifier si la clé utilisateur_id existe dans la session
if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: index.php");
    exit;
}

// Identifier l'utilisateur
$user_id = $_SESSION["utilisateur_id"];


// Vérifier si le formulaire de mise à jour de la bannière est soumis
if (isset($_FILES['new_banner']) && $_FILES['new_banner']['error'] === UPLOAD_ERR_OK) {
    // Traitement de l'upload et mise à jour de la base de données
    $new_banner_path = "bannieres/" . basename($_FILES['new_banner']['name']);
    move_uploaded_file($_FILES['new_banner']['tmp_name'], $new_banner_path);

    // Mettre à jour la base de données avec le nouveau chemin de la bannière
    $query = $connexion->prepare("UPDATE Utilisateurs SET banniere = ? WHERE utilisateur_id = ?");
    $query->bind_param("si", $new_banner_path, $user_id);
    $query->execute();
}

// Rediriger l'utilisateur vers sa page de profil après la mise à jour
header("Location: PageProfil.php");
exit;
?>