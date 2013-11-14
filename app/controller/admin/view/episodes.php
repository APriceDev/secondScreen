<?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>

<div class="row-fluid">
    <ul class="breadcrumb pull-left">
        <li><?php echo $this->escape($this->series['title']);?> - Season <?php echo $this->season['number'];?> - Episodes&nbsp;<span class="muted">(<?php echo number_format(count($this->episodes));?> total)</span></li>
    </ul>
    <a href="/admin/series/<?php echo $this->series['id'];?>/seasons/<?php echo $this->season['id'];?>/episodes/new" class="btn btn-primary pull-left margin-left-20 margin-top-10">Create a new episode</a>
</div><br>

<form method="post" action="/admin/series/<?php echo $this->series['id'];?>/seasons/<?php echo $this->season['id'];?>/episodes" class="form-horizontal">
    <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="short">Select&nbsp;<input type="checkbox" id="select-all"/></th>
                <th>Episode</th>
                <th>Title</th>
                <th>Release Date</th>
                <th>Rating</th>
                <th>Actors</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->episodes as $s):?>
            <tr>
                <td class="short"><input type="checkbox" name="selected[]" value="<?php echo $s['id'];?>"/></td>
                <td><i class="icon-edit color-red margin-top-2" title="Edit Episode Information"></i>&nbsp;&nbsp;&nbsp;<a title="Edit Episode Information" href="/admin/series/<?php echo $this->series['id'];?>/seasons/<?php echo $this->season['id'];?>/episodes/edit/<?php echo $s['id'];?>">Episode <?php echo $this->escape($s['number']);?></a></td>
                <td><?php echo $this->escape($s['title']);?></td>
                <td><?php echo $s['release_date'] ? App_Util::formatDate($s['release_date'], 0, 'America/New_York', 'F jS, Y') : '-';?></td>
                <td><?php echo $this->escape($s['rating']);?></td>
                <td><?php echo $s['actor_count'];?> Actors</td>
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

