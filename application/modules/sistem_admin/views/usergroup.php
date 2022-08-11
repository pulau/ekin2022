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
            <small>User to Group Management</small>
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
              <h3 class="box-title">User to Group</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div id="notification_type" class="alert alert-dismissable" style="display:none;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p id="notification_message"></p>
                </div>
                <table id="usergroup_table" class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama User</th>
                        <th>Username Login</th>
                        <th>Group List</th>
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
                        <th>Nama User</th>
                        <th>Username Login</th>
                        <th>Group List</th>
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

<div class="modal fade" id="update-usergroup-modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Update User to Group</h4>
        </div>
        <div class="modal-body">
            <div id="upd_alert_type" class="alert alert-dismissable" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p id="upd_alert_message"></p>
            </div>
            <?php echo form_open('#',array('id' => 'fmUpdateUserGroup','role'=> 'form'))?>
                <div class="form-group">
                  <label>User Name</label>
                  <input type="text" class="form-control" id="username" name="username" readonly>
                  <input type="hidden" name="user_id" id="user_id" readonly>
                </div>
                <div class="form-group">
                    <label>Group</label>
                    <fieldset>
                        <div id="group_list">
                            &nbsp;
                        </div>
                    </fieldset>
                </div>
            <?php echo form_close(); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="buttonSimpanUserGroup">Simpan</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
  <?php
  $this->load->view('usergroupJs');
  $this->load->view('footer');
  ?>