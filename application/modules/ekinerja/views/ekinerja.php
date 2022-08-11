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
            E-Kinerja
            <small>Dashboard</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url('co_panel'); ?>"><i class="fa fa-dashboard"></i> CPanel</a></li>
            <li><?php echo $bc_parent; ?></li><li class="active"><?php echo $bc_child; ?></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <h1 align= center><strong>Selamat Datang Di E-Kinerja V.2. Non PNS 
        <br>Puskesmas Kecamatan Kepulauan Seribu Utara</br></strong></h1>
    <h3 style="color: red" align="center"><strong></strong></h3>
     <!--<div class="clearfix"></div>-->
     <h2 align= center><strong>Prosedur Penginputan E-kinerja :</strong></h2>
     <img src="dist/img/Langkah2 ekin.png" height=100% width=100% align= "middle">
     <h4 style="color: blue" align= center><strong><marquee>     Silahkan Melakukan Penginputan Kinerja Anda Setelah Jam Pelayanan. Ekinerjan Non PNS Secara Otomatis Akan Terkunci Pada Tanggal 5 Pukul. 00:00 wib, Pada Tiap Bulan.</marquee></strong></h4>
      <!-- Info boxes -->
        <div class="row">
            <div class="col-md-12 col-sm-6 col-xs-12">
                <div class="small-box bg-olive">
                    <div class="inner">
                      <h3>Jumlah Pegawai Non PNS</h3>

                      <p>230 Orang</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-ios-people"></i>
                    </div>
                    <a href="<?php echo base_url();?>loket/rm_kalideres" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="small-box bg-aqua-gradient">
                    <div class="inner">
                      <h3>PKC Seribu Utara</h3>

                      <p>180 Orang</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-ios-people"></i>
                    </div>
                    <a href="<?php echo base_url();?>loket/rm_semanan1" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- /.col -->

            <!-- fix for small devices only -->
            <div class="clearfix visible-sm-block"></div>

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="small-box bg-green">
                    <div class="inner">
                      <h3>PKM Harapan</h3>

                      <p>20 Orang</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-ios-people"></i>
                    </div>
                    <a href="<?php echo base_url();?>loket/rm_semanan1" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="small-box bg-yellow">
                    <div class="inner">
                      <h3>PKM Panggang</h3>

                      <p>30 Orang</p>
                    </div>
                    <div class="icon">
                      <i class="ion ion-ios-people"></i>
                    </div>
                    <a href="<?php echo base_url();?>loket/rm_kalideres1" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            
            <!-- /.col -->
        <!--</div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Pasien baru hari ini</h3>

                        <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="chart">
                          <canvas id="barChart" style="height:230px"></canvas>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <!--<script src="<?php //echo base_url();?>dist/js/pages/dashboard2.js"></script>-->
  <?php
  $this->load->view('ekinerjaJs');
  $this->load->view('footer');
  ?>