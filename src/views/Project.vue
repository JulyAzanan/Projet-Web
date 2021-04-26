<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container">
      <h3 v-if="page.valid">
        <router-link :to="{ name: 'User', params: { userName } }">
          {{ userName }}
        </router-link>
        /
        <router-link
          :to="{ name: 'Branch-default', params: { userName, projectName } }"
        >
          {{ projectName }}
        </router-link>
      </h3>
      <div v-else uk-spinner></div>
      <div>
        <ul class="uk-tab">
          <li>
            <router-link
              :to="{
                name: 'Branch-default',
                params: { userName, projectName },
              }"
              >Partitions</router-link
            >
          </li>
          <li>
            <router-link
              :to="{ name: 'Pulls', params: { userName, projectName } }"
              >Changements
            </router-link>
          </li>
        </ul>

        <router-view v-if="page.ready" :project="project"> </router-view>
        <div v-else uk-spinner></div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive } from "vue";
import { onBeforeRouteUpdate } from "vue-router";
import router, { notFound } from "@/app/routes";
import * as Project from "@/api/project";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
  },
  setup(props) {
    const page = reactive({
      ready: false,
      valid: false,
    });
    const project = ref<Project.FetchResult>({
      name: "",
      author: "",
      private: false,
      contributors: [],
      branches: [],
      mainBranch: "",
      description: "",
      updatedAt: new Date(),
      createdAt: new Date(),
    });

    async function init() {
      const result = await Project.fetch(props.userName!, props.projectName);
      if (result === null) return notFound();
      project.value = result;
      if (router.currentRoute.value.name === "Branch-default") {
        await router.replace({
          name: "Commit-default",
          params: {
            userName: props.userName!,
            projectName: props.projectName!,
            branchName: project.value.mainBranch,
          },
        });
      }
      page.valid = true;
      page.ready = true;
    }

    onBeforeRouteUpdate((to) => {
      if (to.name === "Project" || to.name === "Branch-default") {
        page.ready = false;
        init();
      }
    });

    init();
    return { page, project };
  },
});
</script>

<style lang="scss" scoped></style>
