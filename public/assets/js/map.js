function initMap() {
  var berlin = { lat: 52.52, lng: 13.405 };
  map = new google.maps.Map(document.getElementById("map"), {
    zoom: 18,
    center: berlin,
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
    position: berlin,
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
    if (iconUrl) {
      marker.setIcon({
        url: iconUrl,
        scaledSize: new google.maps.Size(86, 120),
      });
    }
    marker.setPosition(newPosition);
    map.setCenter(newPosition);
  });

  // Create a single instance of InfoWindow
  var infoWindow = new google.maps.InfoWindow();

  // Create the PlaceService and send the request.
  // Handle the callback with an anonymous function.
  var service = new google.maps.places.PlacesService(map);

  // Add a click event listener to the map
  map.addListener("click", function (mapsMouseEvent) {
    var geocoder = new google.maps.Geocoder();
    let address = "";
    geocoder.geocode(
      { location: mapsMouseEvent.latLng },
      function (results, status) {
        if (status === "OK") {
          if (results[0]) {
            // Set the marker position to the location that the user clicked
            marker.setPosition(mapsMouseEvent.latLng);

            // Calculate the new position for the info window
            var newPosition = {
              lat: mapsMouseEvent.latLng.lat() + 0.0005,
              lng: mapsMouseEvent.latLng.lng(),
            };

            // Set the info window's content and position
            infoWindow.setContent(
              `
                <div class="container fs-3">
                    <i class="bi bi-geo-alt-fill text-primary"></i>
                    <span id="typingAnimation">` +
                results[0].formatted_address +
                `</span>
                </div>
                `
            );
            infoWindow.setPosition(newPosition);
            infoWindow.open(map);
          } else {
            window.alert("No results found");
          }
        } else {
          window.alert("Geocoder failed due to: " + status);
        }
      }
    );
  });

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
