<?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>

<div class="row-fluid">
    <ul class="breadcrumb pull-left">
        <li>Permissions&nbsp;<span class="muted">(<?php echo number_format(count($this->permissions));?> total)</span></li>
    </ul>
    <a href="/admin/permissions/new" class="btn btn-primary pull-left margin-left-20 margin-top-10">Create a new permission</a>
</div><br>

<form method="post" action="/admin/permissions" class="form-horizontal">
    <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="short">Select&nbsp;<input type="checkbox" id="select-all"/></th>
                <th>Permission</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->permissions as $permission):?>
            <tr>
                <td class="short"><input type="checkbox" name="selected[]" value="<?php echo $permission['id'];?>"/></td>
                <td><i class="icon-edit color-red margin-top-2"></i>&nbsp;&nbsp;&nbsp;<a title="Edit Permission Details" href="/admin/permissions/edit/<?php echo $permission['id'];?>"><?php echo $this->escape($permission['name']);?></a></td>
                <td><?php echo $this->escape($permission['description']);?></td>
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

