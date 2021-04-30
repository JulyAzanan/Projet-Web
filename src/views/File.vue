<template>
  <div>
    <div class="uk-child-width-auto" uk-grid>
      <div class="uk-width-expand">
        <blockquote>{{ filePath }}</blockquote>
      </div>
      <ul v-if="ready" class="uk-grid-small uk-flex-middle" uk-grid>
        <li>
          <a
            v-if="!unkownExtension"
            @click="playPause"
            class="uk-icon-button"
            uk-icon="icon: play"
          ></a>
        </li>
        <li>
          <a
            v-if="!unkownExtension"
            @click="refresh"
            class="uk-icon-button"
            uk-icon="icon: refresh"
          ></a>
        </li>
        <li>
          <a
            @click="download"
            class="uk-icon-button"
            uk-icon="icon: download"
          ></a>
        </li>
      </ul>
    </div>

    <div class="uk-placeholder uk-text-center">
      <div v-show="ready" ref="osmdContainer" />
      <div v-if="unkownExtension">
        <span
          class="uk-margin-small-right uk-text-muted"
          uk-icon="icon: warning; ratio: 2"
        ></span>
        Extension de fichier non supportée.
      </div>
      <div v-if="!ready">
        <span uk-spinner class="uk-margin-small-right uk-text-muted"></span>
        Conversion en cours
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, ref } from "vue";
import { onBeforeRouteUpdate } from "vue-router";
import { OpenSheetMusicDisplay } from "opensheetmusicdisplay";
import AudioPlayer from "osmd-audio-player";
import { PlaybackEvent } from "osmd-audio-player/dist/PlaybackEngine";
import { notFound } from "@/app/routes";
import * as Score from "@/api/score";
import { parseScore } from "@/utils/musicXML";
import "@/utils/cursor";

function downloadBase64(data: string, name: string) {
  const link = document.createElement("a");

  link.href = "data:application/octet-stream;base64," + data;
  link.download = name;

  document.body.appendChild(link);
  link.dispatchEvent(
    new MouseEvent("click", {
      bubbles: true,
      cancelable: true,
      view: window,
    })
  );
  document.body.removeChild(link);
}

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
    const unkownExtension = ref(false);
    let xml: Document;

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
      const doc = await parseScore(
        score.value.name.split(".").pop(),
        score.value.content
      );
      if (doc !== null) xml = doc;
      else unkownExtension.value = true;
      ready.value = true;
    }

    const initialized = init();
    const audioPlayer = new AudioPlayer();
    let atFileBeginning = true;
    let osmd: OpenSheetMusicDisplay;

    audioPlayer.on(PlaybackEvent.ITERATION, () => {
      if (atFileBeginning) {
        osmd.cursor.show();
        atFileBeginning = false;
      } else {
        osmd.cursor.next();
      }
    });

    async function loadScore() {
      await initialized;
      if (!unkownExtension.value) {
        osmd = new OpenSheetMusicDisplay(osmdContainer.value!);

        await osmd.load(xml);
        osmd.render();
        osmd.cursor.reset();
        // @ts-ignore
        await audioPlayer.loadScore(osmd);
      }
    }

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
      atFileBeginning = true;
      osmd.cursor.hide();
      osmd.cursor.reset();
      audioPlayer.stop();
      play = false;
    }

    function download() {
      downloadBase64(score.value.content, score.value.name);
    }

    onMounted(loadScore);
    onBeforeRouteUpdate(async (to) => {
      if (to.params.filePath !== props.filePath) {
        ready.value = false;
        await init();
        await loadScore();
      }
    });

    return {
      osmdContainer,
      ready,
      audioPlayer,
      playPause,
      refresh,
      unkownExtension,
      download,
    };
  },
});
</script>
