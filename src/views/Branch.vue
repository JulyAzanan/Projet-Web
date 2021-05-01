<template>
  <div>
    <div class="uk-container uk-container-large">
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
                  v-for="branch in project.branches"
                  :key="branch.name"
                  :value="branch.name"
                >
                  {{ branch.name }}
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
            <div class="uk-button-group">
              <a
                v-if="isContributor"
                class="uk-button uk-button-primary"
                href="#new-commit"
                uk-toggle
              >
                Commit
                <span class="uk-margin-small-left" uk-icon="icon: push"></span>
              </a>
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
          <cite> Créé le {{ project.createdAt }} </cite><br />
          <cite> Mis à jour le {{ project.updatedAt }} </cite>
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
            <li v-for="user in project.contributors" :key="user.name">
              <router-link
                :to="{ name: 'User', params: { userName: user.name } }"
                :uk-tooltip="`title: ${user.name}; pos: bottom`"
              >
                <UserPicture :user="user" :size="3" />
              </router-link>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <NewCommit
      v-if="isContributor"
      :userName="userName"
      :projectName="projectName"
      :branchName="branchName"
      v-on:refresh="init"
    />
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, watch, reactive, WatchStopHandle } from "vue";
import { onBeforeRouteUpdate } from "vue-router";
import * as Project from "@/api/project";
import * as Branch from "@/api/branch";
import { isContributor } from "@/utils/contributor";
import router, { notFound } from "@/app/routes";
import UserPicture from "@/components/User/UserPicture.vue";
import NewCommit from "@/components/Branch/NewCommit.vue";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
    branchName: String,
    project: Object as () => Project.FetchResult,
  },
  components: {
    UserPicture,
    NewCommit,
  },
  setup(props) {
    const page = reactive({
      ready: false,
      noCommitAvailable: false,
    });
    const selectedBranch = ref(props.branchName);
    const branch = ref<Branch.FetchResult>({
      name: "",
      commits: [],
      lastCommit: null,
      commitsCount: 0,
      updatedAt: "",
    });

    let stopBranchWatcher: WatchStopHandle = () => {};

    async function init(bName?: string, pName?: string) {
      const branchName = bName ?? props.branchName;
      const projectName = pName ?? props.projectName;
      const result = await Branch.fetch(
        props.userName!,
        projectName!,
        branchName
      );
      if (result === null) return notFound();
      branch.value = result;
      if (router.currentRoute.value.name === "Files") {
        page.noCommitAvailable = false;
      }
      if (router.currentRoute.value.name === "Commit-default") {
        if (branch.value.lastCommit) {
          await router.replace({
            name: "Files",
            params: {
              userName: props.userName!,
              projectName: projectName!,
              branchName: branchName!,
              commitID: branch.value.lastCommit,
            },
          });
          page.noCommitAvailable = false;
        } else {
          page.noCommitAvailable = true;
        }
      }
      stopBranchWatcher();
      selectedBranch.value = branchName;
      stopBranchWatcher = watch(selectedBranch, async () => {
        await router.replace({
          name: "Commit-default",
          params: {
            userName: props.userName!,
            projectName: projectName!,
            branchName: selectedBranch.value!,
          },
        });
      });
      page.ready = true;
    }

    onBeforeRouteUpdate((to) => {
      if (
        to.name === "Branch" ||
        to.name === "Commit-default" ||
        to.params.branchName !== props.branchName ||
        to.params.projectName !== props.projectName
      ) {
        page.ready = false;
        init(to.params.branchName as string, to.params.projectName as string);
      }
    });

    init();
    return {
      page,
      branch,
      selectedBranch,
      isContributor: isContributor(() => props.project),
      init,
    };
  },
});
</script>
