document.addEventListener("DOMContentLoaded", function () {
  // Swiper
  if (typeof Swiper !== "undefined") {
    new Swiper(".section-portfolio__swiper", {
      slidesPerView: 3,
      spaceBetween: 20,
      loop: true,
      navigation: {
        nextEl: ".section-portfolio__swiper-next",
        prevEl: ".section-portfolio__swiper-prev",
      },
      breakpoints: {
        0: { slidesPerView: 1 },
        640: { slidesPerView: 2 },
        1024: { slidesPerView: 3 },
      },
    });
  }
});
