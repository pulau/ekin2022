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
            <small>Download Kinerja</small>
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
                          <button type="button" class="btn btn-success" id="exportToExcel" onclick="exportExcel();"><i class="fa fa-file-excel-o"></i> Export To Excel</button>
                      </div>
                      <input type="hidden" id="<?php echo $this->security->get_csrf_token_name()?>_del" name="<?php echo $this->security->get_csrf_token_name()?>_del" value="<?php echo $this->security->get_csrf_hash()?>" />
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div id="notification_type" class="alert alert-dismissable" style="display:none;">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p id="notification_message"></p>
                        </div>
                        <table id="tabel_download_aktifitas" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 5%;"> No </th>
                                    <th style="width: 8%;"> Tanggal </th>
                                    <th style="width: 8%;"> Jam Mulai </th>
                                    <th style="width: 8%;"> Jam Akhir </th>
                                    <th style="width: 45%;"> Uraian </th>
                                    <th style="width: 26;"> detail </th>
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


  
  <?php
  $this->load->view('download_aktifitasJs');
  $this->load->view('footer');
  ?>