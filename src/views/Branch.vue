<template>
  <div>
    <div class="uk-container">
      <div class="uk-grid-divider" uk-grid>
        <div class="uk-width-2-3@s">
          <div class="uk-child-width-auto" uk-grid>
            <div class="uk-width-expand">
              <select
                class="uk-select uk-form-width-small uk-margin-small-right"
                v-model="selectedBranch"
              >
                <option disabled value="">Branche:</option>
                <option
                  v-for="name in project.branches"
                  :key="name"
                  :value="name"
                >
                  {{ name }}
                </option>
              </select>
              <span>
                <span class="uk-margin-small-right" uk-icon="icon: git-branch">
                </span>
                <strong>{{ project.branches.length }}</strong> branches
              </span>
              <span class="uk-margin-small-left" v-if="page.ready">
                <span class="uk-margin-small-right" uk-icon="icon: history">
                </span>
                <strong> {{ branch.commitsCount }}</strong> modifications
              </span>
              <span v-else uk-spinner></span>
            </div>
            <div>
              <button class="uk-button uk-button-primary">
                Télécharger
                <span
                  class="uk-margin-small-left"
                  uk-icon="icon: download"
                ></span>
              </button>
            </div>
          </div>
          <div class="uk-margin" v-if="page.ready">
            <div v-if="page.noCommitAvailable">
              <div class="uk-alert-warning uk-alert" uk-alert="">
                <strong>
                  Il n'y a pas encore d'ajouts sur cette branche!
                </strong>
              </div>
            </div>
            <router-view v-else :branch="branch"></router-view>
          </div>
          <div v-else uk-spinner></div>
        </div>
        <div class="uk-width-1-3@s">
          <cite> Créé le {{ project.createdAt.toLocaleString() }} </cite><br />
          <cite> Mis à jour le {{ project.updatedAt.toLocaleString() }} </cite>
          <h4>
            <span class="uk-margin-small-right" uk-icon="icon: info"></span>
            À propos
          </h4>
          <p>
            {{ project.description }}
          </p>

          <hr class="uk-divider-small" />

          <h4>
            <span class="uk-margin-small-right" uk-icon="icon: users"></span>
            Contributeurs
          </h4>
          <ul class="uk-grid-small uk-flex-middle" uk-grid>
            <li v-for="name in project.contributors" :key="name">
              <router-link :to="{ name: 'User', params: { userName: name } }">
                <img
                  :src="`https://picsum.photos/seed/${name}/200/300`"
                  :alt="name"
                  :uk-tooltip="`title: ${name}; pos: bottom`"
                  class="rounded"
                />
              </router-link>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <br />
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, watch, reactive } from "vue";
import { onBeforeRouteUpdate } from "vue-router";
import * as Project from "@/api/project";
import * as Branch from "@/api/branch";
import router, { notFound } from "@/routes";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
    branchName: String,
    project: Object as () => Project.FetchResult,
  },
  setup(props) {
    const page = reactive({
      ready: false,
      noCommitAvailable: false,
    });
    const selectedBranch = ref(props.branchName);
    const branch = ref<Branch.FetchResult>({
      lastCommit: null,
      commitsCount: 0,
      updatedAt: new Date(),
      createdAt: new Date(),
    });

    watch(selectedBranch, async () => {
      await router.replace({
        name: "Commit-default",
        params: {
          userName: props.userName!,
          projectName: props.projectName!,
          branchName: selectedBranch.value!,
        },
      });
    });

    async function init() {
      const result = await Branch.fetch(
        props.userName,
        props.projectName,
        props.branchName
      );
      if (result === null) return notFound();
      branch.value = result;
      if (router.currentRoute.value.name === "Commit-default") {
        if (branch.value.lastCommit) {
          await router.replace({
            name: "Files",
            params: {
              userName: props.userName!,
              projectName: props.projectName!,
              branchName: props.branchName!,
              commitID: branch.value.lastCommit,
            },
          });
        } else {
          page.noCommitAvailable = true;
        }
      }
      page.ready = true;
    }

    onBeforeRouteUpdate((to) => {
      if (to.name === "Branch" || to.name === "Commit-default") {
        page.ready = false;
        init();
      }
    });

    init();
    return { page, branch, selectedBranch };
  },
});
</script>

<style lang="scss" scoped>
img {
  &.rounded {
    object-fit: cover;
    border-radius: 50%;
    height: 50px;
    width: 50px;
  }
}
</style>
