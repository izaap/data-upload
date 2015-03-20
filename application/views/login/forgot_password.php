<div class="container login_sec m_top_30">
  <div class="row bigtitle text-center">
 Forgot password
 </div>

    <?php if ($message = $this->service_message->render()) :?>
        <?php echo $message;?>
    <?php endif; ?>
    
    <?php if (validation_errors()) :?>
        <div class="alert m_top_20 m_bot_0">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Warning!</strong>  <?php echo validation_errors();?>
        </div>
    <?php endif; ?>
    <div class="row">
        <div class="span4"></div>
        <div class="span4 login_box m_auto m_top_30">
            <form class="form-horizontal" action="<?php site_url('forgot_password')?>" method="POST">
                <div class="control-group">
                    <div class="controls btn-group input-prepend">
                        <span class="add-on"> 
                            <i class="icon_username"></i>
                        </span> 
                        <input class="span3" type="text" name="email" id="inputEmail" placeholder="Email">
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <button type="submit" class="btn btn-primary btn-large">Get a new password</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="span4"></div>
    </div>

</div>

