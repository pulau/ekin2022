<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
var table_pendidikan;

$(document).ready(function(){
    table_pendidikan = $('#table_pendidikan').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "kepegawaian/pendidikan/ajax_list_pendidikan",
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
    
    table_pendidikan.columns.adjust().draw();
    
    $('button#btnSavePendidikan').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                var pendidikan_id = $('#pendidikan_id').val();
                if(pendidikan_id == ''){
                    var used_url = ci_baseurl + "kepegawaian/pendidikan/do_insert_pendidikan";
                }else{
                    var used_url = ci_baseurl + "kepegawaian/pendidikan/do_update_pendidikan";
                }
                $.ajax({
                    url: used_url,
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formPendidikan').serialize(),
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
                        table_pendidikan.columns.adjust().draw();
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
    $('#formPendidikan').find("input[type=text]").val("");
    $('#formPendidikan').find("select").prop('selectedIndex',0);
    $('#pendidikan_id').val('');
}

function editPendidikan(pendidikan_id){
    if(pendidikan_id !==''){		
        $.ajax({
        url: ci_baseurl + "kepegawaian/pendidikan/ajax_get_pendidikan_by_id",
        type: 'get',
        dataType: 'json',
        data: {pendidikan_id:pendidikan_id},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#pendidikan_id').val(data.data.pendidikan_id);
                    $('#pendidikan_nama').val(data.data.pendidikan_nama);
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
        $('#notification_messages').html('Pendidikan ID tidak ditemukan');
        $('#notification_type').show();
    }
}
    
function hapusPendidikan(pendidikan_id){
    var jsonVariable = {};
    jsonVariable["pendidikan_id"] = pendidikan_id;
    jsonVariable[csrf_name] = $('#'+csrf_name+'_del').val();;
    if(pendidikan_id !==''){
        jConfirm("Anda yakin akan menghapus data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                url: ci_baseurl + "kepegawaian/pendidikan/do_delete_pendidikan",
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
                            table_pendidikan.columns.adjust().draw();
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
        $('#notification_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
        $('#notification_messages').html('Missing ID Pendidikan');
        $('#notification_type').show();
    }
}
</script>