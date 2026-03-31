(function () {
  "use strict";

  var ppcpDebug =
    typeof window.bpCheckout === "object" && window.bpCheckout
      ? window.bpCheckout.ppcpDebug
      : null;

  function logPpcpDebug(label, payload) {
    if (!ppcpDebug || !ppcpDebug.enabled) return;
    try {
      console.log("[PPCP DEBUG] " + label, payload);
    } catch (e) {
      // no-op
    }
  }

  if (ppcpDebug && ppcpDebug.enabled) {
    logPpcpDebug("init", {
      forceStyle: Boolean(ppcpDebug.forceStyle),
      hasPayPalCommerceGateway: Boolean(window.PayPalCommerceGateway),
      context: window.PayPalCommerceGateway
        ? window.PayPalCommerceGateway.context
        : undefined,
      style:
        window.PayPalCommerceGateway && window.PayPalCommerceGateway.button
          ? window.PayPalCommerceGateway.button.style
          : undefined,
      urlParams: window.PayPalCommerceGateway
        ? window.PayPalCommerceGateway.url_params
        : undefined,
    });
  }

  /* ============================================================
     Payment Tabs
     ============================================================ */
  const tabs = document.querySelectorAll(".payment-tab");
  const panels = document.querySelectorAll(".payment-panel");

  tabs.forEach(function (tab) {
    tab.addEventListener("click", function (event) {
      event.preventDefault();

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

      if (target === "tab-cod") {
        setPaymentMethodSelected(codMethodId);
        setCheckoutFlowFlag("0");
      }

      if (target === "tab-bank") {
        setPaymentMethodSelected(bankMethodId);
        setCheckoutFlowFlag("0");
      }

      if (target === "tab-card") {
        setPaymentMethodSelected(wcpayMethodId, { triggerChange: true });
        setCheckoutFlowFlag("0");
      }

      // If PayPal smart buttons are used, the container might be hidden when the page loads.
      // Trigger the plugin to re-render/re-measure when the PayPal tab becomes visible.
      if (target === "tab-paypal") {
        setPaymentMethodSelected(paypalMethodId);
        setCheckoutFlowFlag("0");
        syncWooHiddenFields();
        reloadPpcpButtons();
      }

      updateSubmitButtonForMethod(target);
    });
  });

  /* ============================================================
     Copy to clipboard buttons
     ============================================================ */
  document.querySelectorAll(".bank-info__copy-btn").forEach(function (btn) {
    btn.addEventListener("click", function (event) {
      event.preventDefault();

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

  function updateSubmitButtonForMethod(target) {
    if (!submitBtn) return;

    if (target === "tab-paypal") {
      submitBtn.style.display = "none";
      return;
    }

    submitBtn.style.display = "";
  }

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

  // PPCP Smart Buttons / wallets are handled by the plugin scripts.
  var checkoutConfig =
    typeof window.bpCheckout === "object" && window.bpCheckout
      ? window.bpCheckout
      : {};
  var billUrl = checkoutConfig.billUrl || "";
  var paypalMethodId = checkoutConfig.paypalMethodId || "ppcp-gateway";
  var wcpayMethodId = checkoutConfig.wcpayMethodId || "woocommerce_payments";
  var codMethodId = checkoutConfig.codMethodId || "cod";
  var bankMethodId = checkoutConfig.bankMethodId || "bacs";

  function setCheckoutFlowFlag(value) {
    setHiddenValue("#bp-checkout-flow", value);
  }

  function notifyPaymentMethodSelected(methodId) {
    if (!window.jQuery) {
      return;
    }
    window.jQuery(document.body).trigger("payment_method_selected", [methodId]);
  }

  function setPaymentMethodSelected(methodId, options) {
    if (!methodId) return;

    options = options || {};
    var triggerChange = Boolean(options.triggerChange);
    var triggerWooSelectionEvent = options.triggerWooSelectionEvent !== false;

    var methodInputs = document.querySelectorAll(
      'input[name="payment_method"]',
    );
    if (!methodInputs.length) return;

    var selectedInput = null;

    methodInputs.forEach(function (input) {
      var isTarget = input.value === methodId;
      input.checked = isTarget;
      if (isTarget) {
        selectedInput = input;
      }
    });

    if (!selectedInput) {
      return;
    }

    if (triggerChange) {
      if (window.jQuery) {
        window.jQuery(selectedInput).trigger("change");
      } else {
        selectedInput.dispatchEvent(new Event("change", { bubbles: true }));
      }
    }

    if (triggerWooSelectionEvent) {
      notifyPaymentMethodSelected(methodId);
    }
  }

  function reloadPpcpButtons() {
    if (!window.jQuery) {
      return;
    }

    var selectors = [
      "#ppc-button-ppcp-gateway",
      "#ppc-button-ppcp-applepay",
      "#ppc-button-ppcp-googlepay",
    ];

    selectors.forEach(function (selector) {
      var el = document.querySelector(selector);
      if (el) {
        window.jQuery(el).trigger("ppcp-reload-buttons");
      }
    });

    if (
      window.PayPalCommerceGateway &&
      window.PayPalCommerceGateway.button &&
      window.PayPalCommerceGateway.button.wrapper
    ) {
      logPpcpDebug("reload ppcp buttons", {
        context: window.PayPalCommerceGateway.context,
        style: window.PayPalCommerceGateway.button.style,
        urlParams: window.PayPalCommerceGateway.url_params,
        wrapper: window.PayPalCommerceGateway.button.wrapper,
      });
      window
        .jQuery(window.PayPalCommerceGateway.button.wrapper)
        .trigger("ppcp-reload-buttons");
    }
  }

  function parseCheckoutErrorMessage(result) {
    if (result && typeof result.message === "string" && result.message.trim()) {
      return result.message.trim();
    }

    if (
      result &&
      typeof result.messages === "string" &&
      result.messages.trim()
    ) {
      var temp = document.createElement("div");
      temp.innerHTML = result.messages;
      var text = (temp.textContent || temp.innerText || "").trim();
      if (text) {
        return text;
      }
    }

    return "Unable to initialize PayPal checkout. Please try again.";
  }

  function setSubmitButtonDefaultState() {
    if (!submitBtn) return;
    submitBtn.classList.remove("is-loading");
    submitBtn.textContent = "Bill Order";
  }

  function submitPaypalInPopup() {
    var checkoutForm = document.getElementById("checkout-form");
    if (!checkoutForm || !checkoutConfig.ajaxUrl) {
      alert("Checkout form is unavailable. Please refresh and try again.");
      return;
    }

    var popup = window.open(
      "",
      "buildpro_paypal_checkout",
      "popup=yes,width=1100,height=760,left=120,top=80,resizable=yes,scrollbars=yes",
    );

    submitBtn.classList.add("is-loading");
    submitBtn.textContent = "Opening PayPal...";

    setPaymentMethodSelected(paypalMethodId);
    setCheckoutFlowFlag("1");
    syncWooHiddenFields();

    var formData = new FormData(checkoutForm);
    formData.set("payment_method", paypalMethodId);
    formData.set("bp_checkout_flow", "1");
    formData.set("terms", "on");
    formData.set("terms-field", "1");

    var params = new URLSearchParams();
    formData.forEach(function (value, key) {
      if (typeof value === "string") {
        params.append(key, value);
      }
    });

    fetch(checkoutConfig.ajaxUrl, {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
      },
      body: params.toString(),
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error("PayPal checkout request failed.");
        }
        return response.text();
      })
      .then(function (raw) {
        var result;
        try {
          result = JSON.parse(raw);
        } catch (error) {
          throw new Error(
            "Invalid checkout response. Please refresh and try again.",
          );
        }

        if (!result || result.result !== "success" || !result.redirect) {
          throw new Error(parseCheckoutErrorMessage(result));
        }

        if (popup && !popup.closed) {
          popup.location.href = result.redirect;
          popup.focus();
        } else {
          window.location.href = result.redirect;
        }

        setSubmitButtonDefaultState();
      })
      .catch(function (error) {
        if (popup && !popup.closed) {
          popup.close();
        }
        setCheckoutFlowFlag("0");
        setSubmitButtonDefaultState();
        alert(
          error && error.message
            ? error.message
            : "Unable to open PayPal checkout.",
        );
      });
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

  // Keep custom submit button state usable if WC checkout returns errors.
  if (window.jQuery && submitBtn) {
    window.jQuery(document.body).on("checkout_error", function () {
      setSubmitButtonDefaultState();
      setCheckoutFlowFlag("0");
    });
  }

  // NOTE: PPCP Smart Buttons handle order creation/approval via their own AJAX endpoints.
  // This page keeps Woo hidden fields in sync so PPCP can read buyer data from the form.

  if (submitBtn) {
    var initialActiveTab = document.querySelector(".payment-tab--active");
    updateSubmitButtonForMethod(
      initialActiveTab ? initialActiveTab.dataset.target : "tab-cod",
    );

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

      // PayPal payments are handled by PPCP Smart Buttons in the PayPal tab.
      if (methodTab === "tab-paypal") {
        return;
      }

      if (methodTab === "tab-card") {
        if (!checkoutConfig.wcpayEnabled) {
          alert(
            "WooPayments credit card gateway is unavailable. Please choose another payment method.",
          );
          return;
        }

        setPaymentMethodSelected(wcpayMethodId, { triggerChange: true });
        setCheckoutFlowFlag("1");
        syncWooHiddenFields();

        submitBtn.classList.add("is-loading");
        submitBtn.textContent = "Processing card...";

        var checkoutForm = document.getElementById("checkout-form");
        if (window.jQuery && checkoutForm) {
          window.jQuery(checkoutForm).trigger("submit");
        } else if (checkoutForm) {
          checkoutForm.submit();
        }
        return;
      }

      if (methodTab === "tab-bank") {
        setPaymentMethodSelected(bankMethodId);
      } else {
        setPaymentMethodSelected(codMethodId);
      }
      setCheckoutFlowFlag("0");

      submitBtn.classList.add("is-loading");
      submitBtn.textContent = "Redirecting to Bill...";

      var joiner = billUrl.indexOf("?") === -1 ? "?" : "&";
      window.location.href = billUrl + joiner + params.toString();
    });
  }
})();
