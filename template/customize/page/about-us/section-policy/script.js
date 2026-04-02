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

  // Shared fetch (avoid double AJAX when both repeaters load)
  var policyFetchState = {
    fetching: false,
    fetched: false,
    data: null,
    queue: [],
  };

  function fetchPolicyData(callback) {
    if (policyFetchState.fetched) {
      if (callback) callback(policyFetchState.data);
      return;
    }
    policyFetchState.queue.push(callback);
    if (policyFetchState.fetching || !window.BuildProAboutPolicy) return;
    policyFetchState.fetching = true;
    $.ajax({
      url: BuildProAboutPolicy.ajax_url,
      method: "POST",
      dataType: "json",
      data: {
        action: "buildpro_get_about_policy",
        nonce: BuildProAboutPolicy.nonce,
        page_id: BuildProAboutPolicy.default_page_id || 0,
      },
    })
      .done(function (resp) {
        if (resp && resp.success && resp.data) {
          policyFetchState.data = resp.data || {};
        } else {
          policyFetchState.data = null;
        }
      })
      .fail(function () {
        policyFetchState.data = null;
      })
      .always(function () {
        policyFetchState.fetching = false;
        policyFetchState.fetched = true;
        var q = policyFetchState.queue.slice();
        policyFetchState.queue = [];
        q.forEach(function (fn) {
          try {
            if (fn) fn(policyFetchState.data);
          } catch (e) {}
        });
      });
  }

  function init(el) {
    var wrap = el.find(".buildpro-about-policy-repeater");
    if (!wrap.length) return;
    if (wrap.data("buildpro-policy-init")) return;
    wrap.data("buildpro-policy-init", true);

    var list = wrap.find(".buildpro-about-policy-list");
    var input = wrap.find(".buildpro-about-policy-input");
    var frame = null;
    var api = window.wp && window.wp.customize ? window.wp.customize : null;
    var type = wrap.attr("data-type") === "certs" ? "certs" : "items";
    var settingId =
      type === "certs"
        ? "buildpro_about_policy_certifications"
        : "buildpro_about_policy_items";

    function openLinkPicker(urlInputEl, titleInputEl) {
      if (!urlInputEl) return;
      window.buildproLinkTarget = {
        sectionId: "buildpro_about_policy_section",
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
      if (api && api.has && api.has(settingId)) {
        try {
          api(settingId).set(items || []);
        } catch (e) {}
      }
    }
    function render() {
      var items = getItems();
      list.empty();
      if (!items || items.length === 0) {
        var addText = t("addItem", "Add Item");
        list.append(
          '<p class="description">' +
            escHtml(
              sprintf(
                t("noItemsHelp", 'No items. Click "%s" to add.'),
                addText,
              ),
            ) +
            "</p>",
        );
      }
      items.forEach(function (it, idx) {
        var row = $('<div class="policy-item-row"/>');
        var itemFallback = sprintf(t("itemLabel", "Item %d"), idx + 1);
        var openByDefault = idx === 0;
        var policyHeader = $(
          '<div class="policy-accordion-header"><span class="policy-accordion-label">' +
            escHtml(it.title || itemFallback) +
            '</span><span class="policy-accordion-arrow">&#9660;</span></div>',
        );
        var policyBody = $(
          '<div class="policy-accordion-body" style="display:' +
            (openByDefault ? "block" : "none") +
            '"></div>',
        );
        row.append(policyHeader).append(policyBody);
        if (openByDefault) {
          policyHeader
            .find(".policy-accordion-arrow")
            .css("transform", "rotate(0deg)");
        }
        policyHeader.on("click", function () {
          var isOpen = policyBody.css("display") !== "none";
          policyBody.css("display", isOpen ? "none" : "block");
          policyHeader
            .find(".policy-accordion-arrow")
            .css("transform", isOpen ? "rotate(-90deg)" : "rotate(0deg)");
        });
        var previewUrl =
          type === "certs" ? it.image_url || "" : it.icon_url || "";
        var preview =
          '<div class="policy-image-preview">' +
          (previewUrl
            ? '<img src="' +
              previewUrl +
              '" style="max-width:2.75rem;height:auto;border-radius:0.625rem;border:1px solid #e5e7eb;" />'
            : '<div class="policy-image-empty">' +
              escHtml(t("noImageSelected", "No image selected")) +
              "</div>") +
          "</div>";
        var idVal = type === "certs" ? it.image_id || 0 : it.icon_id || 0;
        var imgControls =
          '<div class="policy-image-controls">' +
          '<input type="hidden" class="policy-image-id" value="' +
          idVal +
          '">' +
          '<button type="button" class="button button-secondary policy-select-image">' +
          escHtml(t("chooseImage", "Choose Image")) +
          "</button> " +
          '<button type="button" class="button policy-remove-image">' +
          escHtml(t("remove", "Remove")) +
          "</button>" +
          "</div>";
        policyBody.append(
          "<p><label>" + escHtml(t("image", "Image")) + "</label></p>",
        );
        policyBody.append(preview);
        policyBody.append(imgControls);
        if (type === "certs") {
          policyBody.append(
            "<p><label>" +
              escHtml(t("url", "Button Link")) +
              '<br><input type="text" class="widefat policy-url" value="' +
              (it.url || "") +
              '"></label></p>',
          );
          policyBody.append(
            '<p><button type="button" class="button button-secondary policy-choose-link">' +
              escHtml(t("chooseLink", "Choose Link")) +
              "</button></p>",
          );
        }
        policyBody.append(
          "<p><label>" +
            escHtml(t("title", "Title")) +
            '<br><input type="text" class="widefat policy-title" value="' +
            (it.title || "") +
            '"></label></p>',
        );
        policyBody.append(
          "<p><label>" +
            escHtml(t("description", "Description")) +
            '<br><textarea class="widefat policy-desc" rows="3">' +
            (it.desc || "") +
            "</textarea></label></p>",
        );
        policyBody.append(
          '<p><button type="button" class="button remove-policy-row">' +
            escHtml(t("remove", "Remove")) +
            "</button></p>",
        );
        row.on("click", ".policy-select-image", function (e) {
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
            if (type === "certs") {
              cur.image_id = id;
              cur.image_url = url;
            } else {
              cur.icon_id = id;
              cur.icon_url = url;
            }
            items2[idx] = cur;
            setItems(items2);
            render();
          });
          frame.open();
        });
        row.on("click", ".policy-remove-image", function (e) {
          e.preventDefault();
          var items2 = getItems();
          var cur = items2[idx] || {};
          if (type === "certs") {
            cur.image_id = 0;
            cur.image_url = "";
          } else {
            cur.icon_id = 0;
            cur.icon_url = "";
          }
          items2[idx] = cur;
          setItems(items2);
          render();
        });
        row.on("input change", "input,textarea", function () {
          var items2 = getItems();
          var cur = items2[idx] || {};
          if (type === "certs") {
            cur.url = row.find(".policy-url").val();
          }
          cur.title = row.find(".policy-title").val();
          cur.desc = row.find(".policy-desc").val();
          items2[idx] = cur;
          setItems(items2);
          var t = row.find(".policy-title").val();
          if (t) policyHeader.find(".policy-accordion-label").text(t);
        });

        if (type === "certs") {
          row.on("click", ".policy-url, .policy-choose-link", function (e) {
            if (e && e.preventDefault) e.preventDefault();
            if (e && e.stopPropagation) e.stopPropagation();
            openLinkPicker(
              row.find(".policy-url").get(0),
              row.find(".policy-title").get(0),
            );
          });
        }
        row.on("click", ".remove-policy-row", function (e) {
          e.preventDefault();
          var items2 = getItems();
          items2.splice(idx, 1);
          setItems(items2);
          render();
        });
        list.append(row);
      });
    }
    wrap.on("click", ".buildpro-about-policy-add", function (e) {
      e.preventDefault();
      var items = getItems();
      if (type === "certs") {
        items.push({
          image_id: 0,
          image_url: "",
          url: "",
          title: "",
          desc: "",
        });
      } else {
        items.push({
          icon_id: 0,
          icon_url: "",
          title: "",
          desc: "",
        });
      }
      setItems(items);
      render();
    });
    render();
  }

  // Init via WP customize controls (matches Core Values pattern)
  var _wpApi = window.wp && window.wp.customize ? window.wp.customize : null;
  function initControl(controlId) {
    if (!_wpApi || !_wpApi.control) return;
    _wpApi.control(controlId, function (ctrl) {
      ctrl.deferred.embedded.done(function () {
        var inputEl = ctrl.container.find(".buildpro-about-policy-input");
        var wrap = ctrl.container.find(".buildpro-about-policy-repeater");
        var type = wrap.attr("data-type") === "certs" ? "certs" : "items";
        var settingId =
          type === "certs"
            ? "buildpro_about_policy_certifications"
            : "buildpro_about_policy_items";

        // Seed from in-memory setting first
        var seeded = false;
        if (_wpApi.has && _wpApi.has(settingId)) {
          try {
            var apiVal = _wpApi(settingId).get();
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

        if (!wrap.data("buildpro-policy-fetching")) {
          wrap.data("buildpro-policy-fetching", true);
          var $list = wrap.find(".buildpro-about-policy-list");
          $list.html(
            '<p style="color:#888">' +
              escHtml(t("loading", "Loading...")) +
              "</p>",
          );

          fetchPolicyData(function (d) {
            if (d) {
              var remoteItems =
                type === "certs" ? d.certifications || [] : d.items || [];
              if (_wpApi) {
                try {
                  if (typeof d.title_left === "string")
                    _wpApi("buildpro_about_policy_title_left").set(
                      d.title_left,
                    );
                } catch (e) {}
                try {
                  if (typeof d.business_registration === "string")
                    _wpApi("buildpro_about_policy_business_registration").set(
                      d.business_registration,
                    );
                } catch (e) {}
                try {
                  if (typeof d.general_contractor === "string")
                    _wpApi("buildpro_about_policy_general_contractor").set(
                      d.general_contractor,
                    );
                } catch (e) {}
                try {
                  if (typeof d.duns_number === "string")
                    _wpApi("buildpro_about_policy_duns_number").set(
                      d.duns_number,
                    );
                } catch (e) {}
                try {
                  if (typeof d.title_right === "string")
                    _wpApi("buildpro_about_policy_title_right").set(
                      d.title_right,
                    );
                } catch (e) {}
                try {
                  if (typeof d.warranty_desc === "string")
                    _wpApi("buildpro_about_policy_warranty_desc").set(
                      d.warranty_desc,
                    );
                } catch (e) {}
                try {
                  if (typeof d.enabled !== "undefined")
                    _wpApi("buildpro_about_policy_enabled").set(
                      !!parseInt(d.enabled, 10),
                    );
                } catch (e) {}
                try {
                  if (Array.isArray(d.certifications))
                    _wpApi("buildpro_about_policy_certifications").set(
                      d.certifications,
                    );
                } catch (e) {}
                try {
                  if (Array.isArray(d.items))
                    _wpApi("buildpro_about_policy_items").set(d.items);
                } catch (e) {}
              }
              if (Array.isArray(remoteItems)) {
                inputEl.val(JSON.stringify(remoteItems));
              }
            }
            init(ctrl.container);
          });
        } else {
          init(ctrl.container);
        }
      });
    });
  }

  if (_wpApi && _wpApi.control) {
    initControl("buildpro_about_policy_certifications");
    initControl("buildpro_about_policy_items");
  }
})(jQuery);
