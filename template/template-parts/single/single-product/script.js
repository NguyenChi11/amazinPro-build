document.addEventListener("DOMContentLoaded", function () {
  // Khởi tạo thumbs swiper trước
  const thumbsSwiper = new Swiper(".thumbs-swiper", {
    spaceBetween: 10,
    slidesPerView: 4, // hiển thị 4 thumbs cùng lúc (điều chỉnh tùy ý)
    freeMode: true,
    watchSlidesProgress: true, // rất quan trọng cho thumbs
    breakpoints: {
      640: { slidesPerView: 5 },
      1024: { slidesPerView: 6 },
    },
  });

  // Khởi tạo main swiper, kết nối với thumbs
  const mainSwiper = new Swiper(".main-swiper", {
    loop: true, // loop vô hạn
    spaceBetween: 10,
    navigation: {
      // tùy chọn: thêm nút prev/next
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    thumbs: {
      swiper: thumbsSwiper, // kết nối hai swiper
    },
    autoplay: {
      delay: 3000, // 3 giây = 3000ms
      disableOnInteraction: false, // vẫn autoplay sau khi user tương tác (click, swipe)
    },
    // Tùy chọn: pause khi hover (nếu muốn)
    // pauseOnMouseEnter: true,
  });
});
