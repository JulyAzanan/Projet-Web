<template>
  <li>
    <a @click="playPause" class="uk-icon-button" uk-icon="icon: play"></a>
  </li>
  <li>
    <a @click="refresh" class="uk-icon-button" uk-icon="icon: refresh"></a>
  </li>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { OpenSheetMusicDisplay } from "opensheetmusicdisplay";
import AudioPlayer from "osmd-audio-player";
import { PlaybackEvent } from "osmd-audio-player/dist/PlaybackEngine";

export default defineComponent({
  props: {
    osmd: Object as () => OpenSheetMusicDisplay,
  },
  setup(props) {
    const audioPlayer = new AudioPlayer();
    let atFileBeginning = true;
    let play = false;

    function playPause() {
      if (play) {
        audioPlayer.pause();
      } else {
        audioPlayer.play();
      }
      play = !play;
    }

    audioPlayer.on(PlaybackEvent.ITERATION, () => {
      if (atFileBeginning) {
        props.osmd!.cursor.show();
        atFileBeginning = false;
      } else {
        props.osmd!.cursor.next();
      }
    });

    function refresh() {
      atFileBeginning = true;
      props.osmd!.cursor.hide();
      props.osmd!.cursor.reset();
      audioPlayer.stop();
      play = false;
    }

    // @ts-ignore
    audioPlayer.loadScore(props.osmd);

    return { playPause, refresh };
  },
});
</script>

<style lang="scss" scoped>
</style>