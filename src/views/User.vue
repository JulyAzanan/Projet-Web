<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container">
      <div uk-grid class="uk-margin-medium-bottom">
        <div class="uk-width-1-3@s uk-margin-large-top" uk-first-column>
          <div v-if="ready">
            <h2>Profil de {{ userName }}</h2>
            <UserPicture :user="user" :size="12" />
            <div>
              <button class="uk-button uk-button-text buttonNormalText">
                <span class="uk-margin-small-right" uk-icon="icon: star"></span>
                {{ user.followers }} abonnés
              </button>
            </div>
            <div v-if="user.email">
              <span class="uk-margin-small-right" uk-icon="icon: mail"> </span>
              {{ user.email }}
            </div>
            <div v-if="user.age">
              <span
                class="uk-margin-small-right"
                uk-icon="icon: calendar"
              ></span>
              {{ user.age }} ans
            </div>
            <hr class="uk-divider-icon uk-margin-large-right" />
            <p>{{ user.bio }}</p>
          </div>
          <div v-else uk-spinner></div>
        </div>
        <div
          class="uk-width-2-3@s uk-container uk-margin-small-top uk-position-relative"
          uk-grid
        >
          <h2>Projets récents</h2>
          <div v-if="projectsLoaded">
            <div
              class="uk-grid-column-small uk-grid-row-small uk-child-width-1-3@s uk-text-center uk-margin-medium-bottom"
              uk-grid
            >
              <ProjectCard
                v-for="project in user.projects"
                :key="project.name"
                :isPrivate="project.private"
                :projectName="project.name"
                :updatedAt="project.updatedAt"
                :userName="userName"
              />
            </div>
            <Pagination :page="parseInt(page)" :pages="pages" />
          </div>
          <div v-else uk-spinner></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, watch } from "vue";
import * as User from "@/api/user";
import * as Project from "@/api/project";
import ProjectCard from "@/components/User/ProjectCard.vue";
import Pagination from "@/components/Pagination.vue";
import UserPicture from "@/components/User/UserPicture.vue";
import router, { notFound } from "@/app/routes";

export default defineComponent({
  props: {
    userName: String,
    page: String,
  },
  components: {
    ProjectCard,
    Pagination,
    UserPicture,
  },
  setup(props) {
    const ready = ref(false);
    const projectsLoaded = ref(false);
    const user = ref<User.FetchResult>({
      name: "",
      followers: 0,
      projectCount: 0,
      projects: [],
    });
    const pages = ref(0);

    watch(
      () => props.page,
      async () => {
        window.scrollTo(0, 0);
        projectsLoaded.value = false;
        const page = parseInt(props.page!);
        const result = await Project.allOf(
          props.userName,
          User.projectPerPage,
          User.projectPerPage * page
        );
        if (result.length === 0 && page != 1) {
          router.replace({ query: { page: "1" } });
        } else {
          user.value.projects = result;
        }
        projectsLoaded.value = true;
      }
    );

    async function init() {
      const page = parseInt(props.page!);
      const result = await User.fetch(props.userName!, page);
      if (result === null) return notFound();
      user.value = result;
      if (result.projects.length === 0 && page != 1) {
        router.replace({ query: { page: "1" } });
      }
      pages.value = Math.ceil(result.projectCount / User.projectPerPage);
      projectsLoaded.value = true;
      ready.value = true;
    }

    init();
    return { ready, user, projectsLoaded, pages };
  },
});
</script>

<style lang="scss" scoped>
.buttonNormalText {
  text-transform: none;
}

.cardHolder {
  display: flex;
}

.userInfo {
  margin-top: 10em;
}

img {
  &.rounded {
    object-fit: cover;
    border-radius: 50%;
    height: 200px;
    width: 200px;
  }
}
</style>