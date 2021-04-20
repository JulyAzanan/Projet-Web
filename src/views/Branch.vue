<template>
  <div>
    <div class="uk-container">
      <div class="uk-grid-divider" uk-grid>
        <div class="uk-width-2-3@s">
          <div v-if="ready">
            <div class="uk-child-width-auto" uk-grid>
              <div class="uk-width-expand">
                <select
                  class="uk-select uk-form-width-small uk-margin-small-right"
                  v-model="selectedBranch"
                >
                  <option disabled value="">Branche:</option>
                  <option v-for="name in branches" :key="name" :value="name">
                    {{ name }}
                  </option>
                </select>
                <span>
                  <span
                    class="uk-margin-small-right"
                    uk-icon="icon: git-branch"
                  >
                  </span>
                  <strong>{{ branches.length }}</strong> branches
                </span>
                <span class="uk-margin-small-left">
                  <span class="uk-margin-small-right" uk-icon="icon: history">
                  </span>
                  <strong>56</strong> modifications
                </span>
              </div>
              <div>
                <button class="uk-button uk-button-primary">
                  Télécharger<span
                    class="uk-margin-small-left"
                    uk-icon="icon: download"
                  ></span>
                </button>
              </div>
            </div>
            <div class="uk-margin">
              <router-view></router-view>
            </div>
          </div>
          <div v-else uk-spinner></div>
        </div>
        <div class="uk-width-1-3@s">
          <slot name="sidebar"></slot>
        </div>
      </div>
    </div>
    <br />
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, watch } from "vue";
import { onBeforeRouteUpdate } from "vue-router";
import * as Project from "@/api/project";
import * as Branch from "@/api/branch";
import router, { notFound } from "@/routes";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
    branchName: String,
    branches: Array as () => string[],
    mainBranch: String,
  },
  setup(props) {
    const ready = ref(false);
    const selectedBranch = ref(props.branchName);
    const branch = ref<Branch.FindResult>({
      lastCommit: "",
      updatedAt: new Date(),
      createdAt: new Date(),
    });

    watch(selectedBranch, async () => {
      await router.replace({
        name: "Commit-default",
        params: {
          userName: props.userName!,
          projectName: props.projectName!,
          branchName: selectedBranch.value as string,
        },
      });
    });

    watch(
      () => props.branchName,
      (value) => {
        selectedBranch.value = value;
      }
    );

    async function init() {
      if (props.branchName == null) {
        let branchName: string;
        if (props.mainBranch == null) {
          const result = await Project.find(props.userName, props.projectName);
          if (result === null) return notFound();
          branchName = result.mainBranch;
        } else {
          branchName = props.mainBranch!;
        }
        await router.replace({
          name: "Commit-default",
          params: {
            userName: props.userName!,
            projectName: props.projectName!,
            branchName,
          },
        });
      } else {
        const result = await Branch.find(
          props.userName,
          props.projectName,
          props.branchName
        );
        if (result === null) return notFound();
        branch.value = result;
      }
      ready.value = true;
    }

    onBeforeRouteUpdate((to) => {
      if (to.name === "Commit-default") {
        ready.value = false;
        init();
      }
    });

    init();
    return { ready, selectedBranch };
  },
});
</script>
