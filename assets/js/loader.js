const preloader = document.querySelector('.ptk-loaderContainer');
if (preloader) {
    window.addEventListener('load', () => {
        setTimeout(() => {
            preloader.classList.add('loaded');
        }, 1000);
        setTimeout(() => {
            preloader.remove();
        }, 2000);
    });
}
