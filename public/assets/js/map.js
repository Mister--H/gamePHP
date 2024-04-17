
let map; // Global reference to the map object
let mainMarker; // Global reference to the main user's marker
let playerMarkers = {}; // Object to store markers for each user

// URL of your WebSocket server
function initMap() {
  fetch('https://game.metans.de/api/getPosition')
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const position = {
          lng: data.data.coordinates[0],
          lat: data.data.coordinates[1]
        };
        initMapWithPosition(position);
      } else {
        console.error('Failed to get position:', data.error);
        initMapWithPosition({ lng: 13.405, lat: 52.52 }); // Default position
      }
    })
    .catch(error => {
      console.error('Error fetching position:', error);
      initMapWithPosition({ lng: 13.405, lat: 52.52 }); // Default position
    });
}

function initMapWithPosition(position) {
  map = new google.maps.Map(document.getElementById("map"), {
    zoom: 18,
    center: position,
    mapTypeId: "satellite",
    heading: 320,
    tilt: 75.5,
    keyboardShortcuts: false,
    draggable: false,
    styles: [{ featureType: "poi", elementType: "labels", stylers: [{ visibility: "off" }] }],
  });

  mainMarker = new google.maps.Marker({
    position: position,
    map: map,
    icon: {
      url: "../assets/img/characters/char1/char1-down.png",
      scaledSize: new google.maps.Size(86, 120),
    },
  });

  preloadImages();
  setupWebSocket(); // Setup WebSocket connection
loadDrawing(); // Load drawing manager
loadPurchasedPolygons();
}

function preloadImages() {
  ["up", "down", "left", "right"].forEach(direction => {
    var img = new Image();
    img.src = `../assets/img/characters/char1/char1-${direction}.png`;
  });
}

function setupWebSocket() {
  socket.on('nearbyPlayers', (playersData) => {
    if (Array.isArray(playersData)) {
      playersData.forEach(player => {
        const position = { lat: player.lat, lng: player.lng };
        const iconUrl = "../assets/img/characters/char1/char1-down.png";
        addOrUpdatePlayerMarker(player.user_id, position, iconUrl);
      });
    } else {
      console.error('Received data is not an array:', playersData);
    }
  });
    socket.on('updatePosition', (data) => {
    const position = { lat: data.position.lat, lng: data.position.lng };
    const iconUrl = "../assets/img/characters/char1/char1-down.png"; // Use appropriate icon
    addOrUpdatePlayerMarker(data.userId, position, iconUrl);
  });
  document.addEventListener("keydown", (event) => handleKeyDown(event));
  // Add event listeners to the arrow buttons for tap events
document.getElementById("upButton").addEventListener("click", () => handleMovement("up"));
document.getElementById("downButton").addEventListener("click", () => handleMovement("down"));
document.getElementById("leftButton").addEventListener("click", () => handleMovement("left"));
document.getElementById("rightButton").addEventListener("click", () => handleMovement("right"));
}



// Function to handle keyboard events
function handleKeyDown(event) {
    const keyMappings = {
        "w": "up",
        "s": "down",
        "a": "left",
        "d": "right",
        "ص": "up",
        "س": "down",
        "ش": "left",
        "ی": "right"
    };

    const direction = keyMappings[event.key];
    if (direction) {
        handleMovement(direction);
    }
}

// Function to move the character based on the direction
function handleMovement(direction) {
    let lat = mainMarker.getPosition().lat();
    let lng = mainMarker.getPosition().lng();
    let iconUrl = "../assets/img/characters/char1/char1-down.png";

    switch (direction) {
        case "up":
            lat += 0.0001;
            iconUrl = "../assets/img/characters/char1/char1-up.png";
            break;
        case "down":
            lat -= 0.0001;
            break;
        case "left":
            lng -= 0.0001;
            iconUrl = "../assets/img/characters/char1/char1-left.png";
            break;
        case "right":
            lng += 0.0001;
            iconUrl = "../assets/img/characters/char1/char1-right.png";
            break;
    }

  const newPosition = { lng, lat };
  mainMarker.setIcon({ url: iconUrl, scaledSize: new google.maps.Size(86, 120) });
  mainMarker.setPosition(newPosition);
  map.setCenter(newPosition);
  socket.emit('sPosition', JSON.stringify(newPosition));
}

function addOrUpdatePlayerMarker(playerId, position, iconUrl) {
    // Check if marker already exists
    const PlayerPosition = { lng: position.lng, lat: position.lat };
    if (playerMarkers[playerId]) {
      // Update the existing marker position
      playerMarkers[playerId].setPosition(PlayerPosition);
    } else {
        // Create a new marker for new player
        playerMarkers[playerId] = new google.maps.Marker({
          position: PlayerPosition,
          map: map,
          icon: {
            url: iconUrl,
            scaledSize: new google.maps.Size(50, 75) // Smaller size for other players
          },
        });
         // Attach a click event listener to the marker
         playerMarkers[playerId].addListener('click', function() {
            fetchUserInfo(playerId, playerMarkers[playerId]);
        });
    }

}

function removePlayerMarker(playerId) {
  if (playerMarkers[playerId]) {
    playerMarkers[playerId].setMap(null);
    delete playerMarkers[playerId];
  }
}
  
function loadDrawing(){

  var drawingManager = new google.maps.drawing.DrawingManager({
    drawingMode: null, // Set drawing mode to null for default non-drawing mode
    drawingControl: true,
    drawingControlOptions: {
      position: google.maps.ControlPosition.TOP_CENTER,
      drawingModes: ["polygon"],
    },
    polygonOptions: {
      clickable: true,
      editable: true,
    },
  });
  drawingManager.setMap(map);

  google.maps.event.addListener(
    drawingManager,
    "overlaycomplete",
    function (event) {
      if (event.type == "polygon") {
        var area = google.maps.geometry.spherical.computeArea(
          event.overlay.getPath()
        );
        var price = area / 100; // 1 coin per 10x10 area

        // Custom content for the InfoWindow
        var contentString =
          '<div id="content p-3">' +
          '<p><i class="bi bi-rulers"></i> <span style="font-size: 16px;">Area:</span> ' +
          area.toFixed(2) +
          " SqM</p>" +
          '<p><i class="bi bi-currency-dollar"></i> <span style="font-size: 16px;">Price:</span> ' +
          price.toFixed(2) +
          " coins</p>" +
          '<div class="button-group gap-2 d-flex">' +
          '<button class="btn btn-primary" onclick="buyArea(' +
          area.toFixed(2) +
          ", " +
          price.toFixed(2) +
          ')"><i class="bi bi-cart-plus"></i> Buy</button>' +
          '<button class="btn btn-secondary" onclick="cancelArea()"><i class="bi bi-x-circle"></i> Cancel</button>' +
          "</div>" +
          "</div>";

        var infoWindow = new google.maps.InfoWindow({
          content: contentString,
        });

        infoWindow.setPosition(event.overlay.getPath().getAt(0));
        infoWindow.open(map);

        // Function to handle the "Cancel" action
        window.cancelArea = function () {
          event.overlay.setMap(null);
          infoWindow.close();
        };

        // Function to handle the "Buy" action
        window.buyArea = function (area, price) {
          console.log("Bought area: ", area, "Price: ", price);

          // Serialize polygon path
          var path = event.overlay
            .getPath()
            .getArray()
            .map(function (vertex) {
              return { lat: vertex.lat(), lng: vertex.lng() };
            });

          var purchases = JSON.parse(sessionStorage.getItem("purchases")) || [];
          purchases.push({
            area: area,
            price: price,
            path: path,
          });

          sessionStorage.setItem("purchases", JSON.stringify(purchases));
          event.overlay.setMap(null);
          drawingManager.setDrawingMode(null);
          loadSpecificPolygon(path);
          infoWindow.close();
        };
      }
    }
  );
}
  // Function to load a specific polygon given its path
  function loadSpecificPolygon(path) {
    var polygon = new google.maps.Polygon({
      paths: path,
      strokeColor: "#FF0000",
      strokeOpacity: 0.8,
      strokeWeight: 2,
      fillColor: "#FF0000",
      fillOpacity: 0.35,
      editable: false,
      clickable: false,
    });

    polygon.setMap(map);
  }

  // Cancel drawing on Esc key press
  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      drawingManager.setDrawingMode(null);
    }
  });

  function loadPurchasedPolygons() {
    var purchases = JSON.parse(sessionStorage.getItem("purchases")) || [];

    purchases.forEach(function (purchase) {
      // Create a new polygon for each purchase
      var polygon = new google.maps.Polygon({
        paths: purchase.path,
        strokeColor: "#FF0000",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "#FF0000",
        fillOpacity: 0.35,
        editable: false, // Purchased areas should not be editable
        clickable: false, // Assuming you don't want these to be clickable
      });

      polygon.setMap(map);
    });
  }


 async function fetchUserInfo(userId, marker) {
    try {
        const response = await fetch('https://game.metans.de/api/getUserInfo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ userId: userId })
        });

        if (response.ok) {
            const user = await response.json();
            showInfoWindow(user, marker);
        } else {
            console.error('No user info found for userId:', userId);
        }
    } catch (error) {
        console.error('Error fetching user info:', error);
    }
}

function showInfoWindow(user, marker) {
    const contentString = `
        <div id="content" class="p-3">
        <img src="${user.avatar}" alt="Avatar" style="width:50px;height:50px; border-radius:50%">
            <h1>${user.nickname || 'No Nickname'}</h1>
            <p><i class="bi bi-instagram"></i><a href="https://instagram.com/${user.instagram}"> @${user.instagram} </a></p>
            <p><i class="bi bi-telegram"></i><a href="https://t.me/${user.telegram}"> @${user.telegram} </a></p>
            <p><i class="bi bi-coin"></i> Coins: ${user.coins}</p>
        </div>`;

    const infoWindow = new google.maps.InfoWindow({
        content: contentString
    });

    infoWindow.open({
        anchor: marker,
        map,
        shouldFocus: false
    });
}
