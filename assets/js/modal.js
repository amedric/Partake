let modals = document.querySelectorAll(".ptk-modalContainer");
let showModalsLink = document.querySelectorAll(".ptk-modalShow");
let hideModalsLink = document.querySelectorAll(".ptk-formCloseBttn");
let formLoad = document.querySelectorAll(".ptk-newProjectFormContainer");
let formClose = document.querySelectorAll(".ptk-formBttns");

hideModalsLink.forEach((hlink, index) => {
    hlink.addEventListener("click", function(e) {
        modals[0].classList.remove("ptk-modalContainerShow");
        formLoad[0].classList.remove("ptk-formContainerShow");

    });
})

showModalsLink.forEach((link, index) => {// index will be current button index
    link.addEventListener("click", function(e) {
        modals[0].classList.add("ptk-modalContainerShow");
        formLoad[0].classList.add("ptk-formContainerShow");
        console.log(link);
    });
})



