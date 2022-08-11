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
            <small>Group Management</small>
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
              <h3 class="box-title">Group List</h3>
              <div class="box-tools"><button type="button" class="btn btn-block btn-success" data-toggle="modal" data-target="#tambah-group-modal" onclick="perm_create()"><i class="fa fa-plus"></i> Tambah Group</button></div>
              <input type="hidden" id="<?php echo $this->security->get_csrf_token_name()?>_del" name="<?php echo $this->security->get_csrf_token_name()?>_del" value="<?php echo $this->security->get_csrf_hash()?>" />
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div id="notification_type" class="alert alert-dismissable" style="display:none;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p id="notification_message"></p>
                </div>
                <table id="group_table" class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Group Name</th>
                        <th>Definition</th>
                        <th>Permission</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5"> No Data to Display</td>
                        </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Group Name</th>
                        <th>Definition</th>
                        <th>Permission</th>
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
  
<div class="modal fade" id="tambah-group-modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Input New Group</h4>
        </div>
        <div class="modal-body">
            <div id="alert_type" class="alert alert-dismissable" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p id="alert_message"></p>
            </div>
            <?php echo form_open('#',array('id' => 'fmCreateGroup','role'=> 'form'))?>
                <div class="form-group">
                  <label>Group Name</label>
                  <input type="text" class="form-control" id="group_name" name="group_name" placeholder="Enter Group Name">
                </div>
                <div class="form-group">
                  <label>Definition</label>
                  <textarea class="form-control" id="definition" name="definition" placeholder="Enter definition"></textarea>
                </div>
                <div class="form-group">
                    <label>Permission</label>
                    <fieldset>
                        <div id="perm_list">
                            &nbsp;
                        </div>
                    </fieldset>
                </div>
            <?php echo form_close(); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="buttonSimpanGroup">Simpan</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="update-group-modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Update Group</h4>
        </div>
        <div class="modal-body">
            <div id="upd_alert_type" class="alert alert-dismissable" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p id="upd_alert_message"></p>
            </div>
            <?php echo form_open('#',array('id' => 'fmUpdateGroup','role'=> 'form'))?>
                <div class="form-group">
                  <label>Group Name</label>
                  <input type="text" class="form-control" id="upd_group_name" name="upd_group_name" placeholder="Enter Group Name">
                  <input type="hidden" name="upd_group_id" id="upd_group_id" readonly>
                </div>
                <div class="form-group">
                  <label>Definition</label>
                  <textarea class="form-control" id="upd_definition" name="upd_definition" placeholder="Enter definition"></textarea>
                </div>
                <div class="form-group">
                    <label>Permission</label>
                    <fieldset>
                        <div id="upd_perm_list">
                            &nbsp;
                        </div>
                    </fieldset>
                </div>
            <?php echo form_close(); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="buttonUpdateGroup">Simpan</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
  <?php
  $this->load->view('groupJs');
  $this->load->view('footer');
  ?>