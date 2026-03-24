(function () {
  "use strict";

  /* ============================================================
     Payment Tabs
     ============================================================ */
  const tabs = document.querySelectorAll(".payment-tab");
  const panels = document.querySelectorAll(".payment-panel");

  tabs.forEach(function (tab) {
    tab.addEventListener("click", function () {
      if (
        tab.hasAttribute("disabled") ||
        tab.getAttribute("aria-disabled") === "true"
      ) {
        return;
      }

      const target = tab.dataset.target;

      tabs.forEach(function (t) {
        t.classList.remove("payment-tab--active");
        t.setAttribute("aria-selected", "false");
      });
      panels.forEach(function (p) {
        p.classList.remove("payment-panel--active");
      });

      tab.classList.add("payment-tab--active");
      tab.setAttribute("aria-selected", "true");

      const panel = document.getElementById(target);
      if (panel) {
        panel.classList.add("payment-panel--active");
      }

      // If PayPal smart buttons are used, the container might be hidden when the page loads.
      // Trigger the plugin to re-render/re-measure when the PayPal tab becomes visible.
      if (
        target === "tab-paypal" &&
        window.jQuery &&
        window.PayPalCommerceGateway &&
        window.PayPalCommerceGateway.button &&
        window.PayPalCommerceGateway.button.wrapper
      ) {
        window
          .jQuery(window.PayPalCommerceGateway.button.wrapper)
          .trigger("ppcp-reload-buttons");
      }
    });
  });

  /* ============================================================
     Copy to clipboard buttons
     ============================================================ */
  document.querySelectorAll(".bank-info__copy-btn").forEach(function (btn) {
    btn.addEventListener("click", function () {
      const targetId = btn.dataset.copy;
      const el = document.getElementById(targetId);
      if (!el) return;

      const text = el.textContent.trim();
      navigator.clipboard
        .writeText(text)
        .then(function () {
          const originalTitle = btn.title;
          btn.title = "Copied!";
          btn.style.background = "#10b981";
          btn.style.color = "#fff";
          setTimeout(function () {
            btn.title = originalTitle;
            btn.style.background = "";
            btn.style.color = "";
          }, 1500);
        })
        .catch(function () {
          // Fallback for older browsers
          const range = document.createRange();
          range.selectNodeContents(el);
          const sel = window.getSelection();
          sel.removeAllRanges();
          sel.addRange(range);
          document.execCommand("copy");
          sel.removeAllRanges();
        });
    });
  });

  /* ============================================================
     Credit card input formatting
     ============================================================ */
  var cardNumberInput = document.getElementById("card-number");
  if (cardNumberInput) {
    cardNumberInput.addEventListener("input", function () {
      var val = this.value.replace(/\D/g, "").substring(0, 16);
      this.value = val.replace(/(.{4})/g, "$1 ").trim();
    });
  }

  var cardExpiryInput = document.getElementById("card-expiry");
  if (cardExpiryInput) {
    cardExpiryInput.addEventListener("input", function () {
      var val = this.value.replace(/\D/g, "").substring(0, 4);
      if (val.length >= 3) {
        this.value = val.substring(0, 2) + " / " + val.substring(2);
      } else {
        this.value = val;
      }
    });
  }

  var cardCvcInput = document.getElementById("card-cvc");
  if (cardCvcInput) {
    cardCvcInput.addEventListener("input", function () {
      this.value = this.value.replace(/\D/g, "").substring(0, 4);
    });
  }

  /* ============================================================
     Form validation & submit
     ============================================================ */
  var submitBtn = document.getElementById("checkout-submit-btn");
  var form = document.getElementById("checkout-form");

  var requiredFields = [
    { id: "co-fullname", label: "Full Name" },
    { id: "co-phone", label: "Phone Number" },
    { id: "co-email", label: "Email Address" },
    { id: "co-address", label: "Address" },
    { id: "co-city", label: "City" },
    { id: "co-country", label: "Country" },
  ];

  function showError(fieldId, message) {
    var input = document.getElementById(fieldId);
    var error = document.querySelector(
      '.checkout-form__error[data-for="' + fieldId + '"]',
    );
    if (input) input.classList.add("is-invalid");
    if (error) {
      error.textContent = message;
      error.classList.add("is-visible");
    }
  }

  function clearError(fieldId) {
    var input = document.getElementById(fieldId);
    var error = document.querySelector(
      '.checkout-form__error[data-for="' + fieldId + '"]',
    );
    if (input) input.classList.remove("is-invalid");
    if (error) {
      error.textContent = "";
      error.classList.remove("is-visible");
    }
  }

  // Real-time clear on change
  requiredFields.forEach(function (f) {
    var el = document.getElementById(f.id);
    if (el) {
      el.addEventListener("input", function () {
        clearError(f.id);
      });
      el.addEventListener("change", function () {
        clearError(f.id);
      });
    }
  });

  function validateForm() {
    var valid = true;

    requiredFields.forEach(function (f) {
      var el = document.getElementById(f.id);
      if (!el) return;
      if (!el.value.trim()) {
        showError(f.id, f.label + " is required.");
        valid = false;
      } else {
        clearError(f.id);
      }
    });

    // Email format
    var emailEl = document.getElementById("co-email");
    if (emailEl && emailEl.value.trim()) {
      var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRe.test(emailEl.value.trim())) {
        showError("co-email", "Please enter a valid email address.");
        valid = false;
      }
    }

    // Phone — must contain only digits, spaces, +, -, (, )
    var phoneEl = document.getElementById("co-phone");
    if (phoneEl && phoneEl.value.trim()) {
      var phoneRe = /^[\d\s\+\-\(\)]{7,20}$/;
      if (!phoneRe.test(phoneEl.value.trim())) {
        showError("co-phone", "Please enter a valid phone number.");
        valid = false;
      }
    }

    return valid;
  }

  // "Pay with PayPal" button
  var bpPaypalPayBtn = document.getElementById("bp-paypal-pay-btn");
  var checkoutConfig =
    typeof window.bpCheckout === "object" && window.bpCheckout
      ? window.bpCheckout
      : {};
  var paypalMethodId = checkoutConfig.paypalMethodId || "";
  var paypalMethodTitle = checkoutConfig.paypalTitle || "PayPal";
  var billUrl = checkoutConfig.billUrl || "";
  var paypalEnabled = Boolean(
    paypalMethodId &&
    (checkoutConfig.paypalEnabled === true ||
      checkoutConfig.paypalEnabled === "1" ||
      checkoutConfig.paypalEnabled === 1),
  );

  function getCheckoutPayload(paymentMethodId, paymentMethodTitle) {
    var fullname = (
      (document.getElementById("co-fullname") || { value: "" }).value || ""
    ).trim();
    var nameParts = fullname.split(/\s+/);
    var firstName = nameParts[0] || fullname;
    var lastName =
      nameParts.length > 1 ? nameParts.slice(1).join(" ") : firstName;
    var billingAddress1 = (
      (document.getElementById("co-address") || { value: "" }).value || ""
    ).trim();
    var billingCity = (
      (document.getElementById("co-city") || { value: "" }).value || ""
    ).trim();
    var billingPostcode = (
      (document.getElementById("co-zip") || { value: "" }).value || ""
    ).trim();
    var billingCountry = (
      (document.getElementById("co-country") || { value: "" }).value || ""
    ).trim();

    var postData = new URLSearchParams();
    postData.append("billing_first_name", firstName);
    postData.append("billing_last_name", lastName);
    postData.append(
      "billing_email",
      (
        (document.getElementById("co-email") || { value: "" }).value || ""
      ).trim(),
    );
    postData.append(
      "billing_phone",
      (
        (document.getElementById("co-phone") || { value: "" }).value || ""
      ).trim(),
    );
    postData.append("billing_address_1", billingAddress1);
    postData.append("billing_city", billingCity);
    postData.append("billing_postcode", billingPostcode);
    postData.append("billing_country", billingCountry);
    postData.append(
      "order_comments",
      (
        (document.getElementById("co-note") || { value: "" }).value || ""
      ).trim(),
    );
    postData.append("payment_method", paymentMethodId);
    postData.append("payment_method_title", paymentMethodTitle);
    // Keep shipping synced with billing for gateways/hooks that validate shipping fields.
    postData.append("ship_to_different_address", "0");
    postData.append("shipping_first_name", firstName);
    postData.append("shipping_last_name", lastName);
    postData.append("shipping_address_1", billingAddress1);
    postData.append("shipping_city", billingCity);
    postData.append("shipping_postcode", billingPostcode);
    postData.append("shipping_country", billingCountry);
    // WooCommerce checkout expects this exact nonce field name.
    postData.append(
      "woocommerce-process-checkout-nonce",
      checkoutConfig.nonce || "",
    );
    // Keep _wpnonce for compatibility with custom hooks in some installs.
    postData.append("_wpnonce", checkoutConfig.nonce || "");
    postData.append("_wp_http_referer", checkoutConfig.referer || "/");
    postData.append("terms", "on");
    postData.append("terms-field", "1");
    postData.append("woocommerce_checkout_update_totals", "");
    return postData;
  }

  function setHiddenValue(selector, value) {
    var el = document.querySelector(selector);
    if (!el) return;
    el.value = value;
  }

  function syncWooHiddenFields() {
    var fullname = (
      (document.getElementById("co-fullname") || { value: "" }).value || ""
    ).trim();
    var nameParts = fullname.split(/\s+/);
    var firstName = nameParts[0] || fullname;
    var lastName =
      nameParts.length > 1 ? nameParts.slice(1).join(" ") : firstName;

    var email = (
      (document.getElementById("co-email") || { value: "" }).value || ""
    ).trim();
    var phone = (
      (document.getElementById("co-phone") || { value: "" }).value || ""
    ).trim();
    var address1 = (
      (document.getElementById("co-address") || { value: "" }).value || ""
    ).trim();
    var city = (
      (document.getElementById("co-city") || { value: "" }).value || ""
    ).trim();
    var postcode = (
      (document.getElementById("co-zip") || { value: "" }).value || ""
    ).trim();
    var country = (
      (document.getElementById("co-country") || { value: "" }).value || ""
    ).trim();

    setHiddenValue("#billing_first_name", firstName);
    setHiddenValue("#billing_last_name", lastName);
    setHiddenValue("#billing_email", email);
    setHiddenValue("#billing_phone", phone);
    setHiddenValue("#billing_address_1", address1);
    setHiddenValue("#billing_city", city);
    setHiddenValue("#billing_postcode", postcode);
    setHiddenValue("#billing_country", country);

    setHiddenValue("#shipping_first_name", firstName);
    setHiddenValue("#shipping_last_name", lastName);
    setHiddenValue("#shipping_address_1", address1);
    setHiddenValue("#shipping_city", city);
    setHiddenValue("#shipping_postcode", postcode);
    setHiddenValue("#shipping_country", country);
  }

  function parseCheckoutErrorMessage(json) {
    var raw = json && json.messages ? String(json.messages) : "";
    var plain = raw
      .replace(/<[^>]*>/g, " ")
      .replace(/\s+/g, " ")
      .trim();
    return plain || "Could not process payment. Please try again.";
  }

  function submitPaypalCheckout() {
    var ajaxUrl = checkoutConfig.ajaxUrl || "";
    if (!ajaxUrl) {
      alert("Checkout endpoint is missing. Please refresh and try again.");
      return Promise.reject(new Error("Missing checkout AJAX URL"));
    }

    var postData = getCheckoutPayload(paypalMethodId, paypalMethodTitle);

    return fetch(ajaxUrl, {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: postData.toString(),
    }).then(function (res) {
      return res.text();
    });
  }

  if (bpPaypalPayBtn) {
    bpPaypalPayBtn.addEventListener("click", function () {
      // 1. Validate billing form first
      if (!validateForm()) {
        var firstError = document.querySelector(
          ".checkout-form__input.is-invalid",
        );
        if (firstError)
          firstError.scrollIntoView({ behavior: "smooth", block: "center" });
        return;
      }

      syncWooHiddenFields();

      if (!paypalEnabled || !paypalMethodId) {
        alert(
          "PayPal is currently unavailable. Please choose another payment method.",
        );
        return;
      }

      // Submit checkout with the actual WooCommerce PayPal gateway id.
      bpPaypalPayBtn.disabled = true;
      bpPaypalPayBtn.textContent = "Redirecting to PayPal...";

      submitPaypalCheckout()
        .then(function (raw) {
          var json;
          try {
            json = JSON.parse(raw);
          } catch (e) {
            throw new Error("Invalid checkout response: " + raw.slice(0, 220));
          }

          if (json.result === "success" && json.redirect) {
            window.location.href = json.redirect;
            return;
          }

          if (json && json.reload) {
            window.location.reload();
            return;
          } else {
            bpPaypalPayBtn.disabled = false;
            bpPaypalPayBtn.textContent = "Continue with " + paypalMethodTitle;
            var msg = parseCheckoutErrorMessage(json);
            alert(msg);
          }
        })
        .catch(function (err) {
          console.error("[PayPal Checkout]", err);
          bpPaypalPayBtn.disabled = false;
          bpPaypalPayBtn.textContent = "Continue with " + paypalMethodTitle;
          alert("Connection error. Please try again.");
        });
    });
  }

  // Keep hidden WooCommerce fields in sync for Smart Buttons.
  [
    "co-fullname",
    "co-phone",
    "co-email",
    "co-address",
    "co-city",
    "co-zip",
    "co-country",
  ].forEach(function (id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.addEventListener("input", syncWooHiddenFields);
    el.addEventListener("change", syncWooHiddenFields);
  });
  syncWooHiddenFields();

  // WooCommerce PayPal Payments triggers #place_order click after approval.
  // Our checkout page uses an AJAX submit; bridge the click to our existing flow.
  var placeOrderBtn = document.getElementById("place_order");
  if (placeOrderBtn) {
    placeOrderBtn.addEventListener("click", function (e) {
      e.preventDefault();

      // Validate visible billing form.
      if (!validateForm()) {
        var firstError = document.querySelector(
          ".checkout-form__input.is-invalid",
        );
        if (firstError)
          firstError.scrollIntoView({ behavior: "smooth", block: "center" });
        return;
      }

      syncWooHiddenFields();

      // Only handle PayPal Payments gateway; otherwise do nothing.
      var selected = document.querySelector(
        'input[name="payment_method"]:checked',
      );
      var selectedMethod = selected ? String(selected.value || "") : "";
      if (!selectedMethod || selectedMethod.indexOf("ppcp-") !== 0) {
        return;
      }

      submitPaypalCheckout()
        .then(function (raw) {
          var json;
          try {
            json = JSON.parse(raw);
          } catch (err) {
            throw new Error(
              "Invalid checkout response: " + String(raw).slice(0, 220),
            );
          }

          if (json.result === "success" && json.redirect) {
            window.location.href = json.redirect;
            return;
          }
          if (json && json.reload) {
            window.location.reload();
            return;
          }

          alert(parseCheckoutErrorMessage(json));
        })
        .catch(function (err) {
          console.error("[PayPal Place Order]", err);
          alert("Connection error. Please try again.");
        });
    });
  }

  if (submitBtn) {
    submitBtn.addEventListener("click", function () {
      if (!validateForm()) return;

      var activeTab = document.querySelector(".payment-tab--active");
      var methodTab = activeTab ? activeTab.dataset.target : "tab-cod";
      var paymentMap = {
        "tab-cod": "cod",
        "tab-paypal": "paypal",
        "tab-card": "card",
        "tab-bank": "bank",
      };

      var countrySelect = document.getElementById("co-country");
      var countryCode = countrySelect ? countrySelect.value : "";
      var countryLabel =
        countrySelect && countrySelect.selectedIndex >= 0
          ? countrySelect.options[countrySelect.selectedIndex].text
          : "";

      var params = new URLSearchParams();
      params.set(
        "fullname",
        (
          (document.getElementById("co-fullname") || { value: "" }).value || ""
        ).trim(),
      );
      params.set(
        "phone",
        (
          (document.getElementById("co-phone") || { value: "" }).value || ""
        ).trim(),
      );
      params.set(
        "email",
        (
          (document.getElementById("co-email") || { value: "" }).value || ""
        ).trim(),
      );
      params.set(
        "address",
        (
          (document.getElementById("co-address") || { value: "" }).value || ""
        ).trim(),
      );
      params.set(
        "city",
        (
          (document.getElementById("co-city") || { value: "" }).value || ""
        ).trim(),
      );
      params.set(
        "zip",
        (
          (document.getElementById("co-zip") || { value: "" }).value || ""
        ).trim(),
      );
      params.set("country", countryCode);
      params.set("country_label", countryLabel);
      params.set(
        "note",
        (
          (document.getElementById("co-note") || { value: "" }).value || ""
        ).trim(),
      );
      params.set("payment", paymentMap[methodTab] || "cod");
      // Mark that Bill page was reached from the Checkout flow.
      // The Bill page uses this to snapshot cart items and reset the header cart.
      params.set("bp_from_checkout", "1");

      submitBtn.classList.add("is-loading");
      submitBtn.textContent = "Redirecting to Bill...";

      var joiner = billUrl.indexOf("?") === -1 ? "?" : "&";
      window.location.href = billUrl + joiner + params.toString();
    });
  }
})();
