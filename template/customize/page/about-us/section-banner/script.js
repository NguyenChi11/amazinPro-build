(function ($, api) {
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

  function init(container) {
    var $root = $(container);
    var $list = $root.find(".buildpro-about-facts-list");
    var $input = $root.find(".buildpro-about-facts-input");
    var $addBtn = $root.find(".buildpro-about-facts-add");
    var MAX = 4;
    var $limitNote = $root.find(".buildpro-about-facts-limit");
    if ($limitNote.length === 0) {
      var limitText = sprintf(
        t(
          "limitNote",
          "Only up to %d items will be saved; extra items will not be saved.",
        ),
        MAX,
      );
      $limitNote = $(
        '<p class="description buildpro-about-facts-limit" style="display:none">' +
          escHtml(limitText) +
          "</p>",
      );
      $limitNote.insertBefore($addBtn.closest("p"));
    }
    function getItems() {
      try {
        var v = $input.val();
        var arr = JSON.parse(v);
        return Array.isArray(arr) ? arr : [];
      } catch (e) {
        return [];
      }
    }
    (function ensureValidJSON() {
      var initItems = getItems();
      if (!Array.isArray(initItems)) {
        initItems = [];
      }
      $input.val(JSON.stringify(initItems));
    })();
    function setItems(items, opts) {
      var options = opts || {};
      var notify = options.notify !== false;
      $input.val(JSON.stringify(items));
      if (
        api &&
        typeof api === "function" &&
        api.has &&
        api.has("buildpro_about_banner_facts")
      ) {
        try {
          api("buildpro_about_banner_facts").set(items);
        } catch (e) {}
      }
      if (notify) {
        $input.trigger("change");
      }
    }
    function updateAddState(count) {
      var over = count > MAX;
      $addBtn.prop("disabled", false);
      $limitNote.toggle(over);
    }
    function render() {
      var items = getItems();
      $list.empty();
      updateAddState(items.length);
      items.forEach(function (it, idx) {
        var $item = $('<div class="buildpro-about-fact"></div>');
        var itemFallback = sprintf(t("itemLabel", "Item %d"), idx + 1);
        var $factHeader = $(
          '<div class="fact-accordion-header"><span class="fact-accordion-label">' +
            escHtml(it.label || itemFallback) +
            '</span><span class="fact-accordion-arrow">&#9660;</span></div>',
        );
        var $factBody = $(
          '<div class="fact-accordion-body" style="display:none"></div>',
        );
        $item.append($factHeader).append($factBody);
        $factHeader.on("click", function () {
          var isOpen = $factBody.css("display") !== "none";
          $factBody.css("display", isOpen ? "none" : "block");
          $factHeader
            .find(".fact-accordion-arrow")
            .css("transform", isOpen ? "rotate(-90deg)" : "rotate(0deg)");
        });
        var $label = $(
          "<p><label>" +
            escHtml(t("label", "Label")) +
            '<br><input type="text" class="widefat"></label></p>',
        );
        var $value = $(
          "<p><label>" +
            escHtml(t("value", "Value")) +
            '<br><input type="text" class="widefat"></label></p>',
        );
        var $remove = $(
          '<p><button type="button" class="button remove-fact">' +
            escHtml(t("remove", "Remove")) +
            "</button></p>",
        );
        $label.find("input").val(it.label || "");
        $value.find("input").val(it.value || "");
        $factBody.append($label).append($value).append($remove);
        $list.append($item);
        $label.find("input").on("input", function () {
          var items2 = getItems();
          items2[idx] = items2[idx] || { label: "", value: "" };
          items2[idx].label = String($(this).val() || "");
          setItems(items2, { notify: false });
          var v = $(this).val();
          if (v) $factHeader.find(".fact-accordion-label").text(v);
        });
        $label.find("input").on("blur", function () {
          $input.trigger("change");
        });
        $value.find("input").on("input", function () {
          var items2 = getItems();
          items2[idx] = items2[idx] || { label: "", value: "" };
          items2[idx].value = String($(this).val() || "");
          setItems(items2, { notify: false });
        });
        $value.find("input").on("blur", function () {
          $input.trigger("change");
        });
      });
      $list
        .off("click.buildproFactsRemove")
        .on("click.buildproFactsRemove", ".remove-fact", function (e) {
          e.preventDefault();
          var $it = $(this).closest(".buildpro-about-fact");
          var index = $it.index();
          var items2 = getItems();
          if (index >= 0) {
            items2.splice(index, 1);
            setItems(items2);
            render();
          }
        });
    }
    $root
      .off("click.buildproFactsAdd")
      .on("click.buildproFactsAdd", ".buildpro-about-facts-add", function (e) {
        e.preventDefault();
        e.stopPropagation();
        var items = getItems();
        items.push({ label: "", value: "" });
        setItems(items);
        render();
      });
    render();
    $input.off("change").on("change", function () {});
  }
  $(document).on("ready", function () {
    $(".buildpro-about-facts-repeater").each(function () {
      init(this);
    });
  });
  if (api && api.control) {
    api.control("buildpro_about_banner_facts", function (ctrl) {
      var setting =
        api && api.has && api.has("buildpro_about_banner_facts")
          ? api("buildpro_about_banner_facts")
          : null;

      function boot() {
        var el = ctrl.container.find(".buildpro-about-facts-repeater")[0];
        if (!el) return;
        // Seed input from WP setting API value (correct array, not corrupted by jQuery .val(array))
        if (api && api.has && api.has("buildpro_about_banner_facts")) {
          try {
            var apiVal = api("buildpro_about_banner_facts").get();
            if (Array.isArray(apiVal) && apiVal.length > 0) {
              $(el)
                .find(".buildpro-about-facts-input")
                .val(JSON.stringify(apiVal));
            }
          } catch (e) {}
        }
        init(el);
      }

      function fetchAndPopulateFromMeta() {
        try {
          var pid = 0;
          if (window.BuildProAboutFacts && BuildProAboutFacts.default_page_id) {
            pid = parseInt(BuildProAboutFacts.default_page_id, 10) || 0;
          }
          if (!pid) return;
          if (
            !window.BuildProAboutFacts ||
            !BuildProAboutFacts.ajax_url ||
            !BuildProAboutFacts.nonce
          )
            return;
          var $wrap = ctrl.container.find(".buildpro-about-facts-repeater");
          var $inp = $wrap.find(".buildpro-about-facts-input");
          var cur = [];
          try {
            cur = JSON.parse($inp.val() || "[]");
          } catch (e) {
            cur = [];
          }
          // Only fetch from AJAX if no data already loaded
          if (Array.isArray(cur) && cur.length > 0) return;
          $.ajax({
            url: BuildProAboutFacts.ajax_url,
            method: "GET",
            data: {
              action: "buildpro_get_about_facts",
              nonce: BuildProAboutFacts.nonce,
              page_id: pid,
            },
          }).done(function (resp) {
            if (
              resp &&
              resp.success &&
              resp.data &&
              Array.isArray(resp.data.facts) &&
              resp.data.facts.length > 0
            ) {
              $inp.val(JSON.stringify(resp.data.facts));
              var _el = $wrap[0];
              if (_el) init(_el);
              if (
                api &&
                typeof api === "function" &&
                api.has &&
                api.has("buildpro_about_banner_facts")
              ) {
                try {
                  api("buildpro_about_banner_facts").set(resp.data.facts);
                } catch (e) {}
              }
            }
          });
        } catch (e) {}
      }

      if (setting && setting.bind) {
        setting.bind(function (val) {
          try {
            var arr = Array.isArray(val) ? val.slice(0) : [];
            var _el = ctrl.container.find(".buildpro-about-facts-repeater")[0];
            if (_el) {
              var $inp = $(_el).find(".buildpro-about-facts-input");
              var newJSON = JSON.stringify(arr);
              // Skip re-render if the change originated from within this control
              // (i.e. the hidden input already holds the same data). This prevents
              // input fields from losing focus while the user is typing.
              if ($inp.val() === newJSON) return;
              $inp.val(newJSON);
              init(_el);
            }
          } catch (e) {}
        });
      }

      // ctrl.deferred.embedded fires when the control HTML is actually injected into the DOM.
      // Calling boot() before this (in api.control callback directly) means ctrl.container.find()
      // returns nothing because the section hasn't been opened yet.
      ctrl.deferred.embedded.done(function () {
        boot();
        fetchAndPopulateFromMeta();

        var sec = api.section && api.section("buildpro_about_banner_section");
        if (sec && sec.expanded) {
          sec.expanded.bind(function (exp) {
            if (exp) {
              boot();
              fetchAndPopulateFromMeta();
            }
          });
        }
      });
    });
  }
})(jQuery, wp.customize);
