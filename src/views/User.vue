<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container">
      <div uk-grid class="uk-margin-medium-bottom">
        <div class="uk-width-1-3@s uk-margin-large-top" uk-first-column>
          <div v-if="ready">
            <div class="uk-text-center uk-margin-small-right">
              <UserPicture :user="user" :size="15" />
              <h2>
                {{ userName }}
              </h2>
              <button
                v-if="following"
                class="uk-button uk-button-danger"
                @click="unfollow"
              >
                <span
                  class="uk-margin-small-right"
                  uk-icon="icon: heart"
                  :disabled="store.state.loggedIn"
                ></span>
                Se désabonner
              </button>
              <button v-else class="uk-button uk-button-danger" @click="follow">
                <span
                  class="uk-margin-small-right"
                  uk-icon="icon: heart"
                  :disabled="store.state.loggedIn"
                ></span>
                S'abonner
              </button>
              <button
                class="uk-button uk-button-default uk-margin-small-left"
                @click="support"
              >
                Soutenir
              </button>
            </div>
            <hr class="uk-divider-icon uk-margin-small-right" />
            <div>
              <span class="uk-margin-small-right" uk-icon="icon: star"></span>
              {{ user.followers }} abonnés
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
</template>

<script lang="ts">
import { defineComponent, ref, watch } from "vue";
import supportEvent from "canvas-confetti";
import * as User from "@/api/user";
import * as Project from "@/api/project";
import * as Friend from "@/api/friend";
import ProjectCard from "@/components/User/ProjectCard.vue";
import Pagination from "@/components/Pagination.vue";
import UserPicture from "@/components/User/UserPicture.vue";
import router, { notFound } from "@/app/routes";
import store from "@/app/store";

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
    const following = ref(false);

    watch(
      () => props.page,
      async () => {
        window.scrollTo(0, 0);
        projectsLoaded.value = false;
        const page = parseInt(props.page!);
        const result = await Project.allOf(props.userName!, page);
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
      const result = await User.fetch(props.userName, page);
      if (result === null) return notFound();
      user.value = result;
      if (result.projects.length === 0 && page != 1) {
        router.replace({ query: { page: "1" } });
      }
      pages.value = Math.ceil(result.projectCount / Project.perPage);
      if (store.state.loggedIn) {
        following.value = await Friend.isFriend(
          store.state.user,
          user.value.name
        );
      }
      projectsLoaded.value = true;
      ready.value = true;
    }

    async function follow() {
      await Friend.add(store.state.user, user.value.name);
      following.value = true;
    }

    async function unfollow() {
      await Friend.remove(store.state.user, user.value.name);
      following.value = false;
    }

    function support() {
      supportEvent({
        particleCount: 200,
        spread: 100,
        origin: { y: 0.7 },
      });
    }

    init();
    return {
      ready,
      user,
      projectsLoaded,
      pages,
      follow,
      unfollow,
      store,
      following,
      support,
    };
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