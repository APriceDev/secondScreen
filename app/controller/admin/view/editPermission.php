
<div class="row-fluid">
    <ul class="breadcrumb pull-left">
        <li><a href="/admin/permissions">Permissions</a><span class="divider">/</span></li>
        <li>Edit Permission</li>
    </ul>
</div>

<div class="form-wrap">
    <form method="post" action="/admin/permissions/edit/<?php echo $this->permission['id'];?>" class="form-horizontal" id="edit-permission-form">
        <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
        <input type="hidden" name="id" value="<?php echo $this->permission['id'];?>"/>
        <fieldset>
            <?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>
            <div class="control-group">
                <label class="control-label" for="name">Name</label>
                <div class="controls">
                    <input type="text" class="input-xlarge" name="name" id="name" value="<?php echo $this->escape($this->permission['name']);?>"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="description">Description</label>
                <div class="controls">
                    <textarea type="password" class="input-xlarge" name="description"><?php echo $this->escape($this->permission['description']);?></textarea>
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

    $('#edit-permission-form').submit(function(e){
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
