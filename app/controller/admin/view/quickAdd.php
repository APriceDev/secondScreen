
<div class="row-fluid">
    <ul class="breadcrumb pull-left">
        <li><a href="/admin/series">Series</a><span class="divider">/</span></li>
        <li>Quick Add New Series</li>
    </ul>
</div>

<div class="form-wrap">
    <form method="post" action="/admin/quick-add" class="form-horizontal" id="new-user-form">
        <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
        <fieldset>
            <?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>
            <div class="control-group">
                <label class="control-label" for="name"><span class="color-red">*</span>&nbsp;Title</label>
                <div class="controls">
                    <input type="text" class="input-xlarge" name="title" id="title" value="<?php echo isset($this->form_values['title']) ? $this->escape($this->form_values['title']) : '';?>"/>
                </div>
            </div>
            
            <p class="clear-both margin-left-180"><span class="color-red">*</span><span class="muted">&nbsp;Required Fields</span></p>
            <div class="row-fluid">
                <div class="clear-both form-error-messages margin-left-180">
                <?php
                ignore_user_abort(true);
                set_time_limit(0);
                
                if (isset($_POST['submit'])) {
                    $cmd = '/usr/bin/php ' . SolarLite::$system . '/bin/imdb-import.php ' . escapeshellarg($_POST['title']) . ' ' . SS_ENV;
                    exec($cmd, $data);
                    echo implode('<br/>', $data);
                }
                ?>
                </div>
            </div>
            <div class="form-actions">
                <input name="submit" type="submit" class="btn btn-primary" value="Create"/> <p>Creation may take several minutes, please be patient.</p>
            </div>
        </fieldset>
    </form>
</div>
