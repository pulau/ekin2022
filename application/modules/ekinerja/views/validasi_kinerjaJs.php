<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
    var table_aktifitas;
var table_prilaku;
var table_skp;
$(document).ready(function(){
    $('#bln_label').html($("#filter_bln").val());
    $('#bln_label2').html($("#filter_bln").val());
    table_aktifitas = $('#table_validasi_aktifitas').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "ordering": false,
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "ekinerja/validasi_kinerja/ajax_list_validasi_aktifitas",
          "type": "GET",
          "data" : function(d){
                d.filter_bln = $("#filter_bln").val();
            }
      }
      //Set column definition initialisation properties.
//      "columnDefs": [
//      { 
//        "targets": [ -1,0 ], //last column
//        "orderable": false //set not orderable
//      }
//      ]

    });
    
    table_prilaku = $('#table_validasi_prilaku').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "ekinerja/validasi_kinerja/ajax_list_validasi_prilaku",
          "type": "GET",
          "data" : function(d){
                d.filter_bln = $("#filter_bln").val();
            }
      },
      //Set column definition initialisation properties.
      "columnDefs": [
      { 
        "targets": [ -1,0 ], //last column
        "orderable": false //set not orderable
      }
      ]

    });
    
    $('button#goFilter').click(function(){
        $('#bln_label').html($("#filter_bln").val());
        $('#bln_label2').html($("#filter_bln").val());
        table_aktifitas.columns.adjust().draw();
        table_prilaku.columns.adjust().draw();
    });
    
    //$.fn.select2.defaults.set("theme", "bootstrap");
    $('.select2').select2({
        placeholder: 'Pilih Aktifitas',
        width: null,
//        dropdownParent: $('.modal')
    });
    
    $('.blnpicker').datepicker({
//        format: 'dd-mm-yyyy',
        orientation: 'bottom',
        autoclose: 'true',
        format: "M yyyy",
        startView: "months", 
        minViewMode: "months"
    });
    
     $('.tglpicker').datepicker({
        format: 'dd-mm-yyyy',
        orientation: 'bottom',
        autoclose: 'true'
    });
    
    $('.jampicker').timepicker({
        autoclose: true,
        minuteStep: 1,
        showSeconds: false,
        showMeridian: false
    });
    
    $('button#btnSimpanPrilaku').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                    url: ci_baseurl + "ekinerja/validasi_kinerja/do_insert_prilaku",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formValidasiPrilaku').serialize(),
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        //$('input[name='+data.csrfTokenName+'_del]').val(data.csrfHash);
                        if(ret === true) {
                            $('#upd_alert_type').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#upd_alert_message').html(data.messages);
                            $('#upd_alert_type').show();
                            $("#upd_alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#upd_alert_type").hide();
                            });
                            //clearForm();
                            $("html, body").animate({scrollTop: 100}, "fast");
                            table_prilaku.columns.adjust().draw();
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
    
    $('button#btnUpdatePrilaku').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                    url: ci_baseurl + "ekinerja/validasi_kinerja/do_update_prilaku",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formUpdatePrilaku').serialize(),
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        //$('input[name='+data.csrfTokenName+'_del]').val(data.csrfHash);
                        if(ret === true) {
                            $('#pri_alert_type').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#pri_alert_message').html(data.messages);
                            $('#pri_alert_type').show();
                            $("#pri_alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#pri_alert_type").hide();
                            });
                            //clearForm();
                            $("html, body").animate({scrollTop: 100}, "fast");
                            table_prilaku.columns.adjust().draw();
                        } else {
                            $('#pri_alert_type').removeClass('alert alert-dismissable alert-success').addClass('alert alert-danger alert-dismissable');
                            $('#pri_alert_message').html(data.messages);
                            $('#pri_alert_type').show();
                            $("#pri_alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#pri_alert_type").hide();
                            });
                            $("html, body").animate({scrollTop: 100}, "fast");
                        }
                    }
                });
            }
        });
    });
    
    $('#select_all').click(function(){
	$(':checkbox[class='+ $(this).data('checkbox-name') + ']:not(:disabled)').prop("checked", $(this).prop("checked"));
        if(this.checked){
            $('#btnValidasiAll').removeAttr('disabled');
            $('#btnTolakAll').removeAttr('disabled');
        }else{
            $('#btnValidasiAll').attr('disabled','true');
            $('#btnTolakAll').attr('disabled','true');
        }
    });
});

function inputPrilaku(id_pegawai, curr_month, nama_peg){
    if(id_pegawai !=='' && curr_month !==''){
        $('#id_pegawai_pri').val(id_pegawai);
        $('#bulan_pri').val(curr_month);
        $('#peg_nama_pri').html(nama_peg);
        $('#validasiPrilakuModal').modal('toggle');
    }
    else
    {
        $('#notification_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
        $('#notification_messages').html('Perilaku tidak ditemukan');
        $('#notification_type').show();
    }
}

function editPrilaku(id_prilaku, nama_peg){
    if(id_prilaku !==''){		
        $.ajax({
        url: ci_baseurl + "ekinerja/validasi_kinerja/ajax_get_perilaku_by_id",
        type: 'get',
        dataType: 'json',
        data: {id_prilaku:id_prilaku},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#upd_id_perilaku').val(id_prilaku);
                    $('#upd_peg_nama_pri').html(nama_peg);
                    $('#id_pegawai_pri').val(data.data.id_pegawai);
                    $('#upd_or_pel').val(data.data.orientasi_pelayanan);
                    $('#upd_integritas').val(data.data.integrasi);
                    $('#upd_komitmen').val(data.data.komitmen);
                    $('#upd_disiplin').val(data.data.disiplin);
                    $('#upd_kerjasama').val(data.data.kerjasama);
                    $('#updatePrilakuModal').modal('toggle');
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
        $('#notification_messages').html('Perilaku tidak ditemukan');
        $('#notification_type').show();
    }
}

function listAktifitas(id_pegawai, curr_month, nama_peg){
    table_skp = $('#table_list_aktifitas').DataTable({
      "destroy": true,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "ekinerja/validasi_kinerja/ajax_get_list_aktifitas",
          "type": "GET",
          "data" : function(d){
                d.id_pegawai = id_pegawai;
                d.curr_month = curr_month;
            }
      },
      //Set column definition initialisation properties.
      "columnDefs": [
      { 
        "targets": [ 0 ], //last column
        "orderable": false //set not orderable
      }
      ]
    });
    $('#peg_nama').html(nama_peg);
    $('#validasiAktifitasModal').modal('toggle');
}

function select_one(){
    $('#select_all').prop('checked',false);
    var check = $('input[name="aktifitas_valid[]"]:checked').length;
    if(check > 0){
        $('#btnValidasiAll').removeAttr('disabled');
        $('#btnTolakAll').removeAttr('disabled');
    }else{
        $('#btnValidasiAll').attr('disabled','true');
        $('#btnTolakAll').attr('disabled','true');
    }
}

function validateAktifitas(){
    var check = $('input[name="aktifitas_valid[]"]:checked').length;
    if(check > 0){
        jConfirm("Setujui Aktifitas yang anda pilih ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                    url: ci_baseurl + "ekinerja/validasi_kinerja/do_validasi_aktifitas",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#fmValidasiAktifitas').serialize(),
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        if(ret === true) {
                            $('#aktifitas_alert_type').removeClass('alert alert-danger alert-dismissable').addClass('alert alert-success alert-dismissable');
                            $('#aktifitas_alert_message').html(data.messages);
                            $('#aktifitas_alert_type').show();
                            $("#aktifitas_alert_type").fadeTo(2000, 500).slideUp(500, function () {
                                $("#aktifitas_alert_type").hide();
                                //$('.modal.in').modal('hide');
                            });
                            table_aktifitas.columns.adjust().draw();
                            $('#validasiAktifitasModal').modal('hide');
                        } else {
                            $('#aktifitas_alert_type').removeClass('alert alert-success alert-dismissable').addClass('alert alert-danger alert-dismissable');
                            $('#aktifitas_alert_message').html(data.messages);
                            $('#aktifitas_alert_type').show();
                            $("#aktifitas_alert_type").fadeTo(2000, 500).slideUp(500, function () {
                                $("#aktifitas_alert_type").hide();
                            });
                        }
                    }
                });
            }
        });
    }else{
        $('#aktifitas_alert_type').removeClass('alert alert-success alert-dismissable').addClass('alert alert-danger alert-dismissable');
        $('#aktifitas_alert_message').html('Pilih data yang akan diapprove terlebih dahulu !');
        $('#aktifitas_alert_type').show();
        $("#aktifitas_alert_type").fadeTo(2000, 500).slideUp(500, function () {
            $("#aktifitas_alert_type").hide();
        });
    }
}

function tolakAktifitas(){
    var check = $('input[name="aktifitas_valid[]"]:checked').length;
    if(check > 0){
        jConfirm("Tolak Aktifitas yang anda pilih ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                    url: ci_baseurl + "ekinerja/validasi_kinerja/do_tolak_aktifitas",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#fmValidasiAktifitas').serialize(),
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        if(ret === true) {
                            $('#aktifitas_alert_type').removeClass('alert alert-danger alert-dismissable').addClass('alert alert-success alert-dismissable');
                            $('#aktifitas_alert_message').html(data.messages);
                            $('#aktifitas_alert_type').show();
                            $("#aktifitas_alert_type").fadeTo(2000, 500).slideUp(500, function () {
                                $("#aktifitas_alert_type").hide();
                                //$('.modal.in').modal('hide');
                            });
                            table_aktifitas.columns.adjust().draw();
                            $('#validasiAktifitasModal').modal('hide');
                        } else {
                            $('#aktifitas_alert_type').removeClass('alert alert-success alert-dismissable').addClass('alert alert-danger alert-dismissable');
                            $('#aktifitas_alert_message').html(data.messages);
                            $('#aktifitas_alert_type').show();
                            $("#aktifitas_alert_type").fadeTo(2000, 500).slideUp(500, function () {
                                $("#aktifitas_alert_type").hide();
                            });
                        }
                    }
                });
            }
        });
    }else{
        $('#aktifitas_alert_type').removeClass('alert alert-success alert-dismissable').addClass('alert alert-danger alert-dismissable');
        $('#aktifitas_alert_message').html('Pilih data yang akan diapprove terlebih dahulu !');
        $('#aktifitas_alert_type').show();
        $("#aktifitas_alert_type").fadeTo(2000, 500).slideUp(500, function () {
            $("#aktifitas_alert_type").hide();
        });
    }
}
</script>