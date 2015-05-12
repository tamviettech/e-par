
<form id="frm_filter" action="<?php echo SITE_ROOT;?>public_service/article" method="post">
     <input type="hidden" name="filter_status" 
           value="<?php echo isset($_POST['filter_status']) ? $_POST['filter_status'] : 0; ?>"/>
    <input type="hidden" name="filter_category" 
           value="<?php echo isset($_POST['filter_category']) ? $_POST['filter_category'] : -1; ?>"/>
    <input type="hidden" name="filter_article_title" 
           value="<?php echo isset($_POST['filter_article_title']) ? $_POST['filter_article_title'] : ''; ?>"/> 
</form>

<script type="text/javascript">
    //$('#frm_filter').submit();
</script>

