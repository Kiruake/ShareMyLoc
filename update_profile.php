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

// Vérifier si le formulaire de mise à jour de la photo de profil est soumis
if (isset($_FILES['new_profile_picture']) && $_FILES['new_profile_picture']['error'] === UPLOAD_ERR_OK) {
    // Traitement de l'upload et mise à jour de la base de données
    $new_profile_picture_path = "PP/" . basename($_FILES['new_profile_picture']['name']);
    move_uploaded_file($_FILES['new_profile_picture']['tmp_name'], $new_profile_picture_path);

    // Mettre à jour la base de données avec le nouveau chemin de la photo de profil
    $query = $connexion->prepare("UPDATE Utilisateurs SET photo_profil = ? WHERE utilisateur_id = ?");
    $query->bind_param("si", $new_profile_picture_path, $user_id);
    $query->execute();
}


// Rediriger l'utilisateur vers sa page de profil après la mise à jour
header("Location: PageProfil.php");
exit;
?>
