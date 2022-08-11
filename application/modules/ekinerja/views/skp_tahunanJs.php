<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
var table_skptahunan;

$(document).ready(function(){
    table_skptahunan = $('#table_skptahunan').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,
      "iDisplayLength": 25,
      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "ekinerja/skp_tahunan/ajax_list_skptahunan",
          "type": "GET",
          "data" : function(d){
                d.filter_thn = $("#filter_thn").val();
            }
      },
      //Set column definition initialisation properties.
      "columnDefs": [
      { 
        "targets": [ -1,0 ], //last column
        "orderable": false //set not orderable
      }
      ],
      "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/Menit/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            
            // Waktu Total
            total = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total Kuantitas
            qtyTotal = api
                .column( 2 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 2 ).footer() ).html(
                qtyTotal
            );
            $( api.column( 4 ).footer() ).html(
                total +' Menit'
            );
        },
        "sDom": '<"top"Bfl>rt<"bottom"ip><"clear">',
        buttons: [
            'excel', 'pdf'
        ]
    });
    
    $('button#goFilter').click(function(){
        table_skptahunan.columns.adjust().draw(); 
    });
    
    $('.select2').select2({
        placeholder: 'Pilih Activitas Tahunan',
//        width: null,
//        dropdownParent: $('.modal')
    });
    
    $('.thnpicker').datepicker({
//        format: 'dd-mm-yyyy',
        orientation: 'bottom',
        autoclose: 'true',
        format: "yyyy",
        startView: "years", 
        minViewMode: "years"
    });
    
    $('button#btnSaveSKPTahunan').click( function() {
        jConfirm("Anda yakin akan menyimpan data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                    url: ci_baseurl + "ekinerja/skp_tahunan/do_insert_skptahunan",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formSKPTahunan').serialize(),
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
                            clearForm();
                            $("html, body").animate({scrollTop: 100}, "fast");
                        table_skptahunan.columns.adjust().draw();
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
    
    $('button#btnUpdateSKPTahunan').click( function() {
        jConfirm("Anda yakin akan mengubah data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                    url: ci_baseurl + "ekinerja/skp_tahunan/do_update_skptahunan",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formSKPTahunanUpdate').serialize(),
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
                        table_skptahunan.columns.adjust().draw();
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
});

function clearForm(){
    $('#formSKPTahunan').find("input[type=text]").val("");
    $('#formSKPTahunan').find("select").prop('selectedIndex',0);
}

function editSKPTahunan(skpt_id){
    if(skpt_id !==''){	
        $.ajax({
        url: ci_baseurl + "ekinerja/skp_tahunan/ajax_get_skpt_by_id",
        type: 'get',
        dataType: 'json',
        data: {skpt_id:skpt_id},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    item = data.data.skp+" - "+data.data.waktu_skp+" Menit"
                    $('#upd_kuantitas').val(data.data.qty);
                    $('#skptahunan_id').val(data.data.skptahunan_id);
                    $('#editSKPTahunanModal').modal('toggle');
                    $("#upd_skp").append("<option value='"+data.data.kd_skp+"' selected>"+item+"</option>");
                    $('#upd_skp').trigger('change');
                } else {
                    $('#upd_alert_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
                    $('#upd_alert_message').html(data.messages);
                    $('#upd_alert_type').show();
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

function deleteSKPTahunan(skptahunan_id){
    var jsonVariable = {};
    jsonVariable["skptahunan_id"] = skptahunan_id;
    jsonVariable[csrf_name] = $('#'+csrf_name+'_del').val();;
    if(skptahunan_id !==''){
        jConfirm("Anda yakin akan menghapus data ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                url: ci_baseurl + "ekinerja/skp_tahunan/do_delete_skpt",
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
                            table_skptahunan.columns.adjust().draw();
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
        $('#notification_messages').html('SKP Tahunan tidak ditemukan');
        $('#notification_type').show();
    }
}
</script>