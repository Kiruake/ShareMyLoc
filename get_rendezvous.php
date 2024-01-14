<?php
include('connexion.php');

$id = $_GET['id'];

$query = $connexion->prepare("SELECT * FROM RendezVous WHERE id_rendezvous = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$rendezVous = $result->fetch_assoc();
$query->close();

echo json_encode($rendezVous);
?>
