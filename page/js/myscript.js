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