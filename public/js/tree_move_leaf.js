var isActivateMove = false;
var elements;
var hrefs = [];

function activateMove(){
    elements = document.getElementsByTagName("a");

    if(!isActivateMove) {
        isActivateMove = true;
        document.getElementById("button_move").classList = "btn btn-danger";

        alert("Wybierz kategorię, którą chcesz przenieść a następnie kliknij na kategorię, do której ma należeć.");

        deactivateA();
    }
    else {
        isActivateMove = false;
        document.getElementById("button_move").classList = "btn btn-dark";

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

        if(firstElement != secondElement && isActivateMove) {
            sendDataMove();
        }
        else {
            alert("Nie możesz wybrać dwa razy tej samej kategorii!");
            firstElement = secondElement = null;
            activateA();
            isActivateMove = false;
            document.getElementById("button_move").classList = "btn btn-dark";
        }
    }

    if(firstElement == null && secondElement == null){
        firstElement = id;

        if(firstElement == 1 && isActivateMove){
            alert("Nie możesz przenieść tego elementu!");
            location.reload();
        }
    }

    return false;
}

function sendDataMove() {
    var http = new XMLHttpRequest();
    http.open("POST", "/category/move", true);
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