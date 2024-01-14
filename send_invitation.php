<?php
session_start();

include('connexion.php');

if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: index.php");
    exit;
}

// Récupérer les données de la requête AJAX
$data = json_decode(file_get_contents("php://input"));

// Vérifier si les données nécessaires sont présentes
if (isset($data->rendezVousId) && isset($data->participants) && is_array($data->participants)) {
    $rendezVousId = $data->rendezVousId;
    $participants = $data->participants;

    // Fonction pour envoyer des invitations
    sendInvitations($rendezVousId, $_SESSION["utilisateur_id"], $participants);
}

function sendInvitations($rendezVousId, $idSender, $participants) {
    global $connexion;

    foreach ($participants as $idReceiver) {
        // Vérifier si une invitation existe déjà pour ce rendez-vous et ce destinataire
        $queryCheckInvitation = $connexion->prepare("SELECT invitation_id FROM Invitations WHERE id_rendezvous = ? AND id_receiver = ?");
        $queryCheckInvitation->bind_param("ii", $rendezVousId, $idReceiver);
        $queryCheckInvitation->execute();
        $resultCheckInvitation = $queryCheckInvitation->get_result();

        if ($resultCheckInvitation->num_rows == 0) {
            // Envoyer une nouvelle invitation
            $querySendInvitation = $connexion->prepare("INSERT INTO Invitations (id_rendezvous, id_sender, id_receiver, accepted) VALUES (?, ?, ?, 0)");
            $querySendInvitation->bind_param("iii", $rendezVousId, $idSender, $idReceiver);
            $querySendInvitation->execute();
            $querySendInvitation->close();
        }

        $queryCheckInvitation->close();
    }
}
?>
