
function loginSub(event) {

    if (loginForm.elements["username"].value.trim() == "" || loginForm.elements["password"].value == "") {
        event.preventDefault();
        alert("Inserire tutti i campi");
    }
}




const loginForm = document.body.querySelector("#login");
loginForm.addEventListener("submit", loginSub);