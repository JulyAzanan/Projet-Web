<template>
  <div>
    <div class="uk-child-width-auto" uk-grid>
      <div class="uk-width-expand">
        <blockquote>{{ filePath }}</blockquote>
      </div>
      <ul
        v-if="ready && !unkownExtension"
        class="uk-grid-small uk-flex-middle"
        uk-grid
      >
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

    <div v-if="!ready" uk-spinner></div>
    <div class="uk-placeholder uk-text-center">
      <div ref="osmdContainer" />
      <div v-if="unkownExtension">
        <span
          class="uk-margin-small-right uk-text-muted"
          uk-icon="icon: warning; ratio: 2"
        ></span>
        Extension de fichier non supportée.
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, ref } from "vue";
import { OpenSheetMusicDisplay } from "opensheetmusicdisplay";
import AudioPlayer from "osmd-audio-player";
import { notFound } from "@/app/routes";
import * as Score from "@/api/score";
import WebMscore from "webmscore";
import { InputFileFormat } from "webmscore/schemas";

function base64ToUint8Array(data: string) {
  const binary = atob(data);
  const array = new Uint8Array(new ArrayBuffer(binary.length));

  for (let i = 0; i < binary.length; i++) {
    array[i] = binary.charCodeAt(i);
  }
  return array;
}

async function toXml(extension: InputFileFormat, data: string) {
  await WebMscore.ready;
  const webMscore = await WebMscore.load(extension, base64ToUint8Array(data));
  return webMscore.saveXml();
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
    let xml = "";

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
      const extension = score.value.name.split(".").pop();
      switch (extension) {
        case "musicxml":
        case "xml":
          xml = atob(score.value.content);
          break;

        case "ptb":
        case "gp":
        case "gpx":
        case "gp5":
        case "gp4":
        case "gp3":
        case "gtp":
        case "kar":
        case "midi":
        case "mscx":
        case "mscz":
          xml = await toXml(extension, score.value.content);
          break;
        default:
          unkownExtension.value = true;
          break;
      }
      ready.value = true;
    }

    const initialized = init();
    const audioPlayer = new AudioPlayer();

    onMounted(async () => {
      await initialized;
      if (!unkownExtension.value) {
        const osmd = new OpenSheetMusicDisplay(osmdContainer.value!, {
          backend: "svg",
          drawTitle: true,
          drawCredits: true,
        });

        await osmd.load(xml);
        osmd.render();
        // @ts-ignore
        await audioPlayer.loadScore(osmd);
      }
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
      unkownExtension,
    };
  },
});
</script>
