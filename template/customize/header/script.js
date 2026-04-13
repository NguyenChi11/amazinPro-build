(function (wp) {
  if (typeof window === "undefined") return;

  var headerI18n =
    window.buildproHeaderI18n &&
    typeof window.buildproHeaderI18n === "object" &&
    window.buildproHeaderI18n
      ? window.buildproHeaderI18n
      : {};
  function t(key, fallback) {
    var val = headerI18n ? headerI18n[key] : null;
    return typeof val === "string" && val ? val : fallback;
  }
  function escHtml(s) {
    return String(s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/\"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  var selectBtn = document.getElementById("select_header_logo");
  var removeBtn = document.getElementById("remove_header_logo");
  var input = document.getElementById("header_logo");
  var preview = document.getElementById("header_logo_preview");
  var frame = null;

  if (selectBtn && typeof wp !== "undefined" && wp && wp.media) {
    selectBtn.addEventListener("click", function (e) {
      e.preventDefault();
      if (frame) {
        frame.open();
        return;
      }
      frame = wp.media({
        title: t("mediaTitle", "Select Header Logo"),
        button: { text: t("useImage", "Use Image") },
        multiple: false,
      });
      frame.on("select", function () {
        var attachment = frame.state().get("selection").first().toJSON();
        if (input) input.value = attachment.id;
        var url =
          attachment.sizes && attachment.sizes.thumbnail
            ? attachment.sizes.thumbnail.url
            : attachment.url;
        if (preview) {
          preview.innerHTML =
            "<img src='" + url + "' style='max-height:80px;'>";
        }
      });
      frame.open();
    });
  }

  if (removeBtn) {
    removeBtn.addEventListener("click", function (e) {
      e.preventDefault();
      if (input) input.value = "";
      if (preview) preview.innerHTML = "";
    });
  }

  function openHeaderLinkPicker(urlInput, titleInput) {
    if (!urlInput || !(wp && wp.customize)) return;
    window.buildproLinkTarget = {
      urlInput: urlInput,
      titleInput: titleInput || null,
      sectionId: "buildpro_header_section",
      currentUrl: urlInput.value || "",
      currentTitle: titleInput ? titleInput.value || "" : "",
      currentTarget: "",
    };

    if (typeof wp.customize.section === "function") {
      var section = wp.customize.section("buildpro_link_picker_section");
      if (section && typeof section.expand === "function") {
        section.expand();
      }
    }
  }

  function bindHeaderQuoteChooseLinkControl() {
    if (!(wp && wp.customize)) return;

    var urlControl = document.getElementById(
      "customize-control-buildpro_header_quote_url",
    );
    if (!urlControl) {
      urlControl = document.querySelector(
        ".customize-control-buildpro_header_quote_url",
      );
    }
    if (!urlControl) return;
    if (
      urlControl.dataset &&
      urlControl.dataset.buildproChooseLinkBound === "1"
    ) {
      return;
    }

    var urlInput = urlControl.querySelector('input[type="url"]');
    if (!urlInput) return;

    var titleControl = document.getElementById(
      "customize-control-buildpro_header_quote_text",
    );
    if (!titleControl) {
      titleControl = document.querySelector(
        ".customize-control-buildpro_header_quote_text",
      );
    }
    var titleInput = titleControl
      ? titleControl.querySelector('input[type="text"]')
      : null;

    var chooseBtn = document.createElement("button");
    chooseBtn.type = "button";
    chooseBtn.className = "button buildpro-header-choose-link";
    chooseBtn.textContent = t("chooseLink", "Choose Link");
    chooseBtn.style.marginLeft = "0px";
    chooseBtn.style.marginTop = "10px";

    chooseBtn.addEventListener("click", function (e) {
      e.preventDefault();
      openHeaderLinkPicker(urlInput, titleInput);
    });

    urlInput.insertAdjacentElement("afterend", chooseBtn);
    if (urlControl.dataset) {
      urlControl.dataset.buildproChooseLinkBound = "1";
    }
  }

  function initHeaderControlsLinkPicker() {
    bindHeaderQuoteChooseLinkControl();
    if (!(wp && wp.customize && typeof wp.customize.section === "function")) {
      return;
    }

    var headerSection = wp.customize.section("buildpro_header_section");
    if (headerSection && headerSection.expanded) {
      headerSection.expanded.bind(function (expanded) {
        if (expanded) {
          setTimeout(bindHeaderQuoteChooseLinkControl, 80);
        }
      });
    }

    if (window.MutationObserver) {
      var observer = new MutationObserver(function () {
        bindHeaderQuoteChooseLinkControl();
      });
      observer.observe(document.body, { childList: true, subtree: true });
    }
  }

  function initHeaderAdminLinkPicker() {
    if (wp && wp.customize) return;

    var buttons = document.querySelectorAll(".choose-link-single");
    if (!buttons.length) return;

    var customCtx = { urlInput: null, titleInput: null };

    function fetchJSON(url) {
      return fetch(url, { credentials: "same-origin" })
        .then(function (r) {
          return r.ok ? r.json() : [];
        })
        .catch(function () {
          return [];
        });
    }

    function renderItems(items) {
      var results = document.getElementById("buildpro_custom_link_results");
      if (!results) return;
      results.innerHTML = "";

      if (!items || !items.length) {
        results.innerHTML =
          "<div class='buildpro-custom-link-item' style='cursor:default;color:#888'>" +
          escHtml("No results found") +
          "</div>";
        return;
      }

      items.forEach(function (item) {
        var div = document.createElement("div");
        div.className = "buildpro-custom-link-item";
        div.textContent = item.title + " (" + item.type + ")";
        div.addEventListener("click", function () {
          var urlEl = document.getElementById("buildpro_custom_link_url");
          var titleEl = document.getElementById("buildpro_custom_link_title");
          if (urlEl) urlEl.value = item.url;
          if (titleEl) titleEl.value = item.title;
        });
        results.appendChild(div);
      });
    }

    function fetchRecent() {
      var pageUrl = "/wp-json/wp/v2/pages?per_page=20&orderby=date&order=desc";
      var postUrl = "/wp-json/wp/v2/posts?per_page=20&orderby=date&order=desc";
      Promise.all([fetchJSON(pageUrl), fetchJSON(postUrl)]).then(
        function (res) {
          var pages = (res[0] || []).map(function (it) {
            return {
              title:
                it.title && it.title.rendered
                  ? it.title.rendered
                  : it.slug || "Page",
              url: it.link,
              type: "PAGE",
              date: new Date(it.date),
            };
          });
          var posts = (res[1] || []).map(function (it) {
            return {
              title:
                it.title && it.title.rendered
                  ? it.title.rendered
                  : it.slug || "Post",
              url: it.link,
              type: "POST",
              date: new Date(it.date),
            };
          });
          var all = pages.concat(posts).sort(function (a, b) {
            return b.date - a.date;
          });
          renderItems(all);
        },
      );
    }

    function searchContent(q, source) {
      var qparam = q ? "&search=" + encodeURIComponent(q) : "";
      if (source === "page") {
        fetchJSON("/wp-json/wp/v2/pages?per_page=20" + qparam).then(
          function (items) {
            renderItems(
              (items || []).map(function (it) {
                return {
                  title:
                    it.title && it.title.rendered
                      ? it.title.rendered
                      : it.slug || "Page",
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
              (items || []).map(function (it) {
                return {
                  title:
                    it.title && it.title.rendered
                      ? it.title.rendered
                      : it.slug || "Post",
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
            (items || []).map(function (it) {
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

    function openCustomLinkPicker(urlInput, titleInput) {
      customCtx.urlInput = urlInput;
      customCtx.titleInput = titleInput;
      var urlEl = document.getElementById("buildpro_custom_link_url");
      var titleEl = document.getElementById("buildpro_custom_link_title");
      if (urlEl) urlEl.value = urlInput && urlInput.value ? urlInput.value : "";
      if (titleEl) {
        titleEl.value = titleInput && titleInput.value ? titleInput.value : "";
      }
      showCustom();
    }

    function applyCustom() {
      var urlEl = document.getElementById("buildpro_custom_link_url");
      var titleEl = document.getElementById("buildpro_custom_link_title");
      if (customCtx.urlInput && urlEl)
        customCtx.urlInput.value = urlEl.value || "";
      if (customCtx.titleInput && titleEl) {
        customCtx.titleInput.value = titleEl.value || "";
      }
      hideCustom();
    }

    var cancelBtn = document.getElementById("buildpro_custom_link_cancel");
    var applyBtn = document.getElementById("buildpro_custom_link_apply");
    var searchInput = document.getElementById("buildpro_custom_link_search");
    var sourceSel = document.getElementById("buildpro_custom_link_source");
    var backdrop = document.getElementById("buildpro-custom-link-backdrop");

    if (cancelBtn) cancelBtn.addEventListener("click", hideCustom);
    if (applyBtn) applyBtn.addEventListener("click", applyCustom);
    if (backdrop) backdrop.addEventListener("click", hideCustom);

    if (searchInput) {
      searchInput.addEventListener("input", function (e) {
        var q = e.target.value;
        if (q) {
          searchContent(q, sourceSel ? sourceSel.value : "all");
        } else {
          fetchRecent();
        }
      });
    }

    if (sourceSel) {
      sourceSel.addEventListener("change", function () {
        var q = searchInput ? searchInput.value : "";
        if (q) {
          searchContent(q, sourceSel.value);
        } else {
          fetchRecent();
        }
      });
    }

    Array.prototype.forEach.call(buttons, function (btn) {
      btn.addEventListener("click", function (e) {
        e.preventDefault();
        var urlSel = btn.getAttribute("data-url");
        var titleSel = btn.getAttribute("data-title");
        var urlInput = urlSel ? document.querySelector(urlSel) : null;
        var titleInput = titleSel ? document.querySelector(titleSel) : null;
        openCustomLinkPicker(urlInput, titleInput);
      });
    });
  }

  if (wp && wp.customize) {
    wp.customize("header_logo", function (value) {
      value.bind(function () {
        if (wp.customize.selectiveRefresh) {
          wp.customize.selectiveRefresh.requestFullRefresh();
        }
      });
    });

    wp.customize("buildpro_header_quote_text", function (value) {
      value.bind(function (to) {
        var v = (to == null ? "" : String(to)).trim();
        if (!v) {
          var data = window.headerData || {};
          v = data.quoteText || "Request a Quote";
        }
        var nodes = document.querySelectorAll(".header-nav-button p");
        for (var i = 0; i < nodes.length; i++) {
          nodes[i].textContent = v;
        }
      });
    });

    wp.customize("buildpro_header_quote_url", function (value) {
      value.bind(function (to) {
        var v = (to == null ? "" : String(to)).trim();
        if (!v) {
          var data = window.headerData || {};
          v = data.quoteUrl || "#";
        }
        var links = document.querySelectorAll(".header-nav-button");
        for (var i = 0; i < links.length; i++) {
          links[i].setAttribute("href", v);
        }
      });
    });

    if (typeof wp.customize.bind === "function") {
      wp.customize.bind("ready", function () {
        initHeaderControlsLinkPicker();
      });
    } else {
      initHeaderControlsLinkPicker();
    }
  }

  initHeaderAdminLinkPicker();
})(window.wp);
