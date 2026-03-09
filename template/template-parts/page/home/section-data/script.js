document.addEventListener("DOMContentLoaded", function () {
  var container = document.querySelector(".section-data-container");
  if (!container) return;
  if (
    container.children.length === 0 &&
    typeof dataItems !== "undefined" &&
    Array.isArray(dataItems)
  ) {
    dataItems.forEach(function (it) {
      var item = document.createElement("div");
      item.className = "section-data__item";
      var h3 = document.createElement("h3");
      h3.className = "section-data__item-number";
      h3.textContent = String(it.number || "");
      var p = document.createElement("p");
      p.className = "section-data__item-text";
      p.textContent = String(it.text || "");
      item.appendChild(h3);
      item.appendChild(p);
      container.appendChild(item);
    });
  }
});
