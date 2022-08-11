<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
    var table_usergroup;
    $(document).ready(function(){
        table_usergroup = $('#usergroup_table').DataTable({ 
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
      //      "scrollX": true,

            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": ci_baseurl + "sistem_admin/user_group/ajax_list_usergroup",
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
        table_usergroup.columns.adjust().draw();
        
        $('button#buttonSimpanUserGroup').click(function () {
            jConfirm("Anda yakin akan menyimpan data ini ?", "Ok", "Cancel", function (r) {
    //        alertify.confirm("Are you sure want to submit ?", function(r){
                if (r) {
                    var result = $('#group_list').jstree('get_selected');
                    $.ajax({
                        url: ci_baseurl + "sistem_admin/user_group/do_update_usergroup",
                        type: 'POST',
                        dataType: 'JSON',
                        data: $('form#fmUpdateUserGroup').serialize()+ "&tree_res=" +result,
                        success: function (data) {
                            //var data = $.parseJSON(data);
                            var ret = data.success;
                            $('input[name=' + data.csrfTokenName + ']').val(data.csrfHash);
                            if (ret === true) {
                                $('#upd_alert_type').removeClass('alert alert-danger alert-dismissable').addClass('alert alert-success alert-dismissable');
                                $('#upd_alert_message').html(data.messages);
                                $('#upd_alert_type').show();
                                $("#upd_alert_type").fadeTo(2000, 500).slideUp(500, function () {
                                    $("#upd_alert_type").hide();
                                    //$('.modal.in').modal('hide');
                                });

                                table_usergroup.columns.adjust().draw();

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
    
    function editUserGroup(user_id){
        if(user_id){    
            $.ajax({
            url: ci_baseurl + "sistem_admin/user_group/get_userby_id",
            type: 'get',
            dataType: 'json',
            data: {user_id:user_id},
                success: function(data) {
                    var ret = data.success;
                    if(ret === true) {
                        //console.log(data.data);
                        $('#user_id').val(data.data.id);
                        $('#username').val(data.data.nama_lengkap);
                        $('#update-usergroup-modal').modal('toggle');
                        group_update(user_id);
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
            $('#notification_message').html("User ID not exist");
            $('#notification_type').show();
            $("#notification_type").fadeTo(2000, 500).slideUp(500, function () {
                $("#notification_type").hide();
            });
        }
    }
    
    function group_update(user_id){
        $.ajax({
            url: ci_baseurl + "sistem_admin/user_group/ajax_group_update",
            type: 'get',
            dataType: 'json',
            data: {user_id:user_id}, 
            success: function(data) {
                $('#group_list').html("");
                $.jstree.destroy();
                var ret = data.success;
                if(ret === true){
                    jstree = $("#group_list").jstree({
                        "core" : {
                            "data" : data.data_valid
                            //"themes" : {
                            //    "variant" : "large"
                            //}
                        },
                        "checkbox" : {
                            "keep_selected_style" : false,
                            "three_state" : false
    //                        "tie_selection" : false
                        },
                        "plugins" : [ "checkbox" ]
                    });
                }else{
                    $('#group_list').html(data.messages);
                }
            }
        });
    }
</script>