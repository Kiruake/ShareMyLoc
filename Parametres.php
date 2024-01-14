<?php
// Définir le titre de la page
$pageTitle = "Paramètres";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Paramètres</title>
    <!-- Ajoutez des liens CDN pour les styles CSS si nécessaire -->
    <?php include('header.php'); ?>
</head>
<body>


<div>
        <div class="rectangle"></div>  

 <div class="card">


        <?php

        session_start();

        // Inclure le fichier de connexion à la base de données
        include('connexion.php');

        // Vérifier si la clé utilisateur_id existe dans la session
        if (!isset($_SESSION["utilisateur_id"])) {
            header("Location: index.php");
            exit;
        }



        // Récuperer le nickname et la photo de profil de l'utilisateur connecté
        $query = $connexion->prepare("SELECT nickname, photo_profil FROM Utilisateurs WHERE utilisateur_id = ?");
        $query->bind_param("i", $_SESSION["utilisateur_id"]);
        $query->execute();
        $result = $query->get_result();
        $row = $result->fetch_assoc();
        $query->close();

        // Afficher le nickname et la photo de profil de l'utilisateur connecté

        echo '<div style="display:flex; gap:10px; padding-left:25px; padding-top:30px;">';
        echo '<img src="' . $row['photo_profil'] . '" alt="Profile Image" class="profile-image">';
        echo '<h1 class="Nicknamee">' . $row['nickname'] . '</h1>';
        echo '</div>';

        ?>

        <h3 class="title3">Paramètres de profil</h3>

        <ul id="SettingsProfil">
            <li><a href="#">Modifier le profil</a></li>
            <li><a href="Abonnement.php">Abonnement</a></li>
            <li><a href="#">Ajouter un moyen de paiement</a></li>
            <li><a href="#">Aide</a></li>
            <li><a id="deco" href="deconnexion.php">Se déconnecter</a></li>
        </ul>

        <h3 class="title3">Termes et Conditions</h3>

        <ul id="SettingsProfil">
            <li><a href="ML.php">Mentions légales</a></li>
            <li><a href="CGU.php">CGU</a></li>
        </ul>

    </div>
<div>




</body>
</html>
<?php include('footer.php'); ?>

<style>
.rectangle {
    width: 100%;
    height: 150px;
    background-color: #2CB88F;
}

.card {
    position: absolute;
    top: 58%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%; 
    height: 700px; 
    background-color: white;
    border-radius: 16px;
    box-shadow: 0px 2px 16px 0px rgba(75, 75, 75, 0.15);
            overflow-y: auto; /* Ajoute le défilement uniquement à l'intérieur de la carte */
}

#SettingsProfil {
    color: #1E1E1E;
    list-style-type: none;
    font-family: 'Montserrat' , sans-serif;
    font-size: 16px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
    text-decoration: none;
    padding-left: 25px;
    line-height: 3;
}

a {
    color: #1E1E1E;
    text-decoration: none;
}

.title3 {
    color: #174949;
    font-family: 'Poppins' , sans-serif;
    font-size: 16px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    padding-left: 25px;
    margin-top: 30px;
    }

.Nicknamee {
    color: #1E1E1E;
    font-family: 'Montserrat' , sans-serif;
    font-size: 16px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
    }


    .profile-image {
        width: 39px;
        height: 41.079px;
        border-radius: 41.079px;
    }

    body {
            height: 100vh; /* Hauteur maximale de la page des paramètres */
            overflow-y: auto; /* Ajoute le défilement uniquement si nécessaire */
        }


#deco {
    color: #E23744
}

</style>