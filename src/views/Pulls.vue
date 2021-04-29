<template>
  <div>
    <p>Pulls: {{ userName }} / {{ projectName }} / pulls</p>
    <div ref="osmdContainer" />
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from "vue";
import { OpenSheetMusicDisplay } from "opensheetmusicdisplay";
import score2 from "@/scores/E12_intermission.musicxml";
import score1 from "@/scores/E12_intermission-2.musicxml";
import { scoreDiff } from "@/utils/diff";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
  },
  setup() {
    const osmdContainer = ref<HTMLElement>();
    const parser = new DOMParser();

    const actual = parser.parseFromString(score2, "text/xml");
    const base = parser.parseFromString(score1, "text/xml");
    const diff = parser.parseFromString(score2, "text/xml");

    scoreDiff(actual, base, diff.documentElement);
    // console.log(diff)
    // console.log(new XMLSerializer().serializeToString(diff))

    async function loadScore() {
      const osmd = new OpenSheetMusicDisplay(osmdContainer.value!, {
        backend: "svg",
        drawTitle: true,
        drawCredits: true,
      });

      await osmd.load(diff);
      osmd.render();
    }
    onMounted(loadScore);

    return { osmdContainer };
  },
});
</script>
