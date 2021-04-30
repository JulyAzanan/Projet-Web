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
                v-model="projectQuery"
                @input="searchProject"
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
                  :userName="project.authorName"
                  :updatedAt="project.updatedAt"
                  showAuthor
                  class="uk-width-auto"
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
import debounce from "@/utils/debounce";

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
    const projectQuery = ref("");

    watch(() => props.page, search);

    async function search() {
      ready.value = false;
      const page = parseInt(props.page!);
      const result = await Project.search(projectQuery.value, page);
      projects.value = result.results;
      console.log(result)
      pages.value = Math.ceil(result.count / Project.perPage);
      if (result.results.length === 0 && page != 1) {
        router.replace({ query: { page: "1" } });
      }
      ready.value = true;
    }

    const searchProject = debounce(search, 500);

    async function init() {
      const count = await Project.count();
      pages.value = Math.ceil(count / Project.perPage);
    }

    init().then(search);
    return { ready, projects, pages, projectQuery, searchProject };
  },
});
</script>

<style lang="scss" scoped>
</style>