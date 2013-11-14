<?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>

<div class="row-fluid">
    <ul class="breadcrumb pull-left">
        <li>Groups&nbsp;<span class="muted">(<?php echo number_format(count($this->groups));?> total)</span></li>
    </ul>
    <a href="/admin/groups/new" class="btn btn-primary pull-left margin-left-20 margin-top-10">Create a new group</a>
</div><br>

<form method="post" action="/admin/groups" class="form-horizontal">
    <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="short">Select&nbsp;<input type="checkbox" id="select-all"/></th>
                <th>Group</th>
                <th>Description</th>
                <th>Permissions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->groups as $group):?>
            <tr>
                <td class="short"><input type="checkbox" name="selected[]" value="<?php echo $group['id'];?>"/></td>
                <td><i class="icon-edit color-red margin-top-2"></i>&nbsp;&nbsp;&nbsp;<a title="Edit Group Information" href="/admin/groups/edit/<?php echo $group['id'];?>"><?php echo $this->escape($group['name']);?></a></td>
                <td><?php echo $this->escape($group['description']);?></td>
                <td width="60%">
                    <?php $permissions = array(); foreach ($group['permissions'] as $p):?>
                    <?php $permissions[] = $this->escape($p['name']);?>
                    <?php endforeach;?>
                    <?php echo implode(', ', $permissions);?>
                </td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
    <fieldset>
      <div class="form-actions">
        <select name="action">
            <option value="none">-- Bulk Actions --</option>
            <option value="delete">Delete</option>
        </select>
        <input name="process" type="submit" class="btn btn-primary" value="Process"/>
      </div>
    </fieldset>
</form>

