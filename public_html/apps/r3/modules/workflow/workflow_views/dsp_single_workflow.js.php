<?php
/**
Copyright (C) 2012 Tam Viet Tech. All rights reserved.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>

<script>
    function dsp_all_user_to_allot(tbl_id, group_code)
    {

        var ou_id = '<?php echo Session::get('root_ou_id') ?>';
        var url = '<?php echo $this->get_controller_url('user', 'cores'); ?>dsp_all_user_and_group_to_add/'
                + '&pop_win=1'
                + '&group=' + group_code
                + '&root_ou=' + ou_id;
        $('#hdn_tbl_id').val(tbl_id);
        $('#hdn_group_code').val(group_code);

        v_next_task = $("#" + tbl_id).attr('data-next_task');
        $("#hdn_next_task_code").val(v_next_task);

        showPopWin(url, 450, 350, do_assign);
    }

    function do_assign(returnVal)
    {
        var v_tbl_id = $('#hdn_tbl_id').val();

        tbl_s = '#' + v_tbl_id;

        var v_prev_task = $(tbl_s).attr('data-prev_task');

        for (var i = 0; i < returnVal.length; i++)
        {
            v_user_code = returnVal[i].user_code;
            v_user_name = returnVal[i].user_name;
            v_user_type = returnVal[i].user_type;
            v_job_title = returnVal[i].job_title;

            v_user_type_text = (v_user_type == 'user') ? 'NSD' : 'Nhóm';

            //Neu user chua co trong group thi them vao
            var tr_s = tbl_s + " input[name='chk_user'][value='" + v_user_code + "']";
            if ($(tr_s).length < 1)
            {
                v_checkbox_id = v_tbl_id + '_chk_user_' + v_user_code;
                v_tr_id = v_tbl_id + '_tr_user_' + v_user_code;

                html = '<tr id="' + v_tr_id + '">';
                html += '<td class="center">';
                html += '<input type="checkbox" name="chk_user" value="' + v_user_code + '" id="' + v_checkbox_id + '"'
                        + ' data-user_type="' + v_user_type + '" />';
                html += '</td>';
                html += '<td>';
                html += '<img src="' + SITE_ROOT + 'public/images/icon-16-' + v_user_type + '.png" border="0" align="absmiddle" />';
                html += '<label for="' + v_checkbox_id + '">' + v_user_name + '</label>';
                html += '</td>';
                html += '<td>' + v_job_title + '</td>';
                html += '</tr>';

                $(tbl_s).append(html);

                var v_task_code = v_tbl_id.replace('tbl_user_on_task_', '');
                var v_step_time = $("#task_" + v_task_code).attr('data-step_time');
                var v_task_time = $("#task_" + v_task_code).attr('data-task_time');
                var v_first_task = $("#task_" + v_task_code).attr('data-first_task');
                var v_prev_step_last_task = $("#task_" + v_task_code).attr('data-prev_step_last_task');
                var v_no_chain = $("#task_" + v_task_code).attr('data-no_chain');

                //Also, ajax to update
                $.post('<?php echo $this->get_controller_url(); ?>assign_user_on_task',
                        {record_tye_code: '<?php echo $v_record_type_code; ?>'
                                    , user_code: v_user_code
                                    , task_code: v_task_code
                                    , group_code: $('#hdn_group_code').val()
                                    , next_task_code: $("#hdn_next_task_code").val()
                                    , prev_task_code: v_prev_task
                                    , step_time: v_step_time
                                    , task_time: v_task_time
                                    , first_task: v_first_task
                                    , prev_step_last_task: v_prev_step_last_task
                                    , no_chain: v_no_chain
                        },
                function(result) {
                }
                );
            }
        }
        //Neu da remove het, hoac cho phep nhieu NSD cung thuc hien
        v_count_user = $("#" + v_tbl_id + " input[name='chk_user']").length;

        v_task_code = $("#" + v_tbl_id).attr('data-task');
        v_single_user = $("#task_" + v_task_code).attr('data-single_user');

        if (parseBoolean(v_single_user) && (v_count_user > 0))
        {
            $("#task_" + v_task_code + " .add_user").hide();
        }
        else
        {
            $("#task_" + v_task_code + " .add_user").show();
        }
        if (v_count_user > 0)
        {
            $("#task_" + v_task_code + " .remove_user").show();
        }

        $('#hdn_tbl_id').val('');
    }

    function remove_user(tbl_id)
    {
        var tbl_s = "#" + tbl_id + " input[name='chk_user']";
        var v_next_task = $("#" + tbl_id).attr("data-next_task");
        var v_prev_task = $("#" + tbl_id).attr("data-prev_task");

        $(tbl_s).each(function(index) {
            if ($(this).is(':checked'))
            {
                v_user_code = $(this).val();
                v_tr_id = tbl_id + '_tr_user_' + v_user_code;
                tr_s = '#' + v_tr_id;
                $(tr_s).remove();

                //Also, ajax to remove
                $.ajax({
                    url: '<?php echo $this->get_controller_url(); ?>remove_user_on_task',
                    type: "POST",
                    data: {record_tye_code: '<?php echo $v_record_type_code; ?>'
                                , user_code: v_user_code
                                , task_code: tbl_id.replace('tbl_user_on_task_', '')
                                , next_task: v_next_task
                                , prev_task: v_prev_task
                    },
                    success: function(response) {
                        $('#update_message').show();
                    }
                });
            }
        });

        //Neu da remove het, hoac cho phep nhieu NSD cung thuc hien
        v_count_user = $(tbl_s).length;

        v_task_code = $("#" + tbl_id).attr('data-task');
        v_single_user = $("#task_" + v_task_code).attr('data-single_user');

        if (parseBoolean(v_single_user) && (v_count_user > 0))
        {
            $("#task_" + v_task_code + " .add_user").hide();
        }
        else
        {
            $("#task_" + v_task_code + " .add_user").show();
        }

        if (v_count_user == 0)
        {
            $("#task_" + v_task_code + " .remove_user").hide();
        }
    }

    function btn_edit_workflow_onclick()
    {
        var url = '<?php echo $this->get_controller_url(); ?>dsp_plaintext_workflow/';

        url += QS + 'pop_win=1';
        url += '&record_type_code=' + $("#hdn_record_type_code").val();
        url += '&record_type_name=' + encodeURIComponent($("#hdn_record_type_name").val());

        showPopWin(url, 650, 550, reload_form);
    }

    function btn_edit_workflow_ui_onclick()
    {
        var url = '<?php echo $this->get_controller_url(); ?>ui/';

        $('#frmMain').attr('action', url).submit();
    }

    function reload_form()
    {
        $("#frmMain").submit();
    }

    function btn_copy_assign_onclick() {
        record_type_code = '<?php echo $v_record_type_code ?>';
        url = '<?php echo $this->get_controller_url() ?>' + 'dsp_copy_assign/' + record_type_code;
        showPopWin(url, 800, 600, function() {
            $('#frmMain').submit();
        });
    }
</script>