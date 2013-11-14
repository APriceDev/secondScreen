<?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>

<div class="row-fluid">
    <ul class="breadcrumb pull-left">
        <li>Users&nbsp;<span class="muted">(<?php echo number_format(count($this->users));?> total)</span></li>
    </ul>
    <a href="/admin/users/new" class="btn btn-primary pull-left margin-left-20 margin-top-10">Create a new user</a>
</div><br>

<form method="post" action="/admin/users" class="form-horizontal">
    <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="short">Select&nbsp;<input type="checkbox" id="select-all"/></th>
                <th>Username</th>
                <th>E-mail</th>
                <th>Group</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->users as $user):?>
            <tr>
                <td class="short"><input type="checkbox" name="selected[]" value="<?php echo $user['id'];?>"/></td>
                <td><i class="icon-edit color-red margin-top-2" title="Edit User Information"></i>&nbsp;&nbsp;&nbsp;<a title="Edit User Information" href="/admin/users/edit/<?php echo $user['id'];?>"><?php echo $this->escape($user['username']);?></a></td>
                <td><?php echo $this->escape($user['email']);?></td>
                <td><?php echo ($user['group_name']) ? $this->escape($user['group_name']) : 'None';?></td>
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

