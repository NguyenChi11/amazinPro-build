(function () {
  function init() {
    var hidden = document.getElementById("buildpro-option-data");
    var wrapper = document.getElementById("buildpro-option-wrapper");
    var addBtn = document.getElementById("buildpro-option-add");
    if (!hidden || !wrapper || !addBtn) return;
    var frame;
    function write() {
      var rows = wrapper.querySelectorAll(".buildpro-option-row");
      var data = [];
      rows.forEach(function (row) {
        var iconInput = row.querySelector("[data-field='icon_id']");
        var obj = { icon_id: 0, text: "", description: "" };
        obj.icon_id =
          parseInt(iconInput && iconInput.value ? iconInput.value : 0, 10) || 0;
        var textInput = row.querySelector("[data-field='text']");
        var descInput = row.querySelector("[data-field='description']");
        obj.text = textInput && textInput.value ? textInput.value : "";
        obj.description = descInput && descInput.value ? descInput.value : "";
        data.push(obj);
      });
      hidden.value = JSON.stringify(data);
      hidden.dispatchEvent(new Event("change"));
    }
    function bindTabs(row) {
      var tabs = row.querySelectorAll(".buildpro-option-tab");
      var contents = row.querySelectorAll(".tab-content");
      function showTab(name) {
        contents.forEach(function (c) {
          var isTarget = c.getAttribute("data-tab") === name;
          c.style.display = isTarget ? "block" : "none";
        });
        tabs.forEach(function (t) {
          var isTarget = t.getAttribute("data-tab") === name;
          if (isTarget) {
            t.classList.add("active");
          } else {
            t.classList.remove("active");
          }
        });
      }
      tabs.forEach(function (t) {
        t.addEventListener("click", function (e) {
          e.preventDefault();
          var name = t.getAttribute("data-tab");
          showTab(name);
        });
      });
    }
    function bindRow(row, openByDefault) {
      var header = row.querySelector(".buildpro-option-header");
      var body = row.querySelector(".buildpro-option-body");
      var labelEl = row.querySelector(".buildpro-option-label");
      var arrowEl = row.querySelector(".buildpro-option-arrow");
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
      bindTabs(row);
      var selectBtn = row.querySelector(".select-option-icon");
      var removeIconBtn = row.querySelector(".remove-option-icon");
      var input = row.querySelector("[data-field='icon_id']");
      var preview = row.querySelector(".option-icon-preview");
      var removeRowBtn = row.querySelector(".remove-option-row");
      var textInput = row.querySelector("[data-field='text']");
      var descInput = row.querySelector("[data-field='description']");
      function attachChange(el) {
        if (el) {
          el.addEventListener("input", write);
          el.addEventListener("change", write);
        }
      }
      attachChange(textInput);
      attachChange(descInput);
      if (textInput && labelEl) {
        textInput.addEventListener("input", function () {
          if (textInput.value) labelEl.textContent = textInput.value;
        });
      }
      if (selectBtn) {
        selectBtn.addEventListener("click", function (e) {
          e.preventDefault();
          if (!frame) {
            frame = wp.media({
              title: "Select icon",
              button: { text: "Use" },
              multiple: false,
            });
          }
          if (typeof frame.off === "function") {
            frame.off("select");
          }
          frame.on("select", function () {
            var attachment = frame.state().get("selection").first().toJSON();
            input.value = attachment.id;
            var url =
              attachment.sizes && attachment.sizes.thumbnail
                ? attachment.sizes.thumbnail.url
                : attachment.url;
            preview.innerHTML =
              "<img src='" + url + "' style='max-height:80px;'>";
            write();
          });
          frame.open();
        });
      }
      if (removeIconBtn) {
        removeIconBtn.addEventListener("click", function (e) {
          e.preventDefault();
          input.value = "";
          preview.innerHTML = "";
          write();
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
      wrapper.querySelectorAll(".buildpro-option-row"),
      bindRow,
    );
    addBtn.addEventListener("click", function (e) {
      e.preventDefault();
      var idx = wrapper.querySelectorAll(".buildpro-option-row").length;
      var html =
        '<div class="buildpro-option-row" data-index="' +
        idx +
        '">' +
        '  <div class="buildpro-option-header">' +
        '    <span class="buildpro-option-label">Item ' +
        (idx + 1) +
        "</span>" +
        '    <span class="buildpro-option-arrow">&#9660;</span>' +
        "  </div>" +
        '  <div class="buildpro-option-body" style="display:block">' +
        '    <div class="buildpro-option-tabs">' +
        '      <button type="button" class="buildpro-option-tab active" data-tab="icon">Icon</button>' +
        '      <button type="button" class="buildpro-option-tab" data-tab="content">Content</button>' +
        "    </div>" +
        '    <div class="buildpro-option-grid">' +
        '      <div class="buildpro-option-block tab-content" data-tab="icon" style="display:block">' +
        "        <h4>Icon</h4>" +
        '        <div class="buildpro-option-field">' +
        '          <input type="hidden" class="option-icon-id" data-field="icon_id" value="">' +
        '          <button type="button" class="button select-option-icon">Select icon</button>' +
        '          <button type="button" class="button remove-option-icon">Remove icon</button>' +
        "        </div>" +
        '        <div class="option-icon-preview" style="margin-top:8px;min-height:84px;display:flex;align-items:center;justify-content:center;background:#fff;border:1px dashed #ddd;border-radius:6px"><span style="color:#888">No icon selected</span></div>' +
        "      </div>" +
        '      <div class="buildpro-option-block tab-content" data-tab="content" style="display:none">' +
        "        <h4>Content</h4>" +
        '        <p class="buildpro-option-field"><label>Text</label><input type="text" class="regular-text" data-field="text" value=""></p>' +
        '        <p class="buildpro-option-field"><label>Description</label><textarea rows="4" class="large-text" data-field="description"></textarea></p>' +
        "      </div>" +
        "    </div>" +
        '    <div class="buildpro-option-actions"><button type="button" class="button remove-option-row">Remove item</button></div>' +
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
    var s = wp.customize.section("buildpro_option_section");
    if (s && s.expanded) {
      s.expanded.bind(function (exp) {
        if (exp) {
          setTimeout(init, 50);
        }
      });
    }
  }
  var obs = new MutationObserver(function () {
    if (document.getElementById("buildpro-option-wrapper")) {
      init();
      obs.disconnect();
    }
  });
  if (document.body) {
    obs.observe(document.body, { childList: true, subtree: true });
  }
})();
