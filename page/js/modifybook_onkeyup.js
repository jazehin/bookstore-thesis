function loadBookData(isbn) {
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function () {
        const result =  this.responseText;
        const form = document.getElementById("form");
        form.innerHTML = result;
    }

    xmlhttp.open("GET", `pages/modifybookform.php?isbn=${isbn}`);
    xmlhttp.send();
}