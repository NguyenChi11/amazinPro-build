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

  function getCurrentCount() {
    var badge = document.querySelector(".header-cart-count");
    if (!badge) return 0;
    return parseInt(badge.textContent, 10) || 0;
  }

  function addToCart(productId, btn) {
    btn.disabled = true;
    var originalText = btn.textContent;
    btn.textContent = "Adding…";

    var formData = new FormData();
    formData.append("product_id", productId);
    formData.append("quantity", 1);

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
        if (data && !data.error) {
          btn.textContent = "Added ✓";
          updateCartBadge(getCurrentCount() + 1);
          setTimeout(function () {
            btn.textContent = originalText;
          }, 1500);
        } else {
          btn.textContent = originalText;
        }
      })
      .catch(function () {
        btn.disabled = false;
        btn.textContent = originalText;
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
    });
  } else {
    bindButtons();
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
