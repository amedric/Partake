let flashMessages = document.querySelectorAll(".ptk-flashMessages");

function fadeOutEffect() {
    flashMessages.forEach((msgs, index) => {// index will be current button index

        let fadeEffect = setInterval(function () {
            if (!msgs.style.opacity) {
                msgs.style.opacity = 1;
            }
            if (msgs.style.opacity > 0) {
                msgs.style.opacity = 0;
            }
        }, 3500);
    })
}

window.onload = fadeOutEffect;
