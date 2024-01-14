<?php
session_start();
include('connexion.php'); // Inclure votre fichier de connexion

// Récupérer les données JSON envoyées depuis le client
$data = json_decode(file_get_contents("php://input"));

if ($connexion->errno) {
    echo "Erreur MySQL : " . $connexion->error;
}


$rendezVousId = $data->rendezVousId;
$participants = $data->participants;

// Enregistrer les participants dans la table Utilisateurs_RendezVous
$querySaveParticipants = $connexion->prepare("INSERT INTO Utilisateurs_RendezVous (id_user, id_rendezvous, accepted) VALUES (?, ?, NULL)");

foreach ($participants as $participantId) {
    $querySaveParticipants->bind_param("ii", $participantId, $rendezVousId);
    $querySaveParticipants->execute();
}

$querySaveParticipants->close();
?>
