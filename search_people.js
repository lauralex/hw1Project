function populateUsers(info) {
    const flexContainer = document.body.querySelector(".row");
    const column = document.createElement('div');
    column.classList.add("col-sm", "mb-4");

    const card = document.createElement('div');
    card.classList.add("card");

    const clickableCard = document.createElement("a");
    clickableCard.href = "javascript:void(0);";
    clickableCard.addEventListener("click", followHandler);
    clickableCard.dataset.id = info['UID'];
    clickableCard.dataset.followed = info['followed'];

    const image = document.createElement('img');
    image.classList.add("card-img-top");
    image.src = info['foundImage'];


    const clickableImage = document.createElement("a");
    clickableImage.href = "javascript:void(0);";

    const cardBody = document.createElement('div');
    cardBody.classList.add("card-body")

    const cardTitle = document.createElement('h5');
    cardTitle.classList.add("card-title");
    cardTitle.textContent = info['foundUser'];

    const cardFooter = document.createElement('div');
    cardFooter.classList.add('card-footer');

    const footerText = document.createElement('small');
    footerText.textContent = info['followed'] == true ? 'Followed' : 'Not followed';
    footerText.classList.add('footerText');

    flexContainer.append(column);
    column.append(clickableCard);
    clickableCard.append(card);
    card.append(clickableImage, cardBody, cardFooter);
    clickableImage.append(image);
    cardBody.append(cardTitle);
    cardFooter.append(footerText);
    flexContainer.classList.remove("d-none");

}
let selectedUser, followBool;
function follow_modal_handler(event) {
    event.preventDefault();
    const UID = selectedUser.dataset.id;

    async function followRespHandler(followResp) {
        if (!followResp.ok) {
            return Promise.reject("Bad Response!");
        }
        return followResp.text();
    }
    function textRespHandler(textResp) {
        alert(textResp);
        $('#follow_modal').modal('hide');
        selectedUser.dataset.followed = followBool;
        selectedUser.querySelector('.footerText').textContent = followBool ? 'Followed' : 'Not followed';
    }

    fetch(`./search_people.php?follow=${UID}`).then(followRespHandler).then(textRespHandler).catch((error)=>alert(error));

}

function switchModal() {
    if (selectedUser.dataset.followed == "true") {
        document.body.querySelector('.modal-title').textContent = 'Do you really want to unfollow this user?';
        followBool = false;
    } else {
        document.body.querySelector('.modal-title').textContent = 'Do you want to follow this user?';
        followBool = true;
    }
}

function followHandler(event) {
    event.preventDefault();
    selectedUser = event.currentTarget;
    switchModal();

    $('#follow_modal').modal('show');



}


function searchSubmit(event) {
    event.preventDefault();
    if (document.body.querySelector(".row div")) {
        document.body.querySelector(".row").innerHTML = "";
    }
    const searchedUser = encodeURIComponent(search_user_form['searchedUser'].value);

    async function searchedUserHandler(searchResp) {
        if (!searchResp.ok) {
            return Promise.reject("Bad Response");
        }
        return searchResp.json();
    }

    function jsonRespHandler(jsonResp) {
        console.log(jsonResp);
        for (let i = 0; i < jsonResp.length; i++) {
            populateUsers(jsonResp[i]);
        }
    }
    $('input').blur();
    fetch(`./do_search_people.php?searchedUser=${searchedUser}`).then(searchedUserHandler).then(jsonRespHandler).catch((error) => alert(error));

}

function search_all_handler(event) {
    event.preventDefault();
    if (document.body.querySelector(".row div")) {
        document.body.querySelector(".row").innerHTML = "";
    }

    async function getUsersHandler(searchResp) {
        if (!searchResp.ok) {
            return Promise.reject("Bad Response");
        }
        return searchResp.json();
    }

    function jsonRespHandler(jsonResp) {
        //console.log(jsonResp);
        for (let i = 0; i < jsonResp.length; i++) {
            populateUsers(jsonResp[i]);
        }
    }
    $('input').blur();
    fetch(`./do_search_people.php?getAll=`).then(getUsersHandler).then(jsonRespHandler).catch((error)=>alert(error));

}

const search_user_form = document.forms['search_user'];
search_user_form.addEventListener('submit', searchSubmit);
const followModal = document.body.querySelector('#follow_modal');
followModal.addEventListener("submit", follow_modal_handler);
const search_all = document.body.querySelector('#search_all');
search_all.addEventListener('click', search_all_handler);