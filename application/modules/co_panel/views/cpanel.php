<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php $this->load->view('header_cpanel'); ?>
<?php //$this->load->view('sidebar'); ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Apps
        <small>Control Panel</small>
      </h1>
      <ol class="breadcrumb">
        <li class="active"><a href="#"><i class="fa fa-gears"></i> Control Panel</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="row">
            <div class="col-lg-12">
                <div class="box">
                    <div class="box-header">
                      <h3 class="box-title">Manajemen</h3>
                    </div>
                    <div class="box-body">
                        <?php
                        if(sizeof($modul_list) > 0){
                            foreach($modul_list as $manajemen){
                                if(trim($manajemen['modul_kategori']) == "MANAJEMEN"){
                                    echo '<a href="'. site_url($manajemen['modul_url']).'" class="btn btn-app"><i class="'.trim($manajemen['modul_icon']).'"></i>'.$manajemen['label'].'</a>';
                                }
                            }
                        }
                        ?>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="box">
                    <div class="box-header">
                      <h3 class="box-title">Pelayanan</h3>
                    </div>
                    <div class="box-body">
                        <?php
                        if(sizeof($modul_list) > 1){
                            foreach($modul_list as $pelayanan){
                                if(trim($pelayanan['modul_kategori']) == "PELAYANAN"){
                                    echo '<a href="'. site_url($pelayanan['modul_url']).'" class="btn btn-app"><i class="'.trim($pelayanan['modul_icon']).'"></i>'.$pelayanan['label'].'</a>';
                                }
                            }
                        }
                        ?>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php
  $this->load->view('cpanelJs');
  $this->load->view('footer');
  ?>

