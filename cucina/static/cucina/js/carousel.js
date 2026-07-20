let currentSlide = 0;

function showSlide(index) {
    const carousel = document.getElementById('carousel');
    if (!carousel) return;

    const items = carousel.querySelectorAll('.carousel-item');
    const total = items.length;
    if (total === 0) return;

    if (index >= total) currentSlide = 0;
    else if (index < 0) currentSlide = total - 1;
    else currentSlide = index;

    const inner = carousel.querySelector('.carousel-inner');
    if (inner) {
        inner.style.transform = `translateX(-${currentSlide * 100}%)`;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const carousel = document.getElementById('carousel');
    if (!carousel) return;

    const previous = carousel.querySelector('.carousel-control.prev');
    const next = carousel.querySelector('.carousel-control.next');

    if (previous) previous.addEventListener('click', () => showSlide(currentSlide - 1));
    if (next) next.addEventListener('click', () => showSlide(currentSlide + 1));

    showSlide(0);
});
