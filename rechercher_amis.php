<?php
session_start();


// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: index.php");
    exit;
}

// Inclure le fichier de connexion à la base de données
include('connexion.php');

// Vérifier si le formulaire de recherche a été soumis
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["q"])) {
    // Récupérer le terme de recherche
   // ...

// Récupérer le terme de recherche
$search_term = '%' . $_GET["q"] . '%';

// Récupérer la liste des amis de l'utilisateur
$query = $connexion->prepare("
    SELECT Utilisateurs.utilisateur_id, Utilisateurs.nickname, Utilisateurs.photo_profil
    FROM Amis
    JOIN Utilisateurs ON Amis.ami_id = Utilisateurs.utilisateur_id
    WHERE Amis.utilisateur_id = ? AND Amis.statut = 'accepte'
      AND Utilisateurs.nickname LIKE ?
");

// Vérifier si la préparation de la requête a réussi
if ($query === false) {
    die('Erreur de préparation de la requête SQL: ' . $connexion->error);
}

// Utiliser bind_param avec des variables temporaires
$user_id = $_SESSION["utilisateur_id"];
$query->bind_param("is", $user_id, $search_term);
$query->execute();
$result = $query->get_result();


// ...

    // Afficher les résultats de la recherche
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $amiId = $row['utilisateur_id'];
            $nickname = $row['nickname'];
            $photo_profil = $row['photo_profil'];

            echo "<p><img src='$photo_profil' alt='Photo de profil' style='border-radius: 50%; width: 50px; height: 50px; margin-right: 10px;'> <a href='discussion.php?ami_id=$amiId' id='PPnickname'>$nickname</a></p>";
        }
    } else {
        echo "<p class='amis0'>Aucun ami trouvé. L'ami est introuvable.</p>";
    }

    // Fermer la connexion
    $query->close();
    $connexion->close();

    // Terminer le script
    exit;
}

// ...

// Si le formulaire n'a pas été soumis ou si aucune recherche n'a été effectuée,
// afficher la liste complète des amis de l'utilisateur
if ($_SERVER["REQUEST_METHOD"] !== "GET" || !isset($_GET["q"])) {
    // Récupérer la liste des amis de l'utilisateur
    $query = $connexion->prepare("
        SELECT Amis.utilisateur_id, Utilisateurs.nickname, Utilisateurs.photo_profil
        FROM Amis
        JOIN Utilisateurs ON Amis.ami_id = Utilisateurs.utilisateur_id
        WHERE Amis.utilisateur_id = ? AND Amis.statut = 'accepte'
    ");

    // Vérifier si la préparation de la requête a réussi
    if ($query === false) {
        die('Erreur de préparation de la requête SQL: ' . $connexion->error);
    }

    $query->bind_param("i", $_SESSION["utilisateur_id"]);
    $query->execute();
    $result = $query->get_result();

    // Afficher la liste des amis avec des liens vers les discussions
    while ($row = $result->fetch_assoc()) {
        $amiId = $row['utilisateur_id'];
        $nickname = $row['nickname'];
        $photo_profil = $row['photo_profil'];

        echo "<p><img src='$photo_profil' alt='Photo de profil' 
        style='border-radius: 50%; width: 50px; height: 50px; margin-right: 10px;'> <a href='discussion.php?ami_id=$amiId'>$nickname</a></p>";
    }

    // Fermer la connexion
    $query->close();
    $connexion->close();

    exit;  // Terminer le script
}

// ...

// Fermer la connexion
$query->close();
$connexion->close();
?>


<?php include('footer.php'); ?>

<style>
 
 .amis0 {
    color: #666;
    text-decoration: none;
    font-family: 'Poppins', sans-serif;
    font-size: 18px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
    margin: auto;
    justify-content: center;
}


</style>
