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
            <small>User Logs</small>
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
                <h3 class="box-title">User Logs</h3>
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                  <div id="notification_type" class="alert alert-dismissable" style="display:none;">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                      <p id="notification_message"></p>
                  </div>
                  <table id="userlogs_table" class="table table-bordered table-striped table-hover">
                      <thead>
                      <tr>
                          <th>No</th>
                          <th>Permission</th>
                          <th>Tanggal</th>
                          <th>Halaman</th>
                          <th>IP Address</th>
                          <th>User</th>
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
                          <th>Permission</th>
                          <th>Tanggal</th>
                          <th>Halaman</th>
                          <th>IP Address</th>
                          <th>User</th>
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
  
  <div class="modal fade" id="detail-userslog-modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Detail Log</h4>
        </div>
        <div class="modal-body">
            <dl>
              <dt>Tanggal</dt>
              <dd id="tanggal"></dd>
              <dt>User</dt>
              <dd id="user"></dd>
              <dt>IP Address</dt>
              <dd id="ipaddr"></dd>
              <dt>Permission</dt>
              <dd id="perm"></dd>
              <dt>Detail</dt>
              <dd id="perm_detail"></dd>
            </dl>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
  
  <?php
  $this->load->view('userlogsJs');
  $this->load->view('footer');
  ?>