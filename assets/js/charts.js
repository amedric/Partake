document.getElementById("switch1").addEventListener("click", switchProjectCanvas);
function switchProjectCanvas() {
    const canvas1 = document.getElementById('canvas1');
    const canvas2 = document.getElementById('canvas2');
    if (canvas1.classList.contains("visible")) {
        canvas1.classList.replace("visible", "hidden");
        canvas2.classList.replace("hidden", "visible");
    }
    else {
        canvas1.classList.replace("hidden", "visible");
        canvas2.classList.replace("visible", "hidden");
    }
}

document.getElementById("switch2").addEventListener("click", switchIdeaCanvas);
function switchIdeaCanvas() {
    const canvas3 = document.getElementById('canvas3');
    const canvas4 = document.getElementById('canvas4');
    if (canvas3.classList.contains("visible")) {
        canvas3.classList.replace("visible", "hidden");
        canvas4.classList.replace("hidden", "visible");
    }
    else {
        canvas3.classList.replace("hidden", "visible");
        canvas4.classList.replace("visible", "hidden");
    }
}


