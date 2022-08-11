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
            <small>SKP Tahunan</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url('co_panel'); ?>"><i class="fa fa-dashboard"></i> CPanel</a></li>
            <li><?php echo $bc_parent; ?></li><li class="active"><?php echo $bc_child; ?></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                      <h3 class="box-title">Capaian Kinerja Pegawai</h3>
                    </div>
                    <div class="box-header">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" name="filter_bln" id="filter_bln" class="form-control blnpicker" placeholder="M Y" value="<?php echo date('M Y'); ?>">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-danger" id="goFilter">Filter</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="callout callout-info">
                            <p><?php echo $pegawai->nama_pegawai." - "; echo $pegawai->nip." - "; echo isset($pegawai->nama_jabatan) ? $pegawai->nama_jabatan :""; ?></p>
                            <p><?php echo isset($pegawai->bagian_nama) ? $pegawai->bagian_nama :"-"; ?></p>
                            <p><?php echo isset($pegawai->tempattugas_nama) ? $pegawai->tempattugas_nama :"-"; ?></p>
                            <p>Capaian Serapan Anggaran : <span id="serapan_anggaran_label"><?= !empty($serapan->nilai) ? $serapan->nilai : "-" ?></span> %</p>
                        </div>
                      <div class="box-group">
                        <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                        <div class="panel box box-primary">
                          <div class="box-header with-border">
                            <h4 class="box-title">
                              Pencapaian Aktifitas Anda bulan <span id="bln_label1"> <?= date('M Y'); ?></span>
                            </h4>
                          </div>
                            <div class="box-body">
                                <table class="table table-bordered table-striped table-hover" id="table_validasi_aktifitas">
                                    <thead>
                                        <tr>
                                            <th style="width:15px;"> No </th>
                                            <th> Aktifitas </th>
                                            <th> Waktu Efektif </th>
                                            <th> Target per Bulan </th>
                                            <th> Jumlah Aktifitas </th>
                                            <th> Capaian </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="6"> No Data to Display</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" style="text-align:right">Total Capaian &nbsp;&nbsp;</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="panel box box-danger">
                          <div class="box-header with-border">
                            <h4 class="box-title">
                                Batas Maksimal Waktu Efektif dan Capaian Kinerja Pada bulan <span id="bln_label2"> <?= date('M Y'); ?></span>
                            </h4>
                          </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <table class="table table-striped table-bordered table-hover" id="table_capaian_efektif">
                                            <thead>
                                                <tr>
                                                    <th style="width:15px;"> No </th>
                                                    <th> Jenis Absensi </th>
                                                    <th> Jumlah Hari </th>
                                                    <th> Menit Penambah </th>
                                                    <th> Total Waktu Efektif</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="alert alert-success" role="alert">
                                            <!--<a class="alert-link">-->
                                                  <table class="" border="0" width="100%">
                                                    <tbody><tr>
                                                        <td align="left">TOTAL CAPAIAN WAKTU EFEKTIF :</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <h2>
                                                                <b><span id="label_capaianefektif">0</span> + <span id="label_tambahwaktu">0</span> =</b>
                                                            </h2>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center"><h2><b><span id="label_hasil_tambah">0</span></b></h2></td>
                                                    </tr>
                                                </tbody></table>
                                            <!--</a>-->
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <table class="table table-striped table-bordered table-hover" id="table_waktu_efektif">
                                            <thead>
                                                <tr>
                                                    <th style="width:15px;"> No </th>
                                                    <th> Jenis Absensi </th>
                                                    <th> Jumlah Hari </th>
                                                    <th> Menit pengurangan </th>
                                                    <th> Total Waktu Pengurangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="alert alert-danger" role="alert">
                                            <!--<a class="alert-link">-->
                                                  <table class="" border="0" width="100%">
                                                    <tbody><tr>
                                                            <td align="left">TOTAL BATAS MAKSIMAL WAKTU EFEKTIF :</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <h2>
                                                                <b><span id="label_waktuefektif">0</span> - <span id="label_tdkhadir">0</span> =</b>
                                                            </h2>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center"><h2><b><span id="label_hasil_waktu">0</span></b></h2></td>
                                                    </tr>
                                                </tbody></table>
                                            <!--</a>-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel box box-success">
                          <div class="box-header with-border">
                            <h4 class="box-title">
                                Metode Perhitungan Pencapaian Kinerja
                            </h4>
                          </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-sm-6 b-r">
                                        <div class="alert alert-warning" role="alert">
                                                <table class="" border="0">
                                                    <tbody>
                                                        <tr>
                                                            <td align="center" valign="middle" rowspan="2">Nilai Aktifitas &nbsp;&nbsp; = &nbsp;&nbsp;</td>
                                                            <td align="center" style="border-bottom:3px solid;">Min [ capaian , batas max ]</td>
                                                            <td align="center" valign="middle" rowspan="2">&nbsp; x 100 %</td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center">Total Waktu Efektif</td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right" valign="middle" rowspan="2"><br>&nbsp;&nbsp; = &nbsp;&nbsp;</td>
                                                            <td align="center"><br><span style="border-bottom:3px solid;" id="label_poin_capaian">0</span></td>
                                                            <td align="center" valign="middle" rowspan="2"><br>&nbsp; x 100 %</td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center"><span id="label_max_efektif">0</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right" valign="bottom" rowspan="2"><br>&nbsp;&nbsp; = &nbsp;&nbsp;</td>
                                                            <td align="center" valign="bottom" rowspan="2"><span id="label_persen_capaian">0</span> %</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                        </div>
                                        <div class="alert alert-info" role="alert">
                                                <table class="" border="0">
                                                    <tbody><tr>
                                                        <td align="left"><small>Capaian &nbsp;&nbsp; = &nbsp;&nbsp; Waktu efektif x Volume</small></td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left"><small>Batas Max &nbsp;&nbsp; = &nbsp;&nbsp; ( Hari Kerja x Jam Kerja ) - Pengurang Absen</small></td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left"><small>Waktu Efektif &nbsp;&nbsp; = &nbsp;&nbsp; ( Hari Kerja x Jam Kerja )</small></td>
                                                    </tr>
                                                </tbody></table>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 b-r">
                                        <div class="alert alert-info" role="alert">
                                                <table class="" border="0">
                                                    <tbody><tr>
                                                            <td align="left"><small>Hari Kerja : <span id="label_hari_kerja">0</span> hari</small></td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left"><small>Jam Kerja per Hari : <?= JML_MENIT_PER_HARI ?> menit</small></td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left"><small>Batas Maximal Jam Kerja : <span id="label_max_efektif2">0</span> menit</small></td>
                                                    </tr>
                                                </tbody></table>
                                        </div>
                                        <!-- ============================== -->
                                        <!-- ============================== -->

                                        <div class="alert alert-warning" role="alert">
                                                <p>Apabila Prestasi Kerja Efektif kurang dari 50% , maka 1 bulan tidak mendapatkan TKD pada bulan bersangkutan.</p>
                                                <table class="" border="0" style="font-size: 11px">
                                                    <tbody><tr>
                                                        <td align="center" valign="middle" rowspan="2">Prestasi Kerja Efektif = &nbsp;&nbsp;</td>
                                                        <td align="center" valign="middle" rowspan="2">&nbsp;  70 % &nbsp;  x &nbsp; </td>
                                                        <td align="center" style="border-bottom:3px solid;">Capaian</td>
                                                        <td align="center" rowspan="2" valign="middle">&nbsp; + 10 % x Perilaku</td>
                                                        <td align="center" rowspan="2" valign="middle">&nbsp; + 20 % x Serapan</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">Batas Max</td>
                                                    </tr>
                                                </tbody></table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel box box-primary">
                          <div class="box-header with-border">
                            <h4 class="box-title">
                              Rekap Nilai Capaian Kinerja Pada bulan <span id="bln_label3"> <?= date('M Y'); ?></span>
                            </h4>
                          </div>
                            <div class="box-body">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th style="width:17%;"> Nilai Aktifitas </th>
                                        <th style="width:17%;"> Aktifitas (bobot 70%) </th>
                                        <th style="width:17%;"> Perilaku (bobot 10%) </th>
                                        <th style="width:17%;"> Serapan (bobot 20%) </th>
                                        <th style="width:18%;"> Total Nilai </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td> <span id="label_nilai_aktifitas">0</span> %</td>
                                        <td> <span id="label_aktifitas70">0</span> %</td>
                                        <td> <span id="label_nilai_prilaku">0</span> %</td>
                                        <td> <span id="label_nilai_serapan">0</span> %</td>
                                        <td> <span id="label_nilai_total">0</span> %</td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                        </div>
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
  
  <?php
  $this->load->view('capaian_kinerjaJs');
  $this->load->view('footer');
  ?>