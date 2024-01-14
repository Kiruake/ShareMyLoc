<?php
session_start();

// Vérifier si la clé utilisateur_id existe dans la session
if (isset($_SESSION["utilisateur_id"])) {
    // Récupérer les coordonnées de géolocalisation à partir des données POST
    $userLat = $_POST['latitude'] ?? null;
    $userLng = $_POST['longitude'] ?? null;

    // Vérifier si les coordonnées sont présentes
    if ($userLat !== null && $userLng !== null) {
        // Mettez à jour les coordonnées de l'utilisateur dans la base de données
        include('connexion.php');
        $userId = $_SESSION["utilisateur_id"];
        $query = $connexion->prepare("UPDATE Utilisateurs SET latitude = ?, longitude = ? WHERE utilisateur_id = ?");
        $query->bind_param("dds", $userLat, $userLng, $userId);
        $query->execute();
        $query->close();
        $connexion->close();
        
        echo json_encode(['success' => true, 'message' => 'Coordonnées mises à jour avec succès.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Coordonnées manquantes.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Accès interdit.']);
}
?>