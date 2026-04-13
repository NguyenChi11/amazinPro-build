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
    var hidden = document.getElementById("buildpro-banner-data");
    var wrapper = document.getElementById("buildpro-banner-wrapper");
    var addBtn = document.getElementById("buildpro-banner-add");
    if (!hidden || !wrapper || !addBtn) return;
    var frame;
    function write() {
      var rows = wrapper.querySelectorAll(".buildpro-banner-row");
      var data = [];
      rows.forEach(function (row) {
        var imageInput = row.querySelector("[data-field='image_id']");
        var obj = {
          image_id: 0,
          type: "",
          text: "",
          description: "",
          link_url: "",
          link_title: "",
          link_target: "",
        };
        obj.image_id =
          parseInt(imageInput && imageInput.value ? imageInput.value : 0, 10) ||
          0;
        var typeInput = row.querySelector("[data-field='type']");
        var textInput = row.querySelector("[data-field='text']");
        var descInput = row.querySelector("[data-field='description']");
        var urlInput = row.querySelector("[data-field='link_url']");
        var titleInput = row.querySelector("[data-field='link_title']");
        var targetSelect = row.querySelector("[data-field='link_target']");
        obj.type = typeInput && typeInput.value ? typeInput.value : "";
        obj.text = textInput && textInput.value ? textInput.value : "";
        obj.description = descInput && descInput.value ? descInput.value : "";
        obj.link_url = urlInput && urlInput.value ? urlInput.value : "";
        obj.link_title = titleInput && titleInput.value ? titleInput.value : "";
        obj.link_target =
          targetSelect && targetSelect.type === "checkbox"
            ? targetSelect.checked
              ? "_blank"
              : ""
            : targetSelect && targetSelect.value
              ? targetSelect.value
              : "";
        data.push(obj);
      });
      hidden.value = JSON.stringify(data);
      hidden.dispatchEvent(new Event("change"));
    }
    function bindRow(row, openByDefault) {
      var header = row.querySelector(".buildpro-banner-header");
      var body = row.querySelector(".buildpro-banner-body");
      var labelEl = row.querySelector(".buildpro-banner-label");
      var arrowEl = row.querySelector(".buildpro-banner-arrow");
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
      var selectBtn = row.querySelector(".select-banner-image");
      var removeImgBtn = row.querySelector(".remove-banner-image");
      var input = row.querySelector("[data-field='image_id']");
      var preview = row.querySelector(".banner-image-preview");
      var removeRowBtn = row.querySelector(".remove-banner-row");
      var linkBtn = row.querySelector(".choose-link");
      var panel = row.querySelector(".buildpro-link-panel");
      var searchInput = row.querySelector(".buildpro-link-search");
      var resultsBox = row.querySelector(".buildpro-link-results");
      var panelTargetToggle = row.querySelector(".buildpro-link-target");
      var closePanelBtn = row.querySelector(".buildpro-link-close");
      var typeInput = row.querySelector("[data-field='type']");
      var textInput = row.querySelector("[data-field='text']");
      var descInput = row.querySelector("[data-field='description']");
      var urlInput = row.querySelector("[data-field='link_url']");
      var titleInput = row.querySelector("[data-field='link_title']");
      var targetSelect = row.querySelector("[data-field='link_target']");
      function attachChange(el) {
        if (el) {
          el.addEventListener("input", write);
          el.addEventListener("change", write);
        }
      }
      attachChange(typeInput);
      attachChange(textInput);
      attachChange(descInput);
      attachChange(urlInput);
      attachChange(titleInput);
      attachChange(targetSelect);
      if (textInput && labelEl) {
        textInput.addEventListener("input", function () {
          labelEl.textContent = textInput.value || labelEl.textContent;
        });
      }
      function showLinkPanel() {
        if (!panel) return;
        panel.style.display = "block";
        if (searchInput) {
          searchInput.focus();
        }
        performSearch("");
      }
      function hideLinkPanel() {
        if (!panel) return;
        panel.style.display = "none";
      }
      function renderResults(items) {
        if (!resultsBox) return;
        if (!items || !items.length) {
          resultsBox.innerHTML =
            "<p style='color:#888;margin:6px'>" +
            t("noResultsFound", "No results found.") +
            "</p>";
          return;
        }
        var html = items
          .map(function (it) {
            var title =
              it.title && it.title.rendered
                ? it.title.rendered
                : it.title || "";
            var url = it.url || it.link || "";
            return (
              "<button type='button' class='button button-secondary buildpro-link-result' data-url='" +
              (url || "") +
              "'>" +
              (title || url) +
              "</button>"
            );
          })
          .join(" ");
        resultsBox.innerHTML = html;
      }
      function performSearch(q) {
        var endpoint =
          "/wp-json/wp/v2/search?search=" +
          encodeURIComponent(q || "") +
          "&per_page=20";
        fetch(endpoint, { credentials: "same-origin" })
          .then(function (r) {
            return r.json();
          })
          .then(function (data) {
            var items = (data || []).map(function (d) {
              return { title: d.title, url: d.url };
            });
            renderResults(items);
          })
          .catch(function () {
            renderResults([]);
          });
      }
      if (searchInput) {
        var debounce;
        searchInput.addEventListener("input", function () {
          var q = searchInput.value || "";
          clearTimeout(debounce);
          debounce = setTimeout(function () {
            performSearch(q);
          }, 250);
        });
      }
      if (resultsBox) {
        resultsBox.addEventListener("click", function (e) {
          var target = e.target;
          if (
            target &&
            target.classList &&
            target.classList.contains("buildpro-link-result")
          ) {
            var url = target.getAttribute("data-url") || "";
            if (urlInput) {
              urlInput.value = url;
            }
            if (titleInput) {
              titleInput.value = target.textContent || "";
            }
            if (targetSelect && panelTargetToggle) {
              if (targetSelect.type === "checkbox") {
                targetSelect.checked = !!panelTargetToggle.checked;
              } else {
                targetSelect.value = panelTargetToggle.checked ? "_blank" : "";
              }
            }
            write();
            hideLinkPanel();
          }
        });
      }
      if (closePanelBtn) {
        closePanelBtn.addEventListener("click", function (e) {
          e.preventDefault();
          hideLinkPanel();
        });
      }
      function goToLinkPicker() {
        window.buildproLinkTarget = {
          urlInput: urlInput,
          titleInput: titleInput,
          targetSelect: targetSelect,
          sectionId: "buildpro_banner_section",
        };
        if (
          window.wp &&
          wp.customize &&
          typeof wp.customize.section === "function"
        ) {
          var section = wp.customize.section("buildpro_link_picker_section");
          if (section && typeof section.expand === "function") {
            section.expand();
            return;
          }
        }
        showLinkPanel();
      }
      if (selectBtn) {
        selectBtn.addEventListener("click", function (e) {
          e.preventDefault();
          if (!frame) {
            frame = wp.media({
              title: t("selectImage", "Select image"),
              button: { text: t("useImage", "Use Image") },
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
      if (removeImgBtn) {
        removeImgBtn.addEventListener("click", function (e) {
          e.preventDefault();
          input.value = "";
          preview.innerHTML =
            "<span style='color:#888'>" +
            t("noImageSelected", "No image selected") +
            "</span>";
          write();
        });
      }
      if (linkBtn) {
        linkBtn.addEventListener("click", function (e) {
          e.preventDefault();
          goToLinkPicker();
        });
      }
      if (urlInput) {
        urlInput.addEventListener("click", function (e) {
          e.preventDefault();
          goToLinkPicker();
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
      wrapper.querySelectorAll(".buildpro-banner-row"),
      bindRow,
    );
    addBtn.addEventListener("click", function (e) {
      e.preventDefault();
      var idx = wrapper.querySelectorAll(".buildpro-banner-row").length;
      var tmpl = document.getElementById("buildpro-banner-template");
      var row = tmpl.content.cloneNode(true).firstElementChild;
      row.setAttribute("data-index", idx);
      row.querySelector(".buildpro-banner-label").textContent = formatItem(
        idx + 1,
      );
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
    var section = wp.customize.section("buildpro_banner_section");
    if (section && section.expanded) {
      section.expanded.bind(function (expanded) {
        if (expanded) {
          setTimeout(init, 50);
        }
      });
    }
  }
  var obs = new MutationObserver(function () {
    if (document.getElementById("buildpro-banner-wrapper")) {
      init();
      obs.disconnect();
    }
  });
  if (document.body) {
    obs.observe(document.body, { childList: true, subtree: true });
  }
})();

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
        contents.forEach(function (content) {
          var isTarget = content.getAttribute("data-tab") === name;
          content.style.display = isTarget ? "block" : "none";
        });
        tabs.forEach(function (tab) {
          var isTarget = tab.getAttribute("data-tab") === name;
          if (isTarget) {
            tab.classList.add("active");
          } else {
            tab.classList.remove("active");
          }
        });
      }
      tabs.forEach(function (tab) {
        tab.addEventListener("click", function (e) {
          e.preventDefault();
          var name = tab.getAttribute("data-tab");
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
              title: t("selectIcon", "Select icon"),
              button: { text: t("use", "Use") },
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
          preview.innerHTML =
            "<span style='color:#888'>" +
            t("noIconSelected", "No icon selected") +
            "</span>";
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
        '    <span class="buildpro-option-label">' +
        formatItem(idx + 1) +
        "</span>" +
        '    <span class="buildpro-option-arrow">&#9660;</span>' +
        "  </div>" +
        '  <div class="buildpro-option-body" style="display:block">' +
        '    <div class="buildpro-option-tabs">' +
        '      <button type="button" class="buildpro-option-tab active" data-tab="icon">' +
        t("icon", "Icon") +
        "</button>" +
        '      <button type="button" class="buildpro-option-tab" data-tab="content">' +
        t("content", "Content") +
        "</button>" +
        "    </div>" +
        '    <div class="buildpro-option-grid">' +
        '      <div class="buildpro-option-block tab-content" data-tab="icon" style="display:block">' +
        "        <h4>" +
        t("icon", "Icon") +
        "</h4>" +
        '        <div class="buildpro-option-field">' +
        '          <input type="hidden" class="option-icon-id" data-field="icon_id" value="">' +
        '          <button type="button" class="button select-option-icon">' +
        t("selectIcon", "Select icon") +
        "</button>" +
        '          <button type="button" class="button remove-option-icon">' +
        t("removeIcon", "Remove icon") +
        "</button>" +
        "        </div>" +
        '        <div class="option-icon-preview" style="margin-top:8px;min-height:84px;display:flex;align-items:center;justify-content:center;background:#fff;border:1px dashed #ddd;border-radius:6px"><span style="color:#888">' +
        t("noIconSelected", "No icon selected") +
        "</span></div>" +
        "      </div>" +
        '      <div class="buildpro-option-block tab-content" data-tab="content" style="display:none">' +
        "        <h4>" +
        t("content", "Content") +
        "</h4>" +
        '        <p class="buildpro-option-field"><label>' +
        t("text", "Text") +
        '</label><input type="text" class="regular-text" data-field="text" value=""></p>' +
        '        <p class="buildpro-option-field"><label>' +
        t("description", "Description") +
        '</label><textarea rows="4" class="large-text" data-field="description"></textarea></p>' +
        "      </div>" +
        "    </div>" +
        '    <div class="buildpro-option-actions"><button type="button" class="button remove-option-row">' +
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
    var section = wp.customize.section("buildpro_banner_section");
    if (section && section.expanded) {
      section.expanded.bind(function (expanded) {
        if (expanded) {
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
