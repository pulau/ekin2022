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
            Kepegawaian
            <small>Bank Data Pegawai</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url('co_panel'); ?>"><i class="fa fa-dashboard"></i> CPanel</a></li>
            <li>Kepegawaian</li><li><?php echo $bc_parent; ?></li><li class="active"><?php echo $bc_child; ?></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                      <h3 class="box-title">Bank Data Pegawai</h3>
                      <div class="box-tools">
                          <button type="button" class="btn btn-success" id="addPegawai" data-target="#addPegawaiModal" data-toggle="modal">Tambah Pegawai</button>
                          <button type="button" class="btn btn-warning" id="btnImportData" data-target="#importDataModal" data-toggle="modal">Import Data Pegawai</button>
                      </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div id="notification_type" class="alert alert-dismissable" style="display:none;">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p id="notification_message"></p>
                        </div>
                        <input type="hidden" id="<?php echo $this->security->get_csrf_token_name()?>_del" name="<?php echo $this->security->get_csrf_token_name()?>_del" value="<?php echo $this->security->get_csrf_hash()?>" />
                        <table id="table_pegawai" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th style="width: 15px;">No</th>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Tempat Tugas</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5"> No Data to Display</td>
                                </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th style="width: 15px;">No</th>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Tempat Tugas</th>
                                <th>Actions</th>
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
              <h4 class="modal-title">Import Data Pegawai dari File .xls</h4>
            </div>
            <div class="modal-body">
                <div id="alert_type_import" class="alert alert-dismissable" style="display:none;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p id="alert_message_import"></p>
                </div>
                <?php echo form_open('#',array('id' => 'formImport','role'=> 'form', 'enctype' => 'multipart/form-data')) ?>
                <div class="form-group">
                    <label>Upload File Data Pegawai</label>
                    <input type="file" id="file_excel" name="file_excel" multiple="" class="form-control">

                    <p class="help-block">gunakan file dengan format .xls</p>
                </div>
                <div class="form-group">
                    <label>Download Contoh format file</label><br>
                    <a href="<?php echo base_url();?>data/format_data_pegawai.xls" target="_blank" class="btn btn-primary">Download</a>
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

<div class="modal fade" id="addPegawaiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Tambah Data Pegawai</h4>
            </div>
            <div class="modal-body">
                <div id="alert_type" class="alert alert-dismissable" style="display:none;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p id="alert_message"></p>
                </div>
                <div class="row">
                <?php echo form_open('#',array('id' => 'formPegawai','role'=> 'form', 'enctype' => 'multipart/form-data')) ?>
                <div class="col-lg-12">
                    <span style="color: red">* Required Field</span>
                </div>
                <div class="col-lg-12">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="control-label">NIP <span style="color: red">*</span></label>
                            <input type="text" id="nip" name="nip" class="form-control" placeholder="Masukan NIP">
                        </div>
                        <div class="form-group">
                            <label class="control-label">NRK </label>
                            <input type="text" id="nrk" name="nrk" class="form-control" placeholder="Masukan NRK">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Nama Lengkap (Gelar Depan dan Gelar Belakang) <span style="color: red">*</span></label>
                            <div class="row">
                                <div class="col-xs-3">
                                    <input type="text" class="form-control" name="gelar_depan" id="gelar_depan" placeholder="GD">
                                </div>
                                <div class="col-xs-6">
                                    <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" placeholder="Masukan Nama Pegawai">
                                </div>
                                <div class="col-xs-3">
                                    <input type="text" class="form-control" name="gelar_belakang" id="gelar_belakang" placeholder="GB">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Agama </label>
                            <select name="agama" id="agama" class="form-control">
                                <option value="" selected disabled> -- PILIH Agama -- </option>
                                <?php
                                $list_agama = $this->Pegawai_model->list_agama();
                                foreach($list_agama as $agama){
                                    echo "<option value='".$agama->id_agama."'>".$agama->nama_agama."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Tempat Lahir </label>
                            <input type="text" class="form-control" name="tempat_lahir" id="tempat_lahir" placeholder="Masukan termpat lahir">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Tanggal Lahir </label>
                            <input type="text" class="form-control tgl-lahir" name="tgl_lahir" id="tgl_lahir" placeholder="dd-mm-yyyy" readonly>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Jenis Kelamin <span style="color: red">*</span></label>
                            <div class="radio">
                                <label> 
                                    <input value="LAKI-LAKI" name="jenis_kelamin" id="jk1" type="radio">
                                    Laki-laki
                                </label>
                                <label>
                                    <input value="PEREMPUAN" name="jenis_kelamin" id="jk2" type="radio">
                                    Perempuan
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">No. KTP </label>
                            <input type="text" class="form-control" name="no_ktp" id="no_ktp" placeholder="Masukan No. KTP">
                        </div>
                        <div class="form-group">
                            <label class="control-label">No. NPWP </label>
                            <input type="text" class="form-control" name="no_npwp" id="no_npwp" placeholder="Masukan No. NPWP">
                        </div>
                        <div class="form-group">
                            <label class="control-label">No. Rek. DKI </label>
                            <input type="text" class="form-control" name="no_rek" id="no_rek" placeholder="Masukan No. Rek DKI">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Alamat </label>
                            <textarea class="form-control" id="alamat" name="alamat"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="control-label">No. Telepon / HP </label>
                            <input type="text" class="form-control" name="no_tlp" id="no_tlp" placeholder="Masukan No. Telepon">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Pangkat/Golongan</label>
                            <div class="row">
                                <div class="col-xs-6">
                                    <select name="pangkat" id="pangkat" class="form-control">
                                        <option value="" selected> -- Pilih Pangkat -- </option>
                                        <option value="I">I</option>
                                        <option value="II">II</option>
                                        <option value="III">III</option>
                                        <option value="IV">IV</option>
                                    </select>
                                </div>
                                <div class="col-xs-6">
                                    <select name="golongan" id="golongan" class="form-control">
                                        <option value="" selected> -- Pilih Golongan -- </option>
                                        <option value="a">a</option>
                                        <option value="b">b</option>
                                        <option value="c">c</option>
                                        <option value="d">d</option>
                                        <option value="e">e</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
<!--                    <div class="col-lg-1">
                        &nbsp;
                    </div>-->
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="control-label">Tanggal Masuk </label>
                            <input type="text" id="tgl_masuk" name="tgl_masuk" class="form-control tgl-lahir" placeholder="dd-mm-yyyy" readonly>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Status Pegawai <span style="color: red">*</span></label>
                            <div class="radio">
                                <label> 
                                    <input value="NON PNS" name="status_pns" id="status_pns1" type="radio">
                                    NON PNS
                                </label>
                                <label> 
                                    <input value="PNS" name="status_pns" id="status_pns2" type="radio">
                                    PNS
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Status Pernikahan </label>
                            <select name="status_pernikahan" id="status_pernikahan" class="form-control">
                                <option value="" selected disabled> -- PILIH Status -- </option>
                                <?php
                                $list_status = $this->Pegawai_model->list_statuspegawai();
                                foreach($list_status as $statuspegawai){
                                    echo "<option value='".$statuspegawai->statuspegawai_id."'>".$statuspegawai->statuspegawai_nama."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Pendidikan Terakhir </label>
                            <select name="pendidikan" id="pendidikan" class="form-control">
                                <option value="" selected disabled> -- PILIH Status -- </option>
                                <?php
                                $list_pendidikan = $this->Pegawai_model->list_pendidikan();
                                foreach($list_pendidikan as $pendidikan){
                                    echo "<option value='".$pendidikan->pendidikan_id."'>".$pendidikan->pendidikan_nama."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class=" control-label">Rumpun Jabatan </label>
                            <select name="rumpun" id="rumpun" class="form-control">
                                <option value="" selected disabled> -- Pilih Rumpun -- </option>
                                <?php
                                $list_rumpun = $this->Pegawai_model->list_rumpun();
                                foreach($list_rumpun as $rumpun){
                                    echo "<option value='".$rumpun->rumpun_id."'>".$rumpun->rumpun_nama."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Bagian </label>
                            <select name="bagian" id="bagian" class="form-control">
                                <option value="" selected disabled> -- Pilih Bagian -- </option>
                                <?php
                                $list_bagian = $this->Pegawai_model->list_bagian();
                                foreach($list_bagian as $bagian){
                                    echo "<option value='".$bagian->bagian_id."'>".$bagian->bagian_nama."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Jabatan </label>
                            <select name="jabatan" id="jabatan" class="form-control">
                                <option value="" selected disabled> -- Pilih Jabatan -- </option>
                                <?php
                                $list_jabatan = $this->Pegawai_model->list_jabatan();
                                foreach($list_jabatan as $jabatan){
                                    echo "<option value='".$jabatan->id_jabatan."'>".$jabatan->nama_jabatan."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">BPJS Kesehatan </label>
                            <div class="checkbox">
                                <label> 
                                    <input value="0.02" name="bpjsks" type="checkbox">
                                    BPJS Kesehatan
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">BPJS Ketenagakerjaan </label>
                            <div class="checkbox">
                                <label>
                                    <input value="0.0054" name="bpjsjkk" type="checkbox">
                                     JKK & JKM
                                </label>
                                <label>
                                    <input value="0.057" name="bpjsijht" type="checkbox">
                                    IJHT
                                </label>
                                <label> 
                                    <input value="0.03" name="bpjsjp" type="checkbox">
                                    JP
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Status Pajak </label>
                            <select name="pajak" id="pajak" class="form-control">
                                <option value="" selected disabled> -- Pilih Pajak -- </option>
                                <?php
                                $list_pajak = $this->Pegawai_model->list_pajak();
                                foreach($list_pajak as $pajak){
                                    echo "<option value='".$pajak->pajak_id."'>".$pajak->pajak_nama."</option>";
                                }
                                ?>
                            </select>
                        </div>
<!--                        <div class="form-group">
                            <label class="control-label">PJ Cuti </label>
                            <select name="pj_cuti" id="pj_cuti" class="form-control">
                                <option value="" selected disabled> -- Pilih PJ Cuti -- </option>
                                <?php
//                                $list_kasie = $this->Pegawai_model->list_pjcuti();
//                                foreach($list_kasie as $kasie){
//                                    echo "<option value='".$kasie->id_pegawai."'>".$kasie->nama_pegawai."</option>";
//                                }
                                ?>
                            </select>
                        </div>-->
                        <div class="form-group">
                            <label class="control-label">Tempat Tugas</label>
                            <select name="tempattugas" id="tempattugas" class="form-control" onchange="pilihTmptugas()">
                                <option value="" selected disabled> -- Pilih Tempat Tugas -- </option>
                                <?php
                                $list_tmptugas = $this->Pegawai_model->list_tempattugas();
                                foreach($list_tmptugas as $tmptugas){
                                    echo "<option value='".$tmptugas->tempattugas_id."'>".$tmptugas->tempattugas_nama."</option>";
                                }
                                ?>
                            </select>
                            <input type="hidden" id="tempattugas_ket" name="tempattugas_ket" readonly> 
                        </div>
                        <div class="form-group">
                            <label class="control-label">Foto </label>
                            <input type="file" id="foto_file" name="foto_file" multiple="">
                            <p class="help-block"> Upload file berekstensi .jpeg, .png, .gif </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <hr>
                </div>
                <div class="col-lg-12">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="control-label">Email</label>
                            <input type="text" class="form-control" name="email" id="email" placeholder="email format (example@mail.com)">
                        </div>
                    </div>
                    <div class="col-lg-1">
                        &nbsp;
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="control-label">Password <span style="color: red">*</span></label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Konfirmasi Password <span style="color: red">*</span></label>
                            <input type="password" class="form-control" name="conf_pass" id="conf_pass" placeholder="Konfirmasi Password">
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" id="btnSavePegawai">Simpan</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="updatePegawaiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Update Data Pegawai</h4>
            </div>
            <div class="modal-body">
                <div id="upd_alert_type" class="alert alert-dismissable" style="display:none;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p id="upd_alert_message"></p>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#biodata" data-toggle="tab">Biodata Pegawai</a></li>
                                <li><a href="#change_password" data-toggle="tab">Ubah Password</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="biodata">
                                    <div class="row">
                                    <?php echo form_open('#',array('id' => 'formBioPegawai','role'=> 'form'))?>
                                        <div class="col-lg-12">
                                            <span style="color: red">* Required Field</span>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>NIP <span style="color: red">*</span></label>
                                                    <input type="text" id="upd_nip" name="upd_nip" class="form-control" placeholder="Masukan NIP">
                                                    <input type="hidden" id="upd_pegawai_id" name="upd_pegawai_id" value="" readonly>
                                                    <input type="hidden" id="upd_nip_old" name="upd_nip_old" value="" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label>NRK <span style="color: red">*</span></label>
                                                    <input type="text" id="upd_nrk" name="upd_nrk" class="form-control" placeholder="Masukan NRK">
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Nama Lengkap (Gelar Depan dan Gelar Belakang) <span style="color: red">*</span></label>
                                                    <div class="row">
                                                        <div class="col-xs-3">
                                                            <input type="text" class="form-control" name="upd_gelar_depan" id="upd_gelar_depan" placeholder="GD">
                                                        </div>
                                                        <div class="col-xs-6">
                                                            <input type="text" class="form-control" name="upd_nama_lengkap" id="upd_nama_lengkap" placeholder="Masukan Nama Pegawai">
                                                        </div>
                                                        <div class="col-xs-3">
                                                            <input type="text" class="form-control" name="upd_gelar_belakang" id="upd_gelar_belakang" placeholder="GB">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Agama </label>
                                                    <select name="upd_agama" id="upd_agama" class="form-control">
                                                        <option value="" selected disabled> -- PILIH Agama -- </option>
                                                        <?php
                                                        $list_agama = $this->Pegawai_model->list_agama();
                                                        foreach($list_agama as $agama){
                                                            echo "<option value='".$agama->id_agama."'>".$agama->nama_agama."</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Tempat Lahir </label>
                                                    <input type="text" class="form-control" name="upd_tempat_lahir" id="upd_tempat_lahir" placeholder="Masukan termpat lahir">
                                                </div>
                                                <div class="form-group">
                                                    <label>Tanggal Lahir </label>
                                                    <input type="text" class="form-control tgl-lahir" name="upd_tgl_lahir" id="upd_tgl_lahir" placeholder="dd-mm-yyyy" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label>Jenis Kelamin <span style="color: red">*</span></label>
                                                    <div class="radio">
                                                        <label> 
                                                            <input value="LAKI-LAKI" name="upd_jenis_kelamin" id="upd_jk1" type="radio">
                                                            Laki-laki
                                                        </label>
                                                        <label>
                                                            <input value="PEREMPUAN" name="upd_jenis_kelamin" id="upd_jk2" type="radio">
                                                            Perempuan
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">No. KTP </label>
                                                    <input type="text" class="form-control" name="upd_no_ktp" id="upd_no_ktp" placeholder="Masukan No. KTP">
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">No. NPWP </label>
                                                    <input type="text" class="form-control" name="upd_no_npwp" id="upd_no_npwp" placeholder="Masukan No. NPWP">
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">No. Rek. DKI </label>
                                                    <input type="text" class="form-control" name="upd_no_rek" id="upd_no_rek" placeholder="Masukan No. Rek DKI">
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Alamat </label>
                                                    <textarea class="form-control" id="upd_alamat" name="upd_alamat"></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">No. Telepon / HP </label>
                                                    <input type="text" class="form-control" name="upd_no_tlp" id="upd_no_tlp" placeholder="Masukan No. Telepon">
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Pangkat/Golongan</label>
                                                    <div class="row">
                                                        <div class="col-xs-6">
                                                            <select name="upd_pangkat" id="upd_pangkat" class="form-control">
                                                                <option value="" selected> -- Pilih Pangkat -- </option>
                                                                <option value="I">I</option>
                                                                <option value="II">II</option>
                                                                <option value="III">III</option>
                                                                <option value="IV">IV</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-xs-6">
                                                            <select name="upd_golongan" id="upd_golongan" class="form-control">
                                                                <option value="" selected> -- Pilih Golongan -- </option>
                                                                <option value="a">a</option>
                                                                <option value="b">b</option>
                                                                <option value="c">c</option>
                                                                <option value="d">d</option>
                                                                <option value="e">e</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
<!--                                            <div class="col-lg-1">
                                                &nbsp;
                                            </div>-->
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="control-label">Tanggal Masuk </label>
                                                    <input type="text" id="upd_tgl_masuk" name="upd_tgl_masuk" class="form-control tgl-lahir" placeholder="dd-mm-yyyy" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Status Pegawai <span style="color: red">*</span></label>
                                                    <div class="radio">
                                                        <label>
                                                            <input value="NON PNS" name="upd_status_pns" id="upd_status_pns1" type="radio">
                                                            NON PNS
                                                        </label>
                                                        <label>
                                                            <input value="PNS" name="upd_status_pns" id="upd_status_pns2" type="radio">
                                                            PNS
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Status Pernikahan </label>
                                                    <select name="upd_status_pernikahan" id="upd_status_pernikahan" class="form-control">
                                                        <option value="" selected disabled> -- PILIH Status -- </option>
                                                        <?php
                                                        $list_status = $this->Pegawai_model->list_statuspegawai();
                                                        foreach($list_status as $statuspegawai){
                                                            echo "<option value='".$statuspegawai->statuspegawai_id."'>".$statuspegawai->statuspegawai_nama."</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Pendidikan Terakhir </label>
                                                    <select name="upd_pendidikan" id="upd_pendidikan" class="form-control">
                                                        <option value="" selected disabled> -- PILIH Status -- </option>
                                                        <?php
                                                        $list_pendidikan = $this->Pegawai_model->list_pendidikan();
                                                        foreach($list_pendidikan as $pendidikan){
                                                            echo "<option value='".$pendidikan->pendidikan_id."'>".$pendidikan->pendidikan_nama."</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class=" control-label">Rumpun Jabatan </label>
                                                    <select name="upd_rumpun" id="upd_rumpun" class="form-control">
                                                        <option value="" selected disabled> -- Pilih Rumpun -- </option>
                                                        <?php
                                                        $list_rumpun = $this->Pegawai_model->list_rumpun();
                                                        foreach($list_rumpun as $rumpun){
                                                            echo "<option value='".$rumpun->rumpun_id."'>".$rumpun->rumpun_nama."</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Bagian </label>
                                                    <select name="upd_bagian" id="upd_bagian" class="form-control">
                                                        <option value="" selected disabled> -- Pilih Bagian -- </option>
                                                        <?php
                                                        $list_bagian = $this->Pegawai_model->list_bagian();
                                                        foreach($list_bagian as $bagian){
                                                            echo "<option value='".$bagian->bagian_id."'>".$bagian->bagian_nama."</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Jabatan </label>
                                                    <select name="upd_jabatan" id="upd_jabatan" class="form-control">
                                                        <option value="" selected disabled> -- Pilih Jabatan -- </option>
                                                        <?php
                                                        $list_jabatan = $this->Pegawai_model->list_jabatan();
                                                        foreach($list_jabatan as $jabatan){
                                                            echo "<option value='".$jabatan->id_jabatan."'>".$jabatan->nama_jabatan."</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">BPJS Kesehatan </label>
                                                    <div class="checkbox">
                                                        <label> 
                                                            <input value="0.02" name="upd_bpjsks" id="upd_bpjsks" type="checkbox">
                                                            BPJS Kesehatan
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">BPJS Ketenagakerjaan </label>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input value="0.0054" name="upd_bpjsjkk" id="upd_bpjsjkk" type="checkbox">
                                                            JKK & JKM
                                                        </label>
                                                        <label>
                                                            <input value="0.057" name="upd_bpjsijht" id="upd_bpjsijht" type="checkbox">
                                                            IJHT
                                                        </label>
                                                        <label>
                                                            <input value="0.03" name="upd_bpjsjp" id="upd_bpjsjp" type="checkbox">
                                                            JP
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Status Pajak </label>
                                                    <select name="upd_pajak" id="upd_pajak" class="form-control">
                                                        <option value="" selected disabled> -- Pilih Pajak -- </option>
                                                        <?php
                                                        $list_pajak = $this->Pegawai_model->list_pajak();
                                                        foreach($list_pajak as $pajak){
                                                            echo "<option value='".$pajak->pajak_id."'>".$pajak->pajak_nama."</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
<!--                                                <div class="form-group">
                                                    <label class="control-label">PJ Cuti </label>
                                                    <select name="upd_pj_cuti" id="upd_pj_cuti" class="form-control">
                                                        <option value="" selected disabled> -- Pilih PJ Cuti -- </option>
                                                        <?php
//                                                        $list_kasie = $this->Pegawai_model->list_pjcuti();
//                                                        foreach($list_kasie as $kasie){
//                                                            echo "<option value='".$kasie->id_pegawai."'>".$kasie->nama_pegawai."</option>";
//                                                        }
                                                        ?>
                                                    </select>
                                                </div>-->
                                                <div class="form-group">
                                                    <label class="control-label">Tempat Tugas</label>
                                                    <select name="upd_tempattugas" id="upd_tempattugas" class="form-control" onchange="pilihTmptugasUpdate()">
                                                        <option value="" selected disabled> -- Pilih Tempat Tugas -- </option>
                                                        <?php
                                                        $list_tmptugas = $this->Pegawai_model->list_tempattugas();
                                                        foreach($list_tmptugas as $tmptugas){
                                                            echo "<option value='".$tmptugas->tempattugas_id."'>".$tmptugas->tempattugas_nama."</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                    <input type="hidden" id="upd_tempattugas_ket" name="upd_tempattugas_ket" readonly> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="margin-top-10">
                                            <button type="button" type="button" class="btn btn-primary" id="btnUpdateBiodata"> Submit </button>
                                        </div>
                                    <?php echo form_close()?>
                                    </div>
                                </div>
                                <div class="tab-pane" id="change_password">
                                    <div class="row">
                                    <?php echo form_open('#',array('id' => 'formGantiPassword','role'=> 'form'))?>
                                        <div class="col-lg-12">
                                            <span style="color: red">* Required Field</span>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="col-lg-5">
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="text" class="form-control" name="upd_email" id="upd_email" placeholder="email format (example@mail.com)">
                                                    <input type="hidden" id="upd_idpeg" name="upd_idpeg" readonly>
                                                    <input type="hidden" id="upd_nippeg" name="upd_nippeg" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label>Username <span style="color: red">*</span></label>
                                                    <input type="text" class="form-control" name="upd_username" id="upd_username" placeholder="Username">
                                                </div>
                                            </div>
                                            <div class="col-lg-1">
                                                &nbsp;
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Password Baru</label>
                                                    <input type="password" class="form-control" name="upd_password" id="upd_password" placeholder="Password">
                                                </div>
                                                <div class="form-group">
                                                    <label>Konfirmasi Password Baru</label>
                                                    <input type="password" class="form-control" name="upd_conf_pass" id="upd_conf_pass" placeholder="Konfirmasi Password">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="margin-top-10">
                                            <button type="button" class="btn btn-primary" id="btnGantiPassword"> Submit </button>
                                        </div>
                                    <?php echo form_close()?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

  <?php
  $this->load->view('pegawaiJs');
  $this->load->view('footer');
  ?>