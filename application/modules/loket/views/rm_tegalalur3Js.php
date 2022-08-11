<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
var table_rm;
$(document).ready(function(){
    table_rm = $('#table_pasien').DataTable({ 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [[2,'desc']],
  //      "scrollX": true,

        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": ci_baseurl + "loket/rm_tegalalur3/ajax_list_mr",
            "type": "GET"
        },
        //Set column definition initialisation properties.
        "columnDefs": [
        { 
          "targets": [-1,0], //last column
          "orderable": false //set not orderable
        }
        ]
    });
    table_rm.columns.adjust().draw();
    
    $('button#btnSaveRM').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                var used_url = ci_baseurl + "loket/rm_tegalalur3/do_insert_mr";
                $.ajax({
                    url: used_url,
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formRM').serialize(),
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        //$('input[name='+data.csrfTokenName+'_del]').val(data.csrfHash);
                        if(ret === true) {
                            $('#notification_type').removeClass('alert alert-danger alert-dismissable').addClass('alert alert-success alert-dismissable');
                            $('#notification_message').html(data.messages);
                            $('#notification_type').show();
                            $("#notification_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#notification_type").hide();
                            });
                            clearForm();
                            table_rm.columns.adjust().draw();
                            $('#infoPasien').modal('toggle');
                            $('#pasien_nama_mod').html(data.nama_pasien);
                            $('#no_rm_mod').html(data.no_rm);
                        } else {
                            $('#notification_type').removeClass('alert alert-success alert-dismissable').addClass('alert alert-danger alert-dismissable');
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
    
    $('button#btnUpdateRM').click( function() {
        jConfirm("Anda yakin akan mengubah data ini ?","Ok","Cancel", function(r){
            if(r){
                var used_url = ci_baseurl + "loket/rm_tegalalur3/do_update_mr";
                $.ajax({
                    url: used_url,
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#fmUpdateRM').serialize(),
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        if(ret === true) {
                            $('#upd_alert_type').removeClass('alert alert-danger alert-dismissable').addClass('alert alert-success alert-dismissable');
                            $('#upd_alert_message').html(data.messages);
                            $('#upd_alert_type').show();
                            $("#upd_alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#upd_alert_type").hide();
                            });
                            table_rm.columns.adjust().draw();
                        } else {
                            $('#upd_alert_type').removeClass('alert alert-success alert-dismissable').addClass('alert alert-danger alert-dismissable');
                            $('#upd_alert_message').html(data.messages);
                            $('#upd_alert_type').show();
                            $("#upd_alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#upd_alert_type").hide();
                            });
                        }
                    }
                });
            }
        });
    });
    
    $('.tglpicker').datepicker({
        format: 'dd-mm-yyyy',
        orientation: 'bottom',
        autoclose: true
    });
});

function clearForm(){
    $('#formRM').find("input[type=text]").val("");
    $('#formRM').find("select").prop('selectedIndex',0);
}

function editPasien(rm_id){
    if(rm_id !==''){		
        $.ajax({
        url: ci_baseurl + "loket/rm_tegalalur3/ajax_get_rm_by_id",
        type: 'get',
        dataType: 'json',
        data: {rm_id:rm_id},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#upd_no_rm').val(data.data.no_rm);
                    $('#upd_rm_id').val(data.data.rm_id);
                    $('#upd_nama_pasien').val(data.data.nama_pasien);
                    $('#upd_tgl_lahir').val(data.tgl_lahir);
                    $('#update-pasien-modal').modal('toggle');
                } else {
                    $('#notification_type').removeClass('alert alert-success alert-dismissable').addClass('alert alert-danger alert-dismissable');
                    $('#notification_messages').html(data.messages);
                    $('#notification_type').show();
                }
            }
        });
    }
    else
    {
        $('#notification_type').removeClass('alert alert-success alert-dismissable').addClass('alert alert-danger alert-dismissable');
        $('#notification_messages').html('Pasien tidak ditemukan');
        $('#notification_type').show();
    }
}
</script>