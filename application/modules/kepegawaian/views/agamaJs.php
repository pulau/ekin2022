<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
var table_agama;

$(document).ready(function(){
    table_agama = $('#table_agama').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "kepegawaian/agama/ajax_list_agama",
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
    
    table_agama.columns.adjust().draw(); 
    
    $('button#btnSaveAgama').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                var agama_id = $('#agama_id').val();
                if(agama_id == ''){
                    var used_url = ci_baseurl + "kepegawaian/agama/do_insert_agama";
                }else{
                    var used_url = ci_baseurl + "kepegawaian/agama/do_update_agama";
                }
                $.ajax({
                    url: used_url,
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formAgama').serialize(),
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
                        table_agama.columns.adjust().draw();
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
    $('#formAgama').find("input[type=text]").val("");
    $('#formAgama').find("select").prop('selectedIndex',0);
    $('#agama_id').val('');
}

function editAgama(id_agama){
    if(id_agama !==''){		
        $.ajax({
        url: ci_baseurl + "kepegawaian/agama/ajax_get_agama_by_id",
        type: 'get',
        dataType: 'json',
        data: {id_agama:id_agama},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#agama_id').val(data.data.id_agama);
                    $('#agama_nama').val(data.data.nama_agama);
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
        $('#notification_messages').html('Agama ID tidak ditemukan');
        $('#notification_type').show();
    }
}
    
function hapusAgama(agama_id){
    var jsonVariable = {};
    jsonVariable["agama_id"] = agama_id;
    jsonVariable[csrf_name] = $('#'+csrf_name+'_del').val();;
    if(agama_id !==''){
        jConfirm("Anda yakin akan menghapus data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                url: ci_baseurl + "kepegawaian/agama/do_delete_agama",
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
                            table_agama.columns.adjust().draw();
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
        $('#notification_messages').html('Missing ID Agama');
        $('#notification_type').show();
    }
}
</script>