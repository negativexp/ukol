function openPlayerForm() {
    const playerForm = document.getElementById("playerForm")
    playerForm.style.display = "unset"
}
function closePlayerForm() {
    const playerForm = document.getElementById("playerForm")
    playerForm.style.display = "none"
}

function openMatchForm() {
    const matchForm = document.getElementById("matchForm")
    matchForm.style.display = "unset"
}
function closeMatchForm(id) {
    const commentForm = document.getElementById("matchForm")
    commentForm.style.display = "none"
}

// Make the DIV element draggable:
dragElement(document.getElementById("playerForm"));
dragElement(document.getElementById("matchForm"));

function dragElement(elmnt) {
    var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    if (document.getElementById(elmnt.id + "header")) {
        /* if present, the header is where you move the DIV from:*/
        document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
    } else {
        /* otherwise, move the DIV from anywhere inside the DIV:*/
        elmnt.onmousedown = dragMouseDown;
    }

    function dragMouseDown(e) {
        e = e || window.event;
        e.preventDefault();
        // get the mouse cursor position at startup:
        pos3 = e.clientX;
        pos4 = e.clientY;
        document.onmouseup = closeDragElement;
        // call a function whenever the cursor moves:
        document.onmousemove = elementDrag;
    }

    function elementDrag(e) {
        e = e || window.event;
        e.preventDefault();
        // calculate the new cursor position:
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;
        // set the element's new position:
        var newTop = elmnt.offsetTop - pos2;
        var newLeft = elmnt.offsetLeft - pos1;
        // Ensure the window stays within the bounds of the screen
        var screenWidth = window.innerWidth;
        var screenHeight = window.innerHeight;
        var windowWidth = elmnt.offsetWidth;
        var windowHeight = elmnt.offsetHeight;
        if (newTop < 0) {
            newTop = 0;
        } else if (newTop + windowHeight > screenHeight) {
            newTop = screenHeight - windowHeight;
        }
        if (newLeft < 0) {
            newLeft = 0;
        } else if (newLeft + windowWidth > screenWidth) {
            newLeft = screenWidth - windowWidth;
        }
        elmnt.style.top = newTop + "px";
        elmnt.style.left = newLeft + "px";
    }

    function closeDragElement() {
        /* stop moving when mouse button is released:*/
        document.onmouseup = null;
        document.onmousemove = null;
    }
}