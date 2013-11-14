
<div class="row-fluid">
    <ul class="breadcrumb pull-left">
        <li><a href="/admin/series/<?php echo $this->series['id'];?>/seasons"><?php echo $this->escape($this->series['title']);?> - Seasons</a><span class="divider">/</span></li>
        <li>Create New Season</li>
    </ul>
</div>

<div class="form-wrap">
    <form method="post" action="/admin/series/<?php echo $this->series['id'];?>/seasons/new" class="form-horizontal" id="new-user-form">
        <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
        <fieldset>
            <?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>

            <div class="control-group">
                <label class="control-label" for="number">Season Number</label>
                <div class="controls">
                    <select name="number">
                        <?php for($i = 1;$i <= 50;$i++):?>
                        <option value="<?php echo $i;?>"<?php echo (isset($this->form_values['number']) && $this->form_values['number'] == $i) ? ' selected="selected"' : '';?>>Season <?php echo $i;?></option>
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
                        <option value="<?php echo $y;?>"<?php echo (isset($this->form_values['year']) && $this->form_values['year'] == $y) ? ' selected="selected"' : '';?>><?php echo $y;?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            
            <div class="row-fluid">
                <div class="clear-both form-error-messages color-red margin-left-180"></div>
            </div>
            <div class="form-actions">
                <input name="submit" type="submit" class="btn btn-primary" value="Create"/>
            </div>
        </fieldset>
    </form>
</div>
