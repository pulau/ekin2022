<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php $this->load->view('header_cpanel'); ?>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        App Store PKC Seribu Utara
        <small>Profil Pegawai</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo site_url('co_panel'); ?>"><i class="fa fa-dashboard"></i> CPanel</a></li>
            <li class="active">Profil Pegawai</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="row">
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              
              <?php
                if(isset($pegawai->foto_url)){
                ?>
                <img class="profile-user-img img-responsive img-circle" src="<?php echo base_url().$pegawai->foto_url;?>" alt="User profile picture">
                <?php
                }else{
                ?>
                <img class="profile-user-img img-responsive img-circle" src="<?php echo base_url();?>dist/img/avatar04.png" alt="User profile picture">
                <?php
                }
                ?>
              <h3 class="profile-username text-center"><?php echo $gd.$pegawai->nama_pegawai.$gb; ?></h3>
              <?php $nrk = !empty($pegawai->nrk) ? " / ".$pegawai->nrk:""; ?>
              <p class="text-muted text-center"><?php echo $pegawai->nip.$nrk; ?></p>
              <p class="text-muted text-center"><?php echo isset($pegawai->rumpun_nama) ? $pegawai->rumpun_nama :"-"; ?></p>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          <!-- About Me Box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Tentang Saya</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <strong><i class="fa fa-book margin-r-5"></i> Pendidikan</strong>

              <p class="text-muted">
                
              </p>

              <hr>

              <strong><i class="fa fa-map-marker margin-r-5"></i> Pelatihan</strong>

              <p class="text-muted"></p>

              <hr>

              <strong><i class="fa fa-pencil margin-r-5"></i> STR / SIP</strong>

              <p>
                <span class="label label-danger">UI Design</span>
                <span class="label label-success">Coding</span>
                <span class="label label-info">Javascript</span>
                <span class="label label-warning">PHP</span>
                <span class="label label-primary">Node.js</span>
              </p>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#biodata" data-toggle="tab">Biodata</a></li>
              <li><a href="#ubahpasswd" data-toggle="tab">Ubah Password</a></li>
              <li><a href="#pendidikan" data-toggle="tab" id="pendidikanTab">Pendidikan dan Pelatihan</a></li>
              <li><a href="#dokumen" data-toggle="tab">Dokumen Lainnya</a></li>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane" id="biodata">
                    <div id="notifbio_type" class="alert alert-dismissable" style="display:none;">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <p id="notifbio_message"></p>
                    </div>
                    <div class="row">
                        <!--form biodata-->
                        <?php echo form_open('#',array('id' => 'formPegawai','role'=> 'form', 'enctype' => 'multipart/form-data')) ?>
                        <div class="col-lg-12">
                            <span style="color: red">* Wajib diisi</span>
                        </div>
                        <div class="col-lg-12">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">Nama Lengkap (Gelar Depan dan Gelar Belakang) <span style="color: red">*</span></label>
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <input type="text" class="form-control" name="gelar_depan" id="gelar_depan" value="<?php echo $pegawai->gelar_depan ?>">
                                        </div>
                                        <div class="col-xs-6">
                                            <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" value="<?php echo $pegawai->nama_pegawai ?>" placeholder="Masukan Nama Pegawai">
                                        </div>
                                        <div class="col-xs-3">
                                            <input type="text" class="form-control" name="gelar_belakang" id="gelar_belakang" value="<?php echo $pegawai->gelar_belakang ?>" placeholder="GB">
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
                                            if($agama->id_agama == $pegawai->agama){
                                                echo "<option value='".$agama->id_agama."' selected>".$agama->nama_agama."</option>";
                                            }else{
                                                echo "<option value='".$agama->id_agama."'>".$agama->nama_agama."</option>";
                                            }                                 
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Tempat Lahir </label>
                                    <input type="text" class="form-control" name="tempat_lahir" id="tempat_lahir" value="<?php echo $pegawai->tempat_lahir; ?>" placeholder="Masukan termpat lahir">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Tanggal Lahir </label>
                                    <input type="text" class="form-control tgl-lahir" name="tgl_lahir" id="tgl_lahir" value="<?php echo date('d-M-Y',strtotime($pegawai->tgl_lahir)); ?>" placeholder="dd-mm-yyyy" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Jenis Kelamin <span style="color: red">*</span></label>
                                    <div class="radio">
                                        <label> 
                                            <?php if($pegawai->jenis_kelamin == "LAKI-LAKI"){ ?>
                                                <input value="LAKI-LAKI" name="jenis_kelamin" id="jk1" type="radio" checked>
                                            <?php }else{?>
                                                <input value="LAKI-LAKI" name="jenis_kelamin" id="jk1" type="radio">
                                            <?php }?>
                                            Laki-laki
                                        </label>
                                        <label>
                                            <?php if($pegawai->jenis_kelamin == "PEREMPUAN"){ ?>
                                                <input value="PEREMPUAN" name="jenis_kelamin" id="jk2" type="radio" checked>
                                            <?php }else{?>
                                                <input value="PEREMPUAN" name="jenis_kelamin" id="jk2" type="radio">
                                            <?php }?>
                                            
                                            Perempuan
                                        </label>
                                    </div>
                                </div>
                            </div>
        <!--                    <div class="col-lg-1">
                                &nbsp;
                            </div>-->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">No. KTP </label>
                                    <input type="text" class="form-control" name="no_ktp" id="no_ktp" value="<?php echo $pegawai->no_ktp ?>" placeholder="Masukan No. KTP">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">No. NPWP </label>
                                    <input type="text" class="form-control" name="no_npwp" id="no_npwp" value="<?php echo $pegawai->npwp ?>" placeholder="Masukan No. NPWP">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">No. Rek. DKI </label>
                                    <input type="text" class="form-control" name="no_rek" id="no_rek" value="<?php echo $pegawai->norek_dki ?>" placeholder="Masukan No. Rek DKI">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Alamat </label>
                                    <textarea class="form-control" id="alamat" name="alamat"><?php echo !empty($pegawai->alamat) ? $pegawai->alamat : ""; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">No. Telepon / HP </label>
                                    <input type="text" class="form-control" name="no_tlp" id="no_tlp" value="<?php echo $pegawai->no_tlp ?>" placeholder="Masukan No. Telepon">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Foto </label>
                                    <input type="file" id="foto_file" name="foto_file" multiple="">
                                    <p class="help-block"> Upload file berekstensi .jpeg, .png, .gif </p>
                                </div>
                                <button type="button" class="btn btn-success pull-right" id="btnSavePegawai">Simpan</button>
                            </div>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="ubahpasswd">
                    <div id="notifpass_type" class="alert alert-dismissable" style="display:none;">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <p id="notifpass_message"></p>
                    </div>
                    <div class="row">
                        <?php echo form_open('#',array('id' => 'formGantiPassword','role'=> 'form'))?>
                            <div class="col-lg-12">
                                <span style="color: red">* Required Field</span>
                            </div>
                            <div class="col-lg-12">
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label>Email <span style="color: red">*</span></label>
                                        <input type="text" class="form-control" name="upd_email" id="upd_email" value="<?php echo !empty($users->email) ? $users->email : "-"; ?>" placeholder="email format (example@mail.com)">
                                    </div>
                                    <div class="form-group">
                                        <label>Username <span style="color: red">*</span></label>
                                        <input type="text" class="form-control" name="upd_username" id="upd_username" value="<?php echo !empty($users->username) ? $users->username : "-"; ?>" placeholder="Username">
                                    </div>
                                </div>
                                <div class="col-lg-1">
                                    &nbsp;
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Password Baru <span style="color: red">*</span></label>
                                        <input type="password" class="form-control" name="upd_password" id="upd_password" placeholder="Password">
                                    </div>
                                    <div class="form-group">
                                        <label>Konfirmasi Password Baru <span style="color: red">*</span></label>
                                        <input type="password" class="form-control" name="upd_conf_pass" id="upd_conf_pass" placeholder="Konfirmasi Password">
                                    </div>
                                    <button type="button" class="btn btn-success pull-right" id="btnGantiPassword">Simpan</button>
                                </div>
                            </div>
                        <?php echo form_close()?>
                    </div>
                </div>
                <!-- /.tab-pane -->
                <!-- /.tab-pane -->
                <div class="tab-pane" id="pendidikan">
                    <div id="notifpend_type" class="alert alert-dismissable" style="display:none;">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <p id="notifpend_message"></p>
                    </div>
                    <div class="box-header with-border">
                        <h3 class="box-title">Pendidikan</h3>
                        <div class="box-tools">
                          <button type="button" class="btn btn-success" id="addPendidikan" data-target="#addPendidikanModal" data-toggle="modal">Tambah Pendidikan</button>
                        </div>
                    </div>
                    <div class="box-body">
                        <input type="hidden" id="<?php echo $this->security->get_csrf_token_name()?>_del" name="<?php echo $this->security->get_csrf_token_name()?>_del" value="<?php echo $this->security->get_csrf_hash()?>" />
                        <table id="table_pendidikan" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th style="width: 15px;"> No </th>
                                <th> Jenjang Pendidikan </th>
                                <th> Nama Sekolah </th>
                                <th> Tahun Masuk </th>
                                <th> Tahun Lulus </th>
                                <th> No. Ijazah </th>
                                <th> Link ijazah </th>
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
                    <!-- /.box-body -->
                    <div class="box-header with-border">
                        <h3 class="box-title">Pelatihan</h3>
                        <div class="box-tools">
                          <button type="button" class="btn btn-success" id="addPelatihan" data-target="#addPelatihanModal" data-toggle="modal">Tambah Pelatihan</button>
                        </div>
                    </div>
                    <div class="box-body">
                        <table id="table_pelatihan" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th style="width: 15px;"> No </th>
                                <th> Nama Pelatihan </th>
                                <th> Penyedia Pelatihan </th>
                                <th> Bulan </th>
                                <th> No. Sertifikat </th>
                                <th> Link Sertifikat </th>
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
                    <!-- /.box-body -->
                </div>
                <!-- /.tab-pane -->

              <div class="tab-pane" id="dokumen">
                <form class="form-horizontal">
                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">Name</label>

                    <div class="col-sm-10">
                      <input type="email" class="form-control" id="inputName" placeholder="Name">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputEmail" class="col-sm-2 control-label">Email</label>

                    <div class="col-sm-10">
                      <input type="email" class="form-control" id="inputEmail" placeholder="Email">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputName2" class="col-sm-2 control-label">Name</label>

                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputName2" placeholder="Name">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputExperience" class="col-sm-2 control-label">Experience</label>

                    <div class="col-sm-10">
                      <textarea class="form-control" id="inputExperience" placeholder="Experience"></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputSkills" class="col-sm-2 control-label">Skills</label>

                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputSkills" placeholder="Skills">
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <div class="checkbox">
                        <label>
                          <input type="checkbox"> I agree to the <a href="#">terms and conditions</a>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-danger">Submit</button>
                    </div>
                  </div>
                </form>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  <div class="modal fade" id="addPendidikanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Tambah Pendidikan</h4>
            </div>
            <div class="modal-body">
                <div id="alert_type_pendidikan" class="alert alert-dismissable" style="display:none;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p id="alert_message_pendidikan"></p>
                </div>
                
                    <span style="color: red">* Wajib diisi</span>
                <?php echo form_open('#',array('id' => 'formPendidikan','role'=> 'form', 'enctype' => 'multipart/form-data')) ?>
                <div class="form-group">
                    <label class="control-label">Jenjang Pendidikan <span style="color: red">*</span></label>
                    <select name="jenjang" id="jenjang" class="form-control">
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
                    <label class="control-label">Nama Sekolah <span style="color: red">*</span></label>
                    <input type="text" id="nama_sekolah" name="nama_sekolah" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label">Tahun Masuk <span style="color: red">*</span></label>
                    <input type="text" id="tahun_masuk" name="tahun_masuk" class="form-control thnpicker" readonly>
                </div>
                <div class="form-group">
                    <label class="control-label">Tahun Lulus <span style="color: red">*</span></label>
                    <input type="text" id="tahun_lulus" name="tahun_lulus" class="form-control thnpicker" readonly>
                </div>
                <div class="form-group">
                    <label class="control-label">No. Ijazah </label>
                    <input type="text" id="no_ijazah" name="no_ijazah" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label">Scan Ijazah </label>
                    <input type="file" id="ijazah_file" name="ijazah_file" multiple="">
                    <p class="help-block"> Upload file berekstensi .jpeg, .png </p>
                </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" id="btnSavePendidikan">Simpan</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="updatePendidikanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Update Pendidikan</h4>
            </div>
            <div class="modal-body">
                <div id="alert_type_upd_pendidikan" class="alert alert-dismissable" style="display:none;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p id="alert_message_upd_pendidikan"></p>
                </div>
                
                    <span style="color: red">* Wajib diisi</span>
                <?php echo form_open('#',array('id' => 'formUpdPendidikan','role'=> 'form', 'enctype' => 'multipart/form-data')) ?>
                <div class="form-group">
                    <label class="control-label">Jenjang Pendidikan <span style="color: red">*</span></label>
                    <select name="upd_jenjang" id="upd_jenjang" class="form-control">
                        <option value="" selected disabled> -- PILIH Status -- </option>
                        <?php
                        $list_pendidikan = $this->Pegawai_model->list_pendidikan();
                        foreach($list_pendidikan as $pendidikan){
                            echo "<option value='".$pendidikan->pendidikan_id."'>".$pendidikan->pendidikan_nama."</option>";
                        }
                        ?>
                    </select>
                    <input type="hidden" name="upd_pendidikan_id" id="upd_pendidikan_id" readonly>
                </div>
                <div class="form-group">
                    <label class="control-label">Nama Sekolah <span style="color: red">*</span></label>
                    <input type="text" id="upd_nama_sekolah" name="upd_nama_sekolah" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label">Tahun Masuk <span style="color: red">*</span></label>
                    <input type="text" id="upd_tahun_masuk" name="upd_tahun_masuk" class="form-control thnpicker" readonly>
                </div>
                <div class="form-group">
                    <label class="control-label">Tahun Lulus <span style="color: red">*</span></label>
                    <input type="text" id="upd_tahun_lulus" name="upd_tahun_lulus" class="form-control thnpicker" readonly>
                </div>
                <div class="form-group">
                    <label class="control-label">No. Ijazah </label>
                    <input type="text" id="upd_no_ijazah" name="upd_no_ijazah" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label">Scan Ijazah </label>
                    <input type="file" id="upd_ijazah_file" name="upd_ijazah_file" multiple="">
                    <p class="help-block"> Upload file berekstensi .jpeg, .png </p>
                </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" id="btnUpdPendidikan">Simpan</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


<!-- add pelatihan modal -->
<div class="modal fade" id="addPelatihanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Tambah Pelatihan</h4>
            </div>
            <div class="modal-body">
                <div id="alert_type_pelatihan" class="alert alert-dismissable" style="display:none;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p id="alert_message_pelatihan"></p>
                </div>
                
                    <span style="color: red">* Wajib diisi</span>
                <?php echo form_open('#',array('id' => 'formPelatihan','role'=> 'form', 'enctype' => 'multipart/form-data')) ?>
                <div class="form-group">
                    <label class="control-label">Nama Pelatihan <span style="color: red">*</span></label>
                    <input type="text" id="nama_pelatihan" name="nama_pelatihan" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label">Penyedia Pelatihan <span style="color: red">*</span></label>
                    <input type="text" id="penyedia_pelatihan" name="penyedia_pelatihan" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label">Waktu Pelatihan <span style="color: red">*</span></label>
                    <input type="text" id="waktu_pelatihan" name="waktu_pelatihan" class="form-control tgl-lahir" readonly>
                </div>
                <div class="form-group">
                    <label class="control-label">No. Sertifikat </label>
                    <input type="text" id="no_sertifikat" name="no_sertifikat" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label">Scan Sertifikat </label>
                    <input type="file" id="sertifikat_file" name="sertifikat_file" multiple="">
                    <p class="help-block"> Upload file berekstensi .jpeg, .png </p>
                </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" id="btnSavePelatihan">Simpan</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- / add pelatihan modal -->

<!-- edit pelatihan modal -->
<div class="modal fade" id="updatePelatihanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Update Pelatihan</h4>
            </div>
            <div class="modal-body">
                <div id="alert_type_upd_pelatihan" class="alert alert-dismissable" style="display:none;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p id="alert_message_upd_pelatihan"></p>
                </div>
                
                    <span style="color: red">* Wajib diisi</span>
                <?php echo form_open('#',array('id' => 'formUpdPelatihan','role'=> 'form', 'enctype' => 'multipart/form-data')) ?>
                <div class="form-group">
                    <input type="hidden" name="upd_pelatihan_id" id="upd_pelatihan_id" readonly>
                    <label class="control-label">Nama Pelatihan <span style="color: red">*</span></label>
                    <input type="text" id="upd_nama_pelatihan" name="upd_nama_pelatihan" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label">Penyedia Pelatihan <span style="color: red">*</span></label>
                    <input type="text" id="upd_penyedia_pelatihan" name="upd_penyedia_pelatihan" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label">Waktu Pelatihan <span style="color: red">*</span></label>
                    <input type="text" id="upd_waktu_pelatihan" name="upd_waktu_pelatihan" class="form-control tgl-lahir" readonly>
                </div>
                <div class="form-group">
                    <label class="control-label">No. Sertifikat </label>
                    <input type="text" id="upd_no_sertifikat" name="upd_no_sertifikat" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label">Scan Sertifikat </label>
                    <input type="file" id="upd_sertifikat_file" name="upd_sertifikat_file" multiple="">
                    <p class="help-block"> Upload file berekstensi .jpeg, .png </p>
                </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" id="btnUpdPelatihan">Simpan</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- / edit pelatihan modal -->

<?php
  $this->load->view('pelatihanJs');
  $this->load->view('profileJs');
  $this->load->view('footer');
?>