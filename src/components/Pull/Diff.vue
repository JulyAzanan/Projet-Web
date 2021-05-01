<template>
  <div v-show="false" ref="osmdContainerBase" />
  <div v-show="false" ref="osmdContainerTarget" />
  <div
    v-if="ready.player && !unknownExtension"
    class="uk-child-width-auto"
    uk-grid
  >
    <ul v-if="state.added" class="added-file uk-width-expand">
      <strong>Fichier ajouté</strong>
    </ul>
    <ul v-else class="uk-grid-small uk-flex-middle uk-width-expand" uk-grid>
      <Player :osmd="osmd_base" />
    </ul>
    <ul v-if="state.removed" class="removed-file">
      <strong>Fichier supprimé</strong>
    </ul>
    <ul v-else class="uk-grid-small uk-flex-middle" uk-grid>
      <Player :osmd="osmd_target" />
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
</template>

<script lang="ts">
import { defineComponent, ref, onMounted, reactive } from "vue";
import { OpenSheetMusicDisplay } from "opensheetmusicdisplay";
import Player from "@/components/Pull/Player.vue";
import * as Score from "@/api/score";
import { scoreDiff } from "@/utils/diff";
import { parseScore } from "@/utils/musicXML";

export default defineComponent({
  props: {
    base: Object as () => Score.DownloadResult,
    target: Object as () => Score.DownloadResult,
  },
  components: {
    Player,
  },
  setup(props) {
    const ready = reactive({
      page: false,
      player: false,
    });
    const state = reactive({
      added: false,
      removed: false,
    });
    const unknownExtension = ref(false);
    const osmdContainer = ref<HTMLElement>();
    const osmdContainerBase = ref<HTMLElement>();
    const osmdContainerTarget = ref<HTMLElement>();
    const osmd_base = ref<OpenSheetMusicDisplay>();
    const osmd_target = ref<OpenSheetMusicDisplay>();

    let xml_base: Document;
    let xml_target: Document;
    let xml_diff: Document;

    async function init() {
      if (props.base !== null && props.target != null) {
        const docs = await Promise.all([
          parseScore(props.base?.name.split(".").pop(), props.base!.content),
          parseScore(
            props.target?.name.split(".").pop(),
            props.target!.content
          ),
        ]);
        if (docs[0] !== null && docs[1] !== null) {
          xml_base = docs[0];
          xml_target = docs[1];
          xml_diff = xml_target.cloneNode(true) as Document;
        } else unknownExtension.value = true;
      } else if (props.base !== null) {
        const doc = await parseScore(
          props.base?.name.split(".").pop(),
          props.base!.content
        );
        if (doc !== null) {
          xml_base = doc;
          state.removed = true;
        } else unknownExtension.value = true;
      } else {
        const doc = await parseScore(
          props.target?.name.split(".").pop(),
          props.target!.content
        );
        if (doc !== null) {
          xml_target = doc;
          state.added = true;
        } else unknownExtension.value = true;
      }
      ready.page = true;
    }

    const initialized = init();

    async function loadScore() {
      await initialized;
      if (!unknownExtension.value) {
        const osmd = new OpenSheetMusicDisplay(osmdContainer.value!);
        if (state.added) {
          osmd_target.value = new OpenSheetMusicDisplay(
            osmdContainerTarget.value!
          );
          osmd.load(xml_target);
          osmd_target.value.load(xml_target);
          osmd_target.value.render();
        } else if (state.removed) {
          osmd_base.value = new OpenSheetMusicDisplay(osmdContainerBase.value!);
          osmd.load(xml_base);
          osmd_base.value.load(xml_base);
          osmd_base.value.render();
        } else {
          osmd_base.value = new OpenSheetMusicDisplay(osmdContainerBase.value!);
          osmd_target.value = new OpenSheetMusicDisplay(
            osmdContainerTarget.value!
          );
          scoreDiff(
            (xml_target.cloneNode(true) as Document).documentElement,
            xml_base.documentElement,
            xml_diff.documentElement
          );

          await Promise.all([
            osmd.load(xml_diff),
            osmd_base.value.load(xml_base),
            osmd_target.value.load(xml_target),
          ]);

          osmd_base.value.render();
          osmd_target.value.render();
        }
        osmd.render();
        ready.player = true;
      }
    }

    onMounted(loadScore);

    return {
      osmdContainer,
      osmdContainerBase,
      osmdContainerTarget,
      ready,
      unknownExtension,
      osmd_base,
      osmd_target,
      state,
    };
  },
});
</script>

<style lang="scss" scoped>
.removed-file {
  color: #f94144;
}

.added-file {
  color: #69b32b;
}
</style>