<?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>

<div class="row-fluid">
    <ul class="breadcrumb pull-left">
        <li>Videos&nbsp;<span class="muted">(<?php echo number_format(count($this->videos));?> total)</span></li>
    </ul>
    <a href="/admin/upload" class="btn btn-primary pull-left margin-left-20 margin-top-10">Upload Video</a>
</div><br>

<form method="post" action="/admin/videos" class="form-horizontal">
    <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="short">Select&nbsp;<input type="checkbox" id="select-all"/></th>
                <th>Action</th>
                <th>Episode</th>
                <th>Season</th>
                <th>Series</th>
                <th>Date Added</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->videos as $s):?>
            <tr>
                <td class="short"><input type="checkbox" name="selected[]" value="<?php echo $s['id'];?>"/></td>
                <td><i class="icon-edit color-red margin-top-2" title="Edit Timeline Information"></i>&nbsp;&nbsp;&nbsp;<a title="Edit Timeline Information" href="/admin/timeline/<?php echo $s['id'];?>">Edit Timeline Events</a></td>
                <td><?php echo $this->escape($s['episode_title']);?></td>
                <td>Season <?php echo $this->escape($s['number']);?></td>
                <td><?php echo $this->escape($s['series_title']);?></td>
                <td><?php echo $s['date_added'] ? App_Util::formatDate($s['date_added'], 0, 'America/New_York', 'F, Y') : '-';?></td>
                <td>
                <?php
                if ($s['status'] == App_Model_Videos::$statuses['deleted']) {
                    echo 'Deleted';
                } elseif ($s['status'] == App_Model_Videos::$statuses['active']) {
                    echo 'Ready';
                } elseif ($s['status'] == App_Model_Videos::$statuses['fingerprint-pending']) {
                    echo 'Video is being processed';
                } elseif ($s['status'] == App_Model_Videos::$statuses['s3-pending']) {
                    echo 'Video is being processed';
                } elseif ($s['status'] == App_Model_Videos::$statuses['published']) {
                    echo 'Published';
                }
                ?>
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

