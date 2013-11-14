
<div class="form-wrap">
    <form method="post" action="/settings" class="form-horizontal" id="new-group-form">
        <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
        <fieldset>
            <?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>
            <div class="control-group">
                <label class="control-label" for="password"><span class="color-red">*</span>&nbsp;New Password</label>
                <div class="controls">
                    <input type="password" class="input-xlarge" name="password" id="password" value="<?php echo isset($this->form_values['password']) ? $this->escape($this->form_values['password']) : '';?>"/>
                </div>
            </div>
                        <div class="control-group">
                <label class="control-label" for="password"><span class="color-red">*</span>&nbsp;New Password Again</label>
                <div class="controls">
                    <input type="password" class="input-xlarge" name="password2" id="password" value="<?php echo isset($this->form_values['password2']) ? $this->escape($this->form_values['password2']) : '';?>"/>
                </div>
            </div>
            <div class="form-actions">
                <input name="submit" type="submit" class="btn btn-primary" value="Save"/>
            </div>
        </fieldset>
    </form>
</div>
