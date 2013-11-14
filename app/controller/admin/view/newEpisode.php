
<div class="row-fluid">
    <ul class="breadcrumb pull-left">
        <li><a href="/admin/series/<?php echo $this->series['id'];?>/seasons/<?php echo $this->season['id'];?>/episodes"><?php echo $this->escape($this->series['title']);?> - Season <?php echo $this->season['number'];?> - Episodes</a><span class="divider">/</span></li>
        <li>Create New Episode</li>
    </ul>
</div>

<div class="form-wrap">
    <form method="post" action="/admin/series/<?php echo $this->series['id'];?>/seasons/<?php echo $this->season['id'];?>/episodes/new" class="form-horizontal" id="new-user-form">
        <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
        <fieldset>
            <?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>
            <div class="control-group">
                <label class="control-label" for="name"><span class="color-red">*</span>&nbsp;Title</label>
                <div class="controls">
                    <input type="text" class="input-xlarge" name="title" id="title" value="<?php echo isset($this->form_values['title']) ? $this->escape($this->form_values['title']) : '';?>"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="description"><span class="color-red">*</span>&nbsp;Description</label>
                <div class="controls">
                    <textarea class="input-xlarge" name="description"><?php echo isset($this->form_values['description']) ? $this->escape($this->form_values['description']) : '';?></textarea>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label" for="number">Episode Number</label>
                <div class="controls">
                    <select name="number">
                        <?php for($i = 1;$i <= 50;$i++):?>
                        <option value="<?php echo $i;?>"<?php echo (isset($this->form_values['number']) && $this->form_values['number'] == $i) ? ' selected="selected"' : '';?>>Episode <?php echo $i;?></option>
                        <?php endfor;?>
                    </select>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label" for="release_date">Release Date</label>
                <div class="controls">
                    <input type="date" class="input-small" name="release_date" id="release_date" value="<?php echo isset($this->form_values['release_date']) ? $this->escape($this->form_values['release_date']) : '';?>"/>
                    <p class="muted">e.g. 07/20/1985</p>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label" for="rating">Rating</label>
                <div class="controls">
                    <select name="rating">
                        <?php foreach (App_Model_Series::$ratings as $r):?>
                        <option value="<?php echo $r;?>"<?php echo (isset($this->form_values['rating']) && $this->form_values['rating'] == $r) ? ' selected="selected"' : '';?>><?php echo $this->escape($r);?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label" for="tags">Tags</label>
                <div class="controls">
                    <textarea class="input-xlarge" name="tags"><?php echo isset($this->form_values['tags']) ? $this->escape($this->form_values['tags']) : '';?></textarea>
                    <p class="muted">comma separated list of tags e.g. cannibalism, scary, wolves</p>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label" for="genres">Genres</label>
                <div class="controls">
                    <textarea class="input-xlarge" name="genres"><?php echo isset($this->form_values['genres']) ? $this->escape($this->form_values['genres']) : '';?></textarea>
                    <p class="muted">comma separated list of genres e.g. Horror, Crime, Drama</p>
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
