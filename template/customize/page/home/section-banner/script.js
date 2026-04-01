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
          var t = e.target;
          if (
            t &&
            t.classList &&
            t.classList.contains("buildpro-link-result")
          ) {
            var url = t.getAttribute("data-url") || "";
            if (urlInput) {
              urlInput.value = url;
            }
            if (titleInput) {
              titleInput.value = t.textContent || "";
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
          var s = wp.customize.section("buildpro_link_picker_section");
          if (s && typeof s.expand === "function") {
            s.expand();
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
    var s = wp.customize.section("buildpro_banner_section");
    if (s && s.expanded) {
      s.expanded.bind(function (exp) {
        if (exp) {
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
