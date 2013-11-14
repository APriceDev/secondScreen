<div class="row-fluid">
    <ul class="breadcrumb pull-left">
        <li>Upload Video</li>
    </ul>
</div>

<div class="form-wrap">
    <form method="post" id="upload_form" action="/admin/upload" enctype="multipart/form-data" class="form-horizontal">
        <input type="hidden" name="csrf_token" value="<?php echo $this->csrf_token;?>"/>
        <fieldset>
            <?php include SolarLite::$system . '/app/controller/base/view/_messages.php';?>
            <div class="control-group">
                <label class="control-label" for="series_id">Series</label>
                <div class="controls">
                    <select id="series" name="series_id" tabindex="7">
                        <?php foreach ($this->series as $c):?>
                        <option value="<?php echo $c['id'];?>"<?php echo (isset($this->form_values['series_id']) && $this->form_values['series_id'] == $c['id']) ? ' selected="selected"' : '';?>><?php echo $this->escape($c['title']);?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="season_id">Season</label>
                <div class="controls">
                    <select id="seasons" name="season_id" tabindex="6">

                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="episode_id">Episode</label>
                <div class="controls">
                    <select id="episodes" name="episode_id" tabindex="8">

                    </select>
                </div>
            </div>
                
            <div class="control-group">
                <label class="control-label" for="name"><span class="color-red">*</span>&nbsp;File</label>
                    
                <div class="controls">
                    <div id="filesUploaded"></div>
                    <input type="hidden" name="video_file" id="video_file" value=""/>
                    <input type="hidden" name="video_type" id="video_type" value=""/>
                    <div>
                        <div class="fieldset flash" id="fsUploadProgress" style="width:220px;"></div>
                        <div id="CurrentSpeed"></div>
                        <div id="TimeRemaining"></div>
                        <div id="divStatus" style="display:none">0 Files Uploaded</div>
                        <div id="coverUploader" style="margin-top:2px;">
                            <span id="spanButtonPlaceHolder"></span>
                            <input id="btnCancel" type="button" value="Cancel All Uploads" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 29px;position:absolute" />
                        </div>
                    </div>
                    <span class="help-block">Allowed File Types: wmv,avi,mp4,m4v only</span>
                </div>
            </div>
          
            <div class="form-actions">
                <input name="submit" type="submit" class="btn btn-primary" value="Submit"/>
            </div>
        </fieldset>
    </form>
</div>

