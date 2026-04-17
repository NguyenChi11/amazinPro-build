document.addEventListener("DOMContentLoaded", function () {
  var gallery = document.querySelector(".single-product__gallery");
  if (!gallery || typeof Swiper === "undefined") {
    return;
  }

  var thumbsElement = gallery.querySelector(".thumbs-swiper");
  var mainElement = gallery.querySelector(".main-swiper");

  if (!thumbsElement || !mainElement) {
    return;
  }

  if (
    thumbsElement.classList.contains("swiper-initialized") ||
    mainElement.classList.contains("swiper-initialized")
  ) {
    return;
  }

  var totalSlides = mainElement.querySelectorAll(".swiper-slide").length;

  var thumbsSwiper = new Swiper(thumbsElement, {
    spaceBetween: 8,
    slidesPerView: Math.min(4, Math.max(1, totalSlides)),
    watchSlidesProgress: true,
    direction: "vertical",
    breakpoints: {
      0: {
        direction: "horizontal",
        slidesPerView: Math.min(4, Math.max(1, totalSlides)),
        freeMode: false,
        spaceBetween: 8,
      },
      640: {
        direction: "vertical",
        slidesPerView: Math.min(5, Math.max(1, totalSlides)),
        freeMode: true,
        spaceBetween: 10,
      },
    },
  });

  new Swiper(mainElement, {
    loop: false,
    spaceBetween: 10,
    speed: 500,
    thumbs: {
      swiper: thumbsSwiper,
    },
  });
});
