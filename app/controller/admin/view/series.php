<?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>

<div class="row-fluid">
    <ul class="breadcrumb pull-left">
        <li>Series&nbsp;<span class="muted">(<?php echo number_format(count($this->series));?> total)</span></li>
    </ul>
    <a href="/admin/series/new" class="btn btn-primary pull-left margin-left-20 margin-top-10">Create a new series</a>
    <a href="/admin/series/quick-add" class="btn btn-primary pull-left margin-left-20 margin-top-10">Quick-add from IMDB</a>
</div><br>

<form method="post" action="/admin/series" class="form-horizontal">
    <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="short">Select&nbsp;<input type="checkbox" id="select-all"/></th>
                <th>Title</th>
                <th>Release Date</th>
                <th>End Date</th>
                <th>Rating</th>
                <th>Seasons</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->series as $s):?>
            <tr>
                <td class="short"><input type="checkbox" name="selected[]" value="<?php echo $s['id'];?>"/></td>
                <td><i class="icon-edit color-red margin-top-2" title="Edit Series Information"></i>&nbsp;&nbsp;&nbsp;<a title="Edit Series Information" href="/admin/series/edit/<?php echo $s['id'];?>"><?php echo $this->escape($s['title']);?></a></td>
                <td><?php echo $s['release_date'] ? App_Util::formatDate($s['release_date'], 0, 'America/New_York', 'F, Y') : '-';?></td>
                <td><?php echo $s['end_date'] ? App_Util::formatDate($s['end_date'], 0, 'America/New_York', 'F, Y') : '-';?></td>
                <td><?php echo $this->escape($s['rating']);?></td>
                <td><?php echo $s['season_count'];?> Seasons - <a href="/admin/series/<?php echo $s['id'];?>/seasons">Manage Seasons</a></td>
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

