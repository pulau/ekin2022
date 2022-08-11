<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
    var table_module;
    $(document).ready(function(){
        table_module = $('#module_table').DataTable({ 
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
      //      "scrollX": true,

            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": ci_baseurl + "sistem_admin/module/ajax_list_module",
                "type": "GET"
            },
            //Set column definition initialisation properties.
            "columnDefs": [
            { 
              "targets": [-1,0], //last column
              "orderable": false //set not orderable
            }
            ]
        });
        table_module.columns.adjust().draw();
        
        $('button#buttonSimpanModule').click(function () {
            jConfirm("Yakin untuk meneruskan ?", "Ok", "Cancel", function (r) {
            //alertify.confirm("Are you sure want to submit ?", function(r){
                if (r) {
                    $.ajax({
                        url: ci_baseurl + "sistem_admin/module/do_insert_module",
                        type: 'POST',
                        dataType: 'JSON',
                        data: $('form#fmCreateModule').serialize(),
                        success: function (data) {
                            //var data = $.parseJSON(data);
                            var ret = data.success;
                            $('input[name=' + data.csrfTokenName + ']').val(data.csrfHash);
                            $('input[name=' + data.csrfTokenName + '_del]').val(data.csrfHash);
                            if (ret === true) {
                                $('#alert_type').removeClass('alert alert-success alert-dismissable').addClass('alert alert-success alert-dismissable');
                                $('#alert_message').html(data.messages);
                                $('#alert_type').show();
                                $("#alert_type").fadeTo(2000, 500).slideUp(500, function () {
                                    $("#alert_type").hide();
                                    //$('.modal.in').modal('hide');
                                });

                                $('#fmCreateModule').find("select").val("");
                                $('#fmCreateModule').find("input[type=text]").val("");
                                $('#fmCreateModule').find("textarea").val("");

                                table_module.columns.adjust().draw();

                                //$("#createAnalisareject").animate({scrollTop: 0}, "fast");
                            } else {
                                $('#alert_type').removeClass('alert alert-danger alert-dismissable').addClass('alert alert-danger alert-dismissable');
                                $('#alert_message').html(data.messages);
                                $('#alert_type').show();
                                $("#alert_type").fadeTo(2000, 500).slideUp(500, function () {
                                    $("#alert_type").hide();
                                });
                            }
                        }
                    });
                }
            });
        });
        
        $('button#buttonUpdateModule').click(function () {
            jConfirm("Yakin untuk meneruskan ?", "Ok", "Cancel", function (r) {
    //        alertify.confirm("Are you sure want to submit ?", function(r){
                if (r) {
                    $.ajax({
                        url: ci_baseurl + "sistem_admin/module/do_update_module",
                        type: 'POST',
                        dataType: 'JSON',
                        data: $('form#fmUpdateModule').serialize(),
                        success: function (data) {
                            //var data = $.parseJSON(data);
                            var ret = data.success;
                            $('input[name=' + data.csrfTokenName + ']').val(data.csrfHash);
                            $('input[name=' + data.csrfTokenName + '_del]').val(data.csrfHash);
                            if (ret === true) {
                                $('#upd_alert_type').removeClass('alert alert-danger alert-dismissable').addClass('alert alert-success alert-dismissable');
                                $('#upd_alert_message').html(data.messages);
                                $('#upd_alert_type').show();
                                $("#upd_alert_type").fadeTo(2000, 500).slideUp(500, function () {
                                    $("#upd_alert_type").hide();
                                    //$('.modal.in').modal('hide');
                                });

                                table_module.columns.adjust().draw();

                                //$("#createAnalisareject").animate({scrollTop: 0}, "fast");
                            } else {
                                $('#upd_alert_type').removeClass('alert alert-success alert-dismissable').addClass('alert alert-danger alert-dismissable');
                                $('#upd_alert_message').html(data.messages);
                                $('#upd_alert_type').show();
                                $("#upd_alert_type").fadeTo(2000, 500).slideUp(500, function () {
                                    $("#upd_alert_type").hide();
                                });
                            }
                        }
                    });
                }
            });
        });
    });
    
    function editModule(id_module){
        if(id_module){    
            $.ajax({
            url: ci_baseurl + "sistem_admin/module/ajax_get_module_by_id",
            type: 'get',
            dataType: 'json',
            data: {id_module:id_module},
                success: function(data) {
                    var ret = data.success;
                    if(ret === true) {
                        //console.log(data.data);
                        $('#upd_id_module').val(data.data.id);
                        $('#upd_module_name').val(data.data.name);
                        $('#upd_label').val(data.data.label);
                        $('#upd_icon').val(data.data.modul_icon);
                        $('#upd_url').val(data.data.modul_url);
                        $('#upd_kategori').val(data.data.modul_kategori);
                        $('#upd_urutan').val(data.data.modul_urutan);
                        $('#update-module-modal').modal('toggle');
                    } else {
                        $('#notification_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
                        $('#notification_message').html(data.messages);
                        $('#notification_type').show();
                        $("#notification_type").fadeTo(2000, 500).slideUp(500, function () {
                            $("#notification_type").hide();
                        });
                    }
                }
            });
        }
        else
        {
            $('#notification_type').removeClass('alert alert-dismissable').addClass('alert alert-danger alert-dismissable');
            $('#notification_message').html("Module ID not exist");
            $('#notification_type').show();
            $("#notification_type").fadeTo(2000, 500).slideUp(500, function () {
                $("#notification_type").hide();
            });
        }
    }
    
    function deleteModule(module_id){
        var jsonVariable = {};
        jsonVariable["module_id"] = module_id;
        jsonVariable[csrf_name] = $('#'+csrf_name+'_del').val();;
        if(module_id !==''){
            jConfirm("Anda yakin akan menghapus data ini ?","Ok","Cancel", function(r){
                if(r){
                    $.ajax({
                    url: ci_baseurl + "sistem_admin/module/do_delete_module",
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
                                table_module.columns.adjust().draw();
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
            $('#notification_message').html('Missing ID Module');
            $('#notification_type').show();
        }
    }
</script>