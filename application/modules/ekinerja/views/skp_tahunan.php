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
            <small>Activitas Tahunan</small>
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
                                        <input type="text" name="filter_thn" id="filter_thn" class="form-control thnpicker" placeholder="yyyy" value="<?php echo date('Y'); ?>">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-danger" id="goFilter">Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </h3>
                      <div class="box-tools"><button type="button" class="btn btn-block btn-success" data-toggle="modal" data-target="#addSKPTahunanModal"><i class="fa fa-plus"></i> Tambah Activitas Tahunan</button></div>
                      <input type="hidden" id="<?php echo $this->security->get_csrf_token_name()?>_del" name="<?php echo $this->security->get_csrf_token_name()?>_del" value="<?php echo $this->security->get_csrf_hash()?>" />
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div id="notification_type" class="alert alert-dismissable" style="display:none;">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p id="notification_message"></p>
                        </div>
                        <table id="table_skptahunan" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width:15px;"> No </th>
                                    <th style="width:250px;"> Activitas </th>
                                    <th> Kuantitas </th>
                                    <th> Waktu Efektif </th>
                                    <th> Waktu Total </th>
                                    <th> Qty per Bulan</th>
                                    <th> Actions </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7"> No Data to Display</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" style="text-align:right">Total: &nbsp;&nbsp;</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
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
  
  <div class="modal fade" id="addSKPTahunanModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Tambah Activitas Tahunan</h4>
        </div>
        <div class="modal-body">
            <div id="alert_type" class="alert alert-dismissable" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p id="alert_message"></p>
            </div>
            <?php echo form_open('#',array('id' => 'formSKPTahunan','role'=> 'form'))?>
                <div class="form-group">
                    <label>Activitas</label>
                    <select name="skp" id="skp" class="form-control select2" style="width: 100%;">
                        <option value="" selected disabled></option>
                        <?php
                        $skp = $this->Skp_tahunan_model->list_skp();
                        foreach($skp as $list){
                            echo "<option value='".$list->kd_skp."'>".$list->skp." - ".$list->waktu." Menit</option>";
                        }
                        ?>
                    </select>
                    <input type="hidden" id="bagian_id" name="bagian_id" value="<?php echo $pegawai->bagian; ?>" readonly>
                    <input type="hidden" id="id_peg" name="id_peg" value="<?php echo $pegawai->id_pegawai; ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Kuantitas</label>
                    <input type="text" class="form-control" name="kuantitas" id="kuantitas" placeholder="Kuantitas" onkeypress="return numbersOnly(event);">
                </div>
            <?php echo form_close(); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnSaveSKPTahunan">Simpan</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="editSKPTahunanModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Update SKP Tahunan</h4>
        </div>
        <div class="modal-body">
            <div id="upd_alert_type" class="alert alert-dismissable" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p id="upd_alert_message"></p>
            </div>
            <?php echo form_open('#',array('id' => 'formSKPTahunanUpdate','role'=> 'form'))?>
                <div class="form-group">
                    <label>SKP</label>
                    <select name="upd_skp" id="upd_skp" class="form-control select2" style="width: 100%;">
                        <option value="" selected disabled></option>
                        <?php
                        $upd_skp = $this->Skp_tahunan_model->list_skp();
                        foreach($upd_skp as $list){
                            echo "<option value='".$list->kd_skp."'>".$list->skp." - ".$list->waktu." Menit</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Kuantitas</label>
                    <input type="text" class="form-control" name="upd_kuantitas" id="upd_kuantitas" placeholder="Kuantitas" onkeypress="return numbersOnly(event);">
                    <input type="hidden" name="skptahunan_id" id="skptahunan_id" readonly>
                </div>
            <?php echo form_close(); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnUpdateSKPTahunan">Simpan</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
  
  <?php
  $this->load->view('skp_tahunanJs');
  $this->load->view('footer');
  ?>