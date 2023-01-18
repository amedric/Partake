// ------------------ functions for scrolling left and right --------------------------------
var rightButton = document.getElementById('ptk-rightBttnId');
rightButton.onclick = function () {
    var container = document.getElementById('ptk-cardsContainerId');
    sideScroll(container,'right',10,280,10);
};

var leftButton = document.getElementById('ptk-leftBttnId');
leftButton.onclick = function () {
    var container = document.getElementById('ptk-cardsContainerId');
    sideScroll(container,'left',10,280,10);
};

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
