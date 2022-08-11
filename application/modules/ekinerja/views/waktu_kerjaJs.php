<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
    var table_waktukerja;

$(document).ready(function(){
    table_waktukerja = $('#table_waktu_kerja').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "ekinerja/waktu_kerja/ajax_list_waktu_kerja",
          "type": "GET"
      },
      //Set column definition initialisation properties.
      "columnDefs": [
      { 
        "targets": [ -1,0 ], //last column
        "orderable": false //set not orderable
      }
      ]

    });
    
    $('.blnpicker').datepicker({
//        format: 'dd-mm-yyyy',
        orientation: 'bottom',
        autoclose: 'true',
        format: "M yyyy",
        startView: "months", 
        minViewMode: "months"
    });
    
    $('button#reloadWaktuKerja').click(function(){
        table_waktukerja.columns.adjust().draw();
    });
    
    $('button#btnSaveWaktuKerja').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                    url: ci_baseurl + "ekinerja/waktu_kerja/do_insert_waktu_kerja",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formWaktuKerja').serialize(),
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        $('input[name='+data.csrfTokenName+'_del]').val(data.csrfHash);
                        if(ret === true) {
                            $('#notification_type').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#notification_message').html(data.messages);
                            $('#notification_type').show();
                            $("#notification_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#notification_type").hide();
                            });
                            //clearForm();
                            $("html, body").animate({scrollTop: 100}, "fast");
                            table_waktukerja.columns.adjust().draw();
                            clearForm();
                        } else {
                            $('#notification_type').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
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
        });
    });
});

function clearForm(){
    $('#formWaktuKerja').find("input[type=text]").val("");
    $('#formWaktuKerja').find("select").prop('selectedIndex',0);
}

function editWaktu(bln){
    if(bln !==''){		
        $.ajax({
        url: ci_baseurl + "ekinerja/waktu_kerja/ajax_get_waktukerja_by_bln",
        type: 'get',
        dataType: 'json',
        data: {bln:bln},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#bulan').val(data.bln);
                    $('#jml_hari').val(data.data.jml_hari);
                    $('#menit_per_hari').val(data.data.menit_per_hari);
                } else {
                    $('#notification_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
                    $('#notification_message').html(data.messages);
                    $('#notification_type').show();
                }
            }
        });
    }
    else
    {
        $('#notification_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
        $('#notification_messages').html('Waktu tidak ditemukan');
        $('#notification_type').show();
    }
}

function hapusWaktu(bln){
    var jsonVariable = {};
    jsonVariable["bln"] = bln;
    jsonVariable[csrf_name] = $('#'+csrf_name+'_del').val();;
    if(bln !==''){
        jConfirm("Anda yakin akan menghapus data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                url: ci_baseurl + "ekinerja/waktu_kerja/do_delete_waktu_kerja",
                type: 'post',
                dataType: 'json',
                data: jsonVariable,
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        $('input[name='+data.csrfTokenName+'_del]').val(data.csrfHash);
                        if(ret === true) {
                            $('#notification_type').removeClass('alert alert-danger alert-dismissable').addClass('alert alert-success alert-dismissable');
                            $('#notification_message').html(data.messages);
                            $("#notification_type").show();
                            $("#notification_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#notification_type").hide();
                            });
//                            $('button#reloadProduk').click();
                            table_waktukerja.columns.adjust().draw();
                        } else {
                            $('#notification_type').removeClass('alert alert-success alert-dismissable').addClass('alert alert-danger alert-dismissable');
                            $('#notification_message').html(data.messages);
                            $("#notification_type").show();
                            $("#notification_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#notification_type").hide();
                            });
                        }
                    }
                });
            }
        });
    }
    else
    {
        $('#notification_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
        $('#notification_messages').html('Waktu kerja tidak ditemukan');
        $('#notification_type').show();
    }
}
</script>