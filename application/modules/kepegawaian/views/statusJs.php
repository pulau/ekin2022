<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
var table_status;

$(document).ready(function(){
    table_status = $('#table_status').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "kepegawaian/status_kawin/ajax_list_status",
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
    
    table_status.columns.adjust().draw();
    
    $('button#btnSaveStatus').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                var status_id = $('#status_id').val();
                if(status_id == ''){
                    var used_url = ci_baseurl + "kepegawaian/status_kawin/do_insert_status";
                }else{
                    var used_url = ci_baseurl + "kepegawaian/status_kawin/do_update_status";
                }
                $.ajax({
                    url: used_url,
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formStatus').serialize(),
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
                        table_status.columns.adjust().draw();
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
    $('#formStatus').find("input[type=text]").val("");
    $('#formStatus').find("select").prop('selectedIndex',0);
    $('#status_id').val('');
}

function editStatus(status_id){
    if(status_id !==''){		
        $.ajax({
        url: ci_baseurl + "kepegawaian/status_kawin/ajax_get_status_by_id",
        type: 'get',
        dataType: 'json',
        data: {status_id:status_id},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#status_id').val(data.data.statuspegawai_id);
                    $('#status_nama').val(data.data.statuspegawai_nama);
                    $('#nilai').val(data.data.nilai);
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
        $('#notification_messages').html('Status ID tidak ditemukan');
        $('#notification_type').show();
    }
}
    
function hapusStatus(status_id){
    var jsonVariable = {};
    jsonVariable["status_id"] = status_id;
    jsonVariable[csrf_name] = $('#'+csrf_name+'_del').val();;
    if(status_id !==''){
        jConfirm("Anda yakin akan menghapus data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                url: ci_baseurl + "kepegawaian/status_kawin/do_delete_status",
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
                            table_status.columns.adjust().draw();
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
        $('#notification_messages').html('Missing ID Status');
        $('#notification_type').show();
    }
}
</script>