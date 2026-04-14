(function () {
  "use strict";

  function updateCartBadge(count) {
    var badges = document.querySelectorAll(".header-cart-count");
    badges.forEach(function (badge) {
      badge.textContent = count;
      if (count > 0) {
        badge.classList.remove("header-cart-count--hidden");
      } else {
        badge.classList.add("header-cart-count--hidden");
      }
    });
  }

  // Expose so cart dropdown script can call it
  window.buildproUpdateCartBadge = updateCartBadge;

  function getCurrentCount() {
    var badge = document.querySelector(".header-cart-count");
    if (!badge) return 0;
    return parseInt(badge.textContent, 10) || 0;
  }

  function addToCart(productId, btn) {
    btn.classList.remove("is-added", "is-error");
    btn.classList.add("is-adding");
    btn.disabled = true;
    var originalHtml = btn.innerHTML;
    var originalLabel = btn.getAttribute("aria-label") || "Add to Cart";
    btn.textContent = "Adding...";
    btn.setAttribute("aria-label", "Adding");

    var qty = 1;
    var row = btn.closest(".single-product__cart-row");
    if (row) {
      var qInput = row.querySelector(".single-product__qty-input");
      if (qInput) {
        qty = Math.max(1, parseInt(qInput.value, 10) || 1);
      }
    }

    var formData = new FormData();
    formData.append("product_id", productId);
    formData.append("quantity", qty);

    fetch("/?wc-ajax=add_to_cart", {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    })
      .then(function (res) {
        return res.json();
      })
      .then(function (data) {
        btn.disabled = false;
        btn.classList.remove("is-adding");
        if (data && !data.error) {
          btn.textContent = "Added ✓";
          btn.setAttribute("aria-label", "Added");
          btn.classList.add("is-added");
          updateCartBadge(getCurrentCount() + 1); // Refresh mini cart dropdown
          if (typeof window.buildproRefreshMiniCart === "function") {
            window.buildproRefreshMiniCart(true);
          }
          setTimeout(function () {
            btn.innerHTML = originalHtml;
            btn.setAttribute("aria-label", originalLabel);
            btn.classList.remove("is-added");
          }, 1500);
        } else {
          btn.innerHTML = originalHtml;
          btn.setAttribute("aria-label", originalLabel);
          btn.classList.add("is-error");
        }
      })
      .catch(function () {
        btn.disabled = false;
        btn.classList.remove("is-adding");
        btn.innerHTML = originalHtml;
        btn.setAttribute("aria-label", originalLabel);
        btn.classList.add("is-error");
      });
  }

  function bindQty() {
    document
      .querySelectorAll(".single-product__cart-row")
      .forEach(function (row) {
        if (row.dataset.qtyBound) return;
        row.dataset.qtyBound = "1";
        var input = row.querySelector(".single-product__qty-input");
        var minus = row.querySelector(".single-product__qty-minus");
        var plus = row.querySelector(".single-product__qty-plus");
        if (!input) return;
        minus &&
          minus.addEventListener("click", function () {
            var v = parseInt(input.value, 10) || 1;
            if (v > 1) input.value = v - 1;
          });
        plus &&
          plus.addEventListener("click", function () {
            var v = parseInt(input.value, 10) || 1;
            var max = parseInt(input.max, 10) || 999;
            if (v < max) input.value = v + 1;
          });
        input.addEventListener("change", function () {
          var v = parseInt(input.value, 10);
          if (!v || v < 1) input.value = 1;
        });
      });
  }

  function bindButtons(root) {
    var btns = (root || document).querySelectorAll(".btn-add-to-cart");
    btns.forEach(function (btn) {
      if (btn.dataset.cartBound) return;
      btn.dataset.cartBound = "1";
      btn.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        var productId = btn.dataset.productId;
        if (!productId) return;
        addToCart(productId, btn);
      });
    });
  }

  // Bind on DOM ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      bindButtons();
      bindQty();
    });
  } else {
    bindButtons();
    bindQty();
  }

  // Re-bind after AJAX list updates (product page pagination/search)
  var listWrap = document.querySelector(
    ".product-section-products__product--list",
  );
  if (listWrap) {
    var observer = new MutationObserver(function () {
      bindButtons(listWrap);
    });
    observer.observe(listWrap, { childList: true, subtree: true });
  }
})();
