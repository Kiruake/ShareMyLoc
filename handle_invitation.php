<?php
session_start();


error_reporting(E_ALL);
ini_set("display_errors", 1);
// Inclure le fichier de connexion à la base de données
include('connexion.php');

// Récupérer les données de la requête AJAX
$data = json_decode(file_get_contents("php://input"));

// Vérifier si les données nécessaires sont présentes
if (isset($data->rendezVousId) && isset($data->response)) {
    $rendezVousId = $data->rendezVousId;
    $response = $data->response;

    // Fonction pour mettre à jour l'état de l'invitation dans la base de données
    handleInvitationResponse($rendezVousId, $_SESSION["utilisateur_id"], $response);
}

function handleInvitationResponse($rendezVousId, $idReceiver, $response) {
    global $connexion;
    // Utilisez la variable $connexion à l'intérieur de la fonction
    $acceptedValue = ($response === 'accept' ? 1 : 0);
    // Mettre à jour l'état de l'invitation dans la base de données
    $queryUpdateInvitation = $connexion->prepare("UPDATE Invitations SET accepted = ? WHERE id_rendezvous = ? AND id_receiver = ?");
    $queryUpdateInvitation->bind_param("iii", $acceptedValue, $rendezVousId, $idReceiver);
    $queryUpdateInvitation->execute();
    $queryUpdateInvitation->close();   

    // Retourner une réponse JSON pour informer le frontend du succès ou de l'échec de l'opération
    $responseArray = array('success' => true, 'message' => 'Invitation traitée avec succès');
    echo json_encode($responseArray);
    exit; // Ajoutez cette ligne pour arrêter l'exécution après l'envoi du JSON
}
?>
