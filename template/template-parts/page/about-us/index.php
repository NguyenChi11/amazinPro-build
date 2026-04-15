<?php
// Include breadcrumb
get_template_part('template/template-parts/breadcrums/index');
?>
<?php
if (function_exists('buildpro_render_about_sections')) {
    buildpro_render_about_sections();
    return;
}

get_template_part('template/template-parts/page/about-us/section-banner/index');
get_template_part('template/template-parts/page/about-us/section-core-values/index');
get_template_part('template/template-parts/page/about-us/section-leader/index');
get_template_part('template/template-parts/page/about-us/section-policy/index');
get_template_part('template/template-parts/page/about-us/section-contact/index');
get_template_part('template/template-parts/page/about-us/section-contact-form/index');
