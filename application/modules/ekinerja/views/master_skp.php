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
            <small>Master SKP</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url('co_panel'); ?>"><i class="fa fa-dashboard"></i> CPanel</a></li>
            <li>Ekinerja</li><li><?php echo $bc_parent; ?></li><li class="active"><?php echo $bc_child; ?></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                      <h3 class="box-title">Tambah SKP</h3>
                      <div class="box-tools"></div>
                    </div>
                    <div class="box-body">
                        <div class="col-lg-4">
                            <?php echo form_open('#',array('id' => 'formSKP','role'=> 'form'))?>
                                <div class="form-group">
                                    <label>SKP</label>
                                    <textarea name="skp_nama" id="skp_nama" class="form-control"></textarea>
                                    <input type="hidden" id="kd_skp" name="kd_skp" readonly="readonly">
                                </div>
                                <div class="form-group">
                                    <label>Waktu (menit)</label>
                                    <input type="text" id="waktu" name="waktu" class="form-control" placeholder="Masukan Angka" onkeypress="return numbersOnly(event);">
                                </div>
                                <div class="form-actions">
                                    <button type="button" id="btnSaveSKP" class="btn btn-info">Simpan</button>
                                    <button type="button" class="btn btn-default" onclick="clearForm()">Bersihkan</button>
                                </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <div class="box-header">
                      <h3 class="box-title">List Master SKP</h3>
                      <div class="box-tools">
                          <button type="button" class="btn btn-warning" id="btnImportData" data-target="#importDataModal" data-toggle="modal">Import SKP</button>
                      </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div id="notification_type" class="alert alert-dismissable" style="display:none;">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p id="notification_message"></p>
                        </div>
                        <input type="hidden" id="<?php echo $this->security->get_csrf_token_name()?>_del" name="<?php echo $this->security->get_csrf_token_name()?>_del" value="<?php echo $this->security->get_csrf_hash()?>" />
                        <table id="table_skp" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th style="width: 15px;"> No </th>
                                <th> SKP </th>
                                <th> Waktu (Menit) </th>
                                <th> Actions </th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4"> No Data to Display</td>
                                </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th style="width: 15px;"> No </th>
                                <th> SKP </th>
                                <th> Waktu (Menit) </th>
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

<div class="modal fade bd-example-modal-lg" id="spinnerModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="basic" aria-hidden="true" style="z-index:1051;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="width: 48px">
            <span class="fa fa-spinner fa-spin fa-3x"></span>
        </div>
    </div>
</div>
<!-- /.modal -->

<div class="modal fade" id="importDataModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Import Data SKP dari File .xls</h4>
            </div>
            <div class="modal-body">
                <div id="alert_type_import" class="alert alert-dismissable" style="display:none;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p id="alert_message_import"></p>
                </div>
                <?php echo form_open('#',array('id' => 'formImport','role'=> 'form', 'enctype' => 'multipart/form-data')) ?>
                <div class="form-group">
                    <label>Upload File Data SKP</label>
                    <input type="file" id="file_excel" name="file_excel" multiple="" class="form-control">

                    <p class="help-block">gunakan file dengan format .xls</p>
                </div>
                <div class="form-group">
                    <label>Download Contoh format file</label><br>
                    <a href="<?php echo base_url();?>data/format_data_skp.xls" target="_blank" class="btn btn-primary">Download</a>
                </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" id="btnImportFile">Simpan</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
  
<?php
$this->load->view('master_skpJs');
$this->load->view('footer');
?>