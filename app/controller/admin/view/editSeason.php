
<div class="row-fluid">
    <ul class="breadcrumb pull-left">
        <li><a href="/admin/series/<?php echo $this->series['id'];?>/seasons"><?php echo $this->escape($this->series['title']);?> - Seasons</a><span class="divider">/</span></li>
        <li>Edit Season</li>
    </ul>
</div>

<div class="form-wrap">
    <form method="post" action="/admin/series/<?php echo $this->series['id'];?>/seasons/edit/<?php echo $this->season['id'];?>" class="form-horizontal" id="edit-user-form">
        <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
        <input type="hidden" name="id" value="<?php echo $this->season['id'];?>"/>
        <fieldset>
            <?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>
            <div class="control-group">
                <label class="control-label" for="number">Season Number</label>
                <div class="controls">
                    <select name="number">
                        <?php for($i = 1;$i <= 50;$i++):?>
                        <option value="<?php echo $i;?>"<?php echo (isset($this->season['number']) && $this->season['number'] == $i) ? ' selected="selected"' : '';?>>Season <?php echo $i;?></option>
                        <?php endfor;?>
                    </select>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label" for="year">Season Year</label>
                <div class="controls">
                    <select name="year">
                        <?php $years = array_reverse(range(1950, ((int) date('Y') + 2)));?>
                        <?php foreach($years as $y):?>
                        <option value="<?php echo $y;?>"<?php echo (isset($this->season['year']) && $this->season['year'] == $y) ? ' selected="selected"' : '';?>><?php echo $y;?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            
            <div class="row-fluid">
                <div class="clear-both form-error-messages color-red margin-left-180"></div>
            </div>
            <div class="form-actions">
                <input name="submit" type="submit" class="btn btn-primary" value="Submit"/>
            </div>
        </fieldset>
    </form>
</div>

