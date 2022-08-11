<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
var table_pegawai;
var table_skp;
$(document).ready(function(){
    table_pegawai = $('#table_validasi_skp_pegawai').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "ekinerja/validasi_skp/ajax_list_pegawai_skp",
          "type": "GET",
//          "data" : function(d){
//                d.filter_bln = $("#filter_bln").val();
//            }
      },
      //Set column definition initialisation properties.
      "columnDefs": [
      { 
        "targets": [ -1,0 ], //last column
        "orderable": false //set not orderable
      }
      ]

    });
});

function listKinerja(id_pegawai, nama_pegawai){
    if(table_skp){
        table_skp.destroy();
    }
    table_skp = $('#table_list_skpt').DataTable({   
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "ekinerja/validasi_skp/ajax_list_skpt_pegawai",
          "type": "GET",
          "data" : function(d){
                d.id_pegawai = id_pegawai;
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
    $('#peg_nama').html(nama_pegawai);
    $('#listSKPValidasiModal').modal('toggle');
}

function validasiSKPTahunan(skpt_id){
    var jsonVariable = {};
    jsonVariable["skptahunan_id"] = skpt_id;
    jsonVariable[csrf_name] = $('#'+csrf_name+'_val').val();;
    if(skpt_id !==''){
        jConfirm("Anda yakin akan memvalidasi data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                url: ci_baseurl + "ekinerja/validasi_skp/do_update_skptahunan",
                type: 'post',
                dataType: 'json',
                data: jsonVariable,
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+'_val]').val(data.csrfHash);
                        if(ret === true) {
                            $('#upd_alert_type').removeClass('alert alert-danger alert-dismissable').addClass('alert alert-success alert-dismissable');
                            $('#upd_alert_message').html(data.messages);
                            $("#upd_alert_type").show();
                            $("#upd_alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#upd_alert_type").hide();
                            });
//                            $('button#reloadProduk').click();
                            table_skp.columns.adjust().draw();
                        } else {
                            $('#upd_alert_type').removeClass('alert alert-success alert-dismissable').addClass('alert alert-danger alert-dismissable');
                            $('#upd_alert_message').html(data.messages);
                            $("#upd_alert_type").show();
                            $("#upd_alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#upd_alert_type").hide();
                            });
                        }
                    }
                });
            }
        });
    }
    else
    {
        $('#upd_alert_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
        $('#upd_alert_message').html('SKP Tahunan tidak ditemukan');
        $('#upd_alert_type').show();
    }
}

function deleteSKPTahunan(skpt_id){
    var jsonVariable = {};
    jsonVariable["skptahunan_id"] = skpt_id;
    jsonVariable[csrf_name] = $('#'+csrf_name+'_val').val();;
    if(skpt_id !==''){
        jConfirm("Anda yakin akan menghapus data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                url: ci_baseurl + "ekinerja/validasi_skp/do_delete_skpt",
                type: 'post',
                dataType: 'json',
                data: jsonVariable,
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+'_val]').val(data.csrfHash);
                        if(ret === true) {
                            $('#upd_alert_type').removeClass('alert alert-danger alert-dismissable').addClass('alert alert-success alert-dismissable');
                            $('#upd_alert_message').html(data.messages);
                            $("#upd_alert_type").show();
                            $("#upd_alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#upd_alert_type").hide();
                            });
//                            $('button#reloadProduk').click();
                            table_skp.columns.adjust().draw();
                        } else {
                            $('#upd_alert_type').removeClass('alert alert-success alert-dismissable').addClass('alert alert-danger alert-dismissable');
                            $('#upd_alert_message').html(data.messages);
                            $("#upd_alert_type").show();
                            $("#upd_alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#upd_alert_type").hide();
                            });
                        }
                    }
                });
            }
        });
    }
    else
    {
        $('#upd_alert_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
        $('#upd_alert_message').html('SKP Tahunan tidak ditemukan');
        $('#upd_alert_type').show();
    }
}
</script>