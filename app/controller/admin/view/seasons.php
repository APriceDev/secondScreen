<?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>

<div class="row-fluid">
    <ul class="breadcrumb pull-left">
        <li><?php echo $this->escape($this->series['title']);?> - Seasons&nbsp;<span class="muted">(<?php echo number_format(count($this->seasons));?> total)</span></li>
    </ul>
    <a href="/admin/series/<?php echo $this->series['id'];?>/seasons/new" class="btn btn-primary pull-left margin-left-20 margin-top-10">Create a new season</a>
</div><br>

<form method="post" action="/admin/series/<?php echo $this->series['id'];?>/seasons" class="form-horizontal">
    <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="short">Select&nbsp;<input type="checkbox" id="select-all"/></th>
                <th>Season</th>
                <th>Year</th>
                <th>Episodes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->seasons as $s):?>
            <tr>
                <td class="short"><input type="checkbox" name="selected[]" value="<?php echo $s['id'];?>"/></td>
                <td><i class="icon-edit color-red margin-top-2" title="Edit Season Information"></i>&nbsp;&nbsp;&nbsp;<a title="Edit Season Information" href="/admin/series/<?php echo $this->series['id'];?>/seasons/edit/<?php echo $s['id'];?>">Season <?php echo $this->escape($s['number']);?></a></td>
                <td><?php echo $this->escape($s['year']);?></td>
                <td><?php echo $s['episode_count'];?> Episodes - <a href="/admin/series/<?php echo $this->series['id'];?>/seasons/<?php echo $s['id'];?>/episodes">Manage Episodes</a></td>
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

