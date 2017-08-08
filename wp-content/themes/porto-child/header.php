<!DOCTYPE html><html <?php language_attributes(); ?>><head>    <meta charset="utf-8">    <!--[if IE]>    <meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>    <link rel="profile" href="http://gmpg.org/xfn/11"/>    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>"/>    <?php get_template_part('head'); ?>    <script>        window.addEventListener('scroll', function (e) {            if (window.scrollY > 300) {                document.getElementById("sticky").className = "sticky-menu sticky-menu-show";            }            else {                document.getElementById("sticky").className = "sticky-menu";            }        })    </script></head><?phpglobal $porto_settings, $porto_design;$wrapper = porto_get_wrapper_type();$body_class = $wrapper;$body_class .= ' blog-' . get_current_blog_id();$body_class .= ' ' . $porto_settings['css-type'];$header_type = porto_get_header_type();if ($header_type == 'side')    $body_class .= ' body-side';$loading_overlay = porto_get_meta_value('loading_overlay');$showing_overlay = false;if ('no' !== $loading_overlay && ('yes' === $loading_overlay || ('yes' !== $loading_overlay && $porto_settings['show-loading-overlay']))) {    $showing_overlay = true;    $body_class .= ' loading-overlay-showing';}// Vendors Start//if (class_exists('WC_Vendors')) {//////    if (is_product()) {//////        if ($porto_settings['porto_single_wcvendors_hide_header']) {//////            $header_type = porto_get_header_type();//////            if ($header_type == 'side')//////                $body_class .= ' body-side';//////            $loading_overlay = porto_get_meta_value('loading_overlay');//////            $showing_overlay = false;//////            if ('no' !== $loading_overlay && ('yes' === $loading_overlay || ('yes' !== $loading_overlay && $porto_settings['show-loading-overlay']))) {//////                $showing_overlay = true;//////                $body_class .= ' loading-overlay-showing';//////            }//////        }//////    } else {//////        $header_type = porto_get_header_type();//////        if ($header_type == 'side')//////            $body_class .= ' body-side';//////        $loading_overlay = porto_get_meta_value('loading_overlay');//////        $showing_overlay = false;//////        if ('no' !== $loading_overlay && ('yes' === $loading_overlay || ('yes' !== $loading_overlay && $porto_settings['show-loading-overlay']))) {//////            $showing_overlay = true;//////            $body_class .= ' loading-overlay-showing';//////        }//////    }//////}?><body <?php body_class(array($body_class)); ?><?php echo $showing_overlay ? 'data-loading-overlay' : '' ?>><?php// Showing Overlayif ($showing_overlay) : ?>    <div class="loading-overlay">        <div class="loader"></div>    </div><?phpendif;// Get Meta Valueswp_reset_postdata();global $porto_layout, $porto_sidebar;$porto_layout = porto_meta_layout();$porto_sidebar = porto_meta_sidebar();$porto_banner_pos = porto_get_meta_value('banner_pos');if (($porto_layout == 'left-sidebar' || $porto_layout == 'right-sidebar') && !(($porto_sidebar && is_active_sidebar($porto_sidebar)) || porto_have_sidebar_menu())) {    $porto_layout = 'fullwidth';}if (($porto_layout == 'wide-left-sidebar' || $porto_layout == 'wide-right-sidebar') && !(($porto_sidebar && is_active_sidebar($porto_sidebar)) || porto_have_sidebar_menu())) {    $porto_layout = 'widewidth';}if (porto_show_archive_filter()) {    if ($porto_layout == 'fullwidth') $porto_layout = 'left-sidebar';    if ($porto_layout == 'widewidth') $porto_layout = 'wide-left-sidebar';}$breadcrumbs = $porto_settings['show-breadcrumbs'] ? porto_get_meta_value('breadcrumbs', true) : false;$page_title = $porto_settings['show-pagetitle'] ? porto_get_meta_value('page_title', true) : false;$content_top = porto_get_meta_value('content_top');$content_inner_top = porto_get_meta_value('content_inner_top');if ((is_front_page() && is_home()) || is_front_page()) {    $breadcrumbs = false;    $page_title = false;}do_action('porto_before_wrapper');?><div    class="page-wrapper<?php if ($header_type == 'side') echo ' side-nav' ?><?php if (isset($porto_settings['header-side-position']) && $porto_settings['header-side-position']) echo ' side-nav-right' ?>">    <!-- page wrapper -->    <?php    if ($porto_banner_pos == 'before_header') {        porto_banner('banner-before-header');    }    do_action('porto_before_header');    ?>    <?php if (porto_get_meta_value('header', true)) : ?>        <div            class="header-wrapper<?php if ($porto_settings['header-wrapper'] == 'wide') echo ' wide' ?><?php if ($porto_settings['sticky-header-effect'] == 'reveal') echo ' header-reveal' ?><?php if (!($header_type == 'side' && $wrapper == 'boxed') && ($porto_banner_pos == 'below_header' || $porto_banner_pos == 'fixed' || porto_get_meta_value('header_view') == 'fixed')) {                echo ' fixed-header';                if ($porto_settings['header-fixed-show-bottom']) echo ' header-transparent-bottom-border';            } ?><?php if ($header_type == 'side') echo ' header-side-nav' ?> clearfix"><!-- header wrapper -->            <?php            global $porto_settings;            ?>            <?php            global $porto_settings, $porto_layout;            ?>            <header id="header"                    class="custom header-separate header-1 <?php echo $porto_settings['search-size'] ?> sticky-menu-header<?php echo ($porto_settings['logo-overlay'] && $porto_settings['logo-overlay']['url']) ? ' logo-overlay-header' : '' ?>">                <div class="container">                    <div class="pull-left" style="text-align: center;">                        <?php                        $profileUrl = '/my-account';                        // show logo                        $logo = porto_logo();                        echo $logo;                        ?>                        <?php                        $minicart = porto_minicart();                        ?>                        <div                            class="<?php if ($minicart) echo 'header-minicart' . str_replace('minicart', '', $porto_settings['minicart-type']) ?>">                            <?php                            echo $minicart;                            ?>                        </div>                        <a href="/wishlist" class="star">                            <img src="/wp-content/themes/porto-child/images/startop.png" alt="">                        </a>                        <span class="wishlist-items">                            <?php                            $wishlist_count = YITH_WCWL()->count_products();                            echo $wishlist_count;                            ?>                        </span>                        <div class="login">                            <?php                            if (is_user_logged_in()) {                                echo '<a href="' . $profileUrl . '"><img src="/wp-content/themes/porto-child/images/profile.png"></a>';                            }                            ?>                            <?php echo do_shortcode('[wf_login_link img_logout="/wp-content/themes/porto-child/images/logout.png" img_login="/wp-content/themes/porto-child/images/login.png"]'); ?>                            <?php echo do_shortcode('[wf_signup_link img_signup="/wp-content/themes/porto-child/images/register.png"]'); ?>                        </div>                        <?php                        get_template_part('header/header_tooltip');                        ?>                    </div>                    <div class="pull-right">                        <div class="nav-menu-custom">                            <?php                            // show welcome message and top navigation                            $top_nav = porto_top_navigation();                            echo $top_nav;                            ?>                            <a class="mobile-toggle"><i class="fa fa-reorder"></i></a>                        </div>                    </div>                </div>                <div id="sticky" class="sticky-menu">                    <div class="container" style=" position: relative;    padding-left: 80px;">                        <div class="sticky-main-menu">                            <i class="fa fa-reorder"></i>                            <?php                            echo $top_nav;                            ?>                        </div>                        <div                            class="<?php if ($minicart) echo 'header-minicart' . str_replace('minicart', '', $porto_settings['minicart-type']) ?>">                            <?php                            echo $minicart;                            ?>                        </div>                        <a href="/wishlist" class="star">                            <img src="/wp-content/themes/porto-child/images/startop.png" alt="">                        </a>                        <span class="wishlist-items">                            <?php                            echo $wishlist_count;                            ?>                        </span>                        <?php echo do_shortcode('[wd_asp elements=\'search\' ratio=\'100%\' id=1]'); ?>                        <div class="login pull-right" style="    padding-top: 15px;">                            <?php                            if (is_user_logged_in()) {                                echo '<a href="' . $profileUrl . '"><img src="/wp-content/themes/porto-child/images/profile.png"></a>';                            }                            ?>                            <?php echo do_shortcode('[wf_login_link img_logout="/wp-content/themes/porto-child/images/logout.png" img_login="/wp-content/themes/porto-child/images/login.png"]'); ?>                            <?php echo do_shortcode('[wf_signup_link img_signup="/wp-content/themes/porto-child/images/register.png"]'); ?>                        </div>                    </div>                </div>            </header>        </div><!-- end header wrapper -->        <div class="icon-cart-row">            <?php            echo $minicart;            ?>            <a href="/wishlist" class="star">                <img src="/wp-content/themes/porto-child/images/startop.png" alt="">                <span class="wishlist-items"> <?php echo $wishlist_count; ?> </span>            </a>        </div>    <?php endif; ?>    <?php    do_action('porto_before_banner');    if ($porto_banner_pos != 'before_header') {        porto_banner(($porto_banner_pos == 'fixed' && 'boxed' !== $wrapper) ? 'banner-fixed' : '');    }    ?>    <?php    do_action('porto_before_breadcrumbs');    get_template_part('breadcrumbs');    do_action('porto_before_main');    ?>    <div id="main"         class="<?php if ($porto_layout == 'wide-left-sidebar' || $porto_layout == 'wide-right-sidebar' || $porto_layout == 'left-sidebar' || $porto_layout == 'right-sidebar') echo 'column2' . ' column2-' . str_replace('wide-', '', $porto_layout); else echo 'column1'; ?><?php if ($porto_layout == 'widewidth' || $porto_layout == 'wide-left-sidebar' || $porto_layout == 'wide-right-sidebar') echo ' wide clearfix'; else echo ' boxed' ?><?php if (!$breadcrumbs && !$page_title) echo ' no-breadcrumbs' ?><?php if (porto_get_wrapper_type() != 'boxed' && $porto_settings['main-wrapper'] == 'boxed') echo ' main-boxed' ?>">        <!-- main -->        <?php        do_action('porto_before_content_top');        if ($content_top) : ?>            <div id="content-top"><!-- begin content top -->                <?php foreach (explode(',', $content_top) as $block) {                    echo do_shortcode('[porto_block name="' . $block . '"]');                } ?>            </div><!-- end content top -->        <?php endif;        do_action('porto_after_content_top');        ?>        <?php if ($wrapper == 'boxed' || $porto_layout == 'fullwidth' || $porto_layout == 'left-sidebar' || $porto_layout == 'right-sidebar') : ?>        <div class="container">            <?php if (class_exists('WC_Vendors')) {                porto_wc_vendor_header();            } ?>            <?php else: ?>            <div class="container-fluid">                <?php endif; ?>                <div class="row main-content-wrap">                    <!-- main content -->                    <div                        class="main-content <?php if ($porto_layout == 'wide-left-sidebar' || $porto_layout == 'wide-right-sidebar' || $porto_layout == 'left-sidebar' || $porto_layout == 'right-sidebar') echo 'col-md-9'; else echo 'col-md-12'; ?>">                        <?php wp_reset_postdata(); ?>                        <?php                        do_action('porto_before_content_inner_top');                        if ($content_inner_top) : ?>                        <div id="content-inner-top"><!-- begin content inner top -->                            <?php foreach (explode(',', $content_inner_top) as $block) {                                echo do_shortcode('[porto_block name="' . $block . '"]');                            } ?>                        </div><!-- end content inner top --><?php endif;do_action('porto_after_content_inner_top');?>