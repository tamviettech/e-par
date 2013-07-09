function sel_record_type_onchange(e)
{
    e.form.txt_record_type_code.value = e.value;
    if (trim(e.value) != '')
    {
        e.form.submit();
    }
}
function txt_record_type_code_onkeypress(evt)
{
    if (IE()) {
        theKey = window.event.keyCode;
    } else {
        theKey = evt.which;
    }

    if (theKey == 13) {
        v_record_type_code = trim($("#txt_record_type_code").val()).toUpperCase();
        $("#sel_record_type").val(v_record_type_code);
        if ($("#sel_record_type").val() != '')
        {
            $("#frmMain").submit();
        }
        else
        {
            $("#procedure").html('');
        }
    }
    return false;
}

//angular
function ui_ctrl($scope, $http, $timeout) {
    var sortableEle;

    $scope.modal_step = {
        visible: false,
        step_index: 0
    };
    $scope.modal_task = {
        visible: false,
        step_index: 0,
        tasks: [],
        modal_edit: {
            visible: false,
            task_index: 0,
            task: {}
        }
    };

    $scope.no_chain_step = [];

    $scope.get_step_class = function(no_chain_task_code) {
        if (no_chain_task_code)
            return 'no_chain';
        else
            return '';
    }

    $scope.step_width = '600';

    $timeout(function() {
        if (typeof $scope.proc['step'] == 'undefined')
            $scope.proc['step'] = [];
        else if (typeof $scope.proc['step'].length == 'undefined')
            $scope.proc['step'] = [$scope.proc['step']];

        if (typeof $scope.proc['no_chain_step'] == 'undefined')
            $scope.proc['no_chain_step'] = [];
        else if (typeof $scope.proc['no_chain_step'].length == 'undefined')
            $scope.proc['no_chain_step'] = [$scope.proc['no_chain_step']];

        $.each($scope.proc['step'], function(k, v) {
            if ($scope.get_no_chain_task_code(k)) {
                $scope.step_width = 400;
                return;
            }
        });
        $('.step_container').each(function() {
            if ($(this).find('li').length > 1) {
                $(this).width(850);
            }
        });
        $('.step').width($scope.step_width);
        $scope.rebuild_data();
    });

    $scope.not_last_step = function(idx) {
        return (idx < ($scope.proc['step'].length - 1));
    }

    $scope.modal_proc_info = {
        visible: false,
        data: {}
    }

    $scope.get_no_chain_step = function(task_code) {
        if (task_code == false)
        {
            return false;
        }

        if (typeof $scope['proc']['no_chain_step'] == 'undefined') {
            $scope['proc']['no_chain_step'] = [];
        }
        if (typeof $scope['proc']['no_chain_step'].length == 'undefined')
        {
            $scope['proc']['no_chain_step'] = [$scope['proc']['no_chain_step']];
        }
        for (i = 0; i < $scope['proc']['no_chain_step'].length; i++) {
            if (typeof $scope['proc']['no_chain_step'][i]['task'].length == 'undefined')
            {
                $scope['proc']['no_chain_step'][i]['task'] = [$scope['proc']['no_chain_step'][i]['task']];
            }

            for (j = 0; j < $scope['proc']['no_chain_step'][i]['task'].length; j++) {
                if ($scope['proc']['no_chain_step'][i]['task'][j]['@attributes']['code'] == task_code) {
                    return $scope['proc']['no_chain_step'][i];
                }
            }
        }
        return false;
    }

    $scope.get_no_chain_task_code = function(step_index) {
        step = $scope.proc['step'][step_index];
        if (typeof step['task'].length == 'undefined') {
            step['task'] = [step['task']];
        }
        n = step['task'].length;
        for (i = 0; i < n; i++) {
            task = step['task'][i];
            if (typeof task['@attributes']['next_no_chain'] != 'undefined') {
                return task['@attributes']['next_no_chain'];
            }
        }
        return false;
    }

    $scope.modal_step.save = function() {
        if ($scope.modal_step.step_type == 'step') {
            $scope.proc[$scope.modal_step.step_type][$scope.modal_step.step_index] = $scope.modal_step.step;
        } else {
            no_chain_task = $scope.get_no_chain_task_code($scope.modal_step.step_index);
            for (i = 0; i < $scope['proc']['no_chain_step'].length; i++) {
                if (typeof $scope['proc']['no_chain_step'][i]['task'].length == 'undefined') {
                    $scope['proc']['no_chain_step'][i]['task'] = [$scope['proc']['no_chain_step'][i]['task']];
                }
                for (j = 0; j < $scope['proc']['no_chain_step'][i]['task'].length; j++) {
                    if ($scope['proc']['no_chain_step'][i]['task'][j]['@attributes']['code'] == no_chain_task) {
                        $scope['proc']['no_chain_step'][i] = $scope.modal_step.step;
                        break;
                    }
                }
            }
        }

        $scope.modal_step.visible = false;
        $timeout(function() {
            $('.step').width($scope.step_width);
        });
    }

    $scope.dsp_all_task = function(step, is_no_chain) {
        $scope.modal_task.visible = true;
        $scope.modal_task.src_step = step;
        $scope.modal_task.is_no_chain = is_no_chain;
        if (typeof step['task'].length == 'undefined') {
            step['task'] = [step['task']];
        }
        $scope.modal_task.tasks = eval(JSON.stringify(step['task']));
    }

    $scope.delete_no_chain_step = function(step) {
        step_name = step['@attributes']['name'];
        for (i = 0; i < $scope.proc['no_chain_step'].length; i++) {
            if ($scope.proc['no_chain_step'][i]['@attributes']['name'] == step_name) {
                first_task_code = $scope.proc['no_chain_step'][i]['task'][0]['@attributes']['code'];
                for (j = 0; j < $scope.proc['step'].length; j++) {
                    if (typeof $scope.proc['step'][j]['task'].length == 'undefined')
                    {
                        $scope.proc['step']['task'] = [$scope.proc['step']['task']];
                    }
                    for (jj = 0; jj < $scope.proc['step'][j]['task'].length; jj++) {
                        next_no_chain_type = typeof $scope.proc['step'][j]['task'][jj]['@attributes']['next_no_chain'];
                        if (next_no_chain_type != 'undefined' && $scope.proc['step'][j]['task'][jj]['@attributes']['next_no_chain'] == first_task_code) {
                            delete $scope.proc['step'][j]['task'][jj]['@attributes']['next_no_chain'];
                        }
                    }
                }

                $scope.proc['no_chain_step'].splice(i, 1);
                if ($scope.proc['no_chain_step'].length == 0) {
                    $scope.step_width = 600;
                    $timeout(function() {
                        $('.step').width($scope.step_width);
                    });
                }
            }
        }
        $scope.rebuild_data();
    }

    $scope.dsp_step_details = function(idx, step_type) {
        if (step_type == 'step') {
            step = $scope.proc[step_type][idx];
        }
        else {
            step = $scope.get_no_chain_step($scope.get_no_chain_task_code(idx));
        }
        $scope.modal_step.visible = true;
        $scope.modal_step.step_index = idx;
        $scope.modal_step.step_type = step_type;
        $scope.modal_step.step = $.parseJSON(JSON.stringify(step));
    }

    $scope.add_no_chain_step = function(step_index) {
        if (typeof $scope.proc['step'][step_index]['task'].length == 'undefined') {
            $scope.proc['step'][step_index]['task'] = [$scope.proc['step'][step_index]['task']];
        }
        task_count = $scope.proc['step'][step_index]['task'].length;
        console.clear();
        console.log(task_count);
        $scope.step_width = '400';
        task_code = 'UNNAMED_NO_CHAIN_TASK';
        first_task = {
            '@attributes': {
                'code': $scope['proc']['@attributes']['code'] + '::' + task_code
                        , 'name': 'Công việc mới'
            }
        };
        no_chain_step = {
            "@attributes": {
                'name': 'Bước song song'
                        , 'order': step['@attributes']['order']
                        , 'time': 0
                        , 'role': ''
                        , 'code': ''
                        , 'no_chain': true
            }
            , 'task': [first_task]
        };
        if (typeof $scope['proc']['no_chain_step'] == 'undefined')
            $scope['proc']['no_chain_step'] = [];

        if (typeof $scope['proc']['no_chain_step'] == 'undefined')
            $scope['proc']['no_chain_step'] = [];
        $scope['proc']['no_chain_step'].push(no_chain_step);
        console.log($scope['proc']['step'][step_index]['task'][task_count - 1]);
        $scope['proc']['step'][step_index]['task'][task_count - 1]['@attributes']['next_no_chain'] = first_task['@attributes']['code'];
        $scope.rebuild_data();
        $timeout(function() {
            $('.step').width($scope.step_width);
            $('.step_container').each(function() {
                if ($(this).find('li').length > 1) {
                    $(this).width(850);
                }
            });
        });
    }

    $scope.goback = function() {
        $('#frmMain').attr('action', $scope.controller).submit();
    }

    $scope.add_step = function() {
        if (typeof $scope.proc['step'] != 'object') {
            $scope.proc['step'] = [];
        }
        $scope.proc['step'].push({
            "@attributes":
                    {
                        "order": $scope.proc['step'].length + 1
                                ,
                        "group": ""
                                ,
                        "name": "Bước thứ " + ($scope.proc['step'].length + 1),
                        "time": "0"
                    }
            ,
            "task": []
        });
        //sortableEle.refresh();
        $scope.rebuild_data();
    }


    $scope.delete_step = function(step_index) {
        no_chain_task_code = $scope.get_no_chain_task_code(step_index);
        if(typeof $scope.proc['no_chain_step'].length == 'undefined')
            $scope.proc['no_chain_step'] = [$scope.proc['no_chain_step']];
        for (i = 0; i < $scope.proc['no_chain_step'].length; i++) {
            for (j = 0; j < $scope.proc['no_chain_step'][i]['task'].length; j++)
            {
                if (no_chain_task_code == $scope.proc['no_chain_step'][i]['task'][j]['@attributes']['code']) {
                    $scope.proc['no_chain_step'].splice(i, 1);
                }
            }
        }
        $scope.proc['step'].splice(step_index, 1);
        $scope.rebuild_data();
    }

    $scope.submit_workflow = function() {
        if ($scope.submit_errors && $scope.submit_errors.length) {
            return false;
        }
        $scope.submiting = true;
        $.ajax({
            type: 'post',
            url: $scope.controller + 'update_workflow_service',
            data:
                    {
                        process: $scope.proc,
                        hdn_record_type_code: $scope.hdn_record_type_code,
                        hdn_xml_flow_file_path: $scope.hdn_xml_flow_file_path
                    },
            success: function(data) {
                $scope.submiting = false;
                if (data) {
                    alert(data);
                }
                $scope.$apply();
            }
        });
    }

    $scope.dragStart = function(e, ui) {
        ui.item.data('start', ui.item.index());
    }

    $scope.dragEnd = function(e, ui) {
        var start = ui.item.data('start'),
                end = ui.item.index();

        $scope.proc['step'].splice(end, 0,
                $scope.proc['step'].splice(start, 1)[0]);
        $scope.proc['step'][end]['@attributes']['order'] = end;
        $scope.rebuild_data();
        $scope.$apply();
    }

    $scope.modal_task.dragStart = function(e, ui) {
        ui.item.data('start', ui.item.index());
    }
    $scope.modal_task.dragEnd = function(e, ui) {
        var start = ui.item.data('start'),
                end = ui.item.index();

        $scope.modal_task.tasks.splice(end, 0,
                $scope.modal_task.tasks.splice(start, 1)[0]);
        $scope.rebuild_data();
        $scope.$apply();
    }

    $scope.modal_task.delete_task = function(task_index) {
        if ($scope.modal_task.is_no_chain) {
            //xoá no chain step nếu là task đầu của no chain
            if (task_index == 0) {
                no_chain_task_code = $scope.modal_task.tasks[task_index]['@attributes']['code'];
                //xoá next_no_chain ở task liên kết
                if (typeof $scope['proc']['step'].length == 'undefined')
                    $scope['proc']['step'] = [$scope['proc']['step']];

                for (i = 0; i < $scope['proc']['step'].length; i++) {
                    if (typeof $scope['proc']['step'][i]['task'] == 'undefined') {
                        $scope['proc']['step'][i]['task'] = [$scope['proc']['step'][i]['task']];
                    }
                    for (j = 0; j < $scope['proc']['step'][i]['task'].length; j++) {
                        if ($scope['proc']['step'][i]['task'][j]['@attributes']['next_no_chain'] == no_chain_task_code) {
                            delete $scope['proc']['step'][i]['task'][j]['@attributes']['next_no_chain'];
                            break;
                        }
                    }
                }

                //xoá no_chain_step
                if (typeof $scope['proc']['no_chain_step'].length == 'undefined')
                    $scope['proc']['no_chain_step'] = [$scope['proc']['no_chain_step']];
                for (i = 0; i < $scope['proc']['no_chain_step'].length; i++) {
                    if (typeof $scope['proc']['no_chain_step'][i]['task'] == 'undefined') {
                        $scope['proc']['no_chain_step'][i]['task'] = [$scope['proc']['no_chain_step'][i]['task']];
                    }
                    for (j = 0; j < $scope['proc']['no_chain_step'][i]['task'].length; j++) {
                        if ($scope['proc']['no_chain_step'][i]['task'][j]['@attributes']['code'] == no_chain_task_code) {
                            $scope['proc']['no_chain_step'].splice(i, 1);
                            $scope.modal_task.visible = false;
                            break;
                        }
                    }
                }
            }
        } else {
            if (typeof $scope.modal_task.tasks[task_index]['@attributes']['next_no_chain'] != 'undefined') {
                no_chain_code = $scope.modal_task.tasks[task_index]['@attributes']['next_no_chain'];
                //xoá no chain step liên kết (nếu là task thường)
                for (i = 0; i < $scope['proc']['no_chain_step'].length; i++) {
                    if (typeof $scope['proc']['no_chain_step'][i]['task'].length == 'undefined') {
                        $scope['proc']['no_chain_step'][i]['task'] = [$scope['proc']['no_chain_step'][i]['task']];
                    }
                    for (j = 0; j < $scope['proc']['no_chain_step'][i]['task'].length; j++)
                    {
                        if (no_chain_code == $scope['proc']['no_chain_step'][i]['task'][j]['@attributes']['code']) {
                            $scope['proc']['no_chain_step'].splice(i, 1);
                            break;
                        }
                    }
                }

            }
        }

        $scope.modal_task.tasks.splice(task_index, 1);
        $scope.rebuild_data();
    }

    $scope.modal_task.hide = function() {
        $scope.modal_task.visible = false;
        $scope.modal_task.modal_edit.visible = false;
    }

    $scope.modal_task.add = function() {
        $scope.modal_task.tasks.push({
            "@attributes": {
                "code": $scope.proc['@attributes']['code'] + '::',
                "name": "Công việc mới",
                "time": "0",
                "next": "NULL"
            }
        });
        $scope.rebuild_data();
    }

    $scope.modal_task.modal_edit.disable_add_time = function() {
        if (typeof $scope.modal_task.modal_edit.task['@attributes'] == 'undefined') {
            return true;
        }
        return $scope.modal_task.modal_edit.task['@attributes']['code'].match(/KY_DUYET/ig) == null ? true : false;
    }

    $scope.modal_task.modal_edit.disable_pause = function() {
        if (typeof $scope.modal_task.modal_edit.task['@attributes'] == 'undefined') {
            return true;
        }
        return $scope.modal_task.modal_edit.task['@attributes']['code'].match(/TRA_THONG_BAO_NOP_THUE/ig) == null ? true : false;
    }

    $scope.modal_task.modal_edit.disable_unpause = function() {
        if (typeof $scope.modal_task.modal_edit.task['@attributes'] == 'undefined') {
            return true;
        }
        return $scope.modal_task.modal_edit.task['@attributes']['code'].match(/NHAN_BIEN_LAI_NOP_THUE/ig) == null ? true : false;
    }

    $scope.modal_task.modal_edit.disable_biz_done = function() {
        if (typeof $scope.modal_task.modal_edit.task['@attributes'] == 'undefined') {
            return true;
        }
        return $scope.modal_task.modal_edit.task['@attributes']['code'].match(/KY_DUYET/ig) == null ? true : false;
    }

    $scope.modal_task.save = function() {
        $scope.modal_task.src_step['task'] = $scope.modal_task.tasks;
        $scope.modal_task.visible = false;
        $scope.rebuild_data();
    }

    $scope.modal_task.edit = function(task_index) {
        $scope.modal_task.modal_edit.visible = true;
        $scope.modal_task.modal_edit.task_index = task_index;
        $scope.modal_task.modal_edit.task = $.parseJSON(JSON.stringify($scope.modal_task.tasks[task_index]));
        try {
            jQuery.each($scope.modal_task.modal_edit.task['@attributes'], function(k, v) {
                if (v == 'true') {
                    $scope.modal_task.modal_edit.task['@attributes'][k] = true;
                } else if (v == 'false') {
                    $scope.modal_task.modal_edit.task['@attributes'][k] = false;
                }
            });
        } catch (e) {

        }

    }

    $scope.modal_proc_info.show = function() {
        $scope.modal_proc_info.visible = true;
        $scope.modal_proc_info.data = $.parseJSON(JSON.stringify($scope.proc['@attributes']));
    }

    $scope.modal_proc_info.save = function() {
        $scope.proc['@attributes'] = $scope.modal_proc_info.data;
        $scope.modal_proc_info.visible = false;
    }

    $scope.modal_task.modal_edit.save = function() {
        idx = $scope.modal_task.modal_edit.task_index;
        task = $scope.modal_task.tasks[idx];
        old_task_code = $scope.modal_task.src_step['task'][idx]['@attributes']['code'];
        new_task_code = $scope.modal_task.modal_edit.task['@attributes']['code'];

        //cập nhật lại code của task liên kết no chain
        if ($scope.modal_task.is_no_chain && idx == 0) {
            //cập nhật lại next_no_chain của task thường
            if (typeof $scope['proc']['step'].length == 'undefined')
                $scope['proc']['step'] = [$scope['proc']['step']];

            for (i = 0; i < $scope['proc']['step'].length; i++) {
                if (typeof $scope['proc']['step'][i]['task'].length == 'undefined')
                    $scope['proc']['step'][i]['task'] = [$scope['proc']['step'][i]['task']];
                for (j = 0; j < $scope['proc']['step'][i]['task'].length; j++) {
                    if ($scope['proc']['step'][i]['task'][j]['@attributes']['next_no_chain'] == old_task_code) {
                        $scope['proc']['step'][i]['task'][j]['@attributes']['next_no_chain'] = new_task_code;
                        break;
                    }
                }
            }
        }

        $scope.modal_task.tasks[idx] = $scope.modal_task.modal_edit.task;
        $scope.modal_task.modal_edit.visible = false;
    }

    $scope.modal_task.modal_edit.print_selected = function(role_index) {
        role_code = $scope.proc['@attributes']['code'] + $scope.roles[role_index];
        task_code = $scope.modal_task.modal_edit.task['@attributes']['code'];
        if (role_code == task_code) {
            return 'selected'
        }
        return '';
    }

    //Cập nhật lại công việc tiếp theo
    $scope.rebuild_data = function() {
        var count_step = $scope.proc['step'].length;
        var next_step = 'NULL';
        var step_code_repeat = {}; //trung lap ma
        var task_code_repeat = {}; //trung lap ma
        $scope.submit_errors = [];

        for (i = count_step - 1; i >= 0; i--) {
            v_step = $scope.proc['step'][count_step - 1 - i];
            v_step_name = v_step['@attributes']['name'] || '';
            v_step_code = v_step['@attributes']['code'];

            if (v_step_code && step_code_repeat[v_step_code]) {
                v_step.error = 'required';

                $scope.submit_errors.push('Trùng mã bước "' + v_step_code + '" tại "' + v_name + '", bạn cần sửa mã bước bị trùng hoặc để trống');
            } else {
                step_code_repeat[v_step_code] = 1;
            }
            if (typeof $scope.proc['step'][i]['task'].length == 'undefined')
                $scope.proc['step'][i]['task'] = [$scope.proc['step'][i]['task']];

            if (typeof $scope.proc['step'][i]['task'].length == 'undefined')
                $scope.proc['step'][count_step - 1 - i]['task'] = [$scope.proc['step'][i]['task']];

            var count_task = $scope.proc['step'][i]['task'].length;
            $scope.proc['step'][i]['@attributes']['order'] = i + 1;
            for (j = count_task - 1; j >= 0; j--) {
                $scope.proc['step'][i]['task'][j]['@attributes']['order'] = j + 1;
                //next
                if (i == count_step - 1 && j == count_task - 1) {
                    //task cuoi
                    $scope.proc['step'][i]['task'][j]['@attributes']['next'] = 'NULL';
                } else {
                    //task binh thuong
                    $scope.proc['step'][i]['task'][j]['@attributes']['next'] = next_task_code;
                }
                var next_task_code = $scope.proc['step'][i]['task'][j]['@attributes']['code'];
                if (typeof $scope.proc['step'][i]['task'][j]['@attributes']['next_no_chain'] != 'undefined')
                {
                    $scope.proc['step'][i]['task'][j]['@attributes']['code'] += ' [' + $scope.proc['step'][i]['task'][j]['@attributes']['next_no_chain'] + ']';
                }
            }


            for (j = 0; j < $scope.proc['step'][count_step - 1 - i]['task'].length; j++) {
                v_task_code = $scope.proc['step'][count_step - 1 - i]['task'][j]['@attributes']['code'];
                if (task_code_repeat[v_task_code]) {
                    $scope.proc['step'][count_step - 1 - i]['task'][j].error = 'required';
                    $scope.proc['step'][count_step - 1 - i].error = 'required';
                    $scope.submit_errors.push('Trùng mã công việc "' + v_task_code + '" tại bước "' + v_step_name + '", bạn cần sửa mã công việc');
                } else {
                    task_code_repeat[v_task_code] = 1;
                }
            }
        }

        $scope.rebuild_no_chain();
    } //rebuild_data

    $scope.rebuild_no_chain = function() {
        if (typeof $scope['proc']['no_chain_step'].length == 'undefined') {
            $scope['proc']['no_chain_step'] = [$scope['proc']['no_chain_step']];
        }
        for (i = 0; i < $scope['proc']['no_chain_step'].length; i++) {
            if (typeof $scope['proc']['no_chain_step'][i]['task'].length == 'undefined') {
                $scope['proc']['no_chain_step'][i]['task'] = [$scope['proc']['no_chain_step'][i]['task']];
            }
            next_task = 'FINISH_NO_CHAIN_STEP';
            for (j = $scope['proc']['no_chain_step'][i]['task'].length - 1; j >= 0; j--) {
                $scope['proc']['no_chain_step'][i]['task'][j]['@attributes']['next'] = next_task;
                next_task = $scope['proc']['no_chain_step'][i]['task'][j]['@attributes']['code'];
            }
        }
    }

    sortableEle = $("#contentLeft #all_step").sortable({
        opacity: '0.6',
        cursor: 'move',
        start: $scope.dragStart,
        update: $scope.dragEnd,
        placeholder: 'holder'
    });

    sortableEle = $("#contentLeft #all_task").sortable({
        opacity: 0.6,
        cursor: 'move',
        start: $scope.modal_task.dragStart,
        update: $scope.modal_task.dragEnd,
        placeholder: 'holder'
    });
}