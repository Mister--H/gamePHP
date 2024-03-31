// Define global variables for the Three.js scene
let camera, scene, renderer;

document.addEventListener('DOMContentLoaded', function() {
    loadGoogleMapsAPI();    
});

function loadGoogleMapsAPI() {
    const script = document.createElement('script');
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
}
let model;

function initMap() {
    const berlin = { lat: 52.5200, lng: 13.4050 };
    const map = new google.maps.Map(document.getElementById('map'), {
        zoom: 16,
        center: berlin,
        mapTypeId: 'satellite',
        heading: 320,
        tilt: 75.5,
    });

    // Disable arrow key movement on the map
    map.setOptions({ keyboardShortcuts: false });



    // Create the content for the InfoWindow
    const contentString = '<div id="content">'+
        '<div id="siteNotice">'+
        '</div>'+
        '<h1 id="firstHeading" class="firstHeading">Welcome to the Metan</h1>'+
        '<div id="bodyContent">'+
        '<p>Click On the Map to Move Around.</p>'+
        '</div>'+
        '</div>';

    // Create the InfoWindow
    const infowindow = new google.maps.InfoWindow({
        content: contentString,
        position: berlin,
        disableAutoPan: true, // Disable automatic panning when the InfoWindow is opened
        closeBoxURL: '-', // Remove the default close button
    });

    // Open the InfoWindow when the map is loaded
    infowindow.open(map);

    // Close the InfoWindow when user clicks outside the box
    google.maps.event.addListener(map, 'click', function() {
        infowindow.close();
    });
    google.maps.event.addListener(map, 'rightclick', function(event) {
        map.setTilt(map.getTilt() + 10);
    });
    class ThreeJSOverlay extends google.maps.OverlayView {
        constructor(map) {
            super();
            this.map = map;
            this.container = document.createElement('div');
            this.container.style.position = 'absolute';
            this.container.style.width = '100%'; // Change the width to 100%
            this.container.style.height = '100%'; // Change the height to 100%
            this.container.style.overflow = 'visible';
            this.container.id = 'my-overlay'; // Add the id to the div
            this.setMap(map);
        }

        onAdd() {
            this.getPanes().overlayLayer.appendChild(this.container);
            this.initThreeJS();
        }

        draw() {
            const projection = this.getProjection();
            const center = projection.fromLatLngToDivPixel(this.map.getCenter());

            const scale = Math.pow(5, this.map.getZoom());
            const offset = 100;
            
            this.container.style.left = (center.x - offset / 2) + 'px';
            this.container.style.top = (center.y - offset / 2) + 'px';
       

            renderer.setSize(offset, offset);
            camera.aspect = 2;
            camera.updateProjectionMatrix();
        }

        onRemove() {
            if (this.container) {
                this.container.parentNode.removeChild(this.container);
            }
        }

        initThreeJS() {
            camera = new THREE.PerspectiveCamera(75, 1, 0.1, 1000);
            camera.position.z = 1;
        
            scene = new THREE.Scene();
            const ambientLight = new THREE.AmbientLight(0x404040); // soft white light
            scene.add(ambientLight);

            const directionalLight = new THREE.DirectionalLight(0xffffff, 1); // white, intensity 0.5
            directionalLight.position.set(0, 1, 0.5); // set the light direction
            scene.add(directionalLight);
        
            const loader = new THREE.GLTFLoader();        
            loader.load('assets/img/low_poly_scooter/scene.gltf', function(gltf) {
                model = gltf.scene; // Assign the loaded model to the variable
                scene.add(model);
            });
        
            renderer = new THREE.WebGLRenderer({ alpha: true });
            this.container.appendChild(renderer.domElement);
            animate();
        }
    }

    function animate() {
        requestAnimationFrame(animate);
        renderer.render(scene, camera);
    }

    new ThreeJSOverlay(map);

    
    document.addEventListener('keydown', function(event) {
        if (!model) return;
        const moveSpeed = 0.0001;
        const latLng = map.getCenter();
        switch (event.key) {
            case 'ArrowUp':
                model.rotation.x = 10.999999999999977;
                model.rotation.y = 10.999999999999977;
                model.rotation.z = 4;
                console.log('x' + model.rotation.x + 'y' + model.rotation.y + 'z' + model.rotation.z);
                map.setCenter({lat: latLng.lat() + moveSpeed, lng: latLng.lng()});
                break;
            case 'ArrowDown':
                model.rotation.x = 5.1999999999999975;
                model.rotation.y = -10.999999999999977;
                model.rotation.z = -4;
                map.setCenter({lat: latLng.lat() - moveSpeed, lng: latLng.lng()});
                break;
            case 'ArrowLeft':
                model.rotation.x = 0;
                model.rotation.y = 0;
                model.rotation.z = 0;
                model.position.x -= moveSpeed;
                map.setCenter({lat: latLng.lat(), lng: latLng.lng() - moveSpeed});
                break;
            case 'ArrowRight':
                model.rotation.x = 0.88;
                model.rotation.y = 3.0000000000000013;
                model.rotation.z = 6.299999999999994;
                model.position.x += moveSpeed;
                map.setCenter({lat: latLng.lat(), lng: latLng.lng() + moveSpeed});
                break;
        }
    });

}
