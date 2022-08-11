<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
var table_review;

$(document).ready(function(){
    table_review = $('#table_review').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "ecuti/persetujuan_pj/ajax_list_cuti",
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
    
    table_review.columns.adjust().draw(); 
    
    $('button#btnFilterCuti').click(function(){
        table_review.columns.adjust().draw();
    });
    
    $('button#btnTerimaCuti').click( function() {
        jConfirm("Anda yakin akan menerima cuti ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                    url: ci_baseurl + "ecuti/persetujuan_pj/do_terima_cuti",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formReviewCuti').serialize(),
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        if(ret === true) {
                            $('#alert_type').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#alert_message').html(data.messages);
                            $('#alert_type').show();
                            $("#alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type").hide();
                            });
                            $("html, body").animate({scrollTop: 100}, "fast");
                        table_review.columns.adjust().draw();
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
    
    $('button#btnTolakCuti').click( function() {
        jConfirm("Anda yakin akan menolak cuti ini ?","Ok","Cancel", function(r){
            if(r){
                $.ajax({
                    url: ci_baseurl + "ecuti/persetujuan_pj/do_tolak_cuti",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formReviewCuti').serialize(),
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        if(ret === true) {
                            $('#alert_type').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#alert_message').html(data.messages);
                            $('#alert_type').show();
                            $("#alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type").hide();
                            });
                            $("html, body").animate({scrollTop: 100}, "fast");
                        table_review.columns.adjust().draw();
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
    
    $('.blnpicker').datepicker({
        orientation: 'bottom',
        autoclose: 'true',
        format: "M yyyy",
        startView: "months", 
        minViewMode: "months"
    }); 
});

function reviewCuti(cuti_id){
    if(cuti_id !==''){		
        $.ajax({
        url: ci_baseurl + "ecuti/persetujuan_pj/ajax_get_cuti_by_id",
        type: 'get',
        dataType: 'json',
        data: {cuti_id:cuti_id},
            success: function(data) {
                var ret = data.success;
                if(ret === true) {
                    $('#reviewCutiModal').modal('toggle');
                    $('#nama_pegawai').val(data.data.pegawai_nama);
                    $('#cuti_id').val(data.data.cuti_id);
                    $('#bagian').val(data.data.bagian_nama);
                    $('#sisa_cuti').val(data.sisa_cuti+' Hari');
                    $('#jenis_cuti').val(data.data.jeniscuti_nama);
                    $('#ket_cuti').val(data.data.ket_cuti);
                    $('#tgl_awal').val(data.tgl_awal);
                    $('#tgl_akhir').val(data.tgl_akhir);
                    $('#jml_hari').val(data.data.jml_hari);
                    $('#no_tlp').val(data.data.no_tlp);
                    $('#pegawai_pengganti').val(data.data.pengganti_nama);
                    $('#alasan').html(data.data.alasan);
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
        $('#notification_messages').html('CUTI ID tidak ditemukan');
        $('#notification_type').show();
    }
}
</script>