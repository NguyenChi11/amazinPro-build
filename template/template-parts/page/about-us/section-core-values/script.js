document.addEventListener("DOMContentLoaded", function () {
  var section = document.querySelector(".about-core-values[data-auto='1']");
  if (!section) return;
  if (typeof aboutUsCoreValuesData === "undefined") return;
  var data = aboutUsCoreValuesData || {};
  var title = data.title || "";
  var description = data.description || "";
  var items = Array.isArray(data.items) ? data.items : [];

  var headerTitle = section.querySelector(".about-core-values__title");
  var headerDesc = section.querySelector(".about-core-values__description");
  if (headerTitle && title) headerTitle.textContent = title;
  if (headerDesc && description) headerDesc.textContent = description;

  var grid = section.querySelector(".about-core-values__grid");
  if (!grid) return;
  if (grid.children.length > 0) return;

  items.forEach(function (it) {
    var card = document.createElement("div");
    card.className = "about-core-values__card";

    var iconWrap = document.createElement("div");
    iconWrap.className = "about-core-values__icon";
    var i = document.createElement("i");
    i.className = "fa-solid";
    var fa = "fa-circle-info";
    switch ((it.icon || "").toLowerCase()) {
      case "shield-halved":
        fa = "fa-shield-halved";
        break;
      case "lightbulb":
        fa = "fa-lightbulb";
        break;
      case "clipboard-check":
        fa = "fa-clipboard-check";
        break;
      case "leaf":
        fa = "fa-leaf";
        break;
    }
    i.classList.add(fa);
    iconWrap.appendChild(i);

    var h3 = document.createElement("h3");
    h3.className = "about-core-values__card-title";
    h3.textContent = String(it.title || "");

    var p = document.createElement("p");
    p.className = "about-core-values__card-desc";
    p.textContent = String(it.description || "");

    var a = document.createElement("a");
    a.className = "about-core-values__card-link";
    a.href = String(it.url || "#");
    var sp = document.createElement("span");
    sp.textContent = "View Details";
    var ai = document.createElement("i");
    ai.className = "fa-solid fa-arrow-right";
    a.appendChild(sp);
    a.appendChild(ai);

    card.appendChild(iconWrap);
    card.appendChild(h3);
    card.appendChild(p);
    card.appendChild(a);
    grid.appendChild(card);
  });
});
