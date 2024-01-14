<?php
// Définir le titre de la page
$pageTitle = "Messages";
?>

<?php include('header.php'); ?>

<p class='disc'>Mes discussions</p>

<?php
// Inclure le fichier de connexion à la base de données
include('connexion.php');

// Vérifier si la session "utilisateur_id" est définie
session_start();
if (isset($_SESSION["utilisateur_id"])) {
    // Récupérer l'identifiant de l'utilisateur à partir de la session
    $utilisateur_id = $_SESSION['utilisateur_id'];

    // Récupérer les amis avec lesquels l'utilisateur a eu une conversation
    $query = $connexion->prepare("
    SELECT U.utilisateur_id AS ami_id, U.nickname, U.photo_profil, M.message
    FROM Utilisateurs U
    LEFT JOIN (
        SELECT id_auteur, MAX(id) AS last_message_id
        FROM messages
        WHERE id_destinataire = ?
        GROUP BY id_auteur
    ) AS LastMessages ON (U.utilisateur_id = LastMessages.id_auteur)
    LEFT JOIN messages M ON (LastMessages.last_message_id = M.id)
    WHERE U.utilisateur_id != ?
    AND LastMessages.last_message_id IS NOT NULL
");

if ($query) {
    $query->bind_param("ii", $utilisateur_id, $utilisateur_id);
        $query->execute();
        $result = $query->get_result();
    
        // Vérifier si l'utilisateur a des conversations
        if ($result->num_rows == 0) {
         
            echo "<p class='discussion0'>Vous n'avez pas encore de conversation.</p>";
        
        } else {
            // Afficher les informations des conversations de l'ami
            while ($row = $result->fetch_assoc()) {
                $ami_id = $row['ami_id'];
                $nickname = $row['nickname'];
                $photo_profil = $row['photo_profil'];
                $last_message = $row['message'];
    
                // Afficher les informations sur l'ami et le dernier message
                echo "<div class='conversation'>";
                echo "<a href='message.php?ami_id=$ami_id'>";
                    echo "<div class='conversation-flex'>";
                    echo "<img src='$photo_profil' alt='Photo de profil' class='PPAmi'>";
                        echo "<div class='conversation-msg'>";
                            echo "<p class='nicknamecss'>$nickname</p>";
                            echo "<p class='messagecss'>$last_message</p>";
                        echo "</div>";
                        echo "<img id='SendSvg' src='img/Send.svg'>";
                    echo "</div>";
                    echo "</a>";
                echo "</div>";
            }
        }
    
        $query->close();
    } else {
        echo "Erreur dans la préparation de la requête.";
    }
} else {
    echo "Identifiant d'utilisateur non trouvé.";
}

$connexion->close();
?>



<button id="newDisc" onclick="window.location.href='listeAmis.php'">
    <p>Nouvelle discussion</p>
    <img src="img/newDisc.svg">
</button>

<?php include('footer.php'); ?>

<style>
.PPAmi {
    width: 20px;
    height: 20px;
    border-radius: 50%;
}

.conversation {
    padding-top: 15px;
    padding-bottom: 15px;
    margin-left: 10px;
    border-bottom: 1px solid #174949;
    width: 95%;
}

.conversation-flex {
  display: flex;
}

.conversation:last-child {
    margin-bottom: 100px;
}

.conversation-msg {
    display: flex;
    flex-direction: column;
    
}

.nicknamecss{
    margin: 0;
    color: #666;
    font-family: 'Poppins', sans-serif;
    font-size: 18px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
    vertical-align: middle;
    margin: auto;
    margin-left: 15px;
    margin-top: 3px;
}

.messagecss{
    color: #666;
font-family: 'Montserrat', sans-serif;
font-size: 10px;
font-style: normal;
font-weight: 400;
line-height: normal;
margin-left: 15px;
margin-top: 5px;
}

#SendSvg {
    width: 31px;
    height: 31px;
    margin-top: 5px;
    position: absolute;
    right: 15px;
}

.conversation a {
    text-decoration: none;
    color: #3498db;
}

.conversation img {
    width:46px;
    height:46px;
    margin-right: 10px;
}

.discussion0 {
    color: #666;
    text-decoration: none;
    font-family: 'Poppins', sans-serif;
    font-size: 18px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
    margin: auto;
    justify-content: center;
    display: flex;
    margin-top:60px;
    margin-bottom:20px;
}


.disc {
    
        margin-top: 25px;
        margin-left: 15px;
        font-family: 'Poppins', sans-serif;
        font-size: 20px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        color : #174949;
}

#newDisc {
margin: auto;
margin-top: 30px;
border: 2px solid #174949;
display: flex;
gap: 10px;
background-color: white;
border-radius: 6px;
cursor: pointer;

}

#newDisc img {
    width: 25px;
    height: 25px;
    padding-top: 8px;
}

#newDisc p {
       color: #174949;
        font-family: 'Poppins SemiBold', sans-serif;
        font-size: 13px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;   
}

</style>
