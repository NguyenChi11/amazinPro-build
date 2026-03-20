/* global jQuery */
(function ($) {
  var I18N = (window && window.buildproAboutUsI18n) || {};
  function t(key, fallback) {
    var v = I18N && typeof I18N[key] === "string" ? I18N[key] : "";
    return v || fallback || "";
  }
  function sprintf(template) {
    var args = Array.prototype.slice.call(arguments, 1);
    var i = 0;
    return String(template).replace(/%[sd]/g, function () {
      var val = args[i++];
      return val === undefined || val === null ? "" : String(val);
    });
  }
  function escHtml(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/\"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function init(el) {
    var wrap = el.find(".buildpro-about-core-values-repeater");
    if (!wrap.length) return;
    // Prevent duplicate initialization
    if (wrap.data("buildpro-cv-init")) return;
    wrap.data("buildpro-cv-init", true);
    var list = wrap.find(".buildpro-about-core-values-list");
    var input = wrap.find(".buildpro-about-core-values-input");
    var frame = null;
    var api = window.wp && window.wp.customize ? window.wp.customize : null;

    function openLinkPicker(urlInputEl) {
      if (!urlInputEl) return;
      window.buildproLinkTarget = {
        sectionId: "buildpro_about_core_values_section",
        urlInput: urlInputEl,
        currentUrl: urlInputEl.value || "",
      };
      if (api && typeof api.section === "function") {
        var s = api.section("buildpro_link_picker_section");
        if (s && typeof s.expand === "function") {
          s.expand();
        }
      }
    }
    function getItems() {
      try {
        var v = input.val();
        if (!v) return [];
        var parsed = JSON.parse(v);
        return Array.isArray(parsed) ? parsed : [];
      } catch (e) {
        return [];
      }
    }
    function setItems(items) {
      input.val(JSON.stringify(items || []));
      // Push to WP setting API (replaces $this->link() which corrupted JSON arrays)
      var _api = window.wp && window.wp.customize ? window.wp.customize : null;
      if (_api && _api.has && _api.has("buildpro_about_core_values_items")) {
        try {
          _api("buildpro_about_core_values_items").set(items || []);
        } catch (e) {}
      }
    }
    function fetchFromServer(callback) {
      if (!window.BuildProAboutCoreValues) {
        if (callback) callback(null);
        return;
      }
      $.ajax({
        url: BuildProAboutCoreValues.ajax_url,
        method: "POST",
        dataType: "json",
        data: {
          action: "buildpro_get_about_core_values",
          nonce: BuildProAboutCoreValues.nonce,
          page_id: BuildProAboutCoreValues.default_page_id || 0,
        },
      })
        .done(function (resp) {
          if (resp && resp.success && resp.data) {
            var d = resp.data || {};
            // Sync all fields into WP customizer settings
            if (api) {
              try {
                if (typeof d.title === "string") {
                  api("buildpro_about_core_values_title").set(d.title);
                }
              } catch (e) {}
              try {
                if (typeof d.description === "string") {
                  api("buildpro_about_core_values_description").set(
                    d.description,
                  );
                }
              } catch (e) {}
              try {
                if (typeof d.enabled !== "undefined") {
                  api("buildpro_about_core_values_enabled").set(
                    !!parseInt(d.enabled, 10),
                  );
                }
              } catch (e) {}
            }
            if (Array.isArray(d.items)) {
              setItems(d.items);
            }
            if (callback) callback(d);
          } else {
            if (callback) callback(null);
          }
        })
        .fail(function () {
          if (callback) callback(null);
        });
    }
    function render() {
      var items = getItems();
      list.empty();
      items.forEach(function (it, idx) {
        var row = $('<div class="core-value-item"/>');
        var itemFallback = sprintf(t("itemLabel", "Item %d"), idx + 1);
        var cvHeader = $(
          '<div class="cv-accordion-header"><span class="cv-accordion-label">' +
            escHtml(it.title || itemFallback) +
            '</span><span class="cv-accordion-arrow">&#9660;</span></div>',
        );
        var cvBody = $(
          '<div class="cv-accordion-body" style="display:none"></div>',
        );
        row.append(cvHeader).append(cvBody);
        cvHeader.on("click", function () {
          var isOpen = cvBody.css("display") !== "none";
          cvBody.css("display", isOpen ? "none" : "block");
          cvHeader
            .find(".cv-accordion-arrow")
            .css("transform", isOpen ? "rotate(-90deg)" : "rotate(0deg)");
        });
        var previewUrl = it.icon_url || "";
        var preview = $(
          '<div class="cv-icon-preview">' +
            (previewUrl
              ? '<img src="' +
                previewUrl +
                '" style="max-width:2.75rem;height:auto;border-radius:0.625rem;border:1px solid #e5e7eb;" />'
              : '<div class="cv-icon-empty">' +
                escHtml(t("noImageSelected", "No image selected")) +
                "</div>") +
            "</div>",
        );
        var imgControls =
          '<div class="cv-icon-controls">' +
          '<input type="hidden" class="cv-icon-id" value="' +
          (it.icon_id || 0) +
          '">' +
          '<button type="button" class="button button-secondary cv-select-icon">' +
          escHtml(t("chooseImage", "Choose Image")) +
          "</button> " +
          '<button type="button" class="button cv-remove-icon">' +
          escHtml(t("remove", "Remove")) +
          "</button>" +
          "</div>";
        cvBody.append(
          "<p><label>" + escHtml(t("iconImage", "Icon Image")) + "</label></p>",
        );
        cvBody.append(preview);
        cvBody.append(imgControls);
        cvBody.append(
          "<p><label>" +
            escHtml(t("title", "Title")) +
            '<br><input type="text" class="widefat cv-title" value="' +
            (it.title || "") +
            '"></label></p>',
        );
        cvBody.append(
          "<p><label>" +
            escHtml(t("description", "Description")) +
            '<br><textarea class="widefat cv-desc" rows="3">' +
            (it.description || "") +
            "</textarea></label></p>",
        );
        cvBody.append(
          "<p><label>" +
            escHtml(t("url", "URL")) +
            '<br><input type="text" class="widefat cv-url" value="' +
            (it.url || "") +
            '"></label></p>',
        );
        cvBody.append(
          '<p><button type="button" class="button remove-core-value">' +
            escHtml(t("remove", "Remove")) +
            "</button></p>",
        );
        row.on("click", ".cv-select-icon", function (e) {
          e.preventDefault();
          if (frame) {
            frame.off("select");
          }
          frame = wp.media({
            title: t("chooseImage", "Choose Image"),
            button: { text: t("useImage", "Use image") },
            multiple: false,
          });
          frame.on("select", function () {
            var att = frame.state().get("selection").first().toJSON();
            var url =
              att.sizes && att.sizes.thumbnail
                ? att.sizes.thumbnail.url
                : att.url;
            var id = att.id || 0;
            var items2 = getItems();
            var cur = items2[idx] || {};
            cur.icon_id = id;
            cur.icon_url = url;
            items2[idx] = cur;
            setItems(items2);
            render();
          });
          frame.open();
        });
        row.on("click", ".cv-remove-icon", function (e) {
          e.preventDefault();
          var items2 = getItems();
          var cur = items2[idx] || {};
          cur.icon_id = 0;
          cur.icon_url = "";
          items2[idx] = cur;
          setItems(items2);
          render();
        });
        row.on("input change", "input,textarea", function () {
          var items2 = getItems();
          var cur = items2[idx] || {};
          // keep icon fields as they are, updated via media handlers
          cur.title = row.find(".cv-title").val();
          cur.description = row.find(".cv-desc").val();
          cur.url = row.find(".cv-url").val();
          items2[idx] = cur;
          setItems(items2);
          var t = row.find(".cv-title").val();
          if (t) cvHeader.find(".cv-accordion-label").text(t);
        });

        row.on("click", ".cv-url", function (e) {
          if (e && e.preventDefault) e.preventDefault();
          if (e && e.stopPropagation) e.stopPropagation();
          openLinkPicker(this);
        });
        row.on("click", ".remove-core-value", function (e) {
          e.preventDefault();
          var items2 = getItems();
          items2.splice(idx, 1);
          setItems(items2);
          render();
        });
        list.append(row);
      });
    }
    wrap.on("click", ".buildpro-about-core-values-add", function (e) {
      e.preventDefault();
      var items = getItems();
      items.push({
        icon_id: 0,
        icon_url: "",
        title: "",
        description: "",
        url: "",
      });
      setItems(items);
      render();
    });
    render();
  }
  var _wpApi = window.wp && window.wp.customize ? window.wp.customize : null;
  if (_wpApi && _wpApi.control) {
    _wpApi.control("buildpro_about_core_values_items", function (ctrl) {
      ctrl.deferred.embedded.done(function () {
        var inputEl = ctrl.container.find(".buildpro-about-core-values-input");
        // Try to seed from WP setting API (avoids AJAX if already in memory)
        var seededFromApi = false;
        if (_wpApi.has && _wpApi.has("buildpro_about_core_values_items")) {
          try {
            var apiVal = _wpApi("buildpro_about_core_values_items").get();
            if (Array.isArray(apiVal) && apiVal.length > 0) {
              inputEl.val(JSON.stringify(apiVal));
              seededFromApi = true;
            }
          } catch (e) {}
        }
        if (seededFromApi) {
          // Items already in memory, just render
          init(ctrl.container);
        } else {
          // No data in WP setting — fetch from server (post_meta) then render
          var wrap = ctrl.container.find(
            ".buildpro-about-core-values-repeater",
          );
          // Use a temporary fetch control that bypasses the init guard
          if (
            !wrap.data("buildpro-cv-fetching") &&
            window.BuildProAboutCoreValues
          ) {
            wrap.data("buildpro-cv-fetching", true);
            var $list = wrap.find(".buildpro-about-core-values-list");
            $list.html(
              '<p style="color:#888">' +
                escHtml(t("loading", "Loading...")) +
                "</p>",
            );
            $.ajax({
              url: BuildProAboutCoreValues.ajax_url,
              method: "POST",
              dataType: "json",
              data: {
                action: "buildpro_get_about_core_values",
                nonce: BuildProAboutCoreValues.nonce,
                page_id: BuildProAboutCoreValues.default_page_id || 0,
              },
            })
              .done(function (resp) {
                if (resp && resp.success && resp.data) {
                  var d = resp.data || {};
                  if (_wpApi) {
                    try {
                      if (typeof d.title === "string")
                        _wpApi("buildpro_about_core_values_title").set(d.title);
                    } catch (e) {}
                    try {
                      if (typeof d.description === "string")
                        _wpApi("buildpro_about_core_values_description").set(
                          d.description,
                        );
                    } catch (e) {}
                    try {
                      if (typeof d.enabled !== "undefined")
                        _wpApi("buildpro_about_core_values_enabled").set(
                          !!parseInt(d.enabled, 10),
                        );
                    } catch (e) {}
                    try {
                      if (Array.isArray(d.items))
                        _wpApi("buildpro_about_core_values_items").set(d.items);
                    } catch (e) {}
                  }
                  if (Array.isArray(d.items)) {
                    inputEl.val(JSON.stringify(d.items));
                  }
                }
              })
              .always(function () {
                init(ctrl.container);
              });
          } else {
            init(ctrl.container);
          }
        }
      });
    });
  }
})(jQuery);
