var isActivateDeleteAll = false;
var elements;
var hrefs = [];

function activateDeleteCategoryAll() {
    elements = document.getElementsByTagName("a");

    if(!isActivateDeleteAll) {
        isActivateDeleteAll = true;
        document.getElementById("button_delete_all").classList = "btn btn-danger";

        alert("Wybierz gałąź, którą chcesz skasować.");

        deactivateA();
    }
    else {
        isActivateDeleteAll = false;
        document.getElementById("button_delete_all").classList = "btn btn-dark";

        activateA();
    }
}

function deactivateA() {
    for(var i = 0; i < elements.length; i++){
        hrefs[i] = elements[i].href;
        elements[i].href = "javascript:void(0)";
    }
}

function activateA(){
    for(var i = 0; i < elements.length; i++){
        elements[i].href = hrefs[i];
        firstElement = secondElement = null;
    }
}

function deleteCategoryAll(id) {
    if(id == 1 && isActivateDeleteAll) {
        alert("Nie możesz tego usunąć!");
        location.reload();
        return null;
    }

    if(isActivateDeleteAll)
        sendDataDelete(id);
}

function sendDataDelete(id) {   console.log("dupa");
    var http = new XMLHttpRequest();
    http.open("DELETE", "/tree/delete-branch/" + id, true);
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.send();

    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
        }
        else if (this.readyState == 4 && this.status != 200){
            alert("Wystąpił błąd!");
        }
    };
}