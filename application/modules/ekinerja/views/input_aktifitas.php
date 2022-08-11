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
            <small>Input Aktifitas</small>
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
            <div class="col-xs-4">
                <div class="box">
                    <div class="box-header">
                      <h3 class="box-title">Kalender Aktifitas</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      <!-- THE CALENDAR -->
                      <div id="calendar"></div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
            <div class="col-xs-8">
                <div class="box">
                    <div class="box-header">
                      <h3 class="box-title">Tanggal Aktif : <span id="tgl_aktif"><?=  date('Y-m-d'); ?></span></h3>
                      <div class="box-tools" id="tools-box"><button type="button" class="btn btn-block btn-success" data-toggle="modal" data-target="#inputAktifitasModal" id="btnInputAktifitas"><i class="fa fa-plus"></i> Tambah Aktifitas</button></div>
                      <input type="hidden" id="<?php echo $this->security->get_csrf_token_name()?>_del" name="<?php echo $this->security->get_csrf_token_name()?>_del" value="<?php echo $this->security->get_csrf_hash()?>" />
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div id="notification_type" class="alert alert-dismissable" style="display:none;">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p id="notification_message"></p>
                        </div>
                        <input type="hidden" name="filter_tgl" id="filter_tgl" value="<?php echo date('Y-m-d'); ?>" readonly>
                            <table id="table_aktifitas" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th> Aktifitas </th>
                                    <th> Status </th>
                                    <th> Waktu Efektif </th>
                                    <th> Jumlah Aktifitas</th>
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
                                    <th> Aktifitas </th>
                                    <th> Status </th>
                                    <th> Waktu Efektif </th>
                                    <th> Jumlah Aktifitas</th>
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
  
  <div class="modal fade" id="inputAktifitasModal" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Input Aktifitas</h4>
            </div>
            <div class="modal-body">
                <div id="alert_type" class="alert alert-dismissable" style="display:none;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p id="alert_message"></p>
                </div>
                <?php echo form_open('#',array('id' => 'formAktifitas','role'=> 'form'))?>
                <div class="form-group">
                        <span style="color: red">* Required Field</span>
                </div>
                <div class="form-group">
                    <label>Tanggal <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="tgl_aktifitas" id="tgl_aktifitas" placeholder="dd-mm-yyyy" value="<?php echo date('d-m-Y'); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Aktifitas <span style="color: red">*</span></label>
                    <select name="skptahunan_id" id="skptahunan_id" class="form-control select2" style="width: 100%;" onchange="setWaktuEfektif()">
                        <option value="" selected disabled></option>
                        <?php
                        $skp_tahunan = $this->Input_aktifitas_model->list_skptahunan($pegawai->nip);
                        foreach($skp_tahunan as $list){
                            echo "<option value='".$list->skptahunan_id."'>".$list->skp."</option>";
                        }
                        ?>
                    </select>
                    <input type="hidden" id="id_pegawai" name="id_pegawai" value="<?php echo $pegawai->id_pegawai; ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Waktu Efektif <span style="color: red">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="waktu_efektif" id="waktu_efektif" placeholder="-" readonly>
                        <span class="input-group-addon"> Menit </span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Jam Mulai <span style="color: red">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control jampicker" name="jam_mulai" id="jam_mulai" placeholder="hh:mm" onchange="setVolume()" readonly>
                        <!--<input type="text" class="form-control jampicker" name="jam_mulai" id="jam_mulai" placeholder="hh:mm" readonly>-->
                        <span class="input-group-addon"> Sampai </span>
                        <input type="text" class="form-control jampicker" name="jam_selesai" id="jam_selesai" placeholder="hh:mm" onchange="setVolume()" readonly>
                        <!--<input type="text" class="form-control jampicker" name="jam_selesai" id="jam_selesai" placeholder="hh:mm" readonly>-->
                    </div>
                </div>
                <div class="form-group">
                    <label>Volume <span style="color: red">*</span></label>
                    <select name="jumlah" id="jumlah" class="form-control">
                        <option value="" selected disabled>-- Volume --</option>
                    </select>
                    <!--<input type="text" class="form-control" name="jumlah" id="jumlah" placeholder="Input Jumlah Aktifitas" onkeypress="return numbersOnly(event);">-->
                </div>
                <div class="form-group">
                    <label>Uraian</label>
                    <textarea class="form-control" id="uraian" name="uraian"></textarea>
                </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSaveAktifitas">Simpan</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="editAktifitasModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Update Aktifitas</h4>
        </div>
        <div class="modal-body">
            <div id="upd_alert_type" class="alert alert-dismissable" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p id="upd_alert_message"></p>
            </div>
            <?php echo form_open('#',array('id' => 'formEditAktifitas','role'=> 'form'))?>
                <div class="form-group">
                    <span style="color: red">* Required Field</span>
                </div>
                <div class="form-group">
                    <label>Tanggal <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="upd_tgl_aktifitas" id="upd_tgl_aktifitas" placeholder="dd-mm-yyyy" readonly>
                </div>
                <div class="form-group">
                    <label>Aktifitas <span style="color: red">*</span></label>
                    <select name="upd_skptahunan_id" id="upd_skptahunan_id" class="form-control select2" style="width: 100%;" onchange="setWaktuEfektifUpd()">
                        <option value="" selected disabled></option>
                        <?php
                        $upd_skp_tahunan = $this->Input_aktifitas_model->list_skptahunan($pegawai->nip);
                        foreach($upd_skp_tahunan as $list){
                            echo "<option value='".$list->skptahunan_id."'>".$list->skp."</option>";
                        }
                        ?>
                    </select>
                    <input type="hidden" id="upd_aktifitas_id" name="upd_aktifitas_id" readonly>
                </div>
                <div class="form-group">
                    <label>Waktu Efektif <span style="color: red">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="upd_waktu_efektif" id="upd_waktu_efektif" placeholder="-" readonly>
                        <span class="input-group-addon">Menit</span>
                    </div>
                    
                </div>
                <div class="form-group">
                    <label>Jam Mulai <span style="color: red">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control jampicker" name="upd_jam_mulai" id="upd_jam_mulai" placeholder="hh:mm" onchange="setVolumeUpd()" readonly>
                        <!--<input type="text" class="form-control jampicker" name="upd_jam_mulai" id="upd_jam_mulai" placeholder="hh:mm" readonly>-->
                        <span class="input-group-addon"> Sampai </span>
                        <input type="text" class="form-control jampicker" name="upd_jam_selesai" id="upd_jam_selesai" placeholder="hh:mm" onchange="setVolumeUpd()" readonly>
                        <!--<input type="text" class="form-control jampicker" name="upd_jam_selesai" id="upd_jam_selesai" placeholder="hh:mm" readonly>-->
                    </div>
                </div>
                <div class="form-group">
                    <label>Volume <span style="color: red">*</span></label>
                    <select name="upd_jumlah" id="upd_jumlah" class="form-control">
                        <option value="" selected disabled>-- Volume --</option>
                    </select>
                    <!--<input type="text" class="form-control" name="upd_jumlah" id="upd_jumlah" placeholder="Input Jumlah Aktifitas" onkeypress="return numbersOnly(event);">-->
                </div>
                <div class="form-group">
                    <label>Uraian</label>
                    <textarea class="form-control" id="upd_uraian" name="upd_uraian"></textarea>
                </div>
            <?php echo form_close(); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnUpdateAktifitas">Simpan</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
  
  <?php
  $this->load->view('input_aktifitasJs');
  $this->load->view('footer');
  ?>