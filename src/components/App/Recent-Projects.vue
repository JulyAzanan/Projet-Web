<template>
  <li v-for="project in projects" :key="project.name">
    <router-link
      :to="{
        name: 'Branch-default',
        params: { userName, projectName: project.name },
      }"
      uk-margin
    >
      <span
        v-if="project.private"
        class="uk-margin-small-right"
        uk-icon="icon: lock"
      ></span>
      <span v-else class="uk-margin-small-right" uk-icon="icon: folder"></span>
      {{ project.name }}
    </router-link>
  </li>
  <li v-if="projects.length === 0">Pas encore de projets !</li>
</template>

<script lang="ts">
import { computed, defineComponent, ref } from "vue";
import * as Project from "@/api/project";
import store from "@/app/store";

export default defineComponent({
  setup() {
    const projects = ref<Project.BaseResult[]>([]);

    async function init() {
      const result = await Project.allOf(store.state.user, 1);
      projects.value = result;
    }

    store.subscribe((mutation) => {
      if (mutation.type === "updateProjects") init();
    });

    init();
    return {
      projects,
      userName: computed(() => store.state.user),
    };
  },
});
</script>
