<?php
/**


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
<?php
if (!defined('SERVER_ROOT'))
    exit('No direct script access allowed');

$data_form_struct = $VIEW_DATA['data_form_struct'];
$v_record_type_code = get_request_var('sel_record_type');
$i = 0;
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link href="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.css" rel="stylesheet" type="text/css"/>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-1.8.2.js"></script>
        <script src="<?php echo SITE_ROOT; ?>public/js/jquery/jquery-ui.js"></script>
<!--        <script src="//code.jquery.com/jquery-1.10.2.js"></script>
        <script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>-->
    </head>
    <style>
        ul { list-style-type: none; margin: 0; padding: 0; margin-bottom: 10px; }
        li { margin: 5px; padding: 5px; font-weight: bold !important; }
        li .label_attr{font-weight: normal !important; margin-right: 10px;}
        #coppy_li_hide{display: none}
        .block-button{text-align: center}
        #dialog_attr .warning{font-family: cursive; color: #f51212}
        #dialog_attr .block label { display: inline-block; width: 140px; text-align: right; }
        .block{margin: 5px;}
        li button{border-width: 0px; cursor:pointer; font-weight: bolder; padding: 5px; background-color: #D0D0D0 ;}
        h3{font-family: initial; font-size: 18px; font-weight: bold; text-align: center;}
        li:active{ background-color: #9d9393; cursor: move;}
        li.item_li:active{background-color: #938080;cursor: move;}
        .input_line_label {width: 40% !important;}
    </style>
    <body>
        <div>
            <h3><?php echo __('Di chuyển con trỏ vào từng dòng hoặc từng đối tượng trong một dòng để kéo thả vị trí.') ?></h3>
        </div>
        <div id="coppy_li_hide">
            <ul>
                <li class="ui-state-default item_li" id="li_attr_0">
                    <div class="div_li_attr">
                        <?php echo __('Type:') ?>
                        <label class="label_attr" type="type" data="" line_id=""></label>
                        <?php echo __('ID:') ?>
                        <label class="label_attr" type="id" data="" line_id=""></label>
                        <?php echo __('Name:') ?>
                        <label class="label_attr" type="name" data="" line_id=""></label>
                        <?php echo __('Allow Null:') ?>
                        <label class="label_attr" type="allownull" data="" line_id="">
                            <?php echo $line[$k]['item_allownull']; ?>
                        </label>
                        <?php echo __('Validate:') ?>
                        <label class="label_attr" type="validate" data="" line_id="">
                            <?php echo $line[$k]['item_validate']; ?>
                        </label>
                        <?php echo __('Label:') ?>
                        <label class="label_attr" type="label" data="" line_id="">
                            <?php echo $line[$k]['item_label']; ?>
                        </label>
                        <?php echo __('Default Value:') ?>
                        <label class="label_attr" type="default_value" data="" line_id="">
                            <?php $line[$k]['item_default_value']; ?>
                        </label>
                        <?php echo __('-Size:') ?>
                        <label class="label_attr" type="size" data="" line_id="">
                            <?php echo $line[$k]['item_size']; ?>
                        </label>
                        <?php echo __('Css:') ?>
                        <label class="label_attr" type="css" data="" line_id="">
                            <?php echo $line[$k]['item_css']; ?>
                        </label>
                        <?php echo __('Event:') ?>
                        <label class="label_attr" type="Event" data="" line_id="">
                            <?php echo $line[$k]['item_event']; ?>
                        </label>
                        <?php echo __('View:') ?>
                        <label class="label_attr" type="view" data="" line_id="">
                            <?php echo $line[$k]['item_view']; ?>
                        </label>
                    </div>
                    <!--<div class="div_li_btn">-->
                    <button type="button" class="btn_edit_attr"><?php echo __('edit') ?></button>
                    <button type="button" class="remove_attr" id_attr=""><?php echo __('delete') ?></button>
                    <!--</div>-->
                </li>
            </ul>
        </div>
        <div>
            <form >
                <?php
                echo $this->hidden('controller', $this->get_controller_url());
                ?>
                <div id="form_struct_object_attr">
                    <ul>
                        <?php if (count($data_form_struct) > 0): ?>
                            <?php foreach ($data_form_struct as $line): ?>
                                <li class="ui-state-default obj_line" id="li_line_<?php echo $i ?>">
                                    <?php $line_id = "line_id_$i"; ?>
                                    <?php echo __('Lable Line:') ?>
                                    <input type="text" class="input_line_label" placeholder="<?php echo __('Lable Line') ?>" value="<?php echo $line['line_label'] ?>" id="<?php echo $line_id; ?>"/>
                                    <?php $num_atrr = count($line) - 1; ?>
                                    <?php if (is_array($line)): ?>
                                        <ul class="ui-sortable" id="<?php echo "obj_attr_$i" ?>">
                                            <?php $num_li = 0; ?>
                                            <?php for ($k = 0; $k < $num_atrr; $k++): ?>
                                                <li class="ui-state-default item_li" id="li_attr_<?php echo $i ?>">
                                                    <div class="div_li_attr">
                                                        <?php echo __('Type:') ?>
                                                        <label class="label_attr" type="type" data="<?php echo $line[$k]['item_type'] ?>" line_id="<?php echo $line_id ?>">
                                                            <?php echo $line[$k]['item_type']; ?>
                                                        </label>
                                                        <?php echo __('ID:') ?>
                                                        <label class="label_attr" type="id" data="<?php echo $line[$k]['item_id'] ?>" line_id="<?php echo $line_id ?>">
                                                            <?php echo $line[$k]['item_id']; ?>
                                                        </label>
                                                        <?php echo __('Name:') ?>
                                                        <label class="label_attr" type="name" data="<?php echo $line[$k]['item_name'] ?>" line_id="<?php echo $line_id ?>">
                                                            <?php echo $line[$k]['item_name']; ?>
                                                        </label>
                                                        <?php echo __('Allow Null:') ?>
                                                        <label class="label_attr" type="allownull" data="<?php echo $line[$k]['item_allownull'] ?>" line_id="<?php echo $line_id ?>">
                                                            <?php echo $line[$k]['item_allownull']; ?>
                                                        </label>
                                                        <?php echo __('Validate:') ?>
                                                        <label class="label_attr" type="validate" data="<?php echo $line[$k]['item_validate'] ?>" line_id="<?php echo $line_id ?>">
                                                            <?php echo $line[$k]['item_validate']; ?>
                                                        </label>
                                                        <?php echo __('Label:') ?>
                                                        <label class="label_attr" type="label" data="<?php echo $line[$k]['item_label'] ?>" line_id="<?php echo $line_id ?>">
                                                            <?php echo $line[$k]['item_label']; ?>
                                                        </label>
                                                        <?php echo __('Default Value:') ?>
                                                        <label class="label_attr" type="default_value" data="<?php echo $line[$k]['item_default_value'] ?>" line_id="<?php echo $line_id ?>">
                                                            <?php $line[$k]['item_default_value']; ?>
                                                        </label>
                                                        <?php echo __('Size:') ?>
                                                        <label class="label_attr" type="size" data="<?php echo $line[$k]['item_size'] ?>" line_id="<?php echo $line_id ?>">
                                                            <?php echo $line[$k]['item_size']; ?>
                                                        </label>
                                                        <?php echo __('Css:') ?>
                                                        <label class="label_attr" type="css" data="<?php echo $line[$k]['item_css'] ?>" line_id="<?php echo $line_id ?>">
                                                            <?php echo $line[$k]['item_css']; ?>
                                                        </label>
                                                        <?php echo __('Event:') ?>
                                                        <label class="label_attr" type="Event" data="<?php echo $line[$k]['item_event'] ?>" line_id="<?php echo $line_id ?>">
                                                            <?php echo $line[$k]['item_event']; ?>
                                                        </label>
                                                        <?php echo __('View:') ?>
                                                        <label class="label_attr" type="view" data="<?php echo $line[$k]['item_view'] ?>" line_id="<?php echo $line_id ?>">
                                                            <?php echo $line[$k]['item_view']; ?>
                                                        </label>
                                                    </div>
                                                    <!--<div class="div_li_btn">-->
                                                    <button type="button" class="btn_edit_attr"><?php echo __('edit') ?></button>
                                                    <button type="button" class="remove_attr" id_attr="<?php echo $i ?>"><?php echo __('delete') ?></button>
                                                    <!--</div>-->
                                                </li>
                                                <?php $num_li++ ?>
                                            <?php endfor; ?>
                                        </ul>
                                    <?php endif; ?>
                                    <?php if ($num_li < 2): ?>
                                        <button type="button" class="btn_add_object" id="add_obj_<?php echo $i ?>" id_ul="<?php echo $i ?>">Thêm Item</button>
                                    <?php endif; ?>
                                    <button type="button" class="btn_remove_object" id_ul="<?php echo $i ?>">Xoá dòng này</button>
                                </li>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <button type="button" name="btn_add_line" id="btn_add_line" value="<?php echo __('add new line') ?>" >Thêm dòng mới</button>
                    <button type="button"  name="btn_save_attr" id="btn_save_attr" value="<?php echo __('save') ?>" ><?php echo __('save') ?></button>
                </div>
            </form>
            <div id="dialog_attr">
                <form>
                    <label class="warning">Các trường đánh dấu (*) là bắt buộc phải nhập.</label>
                    <div class="block">
                        <label>type:</label>
                        <select name="so_attr_type" id="so_attr_type">
                            <option value="TextboxName">TextboxName</option>
                            <option value="TextboxMoney">TextboxMoney</option>
                            <option value="Textbox">Textbox</option>
                            <option value="TextboxDate">TextboxDate</option>
                            <option value="DropDownList">DropDownList</option>
                            <option value="RadioButton">RadioButton</option>
                            <option value="Textarea">Textarea</option>
                            <option value="Button">Button</option>
                            <option value="MultiCheckbox">MultiCheckbox</option>
                            <option value="Checkbox">Checkbox</option>
                            <option value="TextboxArea">TextboxArea</option>
                            <option value="TextboxDocSEQ">TextboxDocSEQ</option>
                        </select>
                    </div>
                    <div class="block">
                        <label>
                            id:
                        </label>
                        <input type="text" name="txt_attr_id" id="txt_attr_id" placeholder="<?php echo __('ID') ?>" />
                    </div>
                    <div class="block">
                        <label>
                            name:
                        </label>
                        <input type="text" name="txt_attr_name" id="txt_attr_name" placeholder="<?php echo __('Name') ?>"/>
                    </div>
                    <div class="block">
                        <label>
                            allownull:
                        </label>                        
                        <input type="radio" checked name="rd_attr_allownull" class="rd_attr_allownull" value="yes" id="rad_rd_attr_allownull_yes"/>Yes
                        <input type="radio" name="rd_attr_allownull" class="rd_attr_allownull" value="no" id="rad_rd_attr_allownull_no"/>No
                    </div>
                    <div class="block">
                        <label>validate:</label>
                        <select name="so_attr_validate" id="so_attr_validate" >
                            <option value="text">text</option>
                            <option value="email">email</option>
                            <option value="number">number</option>
                            <option value="phone">phone</option>
                            <option value="money">money</option>
                            <option value="date">date</option>
                            <option value="ddli">ddli</option>
                            <option value="fax">fax</option>
                            <option value="numberString">numberString</option>
                        </select>
                    </div>
                    <div class="block">
                        <label>
                            label:
                        </label>
                        <input type="text" name="txt_attr_label_obj" id="txt_attr_label_obj" placeholder="<?php echo __('Label Object') ?>"/>
                    </div>
                    <div class="block">
                        <label>
                            default_value:
                        </label>
                        <input type="text" placeholder="<?php echo __('Default Value') ?>" name="txt_attr_default_value" id="txt_attr_default_value"/>
                    </div>
                    <div class="block">
                        <label>
                            size:
                        </label>
                        <input type="text" placeholder="<?php echo __('Size') ?>" name="txt_attr_size" id="txt_attr_size"/>
                    </div>
                    <div class="block">
                        <label>
                            css:
                        </label>
                        <input type="text" name="txt_attr_css" placeholder="<?php echo __('Css') ?>" id="txt_attr_css"/>
                    </div>
                    <div class="block">
                        <label>
                            Event:
                        </label>
                        <input type="text" name="txt_attr_Event" id="txt_attr_Event" placeholder="<?php echo __('Event') ?>"/>
                    </div>
                    <div class="block">
                        <label>
                            view:
                        </label>
                        <input type="radio" name="rd_attr_view" class="rd_attr_view" value="true" checked/>True
                        <input type="radio" name="rd_attr_view" class="rd_attr_view" value="false"/>False
                    </div>
                    <div class="block-button">
                        <button type="button" id="dialog_attr_btn_ok"><?php echo __('OK') ?></button>
                        <button type="button" id="dialog_attr_cancel"><?php echo __('Cancel') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
<script>
    var item_edit = null;
    var add_item_line = null;
    var i = 0;
    if (i <= 0)
    {
        i = <?php echo $i ?>;
    }

    jQuery(document).ready(function($) {
        //add new line
        $('#btn_add_line').on('click', function() {
            $('#form_struct_object_attr ul:first').append("<li class='ui-state-default ui-sortable-handle obj_line'><?php echo __('Lable Line:') ?><input type='text' class='input_line_label' id='line_id_" + i + "'/><ul class='ui-sortable' id='obj_attr_" + i + "'></ul><button type='button' class='btn_add_object' id_ul='" + i + "'><?php echo __('Add Object') ?></button><button type='button' class='btn_remove_object' id_ul='" + i + "'><?php echo __('Remove Object') ?></button></li>");
            i++;
        });
        //Dag and drop element
        dag_drop();
        function dag_drop() {
            $("#form_struct_object_attr ul").sortable({
                revert: true,
                items: "> li"
            });
        }
        //save xml
        $(document).on('click', '#btn_save_attr', function() {
            var arr_line = get_line_label();
            var arr_item = get_attr_item();
            var url = $('#controller').val() + 'update_form_struct/?sel_record_type=' + '<?php echo trim($v_record_type_code) ?>';
            $.ajax({
                type: "POST",
                url: url,
                data: {arr_line: arr_line, arr_item: arr_item},
                success: function() {
                    alert('Cập nhật thành công');
                    try {
                        window.parent.document.frmMain.submit();
                    } catch (e){
                        location.reload();
                    }
                },
                error: function() {
                    alert('Cập nhật thất bại');
                }
            });
        });

        function get_line_label()
        {
            var arr_label = [];
            $('.input_line_label').each(function() {
                var label_data = $(this).val();
                var line_id = $(this).attr('id');
                arr_label.push(line_id + '-' + label_data);
            });
            return arr_label;
        }
        function get_attr_item() {
            var arr_item = new Array();
            $('.label_attr').each(function() {
                var item_type = $(this).attr('type');
                var item_data = $(this).attr('data');
                var line_id = $(this).attr('line_id');
                arr_item.push(line_id + '-' + item_type + '/' + item_data);
            });
            return arr_item;
        }
        $(document).on('click', '.remove_attr', function() {
            var del = confirm('Bạn chắc chắn xoá Item này?');
            if (del)
            {
                $(this).parent().remove();
                var id_attr = $(this).attr('id_attr');
                var id_add_obj = '#add_obj_' + id_attr;
                $(id_add_obj).show();
            }
        });
        $(document).on('click', '.btn_remove_object', function() {
            var del = confirm('Bạn chắc chắn xoá Item này?');
            if (del)
            {
                $(this).parent().remove();
            }
        });
        $(document).on('click', '.btn_attr_cancel', function() {
            $(this).parent().parent().remove();
            $('.btn_add_object').show();
        });
        $(document).on('click', '.btn_edit_attr', function() {
            var li_parent = $(this).siblings('div').children('label');
            item_edit = li_parent;
            $(li_parent).each(function() {
                var type_item = $(this).attr('type');
                switch (type_item)
                {
                    case 'type':
                        var selected = '#so_attr_type option[value="' + $(this).attr('data') + '"]';
                        $("#so_attr_type option:selected").prop("selected", false);
                        $(selected).prop("selected", true);
                        break;
                    case 'id':
                        $('#txt_attr_id').attr('value', $(this).attr('data'));
                        break;
                    case 'name':
                        $('#txt_attr_name').attr('value', $(this).attr('data'));
                        break;
                    case 'allownull':
                        var checked = '[value="' + $(this).attr('data') + '"]';
                        $('.rd_attr_allownull').filter(checked).attr('checked', true);
                        break;
                    case 'validate':
                        var selected = '#so_attr_validate option[value="' + $(this).attr('data') + '"]';
                        $("#so_attr_validate option:selected").prop("selected", false);
                        $(selected).prop("selected", true);
                        break;
                    case 'label':
                        $('#txt_attr_label_obj').attr('value', $(this).attr('data'));
                        break;
                    case 'default_value':
                        $('#txt_attr_default_value').attr('value', $(this).attr('data'));
                        break;
                    case 'size':
                        $('#txt_attr_size').attr('value', $(this).attr('data'));
                        break;
                    case 'Event':
                        $('#txt_attr_Event').attr('value', $(this).attr('data'));
                        break;
                    case 'css':
                        $('#txt_attr_css').attr('value', $(this).attr('data'));
                        break;
                    case 'view':
                        var checked = '[value="' + $(this).attr('data') + '"]';
                        $('.rd_attr_view').filter(checked).attr('checked', true);
                        break;
                }
            });
            $("#dialog_attr").dialog("open");
        });
        $('#dialog_attr_cancel').click(function() {
            $("#dialog_attr").dialog("close");
        });
        $("#dialog_attr").dialog({
            autoOpen: false,
            title: "<?php echo __('Object') ?>",
            width: 450,
            resizable: false,
            show: {
                duration: 0
            },
            hide: {
                duration: 0
            }
        });
        $('#dialog_attr_btn_ok').click(function() {
            if (check_input_require('#txt_attr_id') && check_input_require('#txt_attr_name'))
            {
                var item_type = $('#so_attr_type option:selected').attr('value');
                var item_id = $('#txt_attr_id').val();
                var item_name = $('#txt_attr_name').val();
                var item_allownull = $('.rd_attr_allownull:checked').val();
                var item_validate = $('#so_attr_validate option:selected').attr('value');
                var item_label_obj = $('#txt_attr_label_obj').val();
                var item_default_value = $('#txt_attr_default_value').val();
                var item_size = $('#txt_attr_size').val();
                var item_css = $('#txt_attr_css').val();
                var item_event = $('#txt_attr_Event').val();
                var item_view = $('.rd_attr_view:checked').val();
                if (add_item_line !== null)
                {
                    var li_element_copy = $('#coppy_li_hide').children('ul').first().children('li').first().attr('id', 'li_attr_' + add_item_line).clone(true);
                    var ul_item_id = '#obj_attr_' + add_item_line;
                    $(ul_item_id).append(li_element_copy);
                    $(ul_item_id).children('li').last().children('div').children('label').attr('line_id', 'line_id_' + add_item_line);
                    var li_element_last = $(ul_item_id).children('li').last().children('div').children('label');
                    add_item_line = null;
                    item_edit = li_element_last;
                    $("#dialog_attr").dialog("close");
                }
                $(item_edit).each(function() {
                    var _item_type = $(this).attr('type');
                    switch (_item_type)
                    {
                        case 'type':
                            $(this).attr('data', item_type);
                            $(this).text(item_type);
                            break;
                        case 'id':
                            $(this).attr('data', item_id);
                            $(this).text(item_id);
                            break;
                        case 'name':
                            $(this).attr('data', item_name);
                            $(this).text(item_name);
                            break;
                        case 'allownull':
                            $(this).attr('data', item_allownull);
                            $(this).text(item_allownull);
                            break;
                        case 'validate':
                            $(this).attr('data', item_validate);
                            $(this).text(item_validate);
                            break;
                        case 'label':
                            $(this).attr('data', item_label_obj);
                            $(this).text(item_label_obj);
                            break;
                        case 'default_value':
                            $(this).attr('data', item_default_value);
                            $(this).text(item_default_value);
                            break;
                        case 'size':
                            $(this).attr('data', item_size);
                            $(this).text(item_size);
                            break;
                        case 'Event':
                            $(this).attr('data', item_event);
                            $(this).text(item_event);
                            break;
                        case 'css':
                            $(this).attr('data', item_css);
                            $(this).text(item_css);
                            break;
                        case 'view':
                            $(this).attr('data', item_view);
                            $(this).text(item_view);
                            break;
                    }
                });
                $("#dialog_attr").dialog("close");
                dag_drop();
            }
            else
            {
                alert('<?php echo __('Require field (*)!') ?>');
            }
        });
        function check_input_require(id)
        {
            var length = $.trim($(id).val()).length;
            if (length > 0) {
                return  true;
            }
            return false;
        }
        $(document).on('click', '.btn_add_object', function() {
            var id_obj = $(this).attr('id_ul');
            var id_ul = '#obj_attr_' + id_obj;
            var num_li = $(id_ul).children().length;
            if (num_li >= 2)
            {
                $(this).hide();
                alert('<?php echo __('Line Max 2 Object!') ?>');
                return;
            }
            add_item_line = id_obj;
            reset_form();
            $("#dialog_attr").dialog("open");
        });
        function reset_form()
        {
            $('.block input[type=text]').val('');
        }
        $('#txt_attr_id, #txt_attr_name').keypress(function() {
            var id_val = $.trim($(this).val());
            $(this).val(id_val);
        });
    })(jQuery);

</script>