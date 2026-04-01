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
    var wrap = el.find(".buildpro-about-leader-repeater");
    if (!wrap.length) return;
    if (wrap.data("buildpro-leader-init")) return;
    wrap.data("buildpro-leader-init", true);

    var list = wrap.find(".buildpro-about-leader-list");
    var input = wrap.find(".buildpro-about-leader-input");
    var frame = null;
    var api = window.wp && window.wp.customize ? window.wp.customize : null;

    function openLinkPicker(urlInputEl, titleInputEl) {
      if (!urlInputEl) return;
      window.buildproLinkTarget = {
        sectionId: "buildpro_about_leader_section",
        urlInput: urlInputEl,
        titleInput: titleInputEl,
        currentUrl: urlInputEl.value || "",
        currentTitle: titleInputEl ? titleInputEl.value || "" : "",
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
      // Push array to WP setting API (matches Core Values behavior)
      if (api && api.has && api.has("buildpro_about_leader_items")) {
        try {
          api("buildpro_about_leader_items").set(items || []);
        } catch (e) {}
      }
    }
    function render() {
      var items = getItems();
      list.empty();
      items.forEach(function (it, idx) {
        var row = $('<div class="leader-item"/>');
        var itemFallback = sprintf(t("itemLabel", "Item %d"), idx + 1);
        var openByDefault = idx === 0;
        var leaderHeader = $(
          '<div class="leader-accordion-header"><span class="leader-accordion-label">' +
            escHtml(it.name || itemFallback) +
            '</span><span class="leader-accordion-arrow">&#9660;</span></div>',
        );
        var leaderBody = $(
          '<div class="leader-accordion-body" style="display:' +
            (openByDefault ? "block" : "none") +
            '"></div>',
        );
        row.append(leaderHeader).append(leaderBody);
        if (openByDefault) {
          leaderHeader
            .find(".leader-accordion-arrow")
            .css("transform", "rotate(0deg)");
        }
        leaderHeader.on("click", function () {
          var isOpen = leaderBody.css("display") !== "none";
          leaderBody.css("display", isOpen ? "none" : "block");
          leaderHeader
            .find(".leader-accordion-arrow")
            .css("transform", isOpen ? "rotate(-90deg)" : "rotate(0deg)");
        });
        var previewUrl = it.icon_url || "";
        var preview =
          '<div class="leader-image-preview">' +
          (previewUrl
            ? '<img src="' +
              previewUrl +
              '" style="max-width:5rem;height:auto;border-radius:0.5rem;border:1px solid #e5e7eb;" />'
            : '<div class="leader-image-empty">' +
              escHtml(t("noImageSelected", "No image selected")) +
              "</div>") +
          "</div>";
        var imgControls =
          '<div class="leader-image-controls">' +
          '<input type="hidden" class="leader-image-id" value="' +
          (it.icon_id || 0) +
          '">' +
          '<button type="button" class="button button-secondary leader-select-image">' +
          escHtml(t("chooseImage", "Choose Image")) +
          "</button> " +
          '<button type="button" class="button leader-remove-image">' +
          escHtml(t("remove", "Remove")) +
          "</button>" +
          "</div>";
        leaderBody.append(
          "<p><label>" + escHtml(t("image", "Image")) + "</label></p>",
        );
        leaderBody.append(preview);
        leaderBody.append(imgControls);
        leaderBody.append(
          "<p><label>" +
            escHtml(t("name", "Name")) +
            '<br><input type="text" class="widefat leader-name" value="' +
            (it.name || "") +
            '"></label></p>',
        );
        leaderBody.append(
          "<p><label>" +
            escHtml(t("position", "Position")) +
            '<br><input type="text" class="widefat leader-position" value="' +
            (it.position || "") +
            '"></label></p>',
        );
        leaderBody.append(
          "<p><label>" +
            escHtml(t("description", "Description")) +
            '<br><input type="text" class="widefat leader-description" value="' +
            (it.description || "") +
            '"></label></p>',
        );
        leaderBody.append(
          "<p><label>" +
            escHtml(t("url", "URL")) +
            '<br><input type="text" class="widefat leader-url" value="' +
            (it.url || "") +
            '"></label></p>',
        );
        leaderBody.append(
          "<p><label>" +
            escHtml(t("linkTitle", "Link Title")) +
            '<br><input type="text" class="widefat leader-link-title" value="' +
            (it.link_title || "") +
            '"></label></p>',
        );
        leaderBody.append(
          '<p><button type="button" class="button button-secondary leader-choose-link">' +
            escHtml(t("chooseLink", "Choose Link")) +
            "</button></p>",
        );
        leaderBody.append(
          '<p><button type="button" class="button remove-leader">' +
            escHtml(t("remove", "Remove")) +
            "</button></p>",
        );
        row.on("click", ".leader-select-image", function (e) {
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
        row.on("click", ".leader-remove-image", function (e) {
          e.preventDefault();
          var items2 = getItems();
          var cur = items2[idx] || {};
          cur.icon_id = 0;
          cur.icon_url = "";
          items2[idx] = cur;
          setItems(items2);
          render();
        });
        row.on("input change", "input", function () {
          var items2 = getItems();
          var cur = items2[idx] || {};
          cur.name = row.find(".leader-name").val();
          cur.position = row.find(".leader-position").val();
          cur.description = row.find(".leader-description").val();
          cur.url = row.find(".leader-url").val();
          cur.link_title = row.find(".leader-link-title").val();
          items2[idx] = cur;
          setItems(items2);
          var n = row.find(".leader-name").val();
          if (n) leaderHeader.find(".leader-accordion-label").text(n);
        });

        row.on("click", ".leader-url, .leader-choose-link", function (e) {
          if (e && e.preventDefault) e.preventDefault();
          if (e && e.stopPropagation) e.stopPropagation();
          openLinkPicker(
            row.find(".leader-url").get(0),
            row.find(".leader-link-title").get(0),
          );
        });
        row.on("click", ".remove-leader", function (e) {
          e.preventDefault();
          var items2 = getItems();
          items2.splice(idx, 1);
          setItems(items2);
          render();
        });
        list.append(row);
      });
    }
    wrap.on("click", ".buildpro-about-leader-add", function (e) {
      e.preventDefault();
      var items = getItems();
      items.push({
        icon_id: 0,
        icon_url: "",
        name: "",
        position: "",
        description: "",
        url: "",
        link_title: "",
      });
      setItems(items);
      render();
    });

    render();
  }

  // Init via WP customize control embed (matches Core Values pattern)
  var _wpApi = window.wp && window.wp.customize ? window.wp.customize : null;
  if (_wpApi && _wpApi.control) {
    _wpApi.control("buildpro_about_leader_items", function (ctrl) {
      ctrl.deferred.embedded.done(function () {
        var inputEl = ctrl.container.find(".buildpro-about-leader-input");

        // Seed from in-memory setting first
        var seeded = false;
        if (_wpApi.has && _wpApi.has("buildpro_about_leader_items")) {
          try {
            var apiVal = _wpApi("buildpro_about_leader_items").get();
            if (Array.isArray(apiVal) && apiVal.length > 0) {
              inputEl.val(JSON.stringify(apiVal));
              seeded = true;
            }
          } catch (e) {}
        }

        if (seeded) {
          init(ctrl.container);
          return;
        }

        // Otherwise fetch from server (post_meta) and sync settings
        var wrap = ctrl.container.find(".buildpro-about-leader-repeater");
        if (
          !wrap.data("buildpro-leader-fetching") &&
          window.BuildProAboutLeader
        ) {
          wrap.data("buildpro-leader-fetching", true);
          var $list = wrap.find(".buildpro-about-leader-list");
          $list.html(
            '<p style="color:#888">' +
              escHtml(t("loading", "Loading...")) +
              "</p>",
          );
          $.ajax({
            url: BuildProAboutLeader.ajax_url,
            method: "POST",
            dataType: "json",
            data: {
              action: "buildpro_get_about_leader",
              nonce: BuildProAboutLeader.nonce,
              page_id: BuildProAboutLeader.default_page_id || 0,
            },
          })
            .done(function (resp) {
              if (resp && resp.success && resp.data) {
                var d = resp.data || {};
                if (_wpApi) {
                  try {
                    if (typeof d.title === "string")
                      _wpApi("buildpro_about_leader_title").set(d.title);
                  } catch (e) {}
                  try {
                    if (typeof d.text === "string")
                      _wpApi("buildpro_about_leader_text").set(d.text);
                  } catch (e) {}
                  try {
                    if (typeof d.executives === "string")
                      _wpApi("buildpro_about_leader_executives").set(
                        d.executives,
                      );
                  } catch (e) {}
                  try {
                    if (typeof d.workforce === "string")
                      _wpApi("buildpro_about_leader_workforce").set(
                        d.workforce,
                      );
                  } catch (e) {}
                  try {
                    if (typeof d.enabled !== "undefined")
                      _wpApi("buildpro_about_leader_enabled").set(
                        !!parseInt(d.enabled, 10),
                      );
                  } catch (e) {}
                  try {
                    if (Array.isArray(d.items))
                      _wpApi("buildpro_about_leader_items").set(d.items);
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
      });
    });
  }
})(jQuery);
