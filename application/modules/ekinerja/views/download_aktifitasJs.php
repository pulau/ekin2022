<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
var table_download_aktifitas;

$(document).ready(function(){
    table_download_aktifitas = $('#tabel_download_aktifitas').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "ekinerja/Download_aktifitas/ajax_list_Download_aktifitas",
          "type": "GET",
          "data" : function(d){
                d.filter_bln = $("#filter_bln").val();
            }
      },
      //Set column definition initialisation properties.
      "columnDefs": [
      
      ]
    });
    table_download_aktifitas.columns.adjust().draw();
    
    $('button#goFilter').click(function(){
        table_download_aktifitas.columns.adjust().draw();
    });
    
    $('.blnpicker').datepicker({
//        format: 'dd-mm-yyyy',
        orientation: 'bottom',
        autoclose: 'true',
        format: "M yyyy",
        startView: "months", 
        minViewMode: "months"
    });
});

function exportExcel(){
    var filter_bln = $("#filter_bln").val();
    if (filter_bln == ""){ filter_bln = "undefined"; }
    window.location.href = ci_baseurl + "ekinerja/Download_aktifitas/export_excel_pegawai/"+ filter_bln;
}
</script>