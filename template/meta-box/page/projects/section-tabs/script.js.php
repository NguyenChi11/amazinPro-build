<?php
// JavaScript for tab functionality
?>
<script>
    (function() {
        function init() {
            var tabs = document.querySelectorAll(".buildpro-admin-tab");
            var ids = ["buildpro_projects_title_meta"];

            function show(id) {
                ids.forEach(function(x) {
                    var el = document.getElementById(x);
                    if (el) {
                        el.style.display = (x === id) ? "block" : "none";
                    }
                });
                tabs.forEach(function(b) {
                    b.classList.toggle("is-active", b.getAttribute("data-target") === id);
                });
            }
            show("buildpro_projects_title_meta");
            tabs.forEach(function(b) {
                b.addEventListener("click", function() {
                    show(b.getAttribute("data-target"));
                });
            });
        }
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", init);
        } else {
            init();
        }
    })();
</script>