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
            <small>Absensi</small>
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
                          <button type="button" class="btn btn-primary" id="btnInputAbsensi" data-target="#modalInputAbsensi" data-toggle="modal"><i class="fa fa-plus"></i> Input Absensi </button>
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
                        <table id="table_kehadiran" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 15px;"> No </th>
                                <th> NIP </th>
                                <th> Nama </th>
                                <th> Izin </th>
                                <th> Sakit </th>
                                <th> Cuti Sakit </th>
                                <th> Cuti/Bersalin </th>
                                <th> Positif Covid-19 </th>
                                <th> Dinas Luar </th>
                                <th> Tanpa Alasan</th>
                                <th> Pulang Cepat (Menit)</th>
                                <th> Terlambat (Menit)</th>
                                <th> Aksi </th>
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
<!-- /.modal -->
  
  <div class="modal fade" id="modalInputAbsensi" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Input Absensi</h4>
        </div>
        <div class="modal-body">
            <div id="alert_type" class="alert alert-dismissable" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p id="alert_message"></p>
            </div>
            <?php echo form_open('#',array('id' => 'formInputKehadiran','role'=> 'form'))?>
                <div class="form-group">
                    <label>Bulan</label>
                    <input type="text" id="bulan" name="bulan" class="form-control blnpicker" placeholder="M Y" readonly>
                </div>
                <div class="form-group">
                    <label>Upload File Absensi</label>
                    <input type="file" id="file_excel" name="file_excel" multiple="" class="form-control">

                    <p class="help-block">gunakan file dengan format .xls</p>
                </div>
                <div class="form-group">
                    <label>Download Contoh format file</label><br>
                    <a href="<?php echo base_url();?>data/format_absensi.xls" target="_blank" class="btn btn-primary">Download</a>
                </div>
            <?php echo form_close(); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnSimpanHadir">Simpan</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="modalEditAbsensi" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Edit Absensi</h4>
        </div>
        <div class="modal-body">
            <div id="alert_type2" class="alert alert-dismissable" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p id="alert_message2"></p>
            </div>
            <?php echo form_open('#',array('id' => 'formEditAbsensi','role'=> 'form'))?>
                <div class="form-group">
                    <label>Bulan</label>
                    <input type="text" id="bulan_upd" name="bulan_upd" class="form-control" placeholder="M Y" readonly>
                    <input type="hidden" name="id_waktukurang" id="id_waktukurang" value="" readonly>
                </div>
                <div class="form-group">
                    <label>NIP <span style="color: red">*</span></label>
                    <input type="text" id="nip" name="nip" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Nama Pegawai <span style="color: red">*</span></label>
                    <input type="text" id="nama_pegawai" name="nama_pegawai" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Izin <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="izin" id="izin" value="0">
                </div>
                <div class="form-group">
                    <label>Sakit <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="sakit" id="sakit" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>Cuti Sakit <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="cutisakit" id="cutisakit" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>Cuti / Bersalin <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="cuti_or_bersalin" value="0" id="cuti_or_bersalin" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>Positif Covid-19 <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="positif_covid" value="0" id="positif_covid" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>Dinas Luar <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="dinas_luar" id="dinas_luar" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>Tanpa Alasan <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="tanpa_alasan" id="tanpa_alasan" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>Terlambat (Menit) <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="terlambat" id="terlambat" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>Pulang Cepat (Menit) <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="pulang_cepat" id="pulang_cepat" value="0" onkeypress="return numbersOnly(event);">
                </div>
            <?php echo form_close(); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnUpdateAbsen">Simpan</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
  
  <?php
  $this->load->view('absensiJs');
  $this->load->view('footer');
  ?>