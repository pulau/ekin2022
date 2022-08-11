<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
var table_inpro;

$(document).ready(function(){
    table_inpro = $('#table_inpro').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "perencanaan/indikator_program/ajax_list_inpro",
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
    
    table_inpro.columns.adjust().draw(); 
    
    $('button#btnSaveInpro').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                var inpro_id = $('#indikator_program_id').val();
                if(inpro_id == ''){
                    var used_url = ci_baseurl + "perencanaan/indikator_program/do_insert_inpro";
                }else{
                    var used_url = ci_baseurl + "perencanaan/indikator_program/do_update_inpro";
                }
                $.ajax({
                    url: used_url,
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formInpro').serialize(),
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
                        table_inpro.columns.adjust().draw();
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
    $('#formInpro').find("input[type=text]").val("");
    $('#formInpro').find("select").prop('selectedIndex',0);
    $('#indikator_program_id').val('');
}

function editInpro(inpro_id){
    if(inpro_id !==''){		
        $.ajax({
        url: ci_baseurl + "perencanaan/indikator_program/ajax_get_inpro_by_id",
        type: 'get',
        dataType: 'json',
        data: {inpro_id:inpro_id},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#indikator_program_id').val(data.data.indikator_id);
                    $('#indikator_program').val(data.data.indikator_program);
                    $('#program').val(data.data.program_id);
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
    
function hapusInpro(inpro_id){
    var jsonVariable = {};
    jsonVariable["inpro_id"] = inpro_id;
    jsonVariable[csrf_name] = $('#'+csrf_name+'_del').val();;
    if(inpro_id !==''){
        jConfirm("Anda yakin akan menghapus data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                url: ci_baseurl + "perencanaan/indikator_program/do_delete_inpro",
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
                            table_inpro.columns.adjust().draw();
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
</script>