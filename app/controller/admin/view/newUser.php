
<div class="row-fluid">
    <ul class="breadcrumb pull-left">
        <li><a href="/admin/users">Users</a><span class="divider">/</span></li>
        <li>Create New User</li>
    </ul>
</div>

<div class="form-wrap">
    <form method="post" action="/admin/users/new" class="form-horizontal" id="new-user-form">
        <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
        <fieldset>
            <?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>
            <div class="control-group">
                <label class="control-label" for="name"><span class="color-red">*</span>&nbsp;Username</label>
                <div class="controls">
                    <input type="text" class="input-xlarge" name="username" id="username" value="<?php echo isset($this->form_values['username']) ? $this->escape($this->form_values['username']) : '';?>"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="email"><span class="color-red">*</span>&nbsp;E-mail</label>
                <div class="controls">
                    <div class="input-prepend">
                        <span class="add-on">@</span><input type="text" class="input-large" name="email" id="email" value="<?php echo isset($this->form_values['email']) ? $this->escape($this->form_values['email']) : '';?>" placeholder="rob@example.com"/>
                    </div>
                    <div class="invalid-email-message color-red"></div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="password"><span class="color-red">*</span>&nbsp;Password</label>
                <div class="controls">
                    <input type="password" class="input-xlarge" name="password" id="password" value="<?php echo isset($this->form_values['password']) ? $this->escape($this->form_values['password']) : '';?>"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="group_id">Group</label>
                <div class="controls">
                    <select name="group_id">
                        <option value="0">None</option>
                        <?php foreach ($this->groups as $group):?>
                        <option value="<?php echo $group['id'];?>"<?php echo (isset($this->form_values['group_id']) && $this->form_values['group_id'] == $group['id']) ? ' selected="selected"' : '';?>><?php echo $this->escape($group['name']);?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <p class="clear-both margin-left-180"><span class="color-red">*</span><span class="muted">&nbsp;Required Fields</span></p>
            <div class="row-fluid">
                <div class="clear-both form-error-messages color-red margin-left-180"></div>
            </div>
            <div class="form-actions">
                <input name="submit" type="submit" class="btn btn-primary" value="Create"/>
            </div>
        </fieldset>
    </form>
</div>

<script>

    function isBlank(element) {
        if (element.val() == null || element.val() == "") {
            $(element).closest('.control-group').addClass('error');
            return 1;
        }
        return 0;
    }

    function isEmailValid(emailAddress) {
        if (emailAddress.val().indexOf("@") == -1) {
            $(emailAddress).closest('.control-group').addClass('error');
            return 1;
        }
        return 0;
    }

    $('#new-user-form').submit(function(e){
        var error_count = 0;
        error_count = error_count + isBlank($('input[name="username"]'));
        error_count = error_count + isBlank($('input[name="password"]'));
        error_count = error_count + isBlank($('input[name="email"]'));
        if (error_count > 0) {
            var errorMessage = "<h2 class=\"pull-left\">OH NO!&nbsp;&nbsp;&nbsp;</h2><p class=\"pull-left\">It appears some necessary fields are still blank!</p>";
            $('div.form-error-messages').html(errorMessage);
            return false;
        }
    });

</script>
