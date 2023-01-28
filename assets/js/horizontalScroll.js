// ------------------ functions for scrolling left and right --------------------------------

// quaryselectorall will return and nodelist of container with classnaem .ptk-cardsContainer
let container = document.querySelectorAll(".ptk-cardsContainer");
// quaryselectorall will return and nodelist of button with classnaem .rightBttn
let rightButton = document.querySelectorAll(".rightBttn");
// quaryselectorall will return and nodelist of button with classnaem .leftBttn
let leftButton = document.querySelectorAll(".leftBttn");

rightButton.forEach((rBtn, index) => {// index will be current button index
    rBtn.addEventListener("click", function(e) {
        sideScroll(container[index],'right',10,280,10);
        console.log(e.target, index);
    });
})

leftButton.forEach((lBtn, index) => {// index will be current button index
    lBtn.addEventListener("click", function(e) {
        sideScroll(container[index],'left',10,280,10);
        console.log(e.target, index);
    });
})

// -------------------Smooth scrolling --------------------------------
function sideScroll(element,direction,speed,distance,step){
    scrollAmount = 0;
    var slideTimer = setInterval(function(){
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
        console.log(walk);
    });
})
