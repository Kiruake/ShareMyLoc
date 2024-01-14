<?php
include('connexion.php');

$id = $_GET['id'];

$query = $connexion->prepare("SELECT U.latitude, U.longitude FROM Utilisateurs_RendezVous UR JOIN Utilisateurs U ON UR.id_user = U.utilisateur_id WHERE UR.id_rendezvous = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$participants = $result->fetch_all(MYSQLI_ASSOC);
$query->close();

echo json_encode($participants);
?>
