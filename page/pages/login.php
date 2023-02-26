<div class="row">
    <div class="col-lg-6 mb-3">
        <form class="card p-3" action="/login" method="post" autocomplete="on">
            <h1 class="fs-2">Belépés</h1>
            <label for="login-username" class="form-label">Felhasználónév:</label>
            <input type="text" name="username" id="login-username" class="form-control">
            <label for="login-password" class="form-label mt-2">Jelszó:</label>
            <input type="password" name="password" id="login-password" class="form-control">
            <div class="row mt-3">
                <div class="col-auto">
                    <input type="submit" value="Belépés" class="btn-brown form-control">
                </div>
                <div class="col my-auto">
                    <a href="/login/forgotten-password">Elfelejtette jelszavát?</a>
                </div>
            </div>
        </form>
    </div>
    <div class="col-lg-6">
        <form class="card p-3" action="/signup" method="post" autocomplete="off">
            <h1 class="fs-2 mb-0">Még nincs fiókja?</h1>
            <span class="mb-2">Regisztráljon egyet most!</span>
            <label for="signup-username" class="form-label">Felhasználónév:</label>
            <input type="text" name="username" id="signup-username" class="form-control">
            <label for="signup-password" class="form-label mt-2">Jelszó:</label>
            <input type="password" name="password" id="signup-password" class="form-control">
            <div class="row mt-3">
                <div class="col-auto">
                    <input type="submit" value="Regisztráció" class="btn-brown form-control">
                </div>
            </div>
        </form>
    </div>
</div>