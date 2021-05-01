<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container uk-container-small uk-margin-large-bottom">
      <div class="uk-margin-small uk-grid-small uk-grid" uk-grid>
        <div class="uk-first-column uk-width-expand">
          <div class="uk-margin">
            <div class="uk-form-controls">
              <select class="uk-select" v-model="baseBranch">
                <option disabled value="">Origine</option>
                <option
                  v-for="branch in project.branches"
                  :key="branch.name"
                  :value="branch.name"
                >
                  {{ branch.name }}
                </option>
              </select>
            </div>
          </div>
        </div>
        <span
          class="chevron"
          uk-icon="ratio: 3; icon: chevron-double-right"
        ></span>
        <div class="uk-width-expand">
          <div class="uk-form-controls">
            <select class="uk-select" v-model="targetBranch">
              <option disabled value="">Branche cible</option>
              <option
                v-for="branch in project.branches"
                :key="branch.name"
                :value="branch.name"
              >
                {{ branch.name }}
              </option>
            </select>
          </div>
        </div>
      </div>
      <div v-if="state.same" class="uk-alert-warning uk-alert" uk-alert="">
        <strong> Les branches sont identiques! </strong>
      </div>
      <div v-if="state.emptyBase" class="uk-alert-warning uk-alert" uk-alert="">
        <strong> La branche d'origine n'a pas de commit! </strong>
      </div>
      <div
        v-if="state.emptyTarget"
        class="uk-alert-warning uk-alert"
        uk-alert=""
      >
        <strong> La branche cible n'a pas de commit! </strong>
      </div>
      <ul v-if="state.ready" uk-accordion="multiple: true">
        <li v-for="pair in differences" :key="pair[0].name" class="uk-open">
          <a class="uk-accordion-title">{{ pair[0].name }}</a>
          <div class="uk-accordion-content">
            <Diff :base="pair[0]" :target="pair[1]" />
          </div>
        </li>
      </ul>
      <div v-if="state.ready" class="uk-child-width-auto" uk-grid>
        <div class="uk-width-expand" />
        <button
          type="button"
          class="uk-button uk-button-primary"
          @click="merge"
        >
          Fusionner les branches
        </button>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, reactive, ref, watch } from "vue";
import * as Project from "@/api/project";
import * as Score from "@/api/score";
import * as Branch from "@/api/branch";
import * as Commit from "@/api/commit";
import router from "@/app/routes";
import Diff from "@/components/Pull/Diff.vue";
import { notifySuccess, notifyWarning } from "@/utils/notification";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
    project: Object as () => Project.FetchResult,
  },
  components: {
    Diff,
  },
  setup(props) {
    const baseBranch = ref("");
    const targetBranch = ref("");
    const state = reactive({
      same: false,
      ready: false,
      emptyBase: false,
      emptyTarget: false,
    });
    const differences = ref<
      [Score.DownloadResult | null, Score.DownloadResult | null][]
    >();

    async function compareBranches() {
      state.ready = false;
      state.same = false;
      state.emptyBase = false;
      state.emptyTarget = false;
      if (baseBranch.value === "" || targetBranch.value === "") return;
      if (baseBranch.value === targetBranch.value) {
        state.same = true;
        return;
      }
      const branches = await Promise.all([
        Branch.fetch(props.userName!, props.projectName!, baseBranch.value),
        Branch.fetch(props.userName!, props.projectName!, targetBranch.value),
      ]);
      if (branches[0]?.lastCommit === null) {
        state.emptyBase = true;
        return;
      }
      if (branches[1]?.lastCommit === null) {
        state.emptyTarget = true;
        return;
      }
      const scores = await Promise.all([
        Commit.download(
          props.userName!,
          props.projectName!,
          baseBranch.value,
          branches[0]!.lastCommit
        ),
        Commit.download(
          props.userName!,
          props.projectName!,
          targetBranch.value,
          branches[1]!.lastCommit
        ),
      ]);
      for (const baseScore of scores[0]) {
        differences.value?.push([
          baseScore,
          scores[1].find((s) => s.name === baseScore.name) ?? null,
        ]);
      }
      for (const targetScore of scores[1]) {
        if (scores[0].some((s) => s.name === targetScore.name)) return;
        differences.value?.push([null, targetScore]);
      }
      state.ready = true;
    }

    async function merge() {
      const success = await Branch.merge(
        props.userName!,
        props.projectName!,
        baseBranch.value,
        targetBranch.value
      );
      if (success) {
        notifySuccess("Branches fusionnées avec succès");
        await router.push({
          name: "Commit-Default",
          params: {
            userName: props.userName!,
            projectName: props.projectName!,
            branchName: targetBranch.value,
          },
        });
      } else notifyWarning("Erreur lors de la fusion des branches");
    }

    watch([baseBranch, targetBranch], compareBranches);

    return {
      baseBranch,
      targetBranch,
      state,
      differences,
      merge,
    };
  },
});
</script>

<style lang="scss" scoped>
.chevron {
  transform: translate(0, -10px);
}
</style>
