const slides = document.querySelector('.slides');
const totalSlides = document.querySelectorAll('.slide').length;
const prevButton = document.querySelector('.prev');
const nextButton = document.querySelector('.next');

let index = 0;
let interval = setInterval(nextSlide, 3000);

function showSlide(i) {
    index = (i + totalSlides) % totalSlides;
    slides.style.transform = `translateX(-${index * 100}%)`;
}
function nextSlide() {
    showSlide(index + 1);
}

function prevSlide() {
    showSlide(index - 1);
}
nextButton.addEventListener('click', () => {
    nextSlide();
    resetInterval();
});

prevButton.addEventListener('click', () => {
    prevSlide();
    resetInterval();
});

function resetInterval() {
    clearInterval(interval);
    interval = setInterval(nextSlide, 3000);
}
