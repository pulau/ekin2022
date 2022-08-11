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
            <small>Permission Management</small>
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
                <h3 class="box-title">Permission List</h3>
                <div class="box-tools"><button type="button" class="btn btn-block btn-success" data-toggle="modal" data-target="#tambah-permission-modal"><i class="fa fa-plus"></i> Tambah Permission</button></div>
                <input type="hidden" id="<?php echo $this->security->get_csrf_token_name()?>_del" name="<?php echo $this->security->get_csrf_token_name()?>_del" value="<?php echo $this->security->get_csrf_hash()?>" />
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                  <div id="notification_type" class="alert alert-dismissable" style="display:none;">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                      <p id="notification_message"></p>
                  </div>
                  <table id="permission_table" class="table table-bordered table-striped table-hover">
                      <thead>
                      <tr>
                          <th>No</th>
                          <th>Permission</th>
                          <th>Definition</th>
                          <th>URL</th>
                          <th>Parent</th>
                          <th>Module</th>
                          <th>Urutan</th>
                          <th>Action</th>
                      </tr>
                      </thead>
                      <tbody>
                          <tr>
                              <td colspan="8"> No Data to Display</td>
                          </tr>
                      </tbody>
                      <tfoot>
                      <tr>
                          <th>No</th>
                          <th>Permission</th>
                          <th>Definition</th>
                          <th>URL</th>
                          <th>Parent</th>
                          <th>Module</th>
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
  
  <div class="modal fade" id="tambah-permission-modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Input New Permission</h4>
        </div>
        <div class="modal-body">
            <div id="alert_type" class="alert alert-dismissable" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p id="alert_message"></p>
            </div>
            <?php echo form_open('#',array('id' => 'fmCreatePermission','role'=> 'form'))?>
                <div class="form-group">
                    <label>Permission Name</label>
                    <input type="text" class="form-control" id="perm_name" name="perm_name" placeholder="Enter Permission Name">
                </div>
                <div class="form-group">
                    <label>Definition</label>
                    <input type="text" class="form-control" id="definition" name="definition" placeholder="Enter Definition">
                </div>
                <div class="form-group">
                    <label>Menu Icon</label>
                    <input type="text" class="form-control" id="icon" name="icon" placeholder="Enter Icon">
                </div>
                <div class="form-group">
                    <label>URL</label>
                    <input type="text" class="form-control" id="url" name="url" placeholder="Enter URL">
                </div>
                <div class="form-group">
                    <label>Parent</label>
                    <select class="form-control" id="parent" name="parent">
                        <?php 
                        $parents = $this->Permission_model->perm_parent_list();
                        echo "<option value='0'>Tanpa Parent</option>";
                        foreach ($parents as $parent){
                            echo "<option value='".$parent->id."'>".$parent->name."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Module</label>
                    <select class="form-control" id="module" name="module">
                        <?php 
                        $modules = $this->Permission_model->module_list();
                        foreach ($modules as $module){
                            echo "<option value='".$module->id."'>".$module->label."</option>";
                        }
                        ?>
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
          <button type="button" class="btn btn-primary" id="buttonSimpanPerm">Simpan</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="update-permission-modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Update Permission</h4>
        </div>
        <div class="modal-body">
            <div id="upd_alert_type" class="alert alert-dismissable" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p id="upd_alert_message"></p>
            </div>
            <?php echo form_open('#',array('id' => 'fmUpdatePermission','role'=> 'form'))?>
                <div class="form-group">
                    <label>Permission Name</label>
                    <input type="text" class="form-control" id="upd_perm_name" name="upd_perm_name" placeholder="Enter Permission Name">
                    <input type="hidden" name="upd_id_perm" id="upd_id_perm" readonly>
                </div>
                <div class="form-group">
                    <label>Definition</label>
                    <input type="text" class="form-control" id="upd_definition" name="upd_definition" placeholder="Enter Definition">
                </div>
                <div class="form-group">
                    <label>Menu Icon</label>
                    <input type="text" class="form-control" id="upd_icon" name="upd_icon" placeholder="Enter Icon">
                </div>
                <div class="form-group">
                    <label>URL</label>
                    <input type="text" class="form-control" id="upd_url" name="upd_url" placeholder="Enter URL">
                </div>
                <div class="form-group">
                    <label>Parent</label>
                    <select class="form-control" id="upd_parent" name="upd_parent">
                        <?php 
                        $upd_parents = $this->Permission_model->perm_parent_list();
                        echo "<option value='0'>Tanpa Parent</option>";
                        foreach ($upd_parents as $upd_parent){
                            echo "<option value='".$upd_parent->id."'>".$upd_parent->name."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Module</label>
                    <select class="form-control" id="upd_module" name="upd_module">
                        <?php 
                        $upd_modules = $this->Permission_model->module_list();
                        foreach ($upd_modules as $upd_module){
                            echo "<option value='".$upd_module->id."'>".$upd_module->label."</option>";
                        }
                        ?>
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
          <button type="button" class="btn btn-primary" id="buttonUpdatePerm">Simpan</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
  
  <?php
  $this->load->view('permissionJs');
  $this->load->view('footer');
  ?>