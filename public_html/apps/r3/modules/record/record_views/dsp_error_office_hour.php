<?php
defined('DS') or die();

/* @var $this \r3_View */
?>
<script>
    var controller = '<?php echo $this->get_controller_url() ?>';
    is_pop_win = window.location != window.parent.location ? true : false;
    alert('Chức năng chỉ thực hiện trong giờ hành chính!');
    if (is_pop_win) {
        window.parent.hidePopWin();
    } else {
        window.location.href = controller;
    }
</script>