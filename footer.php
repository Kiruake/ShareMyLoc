<?php
session_start();

// Inclure le fichier de connexion à la base de données
include('connexion.php');

// Récupérer le chemin de l'image de profil de l'utilisateur connecté
$query = $connexion->prepare("SELECT photo_profil FROM Utilisateurs WHERE utilisateur_id = ?");
$query->bind_param("i", $_SESSION["utilisateur_id"]);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

// Fermer la connexion
$query->close();
$connexion->close();
?>

<footer class="footer">
    <nav>
        <ul class="icon-list">
            <li class="icon-item"><a href="AllConversations.php"><svg class="icon"><?php include('img/IconMessages.svg'); ?></svg></a></li>
            <li class="icon-item"><a href="listeAmis.php"><svg class="icon"><?php include('img/IconAmis.svg'); ?></svg></a></li>
            <li class="icon-item icon-loc"><a href="PageLoc.php "><svg ><?php include('img/IconLoc.svg'); ?></svg></a></li>
            <li class="icon-item"><a href="Parametres.php"><svg class="icon"><?php include('img/IconSettings.svg'); ?></svg></a></li>
            <li class="icon-item"><a href="PageProfil.php"><img src="<?php echo $user['photo_profil']; ?>" alt="Photo de profil" class="iconPP"></a></li>
        </ul>
    </nav>
</footer>

<style>
    .footer {
        background-color: #2CB88F;
        position: fixed;
        z-index: 2;
        bottom: 0;
        left:0;
        width: 100%;
        height: 70px;
    }

    .icon-list {
        display: flex;
        justify-content: center;
        padding: 1rem;
        list-style: none;
        margin: 0;
    }

    .icon-item {
        flex: 1;
        text-align: center;
        position: relative;
    }

    .icon-loc {
        position: relative;
        top: -40px; /* Ajustez cette valeur selon votre besoin */
        width: 73px;
        height: 73px;
    }

    .icon {
        width: 40px;
        height: 32px;
    }

    .iconPP {
        width: 32px;
        height: 32px;
        border-radius: 50%;
    }
</style>
