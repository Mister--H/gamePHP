<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id']; // Safely fetch user ID from session
} else {
    // Handle cases where the user ID is not available
    $userId = "null";
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

    <!-- <script src="https://unpkg.com/three@0.125.0/build/three.min.js"></script>
    <script src="https://unpkg.com/three@0.125.0/examples/js/loaders/GLTFLoader.js"></script> -->
    <script> const userId = <?php echo json_encode($userId); ?>; </script>
    <script src="<?= asset('js/socket.io.min.js') ?>"></script>
    <script> const socket = io({
            query: { userId: userId }
        });  </script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=<?= $api ?>&libraries=places,drawing,geometry&callback=initMap"></script>
    <?php if ($_SERVER['REQUEST_URI'] !== '/'): ?>
        <script src="<?= asset('js/map.js?v=9876543210123456789876543') ?>"></script>

    <?php endif; ?>
    <style>
        #map {
            height: 100%;
        }
    </style>
</head>

<body>
    <nav
        class="navbar navbar-expand-lg navbar-dark <?php echo (strpos($_SERVER['REQUEST_URI'], '/start') === 0 || strpos($_SERVER['REQUEST_URI'], '/login') === 0 || strpos($_SERVER['REQUEST_URI'], '/register') === 0) ? 'bg-dark' : 'bg-transparent'; ?> px-3 align-middle">
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
                <li class="nav-item active">
                    <a class="nav-link text-white" href="/start"><i class="bi bi-joystick"></i> Game</a>
                </li>
            </ul>
        </div>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <?php if (isset($_SESSION['user_id'])) { ?>
                    <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-person"></i> Profile
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="start/settings"><i class="bi bi-gear"></i> Settings</a>
                        <a class="dropdown-item" href="#"><i class="bi bi-bell"></i> Notifications</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
                    <?php } ?>
                </div>
            </li>
        </ul>
    </nav>
    <!-- Main content area -->
    <div class="">
        <?php echo $content; // This will be replaced with the view content              ?>
    </div>

    <script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>
</body>

</html>