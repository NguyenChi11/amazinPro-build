

<?php
if (function_exists('buildpro_render_product_sections')) {
    buildpro_render_product_sections();
    return;
}

get_template_part('template/template-parts/page/product/section-title/index');
get_template_part('template/template-parts/page/product/section-products/index');
