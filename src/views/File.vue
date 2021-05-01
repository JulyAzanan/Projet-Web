<template>
  <div>
    <div class="uk-child-width-auto" uk-grid>
      <div class="uk-width-expand">
        <blockquote>{{ filePath }}</blockquote>
      </div>
      <ul v-if="ready.page" class="uk-grid-small uk-flex-middle" uk-grid>
        <Player v-if="ready.player && !unknownExtension" :osmd="osmd " />
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
      <div v-show="ready.page" ref="osmdContainer" />
      <div v-if="unknownExtension">
        <span
          class="uk-margin-small-right uk-text-muted"
          uk-icon="icon: warning; ratio: 2"
        ></span>
        Extension de fichier non supportée.
      </div>
      <div v-if="!ready.page">
        <span uk-spinner class="uk-margin-small-right uk-text-muted"></span>
        Conversion en cours
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, reactive, ref } from "vue";
import { onBeforeRouteUpdate } from "vue-router";
import { OpenSheetMusicDisplay } from "opensheetmusicdisplay";
import { notFound } from "@/app/routes";
import * as Score from "@/api/score";
import { parseScore } from "@/utils/musicXML";
import Player from "@/components/Pull/Player.vue";
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
  components: {
    Player,
  },
  setup(props) {
    const osmdContainer = ref<HTMLElement>();
    const ready = reactive({
      page: false,
      player: false,
    });
    const score = ref<Score.DownloadResult>({
      name: "",
      content: "",
    });
    const unknownExtension = ref(false);
    const osmd = ref<OpenSheetMusicDisplay>();
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
      else unknownExtension.value = true;
      ready.page = true;
    }

    const initialized = init();

    async function loadScore() {
      await initialized;
      if (!unknownExtension.value) {
        osmd.value = new OpenSheetMusicDisplay(osmdContainer.value!);

        await osmd.value.load(xml);
        osmd.value.render();
        osmd.value.cursor.reset();
        ready.player = true;
      }
    }

    function download() {
      downloadBase64(score.value.content, score.value.name);
    }

    onMounted(loadScore);
    onBeforeRouteUpdate(async (to) => {
      if (to.params.filePath !== props.filePath) {
        ready.page = false;
        ready.player = false;
        await init();
        await loadScore();
      }
    });

    return {
      osmdContainer,
      ready,
      unknownExtension,
      download,
      osmd,
    };
  },
});
</script>
