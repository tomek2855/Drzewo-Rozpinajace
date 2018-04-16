var isActivate = false;
var elements;
var hrefs = [];

function activateMove(){
    elements = document.getElementsByTagName("a");

    if(!isActivate) {
        isActivate = true;
        document.getElementById("button_activate").classList = "btn btn-danger";

        alert("Wybierz kategorię, którą chcesz przenieść a następnie kliknij na kategorię, do której ma należeć.");

        deactivateA();
    }
    else {
        isActivate = false;
        document.getElementById("button_activate").classList = "btn btn-dark";

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

var firstElement = null, secondElement = null;

function moveElements(id) {
    console.log(id);

    if(firstElement != null && secondElement == null){
        secondElement = id;

        if(firstElement != secondElement) {
            sendData();
        }
        else {
            alert("Nie możesz wybrać dwa razy tej samej kategorii!");
            firstElement = secondElement = null;
            activateA();
            isActivate = false;
            document.getElementById("button_activate").classList = "btn btn-dark";
        }
    }

    if(firstElement == null && secondElement == null){
        firstElement = id;
    }

    return false;
}

function sendData() {
    var http = new XMLHttpRequest();
    http.open("POST", "/tree/move", true);
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.send("leaf_id=" + firstElement + "&new_parent_id=" + secondElement);

    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            firstElement = secondElement = null;
            location.reload();
        }
        else if (this.readyState == 4 && this.status != 200){
            alert("Wystąpił błąd!");
        }
    };
}