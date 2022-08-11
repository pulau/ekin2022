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
            <small>Bagian</small>
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
                      <h3 class="box-title">Tambah Data Bagian</h3>
                      <div class="box-tools"></div>
                    </div>
                    <div class="box-body">
                        <div class="col-lg-4">
                            <?php echo form_open('#',array('id' => 'formBagian','role'=> 'form'))?>
                                <div class="form-group">
                                    <label>Bagian</label>
                                    <input type="text" id="bagian_nama" name="bagian_nama" class="form-control" placeholder="bagian / Unit">
                                    <input type="hidden" id="bagian_id" name="bagian_id" readonly="readonly">
                                </div>
                                <div class="form-group">
                                    <label>Koordinator</label>
                                    <select name="koordinator" id="koordinator" class="form-control" style="width: 100%;">
                                        <option value="" selected disabled>-- Pilih Koordinator --</option>
                                        <?php
                                        $list_pegawai = $this->Bagian_model->list_pegawai();
                                        foreach($list_pegawai as $peg){
                                            // echo "<option value='".$peg->id_pegawai."'>".$peg->nama_pegawai."</option>";
                                             echo "<option value='".$peg->id_pegawai."'>".$peg->nama_pegawai."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>PJ Cuti</label>
                                    <select name="pj_cuti" id="pj_cuti" class="form-control">
                                        <option value="" selected disabled>-- Pilih PJ Cuti --</option>
                                        <?php
                                        $list_pegawai2 = $this->Bagian_model->list_pegawai();
                                        foreach($list_pegawai2 as $peg){
                                            echo "<option value='".$peg->id_pegawai."'>".$peg->nama_pegawai."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-actions">
                                    <button type="button" id="btnSaveBagian" class="btn btn-info">Simpan</button>
                                    <button type="button" class="btn btn-default" onclick="clearForm()">Bersihkan</button>
                                </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <div class="box-header">
                      <h3 class="box-title">Data Bagian/Unit</h3>
                      <div class="box-tools"></div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div id="notification_type" class="alert alert-dismissable" style="display:none;">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p id="notification_message"></p>
                        </div>
                        <input type="hidden" id="<?php echo $this->security->get_csrf_token_name()?>_del" name="<?php echo $this->security->get_csrf_token_name()?>_del" value="<?php echo $this->security->get_csrf_hash()?>" />
                        <table id="table_bagian" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th style="width: 15px;"> No </th>
                                <th> Nama Bagian </th>
                                <th> Koordinator </th>
                                <th> PJ Cuti </th>
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
                                <th> Nama Bagian </th>
                                <th> Koordinator </th>
                                <th> PJ Cuti </th>
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
  $this->load->view('bagianJs');
  $this->load->view('footer');
  ?>