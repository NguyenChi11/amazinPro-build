(function () {
  var i18n = window.buildproHomeI18n || {};
  function t(key, fallback) {
    return i18n && i18n[key] ? i18n[key] : fallback;
  }
  function formatItem(n) {
    var fmt = t("itemFormat", "Item %d");
    return String(fmt).replace(/%d/, String(n));
  }

  function init() {
    var hidden = document.getElementById("buildpro-data-data");
    var wrapper = document.getElementById("buildpro-data-wrapper");
    var addBtn = document.getElementById("buildpro-data-add");
    if (!hidden || !wrapper || !addBtn) return;
    function write() {
      var rows = wrapper.querySelectorAll(".buildpro-data-row");
      var data = [];
      rows.forEach(function (row) {
        var numberInput = row.querySelector("[data-field='number']");
        var textInput = row.querySelector("[data-field='text']");
        var obj = { number: "", text: "" };
        obj.number = numberInput && numberInput.value ? numberInput.value : "";
        obj.text = textInput && textInput.value ? textInput.value : "";
        data.push(obj);
      });
      hidden.value = JSON.stringify(data);
      hidden.dispatchEvent(new Event("change"));
    }
    function bindRow(row, openByDefault) {
      var header = row.querySelector(".buildpro-data-header");
      var body = row.querySelector(".buildpro-data-body");
      var labelEl = row.querySelector(".buildpro-data-label");
      var arrowEl = row.querySelector(".buildpro-data-arrow");
      if (header && body) {
        if (openByDefault) {
          body.style.display = "block";
          if (arrowEl) arrowEl.style.transform = "rotate(0deg)";
        }
        header.addEventListener("click", function () {
          var isOpen = body.style.display !== "none";
          body.style.display = isOpen ? "none" : "block";
          if (arrowEl)
            arrowEl.style.transform = isOpen
              ? "rotate(-90deg)"
              : "rotate(0deg)";
        });
      }
      var removeRowBtn = row.querySelector(".remove-data-row");
      var numberInput = row.querySelector("[data-field='number']");
      var textInput = row.querySelector("[data-field='text']");
      function attachChange(el) {
        if (el) {
          el.addEventListener("input", write);
          el.addEventListener("change", write);
        }
      }
      attachChange(numberInput);
      attachChange(textInput);
      if (textInput && labelEl) {
        textInput.addEventListener("input", function () {
          if (textInput.value) labelEl.textContent = textInput.value;
        });
      }
      if (removeRowBtn) {
        removeRowBtn.addEventListener("click", function (e) {
          e.preventDefault();
          row.parentNode.removeChild(row);
          write();
        });
      }
    }
    Array.prototype.forEach.call(
      wrapper.querySelectorAll(".buildpro-data-row"),
      bindRow,
    );
    addBtn.addEventListener("click", function (e) {
      e.preventDefault();
      var idx = wrapper.querySelectorAll(".buildpro-data-row").length;
      var html =
        '<div class="buildpro-data-row" data-index="' +
        idx +
        '">' +
        '  <div class="buildpro-data-header">' +
        '    <span class="buildpro-data-label">' +
        formatItem(idx + 1) +
        "</span>" +
        '    <span class="buildpro-data-arrow">&#9660;</span>' +
        "  </div>" +
        '  <div class="buildpro-data-body" style="display:block">' +
        '    <div class="buildpro-data-grid">' +
        '      <div class="buildpro-data-block">' +
        "        <h4>" +
        t("number", "Number") +
        "</h4>" +
        '        <p class="buildpro-data-field"><label>' +
        t("number", "Number") +
        '</label><input type="text" class="regular-text" data-field="number" value=""></p>' +
        "      </div>" +
        '      <div class="buildpro-data-block">' +
        "        <h4>" +
        t("text", "Text") +
        "</h4>" +
        '        <p class="buildpro-data-field"><label>' +
        t("text", "Text") +
        '</label><input type="text" class="regular-text" data-field="text" value=""></p>' +
        "      </div>" +
        "    </div>" +
        '    <div class="buildpro-data-actions"><button type="button" class="button remove-data-row">' +
        t("removeItem", "Remove item") +
        "</button></div>" +
        "  </div>" +
        "</div>";
      var temp = document.createElement("div");
      temp.innerHTML = html;
      var row = temp.firstElementChild;
      wrapper.appendChild(row);
      bindRow(row, true);
      write();
    });
    write();
  }
  function onReady() {
    init();
  }
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", onReady);
  } else {
    onReady();
  }
  if (window.wp && wp.customize && typeof wp.customize.section === "function") {
    var s = wp.customize.section("buildpro_data_section");
    if (s && s.expanded) {
      s.expanded.bind(function (exp) {
        if (exp) {
          setTimeout(init, 50);
        }
      });
    }
  }
  var obs = new MutationObserver(function () {
    if (document.getElementById("buildpro-data-wrapper")) {
      init();
      obs.disconnect();
    }
  });
  if (document.body) {
    obs.observe(document.body, { childList: true, subtree: true });
  }
})();
