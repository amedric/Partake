// ------------------ functions for scrolling left and right --------------------------------

// querySelectorAll will return and node list of container with classname .ptk-cardsContainer
let container = document.querySelectorAll(".ptk-cardsContainer");
// querySelectorAll will return and node list of button with classname .rightBttn
let rightButton = document.querySelectorAll(".rightBttn");
// querySelectorAll will return and node list of button with classname .leftBttn
let leftButton = document.querySelectorAll(".leftBttn");

let scrollButtons = document.querySelectorAll(".ptk-arrowBttn");

rightButton.forEach((rBtn, index) => {// index will be current button index
    rBtn.addEventListener("click", function(e) {
        sideScroll(container[index],'right',10,280,10);
    });
})

leftButton.forEach((lBtn, index) => {// index will be current button index
    lBtn.addEventListener("click", function(e) {
        sideScroll(container[index],'left',10,280,10);
    });
})

// -------------------Smooth scrolling --------------------------------
function sideScroll(element,direction,speed,distance,step){
    let scrollAmount = 0;
    let slideTimer = setInterval(function(){
        if(direction == 'left'){
            element.scrollLeft -= step;
        } else {
            element.scrollLeft += step;
        }
        scrollAmount += step;
        if(scrollAmount >= distance){
            window.clearInterval(slideTimer);
        }
    }, speed);
}

// ----------------------- Draggable cards container --------------------------------
let isDown = false;
let startX;
let scrollLeft;

container.forEach((ctn, index) => {

    // --------- draggable for each container -----------
    ctn.addEventListener('mousedown', (e) => {
        isDown = true;
        ctn.classList.add('active');
        startX = e.pageX - ctn.offsetLeft;
        scrollLeft = ctn.scrollLeft;
    });
    ctn.addEventListener('mouseleave', () => {
        isDown = false;
        ctn.classList.remove('active');
    });
    ctn.addEventListener('mouseup', () => {
        isDown = false;
        ctn.classList.remove('active');
    });
    ctn.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - ctn.offsetLeft;
        const walk = (x - startX) * 3; //scroll-fast
        ctn.scrollLeft = scrollLeft - walk;
    });

    // ------- checks if container has not overflowed and hides buttons --------------------------------
    if (ctn.clientWidth < ctn.scrollWidth) {
        ctn.previousElementSibling.lastElementChild.classList.add("showButtons");
        ctn.nextElementSibling.firstElementChild.classList.add("showButtons");
    }
})
