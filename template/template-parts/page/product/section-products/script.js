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

  let globalBound = false;

  function qs(sel) {
    return root.querySelector(sel);
  }

  function qsa(sel) {
    return Array.from(root.querySelectorAll(sel));
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

  // Category groups expand/collapse
  function bindFilterGroupToggles() {
    qsa(".psp-cat-group").forEach((group) => {
      const btn = group.querySelector(".psp-cat-group__toggle");
      const list = group.querySelector(".psp-cat-group__list");
      if (!btn || !list) return;

      const more = btn.getAttribute("data-more-label") || "More";
      const less = btn.getAttribute("data-less-label") || "Less";

      btn.addEventListener("click", function () {
        const expanded = btn.getAttribute("aria-expanded") === "true";
        btn.setAttribute("aria-expanded", expanded ? "false" : "true");
        list.dataset.collapsed = expanded ? "1" : "0";
        btn.textContent = expanded ? more : less;
      });
    });
  }

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
      const listWrap = qs(".product-section-products__product--list");
      if (!listWrap) return;
      listWrap.innerHTML = nextWrap.innerHTML;
      if (push) {
        history.pushState({}, "", targetUrl);
      } else {
        history.replaceState({}, "", targetUrl);
      }
      bindPagination();
    } catch (_) {}
  }

  // AJAX update when clicking category chips (updates BOTH left filters & right content)
  async function updateByFilter(targetUrl, push) {
    try {
      root.classList.add("psp-is-loading");
      const res = await fetch(targetUrl, {
        headers: { "X-Requested-With": "XMLHttpRequest" },
        credentials: "same-origin",
      });
      const html = await res.text();
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, "text/html");
      const nextRoot = doc.querySelector(".product-section-products");
      if (!nextRoot) return;

      const nextCategory = nextRoot.querySelector(
        ".product-section-products__category",
      );
      const nextRight = nextRoot.querySelector(
        ".product-section-products__right",
      );
      const curCategory = qs(".product-section-products__category");
      const curRight = qs(".product-section-products__right");

      if (nextCategory && curCategory) {
        curCategory.innerHTML = nextCategory.innerHTML;
      }
      if (nextRight && curRight) {
        curRight.innerHTML = nextRight.innerHTML;
      }

      if (push) {
        history.pushState({}, "", targetUrl);
      } else {
        history.replaceState({}, "", targetUrl);
      }

      // Re-bind events after DOM replacements
      bindFilterGroupToggles();
      bindCategoryChips();
      bindPagination();
      bindSearch();

      scrollToList();
    } catch (_) {
    } finally {
      root.classList.remove("psp-is-loading");
    }
  }

  function bindCategoryChips() {
    const catWrap = qs(".product-section-products__category");
    if (!catWrap) return;
    catWrap.querySelectorAll("a.psp-chip").forEach((a) => {
      a.addEventListener("click", function (e) {
        if (
          e.defaultPrevented ||
          e.metaKey ||
          e.ctrlKey ||
          e.shiftKey ||
          e.altKey
        ) {
          return;
        }
        const href = a.getAttribute("href");
        if (!href) return;
        e.preventDefault();
        sessionStorage.setItem("bp_scroll_product", "1");
        updateByFilter(href, true);
      });
    });
  }

  function bindSearch() {
    const form = qs(".psp-search");
    const listWrap = qs(".product-section-products__product--list");
    if (!form || !listWrap) return;

    // avoid duplicate bindings by cloning on re-init
    const freshForm = form.cloneNode(true);
    form.parentNode.replaceChild(freshForm, form);

    const freshInput = freshForm.querySelector("#psp-search-input");
    if (!freshInput) return;

    const onType = debounce(() => {
      const url = makeURL(window.location.href, freshInput.value);
      updateList(url, false);
    }, 350);
    freshInput.addEventListener("input", onType);

    freshForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const url = makeURL(
        freshForm.getAttribute("action") || window.location.href,
        freshInput.value,
      );
      updateList(url, true);
    });
  }

  // Initial bindings
  bindFilterGroupToggles();
  bindCategoryChips();
  bindPagination();
  bindSearch();

  if (!globalBound) {
    globalBound = true;
    window.addEventListener("popstate", function () {
      updateByFilter(window.location.href, false);
    });
  }
});
