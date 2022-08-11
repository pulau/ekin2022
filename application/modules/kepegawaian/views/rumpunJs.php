<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
var table_rumpun;

$(document).ready(function(){
    table_rumpun = $('#table_rumpun').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "kepegawaian/rumpun_jabatan/ajax_list_rumpun",
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
    
    table_rumpun.columns.adjust().draw(); 
    
    $('button#reloadRumpun').click(function(){
        table_rumpun.columns.adjust().draw();
    });
    
    $('button#btnSaveRumpun').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                var rumpun_id = $('#rumpun_id').val();
                if(rumpun_id == ''){
                    var used_url = ci_baseurl + "kepegawaian/rumpun_jabatan/do_insert_rumpun";
                }else{
                    var used_url = ci_baseurl + "kepegawaian/rumpun_jabatan/do_update_rumpun";
                }
                $.ajax({
                    url: used_url,
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formRumpun').serialize(),
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
                            clearForm();
                        table_rumpun.columns.adjust().draw();
                        } else {
                            $('#notification_type').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
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
    $('#formRumpun').find("input[type=text]").val("");
    $('#formRumpun').find("select").prop('selectedIndex',0);
    $('#rumpun_id').val('');
}

function editRumpun(rumpun_id){
    if(rumpun_id !==''){		
        $.ajax({
        url: ci_baseurl + "kepegawaian/rumpun_jabatan/ajax_get_rumpun_by_id",
        type: 'get',
        dataType: 'json',
        data: {rumpun_id:rumpun_id},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#rumpun_id').val(data.data.rumpun_id);
                    $('#rumpun_nama').val(data.data.rumpun_nama);
                    $('#rumpun_nilai').val(data.data.rumpun_nilai);
                } else {
                    $('#notification_type').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
                    $('#notification_messages').html(data.messages);
                    $('#notification_type').show();
                }
            }
        });
    }
    else
    {
        $('#notification_type').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
        $('#notification_messages').html('Rumpun ID tidak ditemukan');
        $('#notification_type').show();
    }
}
    
function hapusRumpun(rumpun_id){
    var jsonVariable = {};
    jsonVariable["rumpun_id"] = rumpun_id;
    jsonVariable[csrf_name] = $('#'+csrf_name+'_del').val();;
    if(rumpun_id !==''){
        jConfirm("Anda yakin akan menghapus data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                url: ci_baseurl + "kepegawaian/rumpun_jabatan/do_delete_rumpun",
                type: 'post',
                dataType: 'json',
                data: jsonVariable,
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        $('input[name='+data.csrfTokenName+'_del]').val(data.csrfHash);
                        if(ret === true) {
                            $('#notification_type').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#notification_message').html(data.messages);
                            $("#notification_type").show();
                            $("#notification_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#notification_type").hide();
                            });
//                            $('button#reloadProduk').click();
                            table_rumpun.columns.adjust().draw();
                        } else {
                            $('#notification_type').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
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
        $('#notification_type').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
        $('#notification_messages').html('Missing ID Bidang Barang');
        $('#notification_type').show();
    }
}
</script>