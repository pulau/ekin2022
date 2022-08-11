<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php $this->load->view('header'); ?>
<?php $this->load->view('sidebar'); ?>
  <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            E-Kinerja
            <small>Validasi SKP Tahunan</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url('co_panel'); ?>"><i class="fa fa-dashboard"></i> CPanel</a></li>
            <li><?php echo $bc_parent; ?></li><li class="active"><?php echo $bc_child; ?></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div id="notification_type" class="alert alert-dismissable" style="display:none;">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p id="notification_message"></p>
                        </div>
                            <table id="table_validasi_skp_pegawai" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width:15px;"> No </th>
                                    <th> NIP </th>
                                    <th> Nama </th>
                                    <th> Unit </th>
                                    <th> Jumlah Aktifitas </th>
                                    <th> Actions </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5"> No Data to Display</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th style="width:15px;"> No </th>
                                    <th> NIP </th>
                                    <th> Nama </th>
                                    <th> Unit </th>
                                    <th> Jumlah Aktifitas </th>
                                    <th> Actions </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  <div class="modal fade" id="listSKPValidasiModal" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Input Aktifitas <i class="fa fa-arrow-right"></i> <span id="peg_nama"></span></h4>
            </div>
            <div class="modal-body">
                <div id="upd_alert_type" class="alert alert-dismissable" style="display:none;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p id="upd_alert_message"></p>
                </div>
                <input type="hidden" id="<?php echo $this->security->get_csrf_token_name()?>_val" name="<?php echo $this->security->get_csrf_token_name()?>_val" value="<?php echo $this->security->get_csrf_hash()?>" />
                <table class="table table-striped table-bordered table-hover" id="table_list_skpt">
                    <thead>
                        <tr>
                            <th style="width:15px;"> No </th>
                            <th> SKP </th>
                            <th> Kuantitas </th>
                            <th> Waktu Efektif </th>
                            <th> Waktu Total </th>
                            <th> Actions </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6"> No Data to Display</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<?php
$this->load->view('validasi_skpJs');
$this->load->view('footer');
?>