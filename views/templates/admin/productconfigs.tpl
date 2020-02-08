    <div class="panel-body">
        <h2>Simple Video configuration</h2>
        <div class="form-group clearfix">
            {* Only show product video setting if it is enabled in module setting *}
            {if $enable_videos==1}
            <div class="panel">
                <label for="enable_video">{l s='Enable video for this product:' mod='simplevideo'}</label>
                <img src="../img/admin/enabled.gif" alt="" />
                <input type="radio" id="enable_video_1" name="enable_video" value="1" {if $video.enable=='1'
                    }checked{/if} />
                <label class="t" for="enable_video_1">Yes</label>
                <img src="../img/admin/disabled.gif" alt="" />
                <input type="radio" id="enable_video_0" name="enable_video" value="0" {if empty($video.enable) ||
                    $video.enable_video=='0' }checked{/if} />
                <label class="t" for="enable_video_0">No</label>
                <br>
                <label for="id_video"> {l s='Youtube video id for this product:' mod='simplevideo'}</label>
                <input type="text" name="id_video" id="id_video" class="form-control" value="{$video.id_video}"/>
                <input type="hidden" name="update_video" id="update_video" value="1">
                <label>Last update: {l s=$video.date_add mod='simplevideo'}</label>
                <p>Example for Youtube video id: https://www.youtube.com/watch?v=<u>pFAknD_9U7c</u>.
                    You only need to add the underline part of the Youtube link to the module.</p>
            </div>
            {* If it is not enabled in the module setting, an option to enable it will show here. *}
            {else}
            <div class="panel">
                <h3>Product Video is not enabled</h3>
                <label for="enable_videos">{l s='Do you want to enable Product Video:' mod='simplevideo'}</label>
                <img src="../img/admin/enabled.gif" alt="" />
                <input type="radio" id="enable_videos_1" name="enable_videos" value="1" {if $enable_videos=='1'
                    }checked{/if} />
                <label class="t" for="enable_videos_1">Yes</label>
                <img src="../img/admin/disabled.gif" alt="" />
                <input type="radio" id="enable_videos_0" name="enable_videos" value="0" {if empty($enable_videos) ||
                    $enable_videos=='0' }checked{/if} />
                <label class="t" for="enable_videos_0">No</label>
                <br>
                <label>Please refresh the page after successfuly update setting or using the built-in Save and refresh button.</label>
                <input id="submit" type="submit" class="btn btn-primary save uppercase ml-3" value="Save and refresh" onClick="setInterval(observer,2000)">
            </div>
            {/if}
        </div>
    </div>
    <script>
        function observer() {
            var parentDOM = document.getElementById('growls-default');
            var test=parentDOM.getElementsByClassName('growl-message');
            var a ="Settings updated.";
            if(test[0].innerHTML===a){
                setTimeout(window.location.reload(), 5000);
            }
        }
    </script>