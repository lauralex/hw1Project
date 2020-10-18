async function validate(event) {
    /*
    const buttonSubmitter = event.submitter;
    console.log(buttonSubmitter);
    if (buttonSubmitter.name == "image_submit") {
        event.preventDefault();
        return;
    }

    console.log(event.currentTarget);
    */
    const formElements = register_form.elements;
    const nameVal = formElements["nome"].value
    const surnameVal = formElements["cognome"].value;
    const emailVal = formElements["email"].value;
    const userNameVal = formElements["username"].value;
    const passwordVal = formElements["password"].value;
    const passwordConfirmVal = formElements["password_confirm"].value;

    for (let control of formElements) {
        if (control.name == 'image_url') continue;
        if (control.name == 'file_upload' && control.value == '' && formElements['image_url'].value != '') continue;
        if (control.value == "") {
            alert("Inserire tutti i campi");
            event.preventDefault();
            return;
        }

    }
    if (!emailVal.match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/)) {
        alert("Formato email non valido");
        event.preventDefault();
        return;
    }
    if (passwordVal != passwordConfirmVal) {
        alert("Errore conferma password");
        event.preventDefault();
    }

}
/*
function imageSubmit(event) {
    event.preventDefault();

}
*/

if (respCode != '') {
    alert(respCode);
    respCode = '';
}

let usernameFound = false;
function onBlur(event) {
    const username = register_form.elements["username"].value;


    function onResponse(response) {
        if (!response.ok) {
            return Promise.reject();
        }
        return response.text();
    }

    function checkFound(text) {
        if (text == 'found') {
            alert("Username found!");
            usernameFound = true;
        } else {
            usernameFound = false;
        }
    }
    fetch(`${uri}?searchUser=${username}`).then(onResponse).then(checkFound).catch((error) => alert(error));
}
// popular image types (Some of them at least :))
const fileTypes = [
    "image/apng",
    "image/bmp",
    "image/gif",
    "image/jpeg",
    "image/pjpeg",
    "image/png",
    "image/svg+xml",
    "image/tiff",
    "image/webp",
    "image/x-icon"
];

function FilterFileType(file) {
    return fileTypes.includes(file.type);
}

function returnFileSize(number) {
    if(number < 1024) {
        return number + 'bytes';
    } else if(number >= 1024 && number < 1048576) {
        return (number/1024).toFixed(2) + 'KB';
    } else if(number >= 1048576) {
        return (number/1048576).toFixed(2) + 'MB';
    }
}


function uploadHandler(event) {
    prev.innerHTML = '';

    const currentFiles = image_upload.files;
    if (currentFiles.length === 0) {
        const parag = document.createElement('p');
        parag.textContent = 'No files selected';
        prev.append(parag);
        return;
    } else {
        const parag = document.createElement('p');

        if (FilterFileType(currentFiles[0])) {
            parag.textContent = `Name: ${currentFiles[0].name}, Size: ${returnFileSize(currentFiles[0].size)}.`;
            const image = document.createElement('img');
            image.src = URL.createObjectURL(currentFiles[0]);
            prev.append(parag, image);
        } else {
            parag.textContent = `Name: ${currentFiles[0].name} is not valid. Sorry.`;
            prev.append(parag);
            return;
        }
    }


    function handleFileUploadRes(res) {
        if (!res.ok) {
            return Promise.reject("Bad Response!");
        }

        return res.text();
    }

    function handleTextRes(txt) {
        alert(txt);
    }


    const myFileUpload = new FormData();
    myFileUpload.append('file_to_upload', currentFiles[0]);

    const options = {
        method: 'POST',
        body: myFileUpload
    }

    fetch('./upload.php', options).then(handleFileUploadRes).then(handleTextRes).catch((error) => alert(error));

}


const register_form = document.forms["register"];
//register_form.elements["image_submit"].addEventListener("submit", imageSubmit);
register_form.addEventListener("submit", validate);
register_form["username"].addEventListener("blur", onBlur);
const image_upload = register_form['file_upload'];
image_upload.addEventListener("change", uploadHandler);
image_upload.style.opacity = "0";
const prev = document.body.querySelector(".prev");