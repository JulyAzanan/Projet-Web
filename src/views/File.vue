<template>
  <div>
    <div class="uk-child-width-auto" uk-grid>
      <div class="uk-width-expand">
        <h3>{{ filePath }}</h3>
      </div>
      <ul v-if="ready" class="uk-grid-small uk-flex-middle" uk-grid>
        <li>
          <a class="uk-icon-button" uk-icon="icon: play" @click="playPause"></a>
        </li>
        <li>
          <a
            class="uk-icon-button"
            uk-icon="icon: refresh"
            @click="refresh"
          ></a>
        </li>
      </ul>
    </div>

    <div ref="osmdContainer" />
    <div v-if="!ready" uk-spinner></div>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, ref } from "vue";
import { OpenSheetMusicDisplay } from "opensheetmusicdisplay";
import AudioPlayer from "osmd-audio-player";
import { notFound } from "@/app/routes";
import * as Score from "@/api/score";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
    branchName: String,
    commitID: String,
    filePath: String,
  },
  setup(props) {
    const osmdContainer = ref<HTMLElement>();
    const ready = ref(false);
    const score = ref<Score.DownloadResult>({
      name: "",
      content: "",
    });

    async function init() {
      const result = await Score.fetch(
        props.userName!,
        props.projectName!,
        props.branchName!,
        props.commitID!,
        props.filePath
      );
      if (result === null) return notFound();
      score.value = result;
      ready.value = true;
    }

    const initialized = init();
    const audioPlayer = new AudioPlayer();

    onMounted(async () => {
      await initialized;
      const osmd = new OpenSheetMusicDisplay(osmdContainer.value!, {
        backend: "svg",
        drawTitle: true,
        drawCredits: true,
      });

      await osmd.load(atob(score.value.content));
      osmd.render();
      // @ts-ignore
      await audioPlayer.loadScore(osmd);
    });

    let play = false;

    function playPause() {
      if (play) {
        audioPlayer.pause();
      } else {
        audioPlayer.play();
      }
      play = !play;
    }

    function refresh() {
      audioPlayer.stop();
      audioPlayer.play();
      play = true;
    }

    return {
      osmdContainer,
      ready,
      audioPlayer,
      playPause,
      refresh,
    };
  },
});
</script>
