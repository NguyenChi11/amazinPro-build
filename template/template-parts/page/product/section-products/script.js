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

  function makeURL(pageUrl, qVal) {
    const url = new URL(pageUrl, window.location.origin);
    const params = url.searchParams;
    const q = (qVal || "").trim();
    if (q) {
      params.set("q", q);
    } else {
      params.delete("q");
    }
    params.delete("paged");
    params.delete("page");
    url.search = params.toString();
    return url.toString();
  }

  function bindPagination() {
    const anchors = listWrap.querySelectorAll(".product--pagination a");
    anchors.forEach((a) => {
      a.addEventListener("click", function (e) {
        e.preventDefault();
        const url = a.getAttribute("href");
        if (!url) return;
        updateList(url, true);
      });
    });
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
      const top = listWrap.getBoundingClientRect().top + window.scrollY - 80;
      window.scrollTo({ top, behavior: "smooth" });
    } catch (_) {}
  }

  bindPagination();

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
