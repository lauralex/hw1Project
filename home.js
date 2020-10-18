function populatePosts(info) {
    const flexContainer = document.body.querySelector(".row");
    const column = document.createElement('div');
    column.classList.add("col-sm", "mb-4");

    const card = document.createElement('div');
    card.classList.add("card");

    const image = document.createElement('img');
    image.classList.add("card-img-top");
    image.src = info['postUrl'];


    const clickableImage = document.createElement("a");
    clickableImage.href = info['yt_url'] == null ? info['an_url'] != null ? info['an_url'] : "javascript:void(0);" : info['yt_url'];

    const cardBody = document.createElement('div');
    cardBody.classList.add("card-body")

    const cardTitle = document.createElement('h5');
    cardTitle.classList.add("card-title");
    cardTitle.textContent = info['postTitle'];

    const cardFooter = document.createElement('div');
    cardFooter.classList.add('card-footer');

    const footerText = document.createElement('small');
    footerText.textContent = info['date']+'\r\n'+info['username'];
    footerText.style.whiteSpace = 'pre';

    const flexLikeAndCounter = document.createElement("div");
    flexLikeAndCounter.classList.add("d-flex", "flex-row", "align-items-center");

    const like = document.createElement("small");
    like.textContent = info['like'] ? 'Unlike' : 'Like';
    like.classList.add("postLike");

    const clickableLike = document.createElement("a");
    clickableLike.href = 'javascript:void(0);';
    clickableLike.addEventListener('click', resolveLike);
    clickableLike.dataset.id = `${info['postId']}`;

    const likeCounter = document.createElement("small");
    likeCounter.textContent = `${info['num_likes']}`;
    likeCounter.classList.add("postLike", "likeCounter");
    likeCounter.dataset.id = `${info['postId']}`;

    const clickableCounter = document.createElement('a');
    clickableCounter.href = 'javascript:void(0);';
    clickableCounter.addEventListener('click', showModalLikers);

    flexContainer.append(column);
    column.append(card);
    card.append(clickableImage, cardBody, cardFooter);
    cardFooter.append(footerText, flexLikeAndCounter);
    flexLikeAndCounter.append(clickableLike, clickableCounter);
    clickableLike.append(like);
    clickableImage.append(image);
    clickableCounter.append(likeCounter);
    cardBody.append(cardTitle);

    flexContainer.classList.remove("d-none");
}

function showModalLikers(event) {
    const postId = event.target.dataset.id;


    function responseLikers(resp) {
        if (!resp.ok) {
            return Promise.reject("Error getting response-likers");
        }
        return resp.json();
    }

    function getLikers(likers) {
        const jqueryModal = $('#post_modal');
        jqueryModal.modal('show');

        const jqueryModalBody = jqueryModal.find('.modal-body');
        for (const likerInd in likers) {
            const row = document.createElement("div");
            row.classList.add("row", "row-cols-2", "align-items-center");

            const userName = document.createElement("div");
            userName.classList.add("col");
            userName.textContent = likers[likerInd]['username'];

            const userImageContainer = document.createElement("div");
            userImageContainer.classList.add("col");

            const userImage = document.createElement("img");
            userImage.src = likers[likerInd]['image'];
            userImage.height = 50;
            userImage.width = 50;

            row.append(userName);
            row.append(userImageContainer);
            userImageContainer.append(userImage);
            jqueryModalBody.append(row);

        }

    }


    fetch(`./home.php?likers=${postId}`).then(responseLikers).then(getLikers).catch((error)=>alert(error));
}

function resolveLike(event) {
    async function resolveResponse(resp) {
        if (!resp.ok) {
            return Promise.reject('Bad response');
        }
        return resp.text();
    }
    const postId = encodeURIComponent(event.currentTarget.dataset.id);
    function resolveResponseCode(respCode) {
        event.target.textContent = respCode == 'Like' ? 'Like' : 'Unlike';
        const counter = document.body.querySelector(`.likeCounter[data-id="${postId}"]`);
        counter.textContent = respCode == 'Like' ? (parseInt(counter.textContent) - 1).toString() : (parseInt(counter.textContent) + 1).toString();
    }



    fetch(`./home.php?resolveLike=${postId}`).then(resolveResponse).then(resolveResponseCode).catch((error) => alert(error));
}

function jsonHandler(jsonResp) {
    for (let i = 0; i < jsonResp.length; i++) {
        populatePosts(jsonResp[i]);
    }
}

function responseHandler(resp) {
    if (!resp.ok) {
        return Promise.reject("Bad Response");
    }
    return resp.json();
}

function resetModalBody(event) {
    $(event.currentTarget).find('.modal-body').html('');
}

fetch('./home.php?postList=').then(responseHandler).then(jsonHandler).catch((error) => alert(error))
$('#post_modal').on('hidden.bs.modal', resetModalBody);
