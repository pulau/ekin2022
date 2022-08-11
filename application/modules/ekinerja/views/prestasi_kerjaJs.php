<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<script type="text/javascript">
var table_pegawai;
$(document).ready(function(){
    table_pegawai = $('#table_prestasi_pegawai').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,
      "ordering": false,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "ekinerja/prestasi_pegawai/ajax_list_prestasi_pegawai",
          "type": "GET",
          "data" : function(d){
                d.filter_bln = $("#filter_bln").val();
            }
      },
      //Set column definition initialisation properties.
//      "columnDefs": [
//      { 
//        "targets": [ -1,0 ], //last column
//        "orderable": false //set not orderable
//      }
//      ]

    });
    
    $('.blnpicker').datepicker({
        orientation: 'bottom',
        autoclose: 'true',
        format: "M yyyy",
        startView: "months", 
        minViewMode: "months"
    }); 
    
    $('button#goFilter').click(function(){
        table_pegawai.columns.adjust().draw();
    });
   
    $('button#btnSinkronisasi').click( function() {
        jConfirm("Proses Sinkronisasi dimulai ?","Ok","Cancel", function(r){
            if(r){
                $('#spinnerModal').modal('show');
                $.ajax({
                    url: ci_baseurl + "ekinerja/prestasi_pegawai/do_sinkronisasi",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formSinkronisi').serialize(),
                    success: function(data) {
                        var ret = data.success;
                        $('input[name='+data.csrfTokenName+']').val(data.csrfHash);
                        if(ret === true) {
                            $('#spinnerModal').modal('hide');
                            $('#alert_type').removeClass('alert alert-dismissable alert-danger').addClass('alert alert-success alert-dismissable');
                            $('#alert_message').html(data.messages);
                            $('#alert_type').show();
                            $("#alert_type").fadeTo(2000, 500).slideUp(500, function(){
                                $("#alert_type").hide();
                            });
                            table_pegawai.columns.adjust().draw();
                            clearForm();
                            $("html, body").animate({scrollTop: 100}, "fast");
                            table_pegawai.columns.adjust().draw();
                        } else {
                            $('#spinnerModal').modal('hide');
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
    table_pegawai.columns.adjust().draw();
});

function exportExcel(){
    var filter_bln = $("#filter_bln").val();
    if (filter_bln == ""){ filter_bln = "undefined"; }
    window.location.href = ci_baseurl + "ekinerja/prestasi_pegawai/export_excel_pegawai/"+ filter_bln;
}
</script>