const preloader = document.querySelector('.ptk-loaderContainer');
// if (preloader) {
window.addEventListener('load', () => {
    console.log('Loading')
    setTimeout(() => {
        preloader.classList.add('loader-hidden');
        }, 1000);
        setTimeout(() => {
            preloader.remove();
        }, 2000);
    });
// }
