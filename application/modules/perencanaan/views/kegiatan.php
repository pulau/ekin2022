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
            Perencanaan
            <small>Kegiatan</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url('co_panel'); ?>"><i class="fa fa-dashboard"></i> CPanel</a></li>
            <li>Perencanaan</li><li><?php echo $bc_parent; ?></li><li class="active"><?php echo $bc_child; ?></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                      <h3 class="box-title">Tambah Kegiatan</h3>
                      <div class="box-tools"></div>
                    </div>
                    <div class="box-body">
                        <div class="col-lg-4">
                            <?php echo form_open('#',array('id' => 'formKegiatan','role'=> 'form'))?>
                                <div class="form-group">
                                    <label>Program</label>
                                    <select name="program" id="program" class="form-control" onchange="setInpro()">
                                        <option value="" selected disabled>-- Pilih Program --</option>
                                        <?php
                                        $list_program = $this->Kegiatan_model->program_list();
                                        foreach($list_program as $program){
                                            echo "<option value='".$program->program_id."'>".$program->program_nama."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Indikator Program</label>
                                    <select name="inpro" id="inpro" class="form-control">
                                        <option value="" selected disabled>-- Pilih Indikator Program --</option>
                                        <?php
//                                        $list_program = $this->Inpro_model->program_list();
//                                        foreach($list_program as $program){
//                                            echo "<option value='".$program->program_id."'>".$program->program_nama."</option>";
//                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Kegiatan</label>
                                    <input type="text" id="kegiatan" name="kegiatan" class="form-control" placeholder="Kegiatan">
                                    <input type="hidden" id="kegiatan_id" name="kegiatan_id" readonly="readonly">
                                </div>
                                <div class="form-actions">
                                    <button type="button" id="btnSaveKegiatan" class="btn btn-info">Simpan</button>
                                    <button type="button" class="btn btn-default" onclick="clearForm()">Bersihkan</button>
                                </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <div class="box-header">
                      <h3 class="box-title">Kegiatan</h3>
                      <div class="box-tools"></div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div id="notification_type" class="alert alert-dismissable" style="display:none;">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p id="notification_message"></p>
                        </div>
                        <input type="hidden" id="<?php echo $this->security->get_csrf_token_name()?>_del" name="<?php echo $this->security->get_csrf_token_name()?>_del" value="<?php echo $this->security->get_csrf_hash()?>" />
                        <table id="table_kegiatan" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th style="width: 15px;"> No </th>
                                <th> Kegiatan </th>
                                <th> Indikator Program </th>
                                <th> Actions </th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4"> No Data to Display</td>
                                </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th style="width: 15px;"> No </th>
                                <th> Kegiatan </th>
                                <th> Indikator Program </th>
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
  $this->load->view('kegiatanJs');
  $this->load->view('footer');
  ?>