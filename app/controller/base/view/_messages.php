<?php foreach ($this->session->get('ss_errors', array()) as $title => $msg):?>
    <div class="alert alert-error">
        <button class="close" data-dismiss="alert">×</button>
        <strong><?php echo $this->escape($title);?></strong>&nbsp;&mdash;&nbsp;<?php echo $this->escape($msg);?>
    </div>
<?php endforeach;?>
<?php $this->session->set('ss_errors', array());?>

<?php foreach ($this->session->get('ss_messages', array()) as $title => $msg):?>
    <div class="alert alert-info">
        <button class="close" data-dismiss="alert">×</button>
        <strong><?php echo $this->escape($title);?></strong>&nbsp;&mdash;&nbsp;<?php echo $this->escape($msg);?>
    </div>
<?php endforeach;?>
<?php $this->session->set('ss_messages', array());?>
