<div class="container p-4 my-4 rounded"
    style="min-height: 100vh; width: 100%; background-image: linear-gradient(to bottom right, darkblue, black);">
    <form class="fs-5 text-white">
        <div class="row">
            <div class="col-md-2 mb-3">
                <label for="firstName" class="form-label"><i class="bi bi-person"></i> First Name</label>
                <input type="text" class="form-control" id="firstName" name="firstName">
            </div>
            <div class="col-md-2 mb-3">
                <label for="lastName" class="form-label"><i class="bi bi-person"></i> Last Name</label>
                <input type="text" class="form-control" id="lastName" name="lastName">
            </div>
            <div class="col-md-3 mb-3">
                <label for="nickname" class="form-label"><i class="bi bi-person-badge"></i> Display Name</label>
                <input type="text" class="form-control" id="nickname" name="nickname">
            </div>

            <hr>
            <div class="col-md-2 mb-3">
                <label for="sex" class="form-label"><i class="bi bi-gender-ambiguous"></i> Sex</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="sex" id="male" value="male">
                    <label class="form-check-label" for="male">
                        Male
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="sex" id="female" value="female">
                    <label class="form-check-label" for="female">
                        Female
                    </label>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <label for="age" class="form-label"><i class="bi bi-calendar"></i> Brithday</label>
                <input type="date" class="form-control" id="age" name="age">
            </div>
            <hr>
            <div class="col-md-3 mb-3">
                <label for="phoneNumber" class="form-label"><i class="bi bi-telephone"></i> Phone Number</label>
                <div class="input-group">
                    <span class="input-group-text" id="countryCode"><i class="bi bi-flag"></i></span>
                    <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" placeholder="+1...">
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="email" class="form-label"><i class="bi bi-envelope"></i> Email</label>
                <div class="d-flex">
                    <input type="email" class="form-control" id="email" name="email" value="<?=$_SESSION['user']['email']?>" readonly>
                    <button type="button" class="btn btn-link"><i class="bi bi-pencil"></i></button>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <label for="password" class="form-label"><i class="bi bi-lock"></i> Change Password</label>
                <div class="d-flex">
                    <input type="password" class="form-control" id="password" name="password" readonly>
                    <button type="button" class="btn btn-link"><i class="bi bi-shuffle"></i></button>
                </div>
            </div>
            <hr>
            <div class="col-md-12 mb-3 py-3">
                <div class="d-flex gap-3">
                    <div>
                        <label for="instagram" class="form-label"><i class="bi bi-instagram"></i> Instagram</label>
                        <input type="text" class="form-control" id="instagram" name="instagram">
                    </div>
                    <div>
                        <label for="facebook" class="form-label"><i class="bi bi-facebook"></i> Facebook </label>
                        <input type="text" class="form-control" id="facebook" name="facebook">
                    </div>
                    <div>
                        <label for="twitter" class="form-label"><i class="bi bi-twitter"></i> Twitter </label>
                        <input type="text" class="form-control" id="twitter" name="twitter">
                    </div>
                    <div>
                        <label for="telegram" class="form-label"><i class="bi bi-telegram"></i> Telegram </label>
                        <input type="text" class="form-control" id="telegram" name="telegram">
                    </div>
                </div>
            </div>
            <hr>
            <div class="col-md-4 mb-3 py-3">
                <label for="avatar" class="form-label"><i class="bi bi-person-circle"></i> Profile Avatar</label>
                <input type="file" class="form-control" id="avatar" name="avatar">
            </div>
            <div class="col-md-12 mb-3">
                <label for="bio" class="form-label"><i class="bi bi-file-earmark-text"></i> Bio</label>
                <textarea class="form-control" id="bio" name="bio" rows="5"></textarea>
            </div>


        </div>
        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
    </form>
</div>