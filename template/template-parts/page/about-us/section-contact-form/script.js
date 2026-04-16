(function () {
  var forms = document.querySelectorAll(
    ".about-contact-form .section-contact__form",
  );
  if (!forms.length) {
    return;
  }

  forms.forEach(function (form) {
    if (form.closest(".wpcf7")) {
      return;
    }

    var feedback = form.parentElement.querySelector(
      ".section-contact__feedback",
    );
    var input = form.querySelector(".section-contact__input");

    if (!input) {
      return;
    }

    form.addEventListener("submit", function (event) {
      var action = (form.getAttribute("action") || "").trim();

      if (!input.checkValidity()) {
        event.preventDefault();
        if (feedback) {
          feedback.textContent =
            form.getAttribute("data-invalid-message") || "";
          feedback.classList.add("is-error");
          feedback.classList.remove("is-success");
        }
        input.focus();
        return;
      }

      if (action === "" || action === "#") {
        event.preventDefault();
        if (feedback) {
          feedback.textContent =
            form.getAttribute("data-success-message") || "";
          feedback.classList.remove("is-error");
          feedback.classList.add("is-success");
        }
        form.reset();
      }
    });
  });
})();
