(function () {
  "use strict";

  var successPopup = document.getElementById("bill-success-popup");
  if (successPopup) {
    var redirectUrl = successPopup.getAttribute("data-home-url") || "/";

    function closePopupAndRedirect() {
      window.location.href = redirectUrl;
    }

    successPopup.addEventListener("click", function (event) {
      var target = event.target;
      if (target && target.getAttribute("data-popup-close") === "1") {
        closePopupAndRedirect();
      }
    });

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape") {
        closePopupAndRedirect();
      }
    });
  }

  var form = document.getElementById("bill-confirm-form");
  if (!form) {
    return;
  }

  var agreeErrorMessage =
    form.getAttribute("data-i18n-agree-error") ||
    "Please confirm the bill information before submitting.";
  var submittingLabel =
    form.getAttribute("data-i18n-submitting") || "Submitting...";

  var submitBtn = document.getElementById("bill-submit-btn");
  var agreeCheckbox = document.getElementById("bill-agree");
  var agreeError = document.querySelector(
    '.bill-form__error[data-for="bill-agree"]',
  );

  function showAgreeError(message) {
    if (!agreeError) {
      return;
    }
    agreeError.textContent = message;
    agreeError.classList.add("is-visible");
  }

  function clearAgreeError() {
    if (!agreeError) {
      return;
    }
    agreeError.textContent = "";
    agreeError.classList.remove("is-visible");
  }

  if (agreeCheckbox) {
    agreeCheckbox.addEventListener("change", function () {
      if (agreeCheckbox.checked) {
        clearAgreeError();
      }
    });
  }

  form.addEventListener("submit", function (event) {
    if (!agreeCheckbox || !agreeCheckbox.checked) {
      event.preventDefault();
      showAgreeError(agreeErrorMessage);
      if (agreeCheckbox) {
        agreeCheckbox.scrollIntoView({ behavior: "smooth", block: "center" });
      }
      return;
    }

    clearAgreeError();

    if (submitBtn) {
      submitBtn.classList.add("is-loading");
      submitBtn.textContent = submittingLabel;
    }
  });
})();
