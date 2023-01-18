const rightButton = document.getElementById('ptk-rightBttnId');
const leftButton = document.getElementById('ptk-leftBttnId');

rightButton.onclick = () => {
    document.getElementById('ptk-cardsContainerId').scrollLeft += 230;
};

leftButton.onclick = () => {
    document.getElementById('ptk-cardsContainerId').scrollLeft -= 230;
};
