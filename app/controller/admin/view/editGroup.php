
<div class="row-fluid">
    <ul class="breadcrumb pull-left">
        <li><a href="/admin/groups">Groups</a><span class="divider">/</span></li>
        <li>Edit Group</li>
    </ul>
</div>

<div class="form-wrap">
    <form method="post" action="/admin/groups/edit/<?php echo $this->group['id'];?>" class="form-horizontal" id="edit-group-form">
        <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
        <input type="hidden" name="id" value="<?php echo $this->group['id'];?>"/>
        <fieldset>
            <?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>
            <div class="control-group">
                <label class="control-label" for="name">Name</label>
                <div class="controls">
                    <input type="text" class="input-xlarge" name="name" id="name" value="<?php echo $this->escape($this->group['name']);?>"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="description">Description</label>
                <div class="controls">
                    <textarea type="password" class="input-xlarge" name="description"><?php echo $this->escape($this->group['description']);?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="permissions">Permissions</label>
                <div class="controls">
                    <?php foreach ($this->permissions as $p):?>
                    <input type="checkbox" name="permissions[]" value="<?php echo $p['id'];?>"<?php echo (in_array($p['id'], $this->group_permissions)) ? ' checked="checked"' : '';?>/>&nbsp;<?php echo $this->escape($p['name']) . " (" . $this->escape($p['description']) . ")";?><br/>
                    <?php endforeach;?>
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
