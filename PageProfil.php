<?php
// Définir le titre de la page
$pageTitle = "Profil";
?>

<?php
session_start();

// Inclure le fichier de connexion à la base de données
include('connexion.php');

// Vérifier si la clé utilisateur_id existe dans la session
if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: index.php");
    exit;
}

// Récupérer les informations de l'utilisateur
$query = $connexion->prepare("SELECT nickname, photo_profil, banniere FROM Utilisateurs WHERE utilisateur_id = ?");
$query->bind_param("i", $_SESSION["utilisateur_id"]);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();

// Fermer la connexion
$query->close();
$connexion->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <?php include('header.php'); ?>
</head>
<body>

<form id="bannerForm" action="update_banniere.php" method="post" class="Bannière" enctype="multipart/form-data">
    <div style="position: relative;">
        <?php if (!empty($row['banniere'])): ?>
            <img src="<?php echo $row['banniere']; ?>" alt="Bannière de profil" class="Bannière" style="width: 100%;">
        <?php else: ?>
            <p>Aucune bannière disponible.</p>
        <?php endif; ?>
        <label for="new_banner" class="upload-icon" style="position: absolute; top: 10px; right: 15px;">
            <img src="img/Edit.svg" alt="Edit" width="36" height="36">
        </label>
    </div>
    <input type="file" id="new_banner" name="new_banner" accept="image/*" style="display: none;">
</form>

<script>
    document.getElementById('new_banner').addEventListener('change', function() {
        document.getElementById('bannerForm').submit();
    });
</script>

    <!-- Afficher la photo de profil -->
    <div style="text-align: center; margin-top:-20%;">
        <?php if (!empty($row['photo_profil'])): ?>
            <form id="profileForm" action="update_profile.php" method="post" enctype="multipart/form-data">
            <div style="position: relative; display: inline-block;">
                <img src="<?php echo $row['photo_profil']; ?>" alt="Photo de profil" class="PP">
                <label for="new_profile_picture" class="upload-icon" style="position: absolute; top: 10px; right: 15px;">
                    <img src="img/Edit.svg" alt="Edit" width="36" height="36">
                </label>
            </div>
            <input type="file" id="new_profile_picture" name="new_profile_picture" accept="image/*" style="display: none;">
        </form>

        <script>
            document.getElementById('new_profile_picture').addEventListener('change', function() {
                document.getElementById('profileForm').submit();
            });
        </script>
        <?php else: ?>
            <p>Aucune photo de profil disponible.</p>
        <?php endif; ?>
        
        <h1 class="nickname"><?php echo $row['nickname']; ?></h1>
    </div>

    <a href='#' id='DemandeAmis' onclick='toggleDemandesAmis()'><p class='Pami'>Voir mes demandes d'amis</p></a>

    <div id="demandesAmis" style="display: none;">
        <?php
        // Récupérer les demandes d'amitié en attente
        include('connexion.php');

        $queryDemandes = $connexion->prepare("SELECT Amis.id, Utilisateurs.nickname, Utilisateurs.photo_profil FROM Amis JOIN Utilisateurs ON Amis.utilisateur_id = Utilisateurs.utilisateur_id WHERE ami_id = ? AND statut = 'en_attente'");
        $queryDemandes->bind_param("i", $_SESSION["utilisateur_id"]);
        $queryDemandes->execute();
        $resultDemandes = $queryDemandes->get_result();

        // Afficher la liste des demandes d'amitié avec des boutons pour les accepter
        while ($rowDemande = $resultDemandes->fetch_assoc()) {
            $photo_profil_demande = $rowDemande['photo_profil'];
            $nickname_demande = $rowDemande['nickname'];

            echo "<div class='demande-ami'>
                    <img src='$photo_profil_demande' alt='Photo de profil' class='demande-ami-photo'>
                    <p class='demande-ami-nickname'>$nickname_demande</p>
                    <a id='demande-accepter' href='accepter_demande.php?demande_id={$rowDemande['id']}'>Accepter</a>
                  </div>";
        }

        // Fermer la connexion
        $queryDemandes->close();
        ?>
    </div>


    <?php

    $queryAmis = $connexion->prepare("SELECT COUNT(*) AS nbAmis FROM Amis WHERE utilisateur_id = ? AND statut = 'accepte'");
    $queryAmis->bind_param("i", $_SESSION["utilisateur_id"]);
    $queryAmis->execute();
    $resultAmis = $queryAmis->get_result();
    $rowAmis = $resultAmis->fetch_assoc();

    echo "<p class='titleAmis'>Amis (" . $rowAmis['nbAmis'] . ")</p>";

    // Fermer la connexion
    $queryAmis->close();
    ?>

    <div id="photosAmis">
        
<?php
$queryAmis = $connexion->prepare("SELECT Utilisateurs.photo_profil, Utilisateurs.nickname, Amis.ami_id FROM Amis JOIN Utilisateurs ON Amis.ami_id = Utilisateurs.utilisateur_id WHERE Amis.utilisateur_id = ? AND Amis.statut = 'accepte'");
$queryAmis->bind_param("i", $_SESSION["utilisateur_id"]);
$queryAmis->execute();
$resultAmis = $queryAmis->get_result();

// Afficher les photos de profil des amis
while ($rowAmi = $resultAmis->fetch_assoc()) {
    $ami_id = $rowAmi['ami_id'];
    $photo_profil_ami = $rowAmi['photo_profil'];
    $nickname_ami = $rowAmi['nickname'];

    echo "<div class='ami' onclick='redirectToProfil($ami_id)'>
            <img src='$photo_profil_ami' alt='Photo de profil de $nickname_ami' class='ami-photo'>
          </div>";
}

// Fermer la connexion
$queryAmis->close();
?>
</div>

<script>

        function toggleDemandesAmis() {
            var demandesAmisSection = document.getElementById('demandesAmis');
            demandesAmisSection.style.display = (demandesAmisSection.style.display === 'none') ? 'block' : 'none';
        }

    
        function redirectToProfil(ami_id) {
    // Redirection vers la page de profil avec l'ami_id en tant que paramètre d'URL
    window.location.href = 'PageProfil.php?ami_id=' + ami_id;
}

</script>

<?php include('footer.php'); ?>

</body>
</html>
    

<style>

body {
    margin: 0;
}

.PP {
    width: 193px; 
    height: 193px; 
    border-radius: 50%;
}

.Bannière {
    width: 100%;
    height: 145px;
    object-fit: cover;
}

.nickname {

 margin-top: 10px;
 font-size: 36px;
 font-family: 'Poppins', sans-serif;
 font-weight: 400;
 word-wrap: break-word  
}

#DemandeAmis {
    display: flex;
    width: 250px;
    height: 45px;
    border-radius: 6px;
    filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.25));
    background-color: #1F8684;
    text-decoration: none;
    margin: auto;
    margin-top :30px;
 
}

.Pami {
    color: var(--Blanc, #FFF);
    text-align: center;
    font-family: 'Poppins', sans-serif;
    font-size: 16px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    text-decoration: none;
    margin: auto;
}

.demande-ami {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        margin-top: 30px;
        justify-content: center;
    }

    .demande-ami-photo {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .demande-ami-nickname {
        margin: 0;
        font-weight: bold;
    }

    #demande-accepter {
        margin-left: 40px;
        padding: 5px 10px;
        border-radius: 3px;
        background: #2CB88F;
        color: #FFF;
        text-decoration: none;
    }

    #demande-accepter:hover {
        background-color: #174949;
    }

    #photosAmis {
        display: flex;
        flex-wrap: wrap;
        margin-top: 25px;
        gap: 20px;
        margin-left : 25px;
    }

    .ami {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        cursor: pointer;
    }

    .titleAmis {
        margin-top: 40px;
        margin-left: 25px;
        font-family: 'Poppins', sans-serif;
        font-size: 20px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        color : #1F8684;
    }
</style>
