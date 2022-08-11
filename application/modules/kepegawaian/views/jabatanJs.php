<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
var table_jabatan;

$(document).ready(function(){
    table_jabatan = $('#table_jabatan').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "kepegawaian/jabatan/ajax_list_jabatan",
          "type": "GET",
      },
      //Set column definition initialisation properties.
      "columnDefs": [
      { 
        "targets": [ -1,0 ], //last column
        "orderable": false, //set not orderable
      },
      ],

    });
    
    table_jabatan.columns.adjust().draw(); 
    
    $('button#btnSaveJabatan').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                var jabatan_id = $('#jabatan_id').val();
                if(jabatan_id == ''){
                    var used_url = ci_baseurl + "kepegawaian/jabatan/do_insert_jabatan";
                }else{
                    var used_url = ci_baseurl + "kepegawaian/jabatan/do_update_jabatan";
                }
                $.ajax({
                    url: used_url,
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formJabatan').serialize(),
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
                        table_jabatan.columns.adjust().draw();
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
    $('#formJabatan').find("input[type=text]").val("");
    $('#formJabatan').find("select").prop('selectedIndex',0);
    $('#jabatan_id').val('');
}

function editJabatan(id_jabatan){
    if(id_jabatan !==''){		
        $.ajax({
        url: ci_baseurl + "kepegawaian/jabatan/ajax_get_jabatan_by_id",
        type: 'get',
        dataType: 'json',
        data: {id_jabatan:id_jabatan},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#jabatan_id').val(data.data.id_jabatan);
                    $('#jabatan_nama').val(data.data.nama_jabatan);
                } else {
                    $('#notification_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
                    $('#notification_messages').html(data.messages);
                    $('#notification_type').show();
                }
            }
        });
    }
    else
    {
        $('#notification_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
        $('#notification_messages').html('Jabatan ID tidak ditemukan');
        $('#notification_type').show();
    }
}
    
function hapusJabatan(jabatan_id){
    var jsonVariable = {};
    jsonVariable["jabatan_id"] = jabatan_id;
    jsonVariable[csrf_name] = $('#'+csrf_name+'_del').val();;
    if(jabatan_id !==''){
        jConfirm("Anda yakin akan menghapus data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                url: ci_baseurl + "kepegawaian/jabatan/do_delete_jabatan",
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
                            table_jabatan.columns.adjust().draw();
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
        $('#notification_messages').html('Missing ID Jabatan');
        $('#notification_type').show();
    }
}
</script>