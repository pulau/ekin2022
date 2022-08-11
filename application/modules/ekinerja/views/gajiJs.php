<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
    var table_gaji;

$(document).ready(function(){
    table_gaji = $('#table_gaji').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "ekinerja/gaji/ajax_list_gaji",
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
    
    $('button#btnSaveGaji').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                var gaji_id = $('#gaji_id').val();
                if(gaji_id == ''){
                    var used_url = ci_baseurl + "ekinerja/gaji/do_insert_gaji";
                }else{
                    var used_url = ci_baseurl + "ekinerja/gaji/do_update_gaji";
                }
                $.ajax({
                    url: used_url,
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formGaji').serialize(),
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
                            $("html, body").animate({scrollTop: 100}, "fast");
                            table_gaji.columns.adjust().draw();
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
    $('#formGaji').find("input[type=text]").val("");
    $('#formGaji').find("select").prop('selectedIndex',0);
}

function editGaji(gaji_id){
    if(gaji_id !==''){		
        $.ajax({
        url: ci_baseurl + "ekinerja/gaji/ajax_get_gaji_by_id",
        type: 'get',
        dataType: 'json',
        data: {gaji_id:gaji_id},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#mk_awal').val(data.mk_awal);
                    $('#mk_akhir').val(data.mk_akhir);
                    $('#gaji_id').val(data.data.gaji_id);
                    $('#pendidikan').val(data.data.pendidikan);
                    $('#gapok').val(data.data.nominal_gaji);
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
        $('#notification_message').html('Gaji tidak ditemukan');
        $('#notification_type').show();
    }
}

function hapusGaji(gaji_id){
    var jsonVariable = {};
    jsonVariable["gaji_id"] = gaji_id;
    jsonVariable[csrf_name] = $('#'+csrf_name+'_del').val();;
    if(gaji_id !==''){
        jConfirm("Anda yakin akan menghapus data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                url: ci_baseurl + "ekinerja/gaji/do_delete_gaji",
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
                            table_gaji.columns.adjust().draw();
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
        $('#notification_message').html('Penyerapan tidak ditemukan');
        $('#notification_type').show();
    }
}
</script>