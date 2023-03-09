// parentElement: div.row
// fields: div.col-12 > input field
function AddField(fieldName) {
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

function RemoveField(fieldName) {
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

function loadBookDataByIsbn(isbn) {
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function () {
        const result =  this.responseText;

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
                RemoveField("genre");
            }
            genreFields[0].value = "";
            genreFields[0].setAttribute("disabled", "");

            const writerFields = document.getElementsByClassName("writer-field");
            for (let i = writerFields.length; i > 1; i--) {
                RemoveField("writer");
            }
            writerFields[0].value = "";
            writerFields[0].setAttribute("disabled", "");

            document.getElementById("price").value = "";
            document.getElementById("price").setAttribute("disabled", "");

            document.getElementById("discounted_price").value = "";
            document.getElementById("discounted_price").setAttribute("disabled", "");

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
            for (let i = 1; i < genres.length; i++) {
                AddField('genre');
                genreFields = document.getElementsByClassName("genre-field");
                genreFields[i].value = genres[i];
            }

            const writers = array[12].split('@');
            let writerFields = document.getElementsByClassName("writer-field");
            writerFields[0].value = writers[0];
            writerFields[0].removeAttribute("disabled");
            for (let i = 1; i < writers.length; i++) {
                AddField('writer');
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

        }
        
        
        
    }

    xmlhttp.open("GET", `pages/bookdata.php?isbn=${isbn}`);
    xmlhttp.send();
}