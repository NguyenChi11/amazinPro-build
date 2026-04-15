<?php
// Include breadcrumb
get_template_part('template/template-parts/breadcrums/index');
?>

<?php
if (function_exists('buildpro_render_project_sections')) {
    buildpro_render_project_sections();
    return;
}

get_template_part('template/template-parts/page/projects/section-title/index');
get_template_part('template/template-parts/page/projects/section-list/index');
