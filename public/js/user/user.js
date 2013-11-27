/* 
 * @js      : User panel actions
 * Author   : Sobin
 * Date     : 27 Nov 2013
 * ******************************
 */

/*
 * @function    : Play video control
 * Author       : Sobin
 */
function playVideo(id) {    
    document.getElementById('video'+id).play();    
}

/*
 * @function    : Pause video control
 * Author       : Sobin
 */
function pauseVideo(id) {    
    document.getElementById('video'+id).pause();    
}

$(function(){
    /*
    * @method  : Handle play button click from user home
    * Author   : Sobin
    */
    $('.playVideo').click(function(){
        var id = $(this).attr('id');        
        if($('#play-status'+id).val() == 0) { 
            playVideo(id);
            $('#play-status'+id).val("1");
            $(this).val('Pause');
        }
        else {
            pauseVideo(id);
            $('#play-status'+id).val("0");
            $(this).val('Play');
        }
    });
});



