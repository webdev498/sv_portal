/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



start_time = 0;
var prev_timer;
var jid;
var uploader;
$(document).ready(function(){
    
    $('.btn-add-video').click(function(e){
        e.preventDefault();
        $('.qq-upload-button input').click();
    });
    
    t = setTimeout(function(){
        $('.qq-upload-button input').attr('capture','camcorder');
    },2000);
    
    var uploader = new qq.s3.FineUploader({
        debug: true,
        element: document.getElementById('fine-uploader'),
        
        request: {
            endpoint: 'firstshowing.s3.amazonaws.com',
            accessKey: 'AKIAJYWF36QU34TYHOYA',
            params : {
                "generateError": true,
                csrf_test_name:csrf.csrf_test_name
            }
        },
        signature: {
            endpoint:  's3/signature.php',
            version : 4
        },
        uploadSuccess: {
            endpoint: 's3/success.php',
            params : {
                csrf_test_name:csrf.csrf_test_name,
                propertyID: $('.btn-add-video').attr('data-property-id')
                
            }
        },
        iframeSupport: {
            localBlankPagePath: "s3/success.php"
        },
        
        validation: {
            acceptFiles: "video/*;capture=camcorder",
            allowedExtensions:['mp4','mov', 'mkv','flv','wmv', 'wma','ogg', 'oga', 'ogv', 'ogx', '3gp', '3gp2', '3g2', '3gpp', '3gpp2','m4a', 'm4v', 'f4v', 'f4a', 'm4b', 'm4r', 'f4b', 'webm','avi','mxf','mpg', 'mpeg','mp2','.mpe','mpv','m4p','asf','rmvb','rm','yuv','vob', 'm2ts', 'mts','ts'],
            sizeLimit: 53687091200,
            itemLimit: 1,
            minSizeLimit:5242880
        },
        retry: {
            enableAuto: true
            },
        chunking: {
            enabled: true,
            partSize:5242880
            },
        resume: {
            enabled: true
            },
        multiple:false,
        
        callbacks: {
            onComplete: function(id,name,responseJSON,xhr){
                if(responseJSON.result === 0){
                    alert(responseJSON.message);
                    console.log(responseJSON.link);
                }
                else{
                    $('.embed-responsive').html('<video class="embed-responsive-item" controls autoplay name="media"><source src="" type="video/mp4"></video>');
                    $('.embed-responsive video source').attr('src',responseJSON.link);
                    jid = responseJSON.id;
                    $(".embed-responsive video")[0].load();
                    prev_timer = setInterval(checkJobStatus(),5000);
                   
                   
                }
            },
            onUpload: function(){
                d = new Date();
                start_time = d.getTime();
                console.log(start_time);
                
                $('.download-link span').addClass('hidden');
                $('.copy-download-link').addClass('hidden');
                
            },
            onTotalProgress : function(totalUploadedBytes,totalBytes){
                d = new Date();
                speed = totalUploadedBytes/((d.getTime() - start_time)/1000);
                if((speed/1024) > 1024){
                    display_speed = (speed/1024/1024);
                    display_speed = (Math.round(display_speed * 100) / 100) + ' MB/s';
                }
                else{
                    display_speed = (speed/1024);
                    display_speed = (Math.round(display_speed * 100) / 100) + ' KB/s';
                }
                
                time_remaining = Math.round((totalBytes - totalUploadedBytes)/speed);
                
                $('.qq-progress-bar-container-selector').children('div').html('<span>Uploading at ' + display_speed + '. ' + toHHMMSS(time_remaining) + ' remaining.' );
                
            },
            onError : function (id, name, errorReason, xhrOrXdr){
                //alert(errorReason);
            },
            onSubmit : function (id,name){
                
                /* url = 'validateExt';
                retval = true;
                message = '';
                postData = csrf;
                postData.name = name;
                
                $.ajax( url, {
                    async:false,
                    data:postData,
                    type:'POST'
                })
                .done(function( data ) {
                    data = JSON.parse(data);
                    message = data.message;
                    retval = data.status;                    
                })
                .always(function(data){
                    data = JSON.parse(data);
                    csrf.csrf_test_name = data.hash;
                    
                });
                
                if(retval == false ){
                    alert(message);
                }
                
                uploader.setUploadSuccessParams({csrf_test_name:csrf.csrf_test_name});
                uploader.setParams({request:{params:{csrf_test_name:csrf.csrf_test_name}}});
                
                return retval; */
            }
        }
        
    });
    
    console.log(qq.supportedFeatures);
    
});

toHHMMSS = function (handle) {
    var sec_num = parseInt(handle, 10); // don't forget the second param
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    var time    = hours+'h, '+minutes+'m, '+seconds + 's';
    return time;
}

copyToClipboard = function(textToCopy) {
	  // create hidden text element, if it doesn't already exist
    var targetId = "_hiddenCopyText_";
    target = document.getElementById(targetId);
    if (!target) {
        var target = document.createElement("textarea");
        target.style.position = "absolute";
        target.style.left = "-9999px";
        target.style.top = "0";
        target.id = targetId;
        document.body.appendChild(target);
    }
    
    target.textContent = textToCopy;
    
    // select the content
    var currentFocus = document.activeElement;
    target.focus();
    target.setSelectionRange(0, target.value.length);
    
    // copy the selection
    var succeed;
    try {
    	  succeed = document.execCommand("copy");
    } catch(e) {
        succeed = false;
    }
    // clear content
    target.textContent = "";
    if (currentFocus && typeof currentFocus.focus === "function") {
        currentFocus.focus();
    }
    $('#' + targetId).remove();
    return succeed;
}

checkJobStatus = function(){
    //location.reload();
    if($(".embed-responsive video")[0].readyState == 0){
        location.reload();
        $(".embed-responsive video")[0].load();
    }
    else{
        clearInterval(prev_timer);
    }
//    url = 's3/jobstatus.php';
//    postdata = 'id=' + jid;
//    $.post(url,postdata)
//        .done(function(data){
//            //delete successful
//            data = JSON.parse(data);
//            if(data.result === 1){
//                clearInterval(prev_timer);
//                $(".embed-responsive video")[0].load();
//                alert($(".embed-responsive video")[0].networkState);
//            }
//        })
//        .always(function(data){
//            data = JSON.parse(data);
//            //$('input[name="csrf_token"]').val(data.hash);                
//        });
        
    
}