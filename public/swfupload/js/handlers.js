/* Demo Note:  This demo uses a FileProgress class that handles the UI for displaying the file name and percent complete.
The FileProgress class is not part of SWFUpload.
*/


/* **********************
   Event Handlers
   These are my custom event handlers to make my
   web application behave the way I went when SWFUpload
   completes different tasks.  These aren't part of the SWFUpload
   package.  They are part of my application.  Without these none
   of the actions SWFUpload makes will show up in my application.
   ********************** */
function fileQueued(file) {
    try {
        var progress = new FileProgress(file, this.customSettings.progressTarget);
        progress.setStatus("Pending...");
        progress.toggleCancel(true, this);

    } catch (ex) {
        this.debug(ex);
    }

}

function fileQueueError(file, errorCode, message) {
    try {
        if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
            alert("You have attempted to queue too many files.\n" + (message === 0 ? "You have reached the upload limit." : "You may select " + (message > 1 ? "up to " + message + " files." : "one file.")));
            return;
        }

        var progress = new FileProgress(file, this.customSettings.progressTarget);
        progress.setError();
        progress.toggleCancel(false);

        switch (errorCode) {
        case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
            progress.setStatus("File is too big.");
            $('#filesUploaded').html('<div style=\"margin-bottom:5px; padding:10px; background-color:#f0f0f0; \">File is too big.</div>');
            this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
            break;
        case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
            progress.setStatus("Cannot upload Zero Byte files.");
            $('#filesUploaded').html('<div style=\"margin-bottom:5px; padding:10px; background-color:#f0f0f0; \">Cannot upload Zero Byte files.</div>');
            this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
            break;
        case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
            progress.setStatus("Invalid File Type.");
            $('#filesUploaded').html('<div style=\"margin-bottom:5px; padding:10px; background-color:#f0f0f0; \">Invalid File Type.</div>');
            this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
            break;
        default:
            if (file !== null) {
                progress.setStatus("Unhandled Error");
            }
            $('#filesUploaded').html('<div style=\"margin-bottom:5px; padding:10px; background-color:#f0f0f0; \">Unhandled Error.</div>');
            this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
            break;
        }
    } catch (ex) {
        this.debug(ex);
    }
}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
    try {
        if (numFilesSelected > 0) {
            document.getElementById(this.customSettings.cancelButtonId).disabled = false;
        }
        
        /* I want auto start the upload and I can do that here */
        this.startUpload();
    } catch (ex)  {
        this.debug(ex);
    }
}

function uploadStart(file) {
    try {
        /* I don't want to do any file validation or anything,  I'll just update the UI and
        return true to indicate that the upload should start.
        It's important to update the UI here because in Linux no uploadProgress events are called. The best
        we can do is say we are uploading.
         */
        var progress = new FileProgress(file, this.customSettings.progressTarget);
        progress.setStatus("Uploading...");
        progress.toggleCancel(true, this);
        updateDisplay.call(this, file);
        $('#filesUploaded').html('<div style=\"margin-bottom:5px; padding:10px; background-color:#f0f0f0; \">Uploading...</div>');
    }
    catch (ex) {}
    
    return true;
}

function uploadProgress(file, bytesLoaded, bytesTotal) {
    try {
        var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);

        var progress = new FileProgress(file, this.customSettings.progressTarget);
        progress.setProgress(percent);
        progress.setStatus("Uploading...");
        updateDisplay.call(this, file);
    } catch (ex) {
        this.debug(ex);
    }
}

function uploadSuccess(file, serverData) {
    try {
        var progress = new FileProgress(file, this.customSettings.progressTarget);
        progress.setComplete();
        progress.setStatus("Complete.");
        progress.toggleCancel(false);
        finishDisplay.call(this, file);

        var obj = jQuery.parseJSON(serverData);
        console.log(obj);
        $('#video_file').val(obj.name + "." + obj.ext);
        $('#video_type').val(obj.type);
        $('#filesUploaded').html('<div style=\"margin-bottom:5px; padding:10px; background-color:#f0f0f0; \">You have selected <strong>' + file.name + '</strong> as your track.</div>');
        $('#coverUploader').hide();
    } catch (ex) {
        this.debug(ex);
    }
}

function uploadError(file, errorCode, message) {
    try {
        var progress = new FileProgress(file, this.customSettings.progressTarget);
        progress.setError();
        progress.toggleCancel(false);

        switch (errorCode) {
        case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
            progress.setStatus("Upload Error: " + message);
            $('#filesUploaded').html('<div style=\"margin-bottom:5px; padding:10px; background-color:#f0f0f0; \">Upload Error: ' + message + '</div>');
            this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
            break;
        case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
            progress.setStatus("Upload Failed.");
            $('#filesUploaded').html('<div style=\"margin-bottom:5px; padding:10px; background-color:#f0f0f0; \">Upload Failed: ' + message + '</div>');
            this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
            break;
        case SWFUpload.UPLOAD_ERROR.IO_ERROR:
            progress.setStatus("Server (IO) Error");
            $('#filesUploaded').html('<div style=\"margin-bottom:5px; padding:10px; background-color:#f0f0f0; \">Server (IO) Error: ' + message + '</div>');
            this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
            break;
        case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
            progress.setStatus("Security Error");
            $('#filesUploaded').html('<div style=\"margin-bottom:5px; padding:10px; background-color:#f0f0f0; \">Security Error: ' + message + '</div>');
            this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
            break;
        case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
            progress.setStatus("Upload limit exceeded.");
            $('#filesUploaded').html('<div style=\"margin-bottom:5px; padding:10px; background-color:#f0f0f0; \">Upload limit exceeded.</div>');
            this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
            break;
        case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
            progress.setStatus("Failed Validation.  Upload skipped.");
            $('#filesUploaded').html('<div style=\"margin-bottom:5px; padding:10px; background-color:#f0f0f0; \">Failed Validation, Upload Skipped: ' + message + '</div>');
            this.debug("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
            break;
        case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
            // If there aren't any files left (they were all cancelled) disable the cancel button
            if (this.getStats().files_queued === 0) {
                document.getElementById(this.customSettings.cancelButtonId).disabled = true;
            }
            progress.setStatus("Cancelled");
            progress.setCancelled();
            $('#filesUploaded').html('<div style=\"margin-bottom:5px; padding:10px; background-color:#f0f0f0; \">Upload Cancelled.</div>');
            break;
        case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
            $('#filesUploaded').html('<div style=\"margin-bottom:5px; padding:10px; background-color:#f0f0f0; \">Upload Stopped.</div>');
            progress.setStatus("Stopped");
            break;
        default:
            progress.setStatus("Unhandled Error: " + errorCode);
            $('#filesUploaded').html('<div style=\"margin-bottom:5px; padding:10px; background-color:#f0f0f0; \">Unhandled Error: ' + errorCode + ' ' + message + '</div>');
            this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
            break;
        }
    } catch (ex) {
        this.debug(ex);
    }
}

function uploadComplete(file) {
    if (this.getStats().files_queued === 0) {
        document.getElementById(this.customSettings.cancelButtonId).disabled = true;
    }
}

// This event comes from the Queue Plugin
function queueComplete(numFilesUploaded) {
    var status = document.getElementById("divStatus");
    status.innerHTML = numFilesUploaded + " file" + (numFilesUploaded === 1 ? "" : "s") + " uploaded.";
}

function updateDisplay(file) {
    this.customSettings.tdCurrentSpeed.innerHTML = 'Current Speed: ' + SWFUpload.speed.formatBPS(file.currentSpeed);
    this.customSettings.tdTimeRemaining.innerHTML = 'Time Remaining: ' + SWFUpload.speed.formatTime(file.timeRemaining);

}

function finishDisplay(file) {
    this.customSettings.tdCurrentSpeed.innerHTML = '';
    this.customSettings.tdTimeRemaining.innerHTML = '';

}
