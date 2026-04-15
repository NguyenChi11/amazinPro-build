(function () {
  var data = window.buildproProductTitleData || {};
  var t = document.querySelector(".product--section-title__title");
  var d = document.querySelector(".product--section-title__desc");
  var tEmpty =
    t && typeof t.textContent === "string"
      ? t.textContent.trim() === ""
      : false;
  var dEmpty =
    d && typeof d.textContent === "string"
      ? d.textContent.trim() === ""
      : false;

  if (t && tEmpty && typeof data.title === "string") t.textContent = data.title;
  if (d && dEmpty && typeof data.description === "string")
    d.textContent = data.description;
})();
