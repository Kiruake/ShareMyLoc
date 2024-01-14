<?php
session_start();
include('connexion.php'); // Inclure votre fichier de connexion

if ($connexion->errno) {
    echo "Erreur MySQL : " . $connexion->error;
}


// Récupérer les données JSON envoyées depuis le client
$data = json_decode(file_get_contents("php://input"));

$meetingName = $data->meetingName;
$creatorId = $data->creatorId;
$centerLat = $data->centerLat;
$centerLng = $data->centerLng;

// Enregistrer l'itinéraire dans la table RendezVous
$querySaveRoute = $connexion->prepare("INSERT INTO RendezVous (nom_meeting, id_createur, point_calcule_lat, point_calcule_lng) VALUES (?, ?, ?, ?)");
$querySaveRoute->bind_param("sidd", $meetingName, $creatorId, $centerLat, $centerLng);
$querySaveRoute->execute();
$rendezVousId = $querySaveRoute->insert_id; // Récupérer l'ID du rendez-vous enregistré
$querySaveRoute->close();

// Renvoyer l'ID du rendez-vous au client
echo json_encode($rendezVousId);
?>
