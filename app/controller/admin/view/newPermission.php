
<div class="row-fluid">
    <ul class="breadcrumb pull-left">
        <li><a href="/admin/permissions">Permissions</a><span class="divider">/</span></li>
        <li>Create New Permission</li>
    </ul>
</div>

<div class="form-wrap">
    <form method="post" action="/admin/permissions/new" class="form-horizontal" id="new-permission-form">
        <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
        <fieldset>
            <?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>
            <div class="control-group">
                <label class="control-label" for="name"><span class="color-red">*</span>&nbsp;Name</label>
                <div class="controls">
                    <input type="text" class="input-xlarge" name="name" id="name" value="<?php echo isset($this->form_values['name']) ? $this->escape($this->form_values['name']) : '';?>"/>
                    <p class="muted">e.g. users_delete</p>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="description"><span class="color-red">*</span>&nbsp;Description</label>
                <div class="controls">
                    <textarea type="password" class="input-xlarge" name="description"><?php echo isset($this->form_values['description']) ? $this->escape($this->form_values['description']) : '';?></textarea>
                    <p class="muted">e.g. Allow deletion of users.</p>
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

    $('#new-permission-form').submit(function(e){
        var error_count = 0;
        error_count = error_count + isBlank($('input[name="name"]'));
        error_count = error_count + isBlank($('textarea[name="description"]'));
        if (error_count > 0) {
            var errorMessage = "<h2 class=\"pull-left\">OH NO!&nbsp;&nbsp;&nbsp;</h2><p class=\"pull-left\">It appears some necessary fields are still blank!</p>";
            $('div.form-error-messages').html(errorMessage);
            return false;
        }
    });

</script>
