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
            <small>Review Cuti</small>
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
                      <div class="box-tools"><button type="button" class="btn btn-block btn-success" data-toggle="modal" data-target="#addPengajuanCutiModal"><i class="fa fa-plus"></i> Input Cuti Pegawai</button></div>
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
                                    <th> PJ Cuti</th>
                                    <th> Persetuan PJ</th>
                                    <th> Status Review </th>
                                    <th> Status Approval </th>
                                    <th> Actions </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="13"> No Data to Display</td>
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
          <h4 class="modal-title">Review Permohonan Cuti</h4>
        </div>
        <div class="modal-body">
            <div id="alert_type_rev" class="alert alert-dismissable" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p id="alert_message_rev"></p>
            </div>
            <div class="row">
                <?php echo form_open('#',array('id' => 'formReviewCuti','role'=> 'form'))?>
                <div class="col-lg-12">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label class="control-label">Nama Pegawai</label>
                            <input type="text" id="nama_pegawai_rev" name="nama_pegawai_rev" class="form-control" value="" disabled>
                            <input type="hidden" id="cuti_id" name="cuti_id" value="" readonly>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        &nbsp;
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="control-label">Bagian / Unit <span style="color: red">*</span></label>
                            <input type="text" id="bagian_rev" name="bagian_rev" class="form-control" value="" disabled>

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
                            <input type="text" id="sisa_cuti_rev" name="sisa_cuti_rev" class="form-control" value="" disabled>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Jenis Cuti</label>
                            <input type="text" id="jenis_cuti_rev" name="jenis_cuti_rev" class="form-control" value="" disabled>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Keterangan Cuti </label>
                            <input type="text" class="form-control" name="ket_cuti_rev" id="ket_cuti_rev" disabled>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Tanggal Rencana Cuti</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="tgl_awal_rev" id="tgl_awal_rev" disabled>
                                <span class="input-group-addon"> Sampai </span>
                                <input type="text" class="form-control" name="tgl_akhir_rev" id="tgl_akhir_rev" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        &nbsp;
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="control-label">Jumlah Hari</label>
                            <input type="text" class="form-control" name="jml_hari_rev" id="jml_hari_rev" disabled>
                        </div>
                        <div class="form-group">
                            <label class="control-label">No. Telepon / HP</label>
                            <input type="text" class="form-control" name="no_tlp_rev" id="no_tlp_rev" disabled>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Pegawai Pengganti</label>
                            <input type="text" class="form-control" name="pegawai_pengganti_rev" id="pegawai_pengganti_rev" disabled>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Alasan</label>
                            <textarea class="form-control" id="alasan_rev" name="alasan_rev" readonly></textarea>
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
  
  <div class="modal fade" id="addPengajuanCutiModal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Form Permohonan Cuti</h4>
        </div>
        <div class="modal-body">
            <div id="alert_type" class="alert alert-dismissable" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p id="alert_message"></p>
            </div>
            <div class="callout callout-info">
                <h4>Jatah Cuti Pegawai Tersisa <span id="sisa_cuti_label">0</span> Hari</h4>
            </div>
            <div class="row">
                <?php echo form_open('#',array('id' => 'formPengajuanCuti','role'=> 'form'))?>
                <div class="col-lg-12">
                    <span style="color: red">* Required Field</span>
                </div>
                <div class="col-lg-12">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label>Nama Pegawai <span style="color: red">*</span></label>
                            <select name="pegawai_id" id="pegawai_id" class="form-control select3" style="width: 100%;" onchange="setBagian()">
                                <option value="" selected disabled> -- Pilih Pegawai -- </option>
                                <?php
                                $list_pegawai = $this->Review_cuti_model->list_pegawai();
                                foreach($list_pegawai as $peg){
                                    echo "<option value='".$peg->id_pegawai."'>".$peg->nama_pegawai."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        &nbsp;
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Bagian / Unit <span style="color: red">*</span></label>
                            <input type="text" id="bagian" name="bagian" class="form-control" value="" readonly>
                            <input type="hidden" id="bagian_id" name="bagian_id" value="" readonly>
                            <input type="hidden" id="kordinator" name="kordinator" value="" readonly>
                            <input type="hidden" id="sisa_cuti" name="sisa_cuti" value="" readonly>
                            <input type="hidden" id="input_by_review" name="input_by_review" value="1" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <hr>
                </div>
                <div class="col-lg-12">
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label>Jenis Cuti <span style="color: red">*</span></label>
                            <select name="jenis_cuti" id="jenis_cuti" class="form-control" onchange="setKeterangan()">
                                <option value="" selected disabled>-- Pilih Jenis Cuti --</option>
                                <?php
                                $jenis_cuti = $this->Review_cuti_model->list_jeniscuti();
                                foreach($jenis_cuti as $jenis){
                                    echo "<option value='".$jenis->jeniscuti_id."'>".$jenis->jeniscuti_nama."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Keterangan Cuti </label>
                            <input type="text" class="form-control" name="ket_cuti" id="ket_cuti" placeholder="Keterangan Cuti" disabled>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Rencana Cuti <span style="color: red">*</span></label>
                            <input type="text" class="form-control tglpicker2" name="tgl_cuti" id="tgl_cuti" placeholder="dd-mm-yyyy" onchange="setJmlHari()" readonly>
                            <input type="hidden" name="tgl_awal" id="tgl_awal" readonly>
                            <input type="hidden" name="tgl_akhir" id="tgl_akhir" readonly>
                        </div>
                        <div class="form-group">
                            <label>Jumlah Hari <span style="color: red">*</span></label>
                            <input type="text" class="form-control" name="jml_hari" id="jml_hari" placeholder="Jumlah Hari" value="0" readonly>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        &nbsp;
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Keberadaan saat cuti (Kota) </label>
                            <input type="text" class="form-control" name="kota_cuti" id="kota_cuti" placeholder="Masukan Kota">
                        </div>
                        <div class="form-group">
                            <label>No. Telepon / HP </label>
                            <input type="text" class="form-control" name="no_tlp" id="no_tlp" placeholder="Masukan No. Telepon">
                        </div>
                        <div class="form-group">
                            <label>Pegawai Pengganti <span style="color: red">*</span></label>
                            <select name="pengganti" id="pengganti" class="form-control select3" style="width: 100%;" style="width:100%">
                                <option value="" selected disabled> -- Pilih Pengganti -- </option>
                                <?php
                                $list_pegawai = $this->Review_cuti_model->list_pegawai($pegawai->id_pegawai);
                                foreach($list_pegawai as $peg){
                                    echo "<option value='".$peg->id_pegawai."'>".$peg->nama_pegawai."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Alasan <span style="color: red">*</span></label>
                            <textarea class="form-control" id="alasan" name="alasan"></textarea>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnSavePengajuanCuti">Simpan</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php
$this->load->view('review_cutiJs');
$this->load->view('footer');
?>