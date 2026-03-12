(function () {
  "use strict";

  var shippingCost =
    typeof cartSectionData !== "undefined"
      ? Number(cartSectionData.shippingCost)
      : 120;
  var taxRate =
    typeof cartSectionData !== "undefined"
      ? Number(cartSectionData.taxRate)
      : 0.08;
  var discount =
    typeof cartSectionData !== "undefined"
      ? Number(cartSectionData.discount)
      : 0;
  var ajaxUrl =
    typeof cartSectionData !== "undefined" ? cartSectionData.ajaxUrl : "";
  var miniNonce =
    typeof cartSectionData !== "undefined" ? cartSectionData.miniNonce : "";
  var cartNonce =
    typeof cartSectionData !== "undefined" ? cartSectionData.cartNonce : "";

  function formatPrice(val) {
    return "$" + val.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  function updateSummary() {
    var subtotal = 0;
    var regularTotal = 0;
    document.querySelectorAll(".cart-item").forEach(function (item) {
      var checkbox = item.querySelector(".cart-item__checkbox");
      if (checkbox && !checkbox.checked) return;
      var price = parseFloat(item.dataset.price) || 0;
      var regularPrice = parseFloat(item.dataset.regularPrice) || price;
      var qtyInput = item.querySelector(".cart-item__qty-input");
      var qty = qtyInput ? parseInt(qtyInput.value, 10) || 1 : 1;
      var lineTotal = price * qty;
      var priceEl = item.querySelector(".cart-item__price");
      if (priceEl) priceEl.textContent = formatPrice(lineTotal);
      subtotal += lineTotal;
      regularTotal += regularPrice * qty;
    });

    var total = subtotal - discount;

    var el = function (id) {
      return document.getElementById(id);
    };
    var regularEl = el("summary-regular-price");
    var saleRow = el("summary-sale-row");
    var saleEl = el("summary-sale-price");
    var savingsRow = el("summary-savings-row");
    var savingsEl = el("summary-savings");
    var totalEl = el("summary-total");
    var discountRow = el("summary-discount-row");
    var discountEl = el("summary-discount");

    if (regularEl) regularEl.textContent = formatPrice(regularTotal);
    if (saleRow && saleEl) {
      if (subtotal < regularTotal) {
        saleRow.style.display = "";
        saleEl.textContent = formatPrice(subtotal);
      } else {
        saleRow.style.display = "none";
      }
    }
    if (savingsRow && savingsEl) {
      var savings = regularTotal - subtotal;
      if (savings > 0) {
        savingsRow.style.display = "";
        savingsEl.textContent = "-" + formatPrice(savings);
      } else {
        savingsRow.style.display = "none";
      }
    }
    if (totalEl) totalEl.textContent = formatPrice(total > 0 ? total : 0);

    if (discount > 0) {
      if (discountRow) discountRow.style.display = "";
      if (discountEl) discountEl.textContent = "-" + formatPrice(discount);
    } else {
      if (discountRow) discountRow.style.display = "none";
    }
  }

  /* ---- WC AJAX: update qty in session ---- */
  function wcUpdateQty(cartKey, qty) {
    if (!ajaxUrl || !miniNonce) return;
    var fd = new FormData();
    fd.append("action", "buildpro_update_cart_qty");
    fd.append("nonce", miniNonce);
    fd.append("cart_key", cartKey);
    fd.append("qty", qty);
    fetch(ajaxUrl, { method: "POST", body: fd, credentials: "same-origin" })
      .then(function (r) {
        return r.json();
      })
      .then(function (data) {
        if (data && data.data && data.data.nonce) {
          miniNonce = data.data.nonce;
        }
        // Update header badge count
        if (data && data.data && typeof data.data.count !== "undefined") {
          if (typeof window.buildproUpdateCartBadge === "function") {
            window.buildproUpdateCartBadge(data.data.count);
          }
        }
        // Refresh header cart dropdown
        if (typeof window.buildproRefreshMiniCart === "function") {
          window.buildproRefreshMiniCart(false);
        }
      });
  }

  /* ---- WC AJAX: remove item from session ---- */
  function wcRemoveItem(cartKey, itemEl) {
    if (!ajaxUrl || !miniNonce) {
      // fallback: reload
      window.location.reload();
      return;
    }
    var fd = new FormData();
    fd.append("action", "buildpro_remove_cart_item");
    fd.append("nonce", miniNonce);
    fd.append("cart_key", cartKey);
    fetch(ajaxUrl, { method: "POST", body: fd, credentials: "same-origin" })
      .then(function (r) {
        return r.json();
      })
      .then(function (data) {
        if (data && data.success) {
          itemEl.remove();
          updateSummary();
          if (data.data && data.data.nonce) {
            miniNonce = data.data.nonce;
          }
          if (data.data && typeof data.data.count !== "undefined") {
            if (typeof window.buildproUpdateCartBadge === "function") {
              window.buildproUpdateCartBadge(data.data.count);
            }
          }
          // Refresh header cart dropdown
          if (typeof window.buildproRefreshMiniCart === "function") {
            window.buildproRefreshMiniCart(false);
          }
          // Show empty state if no items remain
          if (!document.querySelector(".cart-item")) {
            var wrap = document.getElementById("cart-items");
            if (wrap) {
              wrap.innerHTML =
                '<div class="cart-section__empty"><p>Your cart is empty.</p></div>';
            }
          }
        } else {
          window.location.reload();
        }
      })
      .catch(function () {
        window.location.reload();
      });
  }

  /* ---- Bind qty +/- controls ---- */
  function bindQtyControls() {
    document.querySelectorAll(".cart-item").forEach(function (item) {
      if (item.dataset.qtyBound) return;
      item.dataset.qtyBound = "1";

      var input = item.querySelector(".cart-item__qty-input");
      var minusBtn = item.querySelector(".cart-item__qty-minus");
      var plusBtn = item.querySelector(".cart-item__qty-plus");
      var checkbox = item.querySelector(".cart-item__checkbox");
      var cartKey = item.dataset.key;

      if (!input) return;

      minusBtn &&
        minusBtn.addEventListener("click", function () {
          var v = parseInt(input.value, 10) || 1;
          if (v > 1) {
            input.value = v - 1;
            updateSummary();
            wcUpdateQty(cartKey, v - 1);
          }
        });

      plusBtn &&
        plusBtn.addEventListener("click", function () {
          var v = parseInt(input.value, 10) || 1;
          var max = parseInt(input.max, 10) || 9999;
          if (v < max) {
            input.value = v + 1;
            updateSummary();
            wcUpdateQty(cartKey, v + 1);
          }
        });

      input.addEventListener("change", function () {
        var v = parseInt(input.value, 10);
        if (!v || v < 1) input.value = 1;
        updateSummary();
        wcUpdateQty(cartKey, parseInt(input.value, 10));
      });

      checkbox && checkbox.addEventListener("change", updateSummary);
    });
  }

  /* ---- Bind Remove buttons ---- */
  function bindRemoveButtons() {
    document.querySelectorAll(".cart-item__remove").forEach(function (btn) {
      if (btn.dataset.removeBound) return;
      btn.dataset.removeBound = "1";

      btn.addEventListener("click", function (e) {
        e.preventDefault();
        var item = btn.closest(".cart-item");
        var cartKey = btn.dataset.cartKey;
        if (!item || !cartKey) return;
        btn.disabled = true;
        wcRemoveItem(cartKey, item);
      });
    });
  }

  /* ---- Coupon apply ---- */
  function bindCoupon() {
    var btn = document.getElementById("apply-coupon");
    var input = document.getElementById("coupon-code");
    var msg = document.getElementById("coupon-msg");
    if (!btn || !input) return;

    btn.addEventListener("click", function () {
      var code = input.value.trim();
      if (!code) {
        if (msg) {
          msg.textContent = "Please enter a discount code.";
          msg.className =
            "cart-summary__coupon-msg cart-summary__coupon-msg--error";
        }
        return;
      }
      btn.disabled = true;
      btn.textContent = "Applying…";
      if (msg) {
        msg.textContent = "";
        msg.className = "cart-summary__coupon-msg";
      }

      var fd = new FormData();
      fd.append("action", "buildpro_apply_coupon");
      fd.append("coupon_code", code);
      fd.append("nonce", cartNonce);

      fetch(ajaxUrl, { method: "POST", body: fd, credentials: "same-origin" })
        .then(function (r) {
          return r.json();
        })
        .then(function (data) {
          btn.disabled = false;
          btn.textContent = "Apply";
          if (data.success) {
            discount = parseFloat(data.data.discount) || 0;
            if (msg) {
              msg.textContent = data.data.message || "Coupon applied!";
              msg.className = "cart-summary__coupon-msg";
            }
            updateSummary();
          } else {
            if (msg) {
              msg.textContent =
                data.data && data.data.message
                  ? data.data.message
                  : "Invalid coupon code.";
              msg.className =
                "cart-summary__coupon-msg cart-summary__coupon-msg--error";
            }
          }
        })
        .catch(function () {
          btn.disabled = false;
          btn.textContent = "Apply";
          if (msg) {
            msg.textContent = "An error occurred. Please try again.";
            msg.className =
              "cart-summary__coupon-msg cart-summary__coupon-msg--error";
          }
        });
    });

    input.addEventListener("keydown", function (e) {
      if (e.key === "Enter") btn.click();
    });
  }
  /* ---- Order Notes ---- */
  function bindNotes() {
    var saveBtn = document.getElementById("notes-save-btn");
    var deleteBtn = document.getElementById("notes-delete-btn");
    var textarea = document.getElementById("notes-textarea");
    var msg = document.getElementById("notes-msg");

    function setMsg(text, isError) {
      if (!msg) return;
      msg.textContent = text;
      msg.className =
        "cart-notes__msg" +
        (isError ? " cart-notes__msg--error" : " cart-notes__msg--success");
    }

    if (saveBtn)
      saveBtn.addEventListener("click", function () {
        var note = textarea ? textarea.value.trim() : "";
        if (!note) {
          setMsg("Please enter a note.", true);
          return;
        }
        saveBtn.disabled = true;
        setMsg("");
        var fd = new FormData();
        fd.append("action", "buildpro_save_order_note");
        fd.append("nonce", miniNonce);
        fd.append("note", note);
        fetch(ajaxUrl, { method: "POST", body: fd, credentials: "same-origin" })
          .then(function (r) {
            return r.json();
          })
          .then(function (data) {
            saveBtn.disabled = false;
            if (data && data.success) {
              if (data.data && data.data.nonce) miniNonce = data.data.nonce;
              setMsg("Note saved!", false);
            } else {
              setMsg("Could not save note. Please try again.", true);
            }
          })
          .catch(function () {
            saveBtn.disabled = false;
            setMsg("Network error. Please try again.", true);
          });
      });

    if (deleteBtn)
      deleteBtn.addEventListener("click", function () {
        deleteBtn.disabled = true;
        var fd = new FormData();
        fd.append("action", "buildpro_delete_order_note");
        fd.append("nonce", miniNonce);
        fetch(ajaxUrl, { method: "POST", body: fd, credentials: "same-origin" })
          .then(function (r) {
            return r.json();
          })
          .then(function (data) {
            deleteBtn.disabled = false;
            if (data && data.success) {
              if (data.data && data.data.nonce) miniNonce = data.data.nonce;
              if (textarea) textarea.value = "";
              setMsg("Note deleted.", false);
            }
          })
          .catch(function () {
            deleteBtn.disabled = false;
          });
      });
  }
  document.addEventListener("DOMContentLoaded", function () {
    bindQtyControls();
    bindRemoveButtons();
    bindCoupon();
    bindNotes();
    updateSummary();
  });
})();
