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
            <small>Validasi Kinerja</small>
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
                    </div>
                    <div class="box-header">
                      <h3 class="box-title">Validasi Aktifitas <span id="bln_label"></span></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div id="notification_type" class="alert alert-dismissable" style="display:none;">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p id="notification_message"></p>
                        </div>
                        <table id="table_validasi_aktifitas" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width:15px;"> No </th>
                                    <th> NIP </th>
                                    <th> Nama </th>
                                    <th> Jumlah Aktifitas </th>
                                    <th> Sudah Divalidasi </th>
                                    <th> Belum Divalidasi </th>
                                    <th> Ditolak </th>
                                    <th> Actions </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="8"> No Data to Display</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="box-header">
                      <h3 class="box-title">Validasi Prilaku <span id="bln_label2"></span></h3>
                    </div>
                    <div class="box-body">
                        <table id="table_validasi_prilaku" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width:15px;"> No </th>
                                    <th> NIP </th>
                                    <th> Nama </th>
                                    <th> Status Validasi </th>
                                    <th> Nilai Prilaku </th>
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
  
  <div class="modal fade" id="validasiAktifitasModal" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Validasi Aktifitas <i class="fa fa-arrow-right"></i> <span id="peg_nama"></span></h4>
            </div>
            <div class="modal-body">
                <div id="aktifitas_alert_type" class="alert alert-dismissable" style="display:none;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p id="aktifitas_alert_message"></p>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="btn-group">
                            <button class="btn btn-success" id="btnValidasiAll" onclick="validateAktifitas()" disabled>Terima
                                <i class="fa fa-check"></i>
                            </button>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-warning" id="btnTolakAll" onclick="tolakAktifitas()" disabled>Tolak
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                    </div>
                </div>
                <?php echo form_open('#',array('id' => 'fmValidasiAktifitas')); ?>
               <table class="table table-striped table-bordered table-hover table-checkable order-column" id="table_list_aktifitas">
                    <thead>
                        <tr>
                            <!--<th style="width:15px;"> No </th>-->
                            <th>
                                <input type="checkbox" name="select_all" id="select_all" data-checkbox-name="aktif">
                            </th>
                            <th> Nama Aktifitas </th>
                            <th> Uraian </th>
                            <th> Waktu </th>
                            <th> Status </th>
                            <!--<th> Aksi </th>-->
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5"> No Data to Display</td>
                        </tr>
                    </tbody>
                </table>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
  
<div class="modal fade" id="validasiPrilakuModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Validasi Prilaku <i class="fa fa-arrow-right"></i> <span id="peg_nama_pri"></span></h4>
        </div>
        <div class="modal-body">
            <div id="upd_alert_type" class="alert alert-dismissable" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p id="upd_alert_message"></p>
            </div>
            <?php echo form_open('#',array('id' => 'formValidasiPrilaku','role'=> 'form'))?>
                <div class="form-group">
                    <span style="color: red">* Required Field</span>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label">Orientasi Pelayanan <span style="color: red">*</span></label>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="or_pel" id="or_pel" onkeypress="return numbersOnly(event);"> 
                        <span>range 0 - 100 %</span>
                        <input type="hidden" name="id_pegawai_pri" id="id_pegawai_pri" value="" readonly>
                        <input type="hidden" class="form-control" name="bulan_pri" id="bulan_pri" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label">Integritas <span style="color: red">*</span></label>
                    <div class="col-md-5">  
                        <input type="text" class="form-control" name="integritas" id="integritas" onkeypress="return numbersOnly(event);">
                        <span>range 0 - 100 %</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label">Komitmen <span style="color: red">*</span></label>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="komitmen" id="komitmen" onkeypress="return numbersOnly(event);">
                        <span>range 0 - 100 %</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label">Disiplin <span style="color: red">*</span></label>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="disiplin" id="disiplin" onkeypress="return numbersOnly(event);">
                        <span>range 0 - 100 %</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label">Kerjasama <span style="color: red">*</span></label>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="kerjasama" id="kerjasama" onkeypress="return numbersOnly(event);">
                        <span>range 0 - 100 %</span>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnSimpanPrilaku">Simpan</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="updatePrilakuModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Validasi Prilaku <i class="fa fa-arrow-right"></i> <span id="upd_peg_nama_pri"></span></h4>
        </div>
        <div class="modal-body">
            <div id="pri_alert_type" class="alert alert-dismissable" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p id="pri_alert_message"></p>
            </div>
            <?php echo form_open('#',array('id' => 'formUpdatePrilaku','role'=> 'form'))?>
                <div class="form-group">
                    <span style="color: red">* Required Field</span>
                </div>
                <div class="form-group">
                    <label>Orientasi Pelayanan <span style="color: red">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="upd_or_pel" id="upd_or_pel" onkeypress="return numbersOnly(event);"> 
                        <span>range 0 - 100 %</span>
                        <input type="hidden" name="upd_id_pegawai_pri" id="upd_id_pegawai_pri" value="" readonly>
                        <input type="hidden" name="upd_id_perilaku" id="upd_id_perilaku" value="" readonly>
                        <!--<input type="hidden" class="form-control" name="bulan_pri" id="bulan_pri" readonly>-->
                    </div>
                </div>
                <div class="form-group">
                    <label>Integritas <span style="color: red">*</span></label>
                    <div class="input-group">  
                        <input type="text" class="form-control" name="upd_integritas" id="upd_integritas" onkeypress="return numbersOnly(event);">
                        <span>range 0 - 100 %</span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Komitmen <span style="color: red">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="upd_komitmen" id="upd_komitmen" onkeypress="return numbersOnly(event);">
                        <span>range 0 - 100 %</span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Disiplin <span style="color: red">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="upd_disiplin" id="upd_disiplin" onkeypress="return numbersOnly(event);">
                        <span>range 0 - 100 %</span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Kerjasama <span style="color: red">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="upd_kerjasama" id="upd_kerjasama" onkeypress="return numbersOnly(event);">
                        <span>range 0 - 100 %</span>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnUpdatePrilaku">Simpan</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php
$this->load->view('validasi_kinerjaJs');
$this->load->view('footer');
?>