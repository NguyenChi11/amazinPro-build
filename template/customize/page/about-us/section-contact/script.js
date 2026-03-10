/* global jQuery */
(function ($) {
  $(function () {
    var api = window.wp && window.wp.customize ? window.wp.customize : null;
    if (!api || !window.BuildProAboutContact) return;
    function applyData(d) {
      if (typeof d.enabled !== "undefined") {
        api("buildpro_about_contact_enabled").set(!!parseInt(d.enabled, 10));
      }
      if (typeof d.title === "string") {
        api("buildpro_about_contact_title").set(d.title);
      }
      if (typeof d.text === "string") {
        api("buildpro_about_contact_text").set(d.text);
      }
      if (typeof d.address === "string") {
        api("buildpro_about_contact_address").set(d.address);
      }
      if (typeof d.phone === "string") {
        api("buildpro_about_contact_phone").set(d.phone);
      }
      if (typeof d.email === "string") {
        api("buildpro_about_contact_email").set(d.email);
      }
    }
    function fetchData() {
      $.ajax({
        url: BuildProAboutContact.ajax_url,
        method: "POST",
        dataType: "json",
        data: {
          action: "buildpro_get_about_contact",
          nonce: BuildProAboutContact.nonce,
          page_id: BuildProAboutContact.default_page_id || 0,
        },
      }).done(function (resp) {
        if (resp && resp.success && resp.data) {
          applyData(resp.data || {});
        }
      });
    }
    fetchData();
    var sec = api.section && api.section("buildpro_about_contact_section");
    if (sec && typeof sec.expanded !== "undefined") {
      sec.expanded.bind(function (exp) {
        if (!exp) return;
        fetchData();
      });
    }
  });
})(jQuery);
