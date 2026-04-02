/* ============================================================
   Footer Customizer Controls
   Uses event delegation so it works regardless of when the
   Customizer renders the control DOM.
   ============================================================ */
(function () {
  function getWpRef() {
    if (window.wp) return window.wp;
    try {
      if (window.parent && window.parent.wp) return window.parent.wp;
    } catch (e) {}
    return null;
  }
  var footerI18n =
    window.buildproFooterI18n &&
    typeof window.buildproFooterI18n === "object" &&
    window.buildproFooterI18n
      ? window.buildproFooterI18n
      : {};
  function t(key, fallback) {
    var val = footerI18n ? footerI18n[key] : null;
    return typeof val === "string" && val ? val : fallback;
  }
  function escHtml(s) {
    return String(s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function readTargetValue(targetEl) {
    if (!targetEl) return "";
    if (targetEl.type === "checkbox") {
      return targetEl.checked ? "_blank" : "";
    }
    return targetEl.value || "";
  }

  /* ── helpers ─────────────────────────────────────────────── */
  function getCLWrap() {
    return document.getElementById("customizer-footer-contact-links-wrapper");
  }
  function getCLInput() {
    return (
      document.getElementById("buildpro-footer-contact-links-data") ||
      document.querySelector(".footer-contact-links-json")
    );
  }

  function initContactLinksRepeater() {
    var wrap = getCLWrap();
    var input = getCLInput();
    var addBtn = document.getElementById("customizer-footer-contact-links-add");
    var tmpl = document.getElementById(
      "buildpro-footer-contact-links-template",
    );
    if (!wrap || !input || !addBtn || !tmpl) return;

    function formatItem(n) {
      return "Item " + String(n);
    }

    function updateRowLabel(row) {
      if (!row || !row.querySelector) return;
      var labelEl = row.querySelector(".buildpro-footer-row-label");
      if (!labelEl) return;
      var titleInput = row.querySelector('[data-field="title"]');
      var title =
        titleInput && titleInput.value ? String(titleInput.value).trim() : "";

      var fallbackN = 1;
      var wrapper =
        row.closest && row.closest("#customizer-footer-contact-links-wrapper");
      if (wrapper) {
        var rows = wrapper.querySelectorAll(".buildpro-block");
        for (var i = 0; i < rows.length; i++) {
          if (rows[i] === row) {
            fallbackN = i + 1;
            break;
          }
        }
      }
      labelEl.textContent = title || formatItem(fallbackN);
    }

    function bindContactLinkRow(row) {
      if (!row || !row.querySelector) return;
      if (row.dataset && row.dataset.buildproRowBound === "1") return;
      if (row.dataset) row.dataset.buildproRowBound = "1";

      // Collapsible header/body
      var header = row.querySelector(".buildpro-footer-row-header");
      var body = row.querySelector(".buildpro-footer-row-body");
      if (header && body) {
        function toggleBody() {
          var isOpen = body.style.display !== "none";
          body.style.display = isOpen ? "none" : "block";
          header.setAttribute("aria-expanded", isOpen ? "false" : "true");
        }
        header.addEventListener("click", toggleBody);
        header.addEventListener("keydown", function (e) {
          var key = e && e.key ? e.key : "";
          if (key === "Enter" || key === " ") {
            e.preventDefault();
            toggleBody();
          }
        });
      }

      var selectBtn = row.querySelector(".select-contact-icon");
      var removeBtn = row.querySelector(".remove-contact-icon");

      var titleInput = row.querySelector('[data-field="title"]');
      if (titleInput) {
        titleInput.addEventListener("input", function () {
          updateRowLabel(row);
        });
        titleInput.addEventListener("change", function () {
          updateRowLabel(row);
        });
      }

      if (selectBtn) {
        selectBtn.addEventListener("click", function (e) {
          e.preventDefault();
          e.stopPropagation();
          if (typeof e.stopImmediatePropagation === "function") {
            e.stopImmediatePropagation();
          }

          var idInput = row.querySelector('[data-field="icon_id"]');
          var preview = row.querySelector(".contact-icon-preview");
          var wpRef = getWpRef();
          if (!wpRef || typeof wpRef.media !== "function") return;

          if (!row._iconFrame) {
            row._iconFrame = wpRef.media({
              title: t("selectIconTitle", "Select Icon"),
              multiple: false,
              library: { type: "image" },
            });
            row._iconFrame.on("select", function () {
              var state = row._iconFrame.state && row._iconFrame.state();
              var selection =
                state && state.get ? state.get("selection") : null;
              var first =
                selection && selection.first ? selection.first() : null;
              if (!first || !first.toJSON) return;
              var file = first.toJSON();

              if (idInput) {
                idInput.value = String(file.id || 0);
                idInput.dispatchEvent(new Event("input"));
              }
              if (preview) {
                var imgUrl =
                  (file &&
                    file.sizes &&
                    file.sizes.thumbnail &&
                    file.sizes.thumbnail.url) ||
                  file.url ||
                  "";
                preview.innerHTML = imgUrl
                  ? '<img src="' + imgUrl + '" style="max-height:80px;">'
                  : '<span style="color:#888">' +
                    escHtml(t("noImageSelected", "No image selected")) +
                    "</span>";
              }
              collectContactLinks();
            });
          }
          row._iconFrame.open();
        });
      }

      if (removeBtn) {
        removeBtn.addEventListener("click", function (e) {
          e.preventDefault();
          e.stopPropagation();
          if (typeof e.stopImmediatePropagation === "function") {
            e.stopImmediatePropagation();
          }

          var idInput = row.querySelector('[data-field="icon_id"]');
          var preview = row.querySelector(".contact-icon-preview");
          if (idInput) {
            idInput.value = "0";
            idInput.dispatchEvent(new Event("input"));
          }
          if (preview) {
            preview.innerHTML =
              '<span style="color:#888">' +
              escHtml(t("noImageSelected", "No image selected")) +
              "</span>";
          }
          collectContactLinks();
        });
      }

      // Initial label set
      updateRowLabel(row);
    }

    wrap.querySelectorAll(".buildpro-block").forEach(bindContactLinkRow);
    if (addBtn.dataset && addBtn.dataset.buildproBound === "1") {
      return;
    }
    if (addBtn.dataset) {
      addBtn.dataset.buildproBound = "1";
    }
    addBtn.addEventListener("click", function (e) {
      e.preventDefault();
      var node = tmpl.content.cloneNode(true);
      wrap.appendChild(node);
      var last = wrap.lastElementChild;
      bindContactLinkRow(last);
      collectContactLinks();
    });

    // Ensure the hidden JSON always reflects current DOM.
    collectContactLinks();
  }

  function getSingleLinkInput(wrap) {
    if (!wrap) return null;
    return wrap.querySelector(".footer-single-link-json");
  }

  function collectSingleLink(wrap) {
    var input = getSingleLinkInput(wrap);
    if (!wrap || !input) return;
    var targetEl = wrap.querySelector('[data-field="target"]');
    var out = {
      url: (wrap.querySelector('[data-field="url"]') || {}).value || "",
      title: (wrap.querySelector('[data-field="title"]') || {}).value || "",
      target: readTargetValue(targetEl),
    };
    input.value = JSON.stringify(out);
    input.dispatchEvent(new Event("change", { bubbles: true }));
  }

  function collectContactLinks() {
    var wrap = getCLWrap();
    var input = getCLInput();
    if (!wrap || !input) return;
    var out = [];
    wrap.querySelectorAll(".buildpro-block").forEach(function (row) {
      var icon = row.querySelector('[data-field="icon_id"]');
      var targetEl = row.querySelector('[data-field="target"]');
      out.push({
        icon_id: icon ? parseInt(icon.value || 0, 10) : 0,
        url: (row.querySelector('[data-field="url"]') || {}).value || "",
        title: (row.querySelector('[data-field="title"]') || {}).value || "",
        target: readTargetValue(targetEl),
      });
    });
    input.value = JSON.stringify(out);
    input.dispatchEvent(new Event("change", { bubbles: true }));
  }

  function navigateToLinkPicker(urlEl, titleEl, targetEl) {
    var url = urlEl ? urlEl.value || "" : "";
    var title = titleEl ? titleEl.value || "" : "";
    var tgt = readTargetValue(targetEl);
    window.buildproLinkTarget = {
      urlInput: urlEl,
      titleInput: titleEl,
      targetSelect: targetEl,
      sectionId: "buildpro_footer_section",
      currentUrl: url,
      currentTitle: title,
      currentTarget: tgt,
    };
    var wpRef = getWpRef();
    if (
      wpRef &&
      wpRef.customize &&
      typeof wpRef.customize.section === "function"
    ) {
      var s = wpRef.customize.section("buildpro_link_picker_section");
      if (s && typeof s.expand === "function") {
        s.expand();
        return true;
      }
    }
    return false;
  }

  /* ── init (match Banner Items behavior) ──────────────────────────── */
  function onReady() {
    initContactLinksRepeater();
  }
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", onReady);
  } else {
    onReady();
  }
  var wpRef = getWpRef();
  if (
    wpRef &&
    wpRef.customize &&
    typeof wpRef.customize.section === "function"
  ) {
    var footerSection = wpRef.customize.section("buildpro_footer_section");
    if (footerSection && footerSection.expanded) {
      footerSection.expanded.bind(function (exp) {
        if (exp) {
          setTimeout(initContactLinksRepeater, 50);
        }
      });
    }
  }
  var footerObs = new MutationObserver(function () {
    if (getCLWrap() && getCLInput()) {
      initContactLinksRepeater();
    }
  });
  if (document.body) {
    footerObs.observe(document.body, { childList: true, subtree: true });
  }

  /* ── global event delegation (works after Customizer renders DOM) ── */
  document.addEventListener(
    "input",
    function (e) {
      var t = e.target;
      if (!t) return;
      var single = t.closest(".buildpro-single-link-wrapper");
      if (single) {
        collectSingleLink(single);
        return;
      }
      var wrap = t.closest("#customizer-footer-contact-links-wrapper");
      if (wrap) {
        collectContactLinks();
      }
    },
    true,
  );

  document.addEventListener(
    "change",
    function (e) {
      var t = e.target;
      if (!t) return;
      var single = t.closest(".buildpro-single-link-wrapper");
      if (single) {
        collectSingleLink(single);
        return;
      }
      var wrap = t.closest("#customizer-footer-contact-links-wrapper");
      if (wrap) {
        collectContactLinks();
      }
    },
    true,
  );

  document.addEventListener(
    "click",
    function (e) {
      var t = e.target;
      // In some browsers the click target can be a Text node.
      if (t && t.nodeType === 3) {
        t = t.parentElement;
      }
      if (!t) return;

      /* ── Remove Item ── */
      var removeBtn =
        t.closest &&
        t.closest("#customizer-footer-contact-links-wrapper .remove-row");
      if (removeBtn) {
        var inCL = removeBtn.closest(
          "#customizer-footer-contact-links-wrapper",
        );
        if (!inCL) return;
        e.preventDefault();
        var row = removeBtn.closest(".buildpro-block");
        if (row) {
          row.parentNode.removeChild(row);
          collectContactLinks();
        }
        return;
      }

      /* ── Choose Link (navigate to Link Picker section) ── */
      var chooseLinkBtn = t.closest && t.closest(".choose-link");
      if (chooseLinkBtn) {
        var inCL = chooseLinkBtn.closest(
          "#customizer-footer-contact-links-wrapper",
        );
        var inSingle = chooseLinkBtn.closest(".buildpro-single-link-wrapper");
        if (!inCL && !inSingle) return;
        e.preventDefault();
        e.stopImmediatePropagation();
        var row = inCL
          ? chooseLinkBtn.closest(".buildpro-block")
          : chooseLinkBtn.closest(".buildpro-single-link-wrapper");
        if (!row) return;
        navigateToLinkPicker(
          row.querySelector('[data-field="url"]'),
          row.querySelector('[data-field="title"]'),
          row.querySelector('[data-field="target"]'),
        );
        return;
      }
    },
    true,
  );
})();

(function (wp) {
  var footerI18n =
    window.buildproFooterI18n &&
    typeof window.buildproFooterI18n === "object" &&
    window.buildproFooterI18n
      ? window.buildproFooterI18n
      : {};
  function t(key, fallback) {
    var val = footerI18n ? footerI18n[key] : null;
    return typeof val === "string" && val ? val : fallback;
  }
  function escHtml(s) {
    return String(s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  /* Admin page only — skip when inside WP Customizer */
  if (window.wp && window.wp.customize) return;
  (function () {
    var tabs = document.querySelectorAll(".nav-tab-wrapper .nav-tab");
    var sections = document.querySelectorAll(".buildpro-footer-section");
    Array.prototype.forEach.call(tabs, function (tab) {
      tab.addEventListener("click", function (e) {
        e.preventDefault();
        Array.prototype.forEach.call(tabs, function (t) {
          t.classList.remove("nav-tab-active");
        });
        tab.classList.add("nav-tab-active");
        var id = tab.getAttribute("href");
        Array.prototype.forEach.call(sections, function (sec) {
          sec.classList.remove("active");
        });
        var target = document.querySelector(id);
        if (target) {
          target.classList.add("active");
        }
      });
    });
    function selectImage(buttonId, inputId, previewId) {
      var btn = document.getElementById(buttonId);
      var removeBtnId = buttonId.replace("select_", "remove_");
      var removeBtn = document.getElementById(removeBtnId);
      var input = document.getElementById(inputId);
      var preview = document.getElementById(previewId);
      var frame;
      if (btn) {
        btn.addEventListener("click", function (e) {
          e.preventDefault();
          if (!window.wp || typeof window.wp.media !== "function") {
            console.error(
              "[BuildPro] wp.media is not available. Make sure wp_enqueue_media() was called.",
            );
            return;
          }
          if (!frame) {
            frame = window.wp.media({
              title: t("chooseImage", "Choose image"),
              button: { text: t("useImage", "Use image") },
              multiple: false,
            });
            frame.on("select", function () {
              var state = frame.state();
              if (!state) return;
              var selection = state.get("selection");
              if (!selection || !selection.first()) return;
              var attachment = selection.first().toJSON();
              if (input) {
                input.value = attachment.id;
                input.dispatchEvent(new Event("change", { bubbles: true }));
              }
              var url =
                attachment.sizes && attachment.sizes.thumbnail
                  ? attachment.sizes.thumbnail.url
                  : attachment.url;
              if (preview) {
                preview.innerHTML =
                  "<img src='" + url + "' style='max-height:80px;'>";
              }
            });
          }
          frame.open();
        });
      }
      if (removeBtn) {
        removeBtn.addEventListener("click", function (e) {
          e.preventDefault();
          if (input) input.value = "";
          if (preview)
            preview.innerHTML =
              "<span style='color:#888'>" +
              escHtml(t("noImageSelected", "No image selected")) +
              "</span>";
        });
      }
    }
    selectImage(
      "select_footer_banner_image",
      "footer_banner_image_id",
      "footer_banner_preview",
    );

    function readTargetValue(targetEl) {
      if (!targetEl) return "";
      if (targetEl.type === "checkbox") {
        return targetEl.checked ? "_blank" : "";
      }
      return targetEl.value || "";
    }

    function writeTargetValue(targetEl, value) {
      if (!targetEl) return;
      if (targetEl.type === "checkbox") {
        targetEl.checked = value === "_blank";
        return;
      }
      targetEl.value = value || "";
    }

    var customCtx = { urlInput: null, titleInput: null, targetSelect: null };
    function showCustom() {
      var b = document.getElementById("buildpro-custom-link-backdrop");
      var m = document.getElementById("buildpro-custom-link-modal");
      if (b) b.style.display = "block";
      if (m) m.style.display = "block";
      fetchRecent();
    }
    function hideCustom() {
      var b = document.getElementById("buildpro-custom-link-backdrop");
      var m = document.getElementById("buildpro-custom-link-modal");
      if (b) b.style.display = "none";
      if (m) m.style.display = "none";
    }
    function openCustomLinkPicker(urlInput, titleInput, targetSelect) {
      customCtx.urlInput = urlInput;
      customCtx.titleInput = titleInput;
      customCtx.targetSelect = targetSelect;
      var urlEl = document.getElementById("buildpro_custom_link_url");
      var titleEl = document.getElementById("buildpro_custom_link_title");
      var targetEl = document.getElementById("buildpro_custom_link_target");
      if (urlEl) urlEl.value = urlInput && urlInput.value ? urlInput.value : "";
      if (titleEl)
        titleEl.value = titleInput && titleInput.value ? titleInput.value : "";
      if (targetEl)
        targetEl.checked = readTargetValue(targetSelect) === "_blank";
      showCustom();
    }
    function applyCustom() {
      var urlEl = document.getElementById("buildpro_custom_link_url");
      var titleEl = document.getElementById("buildpro_custom_link_title");
      var targetEl = document.getElementById("buildpro_custom_link_target");
      if (customCtx.urlInput && urlEl)
        customCtx.urlInput.value = urlEl.value || "";
      if (customCtx.titleInput && titleEl)
        customCtx.titleInput.value = titleEl.value || "";
      if (customCtx.targetSelect && targetEl)
        writeTargetValue(
          customCtx.targetSelect,
          targetEl.checked ? "_blank" : "",
        );
      hideCustom();
    }
    function fetchJSON(u) {
      try {
        if (window.wp && window.wp.apiFetch) {
          var p = u.replace(/^\/wp-json\//, "");
          return window.wp.apiFetch({ path: p }).catch(function () {
            return [];
          });
        }
      } catch (e) {}
      return fetch(u, { credentials: "same-origin" })
        .then(function (r) {
          return r.ok ? r.json() : [];
        })
        .catch(function () {
          return [];
        });
    }
    function renderItems(items) {
      var results = document.getElementById("buildpro_custom_link_results");
      if (results) results.innerHTML = "";
      items.forEach(function (m) {
        var div = document.createElement("div");
        div.className = "buildpro-custom-link-item";
        div.textContent = m.title + " (" + m.type + ")";
        div.addEventListener("click", function () {
          var urlEl = document.getElementById("buildpro_custom_link_url");
          var titleEl = document.getElementById("buildpro_custom_link_title");
          if (urlEl) urlEl.value = m.url;
          if (titleEl) titleEl.value = m.title;
        });
        if (results) results.appendChild(div);
      });
    }
    function fetchRecent() {
      var qpages = "/wp-json/wp/v2/pages?per_page=20&orderby=date&order=desc";
      var qposts = "/wp-json/wp/v2/posts?per_page=20&orderby=date&order=desc";
      Promise.all([fetchJSON(qpages), fetchJSON(qposts)]).then(function (res) {
        var pages = res[0].map(function (it) {
          return {
            title:
              it.title && it.title.rendered
                ? it.title.rendered
                : it.slug || t("page", "Page"),
            url: it.link,
            type: "PAGE",
            date: new Date(it.date),
          };
        });
        var posts = res[1].map(function (it) {
          return {
            title:
              it.title && it.title.rendered
                ? it.title.rendered
                : it.slug || t("post", "Post"),
            url: it.link,
            type: "POST",
            date: new Date(it.date),
          };
        });
        var all = pages.concat(posts).sort(function (a, b) {
          return b.date - a.date;
        });
        renderItems(all);
      });
    }
    function searchContent(q, source) {
      var qparam = q ? "&search=" + encodeURIComponent(q) : "";
      if (source === "page") {
        fetchJSON("/wp-json/wp/v2/pages?per_page=20" + qparam).then(
          function (items) {
            renderItems(
              items.map(function (it) {
                return {
                  title:
                    it.title && it.title.rendered
                      ? it.title.rendered
                      : it.slug || t("page", "Page"),
                  url: it.link,
                  type: "PAGE",
                };
              }),
            );
          },
        );
      } else if (source === "post") {
        fetchJSON("/wp-json/wp/v2/posts?per_page=20" + qparam).then(
          function (items) {
            renderItems(
              items.map(function (it) {
                return {
                  title:
                    it.title && it.title.rendered
                      ? it.title.rendered
                      : it.slug || t("post", "Post"),
                  url: it.link,
                  type: "POST",
                };
              }),
            );
          },
        );
      } else {
        fetchJSON(
          "/wp-json/wp/v2/search?per_page=20" +
            (q ? "&search=" + encodeURIComponent(q) : ""),
        ).then(function (items) {
          renderItems(
            items.map(function (it) {
              return {
                title: it.title || it.url,
                url: it.url,
                type:
                  it.type === "post"
                    ? (it.subtype || "POST").toUpperCase()
                    : "LINK",
              };
            }),
          );
        });
      }
    }
    var cancelBtn = document.getElementById("buildpro_custom_link_cancel");
    var applyBtn = document.getElementById("buildpro_custom_link_apply");
    var searchInput = document.getElementById("buildpro_custom_link_search");
    var sourceSel = document.getElementById("buildpro_custom_link_source");
    if (cancelBtn) cancelBtn.addEventListener("click", hideCustom);
    if (applyBtn) applyBtn.addEventListener("click", applyCustom);
    if (searchInput)
      searchInput.addEventListener("input", function (e) {
        var q = e.target.value;
        if (q) {
          searchContent(q, sourceSel ? sourceSel.value : "all");
        } else {
          fetchRecent();
        }
      });
    if (sourceSel)
      sourceSel.addEventListener("change", function () {
        var q = searchInput ? searchInput.value : "";
        if (q) {
          searchContent(q, sourceSel.value);
        } else {
          fetchRecent();
        }
      });
    fetchRecent();
    window.buildproOpenCustom = function (btn) {
      var row = btn.closest(".buildpro-block");
      var urlInput = row ? row.querySelector("input[name$='[url]']") : null;
      var titleInput = row ? row.querySelector("input[name$='[title]']") : null;
      var targetSelect = row ? row.querySelector("[name$='[target]']") : null;
      openCustomLinkPicker(urlInput, titleInput, targetSelect);
      return false;
    };
    document.addEventListener(
      "click",
      function (e) {
        var t = e.target;
        if (!t) return;
        if (t.classList && t.classList.contains("choose-link")) {
          e.preventDefault();
          var row = t.closest(".buildpro-block");
          var urlInput = row ? row.querySelector("input[name$='[url]']") : null;
          var titleInput = row
            ? row.querySelector("input[name$='[title]']")
            : null;
          var targetSelect = row
            ? row.querySelector("[name$='[target]']")
            : null;
          openCustomLinkPicker(urlInput, titleInput, targetSelect);
        }
      },
      true,
    );
    function bindRow(row) {
      var linkBtn = row.querySelector(".choose-link");
      var urlInput = row.querySelector("input[name$='[url]']");
      var titleInput = row.querySelector("input[name$='[title]']");
      var targetSelect = row.querySelector("[name$='[target]']");
      var removeRowBtn = row.querySelector(".remove-row");
      var selectIconBtn = row.querySelector(".select-contact-icon");
      var removeIconBtn = row.querySelector(".remove-contact-icon");
      var iconInput = row.querySelector("input[name$='[icon_id]']");
      var iconPreview = row.querySelector(".contact-icon-preview");
      var iconFrame;
      if (linkBtn)
        linkBtn.addEventListener("click", function (e) {
          e.preventDefault();
          openCustomLinkPicker(urlInput, titleInput, targetSelect);
        });
      if (urlInput)
        urlInput.addEventListener("click", function (e) {
          e.preventDefault();
          openCustomLinkPicker(urlInput, titleInput, targetSelect);
        });
      if (removeRowBtn)
        removeRowBtn.addEventListener("click", function (e) {
          e.preventDefault();
          row.parentNode.removeChild(row);
        });
      if (selectIconBtn) {
        selectIconBtn.addEventListener("click", function (e) {
          e.preventDefault();
          if (!window.wp || typeof window.wp.media !== "function") return;
          if (!iconFrame) {
            iconFrame = window.wp.media({
              title: t("selectImage", "Select Image"),
              button: { text: t("useImage", "Use Image") },
              multiple: false,
            });
            iconFrame.on("select", function () {
              var state = iconFrame.state();
              if (!state) return;
              var selection = state.get("selection");
              if (!selection || !selection.first()) return;
              var attachment = selection.first().toJSON();
              if (iconInput) iconInput.value = attachment.id;
              var url =
                attachment.sizes && attachment.sizes.thumbnail
                  ? attachment.sizes.thumbnail.url
                  : attachment.url;
              if (iconPreview)
                iconPreview.innerHTML =
                  "<img src='" + url + "' style='max-height:80px;'>";
            });
          }
          iconFrame.open();
        });
      }
      if (removeIconBtn) {
        removeIconBtn.addEventListener("click", function (e) {
          e.preventDefault();
          if (iconInput) iconInput.value = "";
          if (iconPreview)
            iconPreview.innerHTML =
              "<span style='color:#888'>" +
              escHtml(t("noImageSelected", "No image selected")) +
              "</span>";
        });
      }
    }
    Array.prototype.forEach.call(
      document.querySelectorAll(
        "#footer-contact-links-wrapper .buildpro-block",
      ),
      bindRow,
    );
    var addCL = document.getElementById("footer-contact-links-add");
    if (addCL) {
      addCL.addEventListener("click", function (e) {
        e.preventDefault();
        var wrapper = document.getElementById("footer-contact-links-wrapper");
        var idx = wrapper.querySelectorAll(".buildpro-block").length;
        var html =
          "" +
          "<div class='buildpro-block' data-index='" +
          idx +
          "'>" +
          "  <p class='buildpro-field'><label>" +
          escHtml(t("icon", "Icon")) +
          "</label><input type='hidden' name='footer_contact_links[" +
          idx +
          "][icon_id]' value=''> <button type='button' class='button select-contact-icon'>" +
          escHtml(t("selectPhoto", "Select photo")) +
          "</button> <button type='button' class='button remove-contact-icon'>" +
          escHtml(t("removePhoto", "Remove photo")) +
          "</button></p>" +
          "  <div class='image-preview contact-icon-preview'><span style='color:#888'>" +
          escHtml(t("noImageSelected", "No image selected")) +
          "</span></div>" +
          "  <p class='buildpro-field'><label>" +
          escHtml(t("linkUrl", "URL")) +
          "</label><input type='url' name='footer_contact_links[" +
          idx +
          "][url]' class='regular-text' value='' placeholder='https://...'> <button type='button' class='button choose-link'>" +
          escHtml(t("chooseLink", "Choose Link")) +
          "</button></p>" +
          "  <p class='buildpro-field'><label>" +
          escHtml(t("linkTitle", "Button Label")) +
          "</label><input type='text' name='footer_contact_links[" +
          idx +
          "][title]' class='regular-text' value=''></p>" +
          "  <p class='buildpro-field'><label>" +
          escHtml(t("linkTarget", "Link Target")) +
          "</label><div class='checkbox-label'><input type='checkbox' name='footer_contact_links[" +
          idx +
          "][target]' value='_blank'> " +
          escHtml(t("openInNewTab", "Open in new tab")) +
          "</div></p>" +
          "  <div class='buildpro-actions'><button type='button' class='button remove-row'>" +
          escHtml(t("remove", "Remove")) +
          "</button></div>" +
          "</div>";
        var temp = document.createElement("div");
        temp.innerHTML = html;
        var row = temp.firstElementChild;
        wrapper.appendChild(row);
        bindRow(row);
      });
    }
    Array.prototype.forEach.call(
      document.querySelectorAll(".choose-link-single"),
      function (btn) {
        btn.addEventListener("click", function (e) {
          e.preventDefault();
          var urlSel = btn.getAttribute("data-url");
          var titleSel = btn.getAttribute("data-title");
          var targetSel = btn.getAttribute("data-target");
          var urlInput = document.querySelector(urlSel);
          var titleInput = document.querySelector(titleSel);
          var targetSelect = document.querySelector(targetSel);
          openCustomLinkPicker(urlInput, titleInput, targetSelect);
        });
      },
    );
  })();

  if (wp && wp.customize) {
    // Simple text fields — instant live preview via postMessage
    wp.customize("footer_information_description", function (value) {
      value.bind(function (to) {
        var el = document.querySelector(".footer__description");
        if (el) el.textContent = (to == null ? "" : String(to)).trim();
      });
    });
    wp.customize("footer_create_build_text", function (value) {
      value.bind(function (to) {
        var el = document.querySelector(".footer__create");
        if (el) el.textContent = (to == null ? "" : String(to)).trim();
      });
    });
    wp.customize("footer_policy_text", function (value) {
      value.bind(function (to) {
        var el = document.querySelector(".footer__policy");
        if (el) el.textContent = (to == null ? "" : String(to)).trim();
      });
    });
    wp.customize("footer_servicer_text", function (value) {
      value.bind(function (to) {
        var el = document.querySelector(".footer__service");
        if (el) el.textContent = (to == null ? "" : String(to)).trim();
      });
    });
  }
})(window.wp);
