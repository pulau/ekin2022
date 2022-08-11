<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
var ci_baseurl = '<?php echo base_url();?>';
var csrf_name = '<?php echo $this->security->get_csrf_token_name()?>';
var csrf_hash = '<?php echo $this->security->get_csrf_hash()?>';
var table_pendidikan;
$(document).ready(function(){
    table_pendidikan = $('#table_pendidikan').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,
      "paging":   false,
        "ordering": false,
        "info":     false,
        "bFilter": false,
        "autoWidth": false,
//      "sDom": '<"top">rt<"bottom"ip><"clear">',

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "login/profile/ajax_pendidikan_list",
          "type": "GET"
      }
    });
    //table_pendidikan.columns.adjust().draw();
//    $('#pendidikanTab').click(function(){
//        table_pendidikan.columns.adjust().draw();
//    });
    $('button#btnSavePegawai').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                var formData = new FormData($('#formPegawai')[0]);
                $.ajax({
                    url: ci_baseurl + "login/profile/do_update_bio",
                    type: 'POST',
                    dataType: 'JSON',
                    //data: $('form#formPegawai').serialize(),
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        if(ret === true) {
                            $('#notifbio_type').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#notifbio_message').html(data.messages);
                            $('#notifbio_type').show();
                            $("#notifbio_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#notifbio_type").hide();
                            });
                            $("html, body").animate({scrollTop: 100}, "fast");
                            location.reload();
                        } else {
                            $('#notifbio_type').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
                            $('#notifbio_message').html(data.messages);
                            $('#notifbio_type').show();
                            $("#notifbio_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#notifbio_type").hide();
                            });
                            $("html, body").animate({scrollTop: 100}, "fast");
                        }
                    }
                });
            }
        });
    });
    
    $('button#btnGantiPassword').click( function() {
        var pass = $('#upd_password').val();
        var conf_pass = $('#upd_conf_pass').val();
        if(pass !== conf_pass){
            $('#notifpass_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
            $('#notifpass_message').html('Konfirmasi password tidak sesuai');
            $('#notifpass_type').show();
            $("#notifpass_type").fadeTo(2000, 500).slideUp(500, function(){
                $("#notifpass_type").hide();
            });
            return false;
        }
        jConfirm("Anda yakin akan mengganti password ?","Ok","Cancel", function(r){
            if(r){
                //var formData = new FormData($('#formBioPegawai')[0]);
                $.ajax({
                    url: ci_baseurl + "login/profile/do_update_password",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formGantiPassword').serialize(),
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        $('input[name='+data.csrfTokenName+'_del]').val(data.csrfHash);
                        if(ret === true) {
                            $('#notifpass_type').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#notifpass_message').html(data.messages);
                            $('#notifpass_type').show();
                            $("#notifpass_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#notifpass_type").hide();
                            });
                            $("html, body").animate({scrollTop: 100}, "fast");
                            location.reload();
                        } else {
                            $('#notifpass_type').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
                            $('#notifpass_message').html(data.messages);
                            $('#notifpass_type').show();
                            $("#notifpass_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#notifpass_type").hide();
                            });
                            $("html, body").animate({scrollTop: 100}, "fast");
                        }
                    }
                });
            }
        });
    });
    
    $('button#btnSavePendidikan').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                var formData = new FormData($('#formPendidikan')[0]);
                $.ajax({
                    url: ci_baseurl + "login/profile/do_tambah_pendidikan",
                    type: 'POST',
                    dataType: 'JSON',
                    //data: $('form#formPegawai').serialize(),
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        if(ret === true) {
                            $('#alert_type_pendidikan').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#alert_message_pendidikan').html(data.messages);
                            $('#alert_type_pendidikan').show();
                            $("#alert_type_pendidikan").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type_pendidikan").hide();
                            });
                            $("html, body").animate({scrollTop: 100}, "fast");
                            table_pendidikan.columns.adjust().draw();
                        } else {
                            $('#alert_type_pendidikan').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
                            $('#alert_message_pendidikan').html(data.messages);
                            $('#alert_type_pendidikan').show();
                            $("#alert_type_pendidikan").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type_pendidikan").hide();
                            });
                            $("html, body").animate({scrollTop: 100}, "fast");
                        }
                    }
                });
            }
        });
    });
    
    $('button#btnUpdPendidikan').click( function() {
        jConfirm("Anda yakin akan mengubah data ini ?","Ok","Cancel", function(r){
            if(r){
                var formData = new FormData($('#formUpdPendidikan')[0]);
                $.ajax({
                    url: ci_baseurl + "login/profile/do_update_pendidikan",
                    type: 'POST',
                    dataType: 'JSON',
                    //data: $('form#formPegawai').serialize(),
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        if(ret === true) {
                            $('#alert_type_upd_pendidikan').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#alert_message_upd_pendidikan').html(data.messages);
                            $('#alert_type_upd_pendidikan').show();
                            $("#alert_type_upd_pendidikan").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type_upd_pendidikan").hide();
                            });
                            $("html, body").animate({scrollTop: 100}, "fast");
                            table_pendidikan.columns.adjust().draw();
                        } else {
                            $('#alert_type_upd_pendidikan').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
                            $('#alert_message_upd_pendidikan').html(data.messages);
                            $('#alert_type_upd_pendidikan').show();
                            $("#alert_type_upd_pendidikan").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type_upd_pendidikan").hide();
                            });
                            $("html, body").animate({scrollTop: 100}, "fast");
                        }
                    }
                });
            }
        });
    });
    
    $('.tgl-lahir').datepicker({
        format: 'dd-M-yyyy',
        orientation: 'bottom',
        autoclose: 'true',
    });
    
    $('.thnpicker').datepicker({
        orientation: 'bottom',
        autoclose: 'true',
        format: "yyyy",
        startView: "years", 
        minViewMode: "years"
    });
});

function editPendidikan(id_pendidikan){
    if(id_pendidikan !==''){		
        $.ajax({
        url: ci_baseurl + "login/profile/ajax_get_pendidikan_by_id",
        type: 'get',
        dataType: 'json',
        data: {id_pendidikan:id_pendidikan},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#upd_pendidikan_id').val(data.data.pendidikan_peg_id);
                    $('#upd_jenjang').val(data.data.jenjang_pendidikan);
                    $('#upd_nama_sekolah').val(data.data.nama_sekolah);
                    $('#upd_tahun_masuk').val(data.data.tahun_masuk);
                    $('#upd_tahun_lulus').val(data.data.tahun_lulus);
                    $('#upd_no_ijazah').val(data.data.no_ijazah);
                    $('#updatePendidikanModal').modal('toggle');
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
        $('#notifpend_message').html('Pendidikan pegawai tidak ditemukan');
        $('#notifpend_type').show();
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
                url: ci_baseurl + "login/profile/do_delete_pendidikan_pegawai",
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
                            table_pendidikan.columns.adjust().draw();
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
        $('#notifpend_message').html('ID Pendidikan Pegawai tidak ditemukan');
        $('#notifpend_type').show();
    }
}
</script>