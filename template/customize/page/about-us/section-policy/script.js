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
    var wrap = el.find(".buildpro-about-policy-repeater");
    if (!wrap.length) return;
    var list = wrap.find(".buildpro-about-policy-list");
    var input = wrap.find(".buildpro-about-policy-input");
    var frame = null;
    var didFetch = false;
    var api = window.wp && window.wp.customize ? window.wp.customize : null;
    var type = wrap.attr("data-type") === "certs" ? "certs" : "items";
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
      input.trigger("change");
    }
    function render() {
      var items = getItems();
      list.empty();
      if (
        (!items || items.length === 0) &&
        !didFetch &&
        window.BuildProAboutPolicy
      ) {
        didFetch = true;
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
              var d = resp.data || {};
              var remoteItems =
                type === "certs" ? d.certifications || [] : d.items || [];
              if (Array.isArray(remoteItems) && remoteItems.length) {
                setItems(remoteItems);
                items = remoteItems;
              }
              if (api) {
                if (typeof d.title_left === "string") {
                  api("buildpro_about_policy_title_left").set(d.title_left);
                }
                if (typeof d.business_registration === "string") {
                  api("buildpro_about_policy_business_registration").set(
                    d.business_registration,
                  );
                }
                if (typeof d.general_contractor === "string") {
                  api("buildpro_about_policy_general_contractor").set(
                    d.general_contractor,
                  );
                }
                if (typeof d.duns_number === "string") {
                  api("buildpro_about_policy_duns_number").set(d.duns_number);
                }
                if (typeof d.title_right === "string") {
                  api("buildpro_about_policy_title_right").set(d.title_right);
                }
                if (typeof d.warranty_desc === "string") {
                  api("buildpro_about_policy_warranty_desc").set(
                    d.warranty_desc,
                  );
                }
                if (typeof d.enabled !== "undefined") {
                  api("buildpro_about_policy_enabled").set(
                    !!parseInt(d.enabled, 10),
                  );
                }
              }
              list.empty();
            }
          })
          .always(function () {
            // after attempt, render whatever we have
            render();
          });
        return;
      }
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
        var policyHeader = $(
          '<div class="policy-accordion-header"><span class="policy-accordion-label">' +
            escHtml(it.title || itemFallback) +
            '</span><span class="policy-accordion-arrow">&#9660;</span></div>',
        );
        var policyBody = $(
          '<div class="policy-accordion-body" style="display:none"></div>',
        );
        row.append(policyHeader).append(policyBody);
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
              escHtml(t("url", "URL")) +
              '<br><input type="text" class="widefat policy-url" value="' +
              (it.url || "") +
              '"></label></p>',
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
  $(function () {
    $(".customize-control").each(function () {
      init($(this));
    });
  });
})(jQuery);
