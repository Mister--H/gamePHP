<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metan Game</title>
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('bootstrap-icons/font/bootstrap-icons.min.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;900&display=swap" rel="stylesheet">

        <script src="https://unpkg.com/three@0.125.0/build/three.min.js"></script>
        <script src="https://unpkg.com/three@0.125.0/examples/js/loaders/GLTFLoader.js"></script>
        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDpQiFpsx6s7ku-9mMf_jt0OcqkUNnK53E&libraries=places,drawing,geometry&callback=initMap"></script>
        <?php if ($_SERVER['REQUEST_URI'] === '/dashboard'): ?>
            <script src="<?= asset('js/map.js') ?>"></script>
        <?php endif; ?>
        <style>
            #map {
                height: 100%;
            }
        </style>
    </head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-transparent ps-3 align-middle">
        <a class="navbar-brand text-primary d-flex fw-light" style="align-items:center;" href="#">
            <i class="bi bi-infinity fs-1"></i>
            <span class="">
                Metan Game
            </span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link text-white" href="/"><i class="bi bi-app"></i> Home</a>
                </li>

            </ul>
        </div>
    </nav>
    <!-- Main content area -->
    <div class="">
        <?php echo $content; // This will be replaced with the view content  ?>
    </div>

    <script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>
</body>

</html>