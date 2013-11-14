
var csrf_token = '';
var ajax_lock = false;
var current_video_id = 0;
var current_resolution = 1;
var left_offset = 0;
var right_offset = 0;
var last_t = 0;
var visible_width = 0;
var ss =
{
    presence :
    {
        init : function(csrf) {
            csrf_token = csrf;
        }
    },
    main :
    {
        global : function () {
            $(document).ready(function() {
                $(".popover-trigger").popover({
                    trigger:'hover'
                });
            });

            $('#select-all').click(function(e) {
                if ($(this).attr('checked') == 'checked') {
                    $(':input[type=checkbox]').attr('checked', 'checked');
                } else {
                    $(':input[type=checkbox]').attr('checked', null);
                }
            });
        }
    },
    index :
    {
        login: {
            init : function () {
                $(document).ready(function() {
                    $('#username-or-email').keyup(function() {
                        setGravatarImage($(this).val());
                    });
                    setGravatarImage($('#username-or-email').val());
                });
                
                function setGravatarImage(email) {
                    md5 = function(h){function i(c,d){return((c>>1)+(d>>1)<<1)+(c&1)+(d&1)}for(var m=[],l=0;64>l;)m[l]=0|4294967296*Math.abs(Math.sin(++l));return function(c){for(var d,g,f,a,j=[],c=unescape(encodeURI(c)),b=c.length,k=[d=1732584193,g=-271733879,~d,~g],e=0;e<=b;)j[e>>2]|=(c.charCodeAt(e)||128)<<8*(e++%4);j[c=(b+8>>6)*h+14]=8*b;for(e=0;e<c;e+=h){b=k;for(a=0;64>a;)b=[f=b[3],i(d=b[1],(f=i(i(b[0],[d&(g=b[2])|~d&f,f&d|~f&g,d^g^f,g^(d|~f)][b=a>>4]),i(m[a],j[[a,5*a+1,3*a+5,7*a][b]%h+e])))<<(b=[7,12,17,22,5,9,14,20,4,11,h,23,6,10,15,21][4*b+a++%4])|f>>>32-b),d,g];for(a=4;a;)k[--a]=i(k[a],b[a])}for(c="";32>a;)c+=(k[a>>3]>>4*(1^a++&7)&15).toString(h);return c}}(16);
                    if (email.indexOf('@') != -1 && email.indexOf('.') != -1) {
                        $('.login-image img').attr('src', 'https://gravatar.com/avatar/' + md5(email) + '?d=mm');
                    }
                }
            }
        }
    },
    admin :
    {
        upload: {
            init : function () {
                
                $(document).ready(function() {
                    function loadSeasons() {
                        $.ajax({
                            type: "GET",
                            url: "/ajax/seasons-by-series",
                            data: {
                                'series_id' : $('#series').val()
                            },
                            dataType: "json",
                            success: function(data) {
                                if(data.success == true) {
                                    select_html = '';
                                    for (i in data.data) {
                                        select_html = select_html + '<option value="' + data.data[i].id + '">Season ' + data.data[i].number + '</option>';
                                    }
                                    $('#seasons').html(select_html);
                                    loadEpisodes();
                                } 
                            }
                        });
                    }
                    
                    $('#series').change(function() {
                        loadSeasons();
                    });
                    
                    loadSeasons();
                    
                    function loadEpisodes() {
                        $.ajax({
                            type: "GET",
                            url: "/ajax/episodes-by-season",
                            data: {
                                'season_id' : $('#seasons').val()
                            },
                            dataType: "json",
                            success: function(data) {
                                if(data.success == true) {
                                    select_html = '';
                                    for (i in data.data) {
                                        select_html = select_html + '<option value="' + data.data[i].id + '">Episode ' + data.data[i].number + ' - ' + data.data[i].title + '</option>';
                                    }
                                    $('#episodes').html(select_html);
                                } 
                            }
                        });
                    }
                    
                    $('#seasons').change(function() {
                        loadEpisodes();
                    });
                });
                
                var swfu;
        
                window.onload = function() {
                    var settings = {
                        flash_url : "/swfupload/Flash/swfupload.swf",
                        upload_url: "/swfupload.php",
                        file_size_limit : "1024 MB",
                        file_types : "*.mp4;*.wmv;*.avi;*.m4v",
                        file_types_description : "Video Files",
                        file_upload_limit : 0,
                        file_queue_limit : 0,
                        custom_settings : {
                            progressTarget : "fsUploadProgress",
                            cancelButtonId : "btnCancel",
                            tdCurrentSpeed : document.getElementById("CurrentSpeed"),
                            tdTimeRemaining : document.getElementById("TimeRemaining")
                        },
                        debug: false,
                        
                        moving_average_history_size: 40,

                        // Button settings
                        button_width: "65",
                        button_height: "29",
                        button_image_url: "/images/browsebg.png",
                        button_placeholder_id: "spanButtonPlaceHolder",
                        button_text: '<span class="theFont">Browse</span>',
                        button_text_style: ".theFont {color: #ffffff;font-family: Arial;font-size: 12;font-weight: bold;}",
                        button_text_left_padding: 8,
                        button_text_top_padding: 5,
                        button_cursor : SWFUpload.CURSOR.HAND,
                        
                        // The event handler functions are defined in handlers.js
                        file_queued_handler : fileQueued,
                        file_queue_error_handler : fileQueueError,
                        file_dialog_complete_handler : fileDialogComplete,
                        upload_start_handler : uploadStart,
                        upload_progress_handler : uploadProgress,
                        upload_error_handler : uploadError,
                        upload_success_handler : uploadSuccess,
                        upload_complete_handler : uploadComplete,
                        queue_complete_handler : queueComplete  // Queue plugin event
                    };
        
                    swfu = new SWFUpload(settings);
                 };
            }
        },
        timeline: {
            init : function (duration, video_id) {
                // initial page load settings
                resolutions = {
                    'seconds' : 0,
                    'minutes' : 1,
                    'hours' : 2,
                };
                
                current_video_id = video_id;
                
                // set current resolution on page load according to the duration of our video
                if (duration >= 900 && duration < 14400) {
                    // if video is >= 15min set resolution to minutes
                    current_resolution = resolutions['minutes'];
                } else if (duration >= 14400) {
                    // if video is >= 4hrs set resolution to hours
                    current_resolution = resolutions['minutes'];
                } else {
                    // if video is < 15 minutes set resolution to seconds
                    current_resolution = resolutions['seconds'];
                }
                
                // Modal
                $('body').delegate(".close-modal", "click", function(e) {
                    e.preventDefault();
                    if (!$('.modal').hasClass('hide')) {
                        $('.modal').addClass('hide');
                    } else {
                        $('.modal').removeClass('hide');
                    }
                });
                
                $(function(){
                    $('.carousel').each(function(){
                        $(this).carousel({
                            interval: false
                        });
                    });
                });
                
                // Hairline
                // TODO: have horizontal scroller work in sync
                // This gets hairy because the user can move it
                
                _V_("my_video_1").ready(function(){
                    var myPlayer = this;

                    myPlayer.on("timeupdate", function (){
                        return;
                        // skip doing this for now. too many issues atm
                        var t = Math.floor(myPlayer.currentTime());
                        if (last_t != t) {
                            last_t =t;

                            if (current_resolution == 1) {
                                if (t % 60 == 0) {
                                    $('div.timeline-hairline').css('left', $('div[data-tick="'+t+'"]').position().left);
                                }
                            } else if (current_resolution == 2) {
                                if (t % 3600 == 0) {
                                    $('div.timeline-hairline').css('left', $('div[data-tick="'+t+'"]').position().left);
                                }
                            } else {
                                $('div.timeline-hairline').css('left', $('div[data-tick="'+t+'"]').position().left);
                            }
                        }
                    });
                });

                $(document).ready(function(){
                     ss.admin.timeline.setResolution(current_resolution, video_id);
                    
                     // Firefox
                     $('body').delegate("#timeline-container", "DOMMouseScroll", function(e) {
                         e.preventDefault();
                         if (e.originalEvent.detail > 0) {
                             //scroll down
                             if (current_resolution != resolutions['hours']) {
                                 ss.admin.timeline.setResolution(current_resolution + 1, video_id);
                                 current_resolution = current_resolution + 1;
                             }
                         } else {
                             //scroll up
                             if (current_resolution != resolutions['seconds']) {
                                 ss.admin.timeline.setResolution(current_resolution - 1, video_id);
                                 current_resolution = current_resolution - 1;
                             }
                         }
                         return false;
                     });
                    
                     // IE, Opera, Safari
                     $('body').delegate("#timeline-container", "mousewheel", function(e) {
                         e.preventDefault();
                         if (e.originalEvent.wheelDelta < 0) {
                             //scroll down
                             if (current_resolution != resolutions['hours']) {
                                 ss.admin.timeline.setResolution(current_resolution + 1, video_id);
                                 current_resolution = current_resolution + 1;
                             }
                         } else {
                             //scroll up
                             if (current_resolution != resolutions['seconds']) {
                                 ss.admin.timeline.setResolution(current_resolution - 1, video_id);
                                 current_resolution = current_resolution - 1;
                             }
                         }
                         return false;
                     });
                     
                     $('body').delegate(".description_save", "click", function(e) {
                         e.preventDefault();
                         var eid = $(this).data('episode-id');
                         var desc = $('textarea.dtext').val();
                         var link = $(this);
                         $.ajax({
                            type: "POST",
                            url: "/ajax/update-episode-description",
                            data: { 'episode_id' : eid, 'desc' : desc },
                            dataType: "json",
                            success: function(data) {
                                if (data.success == true) {
                                    link.html('Saved!');
                                    setTimeout(function () {
                                        link.html('SAVE DESCRIPTION<img src="/images/edit.png" width="14" height="14" class="edit" alt=""/>');
                                    }, 2000);
                                }
                            }
                        });
                     });
                     
                     $('body').delegate("a.publish-video", "click", function(e) {
                         e.preventDefault();
                         var vid = $(this).data('video-id');
                         var link = $(this);
                         $.ajax({
                            type: "POST",
                            url: "/ajax/publish-video",
                            data: { 'video_id' : vid },
                            dataType: "json",
                            success: function(data) {
                                if (data.success == true) {
                                    if (data.data['published']) {
                                        link.html('PUBLISHED');
                                    } else {
                                        link.html('PUBLISH VIDEO');
                                    }
                                }
                            }
                        });
                     });
                     
                     $('body').delegate("#delete_event", "click", function(e) {
                         e.preventDefault();
                         var eid = $(this).data('event-id');
                         $.ajax({
                            type: "POST",
                            url: "/ajax/delete-event",
                            data: { 'event_id' : eid },
                            dataType: "json",
                            success: function(data) {
                                if (data.success == true) {
                                    $('.modal').addClass('hide');
                                    $('#event-' + eid).remove();
                                }
                            }
                        });
                     });
                     
                     $('body').delegate("#submit-id", "click", function(e) {
                         e.preventDefault();
                         var eid = $(this).data('event-id');
                         f = $('#options-form');
                         $.ajax({
                            type: "POST",
                            url: "/ajax/update-video-event-options",
                            data: { 'event_id' : eid, 'form' : f.serialize() },
                            dataType: "json",
                            success: function(data) {
                                if (data.success == true) {
                                    $('.modal').addClass('hide');
                                    ss.admin.timeline.setResolution(current_resolution, current_video_id);
                                }
                            }
                        });
                     });
                });

            },
            setResolution : function(resolution, video_id) {
                if (ajax_lock) {
                    return;
                }
                ajax_lock = true;
                $.ajax({
                    type: "GET",
                    url: "/ajax/timeline",
                    data: { 'resolution' : resolution, 'video_id' : video_id },
                    dataType: "json",
                    success: function(data) {
                        if (data.success == true) {
                            $('#footer').html($(data.data['html']));
                            ajax_lock = false;
                            left_offset = $('#timeline-container').position().left;
                            right_offset = $('#timeline-container').width() + left_offset;
                            visible_width = $('#timeline-container').width();
                        }
                    }
                });
            },
            overlay : function (event_id) {
                if (!$('.modal').hasClass('hide')) {
                    $('.modal').addClass('hide');
                    $('.modal').html('<img src="/images/spinner.gif"/>');
                } else {
                    $('.modal').removeClass('hide');
                    $.ajax({
                        type: "GET",
                        url: "/ajax/event-options",
                        data: { 'event_id' : event_id },
                        dataType: "json",
                        success: function(data) {
                            if (data.success == true) {
                                $('.modal').html(data.data['html']);
                            }
                        }
                    });
                }
            },
            allowDrop : function(ev) {
                ev.preventDefault();
            },
            drag : function (ev) {
                ev.dataTransfer.setData("Text",ev.target.id);
            },
            drop : function (ev) {
                ev.preventDefault();
                var from = $(document.getElementById(ev.dataTransfer.getData("Text")));
                var to = $(ev.target);

                if ( from.hasClass('timeline-module') ) {
                    // Existing module on timeline that we are moving to another tick or swaping order
                    if (!to.hasClass('timeline-tick')) {
                        if (!to.hasClass('timeline-module')) {
                            var index = $(".timeline-module").index(to.parents('.timeline-module'));
                        } else {
                            var index = $(".timeline-module").index(to);
                        }

                        to.parents('.timeline-tick').children('.timeline-module:nth-child(' + (index + 1) +')').before($(document.getElementById(ev.dataTransfer.getData("Text"))));
                    } else {
                        to.append(document.getElementById(ev.dataTransfer.getData("Text")));
                    }
                    $.ajax({
                        type: "POST",
                        url: "/ajax/update-video-event",
                        data: { 
                            'start_sec' : to.data('tick'), 
                            'resolution' : current_resolution, 
                            'event_id' : from.data('event-id')
                        },
                        dataType: "json",
                        success: function(data) {
                            if (data.success == true) {
                               ss.admin.timeline.setResolution(current_resolution, current_video_id);
                            }
                        }
                    });
                } else {
                    // Drap from module list
                    var module = from.clone();
                    module.removeClass('module').addClass('timeline-module');
                    module.children('span.down-arrow').remove();
                    module.append('<i class="module-settings icon-cog icon-white"></i>');
                    module.attr("draggable", true);
                    module.attr("id", Date.now());
                    module.attr("ondragstart", "ss.admin.timeline.drag(event)");

                    to.append(module);
                    // Now that we have it placed, lets fire off an ajax call to store this event 
                    
                    $.ajax({
                        type: "POST",
                        url: "/ajax/new-video-event",
                        data: { 
                            'start_sec' : to.data('tick'), 
                            'resolution' : current_resolution, 
                            'video_id' : current_video_id, 
                            'module_id' : module.data('module-id')
                        },
                        dataType: "json",
                        success: function(data) {
                            if (data.success == true) {
                                module.attr('data-event-id', data.data['event_id']);
                                module.attr("onclick", "ss.admin.timeline.overlay("+data.data['event_id']+")");
                            }
                        }
                    });
                }
            }
        }
    }
}
// bootstrap carousel
$(document).ready(function(){
    $('.carousel').each(function(){
        $(this).carousel({
            interval: false
        });
    });
});

// You need to move this into the specific page javascript above as it is breaking javascript elsewhere.
// Bootstrap - jQuery
$(function(){

    //var it = $("#module-2");
    //var offset = it.offset();
    //console.log( "left: " + offset.left + ", top: " + offset.top );
   
    // $('.carousel').each(function(){
    //     $(this).carousel({
    //         interval: false
    //     });
    // });
});

