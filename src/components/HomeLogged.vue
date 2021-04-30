<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container uk-margin-large-bottom">
      <div
        class="uk-margin-small uk-grid-small uk-child-width-1-2 uk-grid"
        uk-grid
      >
        <div class="uk-first-column">
          <h1 class="uk-text-italic">"Work Better, Compose Together"</h1>
          <p class="uk-text-lead">Bienvenue {{ userName }} !</p>
        </div>
        <div>
          <img src="@/assets/logo_simple.png" width="110" alt="" />
          <img src="@/assets/logo_text.png" width="250" alt="" />
        </div>
      </div>
      <div class="uk-grid-small uk-child-width-1-2 uk-grid" uk-grid>
        <div class="uk-first-column">
          <router-link :to="{ name: 'NewProject' }">
            <p class="uk-text uk-button uk-button-default">
              Créer un nouveau projet
            </p>
          </router-link>
        </div>
        <div class="uk-width-expand">
          <router-link :to="{ name: 'Projects' }">
            <p
              class="uk-margin-xlarge-left uk-text uk-button uk-button-default"
            >
              Rechercher un projet
            </p>
          </router-link>
        </div>
      </div>
      <hr class="uk-tab" />
      <div>
        <!-- Insérer des projets qui contiennent une lettre prise aléatoirement -->
        <h3>Projets pouvant vous intéresser</h3>
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
                class="uk-width-auto"
              />
            </div>
          </div>
          <div v-else uk-spinner></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, computed, ref } from "vue";
import store from "@/app/store";
import ProjectCard from "@/components/User/ProjectCard.vue";
import * as Project from "@/api/project";
import router from "@/app/routes";

export default defineComponent({
  components: {
    ProjectCard,
  },
  setup() {
    const ready = ref(false);
    const projects = ref<Project.BaseResult[]>([]);

    async function load() {
      ready.value = false;
      const page = 1;
      const result = await Project.all(page);
      projects.value = result;
      if (result.length === 0 && page != 1) {
        router.replace({ query: { page: "1" } });
      }
      ready.value = true;
    }

    load();
    return {
      logged: computed(() => store.state.loggedIn),
      userName: computed(() => store.state.user),
      ready,
      projects,
    };
  },
});
</script>
