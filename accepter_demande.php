<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: index.php");
    exit;
}

// Inclure le fichier de connexion à la base de données
include('connexion.php');

// Récupérer l'ID de la demande d'amitié à partir de la requête
$demande_id = $_GET['demande_id'];

// Mettre à jour le statut de la demande d'amitié à 'accepte'
$query = $connexion->prepare("UPDATE Amis SET statut = 'accepte' WHERE id = ?");
$query->bind_param("i", $demande_id);
$query->execute();

// Récupérer les informations sur la demande acceptée
$query_info = $connexion->prepare("SELECT utilisateur_id, ami_id FROM Amis WHERE id = ?");
$query_info->bind_param("i", $demande_id);
$query_info->execute();
$result_info = $query_info->get_result();

if ($row_info = $result_info->fetch_assoc()) {
    // Insérer la relation d'amitié dans les deux sens
    $query_insert = $connexion->prepare("INSERT INTO Amis (utilisateur_id, ami_id, statut) VALUES (?, ?, 'accepte')");
    
    $query_insert->bind_param("ii", $row_info['ami_id'], $row_info['utilisateur_id']);
    $query_insert->execute();
}

// Rediriger vers la page des demandes d'amitié
header("Location: PageProfil.php");
exit;


?>

<?php include('footer.php'); ?>
