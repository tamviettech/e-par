function main_Controller($scope){
    $scope.row_onclick = function(id){
        controller = $('#controller').val();
        dsp_single = $('#hdn_dsp_single_method').val();
        v_url = controller + dsp_single + '/' + id;
        window.location.href = v_url;
    }
}

