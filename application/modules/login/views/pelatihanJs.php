<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
var ci_baseurl = '<?php echo base_url();?>';
var csrf_name = '<?php echo $this->security->get_csrf_token_name()?>';
var csrf_hash = '<?php echo $this->security->get_csrf_hash()?>';
var table_pelatihan;

$(document).ready(function(){
     table_pelatihan = $('#table_pelatihan').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,
      "paging":   false,
        "ordering": false,
        "info":     false,
        "bFilter": false,
        "autoWidth": false,
      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "login/profile/ajax_pelatihan_list",
          "type": "GET"
      }
    });

    $('button#btnSavePelatihan').click(function(){
        //do save action here
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if (r) {
                var formData = new FormData($('#formPelatihan')[0]);
                $.ajax({
                    url: ci_baseurl + "login/profile/do_tambah_pelatihan",
                    type: 'POST',
                    dataType: 'JSON',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data){
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        if(ret === true) {
                            $('#alert_type_pelatihan').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#alert_message_pelatihan').html(data.messages);
                            $('#alert_type_pelatihan').show();
                            $("#alert_type_pelatihan").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type_pelatihan").hide();
                            });
                            $("html, body").animate({scrollTop: 100}, "fast");
                            table_pelatihan.columns.adjust().draw();
                        } else {
                            $('#alert_type_pelatihan').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
                            $('#alert_message_pelatihan').html(data.messages);
                            $('#alert_type_pelatihan').show();
                            $("#alert_type_pelatihan").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type_pelatihan").hide();
                            });
                            $("html, body").animate({scrollTop: 100}, "fast");
                        }
                    }
                });
            }
        })
        console.log('save pelatihan');
    });


    $('button#btnUpdPelatihan').click( function() {
        jConfirm("Anda yakin akan mengubah data ini ?","Ok","Cancel", function(r){
            if(r){
                var formData = new FormData($('#formUpdPelatihan')[0]);
                $.ajax({
                    url: ci_baseurl + "login/profile/do_update_pelatihan",
                    type: 'POST',
                    dataType: 'JSON',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        if(ret === true) {
                            $('#alert_type_upd_pelatihan').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#alert_message_upd_pelatihan').html(data.messages);
                            $('#alert_type_upd_pelatihan').show();
                            $("#alert_type_upd_pelatihan").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type_upd_pelatihan").hide();
                            });
                            $("html, body").animate({scrollTop: 100}, "fast");
                            table_pelatihan.columns.adjust().draw();
                        } else {
                            $('#alert_type_upd_pelatihan').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
                            $('#alert_message_upd_pelatihan').html(data.messages);
                            $('#alert_type_upd_pelatihan').show();
                            $("#alert_type_upd_pelatihan").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type_upd_pelatihan").hide();
                            });
                            $("html, body").animate({scrollTop: 100}, "fast");
                        }
                    }
                });
            }
        });
    });
});



function editPelatihan(id_pelatihan){
    if(id_pelatihan !==''){        
        $.ajax({
        url: ci_baseurl + "login/profile/ajax_get_pelatihan_by_id",
        type: 'get',
        dataType: 'json',
        data: {id_pelatihan:id_pelatihan},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#upd_pelatihan_id').val(data.data.pelatihan_pegawai_id);
                    $('#upd_nama_pelatihan').val(data.data.nama_pelatihan);
                    $('#upd_penyedia_pelatihan').val(data.data.penyedia_pelatihan);
                    $('#upd_waktu_pelatihan').val(data.data.waktu_pelatihan);
                    $('#upd_no_sertifikat').val(data.data.no_sertifikat);
                    $('#updatePelatihanModal').modal('toggle');
                } else {
                    $('#notifpend_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
                    $('#notifpend_message').html(data.messages);
                    $('#notifpend_type').show();
                }
            }
        });
    }
    else
    {
        $('#notifpend_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
        $('#notifpend_message').html('pelatihan pegawai tidak ditemukan');
        $('#notifpend_type').show();
    }
}

function hapusPelatihan(pelatihan_id){
    var jsonVariable = {};
    jsonVariable["pelatihan_id"] = pelatihan_id;
    jsonVariable[csrf_name] = $('#'+csrf_name+'_del').val();;
    if(pelatihan_id !==''){
        jConfirm("Anda yakin akan menghapus data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                url: ci_baseurl + "login/profile/do_delete_pelatihan_pegawai",
                type: 'post',
                dataType: 'json',
                data: jsonVariable,
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        $('input[name='+data.csrfTokenName+'_del]').val(data.csrfHash);
                        if(ret === true) {
                            $('#notifpend_type').removeClass('alert alert-danger alert-dismissable').addClass('alert alert-success alert-dismissable');
                            $('#notifpend_message').html(data.messages);
                            $("#notifpend_type").show();
                            $("#notifpend_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#notification_type").hide();
                            });
                            // $('button#reloadProduk').click();
                            table_pelatihan.columns.adjust().draw();
                        } else {
                            $('#notifpend_type').removeClass('alert alert-success alert-dismissable').addClass('alert alert-danger alert-dismissable');
                            $('#notifpend_message').html(data.messages);
                            $("#notifpend_type").show();
                            $("#notifpend_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#notifpend_type").hide();
                            });
                        }
                    }
                });
            }
        });
    }
    else
    {
        $('#notifpend_type').removeClass('alert alert-success alert-dismissable').addClass('alert alert-danger alert-dismissable');
        $('#notifpend_message').html('ID pelatihan Pegawai tidak ditemukan');
        $('#notifpend_type').show();
    }
}
</script>