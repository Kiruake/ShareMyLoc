<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: index.php");
    exit;
}

// Récupérer l'ID de l'ami à partir de la requête
$ami_id = $_GET['ami_id'];

// Inclure le fichier de connexion à la base de données
include('connexion.php');

// Vérifier si une demande d'amitié existe déjà
$query = $connexion->prepare("SELECT id FROM Amis WHERE utilisateur_id = ? AND ami_id = ?");
$query->bind_param("ii", $_SESSION["utilisateur_id"], $ami_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    // Envoyer la demande d'amitié
    $query = $connexion->prepare("INSERT INTO Amis (utilisateur_id, ami_id, statut) VALUES (?, ?, 'en_attente')");
    $query->bind_param("ii", $_SESSION["utilisateur_id"], $ami_id);
    $query->execute();
}

// Rediriger vers la liste des utilisateurs
header("Location: listeUsers.php");
exit;
?>

<?php include('footer.php'); ?>
    