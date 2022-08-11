<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
var table_bagian;

$(document).ready(function(){
    table_bagian = $('#table_bagian').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "kepegawaian/bagian/ajax_list_bagian",
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
    
    table_bagian.columns.adjust().draw(); 
    
    $('button#btnSaveBagian').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                var bagian_id = $('#bagian_id').val();
                if(bagian_id == ''){
                    var used_url = ci_baseurl + "kepegawaian/bagian/do_insert_bagian";
                }else{
                    var used_url = ci_baseurl + "kepegawaian/bagian/do_update_bagian";
                }
                $.ajax({
                    url: used_url,
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formBagian').serialize(),
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
                        table_bagian.columns.adjust().draw();
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
    
    $('.select2').select2({
        placeholder: 'Pilih Koordinator',
        //width: null,
//        dropdownParent: $('.modal')
    });
});

function clearForm(){
    $('#formBagian').find("input[type=text]").val("");
    $('#formBagian').find("select").prop('selectedIndex',0);
    $('#bagian_id').val('');
}

function editBagian(bagian_id){
    if(bagian_id !==''){		
        $.ajax({
        url: ci_baseurl + "kepegawaian/bagian/ajax_get_bagian_by_id",
        type: 'get',
        dataType: 'json',
        data: {bagian_id:bagian_id},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#bagian_id').val(data.data.bagian_id);
                    $('#bagian_nama').val(data.data.bagian_nama);
                    $('#honorshift').val(data.data.id_honorshift);
                    $('#koordinator').val(data.data.kordinator_id);
                    $('#pj_cuti').val(data.data.pj_cuti);
                    //$("#kordinator").append("<option value='"+data.data.kordinator_id+"' selected>"+item+"</option>");
                    //$('#kordinator').trigger('change');
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
        $('#notification_messages').html('Bagian ID tidak ditemukan');
        $('#notification_type').show();
    }
}
    
function hapusBagian(bagian_id){
    var jsonVariable = {};
    jsonVariable["bagian_id"] = bagian_id;
    jsonVariable[csrf_name] = $('#'+csrf_name+'_del').val();;
    if(bagian_id !==''){
        jConfirm("Anda yakin akan menghapus data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                url: ci_baseurl + "kepegawaian/bagian/do_delete_bagian",
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
                            table_bagian.columns.adjust().draw();
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
        $('#notification_messages').html('Missing ID Bagian');
        $('#notification_type').show();
    }
}
</script>