(function () {
  var buildproLinkPickerI18n =
    window.buildproLinkPickerI18n &&
    typeof window.buildproLinkPickerI18n === "object" &&
    window.buildproLinkPickerI18n
      ? window.buildproLinkPickerI18n
      : {};

  function escHtml(s) {
    return String(s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function t(key, fallback) {
    var val = buildproLinkPickerI18n ? buildproLinkPickerI18n[key] : null;
    return typeof val === "string" && val ? val : fallback;
  }

  function ensureDirectOpenNotice(visible) {
    var wrap = document.querySelector(".buildpro-link-popup");
    if (!wrap) return;

    var id = "buildpro-link-picker-direct-notice";
    var note = document.getElementById(id);
    if (!note) {
      note = document.createElement("div");
      note.id = id;
      note.className = "notice notice-warning inline";
      note.setAttribute("role", "status");
      note.style.margin = "0 0 12px";
      note.style.padding = "8px 10px";
      note.style.boxSizing = "border-box";
      note.textContent = t(
        "directOpenNotice",
        "Link Picker is used from other sections. Please use the “Choose Link” button in the relevant tab to pick a link.",
      );
      wrap.insertBefore(note, wrap.firstChild);
    }

    note.style.display = visible ? "block" : "none";
  }

  /* ── helpers ───────────────────────────────────────────────── */
  function normalizeTitle(t) {
    if (!t) return "";
    if (typeof t === "string") return t;
    if (t.rendered) return t.rendered;
    return String(t);
  }

  function fetchAll(base) {
    var per = 50;
    function onePage(page) {
      return fetch(base + "&per_page=" + per + "&page=" + page, {
        credentials: "same-origin",
      })
        .then(function (r) {
          var total = parseInt(r.headers.get("X-WP-TotalPages") || "1", 10);
          return r.json().then(function (data) {
            return { data: data, totalPages: total };
          });
        })
        .catch(function () {
          return { data: [], totalPages: 1 };
        });
    }
    return onePage(1).then(function (res1) {
      var all = res1.data || [];
      var total = res1.totalPages;
      if (total <= 1) return all;
      var tasks = [];
      for (var i = 2; i <= total; i++) tasks.push(onePage(i));
      return Promise.all(tasks).then(function (rs) {
        rs.forEach(function (r) {
          all = all.concat(r.data || []);
        });
        return all;
      });
    });
  }

  function resolveRestBase(slug) {
    return fetch("/wp-json/wp/v2/types", { credentials: "same-origin" })
      .then(function (r) {
        return r.json();
      })
      .then(function (types) {
        var t = types && types[slug];
        return t && t.rest_base ? t.rest_base : slug + "s";
      })
      .catch(function () {
        return slug + "s";
      });
  }

  /* ── render ────────────────────────────────────────────────── */
  function renderResults(items, results) {
    if (!results) return;
    if (!items || !items.length) {
      results.innerHTML =
        "<p style='color:#8c8f94;padding:10px'>" +
        escHtml(t("noResults", "No results found.")) +
        "</p>";
      return;
    }
    results.innerHTML = items
      .map(function (it) {
        var title = normalizeTitle(it.title) || it.url || it.link || "";
        var url = it.url || it.link || "";
        var type = it.type || it.subtype || "";
        var chip = type
          ? '<span class="chip">' + String(type).toUpperCase() + "</span>"
          : "";
        return (
          '<div class="result">' +
          '<div class="result-info">' +
          '<div class="result-title">' +
          title +
          chip +
          "</div>" +
          '<div class="meta">' +
          url +
          "</div>" +
          "</div>" +
          '<button type="button" class="button buildpro-link-pick"' +
          ' data-url="' +
          url +
          '"' +
          ' data-title="' +
          title.replace(/"/g, "&quot;") +
          '">' +
          escHtml(t("select", "Select")) +
          "</button>" +
          "</div>"
        );
      })
      .join("");
  }

  /* ── data loading ──────────────────────────────────────────── */
  function loadDefault(results) {
    if (results) {
      results.innerHTML =
        "<p style='color:#8c8f94;padding:10px'>" +
        escHtml(t("loading", "Loading...")) +
        "</p>";
    }
    Promise.all([
      fetchAll("/wp-json/wp/v2/pages?_fields=title,link").then(function (l) {
        return l.map(function (d) {
          return { title: d.title, url: d.link, type: "page" };
        });
      }),
      fetchAll("/wp-json/wp/v2/posts?_fields=title,link").then(function (l) {
        return l.map(function (d) {
          return { title: d.title, url: d.link, type: "post" };
        });
      }),
      resolveRestBase("project").then(function (base) {
        return fetchAll("/wp-json/wp/v2/" + base + "?_fields=title,link")
          .then(function (l) {
            return l.map(function (d) {
              return { title: d.title, url: d.link, type: "project" };
            });
          })
          .catch(function () {
            return [];
          });
      }),
      resolveRestBase("material").then(function (base) {
        return fetchAll("/wp-json/wp/v2/" + base + "?_fields=title,link")
          .then(function (l) {
            return l.map(function (d) {
              return { title: d.title, url: d.link, type: "material" };
            });
          })
          .catch(function () {
            return [];
          });
      }),
    ])
      .then(function (groups) {
        var merged = [];
        groups.forEach(function (g) {
          merged = merged.concat(g || []);
        });
        renderResults(merged, results);
      })
      .catch(function () {
        renderResults([], results);
      });
  }

  function performSearch(q, results) {
    if (!q) {
      loadDefault(results);
      return;
    }
    fetch(
      "/wp-json/wp/v2/search?search=" + encodeURIComponent(q) + "&per_page=50",
      { credentials: "same-origin" },
    )
      .then(function (r) {
        return r.json();
      })
      .then(function (data) {
        var items = (data || []).map(function (d) {
          return {
            title: d.title,
            url: d.url,
            type: d.subtype || d.type || "",
          };
        });
        renderResults(items, results);
      })
      .catch(function () {
        renderResults([], results);
      });
  }

  /* ── apply & navigate back ─────────────────────────────────── */
  function goBackToSection(sectionId) {
    if (
      window.wp &&
      wp.customize &&
      typeof wp.customize.section === "function"
    ) {
      if (sectionId) {
        var s = wp.customize.section(sectionId);
        if (s && typeof s.expand === "function") {
          s.expand();
          return;
        }
      }
    }
  }

  function applySelection(urlField, textField, targetToggle) {
    var url = urlField ? urlField.value || "" : "";
    var title = textField ? textField.value || "" : "";
    var targetBlank = targetToggle ? !!targetToggle.checked : false;
    var tgt = window.buildproLinkTarget;

    function dispatchBubbling(el, type) {
      if (!el) return;
      try {
        el.dispatchEvent(new Event(type, { bubbles: true }));
        return;
      } catch (e) {}
      try {
        // Legacy fallback
        var ev = document.createEvent("Event");
        ev.initEvent(type, true, true);
        el.dispatchEvent(ev);
      } catch (e2) {}
    }

    if (tgt) {
      var sectionId = tgt.sectionId || "";
      if (tgt.urlInput) {
        tgt.urlInput.value = url;
        dispatchBubbling(tgt.urlInput, "input");
        dispatchBubbling(tgt.urlInput, "change");
      }
      if (tgt.titleInput) {
        tgt.titleInput.value = title;
        dispatchBubbling(tgt.titleInput, "input");
        dispatchBubbling(tgt.titleInput, "change");
      }
      if (tgt.targetSelect) {
        tgt.targetSelect.value = targetBlank ? "_blank" : "";
        dispatchBubbling(tgt.targetSelect, "change");
      }
      window.buildproLinkTarget = null;
      goBackToSection(sectionId);
    }
  }

  /* ── setup when elements are in DOM ────────────────────────── */
  function setup() {
    var urlField = document.getElementById("buildpro-link-url");
    var textField = document.getElementById("buildpro-link-text");
    var targetToggle = document.getElementById("buildpro-link-target");
    var searchField = document.getElementById("buildpro-link-search");
    var results = document.getElementById("buildpro-link-results");
    var applyBtn = document.getElementById("buildpro-link-apply");
    var closeBtn = document.getElementById("buildpro-link-close");
    var currentBox = document.getElementById("buildpro-link-current");
    var currentUrlEl = document.getElementById("buildpro-link-current-url");

    if (!urlField || !results || !applyBtn) return;
    if (urlField._blpBound) return; // already bound to this exact element
    urlField._blpBound = true;

    // If user opened Link Picker directly (no target), show a notice.
    // When opened via other sections (Choose Link), hide it.
    ensureDirectOpenNotice(!window.buildproLinkTarget);

    /* Pre-fill fields from the banner row that opened us */
    var tgt = window.buildproLinkTarget;
    if (tgt) {
      ensureDirectOpenNotice(false);
      if (tgt.currentUrl !== undefined && urlField)
        urlField.value = tgt.currentUrl;
      if (tgt.currentTitle !== undefined && textField)
        textField.value = tgt.currentTitle;
      if (tgt.currentTarget !== undefined && targetToggle)
        targetToggle.checked = tgt.currentTarget === "_blank";
      /* Show the "current link" badge */
      if (currentBox && currentUrlEl && tgt.currentUrl) {
        currentUrlEl.textContent = tgt.currentUrl;
        currentBox.classList.add("visible");
      }
    }

    /* Save original PHP-rendered HTML once so we can restore it on clear */
    var phpHtml = results.innerHTML;

    /* Search input */
    if (searchField) {
      var debounce;
      searchField.addEventListener("input", function () {
        clearTimeout(debounce);
        debounce = setTimeout(function () {
          var q = searchField.value.trim();
          if (!q) {
            /* Restore PHP list when search is cleared */
            results.innerHTML = phpHtml;
          } else {
            performSearch(q, results);
          }
        }, 250);
      });
    }

    /* Click a result row → fill fields + highlight row */
    results.addEventListener("click", function (e) {
      var btn = e.target;
      if (
        !btn ||
        !btn.classList ||
        !btn.classList.contains("buildpro-link-pick")
      )
        return;
      var url = btn.getAttribute("data-url") || "";
      var title = btn.getAttribute("data-title") || "";
      if (urlField) urlField.value = url;
      if (textField) textField.value = title;
      /* Highlight selected row */
      results.querySelectorAll(".result").forEach(function (r) {
        r.classList.remove("selected");
      });
      var row = btn.parentNode;
      if (row && row.classList) row.classList.add("selected");
    });

    /* Apply button – write back to banner and navigate back */
    applyBtn.addEventListener("click", function (e) {
      e.preventDefault();
      applySelection(urlField, textField, targetToggle);
    });

    /* Back / Close button – return without applying */
    if (closeBtn) {
      closeBtn.addEventListener("click", function (e) {
        e.preventDefault();
        var sectionId = window.buildproLinkTarget
          ? window.buildproLinkTarget.sectionId || ""
          : "";
        window.buildproLinkTarget = null;
        goBackToSection(sectionId);
      });
    }

    /* Load default list
       – If PHP already rendered items (data-initial-count > 0), keep them.
         REST API is only used when search box is used or list was empty. */
    var phpCount = parseInt(
      results.getAttribute("data-initial-count") || "0",
      10,
    );
    if (phpCount === 0) {
      loadDefault(results);
    }
  }

  /* ── bind on section expand ─────────────────────────────────── */
  function tryBind() {
    if (
      !(window.wp && wp.customize && typeof wp.customize.section === "function")
    ) {
      return false;
    }
    var sec = wp.customize.section("buildpro_link_picker_section");
    if (!sec || !sec.expanded || typeof sec.expanded.bind !== "function") {
      return false;
    }
    sec.expanded.bind(function (expanded) {
      if (expanded) {
        setTimeout(function () {
          /* Reset bound flag so setup can re-run, but don't wipe PHP list */
          var el = document.getElementById("buildpro-link-url");
          if (el) el._blpBound = false;
          setup();
        }, 80);
      }
    });
    return true;
  }

  if (!tryBind()) {
    if (window.wp && wp.customize) {
      wp.customize.bind("ready", function () {
        tryBind();
      });
    }
  }

  /* MutationObserver fallback */
  var mo = new MutationObserver(function () {
    if (document.getElementById("buildpro-link-url")) setup();
  });
  try {
    mo.observe(document.documentElement || document.body, {
      childList: true,
      subtree: true,
    });
  } catch (e) {}

  setup();
})();
