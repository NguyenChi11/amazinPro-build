function debounce(fn, wait) {
  let t;
  return function (...args) {
    clearTimeout(t);
    t = setTimeout(() => fn.apply(this, args), wait);
  };
}

document.addEventListener("DOMContentLoaded", function () {
  const root = document.querySelector(".product-section-products");
  if (!root) return;

  function qs(sel) {
    return root.querySelector(sel);
  }

  // Scroll to product list (like blog pagination behavior)
  function scrollToList() {
    const listWrap = qs(".product-section-products__product--list");
    if (!listWrap) return;
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
    const listWrap = qs(".product-section-products__product--list");
    if (!listWrap) return;
    const pg = listWrap.querySelector(".product--pagination");
    if (!pg) return;
    pg.querySelectorAll("a").forEach((a) => {
      a.addEventListener("click", function () {
        sessionStorage.setItem("bp_scroll_product", "1");
      });
    });
  }

  function buildURLFromForm(form) {
    const action = form.getAttribute("action") || window.location.href;
    const url = new URL(action, window.location.origin);
    const params = new URLSearchParams();
    const formData = new FormData(form);

    formData.forEach((rawValue, key) => {
      const value = String(rawValue).trim();
      if (value !== "") {
        params.set(key, value);
      }
    });

    // Always reset pagination when filters/search change.
    params.delete("prod_p");
    url.search = params.toString();
    return url.toString();
  }

  function syncFormFromURL(form, urlString) {
    const url = new URL(urlString, window.location.origin);
    const params = url.searchParams;

    const input = form.querySelector("#psp-search-input");
    if (input) {
      input.value = params.get("q") || "";
    }

    ["brand", "category", "tag"].forEach((name) => {
      const el = form.querySelector(`[name="${name}"]`);
      if (!el) return;
      el.value = params.get(name) || "";
    });
  }

  async function updateList(targetUrl, push) {
    try {
      root.classList.add("psp-is-loading");
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
      const listWrap = qs(".product-section-products__product--list");
      if (!listWrap) return;
      listWrap.innerHTML = nextWrap.innerHTML;
      if (push) {
        history.pushState({}, "", targetUrl);
      } else {
        history.replaceState({}, "", targetUrl);
      }
      bindPagination();
    } catch (_) {
    } finally {
      root.classList.remove("psp-is-loading");
    }
  }

  function bindSearchAndFilters() {
    const form = qs(".psp-filter-form");
    const listWrap = qs(".product-section-products__product--list");
    if (!form || !listWrap) return;

    // avoid duplicate bindings by cloning on re-init
    const freshForm = form.cloneNode(true);
    form.parentNode.replaceChild(freshForm, form);

    const freshInput = freshForm.querySelector("#psp-search-input");
    const freshSelects = Array.from(
      freshForm.querySelectorAll(".psp-filter-field__select"),
    );

    if (freshInput) {
      const onType = debounce(() => {
        const url = buildURLFromForm(freshForm);
        updateList(url, false);
      }, 350);
      freshInput.addEventListener("input", onType);
    }

    freshSelects.forEach((select) => {
      select.addEventListener("change", function () {
        const url = buildURLFromForm(freshForm);
        updateList(url, true);
        scrollToList();
      });
    });

    freshForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const url = buildURLFromForm(freshForm);
      updateList(url, true);
      scrollToList();
    });
  }

  // Initial bindings
  bindPagination();
  bindSearchAndFilters();

  window.addEventListener("popstate", function () {
    const form = qs(".psp-filter-form");
    if (form) {
      syncFormFromURL(form, window.location.href);
    }
    updateList(window.location.href, false);
  });
});
