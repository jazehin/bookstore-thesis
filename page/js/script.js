function addField(fieldName) {
    let row = document.getElementById(fieldName + "-fields");

    let col = document.createElement("div");
    col.classList.add("col-12", "mb-3");

    let fieldCount = document.getElementsByClassName(fieldName + "-field").length;
    let nextFieldId = fieldCount + 1;

    let field = document.createElement("input");
    field.type = "text";
    field.classList.add("form-control", fieldName + "-field");
    field.id = fieldName + "-" + nextFieldId;
    field.name = fieldName + "-" + nextFieldId;
    field.setAttribute("list", fieldName + "s");

    col.appendChild(field);
    row.appendChild(col);
}

function removeField(fieldName) {
    let fieldIdToRemove = document.getElementsByClassName(fieldName + "-field").length;

    if (fieldIdToRemove > 1) {
        let elementToRemove = document.getElementById(fieldName + "-" + fieldIdToRemove).parentElement;
        let parentElement = elementToRemove.parentElement;
        parentElement.removeChild(elementToRemove);
    } else {
        alert("Legalább egy mezőnek maradnia kell!");
    }

}

function login() {
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function () {
        const result = this.responseText;

        if (result == "error") {
            displayLoginError("Nincs ilyen felhasználónév-jelszó párosítás!");
        } else {
            window.location.href = window.location.href;
        }
    }

    const username = document.getElementById("login-username").value;
    const password = document.getElementById("login-password").value;

    if (username.length === 0 || password.length === 0) {
        displayLoginError("Ne hagyjon üres mező(ke)t!");
        return;
    }
    xmlhttp.open("GET", `/pages/ajax/login.php?username=${username}&password=${password}`);
    xmlhttp.send();
}

function displayLoginError(errorMessage) {
    const errorSpan = document.getElementById("login-error");
    errorSpan.innerText = errorMessage;
    errorSpan.classList.remove("d-none");
    errorSpan.classList.add("d-block");
}

function signUp() {
    clearSignUpErrors();

    const email = document.getElementById("signup-email").value;
    const username = document.getElementById("signup-username").value;
    const password = document.getElementById("signup-password").value;



    //checking if email is already registered
    let xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function () {
        const result = this.responseText;

        if (username.length === 0 || password.length === 0 || email.length === 0) {
            displaySignUpError("Ne hagyjon üres mező(ke)t!");
            return;
        }

        //if there is an error, don't make the ajax request
        let error = false;

        //checking if email is ok
        const emailRegex = /^\S+@\S+\.\S+$/;

        if (!emailRegex.test(email)) {
            displaySignUpError("Érvénytelen email cím!");
            error = true;
        }

        //checking if email already exists in the database
        if (result.includes("email-exists")) {
            displaySignUpError("Ezzel az e-mail fiókkal már van regisztrálva fiók!");
            error = true;
        }

        //checking if username length is ok
        if (username.length < 4 || username.length > 20) {
            displaySignUpError("A felhasználónév minimum 4 és maximum 20 karakter hosszú lehet!");
            error = true;
        }

        //checking if username already exists in the database
        if (result.includes("username-exists")) {
            displaySignUpError("Ezzel az felhasználónévvel már létezik fiók!");
            error = true;
        }

        //checking if password is valid
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@&#_%]).{8,20}$/;

        if (!passwordRegex.test(password)) {
            displaySignUpError("A jelszó nem felel meg az elvárásoknak!");
            error = true;
        }

        if (error) return;

        xmlhttp = new XMLHttpRequest();
        xmlhttp.onload = function () {
            const result = this.responseText;

            if (result.length != 0) {
                alert("Regisztráció sikertelen.");
            } else {
                window.location.href = window.location.href;
            }
        }
        xmlhttp.open("GET", `/pages/ajax/signup.php?username=${username}&email=${email}&password=${password}`);
        xmlhttp.send();
    }

    xmlhttp.open("GET", `/pages/ajax/exists.php?email=${email}&username=${username}`);
    xmlhttp.send();
}

function clearSignUpErrors() {
    const errorDiv = document.getElementById("signup-errors");
    errorDiv.innerHTML = "";
}

function displaySignUpError(errorMessage) {
    const errorDiv = document.getElementById("signup-errors");
    const errorP = document.createElement("span");

    errorP.innerText = errorMessage;
    if (errorDiv.hasChildNodes()) {
        errorDiv.append(document.createElement("br"));
    }
    errorDiv.append(errorP);

    errorDiv.classList.remove("d-none");
    errorDiv.classList.add("mb-2");
}

function showPasswordChecks(show) {
    const ul = document.getElementById("password-checks");
    if (show) {
        ul.classList.remove("d-none");
    } else {
        ul.classList.add("d-none");
    }
}

function checkPassword(password) {
    const ul = document.getElementById("password-checks");
    ul.classList.remove("d-none");

    const spans = [
        document.getElementById("password-length"),
        document.getElementById("password-lowercase"),
        document.getElementById("password-capital"),
        document.getElementById("password-numeric"),
        document.getElementById("password-special")
    ];

    const patterns = [/^.{8,20}$/, /[a-z]/, /[A-Z]/, /[0-9]/, /[@&#_%]/];

    for (let i = 0; i < spans.length; i++) {
        if (patterns[i].test(password)) {
            spans[i].classList.remove("text-danger");
            spans[i].classList.add("text-success");
        } else {
            spans[i].classList.add("text-danger");
            spans[i].classList.remove("text-success");
        }
    }
}

function loadBookDataByIsbn(isbn) {
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function () {
        const result = this.responseText;

        if (result === "") {
            document.getElementById("title").value = "";
            document.getElementById("title").setAttribute("disabled", "");

            document.getElementById("series").value = "";
            document.getElementById("series").setAttribute("disabled", "");

            document.getElementById("date_published").value = "";
            document.getElementById("date_published").setAttribute("disabled", "");

            document.getElementById("stock").value = "";
            document.getElementById("stock").setAttribute("disabled", "");

            document.getElementById("pages").value = "";
            document.getElementById("pages").setAttribute("disabled", "");

            document.getElementById("weight").value = "";
            document.getElementById("weight").setAttribute("disabled", "");

            document.getElementById("weight").value = "";
            document.getElementById("weight").setAttribute("disabled", "");

            document.getElementById("publisher").value = "";
            document.getElementById("publisher").setAttribute("disabled", "");

            document.getElementById("covertype").value = "";
            document.getElementById("covertype").setAttribute("disabled", "");

            document.getElementById("language").value = "";
            document.getElementById("language").setAttribute("disabled", "");

            document.getElementById("description").value = "";
            document.getElementById("description").setAttribute("disabled", "");

            const genreFields = document.getElementsByClassName("genre-field");
            for (let i = genreFields.length; i > 1; i--) {
                removeField("genre");
            }
            genreFields[0].value = "";
            genreFields[0].setAttribute("disabled", "");

            const writerFields = document.getElementsByClassName("writer-field");
            for (let i = writerFields.length; i > 1; i--) {
                removeField("writer");
            }
            writerFields[0].value = "";
            writerFields[0].setAttribute("disabled", "");

            document.getElementById("price").value = "";
            document.getElementById("price").setAttribute("disabled", "");

            document.getElementById("discounted_price").value = "";
            document.getElementById("discounted_price").setAttribute("disabled", "");

            document.getElementById("cover").value = "";
            document.getElementById("cover").setAttribute("disabled", "");

            const buttons = document.getElementsByClassName("form-button");

            for (let i = 0; i < buttons.length; i++) {
                buttons[i].setAttribute("disabled", "");
            }
        } else {
            array = result.split('#');

            document.getElementById("title").value = array[1];
            document.getElementById("title").removeAttribute("disabled");

            document.getElementById("series").value = array[2];
            document.getElementById("series").removeAttribute("disabled");

            document.getElementById("date_published").value = array[3];
            document.getElementById("date_published").removeAttribute("disabled");

            document.getElementById("stock").value = array[4];
            document.getElementById("stock").removeAttribute("disabled");

            document.getElementById("pages").value = array[5];
            document.getElementById("pages").removeAttribute("disabled");

            document.getElementById("weight").value = array[6];
            document.getElementById("weight").removeAttribute("disabled");

            document.getElementById("publisher").value = array[7];
            document.getElementById("publisher").removeAttribute("disabled");

            document.getElementById("covertype").value = array[8];
            document.getElementById("covertype").removeAttribute("disabled");

            document.getElementById("language").value = array[9];
            document.getElementById("language").removeAttribute("disabled");

            document.getElementById("description").value = array[10];
            document.getElementById("description").removeAttribute("disabled");

            const genres = array[11].split('@');

            let genreFields = document.getElementsByClassName("genre-field");
            genreFields[0].value = genres[0];
            genreFields[0].removeAttribute("disabled");
            for (let i = genreFields.length; i < genres.length; i++) {
                addField('genre');
                genreFields = document.getElementsByClassName("genre-field");
                genreFields[i].value = genres[i];
            }

            const writers = array[12].split('@');
            let writerFields = document.getElementsByClassName("writer-field");
            writerFields[0].value = writers[0];
            writerFields[0].removeAttribute("disabled");
            for (let i = writerFields.length; i < writers.length; i++) {
                addField('writer');
                writerFields = document.getElementsByClassName("writer-field");
                writerFields[i].value = writers[i];
            }

            document.getElementById("price").value = array[13];
            document.getElementById("price").removeAttribute("disabled");

            document.getElementById("discounted_price").value = array[14];
            document.getElementById("discounted_price").removeAttribute("disabled");

            const buttons = document.getElementsByClassName("form-button");

            for (let i = 0; i < buttons.length; i++) {
                buttons[i].removeAttribute("disabled");
            }

            document.getElementById("cover").removeAttribute("disabled");
        }
    }

    xmlhttp.open("GET", `/pages/ajax/bookdata.php?isbn=${isbn}`);
    xmlhttp.send();
}

function addToBasket(isbn, title) {
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function () {
        const result = this.responseText;
        document.getElementById("info").innerText = result;
    }
    xmlhttp.open("GET", `/pages/ajax/addtobasket.php?title=${title}&isbn=${isbn}`);
    xmlhttp.send();
}

function changeBasketValue(isbn, value) {
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function () {
        const result = this.responseText;
        document.getElementById("sum-table").innerHTML = result;
    }
    xmlhttp.open("GET", `/pages/ajax/changebasketvalue.php?isbn=${isbn}&value=${value}`);
    xmlhttp.send();
}

function updateModal(isbn) {
    document.getElementById("delete-button").setAttribute("onclick", `changeBasketValue(${isbn}, -1)`);
}

function changePill(pill) {
    if (pill.classList.contains("pill-inactive")) {
        pill.classList.remove("pill-inactive");
        pill.classList.add("pill-active");

        const xmlhttp = new XMLHttpRequest();
        xmlhttp.open("GET", `/pages/ajax/preference.php?genre=${pill.value}&mode=add`);
        xmlhttp.send();
    } else {
        pill.classList.remove("pill-active");
        pill.classList.add("pill-inactive");

        const xmlhttp = new XMLHttpRequest();
        xmlhttp.open("GET", `/pages/ajax/preference.php?genre=${pill.value}&mode=remove`);
        xmlhttp.send();
    }
}

function editProfile() {
    document.getElementById("edit-button").classList.add("d-none");
    document.getElementById("save-button").classList.remove("d-none");

    const labels = document.getElementsByClassName("data-label");
    for (let i = 0; i < labels.length; i++) {
        labels[i].classList.add("d-none");
    }

    const inputs = document.getElementsByClassName("data-input");
    for (let i = 0; i < inputs.length; i++) {
        inputs[i].classList.remove("d-none");
    }
}

function saveProfile() {
    let error = false;

    let familyName = document.getElementById("family_name").value;
    let givenName = document.getElementById("given_name").value;

    if ((familyName.length == 0 && givenName.length != 0) || (familyName.length != 0 && givenName.length == 0)) {
        document.getElementById("name-error").innerText = "Kérem vagy mindkét mezőt töltse ki, vagy mindkettőt hagyja üresen!";
        error = true;
    } else {
        document.getElementById("name-error").innerText = "";
    }

    if (familyName.length == 0 && givenName.length == 0) {
        familyName = null;
        givenName = null;
    }

    let gender = null;
    if (document.getElementById("male").checked) {
        gender = "male";
    } else if (document.getElementById("female").checked) {
        gender = "female";
    }

    let birthdate = document.getElementById("birthdate").value;
    if (new Date(birthdate) > new Date()) {
        document.getElementById("birthdate-error").innerText = "Kérem ne jövőbeli dátumot adjon meg!";
        error = true;
    } else {
        document.getElementById("birthdate-error").innerText = "";
    }

    if (birthdate.length == 0) {
        birthdate = null;
    }

    let phoneNumber = document.getElementById("phone_number").value.replaceAll(' ', '');
    const phoneRegex = /^\+?\d{11}$/;
    if (phoneNumber.length != 0 && !phoneRegex.test(phoneNumber)) {
        document.getElementById("phone-number-error").innerText = "Kérem érvényes telefonszámot adjon meg +36123456789 vagy 06123456789 formátumban!";
        error = true;
    } else {
        document.getElementById("phone-number-error").innerText = "";
    }

    if (phoneNumber.length == 0) {
        phoneNumber = null;
    }

    if (error) return;

    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function () {
        window.location.href = window.location.href;
    }
    xmlhttp.open("GET", `/pages/ajax/saveuserdata.php?family_name=${familyName}&given_name=${givenName}&gender=${gender}&birthdate=${birthdate}&phone_number=${phoneNumber}`);
    xmlhttp.send();
}

function makeAddressFormVisible() {
    document.getElementById("new-address-form").classList.remove("d-none");
}

function saveAddress() {
    let error = false;

    let company = document.getElementById("company").value;
    if (company.length == 0) {
        company = null;
    }

    let county = document.getElementById("county").value;

    let city = document.getElementById("city").value;
    if (city.length == 0) {
        document.getElementById("city-error").innerText = "Kérem adjon meg egy várost!";
        error = true;
    }

    let publicSpace = document.getElementById("public-space").value;
    if (publicSpace.length == 0) {
        document.getElementById("public-space-error").innerText = "Kérem adjon meg egy közterületet!";
        error = true;
    }

    let zipCode = document.getElementById("zip-code").value;
    const zipCodeRegex = /^\d{4}$/;
    if (!zipCodeRegex.test(zipCode)) {
        document.getElementById("zip-code-error").innerText = "Kérem adjon meg egy érvényes irányítószámot!";
        error = true;
    }

    let note = document.getElementById("note").value;
    if (note.length == 0) {
        note = null;
    }

    if (error) return;

    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function () {
        window.location.href = window.location.href;
    }
    xmlhttp.open("GET", `/pages/ajax/saveaddress.php?company=${company}&county=${county}&city=${city}&public_space=${publicSpace}&zip_code=${zipCode}&note=${note}`);
    xmlhttp.send();
}

function deleteAddressCon(address_id) {
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", `/pages/ajax/deleteaddresscon.php?address_id=${address_id}`);
    xmlhttp.send();
}

function onAddressChoose(index) {
    
    if (document.getElementById(`company-${index}`) !== null)
        document.getElementById('company').value = document.getElementById(`company-${index}`).innerText;
    else
        document.getElementById('company').value = "";
    document.getElementById('county').value = document.getElementById(`county-code-${index}`).innerText;
    document.getElementById('city').value = document.getElementById(`city-${index}`).innerText;
    document.getElementById('public-space').value = document.getElementById(`public-space-${index}`).innerText;
    document.getElementById('zip-code').value = document.getElementById(`zip-code-${index}`).innerText;
    if (document.getElementById(`note-${index}`) !== null)
        document.getElementById('note').value = document.getElementById(`note-${index}`).innerText;
    else
        document.getElementById('note').value = "";

    document.getElementById('submit').classList.remove('disabled');
}

function validateAddress() {
    let error = false;

    let company = document.getElementById("company").value;
    if (company.length == 0) {
        company = null;
    }

    let county = document.getElementById("county").value;

    let city = document.getElementById("city").value;
    if (city.length == 0) {
        document.getElementById("city-error").innerText = "Kérem adjon meg egy várost!";
        error = true;
    } else {
        document.getElementById("city-error").innerText = "";
    }

    let publicSpace = document.getElementById("public-space").value;
    if (publicSpace.length == 0) {
        document.getElementById("public-space-error").innerText = "Kérem adjon meg egy közterületet!";
        error = true;
    } else {
        document.getElementById("public-space-error").innerText = "";
    }

    let zipCode = document.getElementById("zip-code").value;
    const zipCodeRegex = /^\d{4}$/;
    if (!zipCodeRegex.test(zipCode)) {
        document.getElementById("zip-code-error").innerText = "Kérem adjon meg egy érvényes irányítószámot!";
        error = true;
    } else {
        document.getElementById("zip-code-error").innerText = "";
    }

    let note = document.getElementById("note").value;
    if (note.length == 0) {
        note = null;
    }

    if (error) {
        document.getElementById("submit-button").classList.add("disabled");
    } else {
        document.getElementById("submit-button").classList.remove("disabled");
    }
}