<?php
// Définir le titre de la page
$pageTitle = "Ajout d'amis";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Paramètres</title>
    <!-- Ajoutez des liens CDN pour les styles CSS si nécessaire -->
    <?php include('header.php'); ?>
    <!-- Inclure la bibliothèque QR Code -->
    <?php include('phpqrcode/qrlib.php'); ?>
</head>
<body>
    
    <form method="GET" id="formFind" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label id="FindAmi" for="search">Trouver un ami avec son @</label>
        <input type="text" id="search" placeholder="Nom d'utilisateur" name="search" required>
        <button type="submit" value="Rechercher"  id="btnFind">Envoyer une demande d'ami</button>
    </form>

    <?php
    // Inclure le fichier de connexion à la base de données
    include('connexion.php');

    // Démarrer la session en premier
    session_start();

    // Récupérer l'ID de l'utilisateur à partir de la session
    $utilisateur_id = $_SESSION["utilisateur_id"];

    // Vérifier si une recherche a été effectuée
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $searchValue = "%$search%";
        
        // Assurez-vous que $connexion est défini avant d'appeler prepare()
        if ($connexion) {
            // Requête pour rechercher des utilisateurs par nickname
            $query = $connexion->prepare("SELECT utilisateur_id, nickname FROM Utilisateurs WHERE utilisateur_id <> ? AND nickname LIKE ?");
            $query->bind_param("is", $utilisateur_id, $searchValue);

            // ...

            $query->execute();
            $result = $query->get_result();

            // Afficher la liste des utilisateurs avec des boutons d'envoi de demande
            while ($row = $result->fetch_assoc()) {
                $svgPath = 'img/Add.svg';
                echo "<div class='demande-link'>";
                echo "<span>{$row['nickname']}</span>"; // Afficher le nickname en dehors du lien
            
                // Afficher le lien pour envoyer la demande d'ami avec l'image SVG
                echo "<a href='envoyer_demande.php?ami_id={$row['utilisateur_id']}'>";
                echo "<img src='{$svgPath}' alt='Envoyer une demande' width='26' height='26'>";
                echo "</a>";
            
                echo "</div>";
            }
            
            // Fermer la connexion
            $query->close();
        } else {
            // Gérer le cas où la connexion à la base de données n'est pas établie
            echo "Erreur de connexion à la base de données.";
        }

        // Fermer la connexion en dehors de la condition pour s'assurer qu'elle est fermée même en cas d'erreur
        $connexion->close();
    }
    ?>

    <h2 class="TitleQR" >QR Code</h2>

<div class="FondVert">


<?php
    // Inclure le fichier de connexion à la base de données
    include('connexion.php');

    // Démarrer la session en premier
    session_start();

    // Récupérer l'ID de l'utilisateur à partir de la session
    $utilisateur_id = $_SESSION["utilisateur_id"];

    // Récupérer les informations de l'utilisateur connecté
    $query_user = $connexion->prepare("SELECT * FROM Utilisateurs WHERE utilisateur_id = ?");
    $query_user->bind_param("i", $utilisateur_id);
    $query_user->execute();
    $result_user = $query_user->get_result();

    // Vérifier si l'utilisateur existe
    if ($result_user->num_rows > 0) {
        $user = $result_user->fetch_assoc();

        // Afficher la photo de profil de l'utilisateur connecté
        echo '<img src="' . $user['photo_profil'] . '" alt="Photo de profil" class="profile-photo">';
        //afficher le nickname de l'utilisateur connecté
        echo "<p class='NicknameQR'>@" . $user['nickname'] . "</p>";
    } else {
        // Gérer le cas où l'utilisateur n'est pas trouvé
        echo "Utilisateur non trouvé.";
    }

    $query_user->close();
    ?>

<div>
    <?php
    // Générer le contenu du QR code
    $profileURL = "https://sharemyloc.chatonnay.com/PageProfil.php?utilisateur_id=" . $utilisateur_id;
    $qrContent = "BEGIN:VCARD\nVERSION:3.0\nFN:" . $utilisateur_id . "\nURL:" . $profileURL . "\nEND:VCARD";

    // Définir le chemin pour sauvegarder le QR code
    $qrImagePath = 'qrcodes/' . $utilisateur_id . '_qrcode.png';

    // Générer le QR code
    QRcode::png($qrContent, $qrImagePath, 'H', 8, 2);

    // Afficher l'image du QR code
    echo '<img src="' . $qrImagePath . '" alt="QR Code" class="QRCode">';
    ?>
</div>

</div>

<!-- Ajouter la phrase pour ouvrir l'appareil photo -->
<p class="OuvrirCamera" id="startCamera">Scanner le QR code d'un ami</p>

<!-- Ajouter un script JavaScript pour ouvrir l'appareil photo -->
<script>
document.getElementById('startCamera').addEventListener('click', function() {
    // Demander à l'utilisateur l'accès à la caméra
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function(stream) {
            // Vous pouvez ajouter ici la logique pour traiter le flux vidéo
            console.log('Caméra ouverte avec succès');
        })
        .catch(function(error) {
            console.error('Erreur lors de l\'ouverture de la caméra :', error);
        });
});
</script>



</body>
</html>

<?php include('footer.php'); ?>

<style>
#FindAmi {
    color: #1F8684;
    font-family: 'Poppins', sans-serif;
    font-size: 16px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    margin-left: 30px;
    margin-bottom: 20px;
}

.TitleQR {
    color: #1F8684;
    font-family: 'Poppins', sans-serif;
    font-size: 16px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    margin-left: 30px;
    margin-bottom: 20px;
    margin-top: 35px;
}

#formFind {
    display: flex;
    flex-direction: column;
    justify-content: center;
    margin-top: 20px;
    margin-bottom: 20px;
}

input#search{
    width: 278px;
    background-color: #FFF;
    color: #000;
    border: #174949 1px solid;
    border-radius: 6px;
    padding: 10px;
    margin:auto;
    font-family: 'Poppins' ,sans-serif;
    font-size: 16px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.20), 0px 0.1px 0.3px 0px rgba(0, 0, 0, 0.10);
    margin-bottom: 20px;
}

#btnFind {
    width: 300px;
    background-color: #1F8684;
    color: #FFF;
    border: none;
    border-radius: 6px;
    padding: 13px;
    cursor: pointer;
    margin:auto;
    color: #FFF;
    font-family: 'Poppins' ,sans-serif;
    font-size: 16px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.20), 0px 0.1px 0.3px 0px rgba(0, 0, 0, 0.10);
}

.QRCode {

    width: 60%;
    display: flex;
    margin: auto;
    padding-top: 5px;
}

.DemandeAmi {
    color: black;
    font-family: 'Poppins', sans-serif;
    font-size: 16px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
   text-align: center;
}

.demande-link {
    display: grid;
    grid-template-columns: auto auto 1fr auto; /* 1fr pour occuper l'espace restant */
    align-items: center;
    text-decoration: none;
    color: black;
    font-family: 'Poppins', sans-serif;
    font-size: 16px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
    margin-bottom: 10px;
    margin-left: 10%;
}

.demande-link span {
    grid-column: 1 / span 3; /* Prend deux colonnes pour le nickname */
}

.demande-link img {
    margin-top: 5px; /* Ajustez la marge supérieure selon vos besoins */
    grid-column: 4; /* Colonne pour l'image SVG */
    padding-right:50px;
}

.FondVert {
    background-color: #2CB88F;
    border-radius: 12px;
    width: 290px;
    height: 300px;
    margin: auto;
    margin-top:40px
}

.profile-photo {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    top: -25px;
    position: relative;
    right: -120px;
    border: solid 3px #2CB88F;

}

.NicknameQR {
    color: #FFF;
    font-family: 'Poppins', sans-serif;
    font-size: 16px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
    text-align: center;
    margin-top: -15px;
}

.OuvrirCamera {
text-align: center;
color: #1E1E1E;
font-family: 'Poppins', sans-serif;
font-size: 15px;
font-style: normal;
font-weight: 400;
line-height: normal;
text-decoration: underline;
margin-bottom: 120px;
}

</style>