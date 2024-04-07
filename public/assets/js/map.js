let markers = {}; // Object to store markers for each user

function initMap() {
  let defaultPosition = { lat: 52.52, lng: 13.405 }; // Default position if no data is retrieved
  fetch('api/getPosition')
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      // Check if the response contains position data
      if (data && data.success && data.data && data.data.lat && data.data.lng) {
        const lastLat = data.data.lat;
        const lastLng = data.data.lng;
        // Use the latitude and longitude values as needed
        // For example, update the defaultPosition variable
        defaultPosition = { lat: lastLat, lng: lastLng };
      } else {
        console.error('Invalid position data received:', data);
      }
      // Initialize the map with the retrieved or default position
      initMapWithPosition(defaultPosition);
    })
    .catch(error => {
      // Handle errors that occurred during the fetch request
      console.error('Error fetching position data:', error);
      // Initialize the map with the default position in case of an error
      initMapWithPosition(defaultPosition);
    });
}

function initMapWithPosition(position) {
  const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 18,
    center: position,
    mapTypeId: "satellite",
    heading: 320,
    tilt: 75.5,
    keyboardShortcuts: false, // Disable keyboard shortcuts
    draggable: false, // Disable mouse dragging
    styles: [
      {
        featureType: "poi",
        elementType: "labels",
        stylers: [{ visibility: "off" }], // Hide places labels
      },
    ],
  });


  var marker = new google.maps.Marker({
    position: position,
    map: map,
    icon: {
      url: "../assets/img/characters/char1/char1-down.png",
      scaledSize: new google.maps.Size(86, 120),
    },
  });

  // Preload images
  var images = ["up", "down", "left", "right"].map((direction) => {
    var img = new Image();
    img.src = `../assets/img/characters/char1/char1-${direction}.png`;
    return img;
  });
  document.addEventListener("keydown", function (event) {
    var lat = marker.getPosition().lat();
    var lng = marker.getPosition().lng();
    var iconUrl = "../assets/img/characters/char1/char1-down.png"; // Default icon

    switch (event.key) {
      case "w":
        lat += 0.0001;
        iconUrl = "../assets/img/characters/char1/char1-up.png";
        console.log(lat, lng)
        break;
      case "s":
        lat -= 0.0001;
        // iconUrl already has the default value
        break;
      case "a":
        lng -= 0.0001;
        iconUrl = "../assets/img/characters/char1/char1-left.png";
        break;
      case "d":
        lng += 0.0001;
        iconUrl = "../assets/img/characters/char1/char1-right.png";
        break;
    }

    var newPosition = { lat: lat, lng: lng };

// Assuming marker and map are defined earlier
if (iconUrl) {
    marker.setIcon({
        url: iconUrl,
        scaledSize: new google.maps.Size(86, 120),
    });
}

marker.setPosition(newPosition);
map.setCenter(newPosition);

// Send data to the API endpoint
fetch('api/setPosition', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify(newPosition)
})
.then(response => {
    if (!response.ok) {
        throw new Error('Network response was not ok');
    }
    // Check if the response body is empty
    const contentType = response.headers.get('content-type');
    if (contentType && contentType.includes('application/json')) {
        return response.json(); // Parse JSON only if content type is JSON
    } else {
        return {}; // Return empty object if response body is empty
    }
})
.then(data => {
    console.log('Position updated successfully:', data);
})
.catch(error => {
    console.error('Error updating position:', error);
});
      });

  // Create a single instance of InfoWindow
  var infoWindow = new google.maps.InfoWindow();

  // Create the PlaceService and send the request.
  // Handle the callback with an anonymous function.
  var service = new google.maps.places.PlacesService(map);

  

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

  loadPurchasedPolygons();
}
