
<div class="row-fluid">
    <ul class="breadcrumb pull-left">
        <li><a href="/users">Users</a><span class="divider">/</span></li>
        <li>Edit User</li>
    </ul>
</div>

<div class="form-wrap">
    <form method="post" action="/admin/users/edit/<?php echo $this->user['id'];?>" class="form-horizontal" id="edit-user-form">
        <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
        <input type="hidden" name="id" value="<?php echo $this->user['id'];?>"/>
        <fieldset>
            <?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>
            <div class="control-group">
                <label class="control-label" for="name">Username</label>
                <div class="controls">
                    <input type="text" class="input-xlarge" name="username" id="username" value="<?php echo isset($this->user['username']) ? $this->escape($this->user['username']) : '';?>"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="email"><span class="color-red">*</span>&nbsp;E-mail</label>
                <div class="controls">
                    <div class="input-prepend">
                        <span class="add-on">@</span><input type="text" class="input-large" name="email" id="email" value="<?php echo isset($this->user['email']) ? $this->escape($this->user['email']) : '';?>"/>
                    </div>
                    <div class="invalid-email-message color-red"></div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="group_id">Group</label>
                <div class="controls">
                    <select name="group_id">
                        <option value="0">None</option>
                        <?php foreach ($this->groups as $group):?>
                        <option value="<?php echo $group['id'];?>"<?php echo (isset($this->user['group_id']) && $this->user['group_id'] == $group['id']) ? ' selected="selected"' : '';?>><?php echo $this->escape($group['name']);?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <p class="clear-both margin-left-180"><span class="color-red">*</span><span class="muted">&nbsp;Required Fields</span></p>
            <div class="row-fluid">
                <div class="clear-both form-error-messages color-red margin-left-180"></div>
            </div>
            <div class="form-actions">
                <input name="submit" type="submit" class="btn btn-primary" value="Submit"/>
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

    $('#edit-user-form').submit(function(e){
        var error_count = 0;
        error_count = error_count + isBlank($('input[name="username"]'));
        error_count = error_count + isBlank($('input[name="email"]'));
        if (error_count > 0) {
            var errorMessage = "<h2 class=\"pull-left\">OH NO!&nbsp;&nbsp;&nbsp;</h2><p class=\"pull-left\">It appears some necessary fields are still blank!</p>";
            $('div.form-error-messages').html(errorMessage);
            return false;
        }
    });

</script>
