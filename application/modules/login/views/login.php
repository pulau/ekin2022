<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>PKC Seribu Utara</title>
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url();?>dist/img/LOGO PKC KD.png">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo base_url();?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url();?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo base_url();?>bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url();?>dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo base_url();?>plugins/iCheck/square/blue.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style type="text/css">
  .bg { 
    background: url(./dist/img/bg.png) no-repeat center center fixed; 
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
  }
  </style>
</head>

<body class="hold-transition login-page bg">
    <h3 style="color: red" align="center"><strong></strong></h3>
<div class="login-box">
<!-- /.login-logo -->
  <div style="background-color: #34bdeb" class="login-box-body" style="border-radius: 5px;">
    <!-- <div class="row">
      <div class="col-md-4">
        <img src="<?= base_url()?>dist/img/logo.png" width="100%">
      </div>
      <div class="col-xs-8">
        <h4 class="login-box-msg"><b>Aplikasi Manajemen Internal Pegawai Puskesmas Kecamatan Kalideres</b></h4>
      </div>
    </div> -->
    <div class="row">
      <div class="col-md-12">
        <img src="<?= base_url()?>dist/img/ekin.png" width="95%" style="margin-bottom:2rem;display: block;
  margin-left: auto;
  margin-right: auto;">
      </div>
    </div>
    <?php
    echo form_open('login/do_login');
    ?>
    <?php
        $messages = $this->session->flashdata('messages');
        if($messages != ''){
            if(is_array($messages))
                echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><span>'.$messages[0].'</span></div>';
            else
                echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><span>'.$messages.'</span></div>';
        }
    ?>
      <div class="form-group has-feedback">
          <input type="text" name="username" class="form-control" placeholder="Username">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
          <input type="password" name="password" class="form-control" placeholder="Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox"> Remember Me
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
        </div>
        <!-- /.col -->
      </div>
    <?php
    echo form_close();
    ?>
    <!-- <div class="social-auth-links text-center">
      <p>- OR -</p>
      <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using
        Facebook</a>
      <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using
        Google+</a>
    </div>-->
    <!-- /.social-auth-links -->
    <a href="#">I forgot my password</a><br>
    <!--<a href="register.html" class="text-center">Register a new membership</a>-->
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
    
    <!-- <div class="clearfix"></div> -->
    <!-- <h3 style="color: blue;margin-top: 5rem;" align= center><strong><marquee>Silahkan Melakukan Penginputan Kinerja Anda Setelah Jam Pelayanan. Ekinerja Secara Otomatis Akan Terkunci Pada Tanggal 5 Pukul:00:00 wib, Pada Tiap Bulan.</marquee></strong></h3>
     --><div id="modal" class="modal fade" tabindex="-1" role="dialog" style="border-radius: 5px;">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background-color: yellow">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h2 class="modal-title text-center"><i class="fa fa-warning"> <b>PERHATIAN</b></i></h2>
        </div>
        <div class="modal-body" style="background-color: #34ebba">
          <h4 style="color: red" align="center"><strong>Pastikan Input E-kinerja Dengan Benar Agar Tidak Mengurangi Nilai Kinerja Anda. Input Aktivitas Sesuai Dengan Waktu Yang Telah Di Tentukan</strong></h4>
          <h4 style="color: blue" align="center"><strong>Silahkan Melakukan Penginputan Kinerja Anda Setelah Jam Pelayanan.</strong></h4>
          <p><center><h4 style="color: blue" align="center"> Ayo tertib lakukan gerakan 3M - Memakai Masker, Menjaga Jarak, Mencuci Tangan, </h4></center></p>
          <h4 style="color: red" align="center"><strong>Ekinerja Secara Otomatis Akan Terkunci Pada Tanggal 5 Pukul:24:00 wib, Pada Tiap Bulan.</strong></h4>
        </div>
        <div class="modal-footer" style="background-color: yellow">
          <button type="button" class="btn btn-success" data-dismiss="modal"><b>Close</b></button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
<!-- jQuery 3 -->
<script src="<?php echo base_url();?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url();?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="<?php echo base_url();?>plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
</script>
<script>
$('#modal').modal('show');
//   $(window).on('load',function(){
//     if (!sessionStorage.getItem('shown-modal')){
//          $('#modal').modal('show');
//            sessionStorage.setItem('shown-modal', 'true');
//   }
// });
</script>
</body>
</html>