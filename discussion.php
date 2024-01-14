<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: index.php");
    exit;
}

// Inclure le fichier de connexion à la base de données
include('connexion.php');

// Récupérer l'ami avec lequel l'utilisateur souhaite discuter
if (isset($_GET['ami_id'])) {
    $amiId = $_GET['ami_id'];
} else {
    // Rediriger si aucun ami spécifié
    header("Location: amis.php");
    exit;
}

// Vérifier si l'ami spécifié est vraiment un ami
$queryAmi = $connexion->prepare("SELECT 1 FROM Amis WHERE utilisateur_id = ? AND ami_id = ? AND statut = 'accepte'");
$queryAmi->bind_param("ii", $_SESSION["utilisateur_id"], $amiId);
$queryAmi->execute();
$resultAmi = $queryAmi->get_result();

if ($resultAmi->num_rows === 0) {
    // Rediriger si l'ami n'est pas valide
    header("Location: amis.php");
    exit;
}

// Récupérer le nickname de l'ami
$queryNickname = $connexion->prepare("SELECT nickname FROM Utilisateurs WHERE utilisateur_id = ?");
$queryNickname->bind_param("i", $amiId);
$queryNickname->execute();
$resultNickname = $queryNickname->get_result();
$rowNickname = $resultNickname->fetch_assoc();
$amiNickname = $rowNickname['nickname'];

// Récupérer les messages de la conversation
$queryMessages = $connexion->prepare("
    SELECT Utilisateurs.nickname, Messages.message
    FROM Messages
    JOIN Utilisateurs ON Messages.utilisateur_id = Utilisateurs.utilisateur_id
    WHERE (Messages.utilisateur_id = ? AND Messages.ami_id = ?)
        OR (Messages.utilisateur_id = ? AND Messages.ami_id = ?)
    ORDER BY Messages.timestamp ASC
");
$queryMessages->bind_param("iiii", $_SESSION["utilisateur_id"], $amiId, $amiId, $_SESSION["utilisateur_id"]);
$queryMessages->execute();
$resultMessages = $queryMessages->get_result();

// Traitement de l'envoi de message
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["message"])) {
        $message = $_POST["message"];

        // Insérer le nouveau message dans la base de données
        $queryInsert = $connexion->prepare("
            INSERT INTO Messages (utilisateur_id, ami_id, message, timestamp)
            VALUES (?, ?, ?, NOW())
        ");
        $queryInsert->bind_param("iis", $_SESSION["utilisateur_id"], $amiId, $message);
        $queryInsert->execute();
    }
}

// Fermer la connexion
$queryAmi->close();
$queryNickname->close();
$queryMessages->close();
$connexion->close();
?>

<!-- ... Autres balises HTML ... -->

<form id="messageForm" method="POST">
    <label for="message">Nouveau message:</label>
    <input type="text" id="message" name="message" required>
    <button type="submit">Envoyer</button>
</form>

<!-- ... Autres balises HTML ... -->

<?php include('footer.php'); ?>
