<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
var table_kegiatan;

$(document).ready(function(){
    table_kegiatan = $('#table_kegiatan').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "perencanaan/kegiatan/ajax_list_kegiatan",
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
    
    table_kegiatan.columns.adjust().draw(); 
    
    $('button#btnSaveKegiatan').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                var inpro_id = $('#kegiatan_id').val();
                if(inpro_id == ''){
                    var used_url = ci_baseurl + "perencanaan/kegiatan/do_insert_kegiatan";
                }else{
                    var used_url = ci_baseurl + "perencanaan/kegiatan/do_update_kegiatan";
                }
                $.ajax({
                    url: used_url,
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formKegiatan').serialize(),
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
                        table_kegiatan.columns.adjust().draw();
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
});

function clearForm(){
    $('#formKegiatan').find("input[type=text]").val("");
    $('#formKegiatan').find("select").prop('selectedIndex',0);
    $('#kegiatan_id').val('');
}

function editKegiatan(kegiatan_id){
    if(kegiatan_id !==''){		
        $.ajax({
        url: ci_baseurl + "perencanaan/kegiatan/ajax_get_kegiatan_by_id",
        type: 'get',
        dataType: 'json',
        data: {kegiatan_id:kegiatan_id},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#kegiatan_id').val(data.data.kegiatan_id);
                    $('#kegiatan').val(data.data.kegiatan_nama);
                    $('#program').val(data.program_id);
                    $("#inpro").html(data.list_inpro);
                    $("#inpro").val(data.data.indikator_id);
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
        $('#notification_message').html('Indikator Program ID tidak ditemukan');
        $('#notification_type').show();
    }
}
    
function hapusKegiatan(kegiatan_id){
    var jsonVariable = {};
    jsonVariable["kegiatan_id"] = kegiatan_id;
    jsonVariable[csrf_name] = $('#'+csrf_name+'_del').val();;
    if(kegiatan_id !==''){
        jConfirm("Anda yakin akan menghapus data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                url: ci_baseurl + "perencanaan/kegiatan/do_delete_kegiatan",
                type: 'post',
                dataType: 'json',
                data: jsonVariable,
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        $('input[name='+data.csrfTokenName+'_del]').val(data.csrfHash);
                        if(ret === true) {
                            $('#notification_type').removeClass('alert alert-dismissable').addClass('alert alert-success alert-dismissable');
                            $('#notification_message').html(data.messages);
                            $("#notification_type").show();
                            $("#notification_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#notification_type").hide();
                            });
//                            $('button#reloadProduk').click();
                            table_kegiatan.columns.adjust().draw();
                        } else {
                            $('#notification_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
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
        $('#notification_message').html('Missing ID Indikator Program');
        $('#notification_type').show();
    }
}

function setInpro(){
    var program = $("#program").val();
    if(program != ''){
        $.ajax({
            url: ci_baseurl + "perencanaan/kegiatan/set_inpro",
            type: 'GET',
            dataType: 'JSON',
            data: {program:program},
            success: function(data) {
                $("#inpro").html(data.list_inpro);
            }
        });
    }
}
</script>