<?php
session_start();

// Vérification des données saisies
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nickname = $_POST["nickname"];
    $mot_de_passe = $_POST["password"];

    // Inclure le fichier de connexion à la base de données
    include('connexion.php');

    // Vérifier les données dans la base de données
    $query = $connexion->prepare("SELECT * FROM Utilisateurs WHERE nickname = ? AND mot_de_passe = ?");
    $query->bind_param("ss", $nickname, $mot_de_passe);
    $query->execute();
    $result = $query->get_result();

    // Si les données sont correctes, rediriger vers la page d'accueil
    if ($result->num_rows === 1) {
        $utilisateur = $result->fetch_assoc();
        $_SESSION["utilisateur_id"] = $utilisateur["utilisateur_id"];
        header("Location: PageLoc.php");
        exit;
    } else {
        $error = "Identifiant ou mot de passe incorrect";
    
    }

    // Fermer la connexion
    $query->close();
    $connexion->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Page de connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <h1 class="titre">Se connecter</h1>
    <?php if (isset($error)) { ?>
        <p><?php echo $error; ?></p>
    <?php } ?>

    <div style="right : 0px; top: 0px; position: absolute">
        <img src="img/Forme.png" >
    </div>

   <div class="logo"> <?php include('img/Logo.svg');?> </div>

    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <input type="text" id="nickname" name="nickname" placeholder="Username" required><br><br> 
        <input type="password" id="password" name="password" placeholder="Mot de passe"  required><br><br>

        <p class="mdp">Mot de passe oublié ?</p>

        <button type="submit" id="connexion"><p class="para"> Se connecter</p> </button>

        <a href="Inscription.php" style="text-decoration: none"><p class="creation">Créer un nouveau compte</p></a>

    </form>
</body>
</html>

<style>

.logo {
 text-align: center;
 z-index: 2;
position: relative;
margin-bottom: 50px;
}

.titre {
 color: var(--Vert-secondaire, #1F8684);
 margin-top: 55px;
text-align: center;
font-family: 'Poppins', sans-serif;
font-size: 30px;
font-style: normal;
font-weight: 700;
line-height: normal;
z-index: 2;
position: relative;
}

.error {
    position: absolute;
    text-align: center;
    bottom: 350px;
    left: 70px;
    margin: auto;
     }

.para {
    text-align: center;
    margin: auto;
    padding-top: 8px;
    padding-bottom: 8px;
    color: #FFF;
    font-family: 'Poppins', sans-serif;
    font-size: 20px;
    font-style: normal;
    font-weight: 700;
    line-height: normal;
}

.mdp {

color: #1F8684;
text-align: center;
font-family: 'Poppins', sans-serif;
font-size: 16px;
font-style: normal;
font-weight: 600;
line-height: normal;
margin-top: 5px;
margin-bottom: 35px;
}

.creation {

color: #494949;
text-align: center;
font-family: 'Poppins' , sans-serif;
font-size: 16px;
font-style: normal;
font-weight: 600;
line-height: normal;
margin-top: 30px;

}

#nickname {
    display: flex;
    width: 270px;
    padding: 20px 35px 20px 20px;
    align-items: center;
    gap: 10px;
    z-index: 2;
    border-radius: 10px;
    border: none;
    background-color : #F0F0F0;
    margin: auto;
    position: relative;
    font-family: 'Montserrat', sans-serif;
    font-size: 16px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
}

#password {
    display: flex;
    width: 270px;
    padding: 20px 35px 20px 20px;
    align-items: center;
    gap: 10px;
    z-index: 2;
    border-radius: 10px;
    border: none;
    background-color : #F0F0F0;
    margin: auto;
    position: relative;
    font-family: 'Montserrat', sans-serif;
    font-size: 16px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
    }

#connexion {
    display: flex;
    width: 290px;
    padding: 8px 35px 8px 20px;
    align-items: center;
    gap: 10px;
    z-index: 2;
    border-radius: 10px;
    border: none;
    background-color: #1F8684;
    box-shadow: 0px 10px 20px 0px rgba(44, 184, 143, 0.38);
    margin: auto;
    position: relative;
    text-align: center;
}

input#nickname:focus, input#password:focus {
    border: 2px solid #1F8684;
    background-color: #F0F0F0;
    /* Autres styles personnalisés... */
}



</style>