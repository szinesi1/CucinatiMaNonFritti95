let currentSlide = 0;

function showSlide(index) {
    const items = document.querySelectorAll('.carousel-item');
    const total = items.length;

    if (index >= total) currentSlide = 0;
    else if (index < 0) currentSlide = total - 1;
    else currentSlide = index;

    document.querySelector('.carousel-inner').style.transform =
        `translateX(-${currentSlide * 100}%)`;
}

function nextSlide() {
    showSlide(currentSlide + 1);
}

function prevSlide() {
    showSlide(currentSlide - 1);
}
