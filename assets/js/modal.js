let modals = document.querySelectorAll(".ptk-modalContainer");
let showModalsLink = document.querySelectorAll(".ptk-modalShow");
let hideModalsLink = document.querySelectorAll(".ptk-formCloseBttn");

hideModalsLink.forEach((hlink, index) => {
    hlink.addEventListener("click", function(e) {
        // console.log(hideModalsLink);
    });
})

showModalsLink.forEach((link, index) => {// index will be current button index
    link.addEventListener("click", function(e) {
        console.log(showModalsLink);
    });
})



