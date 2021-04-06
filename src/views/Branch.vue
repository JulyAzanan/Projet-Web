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
                >
                  <option>{{ branch }}</option>
                  <option>dev</option>
                </select>
                <span>
                  <span
                    class="uk-margin-small-right"
                    uk-icon="icon: git-branch"
                  >
                  </span>
                  <strong>2</strong> branches
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
          <div v-if="ready">
            <slot name="sidebar"></slot>
          </div>
          <div v-else uk-spinner></div>
        </div>
      </div>
    </div>
    <br />
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from "vue";
import * as Project from "@/api/project";
import * as Branch from "@/api/branch";
import router, { notFound } from "@/routes";

export default defineComponent({
  props: {
    username: String,
    project: String,
    branch: String,
    mainBranch: String,
  },
  setup(props) {
    const ready = ref(false);

    async function init() {
      if (props.branch == null) {
        let branch: string;
        if (props.mainBranch == null) {
          const project = await Project.find(props.username, props.project);
          if (project === null) return notFound();
          branch = project.mainBranch;
        } else {
          branch = props.mainBranch!;
        }
        await router.replace({
          name: "Commit-default",
          params: {
            username: props.username!,
            project: props.project!,
            branch,
          },
        });
      } else {
        const branch = await Branch.find(
          props.username,
          props.project,
          props.branch
        );
        if (branch === null) return notFound();
      }
      ready.value = true;
    }

    init();
    return { ready };
  },
});
</script>
