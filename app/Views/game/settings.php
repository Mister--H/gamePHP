<div class="container p-4 my-4 rounded"
    style="min-height: 100vh; width: 100%; background-image: linear-gradient(to bottom right, darkblue, black);">
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?= $_SESSION['error']; ?>
        </div>
        <?php unset($_SESSION['error']); // Clear the error message from the session      ?>
    <?php endif; ?>
    <form class="fs-5 text-white" method="post" enctype="multipart/form-data">
        <div class="row">

            <div class="col-md-2 mb-3">
                <label for="firstName" class="form-label"><i class="bi bi-person"></i> First Name</label>
                <input type="text" class="form-control" id="firstName" name="firstName"
                    value="<?= $_SESSION['user']['firstName'] ?>">
            </div>
            <div class="col-md-2 mb-3">
                <label for="lastName" class="form-label"><i class="bi bi-person"></i> Last Name</label>
                <input type="text" class="form-control" id="lastName" name="lastName"
                    value="<?= $_SESSION['user']['lastName'] ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label for="nickname" class="form-label"><i class="bi bi-person-badge"></i> Display Name</label>
                <input type="text" class="form-control" id="nickname" name="nickname"
                    value="<?= $_SESSION['user']['nickname'] ?>">
            </div>

            <hr>
            <div class="col-md-2 mb-3">
                <label for="sex" class="form-label"><i class="bi bi-gender-ambiguous"></i> Sex</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="sex" id="male" value="male" <?php if ($_SESSION['user']['sex'] === 'male')
                        echo 'checked'; ?>>
                    <label class="form-check-label" for="male">
                        Male
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="sex" id="female" value="female" <?php if ($_SESSION['user']['sex'] === 'female')
                        echo 'checked'; ?>>
                    <label class="form-check-label" for="female">
                        Female
                    </label>
                </div>

            </div>
            <div class="col-md-3 mb-3">
                <label for="age" class="form-label"><i class="bi bi-calendar"></i> Brithday</label>
                <input type="date" class="form-control" id="age" name="age"
                    value="<?= date('Y-m-d', strtotime($_SESSION['user']['age'])); ?>">

            </div>
            <hr>
            <div class="col-md-3 mb-3">
                <label for="phoneNumber" class="form-label"><i class="bi bi-telephone"></i> Phone Number</label>
                <div class="input-group">
                    <span class="input-group-text" id="countryCode"><i class="bi bi-flag"></i></span>
                    <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" placeholder="+1..."
                        value="<?= $_SESSION['user']['phoneNumber'] ?>">
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="email" class="form-label"><i class="bi bi-envelope"></i> Email</label>
                <div class="d-flex">
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?= $_SESSION['user']['email'] ?>" readonly>
                    <button type="button" class="btn btn-link" id="toggleEmail"><i class="bi bi-pencil"></i></button>

                </div>
            </div>
            <div class="col-md-3 mb-3">
                <label for="password" class="form-label"><i class="bi bi-lock"></i> Change Password</label>
                <div class="d-flex">
                    <input type="password" class="form-control" id="password" name="password">
                    <button type="button" class="btn btn-link" id="togglePasswordBtn"><i class="bi bi-eye"></i></button>
                    <button type="button" id="generatePasswordBtn" class="btn btn-link"><i
                            class="bi bi-shuffle"></i></button>
                </div>
            </div>
            <hr>
            <div class="col-md-12 mb-3 py-3">
                <div class="d-flex gap-3">
                    <div>
                        <label for="instagram" class="form-label"><i class="bi bi-instagram"></i> Instagram</label>
                        <input type="text" class="form-control" id="instagram" name="instagram"
                            value="<?= $_SESSION['user']['instagram'] ?>">
                    </div>
                    <div>
                        <label for="facebook" class="form-label"><i class="bi bi-facebook"></i> Facebook </label>
                        <input type="text" class="form-control" id="facebook" name="facebook"
                            value="<?= $_SESSION['user']['facebook'] ?>">
                    </div>
                    <div>
                        <label for="twitter" class="form-label"><i class="bi bi-twitter"></i> Twitter </label>
                        <input type="text" class="form-control" id="twitter" name="twitter"
                            value="<?= $_SESSION['user']['twitter'] ?>">
                    </div>
                    <div>
                        <label for="telegram" class="form-label"><i class="bi bi-telegram"></i> Telegram </label>
                        <input type="text" class="form-control" id="telegram" name="telegram"
                            value="<?= $_SESSION['user']['telegram'] ?>">
                    </div>
                </div>
            </div>
            <hr>
            <div class="col-md-4 mb-3 py-3">
                <label for="avatar" class="form-label"><i class="bi bi-person-circle"></i> Profile Avatar</label>
                <div class="mb-3">
                    <?php if (isset($_SESSION['user']['avatar']) && !empty($_SESSION['user']['avatar'])): ?>
                        <img src="<?= $_ENV['BASE_URL'] . '/' . $_SESSION['user']['avatar'] ?>" alt="Profile Avatar"
                            class="img-fluid mb-2 rounded" width="150px">
                    <?php endif; ?>
                    <div id="avatarContainer">
                        <input type="file" class="form-control" id="avatar" name="avatar" <?= isset($_SESSION['user']['avatar']) && !empty($_SESSION['user']['avatar']) ? 'style="display:none"' : '' ?>>
                        <button type="button" id="changeAvatarBtn"><i class="bi bi-pencil"></i></button>
                    </div>
                </div>

            </div>
            <div class="col-md-12 mb-3">
                <label for="bio" class="form-label"><i class="bi bi-file-earmark-text"></i> Bio</label>
                <textarea class="form-control" id="bio" name="bio" rows="5"><?= $_SESSION['user']['bio'] ?></textarea>

            </div>


        </div>
        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("toggleEmail").addEventListener("click", function () {
            document.getElementById("email").removeAttribute("readonly");
        });
        document.getElementById("generatePasswordBtn").addEventListener("click", function () {
            var length = 10;
            var charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            var password = "";
            for (var i = 0; i < length; i++) {
                password += charset.charAt(Math.floor(Math.random() * charset.length));
            }
            document.getElementById("password").value = password;
        });
        document.getElementById("togglePasswordBtn").addEventListener("click", function () {
            var passwordInput = document.getElementById("password");
            var icon = document.querySelector("#togglePasswordBtn i");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                passwordInput.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        });
        const avatarInput = document.getElementById('avatar');
        const changeAvatarBtn = document.getElementById('changeAvatarBtn');
        const avatarContainer = document.getElementById('avatarContainer');
        // Show the avatar input when the change avatar button is clicked
        changeAvatarBtn.addEventListener('click', function() {
            avatarInput.style.display = 'block';
        });
        
    });

</script>