<script type="text/javascript" src="{$GLOBALS.user_site_url}/files/video/flowplayer-3.2.12.min.js"></script>
<a href="{$listing.user.video.file_url|escape:'url'}" id="player_{$listing.user.video.file_id}" class="player"></a>
<script type="text/javascript">
    $f("player_{$listing.user.video.file_id}", "{$GLOBALS.user_site_url}/files/video/flowplayer-3.2.16.swf",  {
        clip: {
            url: "{$listing.user.video.file_url|escape:'url'}",
            autoPlay: false,
            autoBuffering: true,
            scaling: "fit"
        },
        plugins: {
            // default controls with the same background color as the page background
            controls:  {
                backgroundColor: '#1c1c1c',
                backgroundGradient: 'none',
                all:false,
                scrubber:true,
                fullscreen:true,
                play:true,
                volume:true,
                mute:true,
                height:30,
                progressColor: '#6d9e6b',
                bufferColor: '#333333',
                autoHide: false
            },
            // time display positioned into upper right corner
            time: {
                url: "{$GLOBALS.user_site_url}/files/video/flowplayer.controls-3.2.15.swf",
                top:0,
                backgroundGradient: 'none',
                backgroundColor: 'transparent',
                buttonColor: '#ffffff',
                all: false,
                time: true,
                height:20,
                right:0,
                width:100,
                autoHide: false
            }
        },
        // canvas coloring and custom gradient setting
        canvas: {
            backgroundColor:'#000000',
            backgroundGradient: [0.1, 0]
        }

    });
</script>