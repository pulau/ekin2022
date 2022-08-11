<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
$(document).ready(function(){
    $('#bln_label1').html($("#filter_bln").val());
    $('#bln_label2').html($("#filter_bln").val());
    $('#bln_label3').html($("#filter_bln").val());
    //getSerapan($("#filter_bln").val());
    getWaktuEfektif($("#filter_bln").val());
    table_aktifitas = $('#table_validasi_aktifitas').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      //"scrollX": true,
      "paging":   false,
        "ordering": false,
        "info":     false,
        "bFilter": false,
//      "sDom": '<"top">rt<"bottom"ip><"clear">',

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": ci_baseurl + "ekinerja/capaian_kinerja/ajax_aktifitas_list",
          "type": "GET",
          "data" : function(d){
                d.filter_bln = $("#filter_bln").val();
            }
      },
      //Set column definition initialisation properties.
      "columnDefs": [
      { "width": "10px", "targets": 0 },
      { "width": "50%", "targets": 1 }
      ],
      "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            
            // Waktu Total
            totalVolume = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total Kuantitas
            totalCapaian = api
                .column( 5 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 4 ).footer() ).html(
                totalVolume
            );
            $( api.column( 5 ).footer() ).html(
                totalCapaian
            );
        }
    });
    
    $('.blnpicker').datepicker({
        orientation: 'bottom',
        autoclose: 'true',
        format: "M yyyy",
        startView: "months", 
        minViewMode: "months"
    }); 
    
    $('button#goFilter').click(function(){
        $('#bln_label1').html($("#filter_bln").val());
        $('#bln_label2').html($("#filter_bln").val());
        $('#bln_label3').html($("#filter_bln").val());
        //getSerapan($("#filter_bln").val());
        getWaktuEfektif($("#filter_bln").val());
        table_aktifitas.columns.adjust().draw();
    });
});

function getWaktuEfektif(bulan){
    if(bulan != ''){
        $.ajax({
            url: ci_baseurl + "ekinerja/capaian_kinerja/ajax_get_jml_waktu_efektif",
            type: 'GET',
            dataType: 'JSON',
            data: {bulan:bulan},
            success: function(data) {
                var aktifitas70 = data.persen_capaian*0.7;
                var aktifitas70_fix = (aktifitas70 > 70) ? 70 : aktifitas70;
                // var total = parseFloat(aktifitas70_fix)+parseFloat(data.persen_perilaku)+parseFloat(data.persen_serapan);
                $('#table_waktu_efektif > tbody').html(data.tabel_waktu);
                $('#table_capaian_efektif > tbody').html(data.tabel_capaian_efektif);
                $('#label_waktuefektif').html(data.menit_kerja);
                $('#label_tdkhadir').html(data.total_pengurangan);
                $('#label_hasil_waktu').html(data.hasil_pengurangan);
                $('#label_capaianefektif').html(data.nilai_capaian);
                $('#label_tambahwaktu').html(data.total_penambahan);
                $('#label_hasil_tambah').html(data.hasil_penambahan);
                $('#label_poin_capaian').html(data.poin_capaian);
                $('#label_persen_capaian').html(data.persen_capaian);
                $('#label_nilai_pengurangan').html(data.persen_kurang);
                $('#label_max_efektif').html(data.menit_kerja);
                $('#label_hari_kerja').html(data.hari_kerja);
                $('#label_max_efektif2').html(data.menit_kerja);
                $('#label_nilai_aktifitas').html(data.persen_capaian);
                $('#label_aktifitas70').html(aktifitas70_fix.toFixed(2));
                $('#label_nilai_prilaku').html(data.persen_perilaku);
                // $('#label_nilai_total').html(total.toFixed(2));
                $('#label_nilai_total').html(data.nilai_total);
                $("#serapan_anggaran_label").html(data.persen_serapan);
                $('#label_nilai_serapan').html(data.persen_serapan);
            }
        });
    }
}
</script>