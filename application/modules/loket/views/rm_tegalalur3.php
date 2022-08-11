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
            Loket
            <small>RM PKL TEGAL ALUR 3</small>
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
                      <h3 class="box-title">Daftarkan Pasien Baru</h3>
                      <div class="box-tools"></div>
                    </div>
                    <div class="box-body">
                        <div class="col-lg-4">
                            <?php echo form_open('#',array('id' => 'formRM','role'=> 'form'))?>
                                <div class="form-group">
                                    <label>Nama Pasien</label>
                                    <input type="text" id="nama_pasien" name="nama_pasien" class="form-control" placeholder="Nama Lengkap">
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Lahir</label>
                                    <input type="text" id="tgl_lahir" name="tgl_lahir" class="form-control tglpicker" placeholder="dd-mm-yyyy" readonly>
                                </div>
                                <div class="form-actions">
                                    <button type="button" id="btnSaveRM" class="btn btn-info">Simpan</button>
                                    <button type="button" class="btn btn-default" onclick="clearForm()">Bersihkan</button>
                                </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <div class="box-header">
                      <h3 class="box-title">Data Pasien</h3>
                      <div class="box-tools"></div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div id="notification_type" class="alert alert-dismissable" style="display:none;">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p id="notification_message"></p>
                        </div>
                        <table id="table_pasien" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Daftar</th>
                                <th>No. Rekam Medis</th>
                                <th>Nama Pasien</th>
                                <th>Tanggal Lahir</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6"> No Data to Display</td>
                                </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Daftar</th>
                                <th>No. Rekam Medis</th>
                                <th>Nama Pasien</th>
                                <th>Tanggal Lahir</th>
                                <th>Action</th>
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

<div class="modal fade in" id="infoPasien" tabindex="-1 " role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Info Pasien</h4>
            </div>
            <div class="modal-body">
                <!--<div class="portlet-body">-->
                    <h1>Pasien Bernama <span id="pasien_nama_mod"></span></h1>
                    <h1>Dengan No. RM <span id="no_rm_mod"></span></h1>
                    <h1>Berhasil Didaftarkan</h1>
                <!--</div>-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Keluar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="update-pasien-modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Update RM Pasien</h4>
        </div>
        <div class="modal-body">
            <div id="upd_alert_type" class="alert alert-dismissable" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p id="upd_alert_message"></p>
            </div>
            <?php echo form_open('#',array('id' => 'fmUpdateRM','role'=> 'form'))?>
                <div class="col-lg-12">
                    <span style="color: red">* Wajib diisi</span>
                </div>
                <div class="form-group">
                    <label>No. RM <span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="upd_no_rm" id="upd_no_rm" readonly>
                    <input type="hidden" name="upd_rm_id" id="upd_rm_id" readonly>
                </div>
                <div class="form-group">
                    <label>Nama Pasien <span style="color: red">*</span></label>
                    <input type="text" id="upd_nama_pasien" name="upd_nama_pasien" class="form-control" placeholder="Nama Lengkap">
                </div>
                <div class="form-group">
                    <label>Tanggal Lahir <span style="color: red">*</span></label>
                    <input type="text" id="upd_tgl_lahir" name="upd_tgl_lahir" class="form-control tglpicker" placeholder="dd-mm-yyyy" readonly>
                </div>
            <?php echo form_close(); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnUpdateRM">Simpan</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
  
  <?php
  $this->load->view('rm_tegalalur3Js');
  $this->load->view('footer');
  ?>