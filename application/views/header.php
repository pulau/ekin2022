<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="Puskesmas, Kepulauan Seribu Utara" name="viewport" />
  <meta content="Puskesmas Kecamatan Kepulauan Seribu Utara" />
  <meta content="" name="author" />
  <title>Ekinkerja|Dashboard</title>
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url();?>dist/img/LOGO PKC KD.png">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo base_url();?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url();?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo base_url();?>bower_components/Ionicons/css/ionicons.min.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="<?php echo base_url();?>bower_components/jvectormap/jquery-jvectormap.css">
  <!-- daterange picker -->
  <link rel="stylesheet" href="<?php echo base_url();?>bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="<?php echo base_url();?>bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="<?php echo base_url();?>plugins/iCheck/all.css">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="<?php echo base_url();?>bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css">
  <!-- Bootstrap time Picker -->
  <link rel="stylesheet" href="<?php echo base_url();?>plugins/timepicker/bootstrap-timepicker.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url();?>bower_components/select2/dist/css/select2.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url();?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <!-- Alert JS -->
  <link rel="stylesheet" href="<?php echo base_url();?>plugins/alert/jquery.alerts.css">
  <!-- JsTree -->
  <link rel="stylesheet" href="<?php echo base_url();?>plugins/jstree/dist/themes/default/style.min.css">
  <!-- fullCalendar -->
  <link rel="stylesheet" href="<?php echo base_url();?>bower_components/fullcalendar/dist/fullcalendar.min.css">
  <link rel="stylesheet" href="<?php echo base_url();?>bower_components/fullcalendar/dist/fullcalendar.print.min.css" media="print">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url();?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo base_url();?>dist/css/customs.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo base_url();?>dist/css/skins/_all-skins.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-purple-light sidebar-mini">
    <!-- jQuery 3 -->
    <script src="<?php echo base_url();?>bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo base_url();?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="<?php echo base_url();?>bower_components/fastclick/lib/fastclick.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo base_url();?>dist/js/adminlte.min.js"></script>
    <!-- Select2 -->
    <script src="<?php echo base_url();?>bower_components/select2/dist/js/select2.full.min.js"></script>
    <!-- InputMask -->
    <script src="<?php echo base_url();?>plugins/input-mask/jquery.inputmask.js"></script>
    <script src="<?php echo base_url();?>plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
    <script src="<?php echo base_url();?>plugins/input-mask/jquery.inputmask.extensions.js"></script>
    <!-- date-range-picker -->
    <script src="<?php echo base_url();?>bower_components/moment/min/moment.min.js"></script>
    <script src="<?php echo base_url();?>bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- fullCalendar -->
    <script src="<?php echo base_url();?>bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
    <script src="<?php echo base_url();?>bower_components/fullcalendar/dist/locale/id.js"></script>
    <!-- bootstrap datepicker -->
    <script src="<?php echo base_url();?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <!-- bootstrap color picker -->
    <script src="<?php echo base_url();?>bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
    <!-- bootstrap time picker -->
    <script src="<?php echo base_url();?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <!-- iCheck 1.0.1 -->
    <script src="<?php echo base_url();?>plugins/iCheck/icheck.min.js"></script>
    <!-- Sparkline -->
    <script src="<?php echo base_url();?>bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
    <!-- jvectormap  -->
    <script src="<?php echo base_url();?>plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="<?php echo base_url();?>plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <!-- alert JS  -->
    <script src="<?php echo base_url();?>plugins/alert/jquery.alerts.js"></script>
    <!-- JS Tree  -->
    <script src="<?php echo base_url();?>plugins/jstree/dist/jstree.min.js"></script>
    <!-- DataTables -->
    <script src="<?php echo base_url();?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url();?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <!-- SlimScroll -->
    <script src="<?php echo base_url();?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <!-- ChartJS -->
    <script src="<?php echo base_url();?>bower_components/chart.js/Chart.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?php echo base_url();?>dist/js/demo.js"></script>
    
    <script language="javascript">
        var ci_baseurl = '<?php echo base_url();?>';
        var csrf_name = '<?php echo $this->security->get_csrf_token_name()?>';
        var csrf_hash = '<?php echo $this->security->get_csrf_hash()?>';
        
        $(function(e){
            /** add active class and stay opened when selected */
            var url = window.location;

            // for sidebar menu entirely but not cover treeview
            $('ul.sidebar-menu a').filter(function() {
               return this.href == url;
            }).parent().addClass('active');

            // for treeview
            $('ul.treeview-menu a').filter(function() {
               return this.href == url;
            }).parentsUntil(".sidebar-menu > .treeview-menu").addClass('active');
        });

        function numbersOnly(event){
            var charCode = (event.which) ? event.which : event.keyCode;
        //    console.log(charCode);
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
    </script>
   
<div class="wrapper">
    <div id="top">
      <img id="logo_puskes" src="<?php echo base_url();?>dist/img/logo_dki.png">
      <div id="pus_name">PUSKESMAS KEC. SERIBU UTARA<br></div>
      <img id="logo_intranet" class="hidden-xs" src="<?php echo base_url();?>dist/img/ekin.png">
    </div>
  <header class="main-header">

    <!-- Logo -->
    <a href="#" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>PKC</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg">PKC Seribu Utara</span>
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          
          <!-- Notifications: style can be found in dropdown.less -->
          <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning">10</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have 10 notifications</li>
              <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu">
                  <li>
                    <a href="#">
                      <i class="fa fa-users text-aqua"></i> 5 new members joined today
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <i class="fa fa-warning text-yellow"></i> Very long description here that may not fit into the
                      page and may cause design problems
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <i class="fa fa-users text-red"></i> 5 new members joined
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <i class="fa fa-shopping-cart text-green"></i> 25 sales made
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <i class="fa fa-user text-red"></i> You changed your username
                    </a>
                  </li>
                </ul>
              </li>
              <li class="footer"><a href="#">View all</a></li>
            </ul>
          </li>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <?php
                if(isset($pegawai->foto_url)){
                ?>
                    <img src="<?php echo base_url().$pegawai->foto_url;?>" class="user-image" alt="User Image"> </button>
                <?php
                }else{
                ?>
                    <img src="<?php echo base_url();?>dist/img/avatar04.png" class="user-image" alt="User Image"> </button>
                <?php
                }
                ?>
              <span class="hidden-xs"><?php echo $pegawai->nama_pegawai; ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <?php
                if(isset($pegawai->foto_url)){
                ?>
                    <img src="<?php echo base_url().$pegawai->foto_url;?>" class="img-circle" alt="User Image"> </button>
                <?php
                }else{
                ?>
                    <img src="<?php echo base_url();?>dist/img/avatar04.png" class="img-circle" alt="User Image"> </button>
                <?php
                }
                ?>
                <p>
                  <?php echo $pegawai->nama_pegawai; ?>
                  <small><?php echo $bagian->bagian_nama; ?></small>
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo base_url(); ?>login/profile" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo base_url(); ?>login/do_logout" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>

    </nav>
  </header>