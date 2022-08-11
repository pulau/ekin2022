<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
$(document).ready(function(){
    table_skp = $('#table_skp').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "ekinerja/master_skp/ajax_list_skp",
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
    
    table_skp.columns.adjust().draw(); 
    
    $('button#btnSaveSKP').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                var kd_skp = $('#kd_skp').val();
                if(kd_skp == ''){
                    var used_url = ci_baseurl + "ekinerja/master_skp/do_insert_skp";
                }else{
                    var used_url = ci_baseurl + "ekinerja/master_skp/do_update_skp";
                }
                $.ajax({
                    url: used_url,
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formSKP').serialize(),
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        $('input[name='+data.csrfTokenName+'_del]').val(data.csrfHash);
                        if(ret === true) {
                            $('#notification_type').removeClass('alert alert-dismissable').addClass('alert alert-success alert-dismissable');
                            $('#notification_message').html(data.messages);
                            $('#notification_type').show();
                            $("#notification_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#notification_type").hide();
                            });
                            clearForm();
                        table_skp.columns.adjust().draw();
                        } else {
                            $('#notification_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
                            $('#notification_message').html(data.messages);
                            $('#notification_type').show();
                            $("#notification_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#notification_type").hide();
                            });
                        }
                    }
                });
            }
        });
    });
    
    $('button#btnImportFile').click( function() {
        jConfirm("Anda yakin akan meng-import data ini ?","Ok","Cancel", function(r){
            if(r){
                $('#spinnerModal').modal('show');
                var formData = new FormData($('#formImport')[0]);
                $.ajax({
                    url: ci_baseurl + "ekinerja/master_skp/do_import_skp",
                    type: 'POST',
                    dataType: 'JSON',
                    //data: $('form#formPegawaiUkpd').serialize(),
                    data : formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        $('input[name='+data.csrfTokenName+'_del]').val(data.csrfHash);
                        if(ret === true) {
                            $('#spinnerModal').modal('hide');
                            $('#alert_type_import').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#alert_message_import').html(data.messages);
                            $('#alert_type_import').show();
                            $("#alert_type_import").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type_import").hide();
                            });
                            //clearForm();
                            $("html, body").animate({scrollTop: 100}, "fast");
                        table_skp.columns.adjust().draw();
                        } else {
                            $('#spinnerModal').modal('hide');
                            $('#alert_type_import').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
                            $('#alert_message_import').html(data.messages);
                            $('#alert_type_import').show();
                            $("#alert_type_import").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type_import").hide();
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
    $('#formSKP').find("input[type=text]").val("");
    $('#formSKP').find("select").prop('selectedIndex',0);
    $('#kd_skp').val('');
    $('#skp_nama').val('');
}

function editSKP(kd_skp){
    if(kd_skp !==''){		
        $.ajax({
        url: ci_baseurl + "ekinerja/master_skp/ajax_get_skp_by_id",
        type: 'get',
        dataType: 'json',
        data: {kd_skp:kd_skp},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#kd_skp').val(data.data.kd_skp);
                    $('#skp_nama').val(data.data.skp);
                    $('#waktu').val(data.data.waktu);
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
        $('#notification_message').html('SKP tidak ditemukan');
        $('#notification_type').show();
    }
}
    
function hapusSKP(kd_skp){
    var jsonVariable = {};
    jsonVariable["kd_skp"] = kd_skp;
    jsonVariable[csrf_name] = $('#'+csrf_name+'_del').val();;
    if(kd_skp !==''){
        jConfirm("Anda yakin akan menghapus data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                url: ci_baseurl + "ekinerja/master_skp/do_delete_skp",
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
                            table_skp.columns.adjust().draw();
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
        $('#notification_message').html('SKP tidak ditemukan');
        $('#notification_type').show();
    }
}
</script>