document.addEventListener("DOMContentLoaded", function () {
  const left = document.querySelector(".blog-section-blog__left");
  if (!left) return;
  const list = left.querySelector(".section-post__list");
  const pagination = left.querySelector(".product--pagination");
  if (!list) return;

  function scrollToList() {
    const top = list.getBoundingClientRect().top + window.scrollY - 80;
    window.scrollTo({ top, behavior: "smooth" });
  }

  const params = new URLSearchParams(window.location.search);
  if (params.has("paged") || params.has("page")) {
    scrollToList();
  }
  if (sessionStorage.getItem("bp_scroll_blog") === "1") {
    sessionStorage.removeItem("bp_scroll_blog");
    scrollToList();
  }
  if (pagination) {
    const links = pagination.querySelectorAll("a");
    links.forEach((a) => {
      a.addEventListener("click", function () {
        sessionStorage.setItem("bp_scroll_blog", "1");
      });
    });
  }
});
