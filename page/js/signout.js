function signout() {
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function () {
        const result =  this.responseText;

        if (result === "error") {
            const errorMessage = "Nincs ilyen felhasználónév-jelszó párosítás!";
            const errorSpan = document.getElementById("login-error");
            errorSpan.innerText = errorMessage;
            errorSpan.classList.remove("d-none");
            errorSpan.classList.add("d-block");
        } else {
            window.location.href = window.location.href;
        }
    }

    const username = document.getElementById("login-username").value;
    const password = document.getElementById("login-password").value;

    xmlhttp.open("GET", `pages/login.php?username=${username}&password=${password}`);
    xmlhttp.send();
}