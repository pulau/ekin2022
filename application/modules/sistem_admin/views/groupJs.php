<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
    var table_group;
    $(document).ready(function(){
        table_group = $('#group_table').DataTable({ 
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
      //      "scrollX": true,

            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": ci_baseurl + "sistem_admin/group/ajax_list_group",
                "type": "GET"
            },
            //Set column definition initialisation properties.
            "columnDefs": [
            { 
              "targets": [-1,0,3], //last column
              "orderable": false //set not orderable
            }
            ]
        });
        table_group.columns.adjust().draw();
        
        $('button#buttonSimpanGroup').click(function () {
            jConfirm("Yakin untuk meneruskan ?", "Ok", "Cancel", function (r) {
            //alertify.confirm("Are you sure want to submit ?", function(r){
                if (r) {
                    var result = $('#perm_list').jstree('get_selected');
                    $.ajax({
                        url: ci_baseurl + "sistem_admin/group/do_create_group",
                        type: 'POST',
                        dataType: 'JSON',
                        data: $('form#fmCreateGroup').serialize()+ "&tree_res=" +result,
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

                                $('#fmCreateGroup').find("select").val("");
                                $('#fmCreateGroup').find("input[type=text]").val("");
                                $('#fmCreateGroup').find("textarea").val("");

                                table_group.columns.adjust().draw();

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
        
        $('button#buttonUpdateGroup').click(function () {
            jConfirm("Are you sure want to submit ?", "Ok", "Cancel", function (r) {
    //        alertify.confirm("Are you sure want to submit ?", function(r){
                if (r) {
                    var result = $('#upd_perm_list').jstree('get_selected');
                    $.ajax({
                        url: ci_baseurl + "sistem_admin/group/do_update_group",
                        type: 'POST',
                        dataType: 'JSON',
                        data: $('form#fmUpdateGroup').serialize()+ "&tree_res=" +result,
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

                                table_group.columns.adjust().draw();

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
    
    function perm_create(){
        $.ajax({
            url: ci_baseurl + "sistem_admin/group/ajax_perm_create",
            type: 'get',
            dataType: 'json',
            data: {}, 
            success: function(data) {
                $('#perm_list').html("");
                $.jstree.destroy();
                var ret = data.success;
                if(ret === true){
                    jstree = $("#perm_list").jstree({
                        "core" : {
                            "data" : data.data_valid
                            //"themes" : {
                            //    "variant" : "large"
                            //}
                        },
                        "checkbox" : {
                            "keep_selected_style" : false,
                            "three_state" : false,
                            //"tie_selection" : false
                        },
                        "plugins" : [ "checkbox" ]
                    });
                }else{
                    $('#perm_list').html(data.messages);
                }
            }
        });
    }
    
    function editGroup(group_id){
        if(group_id){    
            $.ajax({
            url: ci_baseurl + "sistem_admin/group/get_groupby_id",
            type: 'get',
            dataType: 'json',
            data: {group_id:group_id},
                success: function(data) {
                    var ret = data.success;
                    if(ret === true) {
                        //console.log(data.data);
                        $('#upd_group_id').val(data.data.id);
                        $('#upd_group_name').val(data.data.name);
                        $('#upd_definition').val(data.data.definition);
                        $('#update-group-modal').modal('toggle');
                        perm_update(group_id);
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
            $('#notification_message').html("Group ID not exist");
            $('#notification_type').show();
            $("#notification_type").fadeTo(2000, 500).slideUp(500, function () {
                $("#notification_type").hide();
            });
        }
    }
    
    function perm_update(group_id){
        $.ajax({
            url: ci_baseurl + "sistem_admin/group/ajax_perm_update",
            type: 'get',
            dataType: 'json',
            data: {group_id:group_id}, 
            success: function(data) {
                $('#upd_perm_list').html("");
                $.jstree.destroy();
                var ret = data.success;
                if(ret === true){
                    jstree = $("#upd_perm_list").jstree({
                        "core" : {
                            "data" : data.data_valid
                            //"themes" : {
                            //    "variant" : "large"
                            //}
                        },
                        "checkbox" : {
                            "keep_selected_style" : false,
                            "three_state" : false,
    //                        "tie_selection" : false
                        },
                        "plugins" : [ "checkbox" ]
                    });
                }else{
                    $('#upd_perm_list').html(data.messages);
                }
            }
        });
    }
    
    function deleteGroup(group_id){
        var jsonVariable = {};
        jsonVariable["group_id"] = group_id;
        jsonVariable[csrf_name] = $('#'+csrf_name+'_del').val();;
        if(group_id !==''){
            jConfirm("Anda yakin akan menghapus data ini ?","Ok","Cancel", function(r){
                if(r){
                    $.ajax({
                    url: ci_baseurl + "sistem_admin/group/do_delete_group",
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
    //                            $('button#reloadProduk').click();
                                table_group.columns.adjust().draw();
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
            $('#notification_message').html('Missing ID Group');
            $('#notification_type').show();
        }
    }
</script>