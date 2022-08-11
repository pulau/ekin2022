<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
var table_pengajuan;

$(document).ready(function(){
    table_pengajuan = $('#table_pengajuan').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "ecuti/pengajuan_cuti/ajax_list_pengajuan",
          "type": "GET",
          "data" : function(d){
              d.filter_bln = $("#filter_bln").val();
          }
      },
      //Set column definition initialisation properties.
      "columnDefs": [
      { 
        "targets": [ -1,0 ], //last column
        "orderable": false, //set not orderable
      },
      ],

    });
    
    table_pengajuan.columns.adjust().draw(); 
    
    $('button#btnFilterCuti').click(function(){
        table_pengajuan.columns.adjust().draw();
    });
    
    $('button#btnSavePengajuanCuti').click( function() {
        var sisa_cuti = $('#sisa_cuti').val();
        var jumlah_hari = $('#jml_hari').val();
        //console.log(jumlah_hari+" > "+sisa_cuti);
        if(parseInt(jumlah_hari) > parseInt(sisa_cuti)){
            $('#alert_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
            $('#alert_message').html('Jatah Cuti Anda Tidak Mencukupi');
            $('#alert_type').show();
            $("#alert_type").fadeTo(2000, 500).slideUp(500, function(){
                $("#alert_type").hide();
            });
            $("html, body").animate({scrollTop: 100}, "fast");
            return false;
        }
        
        var cuti_id = $('#cuti_id').val();
        if(cuti_id == ''){
            var used_url = ci_baseurl + "ecuti/pengajuan_cuti/do_insert_cuti";
        }else{
            var used_url = ci_baseurl + "ecuti/pengajuan_cuti/do_update_cuti";
        }
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
//                var formData = new FormData($('#formPengajuanCuti')[0]);
                $.ajax({
                    url: used_url,
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formPengajuanCuti').serialize(),
//                    data : formData,
//                    contentType: false,
//                    processData: false,
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        $('input[name='+data.csrfTokenName+'_del]').val(data.csrfHash);
                        if(ret === true) {
                            $('#alert_type').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#alert_message').html(data.messages);
                            $('#alert_type').show();
                            $("#alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type").hide();
                            });
                            //clearForm();
                            $('#cuti_id').val("");
                            $("html, body").animate({scrollTop: 100}, "fast");
                        table_pengajuan.columns.adjust().draw();
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

//    $('.tglpicker').datepicker({
//        format: 'dd-mm-yyyy',
//        orientation: 'bottom',
//        autoclose: true,
//    });
    $('.blnpicker').datepicker({
        orientation: 'bottom',
        autoclose: 'true',
        format: "M yyyy",
        startView: "months", 
        minViewMode: "months"
    }); 
    
    $('.tglpicker2').datepicker({
        format: 'dd-mm-yyyy',
        orientation: 'bottom',
        multidate: true
    });
    
    $('.select2').select2({
        placeholder: 'Pilih Jenis Cuti',
//        width: null,
        dropdownParent: $('.modal')
    });
    
    $('.select3').select2({
        placeholder: 'Pilih Pengganti',
//        width: null,
        dropdownParent: $('.modal')
    });
});

function clearForm(){
    $('#formPengajuanCuti').find("input[type=text]").val("");
    $('#formPengajuanCuti').find("select").prop('selectedIndex',0);
    $('#cuti_id').val("");
}

function setJmlHari(){
    var tgl_cuti = $("#tgl_cuti").val();
    //var tgl_akhir = $("#tgl_akhir").val();
    if(tgl_awal != ''){
        $.ajax({
            url: ci_baseurl + "ecuti/pengajuan_cuti/hitung_hari_explode",
            type: 'GET',
            dataType: 'JSON',
            data: {tgl_cuti:tgl_cuti},
            success: function(data) {
                $("#jml_hari").val(data.jml_hari);
                $("#tgl_awal").val(data.tgl_awal);
                $("#tgl_akhir").val(data.tgl_akhir);
            }
        });
    }
}

function setKeterangan(){
    var jenis_cuti = $("#jenis_cuti").val();
    if(jenis_cuti == '1'){
        $('#ket_cuti').attr('disabled','disabled');
    }else{
        $('#ket_cuti').removeAttr('disabled');
    }
}

function editPengajuan(cuti_id){
    if(cuti_id !==''){	
        $.ajax({
        url: ci_baseurl + "ecuti/pengajuan_cuti/ajax_get_cuti_by_id",
        type: 'get',
        dataType: 'json',
        data: {cuti_id:cuti_id},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#addPengajuanCutiModal').modal('toggle');
                    $('#jenis_cuti').val(data.data.jenis_cuti);
                    $('#ket_cuti').val(data.data.ket_cuti);
                    $('#tgl_awal').val(data.tgl_awal);
                    $('#tgl_akhir').val(data.tgl_akhir);
                    $('#jml_hari').val(data.data.jml_hari);
                    $('#kota_cuti').val(data.data.kota_cuti);
                    $('#no_tlp').val(data.data.no_tlp);
                    $('#alasan').val(data.data.alasan);
                    $('#cuti_id').val(data.data.cuti_id);
                    $('#pengganti').val(data.data.pengganti_id);
                    $("#pengganti").append("<option value='"+data.data.pengganti_id+"' selected>"+data.data.pengganti_nama+"</option>");
                    $('#pengganti').trigger('change');
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

function deleteCuti(cuti_id){
    var jsonVariable = {};
    jsonVariable["cuti_id"] = cuti_id;
    jsonVariable[csrf_name] = $('#'+csrf_name+'_del').val();;
    if(cuti_id !==''){
        jConfirm("Anda yakin akan menghapus data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                url: ci_baseurl + "ecuti/pengajuan_cuti/do_delete_cuti",
                type: 'post',
                dataType: 'json',
                data: jsonVariable,
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        $('input[name='+data.csrfTokenName+'_del]').val(data.csrfHash);
                        if(ret === true) {
                            $('#notification_type').removeClass('alert alert-danger alert-dismissable').addClass('alert alert-success alert-dismissable');
                            $('#notification_message').html(data.messages);
                            $("#notification_type").show();
                            $("#notification_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#notification_type").hide();
                            });
//                            $('button#reloadProduk').click();
                            table_pengajuan.columns.adjust().draw();
                        } else {
                            $('#notification_type').removeClass('alert alert-success alert-dismissable').addClass('alert alert-danger alert-dismissable');
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
        $('#notification_messages').html('Cuti tidak ditemukan');
        $('#notification_type').show();
    }
}
</script>