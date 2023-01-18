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
