<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php $this->load->view('header_cpanel'); ?>
<?php //$this->load->view('sidebar'); ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        404 Error
        <small>App Store PCK Seribu Utara</small>
      </h1>
      <ol class="breadcrumb">
        <li class="active"><a href="#"><i class="fa fa-gears"></i> 404 Error</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="error-page">
        <h2 class="headline text-yellow"> 404</h2>

        <div class="error-content">
          <h3><i class="fa fa-warning text-yellow"></i> Oops! Page not found.</h3>

          <p>
            Kami tidak dapat menemukan halaman yang anda cari.
            Mungkin, ada bisa <a href="<?php echo base_url(); ?>">kembali ke control panel</a>.
          </p>
        </div>
        <!-- /.error-content -->
      </div>
      <!-- /.error-page -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php
  $this->load->view('footer');
  ?>