<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
    var table_userlogs;
    $(document).ready(function(){
        table_userlogs = $('#userlogs_table').DataTable({ 
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [[2,'desc']],

            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": ci_baseurl + "sistem_admin/userlogs/ajax_list_userlogs",
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
        table_userlogs.columns.adjust().draw();
    });
    
    function detailLog(log_id){
        if(log_id){    
            $.ajax({
            url: ci_baseurl + "sistem_admin/userlogs/ajax_get_userlog_detail",
            type: 'get',
            dataType: 'json',
            data: {log_id:log_id},
                success: function(data) {
                    var ret = data.success;
                    if(ret === true) {
                        //console.log(data.data);
                        $('#tanggal').html(data.data.dateactivity);
                        $('#user').html(data.data.nama_user);
                        $('#ipaddr').html(data.data.ipaddr);
                        $('#perm').html(data.data.perms);
                        $('#perm_detail').html(data.data.comments);
                        $('#detail-userslog-modal').modal('toggle');
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
</script>