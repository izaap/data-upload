<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
  <head>
        <? include_title(); ?>
        <? include_javascripts(); ?>
        <? include_stylesheets(); ?>
        <? include_metas(); ?>
        <? include_links(); ?>
        <? include_raws() ?>
        <script>
        $(function() {
            set_base_url("<?php echo base_url();?>");
        });
        </script>
  </head>
  <body>
    <div id="admin-wrapper" class="container_16">
          <div id="logout" class="grid_15 suffix_1">
          </div>
          <div class="clear"></div>
          <div id="admin-header" class="grid_16">
          </div>
            <div class="clear"></div>
      
      
      
            <div id="main-navigation"  class="grid_14 prefix_1 suffix_1">
               
            </div>
      
            <div class="clear"></div>
           
            <div id="admin-content" class="grid_16">
                


<span style="padding-left: 5px">

</span>
<?php  if($stat != '') {  ?>
<div class="grid_5 h70 alpha omega"></div>
<div id="loginContainer" class="grid_6 alpha omega">
<?php echo $stat;?>
</div>
<div class="grid_5 h70 alpha omega"></div>
<div class="clear"></div>
<?php } ?>
<? $this->load->view('_partials/footer'); ?>
</body>
</html>
