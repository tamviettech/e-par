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
    if (IE()){
        theKey=window.event.keyCode;
    } else {
        theKey=evt.which;
    }

    if(theKey == 13){
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
function ui_ctrl($scope){
    var sortableEle;
    
    $scope.modal_step = {
        visible: false,
        step_index: 0
    };
    $scope.modal_task ={
        visible: false, 
        step_index: 0, 
        tasks: [],
        modal_edit: {
            visible: false,
            task_index: 0,
            task: {}
        }
    };
    
    $scope.modal_proc_info = {
        visible: false,
        data: {}
    }
    
    $scope.modal_step.save = function(){
        $scope.proc['step'][$scope.modal_step.step_index] = $scope.modal_step.step;
        $scope.modal_step.visible = false;
    }
        
    $scope.dsp_all_task = function(step_index){
        $scope.modal_task.visible = true;
        $scope.modal_task.step_index = step_index;
        if(typeof $scope.proc['step'][step_index]['task'].length == 'undefined'){
            $scope.proc['step'][step_index]['task'] = [$scope.proc['step'][step_index]['task']];
        }
        $scope.modal_task.tasks = eval(JSON.stringify($scope.proc['step'][step_index]['task']));
    }
    
    $scope.dsp_step_details = function(step_index){
        $scope.modal_step.visible = true;
        var step = $scope.proc['step'][step_index];
        $scope.modal_step.step = $.parseJSON(JSON.stringify(step));
        $scope.modal_step.step_index = step_index;
    }
    
    $scope.goback = function(){
        $('#frmMain').attr('action', $scope.controller).submit();
    }
    
    $scope.add_step = function() {
        $scope.proc['step'].push({
            "@attributes": 
            {
                "order": $scope.proc['step'].length + 1
                ,
                "group":""
                ,
                "name":"Bước thứ " + ($scope.proc['step'].length + 1),
                "time":"0"
            }
            ,
            "task": []
        });
        sortableEle.refresh();
        $scope.rebuild_data();
    }
    
    $scope.delete_step = function(step_index){
        $scope.proc['step'].splice(step_index,1);
        $scope.rebuild_data();
    }
    
    $scope.submit_workflow = function(){
        if($scope.submit_errors && $scope.submit_errors.length){
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
            success: function(data){
                $scope.submiting = false;
                if(data){
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
    
    $scope.modal_task.delete_task = function(task_index){
        $scope.modal_task.tasks.splice(task_index,1);
        $scope.rebuild_data();
    }
    
    $scope.modal_task.hide = function(){
        $scope.modal_task.visible = false;
        $scope.modal_task.modal_edit.visible = false;
    }
    
    $scope.modal_task.add = function(){
        $scope.modal_task.tasks.push({
            "@attributes":{
                "code": $scope.proc['@attributes']['code'] + '::',
                "name":"Công việc mới",
                "time":"0",
                "next":"NULL"
            }
        });
        $scope.rebuild_data();
    }
    
    $scope.modal_task.modal_edit.disable_add_time = function(){
        if(typeof $scope.modal_task.modal_edit.task['@attributes'] == 'undefined'){
            return true;
        }
        return $scope.modal_task.modal_edit.task['@attributes']['code'].match(/KY_DUYET/ig) == null ? true : false;
    }
    
    $scope.modal_task.modal_edit.disable_pause = function(){
        if(typeof $scope.modal_task.modal_edit.task['@attributes'] == 'undefined'){
            return true;
        }
        return $scope.modal_task.modal_edit.task['@attributes']['code'].match(/TRA_THONG_BAO_NOP_THUE/ig) == null ? true : false;
    }
    
    $scope.modal_task.modal_edit.disable_unpause = function(){
        if(typeof $scope.modal_task.modal_edit.task['@attributes'] == 'undefined'){
            return true;
        }
        return $scope.modal_task.modal_edit.task['@attributes']['code'].match(/NHAN_BIEN_LAI_NOP_THUE/ig) == null ? true : false;
    }
    
    $scope.modal_task.modal_edit.disable_biz_done = function(){
        if(typeof $scope.modal_task.modal_edit.task['@attributes'] == 'undefined'){
            return true;
        }
        return $scope.modal_task.modal_edit.task['@attributes']['code'].match(/KY_DUYET/ig) == null ? true : false;
    }
    
    $scope.modal_task.save = function(){
        $scope.proc['step'][$scope.modal_task.step_index]['task'] = $scope.modal_task.tasks;
        $scope.modal_task.visible = false;
        $scope.rebuild_data();
    }
    
    $scope.modal_task.edit = function(task_index){
        $scope.modal_task.modal_edit.visible = true;
        $scope.modal_task.modal_edit.task_index = task_index;
        $scope.modal_task.modal_edit.task = $.parseJSON(JSON.stringify($scope.modal_task.tasks[task_index]));
        try{
            jQuery.each( $scope.modal_task.modal_edit.task['@attributes'], function(k, v){
                if(v == 'true'){
                    $scope.modal_task.modal_edit.task['@attributes'][k] = true;
                }else if(v == 'false'){
                    $scope.modal_task.modal_edit.task['@attributes'][k] = false;
                }
            });
        }catch(e){
        
        }
        
    }
    
    $scope.modal_proc_info.show = function(){
        $scope.modal_proc_info.visible = true;
        $scope.modal_proc_info.data = $.parseJSON(JSON.stringify($scope.proc['@attributes']));
    }
    
    $scope.modal_proc_info.save = function(){
        $scope.proc['@attributes'] = $scope.modal_proc_info.data;
        $scope.modal_proc_info.visible = false;
    }
    
    $scope.modal_task.modal_edit.save = function(){
        $scope.modal_task.tasks[$scope.modal_task.modal_edit.task_index] = $scope.modal_task.modal_edit.task;
        $scope.modal_task.modal_edit.visible = false;
    }
    
    $scope.modal_task.modal_edit.print_selected = function(role_index){
        role_code = $scope.proc['@attributes']['code'] + $scope.roles[role_index];
        task_code = $scope.modal_task.modal_edit.task['@attributes']['code'];
        if(role_code == task_code){
            return 'selected'
        }
        return '';
    }
    
    //Cập nhật lại công việc tiếp theo
    $scope.rebuild_data = function(){
        var count_step = $scope.proc['step'].length;
        var next_step = 'NULL';
        var step_code_repeat = {}; //trung lap ma
        var task_code_repeat = {}; //trung lap ma
        $scope.submit_errors = [];
        
        for(i = count_step - 1; i >= 0; i--){
            v_step_name = $scope.proc['step'][count_step -1- i]['@attributes']['name'] || '';
            v_step_code = $scope.proc['step'][count_step-1 - i]['@attributes']['code'];
            if(v_step_code && step_code_repeat[v_step_code]){
                $scope.proc['step'][count_step-1 - i].error = 'required';
                
                $scope.submit_errors.push('Trùng mã bước "'+v_step_code+'" tại "'+v_name+'", bạn cần sửa mã bước bị trùng hoặc để trống');
            }else{
                step_code_repeat[v_step_code] = 1;
            }
            if(typeof $scope.proc['step'][i]['task'].length == 'undefined'){
                $scope.proc['step'][i]['task'] = [$scope.proc['step'][i]['task']];
            }
            if(typeof $scope.proc['step'][i]['task'].length == 'undefined'){
                $scope.proc['step'][count_step-1-i]['task'] = [$scope.proc['step'][i]['task']];
            }
            var count_task = $scope.proc['step'][i]['task'].length;
            $scope.proc['step'][i]['@attributes']['order'] = i + 1;
            for(j = count_task - 1; j >= 0; j--){
                $scope.proc['step'][i]['task'][j]['@attributes']['order'] = j + 1;
                                
                if(i == count_step - 1 && j == count_task - 1){
                    //task cuoi
                    $scope.proc['step'][i]['task'][j]['@attributes']['next'] = 'NULL';
                }else{
                    //task binh thuong
                    $scope.proc['step'][i]['task'][j]['@attributes']['next'] = next_step;
                }
                next_step = $scope.proc['step'][i]['task'][j]['@attributes']['code'];
            }

            for(j = 0; j < $scope.proc['step'][count_step-1-i]['task'].length; j++){
                v_task_code =  $scope.proc['step'][count_step-1-i]['task'][j]['@attributes']['code'];
                if(task_code_repeat[v_task_code]){
                    $scope.proc['step'][count_step-1-i]['task'][j].error = 'required';
                    $scope.proc['step'][count_step-1-i].error = 'required';
                    $scope.submit_errors.push('Trùng mã công việc "'+v_task_code+'" tại bước "'+v_step_name+'", bạn cần sửa mã công việc');
                }else{
                    task_code_repeat[v_task_code] = 1;
                }
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