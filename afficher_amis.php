<?php
// Récupérer la liste des amis de l'utilisateur
$query = $connexion->prepare("
    SELECT Utilisateurs.utilisateur_id, Utilisateurs.nickname, Utilisateurs.photo_profil
    FROM Amis
    JOIN Utilisateurs ON Amis.ami_id = Utilisateurs.utilisateur_id
    WHERE Amis.utilisateur_id = ? AND Amis.statut = 'accepte'
");
$query->bind_param("i", $_SESSION["utilisateur_id"]);
$query->execute();
$result = $query->get_result();

// Afficher la liste des amis avec des liens vers les discussions
while ($row = $result->fetch_assoc()) {
    $amiId = $row['utilisateur_id'];
    $nickname = $row['nickname'];
    $photo_profil = $row['photo_profil'];

    // Afficher la photo de profil et le nom de l'ami avec un trait vertical
    echo "<p><img src='$photo_profil' alt='Photo de profil'> <a id='PPnickname' href='message.php?ami_id=$amiId'>$nickname</a></p>";
    // Si il y a aucun amis, afficher un message "Vous n'avez pas encore d'amis" 
    if ($result->num_rows == 0) {
        echo "<p>Vous n'avez pas encore d'amis</p>";
    }

}

// Fermer la connexion
$query->close();
$connexion->close();
?>
