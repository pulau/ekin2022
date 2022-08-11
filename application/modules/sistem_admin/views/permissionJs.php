<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
    var table_perm;
    $(document).ready(function(){
        table_perm = $('#permission_table').DataTable({ 
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
      //      "scrollX": true,

            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": ci_baseurl + "sistem_admin/permission/ajax_list_perm",
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
        table_perm.columns.adjust().draw();
        
        $('button#buttonSimpanPerm').click(function () {
            jConfirm("Yakin untuk meneruskan ?", "Ok", "Cancel", function (r) {
            //alertify.confirm("Are you sure want to submit ?", function(r){
                if (r) {
                    $.ajax({
                        url: ci_baseurl + "sistem_admin/permission/do_insert_perm",
                        type: 'POST',
                        dataType: 'JSON',
                        data: $('form#fmCreatePermission').serialize(),
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

                                $('#fmCreatePermission').find("select").val("");
                                $('#fmCreatePermission').find("input[type=text]").val("");
                                $('#fmCreatePermission').find("textarea").val("");

                                table_perm.columns.adjust().draw();

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
        
        $('button#buttonUpdatePerm').click(function () {
            jConfirm("Yakin untuk meneruskan ?", "Ok", "Cancel", function (r) {
    //        alertify.confirm("Are you sure want to submit ?", function(r){
                if (r) {
                    $.ajax({
                        url: ci_baseurl + "sistem_admin/permission/do_update_perm",
                        type: 'POST',
                        dataType: 'JSON',
                        data: $('form#fmUpdatePermission').serialize(),
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

                                table_perm.columns.adjust().draw();

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
    
    function editPerm(id_perm){
        if(id_perm){    
            $.ajax({
            url: ci_baseurl + "sistem_admin/permission/ajax_get_permission_by_id",
            type: 'get',
            dataType: 'json',
            data: {id_perm:id_perm},
                success: function(data) {
                    var ret = data.success;
                    if(ret === true) {
                        //console.log(data.data);
                        $('#upd_id_perm').val(data.data.id);
                        $('#upd_perm_name').val(data.data.name);
                        $('#upd_definition').val(data.data.definition);
                        $('#upd_icon').val(data.data.icon);
                        $('#upd_url').val(data.data.url);
                        $('#upd_parent').val(data.data.parent_id);
                        $('#upd_module').val(data.data.module_id);
                        $('#upd_urutan').val(data.data.urutan_menu);
                        $('#update-permission-modal').modal('toggle');
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
            $('#notification_message').html("Permission ID not exist");
            $('#notification_type').show();
            $("#notification_type").fadeTo(2000, 500).slideUp(500, function () {
                $("#notification_type").hide();
            });
        }
    }
    
    function deletePerm(perm_id){
        var jsonVariable = {};
        jsonVariable["perm_id"] = perm_id;
        jsonVariable[csrf_name] = $('#'+csrf_name+'_del').val();;
        if(perm_id !==''){
            jConfirm("Anda yakin akan menghapus data ini ?","Ok","Cancel", function(r){
                if(r){
                    $.ajax({
                    url: ci_baseurl + "sistem_admin/permission/do_delete_perm",
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
                                table_perm.columns.adjust().draw();
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
            $('#notification_message').html('Missing ID Permission');
            $('#notification_type').show();
        }
    }
</script>