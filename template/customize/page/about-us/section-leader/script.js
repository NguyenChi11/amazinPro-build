/* global jQuery */
(function ($) {
  function init(el) {
    var wrap = el.find(".buildpro-about-leader-repeater");
    if (!wrap.length) return;
    var list = wrap.find(".buildpro-about-leader-list");
    var input = wrap.find(".buildpro-about-leader-input");
    var frame = null;
    var didFetch = false;
    var api = window.wp && window.wp.customize ? window.wp.customize : null;
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
        window.BuildProAboutLeader
      ) {
        didFetch = true;
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
              if (Array.isArray(d.items) && d.items.length) {
                setItems(d.items);
                items = d.items;
              }
              if (api) {
                if (typeof d.title === "string") {
                  api("buildpro_about_leader_title").set(d.title);
                }
                if (typeof d.text === "string") {
                  api("buildpro_about_leader_text").set(d.text);
                }
                if (typeof d.executives === "string") {
                  api("buildpro_about_leader_executives").set(d.executives);
                }
                if (typeof d.workforce === "string") {
                  api("buildpro_about_leader_workforce").set(d.workforce);
                }
                if (typeof d.enabled !== "undefined") {
                  api("buildpro_about_leader_enabled").set(
                    !!parseInt(d.enabled, 10),
                  );
                }
              }
              list.empty();
            }
          })
          .always(function () {
            render();
          });
        return;
      }
      items.forEach(function (it, idx) {
        var row = $('<div class="leader-item"/>');
        var leaderHeader = $(
          '<div class="leader-accordion-header"><span class="leader-accordion-label">' +
            (it.name || "Item " + (idx + 1)) +
            '</span><span class="leader-accordion-arrow">&#9660;</span></div>',
        );
        var leaderBody = $(
          '<div class="leader-accordion-body" style="display:none"></div>',
        );
        row.append(leaderHeader).append(leaderBody);
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
            : '<div class="leader-image-empty">No image</div>') +
          "</div>";
        var imgControls =
          '<div class="leader-image-controls">' +
          '<input type="hidden" class="leader-image-id" value="' +
          (it.icon_id || 0) +
          '">' +
          '<button type="button" class="button button-secondary leader-select-image">Select Image</button> ' +
          '<button type="button" class="button leader-remove-image">Remove</button>' +
          "</div>";
        leaderBody.append("<p><label>Image</label></p>");
        leaderBody.append(preview);
        leaderBody.append(imgControls);
        leaderBody.append(
          '<p><label>Name<br><input type="text" class="widefat leader-name" value="' +
            (it.name || "") +
            '"></label></p>',
        );
        leaderBody.append(
          '<p><label>Position<br><input type="text" class="widefat leader-position" value="' +
            (it.position || "") +
            '"></label></p>',
        );
        leaderBody.append(
          '<p><label>Description<br><input type="text" class="widefat leader-description" value="' +
            (it.description || "") +
            '"></label></p>',
        );
        leaderBody.append(
          '<p><label>URL<br><input type="text" class="widefat leader-url" value="' +
            (it.url || "") +
            '"></label></p>',
        );
        leaderBody.append(
          '<p><button type="button" class="button remove-leader">Remove</button></p>',
        );
        row.on("click", ".leader-select-image", function (e) {
          e.preventDefault();
          if (frame) {
            frame.off("select");
          }
          frame = wp.media({
            title: "Select Image",
            button: { text: "Use image" },
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
          items2[idx] = cur;
          setItems(items2);
          var n = row.find(".leader-name").val();
          if (n) leaderHeader.find(".leader-accordion-label").text(n);
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
      });
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
