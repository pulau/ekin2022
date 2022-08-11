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
            E-Cuti
            <small>Persetujuan PJ</small>
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
                                        <input type="text" name="filter_bln" id="filter_bln" class="form-control blnpicker" placeholder="MM yyyy" value="">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-danger" id="btnFilterCuti">Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </h3>
                      <div class="box-tools"></div>
                      <input type="hidden" id="<?php echo $this->security->get_csrf_token_name()?>_del" name="<?php echo $this->security->get_csrf_token_name()?>_del" value="<?php echo $this->security->get_csrf_hash()?>" />
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div id="notification_type" class="alert alert-dismissable" style="display:none;">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p id="notification_message"></p>
                        </div>
                        <table id="table_review" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width:15px;"> No </th>
                                    <th> Nama Pegawai</th>
                                    <th> Jenis Cuti </th>
                                    <th> Tgl. Pengajuan </th>
                                    <th> Awal Cuti </th>
                                    <th> Akhir Cuti </th>
                                    <th> Alasan </th>
                                    <th> Pengganti </th>
                                    <th> Status Review </th>
                                    <th> Status Approval </th>
                                    <th> Actions </th>
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
  
  <!-- .modal -->
  <div class="modal fade" id="reviewCutiModal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Lembar Persetujuan Cuti</h4>
        </div>
        <div class="modal-body">
            <div id="alert_type" class="alert alert-dismissable" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p id="alert_message"></p>
            </div>
            <div class="row">
                <?php echo form_open('#',array('id' => 'formReviewCuti','role'=> 'form'))?>
                <div class="col-lg-12">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="control-label">Nama Pegawai</label>
                            <input type="text" id="nama_pegawai" name="nama_pegawai" class="form-control" value="" disabled>
                            <input type="hidden" id="cuti_id" name="cuti_id" value="" readonly>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        &nbsp;
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="control-label">Bagian / Unit <span style="color: red">*</span></label>
                            <input type="text" id="bagian" name="bagian" class="form-control" value="" disabled>

                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <hr>
                </div>
                <div class="col-lg-12">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="control-label">Sisa Jatah Cuti</label>
                            <input type="text" id="sisa_cuti" name="sisa_cuti" class="form-control" value="" disabled>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Jenis Cuti</label>
                            <input type="text" id="jenis_cuti" name="jenis_cuti" class="form-control" value="" disabled>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Keterangan Cuti </label>
                            <input type="text" class="form-control" name="ket_cuti" id="ket_cuti" disabled>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Tanggal Rencana Cuti</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="tgl_awal" id="tgl_awal" disabled>
                                <span class="input-group-addon"> Sampai </span>
                                <input type="text" class="form-control" name="tgl_akhir" id="tgl_akhir" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        &nbsp;
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="control-label">Jumlah Hari</label>
                            <input type="text" class="form-control" name="jml_hari" id="jml_hari" disabled>
                        </div>
                        <div class="form-group">
                            <label class="control-label">No. Telepon / HP</label>
                            <input type="text" class="form-control" name="no_tlp" id="no_tlp" disabled>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Pegawai Pengganti</label>
                            <input type="text" class="form-control" name="pegawai_pengganti" id="pegawai_pengganti" disabled>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Alasan</label>
                            <textarea class="form-control" id="alasan" name="alasan" readonly></textarea>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-danger" id="btnTolakCuti">Tolak</button>
            <button type="button" class="btn btn-success" id="btnTerimaCuti">Terima</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
  <!-- /.modal -->
<?php
$this->load->view('persetujuan_pjJs');
$this->load->view('footer');
?>