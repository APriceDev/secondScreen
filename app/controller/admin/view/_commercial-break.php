<div class="modal-header">
    <button type="button" class="close-modal close" id="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3><?php echo $this->escape($this->event['module_name']);?></h3>
    </div>
    <div id="modal-content" class="modal-body">
        
        <div class="form-wrap">
            <form method="post" action="/admin/edit-event-options" class="form-horizontal" id="options-form">
                <fieldset>
                    <div class="control-group">
                        <label class="control-label" for="duration"><span class="color-red">*</span>&nbsp;Duration</label>
                        <div class="controls">
                            <input type="text" class="input-large" name="duration" id="duration" value="<?php echo $this->event['duration'];?>"/>
                            <p class="muted">e.g. Duration in seconds, cannot be zero</p>
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <label class="control-label" for="description"><span class="color-red">*</span>&nbsp;Description</label>
                        <div class="controls">
                            <textarea class="input-xlarge" name="description"><?php echo $this->escape(App_Model_EventOptions::getByKey($this->options, 'description'));?></textarea>
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <label class="control-label" for="image"><span class="color-red">*</span>&nbsp;Image URL</label>
                        <div class="controls">
                            <input type="text" class="input-large" name="image" id="image" value="<?php echo $this->escape(App_Model_EventOptions::getByKey($this->options, 'image'));?>"/>
                            <p class="muted">e.g. http://myimages.com/image.jpg</p>
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <label class="control-label" for="link"><span class="color-red">*</span>&nbsp;Link URL</label>
                        <div class="controls">
                            <input type="text" class="input-large" name="link" id="link" value="<?php echo $this->escape(App_Model_EventOptions::getByKey($this->options, 'link'));?>"/>
                            <p class="muted">e.g. http://mostinterestingmanintheworld.com</p>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>

</div>
<div class="modal-footer">
    <a href="#" id="delete_event" data-event-id="<?php echo $this->event['id'];?>" class="btn btn-danger pull-left">Delete event</a>
    <a href="#" id="submit-id" data-event-id="<?php echo $this->event['id'];?>" class="btn btn-primary">Save changes</a>
    <a href="#" class="close-modal btn">Close</a>
</div>

