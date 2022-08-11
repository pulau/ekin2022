<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
var table_kehadiran;

$(document).ready(function(){
    table_kehadiran = $('#table_kehadiran').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "ekinerja/absensi/ajax_list_absensi",
          "type": "GET",
          "data" : function(d){
                d.filter_bln = $("#filter_bln").val();
            }
      },
      //Set column definition initialisation properties.
      "columnDefs": [
      { 
        "targets": [ -1,0 ], //last column
        "orderable": false //set not orderable
      }
      ]
    });
    table_kehadiran.columns.adjust().draw();
    
    $('button#goFilter').click(function(){
        table_kehadiran.columns.adjust().draw();
    });
    
    $('.blnpicker').datepicker({
    //    format: 'dd-mm-yyyy',
        orientation: 'bottom',
        autoclose: 'true',
        format: "M yyyy",
        startView: "months", 
        minViewMode: "months"
    });
    
    $('button#btnSimpanHadir').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                $('#spinnerModal').modal('show');
                var formData = new FormData($('#formInputKehadiran')[0]);
                $.ajax({
                    url: ci_baseurl + "ekinerja/absensi/do_insert_absensi",
                    type: 'POST',
                    dataType: 'JSON',
                    //data: $('form#formPegawaiUkpd').serialize(),
                    data : formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        if(ret === true) {
                            $('#spinnerModal').modal('hide');
                            $('#alert_type').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#alert_message').html(data.messages);
                            $('#alert_type').show();
                            $("#alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type").hide();
                            });
                            //clearForm();
                            $("html, body").animate({scrollTop: 100}, "fast");
                            table_kehadiran.columns.adjust().draw();
                        } else {
                            $('#spinnerModal').modal('hide');
                            $('#alert_type').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
                            $('#alert_message').html(data.messages);
                            $('#alert_type').show();
                            $("#alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type").hide();
                            });
                            $("html, body").animate({scrollTop: 100}, "fast");
                        }
                    }
                });
            }
        });
    });
    
    $('button#btnUpdateAbsen').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                    url: ci_baseurl + "ekinerja/absensi/do_update_absensi",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formEditAbsensi').serialize(),
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        if(ret === true) {
                            $('#alert_type2').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#alert_message2').html(data.messages);
                            $('#alert_type2').show();
                            $("#alert_type2").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type2").hide();
                            });
                            //clearForm();
                            $("html, body").animate({scrollTop: 100}, "fast");
                            table_kehadiran.columns.adjust().draw();
                        } else {
                            $('#alert_type2').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
                            $('#alert_message2').html(data.messages);
                            $('#alert_type2').show();
                            $("#alert_type2").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type2").hide();
                            });
                            $("html, body").animate({scrollTop: 100}, "fast");
                        }
                    }
                });
            }
        });
    });
});

function editKehadiran(id_waktukurang, nip, nama_pegawai){
    if(id_waktukurang !==''){	
        $.ajax({
        url: ci_baseurl + "ekinerja/absensi/ajax_get_kehadiran_by_id",
        type: 'get',
        dataType: 'json',
        data: {id_waktukurang:id_waktukurang},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#nip').val(nip);
                    $('#nama_pegawai').val(nama_pegawai);
                    $('#bulan_upd').val(data.bulan);
                    $('#id_waktukurang').val(data.data.id_waktukurang);
                    $('#tanpa_alasan').val(data.data.tanpa_alasan);
                    $('#terlambat_menit').val(data.data.terlambat_menit);
                    $('#pulang_cepat_menit').val(data.data.pulang_cepat_menit);
                    $('#izin').val(data.data.izin);
                    $('#sakit').val(data.data.sakit);
                    $('#cuti_alasan_penting').val(data.data.cuti_alasan_penting);

                    $('#izin_setengah_hari').val(data.data.izin_setengah_hari);
                    $('#covid').val(data.data.covid);
                    $('#ranapc19').val(data.data.ranapc19);

                    $('#cuti_tahunan').val(data.data.cuti_tahunan);
                    $('#sakit_srt_dokter').val(data.data.sakit_srt_dokter);
                    $('#cuti_bersalin').val(data.data.cuti_bersalin);
                    $('#dinas_luar_akhir').val(data.data.dinas_luar_akhir);
                    $('#dinas_luar_awal').val(data.data.dinas_luar_awal);
                    $('#tidak_terbaca').val(data.data.tidak_terbaca);
                    $('#dinas_luar_penuh').val(data.data.dinas_luar_penuh);
                    $('#cuti_sakit').val(data.data.cuti_sakit);
                    $('#cuti_bersalin_ak3').val(data.data.cuti_bersalin_ak3);
                    $('#modalEditAbsensi').modal('toggle');
                } else {
                    $('#notification_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
                    $('#notification_message').html(data.messages);
                    $('#notification_type').show();
                    $("#notification_type").fadeTo(2000, 500).slideUp(500, function(){
                        $("#notification_type").hide();
                    });
                    $("html, body").animate({scrollTop: 100}, "fast");
                }
            }
        });
    }
    else
    {
        $('#notification_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
        $('#notification_message').html('Bagian ID tidak ditemukan');
        $('#notification_type').show();
        $("#notification_type").fadeTo(2000, 500).slideUp(500, function(){
            $("#notification_type").hide();
        });
        $("html, body").animate({scrollTop: 100}, "fast");
    }
}
</script>