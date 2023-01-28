// ------------------ functions for scrolling left and right --------------------------------
let rightButton = document.querySelectorAll(".rightBttn"); // quaryselectorall will return and nodelist of button with classnaem .rightBttn
let container = document.querySelectorAll(".ptk-cardsContainer");
rightButton.forEach((rBtn, index) => {// index will be current button index
    rBtn.addEventListener("click", function(e) {
        sideScroll(container[index],'right',10,280,10);
        console.log(e.target, index);
    });
})

let leftButton = document.querySelectorAll(".leftBttn"); // quaryselectorall will return and nodelist of button with classnaem .rightBttn
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
const slider = document.querySelector('.ptk-cardsContainer');
let isDown = false;
let startX;
let scrollLeft;

slider.addEventListener('mousedown', (e) => {
    isDown = true;
    slider.classList.add('active');
    startX = e.pageX - slider.offsetLeft;
    scrollLeft = slider.scrollLeft;
});
slider.addEventListener('mouseleave', () => {
    isDown = false;
    slider.classList.remove('active');
});
slider.addEventListener('mouseup', () => {
    isDown = false;
    slider.classList.remove('active');
});
slider.addEventListener('mousemove', (e) => {
    if(!isDown) return;
    e.preventDefault();
    const x = e.pageX - slider.offsetLeft;
    const walk = (x - startX) * 3; //scroll-fast
    slider.scrollLeft = scrollLeft - walk;
    console.log(walk);
});
