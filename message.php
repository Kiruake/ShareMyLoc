<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Inclure le fichier de connexion à la base de données
include('connexion.php');

// Vérifier si la session "utilisateur_id" est définie
if (isset($_SESSION["utilisateur_id"])) {
    // Récupérer l'identifiant de l'utilisateur à partir de la session
    $utilisateur_id = $_SESSION['utilisateur_id'];

    // Vérifier si l'identifiant de l'utilisateur à contacter est présent dans l'URL
    if (isset($_GET['ami_id'])) {
        $destinataire_id = $_GET['ami_id'];

        // Récupérer les informations de l'utilisateur destinataire
        $recupUser = $connexion->prepare('SELECT * FROM Utilisateurs WHERE utilisateur_id = ?');

        if ($recupUser) {
            $recupUser->bind_param("i", $destinataire_id);
            $recupUser->execute();

            $recupUser->store_result();

            // Vérifier si l'utilisateur destinataire existe
            if ($recupUser->num_rows > 0) {
                // Vérifier si le formulaire a été soumis
                if (isset($_POST['envoyer'])) {
                    // Récupérer le message depuis le formulaire
                    $message = htmlspecialchars($_POST['message']);

                    // Vérifier si le message n'est pas vide
                    if (!empty($message)) {
                        // Insérer le message dans la base de données
                        $insererMessage = $connexion->prepare('INSERT INTO messages (message, id_destinataire, id_auteur) VALUES (?, ?, ?)');
                        $insererMessage->bind_param('sii', $message, $destinataire_id, $utilisateur_id);

                        // Exécuter la requête
                        $insererMessage->execute();
                    } else {
                        echo "Le message ne peut pas être vide.";
                    }
                }
            } else {
                echo "Utilisateur destinataire non trouvé.";
            }

            $recupUser->close();
        } else {
            echo "Erreur dans la préparation de la requête.";
        }
    } else {
        echo "Identifiant d'utilisateur destinataire non trouvé dans l'URL.";
    }
} else {
    echo "Identifiant d'utilisateur non trouvé.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Message privé</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

</head>
<body>

<?php
    if (isset($_SESSION["utilisateur_id"])) {
        $utilisateur_id = $_SESSION['utilisateur_id'];

        // Récupérer les informations de l'ami avec lequel l'utilisateur discute
        if (isset($_GET['ami_id'])) {
            $ami_id = $_GET['ami_id'];

            $queryAmi = $connexion->prepare("
                SELECT nickname, photo_profil
                FROM Utilisateurs
                WHERE utilisateur_id = ?
            ");

            if ($queryAmi) {
                $queryAmi->bind_param("i", $ami_id);
                $queryAmi->execute();
                $resultAmi = $queryAmi->get_result();

                if ($resultAmi->num_rows > 0) {
                    $rowAmi = $resultAmi->fetch_assoc();
                    $amiNickname = $rowAmi['nickname'];
                    $amiPhotoProfil = $rowAmi['photo_profil'];

                    // Afficher le header avec les informations de l'ami et le bouton de retour
                    echo "<header>";
                    echo "<button class='back-button' onclick='window.location.href=\"AllConversations.php\"'>";
                    echo "<img src='img/Back.svg' alt='Retour'>";
                    echo "</button>";
                    echo "<div class='content'>";
                    echo "<img src='$amiPhotoProfil' alt='Photo de profil de l\'ami' class='PPAmiHeader'>";
                    echo "<h2>$amiNickname</h2>";
                    echo "</div>";
                    echo "</header>";
                }

                $queryAmi->close();
            }
        }
    }
?>



    <section id="messages">
        <?php
            //afficher les messages de la conversation entre destinataire_id et auteur_id

            // Récupérer les messages de la conversation entre l'utilisateur connecté et l'utilisateur destinataire
            $query = $connexion->prepare("SELECT * FROM messages WHERE (id_destinataire = ? AND id_auteur = ?) OR (id_destinataire = ? AND id_auteur = ?)");

            // Vérifier si la préparation de la requête a échoué
            if (!$query) {
                die("Erreur de préparation de la requête : " . $connexion->error);
            }

            $query->bind_param("iiii", $utilisateur_id, $destinataire_id, $destinataire_id, $utilisateur_id);
            $query->execute();

            // Vérifier si l'exécution de la requête a échoué
            if (!$query) {
                die("Erreur d'exécution de la requête : " . $connexion->error);
            }

            $result = $query->get_result();

            // Afficher les messages
            while ($row = $result->fetch_assoc()) {
                $message = $row['message'];
                $auteur_id = $row['id_auteur'];

                // Récupérer les informations de l'auteur du message
                $query2 = $connexion->prepare("SELECT nickname, photo_profil FROM Utilisateurs WHERE utilisateur_id = ?");
                $query2->bind_param("i", $auteur_id);
                $query2->execute();

                // Vérifier si la préparation de la deuxième requête a échoué
                if (!$query2) {
                    die("Erreur de préparation de la requête : " . $connexion->error);
                }

                $result2 = $query2->get_result();
                $row2 = $result2->fetch_assoc();

                $nickname = $row2['nickname'];
                $photo_profil = $row2['photo_profil'];

                // Ajouter une classe CSS en fonction de l'auteur du message
                $messageClass = ($auteur_id == $utilisateur_id) ? 'message-auteur' : 'message-destinataire';

                // Afficher le message avec le nom de l'auteur et la photo de profil
                echo "<div class='message $messageClass'>";
                echo "<div class='message-content'>";
                if ($auteur_id == $utilisateur_id) {
                    // Inverser l'ordre pour l'auteur
                    echo "<p>$message</p>";
                    echo "<img src='$photo_profil' alt='Photo de profil' class='PPMessage'>";
                } else {
                    echo "<img src='$photo_profil' alt='Photo de profil' class='PPMessage'>";
                    echo "<p>$message</p>";
                }
                echo "</div>";
                echo "</div>";

                // Fermer la connexion pour la deuxième requête
                $query2->close();
            }

            // Fermer la connexion pour la première requête
            $query->close();
            $connexion->close();
        ?>
    </section>

    <form method="POST" action="">
        <div class="message-input-container">
            <textarea name="message" placeholder="Votre Message" id="SendMessage" required></textarea>
            <button type="submit" name="envoyer" class="send-button">
                <img src="img/Envoyer.svg" alt="Envoyer">
            </button>
        </div>
    </form>

    <style>

body {
    margin: 0;
}

.back-button {
    background: none;
    border: none;
    cursor: pointer;
    margin-right: 10px;
}

.back-button img {
    width: 20px;
    height: 20px;
    margin-right: 10px;
}

.PPAmiHeader {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
}

header {
    display: flex;
    align-items: center;
    background-color: #2CB88F;
    color: #FFF;
    padding: 10px;
    position: fixed;
    top: 0;
    width: 100%;
    gap: 5%;
}

header h2 {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-size: 18px;
    font-weight: 500;
}

#messages {
    margin-top: 100px;
}

.message-input-container {
    display: flex;
    align-items: center;
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background: #FFF;
    padding: 7px;
    border-top: solid 5px #2CB88F;
}

#SendMessage {
    border-radius: 16px 0 0 16px;
    background: #FFF;
    width: calc(100% - 50px); /* Largeur de la zone de texte moins la largeur du bouton */
    height: 50px;
    border: none;
    padding-left: 10px;
    font-family: 'Montserrat', sans-serif;
    font-size: 18px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
}

#SendMessage::placeholder {
    line-height: 50px;
    vertical-align: middle;
    color: #999;
}

.send-button {
    background: transparent;
    border: none;
    border-radius: 0 16px 16px 0;
    width: 50px; /* Largeur du bouton */
    height: 50px;
    cursor: pointer;
    padding-right: 50px;
}

.send-button img {
    width: 20px; /* Ajustez la taille de l'icône selon vos besoins */
    height: 20px;
    display: block;
    margin: auto;
}

.PPMessage {
    width: 20px;
    height: 20px;
    border-radius: 50%;
}

.message {
    padding: 10px;
    margin: 10px;
    border-radius: 10px;
    max-width: 310px;
}

.message:last-child {
    margin-bottom: 100px;
}

.message-content {
    display: flex;
    align-items: center;
    gap: 10px;
}

.message-auteur {
    text-align: right;
    border-radius: 16px 16px 4px 16px;
    background: #1F8684; /* Vert clair pour l'auteur */
    color: #FFF;
    display: flex;
    flex-direction: row-reverse;
    font-family: 'Montserrat', sans-serif;
    font-size: 14px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
    margin-top: 25px;
    margin-bottom: 25px;
    width: fit-content;
    margin-left: auto; /* Ajout de cette ligne pour aligner le contenu à droite */
}

.message-destinataire {
    text-align: left;
    border-radius: 16px 16px 16px 4px;
    background: rgba(44, 184, 143, 0.25);
    font-family: 'Montserrat', sans-serif;
    font-size: 14px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
    margin-top: 25px;
    margin-bottom: 25px;
    width: fit-content;
}

.message p {
    margin: 0;
    min-width: 50px;
    max-width: 400px;
    word-wrap: break-word;
}

.content {
    display: flex;
    align-items: center;
    gap: 10px;
}

    </style>
</body>
</html>
