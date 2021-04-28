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
import score1 from "@/scores/E12_intermission-1.musicxml";
import { measureDiff } from "@/utils/diff";

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

    const parts_a = actual.getElementsByTagName("part");
    const parts_b = base.getElementsByTagName("part");
    const parts_d = diff.getElementsByTagName("part");
    for (let i_p = 0; i_p < /* parts_d.length */ 1; i_p++) {
      const measures_a = parts_a[i_p].getElementsByTagName("measure");
      const measures_b = parts_b[i_p].getElementsByTagName("measure");
      const measures_d = parts_d[i_p].getElementsByTagName("measure");
      for (let i_m = 0; i_m < measures_d.length; i_m++) {
        measureDiff(measures_a[i_m], measures_b[i_m], measures_d[i_m])
      }
    }

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
