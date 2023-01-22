<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
var table_aktifitas;
$(document).ready(function() {
    table_aktifitas = $('#table_aktifitas').DataTable({
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [
            [1, "ASC"]
        ],
        //"scrollX": true,

        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": ci_baseurl + "ekinerja/input_aktifitas/ajax_list_aktifitas",
            "type": "GET",
            "data": function(d) {
                d.filter_tgl = $("#filter_tgl").val();
            }
        },
        //Set column definition initialisation properties.
        "columnDefs": [{
                "targets": [-1], //last column
                "orderable": false //set not orderable
            },
            {
                "width": "40%",
                "targets": 0
            },
            {
                "width": "20%",
                "targets": 1
            },
            {
                "width": "10%",
                "targets": 2
            },
            {
                "width": "10%",
                "targets": 3
            }
        ]

    });

    $('#calendar').fullCalendar({
        locale: 'id',
        contentHeight: "auto",
        selectable: true,
        header: {
            right: 'prev,next'
        },
        //    dayClick: function(date, jsEvent, view, resourceObj) {
        dayClick: function(date, jsEvent) {
            //    console.log(date);
            //    return false;
            $("#filter_tgl").val(date.format());
            $("#tgl_aktif").html(date.format());
            $("#tgl_aktifitas").val(date.format());
            table_aktifitas.columns.adjust().draw();
            batas_input(date.format());
            // alert('Date: ' + date.format());
            //    $(this).css('background-color', 'red');
        }
    });

    $('.select2').select2({
        placeholder: 'Pilih Aktifitas'
        //width: null,
        //    dropdownParent: $('.modal')
    });

    $('.jampicker').timepicker({
        autoclose: true,
        minuteStep: 1,
        showSeconds: false,
        showMeridian: false
    });

    $('button#btnSaveAktifitas').click(function() {
        jConfirm("Anda yakin akan menyimpan data ini ?", "Ok", "Cancel", function(r) {
            if (r) {
                $.ajax({
                    url: ci_baseurl + "ekinerja/input_aktifitas/do_insert_aktifitas",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formAktifitas').serialize(),
                    success: function(data) {
                        var ret = data.success;
                        $('input[name=' + data.csrfTokenName + ']').val(data
                            .csrfHash);
                        $('input[name=' + data.csrfTokenName + '_del]').val(data
                            .csrfHash);
                        if (ret === true) {
                            $('#alert_type').removeClass(
                                    'alert alert-dismissable alert-danger')
                                .addClass('alert alert-success alert-dismissable');
                            $('#alert_message').html(data.messages);
                            $('#alert_type').show();
                            $("#alert_type").fadeTo(2000, 500).slideUp(500,
                                function() {
                                    $("#alert_type").hide();
                                });
                            //clearForm();
                            $("html, body").animate({
                                scrollTop: 100
                            }, "fast");
                            table_aktifitas.columns.adjust().draw();
                        } else {
                            $('#alert_type').removeClass(
                                    'alert alert-dismissable alert-success')
                                .addClass('alert alert-danger alert-dismissable');
                            $('#alert_message').html(data.messages);
                            $('#alert_type').show();
                            $("#alert_type").fadeTo(2000, 500).slideUp(500,
                                function() {
                                    $("#alert_type").hide();
                                });
                            $("html, body").animate({
                                scrollTop: 100
                            }, "fast");
                        }
                    }
                });
            }
        });
    });

    $('button#btnUpdateAktifitas').click(function() {
        jConfirm("Anda yakin akan mengubah data ini ?", "Ok", "Cancel", function(r) {
            if (r) {
                $.ajax({
                    url: ci_baseurl + "ekinerja/input_aktifitas/do_update_aktifitas",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#formEditAktifitas').serialize(),
                    success: function(data) {
                        var ret = data.success;
                        $('input[name=' + data.csrfTokenName + ']').val(data
                            .csrfHash);
                        $('input[name=' + data.csrfTokenName + '_del]').val(data
                            .csrfHash);
                        if (ret === true) {
                            $('#upd_alert_type').removeClass(
                                    'alert alert-dismissable alert-danger')
                                .addClass('alert alert-success alert-dismissable');
                            $('#upd_alert_message').html(data.messages);
                            $('#upd_alert_type').show();
                            $("#upd_alert_type").fadeTo(2000, 500).slideUp(500,
                                function() {
                                    $("#upd_alert_type").hide();
                                });
                            $("html, body").animate({
                                scrollTop: 100
                            }, "fast");
                            table_aktifitas.columns.adjust().draw();
                        } else {
                            $('#upd_alert_type').removeClass(
                                    'alert alert-dismissable alert-success')
                                .addClass('alert alert-danger alert-dismissable');
                            $('#upd_alert_message').html(data.messages);
                            $('#upd_alert_type').show();
                            $("#upd_alert_type").fadeTo(2000, 500).slideUp(500,
                                function() {
                                    $("#upd_alert_type").hide();
                                });
                            $("html, body").animate({
                                scrollTop: 100
                            }, "fast");
                        }
                    }
                });
            }
        });
    });
});

function setVolume() {
    var jam_mulai = $("#jam_mulai").val();
    var jam_selesai = $("#jam_selesai").val();
    var waktu_efektif = $("#waktu_efektif").val();
    if (jam_mulai != '' && jam_selesai != '' && waktu_efektif != '') {
        $.ajax({
            url: ci_baseurl + "ekinerja/input_aktifitas/hitung_volume",
            type: 'GET',
            dataType: 'JSON',
            data: {
                jam_mulai: jam_mulai,
                jam_selesai: jam_selesai,
                waktu_efektif: waktu_efektif
            },
            success: function(data) {
                $("#jumlah").html(data.list_volume);
            }
        });
    }
}

function setWaktuEfektif() {
    var skpt_id = $("#skptahunan_id").val();
    if (skpt_id != '') {
        $.ajax({
            url: ci_baseurl + "ekinerja/input_aktifitas/ajax_get_skpt_by_id",
            type: 'GET',
            dataType: 'JSON',
            data: {
                skpt_id: skpt_id
            },
            success: function(data) {
                $("#waktu_efektif").val(data.data.waktu_skp);
            }
        });
    }
}

function setVolumeUpd() {
    var jam_mulai = $("#upd_jam_mulai").val();
    var jam_selesai = $("#upd_jam_selesai").val();
    var waktu_efektif = $("#upd_waktu_efektif").val();
    if (jam_mulai != '' && jam_selesai != '' && waktu_efektif != '') {
        $.ajax({
            url: ci_baseurl + "ekinerja/input_aktifitas/hitung_volume",
            type: 'GET',
            dataType: 'JSON',
            data: {
                jam_mulai: jam_mulai,
                jam_selesai: jam_selesai,
                waktu_efektif: waktu_efektif
            },
            success: function(data) {
                $("#upd_jumlah").html(data.list_volume);
            }
        });
    }
}

function setWaktuEfektifUpd() {
    var skpt_id = $("#upd_skptahunan_id").val();
    if (skpt_id != '') {
        $.ajax({
            url: ci_baseurl + "ekinerja/input_aktifitas/ajax_get_skpt_by_id",
            type: 'GET',
            dataType: 'JSON',
            data: {
                skpt_id: skpt_id
            },
            success: function(data) {
                $("#upd_waktu_efektif").val(data.data.waktu_skp);
            }
        });
    }
}

function editAktifitas(aktifitas_id) {
    if (aktifitas_id !== '') {
        $.ajax({
            url: ci_baseurl + "ekinerja/input_aktifitas/ajax_get_aktifitas_by_id",
            type: 'get',
            dataType: 'json',
            data: {
                aktifitas_id: aktifitas_id
            },
            success: function(data) {
                var ret = data.success;
                if (ret === true) {
                    $('#upd_aktifitas_id').val(data.data.aktifitas_id);
                    $('#upd_tgl_aktifitas').val(data.tgl_aktifitas);
                    $('#upd_skptahunan_id').val(data.data.skptahunan_id);
                    $("#upd_skptahunan_id").append("<option value='" + data.data.skptahunan_id +
                        "' selected>" + data.data.skp + "</option>");
                    $('#upd_skptahunan_id').trigger('change');
                    $('#upd_waktu_efektif').val(data.data.waktu_efektif);
                    $('#upd_jam_mulai').val(data.jam_mulai);
                    $('#upd_jam_selesai').val(data.jam_akhir);
                    $("#upd_jumlah").html(data.list_volume);
                    $('#upd_jumlah').val(data.data.jumlah);
                    $('#upd_uraian').val(data.data.uraian);
                    $('#editAktifitasModal').modal('toggle');
                } else {
                    $('#upd_alert_type').removeClass('alert alert-dismissable').addClass(
                        'alert alert-danger alert-dismissable');
                    $('#upd_alert_message').html(data.messages);
                    $('#upd_alert_type').show();
                }
            }
        });
    } else {
        $('#notification_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
        $('#notification_messages').html('Bagian ID tidak ditemukan');
        $('#notification_type').show();
    }
}

function deleteAktifitas(aktifitas_id) {
    var jsonVariable = {};
    jsonVariable["aktifitas_id"] = aktifitas_id;
    jsonVariable[csrf_name] = $('#' + csrf_name + '_del').val();;
    if (aktifitas_id !== '') {
        jConfirm("Anda yakin akan menghapus data ini ?", "Ok", "Cancel", function(r) {
            if (r) {
                $.ajax({
                    url: ci_baseurl + "ekinerja/input_aktifitas/do_delete_aktifitas",
                    type: 'post',
                    dataType: 'json',
                    data: jsonVariable,
                    success: function(data) {
                        var ret = data.success;
                        $('input[name=' + data.csrfTokenName + ']').val(data.csrfHash);
                        $('input[name=' + data.csrfTokenName + '_del]').val(data.csrfHash);
                        if (ret === true) {
                            $('#notification_type').removeClass(
                                'alert alert-danger alert-dismissable').addClass(
                                'alert alert-success alert-dismissable');
                            $('#notification_message').html(data.messages);
                            $("#notification_type").show();
                            $("#notification_type").fadeTo(2000, 500).slideUp(500, function() {
                                $("#notification_type").hide();
                            });
                            table_aktifitas.columns.adjust().draw();
                        } else {
                            $('#notification_type').removeClass(
                                'alert alert-success alert-dismissable').addClass(
                                'alert alert-danger alert-dismissable');
                            $('#notification_message').html(data.messages);
                            $("#notification_type").show();
                            $("#notification_type").fadeTo(2000, 500).slideUp(500, function() {
                                $("#notification_type").hide();
                            });
                        }
                    }
                });
            }
        });
    } else {
        $('#notification_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
        $('#notification_messages').html('Aktifitas tidak ditemukan');
        $('#notification_type').show();
    }
}

function batas_input(selectedDate) {
    var peg = <?php echo $this->data['pegawai']->id_pegawai; ?>;
    console.log("id pegawai adalah "+peg);

    //current date
    var d = new Date();
    var currDate = d.getDate(); //24
    var currMonth = d.getMonth() + 1; //5
    var currYear = d.getFullYear(); //2020
    var dd = d.getTime();
    console.log("Current d " + d); //24-5-2020

    //selected date
    var sd = new Date(selectedDate);
    var sDate = sd.getDate(); //24
    var sMonth = sd.getMonth() + 1; //5
    var sYear = sd.getFullYear(); //2020
    var sdd = sd.getTime();
    console.log("selected sd " + sd);


    if (sMonth < currMonth) {
        // tutup date
        var td = new Date(selectedDate);
        var tDate = '5'; //24
        var tMonth = d.getMonth() + 1; //5
        var tYear = td.getFullYear(); //2020
        var tutupbl = tMonth + "-" + tDate + "-" + tYear + " 00:00:00";
        var tutupbl1 = new Date(tutupbl);
        console.log("Tutup ekin bulan lalu adalah " + tutupbl1);
        $('#tools-box').html('<p class="text-red">Tidak diizinkan input Aktifitas</p>');

    } else {
        var td = new Date(selectedDate);
        var tDate = '5'; //24
        var tMonth = d.getMonth() + 2; //5
        var tYear = td.getFullYear(); //2020
        var tutupbd = tMonth + "-" + tDate + "-" + tYear + " 00:00:00";
        var tutupbd1 = new Date(tutupbd);
        console.log("Tutup ekin bulan depan adalah " + tutupbd1);
        $('#tools-box').html('<p class="text-red">Tidak diizinkan input Aktifitas</p>');

    }

    if (peg === 682 || peg === 632) {
        $('#tools-box').html(
            '<button type="button" class="btn btn-block btn-success" data-toggle="modal" data-target="#inputAktifitasModal" id="btnInputAktifitas"><i class="fa fa-plus"></i> Tambah Aktifitas</button>'
        );
        console.log("user "+peg+" diizinkan input aktifitas");
    }

}
</script>