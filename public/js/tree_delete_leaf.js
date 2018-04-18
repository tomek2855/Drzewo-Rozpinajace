var isActivateDelete = false;
var elements;
var hrefs = [];

function activateDeleteCategory() {
    elements = document.getElementsByTagName("a");

    if(!isActivateDelete) {
        isActivateDelete = true;
        document.getElementById("button_delete").classList = "btn btn-danger";

        alert("Wybierz kategorię, którą chcesz skasować.");

        deactivateA();
    }
    else {
        isActivateDelete = false;
        document.getElementById("button_delete").classList = "btn btn-dark";

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

function deleteCategory(id) {
    if(id == 1 && isActivateDelete) {
        alert("Nie możesz tego usunąć!");
        location.reload();
        return null;
    }

    if(isActivateDelete)
        sendDataDelete(id);
}

function sendDataDelete(id) {
    var http = new XMLHttpRequest();
    http.open("DELETE", "/category/delete-category/" + id, true);
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.send();

    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            window.location.replace("/category/show-children");
        }
        else if (this.readyState == 4 && this.status != 200){
            alert("Wystąpił błąd!");
        }
    };
}