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
                                <th> A	</th>
                                <th> TL </th>
                                <th> Pc </th>
                                <th> I</th>
                                <th> S</th>
                                <th> CAP</th>
                                <th> ISH </th>
                                <th> Iso</th>
                                <th> RC19</th>
                                <th> CT</th>
                                <th> SSD</th>
                                <th> CSLN</th>
                                <th> CB</th>
                                <th> DLAR</th>
                                <th> DLAl</th>
                                <th> JTT</th>
                                <th> DLP</th>
                                <th> CS</th>
                                <th> CBAK3</th>
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
                    <label>Tanpa Alasan <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="tanpa_alasan" id="tanpa_alasan" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>Terlambat (Menit) <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="terlambat_menit" id="terlambat_menit" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>Pulang Cepat (Menit) <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="pulang_cepat_menit" id="pulang_cepat_menit" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>Izin <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="izin" id="izin" value="0">
                </div>
                <div class="form-group">
                    <label>Sakit <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="sakit" id="sakit" value="0">
                </div>
                <div class="form-group">
                    <label>cuti_alasan_penting <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="cuti_alasan_penting" id="cuti_alasan_penting" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>izin_setengah_hari <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="izin_setengah_hari" id="izin_setengah_hari" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>Isoman <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="covid" id="covid" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>ranapc19 <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="ranapc19" id="ranapc19" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>cuti_tahunan <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="cuti_tahunan" id="cuti_tahunan" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>sakit_srt_dokter <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="sakit_srt_dokter" id="sakit_srt_dokter" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>cuti_bersalin <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="cuti_bersalin" id="cuti_bersalin" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>cuti_besar <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="cuti_besar" id="cuti_besar" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>dinas_luar_akhir <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="dinas_luar_akhir" id="dinas_luar_akhir" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>dinas_luar_awal <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="dinas_luar_awal" id="dinas_luar_awal" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>tidak_terbaca <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="tidak_terbaca" id="tidak_terbaca" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>dinas_luar_penuh <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="dinas_luar_penuh" id="dinas_luar_penuh" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>cuti_sakit <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="cuti_sakit" id="cuti_sakit" value="0" onkeypress="return numbersOnly(event);">
                </div>
                <div class="form-group">
                    <label>cuti_bersalin_ak3 <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="cuti_bersalin_ak3" id="cuti_bersalin_ak3" value="0" onkeypress="return numbersOnly(event);">
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