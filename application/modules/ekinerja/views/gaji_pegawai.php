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
            <small>Gaji Pegawai</small>
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
                        <h3 class="box-title">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" name="filter_bln" id="filter_bln" class="form-control blnpicker" placeholder="M yyyy" value="<?php echo date('M Y'); ?>">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-danger" id="goFilter">Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </h3>
                      <div class="box-tools">
                          <!--<button type="button" class="btn btn-block btn-success" data-toggle="modal" data-target="#addSKPTahunanModal"><i class="fa fa-plus"></i> Tambah SKP Tahunan</button>-->
                          <button type="button" class="btn btn-primary" id="sinkron" data-target="#sinkronisasiModal" data-toggle="modal"><i class="fa fa-refresh"></i> Sinkronisasi</button>
                          <button type="button" class="btn btn-success" id="exportToExcel" onclick="exportExcel();"><i class="fa fa-file-excel-o"></i> Export To Excel</button>
                      </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div id="notification_type" class="alert alert-dismissable" style="display:none;">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p id="notification_message"></p>
                        </div>
                        <table id="table_gaji_pegawai" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width:15px;"> No </th>
                                    <th> NIP </th>
                                    <th> Nama </th>
                                    <th> Masa Kerja </th>
                                    <th> Gaji Pokok </th>
                                    <th> Tunj. Suami/Istri </th>
                                    <th> Tunj. Anak </th>
                                    <th> Transport </th>
                                    <th> Jumlah </th>
                                    <th> Pph 21 </th>
                                    <th> Jumlah yg diterima </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="11"> No Data to Display</td>
                                </tr>
                            </tbody>
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
  
    <div class="modal fade bd-example-modal-lg" id="spinnerModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="basic" aria-hidden="true" style="z-index:1051;">
        <div class="modal-dialog modal-sm">
            <div class="modal-content" style="width: 48px">
                <span class="fa fa-spinner fa-spin fa-3x"></span>
            </div>
        </div>
    </div>

    <div class="modal fade" id="sinkronisasiModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Sinkronisasi Gaji</h4>
        </div>
        <div class="modal-body">
            <div id="alert_type" class="alert alert-dismissable" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p id="alert_message"></p>
            </div>
            <?php echo form_open('#',array('id' => 'formSinkronisi','role'=> 'form'))?>
                <div class="form-group">
                    <label>Bulan</label>
                    <input type="text" class="form-control blnpicker" name="bln_sinkronisasi" id="bln_sinkronisasi" placeholder="M yyyy" value="<?php echo date('M Y'); ?>" readonly>
                </div>
            <?php echo form_close(); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnSinkronisasi">Mulai Sinkronisasi</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

  <?php
  $this->load->view('gaji_pegawaiJs');
  $this->load->view('footer');
  ?>