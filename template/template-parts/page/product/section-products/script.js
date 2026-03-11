function debounce(fn, wait) {
  let t;
  return function (...args) {
    clearTimeout(t);
    t = setTimeout(() => fn.apply(this, args), wait);
  };
}

document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector(".psp-search");
  const input = document.getElementById("psp-search-input");
  const listWrap = document.querySelector(
    ".product-section-products__product--list",
  );
  if (!form || !input || !listWrap) return;

  // Scroll to product list (like blog pagination behavior)
  function scrollToList() {
    const target = listWrap.querySelector(".section-product__list") || listWrap;
    const top = target.getBoundingClientRect().top + window.scrollY - 80;
    window.scrollTo({ top, behavior: "smooth" });
  }

  const params = new URLSearchParams(window.location.search);
  if (params.has("prod_p")) {
    scrollToList();
  }
  if (sessionStorage.getItem("bp_scroll_product") === "1") {
    sessionStorage.removeItem("bp_scroll_product");
    scrollToList();
  }

  // Pagination: full-page reload + sessionStorage scroll (like blog)
  function bindPagination() {
    const pg = listWrap.querySelector(".product--pagination");
    if (!pg) return;
    pg.querySelectorAll("a").forEach((a) => {
      a.addEventListener("click", function () {
        sessionStorage.setItem("bp_scroll_product", "1");
      });
    });
  }

  bindPagination();

  // Search: AJAX update (preserve filter state, reset to page 1)
  function makeURL(pageUrl, qVal) {
    const url = new URL(pageUrl, window.location.origin);
    const prms = url.searchParams;
    const q = (qVal || "").trim();
    if (q) {
      prms.set("q", q);
    } else {
      prms.delete("q");
    }
    prms.delete("prod_p");
    url.search = prms.toString();
    return url.toString();
  }

  async function updateList(targetUrl, push) {
    try {
      const res = await fetch(targetUrl, {
        headers: { "X-Requested-With": "XMLHttpRequest" },
        credentials: "same-origin",
      });
      const html = await res.text();
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, "text/html");
      const nextWrap = doc.querySelector(
        ".product-section-products__product--list",
      );
      if (!nextWrap) return;
      listWrap.innerHTML = nextWrap.innerHTML;
      if (push) {
        history.pushState({}, "", targetUrl);
      } else {
        history.replaceState({}, "", targetUrl);
      }
      bindPagination();
    } catch (_) {}
  }

  const onType = debounce(() => {
    const url = makeURL(window.location.href, input.value);
    updateList(url, false);
  }, 350);
  input.addEventListener("input", onType);

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    const url = makeURL(
      form.getAttribute("action") || window.location.href,
      input.value,
    );
    updateList(url, true);
  });
});
