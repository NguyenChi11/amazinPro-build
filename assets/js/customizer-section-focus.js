/**
 * customizer-section-focus.js
 * Runs inside the Customizer CONTROLS pane.
 * Sends native postMessages to the preview iframe when a section is focused.
 */
(function (api) {
  if (!api || !api.section) {
    return;
  }

  var activeSectionId = null;
  var iframeLoadBound = false;

  function getPreviewWindow() {
    var iframe = document.querySelector("#customize-preview iframe");
    return iframe && iframe.contentWindow ? iframe.contentWindow : null;
  }

  function postToPreview(payload) {
    var previewWindow = getPreviewWindow();
    if (!previewWindow) {
      return;
    }
    previewWindow.postMessage(payload, "*");
  }

  function sendFocus(sectionId) {
    if (!sectionId) {
      return;
    }
    activeSectionId = sectionId;
    postToPreview({
      _buildpro: true,
      type: "section-focus",
      sectionId: sectionId,
    });
  }

  function sendBlur(sectionId) {
    if (sectionId && activeSectionId === sectionId) {
      activeSectionId = null;
    }
    postToPreview({
      _buildpro: true,
      type: "section-blur",
      sectionId: sectionId || "",
    });
  }

  function getSectionIdFromNode(node) {
    while (node && node !== document) {
      if (node.id && node.id.indexOf("accordion-section-") === 0) {
        return node.id.replace("accordion-section-", "");
      }
      if (node.id && node.id.indexOf("customize-section-") === 0) {
        return node.id.replace("customize-section-", "");
      }
      node = node.parentNode;
    }
    return "";
  }

  function maybeFocusSection(sectionId) {
    if (!sectionId) {
      return;
    }
    window.setTimeout(function () {
      var section = api.section(sectionId);
      if (section && section.expanded && section.expanded()) {
        sendFocus(sectionId);
      }
    }, 0);
  }

  function bindSection(section) {
    if (
      !section ||
      !section.id ||
      !section.expanded ||
      !section.expanded.bind
    ) {
      return;
    }

    section.expanded.bind(function (isExpanded) {
      if (isExpanded) {
        sendFocus(section.id);
        return;
      }
      if (activeSectionId === section.id) {
        sendBlur(section.id);
      }
    });
  }

  function bindAllSections() {
    api.section.each(function (section) {
      bindSection(section);
    });

    if (api.section.bind) {
      api.section.bind("add", function (section) {
        bindSection(section);
      });
    }
  }

  function bindControlClicks() {
    document.addEventListener(
      "click",
      function (event) {
        var sectionId = getSectionIdFromNode(event.target);
        if (!sectionId) {
          return;
        }
        maybeFocusSection(sectionId);
      },
      true,
    );
  }

  function bindPreviewReload() {
    if (iframeLoadBound) {
      return;
    }

    var iframe = document.querySelector("#customize-preview iframe");
    if (!iframe) {
      window.setTimeout(bindPreviewReload, 250);
      return;
    }

    iframeLoadBound = true;
    iframe.addEventListener("load", function () {
      if (!activeSectionId) {
        return;
      }
      window.setTimeout(function () {
        sendFocus(activeSectionId);
      }, 150);
    });
  }

  api.bind("ready", function () {
    bindAllSections();
    bindControlClicks();
    bindPreviewReload();
  });
})(window.wp && window.wp.customize);
