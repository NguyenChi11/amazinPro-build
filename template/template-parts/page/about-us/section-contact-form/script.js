document.addEventListener("DOMContentLoaded", function () {
  const forms = document.querySelectorAll(".about-contact-form .wpcf7");
  forms.forEach((w) => {
    w.addEventListener("wpcf7invalid", () => {});
    w.addEventListener("wpcf7mailsent", () => {});
  });
});
