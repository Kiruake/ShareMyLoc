<?php
session_start();

// Inclure le fichier de connexion à la base de données
include('connexion.php');

// Vérifier si la clé utilisateur_id existe dans la session
if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: index.php");
    exit;
}


// Récupérer les coordonnées de géolocalisation de l'utilisateur et le chemin de la photo de profil
$query = $connexion->prepare("SELECT latitude, longitude, photo_profil FROM Utilisateurs WHERE utilisateur_id = ?");
$query->bind_param("i", $_SESSION["utilisateur_id"]);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();
$query->close();

// Récupérer les amis de l'utilisateur depuis la table Ami
$queryFriends = $connexion->prepare("SELECT ami_id FROM Amis WHERE utilisateur_id = ?");
$queryFriends->bind_param("i", $_SESSION["utilisateur_id"]);
$queryFriends->execute();
$resultFriends = $queryFriends->get_result();
$friendIds = $resultFriends->fetch_all(MYSQLI_ASSOC);
$queryFriends->close();

// Récupérer les informations des amis depuis la table Utilisateurs (y compris les coordonnées)
$friendsData = array();
foreach ($friendIds as $friendId) {
    $queryFriendData = $connexion->prepare("SELECT utilisateur_id, nickname, latitude, longitude, photo_profil FROM Utilisateurs WHERE utilisateur_id = ?");
    $queryFriendData->bind_param("i", $friendId['ami_id']);
    $queryFriendData->execute();
    $resultFriendData = $queryFriendData->get_result();
    $friendData = $resultFriendData->fetch_assoc();
    $queryFriendData->close();

    // Ajouter les données de l'ami à la liste
    $friendsData[] = $friendData;
}

// Récupérer les invitations non acceptées de l'utilisateur
$queryInvitations = $connexion->prepare("SELECT id_rendezvous FROM Invitations WHERE id_receiver = ? AND accepted = 0");
$queryInvitations->bind_param("i", $_SESSION["utilisateur_id"]);
$queryInvitations->execute();
$resultInvitations = $queryInvitations->get_result();
$invitationData = $resultInvitations->fetch_all(MYSQLI_ASSOC);
$queryInvitations->close();


// Déclarer les variables avant de les utiliser
$routeControls = [];
$acceptedRouteControls = [];

$routeControls = array_merge($routeControls, $acceptedRouteControls);

// Fonction pour créer le contrôle d'itinéraire accepté
function createAcceptedRouteControl($rendezVousId) {
    // Récupérer les coordonnées et autres informations nécessaires de la base de données

    // Créer et retourner le contrôle d'itinéraire
    return "<script>createRouteControl(startLatLng, endLatLng, routeColor);</script>";
}


// Fonction pour obtenir les informations d'un utilisateur en fonction de son ID
function getUserInfo($userId) {
    global $connexion;

    $query = $connexion->prepare("SELECT * FROM Utilisateurs WHERE utilisateur_id = ?");
    $query->bind_param("i", $userId);
    $query->execute();
    $result = $query->get_result();
    $userInfo = $result->fetch_assoc();
    $query->close();

    return $userInfo;
}

// Fonction pour obtenir les informations de tous les participants à un rendez-vous en fonction de son ID
function getParticipantsInfo($rendezVousId) {
    global $connexion;

    $query = $connexion->prepare("SELECT U.* FROM Utilisateurs U INNER JOIN Utilisateurs_RendezVous UR ON U.utilisateur_id = UR.id_user WHERE UR.id_rendezvous = ?");
    $query->bind_param("i", $rendezVousId);
    $query->execute();
    $result = $query->get_result();
    $participantsInfo = [];

    while ($row = $result->fetch_assoc()) {
        $participantsInfo[] = $row;
    }

    $query->close();

    return $participantsInfo;
}




// Afficher les itinéraires sur la carte pour les invitations non acceptées
$acceptedRouteControls = [];

foreach ($invitationData as $invitation) {
    $rendezVousId = $invitation['id_rendezvous'];

    // Récupérer les informations du créateur de l'invitation
  // Récupérer les informations du créateur de l'invitation
$rendezVousId = $invitation['id_rendezvous'];

$queryCreatorInfo = $connexion->prepare("SELECT U.utilisateur_id, U.nickname, U.photo_profil FROM Utilisateurs U INNER JOIN RendezVous R ON U.utilisateur_id = R.id_createur WHERE R.id_rendezvous = ?");
$queryCreatorInfo->bind_param("i", $rendezVousId);
$queryCreatorInfo->execute();
$resultCreatorInfo = $queryCreatorInfo->get_result();
$creatorInfo = $resultCreatorInfo->fetch_assoc();
$queryCreatorInfo->close();


    // Récupérer les informations des participants
    $participants = getParticipantsInfo($rendezVousId); // Vous devez définir la fonction getParticipantsInfo() pour récupérer les infos des participants

    echo '<div id="invitation-overlay" class="invitation-overlay">';
    echo '<div class="invitation-content">';
    
    // Header avec photo et nickname du créateur
    echo '<div class="invitation-header">';
    echo '<div class="creator-info">';
    echo '<p class="invitation-text">' . $creatorInfo['nickname'] . ' vous propose de se rejoindre</p>'; // Ajout de la phrase "Invité par :"
    echo '<img src="' . $creatorInfo['photo_profil'] . '" alt="Photo du créateur">';
    echo '</div>';
    echo '</div>';
    
    // Participants
    echo '<p class="invitation-text">Participants</p>';
    echo '<div class="participants">';
    foreach ($participants as $participant) {
        echo '<div class="participant">';
        echo '<img src="' . $participant['photo_profil'] . '" alt="Photo du participant">';
        echo '<p>' . $participant['nickname'] . '</p>';
        echo '</div>';
    }
    echo '</div>';
    
    // Boutons d'acceptation et de refus
    echo '<div class="invitation-buttons">';
    echo '<button class="accept-invitation" data-rendezvous-id="' . $rendezVousId . '">Accepter</button>';
    echo '<button class="reject-invitation" data-rendezvous-id="' . $rendezVousId . '">Refuser</button>';
    echo '</div>';
    
    echo '</div>';
    echo '</div>';
    
    $acceptedRouteControls[] = createAcceptedRouteControl($rendezVousId);
}


// Fonction pour enregistrer un itinéraire dans la base de données
function saveRoute($meetingName, $creatorId, $centerLat, $centerLng) {
    

    // Enregistrement de l'itinéraire dans la table RendezVous
    $querySaveRoute = $connexion->prepare("INSERT INTO RendezVous (nom_meeting, id_createur, point_calcule_lat, point_calcule_lng) VALUES (?, ?, ?, ?)");
    $querySaveRoute->bind_param("sidd", $meetingName, $creatorId, $centerLat, $centerLng);
    $querySaveRoute->execute();
    $idRendezVous = $querySaveRoute->insert_id;
    $querySaveRoute->close();

    return $idRendezVous;
}

// Fonction pour enregistrer les participants dans la table Utilisateurs_RendezVous
function saveParticipants($idRendezVous, $participants) {


    // Enregistrement des participants dans la table Utilisateurs_RendezVous
    foreach ($participants as $participantId) {
        $querySaveParticipant = $connexion->prepare("INSERT INTO Utilisateurs_RendezVous (id_user, id_rendezvous, accepted) VALUES (?, ?, NULL)");
        $querySaveParticipant->bind_param("ii", $participantId, $idRendezVous);
        $querySaveParticipant->execute();
        $querySaveParticipant->close();
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte OSM</title>
    <!-- Ajouter Leaflet via CDN -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>
<body>

<?php include('footer.php'); ?>

<div id="map" style="height: 100vh; width: 100vw; position: relative; z-index: 1;"></div>

<!-- Formulaire caché -->
<form id="coordinatesForm" method="post" action="coordinates.php" style="display: none;">
    <input type="hidden" id="hiddenLatitude" name="latitude" value="">
    <input type="hidden" id="hiddenLongitude" name="longitude" value="">
</form>


<script>
    // Récupérer la position géolocalisée de l'utilisateur depuis PHP
    var userLat = <?php echo isset($row['latitude']) ? $row['latitude'] : '0'; ?>;
    var userLng = <?php echo isset($row['longitude']) ? $row['longitude'] : '0'; ?>;
    var userProfileImage = '<?php echo isset($row['photo_profil']) ? $row['photo_profil'] : ''; ?>';

    // Initialiser la carte
    var map = L.map('map').setView([userLat, userLng], 13);
    var routeControl;
    var friendsData = <?php echo json_encode($friendsData); ?>; // Convertir les données PHP en JSON

    // Ajouter la carte OSM de Leaflet
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Créer une icône personnalisée avec la photo de profil de l'utilisateur
    var userProfileIcon = L.divIcon({
        className: 'user-marker',
        iconSize: [40, 40], // Taille du marqueur
        html: '<img src="' + userProfileImage + '" alt="Profile Image">' // Balise image pour la photo de profil
    });

    // Ajouter le marqueur avec l'icône personnalisée
    var marker = L.marker([userLat, userLng], { icon: userProfileIcon }).addTo(map);

    // Afficher les marqueurs avec les photos de profil des amis
    friendsData.forEach(function (friend) {
        var friendLatLng = [friend.latitude, friend.longitude];

        // Créer une icône personnalisée avec la photo de profil de l'ami
        var friendProfileIcon = L.divIcon({
            className: 'friend-marker',
            iconSize: [40, 40], // Taille du marqueur
            html: '<img src="' + friend.photo_profil + '" alt="Friend Image">' // Balise image pour la photo de profil
        });

        // Ajouter le marqueur avec l'icône personnalisée
        var friendMarker = L.marker(friendLatLng, { icon: friendProfileIcon }).addTo(map);
    });

    // Mettre à jour les coordonnées périodiquement
    setInterval(updateCoordinates, 3000); // Met à jour toutes les 3 secondes

    // Mettre à jour les coordonnées de géolocalisation de l'utilisateur
    async function updateCoordinates() {
        // Vérifier si la géolocalisation est prise en charge par le navigateur
        if (navigator.geolocation) {
            // Récupérer les coordonnées de géolocalisation
            navigator.geolocation.getCurrentPosition(async function (position) {
                // Mettre à jour les coordonnées de l'utilisateur
                userLat = position.coords.latitude;
                userLng = position.coords.longitude;

                // Mettre à jour les coordonnées de l'utilisateur dans le formulaire caché
                var hiddenLatitudeInput = document.getElementById('hiddenLatitude');
                var hiddenLongitudeInput = document.getElementById('hiddenLongitude');

                // Vérifier si les éléments existent avant de mettre à jour les valeurs
                if (hiddenLatitudeInput && hiddenLongitudeInput) {
                    // Mettre à jour les coordonnées de l'utilisateur dans le formulaire caché
                    hiddenLatitudeInput.value = userLat;
                    hiddenLongitudeInput.value = userLng;

                    // Utiliser une requête AJAX pour envoyer les données du formulaire sans recharger la page
                    var formData = new FormData(document.getElementById('coordinatesForm'));
                    await fetch('coordinates.php', {
                        method: 'POST',
                        body: formData
                    });

                    // Mettre à jour la position du marqueur
                    marker.setLatLng([userLat, userLng]);
                } else {
                    console.error('Les éléments de formulaire sont introuvables.');
                }
            });
        }
    }

    // Déclarer les variables globales pour les itinéraires
    var routeControlUser;
    var routeControlFriend;


// Fonction pour mettre à jour l'itinéraire
var routeControls = [];

// Fonction pour mettre à jour l'itinéraire
async function updateRoute(selectedFriends) {
    // Supprimer les itinéraires existants de la carte
    routeControls.forEach(function (control) {
        map.removeControl(control);
    });
    routeControls = []; // Réinitialiser le tableau des contrôles

    // Récupérer les coordonnées des amis sélectionnés et de l'utilisateur
    var allCoordinates = selectedFriends.map(function (friendId) {
        var friend = friendsData.find(friend => friend.utilisateur_id == friendId);
        return [friend.latitude, friend.longitude];
    });

    // Ajouter les coordonnées de l'utilisateur
    allCoordinates.push([userLat, userLng]);

    // Calculer le centre de gravité des coordonnées
    var centerOfGravity = calculateCenterOfGravity(allCoordinates);

    // Récupérer la couleur pour chaque itinéraire
    var routeColors = getRandomColors(selectedFriends.length + 1);

    // Enregistrer l'itinéraire dans la table RendezVous et récupérer l'ID du rendez-vous
    var rendezVousResponse = await fetch('save_route.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            meetingName: "Nom_de_la_reunion", // Remplacez par le nom approprié
            creatorId: <?php echo $_SESSION["utilisateur_id"]; ?>,
            centerLat: centerOfGravity[0],
            centerLng: centerOfGravity[1],
            participants: [<?php echo $_SESSION["utilisateur_id"]; ?>, ...selectedFriends]
        }),
    });

    var rendezVousId = await rendezVousResponse.json();

    // Enregistrer les participants dans la table Utilisateurs_RendezVous
    var participantsResponse = await fetch('save_participants.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            rendezVousId: rendezVousId,
            participants: [<?php echo $_SESSION["utilisateur_id"]; ?>, ...selectedFriends]
        }),
    });

    // Envoyer des invitations aux participants
    var invitationResponse = await fetch('send_invitation.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            rendezVousId: rendezVousId,
            participants: selectedFriends
        }),
    });

    // Afficher les itinéraires sur la carte
    routeControls.push(createRouteControl([userLat, userLng], centerOfGravity, routeColors[routeColors.length - 1]));

    // Afficher les itinéraires pour chaque ami sélectionné
    selectedFriends.forEach(function (selectedFriendId, index) {
        var selectedFriend = friendsData.find(friend => friend.utilisateur_id == selectedFriendId);

        // Ajouter le contrôle de l'itinéraire à la carte
        routeControls.push(createRouteControl(
            [selectedFriend.latitude, selectedFriend.longitude],
            centerOfGravity,
            routeColors[index]
        ));
    });
}



function createRouteControl(startLatLng, endLatLng, routeColor) {
    // Créer un itinéraire avec la couleur spécifiée
    var routeControl = L.Routing.control({
        waypoints: [
            L.latLng(startLatLng[0], startLatLng[1]),
            L.latLng(endLatLng[0], endLatLng[1])
        ],
        routeWhileDragging: true,
        lineOptions: {
            styles: [{ color: routeColor, opacity: 0.7, weight: 5 }]
        }
    }).addTo(map);

    return routeControl;
}


function calculateCenterOfGravity(coordinates) {
    var sumLat = 0;
    var sumLng = 0;

    coordinates.forEach(function (coordinate) {
        sumLat += coordinate[0];
        sumLng += coordinate[1];
    });

    var centerLat = sumLat / coordinates.length;
    var centerLng = sumLng / coordinates.length;

    return [centerLat, centerLng];
}

    function getRandomColors(count) {
    
        var colors = [];
        for (var i = 0; i < count; i++) {
            var color = '#' + Math.floor(Math.random()*16777215).toString(16);
            colors.push(color);
        }
        return colors;
    }

  
</script>

<button id="openFriendFormButton">
    <p>Démarrer un itinéraire</p>
    <img src="img/Join.svg" alt="Join">
</button>

<button id="stopButton">Arrêter</button>


<div id="friendFormSelection">
    <div id="friendSelectionContainer">
        <h2>Sélectionnez vos amis</h2>
        <!-- Ajouter des photos de profil, des noms d'amis et des cases à cocher ici -->
        <?php foreach ($friendsData as $friend): ?>
            <div class="EachFriend">
                <img  src="<?php echo $friend['photo_profil']; ?>" alt="<?php echo $friend['nickname']; ?>" width="40" height="40">
                <?php echo $friend['nickname']; ?>
                <input type="checkbox" class="friend-checkbox" id="friendCheckbox_<?php echo $friend['utilisateur_id']; ?>" value="<?php echo $friend['utilisateur_id']; ?>">
            </div>
        <?php endforeach; ?>
        <button id="updateRouteButton">Créer un itinéraire</button>
    </div>
</div>

<script>

document.addEventListener('DOMContentLoaded', async function () {
    var openFriendFormButton = document.getElementById('openFriendFormButton');
    var friendFormSelection = document.getElementById('friendFormSelection');
    var updateRouteButton = document.getElementById('updateRouteButton');
    var friendCheckboxes = document.querySelectorAll('.friend-checkbox');

    if (openFriendFormButton && friendFormSelection && updateRouteButton && friendCheckboxes) {
        openFriendFormButton.addEventListener('click', function () {
            friendFormSelection.style.display = 'flex';
        });

        updateRouteButton.addEventListener('click', async function () {
            // Récupérer les amis sélectionnés
            var selectedFriends = [];
            friendCheckboxes.forEach(function (checkbox) {
                if (checkbox.checked) {
                    selectedFriends.push(checkbox.value);
                }
            });

            // Logique pour mettre à jour l'itinéraire avec les amis sélectionnés
            if (selectedFriends.length > 0) {
                // Envoi des amis sélectionnés au serveur pour enregistrement
                // Mettre à jour l'itinéraire sur la carte
                updateRoute(selectedFriends);
            }

            // Fermer l'overlay après la mise à jour
            friendFormSelection.style.display = 'none';
        });
    }

});


document.addEventListener('DOMContentLoaded', function () {
    var stopButton = document.getElementById('stopButton');
    if (stopButton) {
        stopButton.addEventListener('click', function () {
            // Supprimer tous les itinéraires de la carte
            routeControls.forEach(function (control) {
                map.removeControl(control);
            });
            routeControls = []; // Réinitialiser le tableau des contrôles
        });
    }
});

// Fonction pour afficher les itinéraires sur la carte
function displayRoutes(rendezVous, participants) {
    // Utilisez les coordonnées existantes et le point calculé du rendez-vous
    var centerOfGravity = [rendezVous.point_calcule_lat, rendezVous.point_calcule_lng];

    var routeColors = getRandomColors(participants.length + 1);
    // Créer l'itinéraire pour l'utilisateur
    routeControls.push(createRouteControl([userLat, userLng], centerOfGravity, routeColors[routeColors.length - 1]));

    // Créer l'itinéraire pour chaque participant
    participants.forEach(function (participant, index) {
        var participantLatLng = [participant.latitude, participant.longitude];

        // Ajouter le contrôle de l'itinéraire à la carte
        routeControls.push(createRouteControl(participantLatLng, centerOfGravity, routeColors[index]));
    });
}



document.addEventListener('DOMContentLoaded', async function () {
    var acceptButtons = document.querySelectorAll('.accept-invitation');
    var rejectButtons = document.querySelectorAll('.reject-invitation');

    if (acceptButtons && rejectButtons) {
        acceptButtons.forEach(async function (button) {
            button.addEventListener('click', async function () {
                var rendezVousId = button.getAttribute('data-rendezvous-id');
                await handleInvitationResponse(rendezVousId, 'accept');

                // Masquer l'overlay complet après avoir cliqué sur "Accepter"
                hideOverlay();
            });
        });

        rejectButtons.forEach(async function (button) {
            button.addEventListener('click', async function () {
                var rendezVousId = button.getAttribute('data-rendezvous-id');
                await handleInvitationResponse(rendezVousId, 'reject');

                // Masquer l'overlay complet après avoir cliqué sur "Refuser"
                hideOverlay();
            });
        });
    }

    function hideOverlay() {
        // Récupérer l'overlay par son ID (ajustez l'ID en fonction de votre structure HTML)
        var overlay = document.getElementById('invitation-overlay');

        // Masquer l'overlay
        if (overlay) {
            overlay.style.display = 'none';
        }
    }

 async function handleInvitationResponse(rendezVousId, userResponse) {
    const serverResponse = await fetch('handle_invitation.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            rendezVousId: rendezVousId,
            response: userResponse,
        }),
    });

    const responseText = await serverResponse.text(); // Obtenir le texte brut de la réponse
    console.log(responseText); // Afficher la réponse brute dans la console

     // Si la réponse est "accept", alors seulement afficher l'itinéraire
     if (userResponse === 'accept') {
        const rendezVousData = await fetch(`get_rendezvous.php?id=${rendezVousId}`);
        const rendezVous = await rendezVousData.json();

        // Récupérer les utilisateurs associés à ce rendez-vous
        const participantsData = await fetch(`get_participants.php?id=${rendezVousId}`);
        const participants = await participantsData.json();

        // Afficher les itinéraires sur la carte
        displayRoutes(rendezVous, participants);
    }
    
    try {
        const responseJson = JSON.parse(responseText);
        console.log(responseJson);
    } catch (error) {
        console.error('Erreur lors de l\'analyse JSON :', error);
    }
}


});



</script>

<style>
    body {
        margin: 0;
        padding: 0;
    }

    /* Style pour le marqueur */
    .user-marker {
        width: 40px; /* Largeur du marqueur */
        height: 40px; /* Hauteur du marqueur */
        overflow: hidden; /* Masquer tout contenu débordant */
        border-radius: 50%; /* Bordures arrondies pour créer un cercle */
        border: 2px solid #3498db; /* Bordure du cercle extérieur */
    }

    .friend-marker {
        width: 40px; /* Largeur du marqueur */
        height: 40px; /* Hauteur du marqueur */
        overflow: hidden; /* Masquer tout contenu débordant */
        border-radius: 50%; /* Bordures arrondies pour créer un cercle */
        border: 2px solid green; /* Bordure du cercle extérieur */
    }

    /* Style pour la photo de profil à l'intérieur du marqueur */
    .user-marker img {
        width: 100%; /* 100% de la largeur du conteneur */
        height: 100%; /* 100% de la hauteur du conteneur */
        object-fit: cover; /* Redimensionner l'image tout en conservant les proportions */
    }

    /* Annuler le style width: auto; pour certains éléments Leaflet */
    .leaflet-container .leaflet-marker-pane img,
    .leaflet-container .leaflet-shadow-pane img,
    .leaflet-container .leaflet-tile-pane img,
    .leaflet-container img.leaflet-image-layer,
    .leaflet-container .leaflet-tile {
        width: 100%;
    }

    .leaflet-routing-container {
        display: none;
    }

    #friendFormSelection {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        justify-content: center;
        align-items: center;

    }

    #friendFormSelection img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }


    #friendFormSelection h2 {
        color: #1F8684;
        font-family: 'Poppins SemiBold', sans-serif;
        font-size: 20px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin-bottom: 20px;
    }

    #friendSelectionContainer {
        background: #fff;
        padding: 25px;
        border-radius: 6px;
    }


.EachFriend {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    gap:10%;
    font-family: 'Poppins', sans-serif;

}

    .friend-checkbox {
        margin-right: 10px;
    }

    #openFriendFormButton {

    position: absolute;
    bottom: 130px;
    right: 20px;
    z-index: 2;
    border: 2px solid #1F8684;
    display: flex;
    gap: 15px;
    background-color: white;
    border-radius: 6px;
    cursor: pointer;

    }

    #openFriendFormButton img {
        width: 25px;
        height: 25px;
        padding-top: 9px;
    }

    #openFriendFormButton p {
           color: #1F8684;
            font-family: 'Poppins SemiBold', sans-serif;
            font-size: 13px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;   
    }
   

    #updateRouteButton {
        background-color: #1F8684;
        color: #FFF;
        border: none;
        border-radius: 6px;
        padding: 10px;
        cursor: pointer;
        margin-top: 10px;
        display: flex;
        margin: auto;
    }
   
    #stopButton {
    position: absolute;
    bottom: 130px;
    left: 30px;
    z-index: 2;
    background-color: #E23744;
    border: none;
    padding: 15px;
    border-radius: 3px;
    color: white;
    font-family: 'Poppins SemiBold', sans-serif;
            font-size: 13px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;   
}
    

    #invitation-overlay {
    position: fixed;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.invitation-content {
    padding: 20px;
    background: #fff; /* Fond blanc */
    border-radius: 10px;
}

.invitation-header {
    text-align: center;
    margin-bottom: 20px;
}

.invitation-text {
    color: var(--Vert-secondaire, #1F8684);

font-family: 'Poppins SemiBold', sans-serif;
font-size: 12px;
font-style: normal;
font-weight: 600;
line-height: normal;
margin-bottom: 20;
}

.creator-info img {
    width: 50px; /* Ajustez la taille de la photo du créateur */
    border-radius: 50%;
}

.participants {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-bottom: 15px;
    margin-top: 20px;
    gap: 5px;
}

.participant {
    text-align: center;
    margin: 0 10px;
}

.participant img {
    width: 40px; /* Ajustez la taille des photos des participants */
    height: 40px;
    border-radius: 50%;
}

.invitation-buttons {
    text-align: center;
}

.accept-invitation {
    padding: 10px 20px;
    margin: 0 10px;
    font-size: 16px;
    background: #2CB88F;
    border: none;
    border-radius: 3px;
    color : white;
}

.reject-invitation {
    padding: 10px 20px;
    margin: 0 10px;
    font-size: 16px;
    background: #E23744;
    border: none;
    border-radius: 3px;
    color : white;
}


</style>

</body>
</html>