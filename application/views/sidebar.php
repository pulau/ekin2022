<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header"><a href="http://puskesseribuutara.com" target="_blank"><i class="fa fa-globe"></i>PKC Seribu Utara</a></li>
            <li>
                <a href="<?php echo site_url('co_panel'); ?>">
                    <i class="fa fa-desktop"></i> <span>Control Panel</span>
                </a>
            </li>
            <?php
                $list_menus = build_tree($menu_list);
                print_tree($list_menus, TRUE);
            ?>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
