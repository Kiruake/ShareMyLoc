<?php
// Définir le titre de la page
$pageTitle = "Contacts";
?>

<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: index.php");
    exit;
}


// Inclure le fichier de connexion à la base de données
include('connexion.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste d'amis</title>
    <!-- Ajoutez des liens CDN pour les styles CSS si nécessaire -->
    <?php include('header.php'); ?>
</head>
<body>

<a href='listeUsers.php' id='btnAjouterAmis'><p class='Pami'>Ajouter d'autres amis</p></a>

<form action="#" method="GET" id="formRecherche">
    <input type="text" name="q" placeholder="Rechercher ses amis" id="champRecherche">
    <button type="button" onclick="rechercherAmis()" id="btnRecherche">Rechercher</button>
</form>

<div id="resultatsAmis">
    <?php
    // Afficher la liste des amis par défaut
    include('afficher_amis.php');

    // si il n'a pas d'amis afficher un message 
    if (mysqli_num_rows($result) == 0) {
        echo "<p class='amis0'>Vous n'avez pas encore d'amis.</p>";
    }

    ?>
</div>

<?php include('footer.php'); ?>

<script>
function rechercherAmis() {
    var champRecherche = document.getElementById('champRecherche').value;

    // Envoyer une requête AJAX pour rechercher des amis
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Mettre à jour la section des résultats avec les données reçues
            document.getElementById('resultatsAmis').innerHTML = xhr.responseText;
        }
    };

    // Construire l'URL avec le terme de recherche
    var url = 'rechercher_amis.php?q=' + encodeURIComponent(champRecherche);
    xhr.open('GET', url, true);
    xhr.send();
}
</script>

</body>
</html>


<style>

#btnAjouterAmis {
    display: flex;
    width: 250px;
    height: 58px;
    border-radius: 6px;
    filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.25));
    background-color: #1F8684;
    text-decoration: none;
    margin: auto;
    margin-top :40px;
 
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

#formRecherche {
    display: flex;
    justify-content: center;
    margin-top: 20px;
    margin-bottom: 30px;
}

#champRecherche {
    width: 200px;
    padding: 13px;
    border: 2px solid #1F8684;
    border-radius: 6px 0 0 6px;
    color: rgba(60, 60, 67, 0.60);
    font-family: 'Montserrat', sans-serif;
    font-size: 17px;
    font-style: normal;
    font-weight: 400;
    line-height: 22px;
    letter-spacing: 0.5px;
}

#btnRecherche {
    background-color: #1F8684;
    color: #FFF;
    border: none;
    border-radius: 0 6px 6px 0;
    padding: 10px;
    cursor: pointer;
}

#resultatsAmis {
    display: flex;
    flex-direction: column;
}

#resultatsAmis p {
    display: flex;
    align-items: center;
    margin: 10px;
    border-bottom: 1px solid #174949;
    padding-bottom: 17px;
    padding-left:10px;
}

#resultatsAmis p:last-child {
    display: flex;
    align-items: center;
    margin: 10px;
    padding-bottom: 17px;
    border-bottom: none;
    margin-bottom: 100px;
}

#resultatsAmis img {
    border-radius: 50%;
    margin-right: 10px;
    width: 50px; /* Ajustez la taille de la photo selon vos besoins */
    height: 50px;
}

#PPnickname {
    color: #666;
    text-decoration: none;
    font-family: 'Poppins', sans-serif;
    font-size: 18px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
}

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
