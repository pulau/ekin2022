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
            <small>Penyerapan</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url('co_panel'); ?>"><i class="fa fa-dashboard"></i> CPanel</a></li>
            <li>Ekinerja</li><li><?php echo $bc_parent; ?></li><li class="active"><?php echo $bc_child; ?></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                      <h3 class="box-title">Tambah Master Gaji</h3>
                      <div class="box-tools"></div>
                    </div>
                    <div class="box-body">
                        <div class="col-lg-4">
                            <?php echo form_open('#',array('id' => 'formGaji','role'=> 'form'))?>
                                <div class="form-group">
                                    <label>Masa Kerja</label>
                                    <div class="row">
                                        <div class="col-xs-5">
                                            <input type="text" id="mk_awal" name="mk_awal" class="form-control" onkeypress="return numbersOnly(event);">
                                            <input type="hidden" id="gaji_id" name="gaji_id" readonly="readonly">
                                        </div>
                                        <div class="col-xs-1">
                                            <label>-</label>
                                        </div>
                                        <div class="col-xs-5">
                                            <input type="text" id="mk_akhir" name="mk_akhir" class="form-control" onkeypress="return numbersOnly(event);">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Pendidikan</label>
                                    <select name="pendidikan" id="pendidikan" class="form-control">
                                        <option value="" selected disabled> -- PILIH Pendidikan -- </option>
                                        <?php
                                        $list_pendidikan = $this->Gaji_model->list_pendidikan();
                                        foreach($list_pendidikan as $pendidikan){
                                            echo "<option value='".$pendidikan->pendidikan_id."'>".$pendidikan->pendidikan_nama."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Gaji Pokok</label>
                                    <input type="text" id="gapok" name="gapok" class="form-control" placeholder="Masukan Angka" onkeypress="return numbersOnly(event);">
                                </div>
                                <div class="form-actions">
                                    <button type="button" id="btnSaveGaji" class="btn btn-info">Simpan</button>
                                    <button type="button" class="btn btn-default" onclick="clearForm()">Bersihkan</button>
                                </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <div class="box-header">
                      <h3 class="box-title">List Gaji Pokok</h3>
                      <div class="box-tools">
                      </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div id="notification_type" class="alert alert-dismissable" style="display:none;">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p id="notification_message"></p>
                        </div>
                        <input type="hidden" id="<?php echo $this->security->get_csrf_token_name()?>_del" name="<?php echo $this->security->get_csrf_token_name()?>_del" value="<?php echo $this->security->get_csrf_hash()?>" />
                        <table id="table_gaji" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th style="width: 15px;"> No </th>
                                <th> Masa Kerja </th>
                                <th> Pendidikan </th>
                                <th> Gaji Pokok </th>
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
                                <th style="width: 15px;"> No </th>
                                <th> Masa Kerja </th>
                                <th> Pendidikan </th>
                                <th> Gaji Pokok </th>
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
  
<?php
$this->load->view('gajiJs');
$this->load->view('footer');
?>