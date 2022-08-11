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
            <small>Pengajuan Cuti</small>
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
                      <div class="box-tools"><button type="button" class="btn btn-block btn-success" data-toggle="modal" data-target="#addPengajuanCutiModal"><i class="fa fa-plus"></i> Buat Pengajuan Cuti</button></div>
                      <input type="hidden" id="<?php echo $this->security->get_csrf_token_name()?>_del" name="<?php echo $this->security->get_csrf_token_name()?>_del" value="<?php echo $this->security->get_csrf_hash()?>" />
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div id="notification_type" class="alert alert-dismissable" style="display:none;">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p id="notification_message"></p>
                        </div>
                        <table id="table_pengajuan" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width:15px;"> No </th>
                                    <th> Jenis Cuti </th>
                                    <th> Tgl. Pengajuan </th>
                                    <th> Tanggal Cuti </th>
                                    <th> Alasan </th>
                                    <th> Pengganti </th>
                                    <th> Status Approval </th>
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
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
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
                <h4>Jatah Cuti Anda Tersisa <?php echo $sisa_cuti; ?> Hari</h4>
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
                            <input type="text" id="nama_pegawai" name="nama_pegawai" class="form-control" value="<?php echo $pegawai->nama_pegawai; ?>" readonly>
                            <input type="hidden" id="pegawai_id" name="pegawai_id" value="<?php echo $pegawai->id_pegawai; ?>" readonly>
                            <input type="hidden" id="cuti_id" name="cuti_id" value="" readonly>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        &nbsp;
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Bagian / Unit <span style="color: red">*</span></label>
                            <input type="text" id="bagian" name="bagian" class="form-control" value="<?php echo $bagian->bagian_nama; ?>" readonly>
                            <input type="hidden" id="bagian_id" name="bagian_id" value="<?php echo $pegawai->bagian; ?>" readonly>
                            <input type="hidden" id="kordinator" name="kordinator" value="<?php echo isset($pegawai->pj_cuti) ? $pegawai->pj_cuti : $bagian->pj_cuti; ?>" readonly>
                            <input type="hidden" id="sisa_cuti" name="sisa_cuti" value="<?php echo $sisa_cuti; ?>" readonly>
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
                                $jenis_cuti = $this->Pengajuan_cuti_model->list_jeniscuti();
                                foreach($jenis_cuti as $jenis){
                                    echo "<option value='".$jenis->jeniscuti_id."'>".$jenis->jeniscuti_nama."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Keterangan Cuti </label>
                            <input type="text" class="form-control" name="ket_cuti" id="ket_cuti" placeholder="Keterangan Cuti" enabled>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Rencana Cuti <span style="color: red">*</span></label>
    <!--                                    <div class="input-group">-->
                            <input type="text" class="form-control tglpicker2" name="tgl_cuti" id="tgl_cuti" placeholder="dd-mm-yyyy" onchange="setJmlHari()" readonly>
                                <!--<span class="input-group-addon"> Sampai </span>-->
                            <input type="hidden" name="tgl_awal" id="tgl_awal" readonly>
                            <input type="hidden" name="tgl_akhir" id="tgl_akhir" readonly>
    <!--                                    </div>-->
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
                            <select name="pengganti" id="pengganti" class="form-control select3" style="width: 100%;">
                                <option value="" selected disabled> -- Pilih Pengganti -- </option>
                                <?php
                                $list_pegawai = $this->Pengajuan_cuti_model->list_pegawai($pegawai->id_pegawai,$pegawai->bagian);
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
$this->load->view('pengajuan_cutiJs');
$this->load->view('footer');
?>