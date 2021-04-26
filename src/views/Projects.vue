<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container uk-margin-medium-bottom">
      <h2>Liste des projets</h2>
      <div uk-grid class="uk-display-block uk-inline">
        <div>
          <div class="uk-position-top-center">
            <div class="uk-inline">
              <button class="uk-form-icon" uk-icon="icon: search"></button>
              <input
                class="uk-input uk-form-width-large"
                type="text"
                placeholder="Rechercher un projet..."
              />
            </div>
          </div>
          <div class="uk-container uk-margin-large-top uk-inline" uk-grid>
            <div v-if="ready">
              <div
                class="uk-grid-column-small uk-child-width-1-4@s uk-text-center uk-margin-medium-bottom"
                uk-grid
              >
                <ProjectCard
                  v-for="project in projects"
                  :key="project.name"
                  :isPrivate="project.private"
                  :projectName="project.name"
                  :userName="project.author"
                  :updatedAt="project.updatedAt"
                  showAuthor
                />
              </div>
              <Pagination :page="parseInt(page)" :pages="pages" />
            </div>
            <div v-else uk-spinner></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, watch } from "vue";
import ProjectCard from "@/components/User/ProjectCard.vue";
import Pagination from "@/components/Pagination.vue";
import * as Project from "@/api/project";
import router from "@/app/routes";

export default defineComponent({
  props: {
    page: String,
  },
  components: {
    ProjectCard,
    Pagination,
  },
  setup(props) {
    const ready = ref(false);
    const projects = ref<Project.BaseResult[]>([]);
    const pages = ref(0);

    watch(() => props.page, load);

    async function load() {
      ready.value = false;
      const page = parseInt(props.page!);
      const result = await Project.all(page);
      projects.value = result;
      if (result.length === 0 && page != 1) {
        router.replace({ query: { page: "1" } });
      }
      ready.value = true;
    }

    async function init() {
      const count = await Project.count();
      pages.value = Math.ceil(count / Project.perPage);
    }

    init().then(load);
    return { ready, projects, pages };
  },
});
</script>

<style lang="scss" scoped>
</style>