
var i = 0;
var activities_ticker = $('#activities').newsTicker({
        row_height:     360,
        max_rows:       3,
        direction:      'down',
        autostart:      0,
        pauseOnHover:   1
      
});  

var audio = new Audio();

function show_activity(lamp, action, msg, photo, sound) {

   
  
    str = '<li> \
            <div class="lamp"> \
                <img src="/data/images/lamp_icons/' + lamp + '.svg" width="140" alt=""/> \
                <br/> \
                <img class="action_icon" src="/data/images/action_icons/' + action + '.svg"  width="60" alt=""/> \
            </div> \
            <div class="message"> \
                <span class="dyntextval">' + msg + '</span> \
            </div> \
            <div class="photo"> \
                <img class="ring_cut" src="/content/ring_cut.png" width="250" alt=""/>';

                if (photo!=''){
                    str = str + ' <img src="/data/content/' + photo + '"  width="250" height="250" alt=""/>';
                }
                
               str = str + ' </div> \
        </li>';

    $('#activities').append(str);

    $('.message').textfill({debug: false, maxFontPixels: 120});
    audio.pause();
    audio = new Audio('/data/sounds/' + sound);
    audio.play();

    setTimeout(function() {
        $('#activities').newsTicker('moveNext');
    }, 100);

     setTimeout(function() {
        $('#activities').children().last().remove();
    }, 150);
}
