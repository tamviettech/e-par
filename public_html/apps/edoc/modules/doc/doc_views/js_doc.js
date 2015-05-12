function delete_doc_file(id)
{
    var f = document.frmMain;
    s = document.getElementById('file_' + id);
    s.style.display = "none";

    f.hdn_deleted_doc_file_id_list.value = $("#hdn_deleted_doc_file_id_list").val() + ','  + id;
}

function row_onclick_pop_win(url)
{
    showPopWin(url ,970,600, null);
}

function submit_doc_pop_win(url)
{
    showPopWin(url ,970, 250, null);
}

function allot_doc_pop_win(url)
{
    showPopWin(url ,970, 600, null);
}

function approve_doc_pop_win(url)
{
    showPopWin(url ,970, 250, null);
}

function exec_doc_pop_win(url)
{
    showPopWin(url ,970, 600, null);
}

function btn_dsp_add_exec_onclick()
{
    document.getElementById("div_add_exec").style.display = "block";
    document.frmMain.txt_exec.focus();
}
function btn_cancel_exec_onclick()
{
    document.getElementById("div_add_exec").style.display = "none";
}

function sub_allot_doc_pop_win(url)
{
    showPopWin(url ,970, 600, null);
}

function remove_doc_folder(p_doc_id, p_folder_id)
{
    var f = document.frmMain;
    if (confirm('Bạn chắc chắn bỏ văn bản khỏi hồ sơ?')){
        f.hdn_item_id.value =  p_doc_id;
        f.hdn_folder_id.value =  p_folder_id;
        m = $("#controller").val() + 'remove_doc_folder';
        $("#frmMain").attr("action", m);
        f.submit();
    }
}

