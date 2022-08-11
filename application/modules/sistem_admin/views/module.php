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
            Sistem Administrator
            <small>Module Management</small>
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
                <h3 class="box-title">Module List</h3>
                <div class="box-tools"><button type="button" class="btn btn-block btn-success" data-toggle="modal" data-target="#tambah-module-modal"><i class="fa fa-plus"></i> Tambah Module</button></div>
                <input type="hidden" id="<?php echo $this->security->get_csrf_token_name()?>_del" name="<?php echo $this->security->get_csrf_token_name()?>_del" value="<?php echo $this->security->get_csrf_hash()?>" />
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                  <div id="notification_type" class="alert alert-dismissable" style="display:none;">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                      <p id="notification_message"></p>
                  </div>
                  <table id="module_table" class="table table-bordered table-striped table-hover">
                      <thead>
                      <tr>
                          <th>No</th>
                          <th>Module Name</th>
                          <th>Label</th>
                          <th>URL</th>
                          <th>Kategori</th>
                          <th>Urutan</th>
                          <th>Action</th>
                      </tr>
                      </thead>
                      <tbody>
                          <tr>
                              <td colspan="7"> No Data to Display</td>
                          </tr>
                      </tbody>
                      <tfoot>
                      <tr>
                          <th>No</th>
                          <th>Module Name</th>
                          <th>Label</th>
                          <th>URL</th>
                          <th>Kategori</th>
                          <th>Urutan</th>
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
  
  <div class="modal fade" id="tambah-module-modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Input New Module</h4>
        </div>
        <div class="modal-body">
            <div id="alert_type" class="alert alert-dismissable" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p id="alert_message"></p>
            </div>
            <?php echo form_open('#',array('id' => 'fmCreateModule','role'=> 'form'))?>
                <div class="form-group">
                    <label>Module Name</label>
                    <input type="text" class="form-control" id="module_name" name="module_name" placeholder="Enter Module Name">
                </div>
                <div class="form-group">
                    <label>Label</label>
                    <input type="text" class="form-control" id="label" name="label" placeholder="Enter Label">
                </div>
                <div class="form-group">
                    <label>Module Icon</label>
                    <input type="text" class="form-control" id="icon" name="icon" placeholder="Enter Icon">
                </div>
                <div class="form-group">
                    <label>URL</label>
                    <input type="text" class="form-control" id="url" name="url" placeholder="Enter URL">
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select class="form-control" id="kategori" name="kategori">
                        <option value="MANAJEMEN">Manajemen</option>
                        <option value="PELAYANAN">Pelayanan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Urutan</label>
                    <input type="number" class="form-control" id="urutan" name="urutan" placeholder="Enter Urutan">
                </div>
            <?php echo form_close(); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="buttonSimpanModule">Simpan</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="update-module-modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Update Module</h4>
        </div>
        <div class="modal-body">
            <div id="upd_alert_type" class="alert alert-dismissable" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p id="upd_alert_message"></p>
            </div>
            <?php echo form_open('#',array('id' => 'fmUpdateModule','role'=> 'form'))?>
                <div class="form-group">
                    <label>Module Name</label>
                    <input type="text" class="form-control" id="upd_module_name" name="upd_module_name" placeholder="Enter Module Name">
                    <input type="hidden" name="upd_id_module" id="upd_id_module" readonly>
                </div>
                <div class="form-group">
                    <label>Label</label>
                    <input type="text" class="form-control" id="upd_label" name="upd_label" placeholder="Enter Label">
                </div>
                <div class="form-group">
                    <label>Module Icon</label>
                    <input type="text" class="form-control" id="upd_icon" name="upd_icon" placeholder="Enter Icon">
                </div>
                <div class="form-group">
                    <label>URL</label>
                    <input type="text" class="form-control" id="upd_url" name="upd_url" placeholder="Enter URL">
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select class="form-control" id="upd_kategori" name="upd_kategori">
                        <option value="MANAJEMEN">Manajemen</option>
                        <option value="PELAYANAN">Pelayanan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Urutan</label>
                    <input type="number" class="form-control" id="upd_urutan" name="upd_urutan" placeholder="Enter Urutan">
                </div>
            <?php echo form_close(); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="buttonUpdateModule">Simpan</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
  
  <?php
  $this->load->view('moduleJs');
  $this->load->view('footer');
  ?>