<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
var table_pegawai;

$(document).ready(function(){
    table_pegawai = $('#table_pegawai').DataTable({ 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
  //      "scrollX": true,

        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": ci_baseurl + "kepegawaian/pegawai/ajax_list_pegawai",
            "type": "GET"
        },
        //Set column definition initialisation properties.
        "columnDefs": [
        { 
          "targets": [ -1,0 ], //last column
          "orderable": false //set not orderable
        },
        { "width": "3%", "targets": 0 }
        ]
    });
    
    table_pegawai.columns.adjust().draw(); 
    
    $('button#btnSavePegawai').click( function() {
        var pass = $('#password').val();
        var conf_pass = $('#conf_pass').val();
        if(pass !== conf_pass){
            $('#alert_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
            $('#alert_message').html('Konfirmasi password tidak sesuai');
            $('#alert_type').show();
            $("#alert_type").fadeTo(2000, 500).slideUp(500, function(){
                $("#alert_type").hide();
            });
            return false;
        }
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                var formData = new FormData($('#formPegawai')[0]);
                $.ajax({
                    url: ci_baseurl + "kepegawaian/pegawai/do_insert_pegawai",
                    type: 'POST',
                    dataType: 'JSON',
                    //data: $('form#formPegawaiUkpd').serialize(),
                    data : formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        var ret = data.success;
                        console.log(formData);
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        $('input[name='+data.csrfTokenName+'_del]').val(data.csrfHash);
                        if(ret === true) {
                            $('#alert_type').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#alert_message').html(data.messages);
                            $('#alert_type').show();
                            $("#alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type").hide();
                            });
                            clearForm();
                            $("html, body").animate({scrollTop: 100}, "fast");
                        table_pegawai.columns.adjust().draw();
                        } else {
                            $('#alert_type').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
                            $('#alert_message').html(data.messages);
                            $('#alert_type').show();
                            $("#alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type").hide();
                            });
                            $("html, body").animate({scrollTop: 100}, "fast");
                        }
                    }
                });
            }
        });
    });
    
    $('button#btnImportFile').click( function() {
        jConfirm("Anda yakin akan meng-import data ini ?","Ok","Cancel", function(r){
            if(r){
                $('#spinnerModal').modal('show');
                var formData = new FormData($('#formImport')[0]);
                $.ajax({
                    url: ci_baseurl + "kepegawaian/pegawai/do_import_pegawai",
                    type: 'POST',
                    dataType: 'JSON',
                    //data: $('form#formPegawaiUkpd').serialize(),
                    data : formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        $('input[name='+data.csrfTokenName+'_del]').val(data.csrfHash);
                        if(ret === true) {
                            $('#spinnerModal').modal('hide');
                            $('#alert_type_import').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#alert_message_import').html(data.messages);
                            $('#alert_type_import').show();
                            $("#alert_type_import").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type_import").hide();
                            });
                            clearForm();
                            $("html, body").animate({scrollTop: 100}, "fast");
                        table_pegawai.columns.adjust().draw();
                        } else {
                            $('#spinnerModal').modal('hide');
                            $('#alert_type_import').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
                            $('#alert_message_import').html(data.messages);
                            $('#alert_type_import').show();
                            $("#alert_type_import").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type_import").hide();
                            });
                            $("html, body").animate({scrollTop: 100}, "fast");
                        }
                    }
                });
            }
        });
    });
    
    $('button#btnUpdateBiodata').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                var formData = new FormData($('#formBioPegawai')[0]);
                $.ajax({
                    url: ci_baseurl + "kepegawaian/pegawai/do_update_bio",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formBioPegawai').serialize(),
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        $('input[name='+data.csrfTokenName+'_del]').val(data.csrfHash);
                        if(ret === true) {
                            $('#upd_alert_type').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#upd_alert_message').html(data.messages);
                            $('#upd_alert_type').show();
                            $("#upd_alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#upd_alert_type").hide();
                            });
                            clearForm();
                            $("html, body").animate({scrollTop: 100}, "fast");
                        table_pegawai.columns.adjust().draw();
                        } else {
                            $('#upd_alert_type').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
                            $('#upd_alert_message').html(data.messages);
                            $('#upd_alert_type').show();
                            $("#upd_alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#upd_alert_type").hide();
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
            $('#upd_alert_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
            $('#upd_alert_message').html('Konfirmasi password tidak sesuai');
            $('#upd_alert_type').show();
            $("#upd_alert_type").fadeTo(2000, 500).slideUp(500, function(){
                $("#upd_alert_type").hide();
            });
            return false;
        }
        jConfirm("Anda yakin akan mengganti password ?","Ok","Cancel", function(r){
            if(r){
                //var formData = new FormData($('#formBioPegawai')[0]);
                $.ajax({
                    url: ci_baseurl + "kepegawaian/pegawai/do_ubah_password",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formGantiPassword').serialize(),
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        $('input[name='+data.csrfTokenName+'_del]').val(data.csrfHash);
                        if(ret === true) {
                            $('#upd_alert_type').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#upd_alert_message').html(data.messages);
                            $('#upd_alert_type').show();
                            $("#upd_alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#upd_alert_type").hide();
                            });
                            //clearForm();
                            $("html, body").animate({scrollTop: 100}, "fast");
                        table_pegawai.columns.adjust().draw();
                        } else {
                            $('#upd_alert_type').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
                            $('#upd_alert_message').html(data.messages);
                            $('#upd_alert_type').show();
                            $("#upd_alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#upd_alert_type").hide();
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
});

function clearForm(){
    $('#formPegawaiUkpd').find("input[type=text]").val("");
    $('#formPegawaiUkpd').find("select").prop('selectedIndex',0);
    $('#user_id').val('');
}

function editPegawai(pegawai_id){
    if(pegawai_id !==''){		
        $.ajax({
        url: ci_baseurl + "kepegawaian/pegawai/ajax_get_pegawai_by_id",
        type: 'get',
        dataType: 'json',
        data: {pegawai_id:pegawai_id},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#upd_pegawai_id').val(data.data.id_pegawai);
                    $('#upd_nip').val(data.data.nip);
                    $('#upd_nip_old').val(data.data.nip);
                    $('#upd_nama_lengkap').val(data.data.nama_pegawai);
                    $('#upd_tempat_lahir').val(data.data.tempat_lahir);
                    $('#upd_tgl_lahir').val(data.tgl_lahir);
                    if (data.data.jenis_kelamin==='LAKI-LAKI') {
                        $('#upd_jk1').prop('checked',true);
                    }else{
                        $('#upd_jk2').prop('checked',true);
                    }
                    $('#upd_no_ktp').val(data.data.no_ktp);
                    $('#upd_no_npwp').val(data.data.npwp);
                    $('#upd_no_rek').val(data.data.norek_dki);
                    $('#upd_alamat').html(data.data.alamat);
                    $('#upd_no_tlp').val(data.data.no_tlp);
                    $('#upd_tgl_masuk').val(data.tgl_masuk);
                    if (data.data.status_pns==='NON PNS') {
                        $('#upd_status_pns1').prop('checked',true);
                    }else{
                        $('#upd_status_pns2').prop('checked',true);
                    }
                    $('#upd_status_pernikahan').val(data.data.status);
                    $('#upd_pendidikan').val(data.data.pendidikan);
                    $('#upd_rumpun').val(data.data.rumpun);
                    $('#upd_bagian').val(data.data.bagian);
                    $('#upd_jabatan').val(data.data.jabatan);
                    if (data.data.bpjs_ks=='0.02') {
                        $('#upd_bpjsks').prop('checked',true);
                    }
                    if (data.data.bpjs_jkk=='0.0054') {
                        $('#upd_bpjsjkk').prop('checked',true);
                    }
                    if (data.data.bpjs_ijht=='0.057') {
                        $('#upd_bpjsijht').prop('checked',true);
                    }
                    if (data.data.bpjs_jp=='0.057') {
                        $('#upd_bpjsjp').prop('checked',true);
                    }
                    $('#upd_pajak').val(data.data.pajak);
                    $('#upd_pj_cuti').val(data.data.pj_cuti);
                    $('#upd_email').val(data.data.email);
                    $('#upd_username').val(data.data.username);
                    $('#upd_idpeg').val(data.data.id_pegawai);
                    $('#upd_nippeg').val(data.data.nip);
                    $('#upd_level_user').val(data.data.group_id);
                    $('#upd_tempattugas').val(data.data.tempat_tugas);
                    $('#upd_tempattugas_ket').val(data.data.tempat_tugas_ket);
                    $('#upd_nrk').val(data.data.nrk);
                    $('#upd_gelar_depan').val(data.data.gelar_depan);
                    $('#upd_gelar_belakang').val(data.data.gelar_belakang);
                    $('#upd_agama').val(data.data.agama);
                    $('#upd_pangkat').val(data.pangkat);
                    $('#upd_golongan').val(data.golongan);
                    $('#updatePegawaiModal').modal('toggle');
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
        $('#notification_messages').html('User ID tidak ditemukan');
        $('#notification_type').show();
    }
}
    
function hapusPegawai(user_id, nip){
    var jsonVariable = {};
    jsonVariable["user_id"] = user_id;
    jsonVariable["nip"] = nip;
    jsonVariable[csrf_name] = $('#'+csrf_name+'_del').val();;
    if(user_id !==''){
        jConfirm("Anda yakin akan menghapus data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                url: ci_baseurl + "kepegawaian/pegawai/do_delete_pegawai",
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
                            table_pegawai.columns.adjust().draw();
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
        $('#notification_messages').html('Missing ID UKPD');
        $('#notification_type').show();
    }
}

function pilihTmptugas(){
    var tempattugas = $('#tempattugas').val();
    if(tempattugas !==''){		
        $.ajax({
        url: ci_baseurl + "kepegawaian/pegawai/get_tempattugas_by_id",
        type: 'get',
        dataType: 'json',
        data: {tempattugas:tempattugas},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#tempattugas_ket').val(data.data.tempattugas_ket);
                }
            }
        });
    }
}

function pilihTmptugasUpdate(){
    var tempattugas = $('#upd_tempattugas').val();
    if(tempattugas !==''){		
        $.ajax({
        url: ci_baseurl + "kepegawaian/pegawai/get_tempattugas_by_id",
        type: 'get',
        dataType: 'json',
        data: {tempattugas:tempattugas},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#upd_tempattugas_ket').val(data.data.tempattugas_ket);
                }
            }
        });
    }
}
</script>