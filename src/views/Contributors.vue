<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container uk-margin-large-bottom">
      <div class="uk-container uk-container-xsmall">
        <h3>Liste des contributeurs du projet</h3>
        <ul class="uk-grid-small uk-flex-middle" uk-grid>
            <li v-for="user in project.contributors" :key="user.name">
              <button
              class="b1"
                :uk-tooltip="`title: Retirer ${user.name}; pos: bottom`"
              >
                <UserPicture :user="user" :size="3"
                />
              </button>
            </li>
          </ul>
        <hr class="uk-tab" />
        <h3>Ajouter des contributeurs</h3>
      <div uk-grid class="uk-display-block uk-inline">
        <div>
          <div class="uk-position-top-center">
            <div class="uk-inline">
              <button class="uk-form-icon" uk-icon="icon: search"></button>
              <input
                class="uk-input uk-form-width-large"
                type="text"
                placeholder="Rechercher un utilisateur..."
              />
            </div>
          </div>
          <div class="uk-container uk-margin-large-top uk-inline" uk-grid>
            <div v-if="ready">
              <div
                class="uk-grid uk-grid-small uk-child-width-1-3@s uk-text-center uk-margin-medium-bottom"
                uk-grid
              >
                <ContributorCard
                  v-for="user in users"
                  :key="user.name"
                  :user="user"
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
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, watch } from "vue";
import * as Project from "@/api/project";
import UserPicture from "@/components/User/UserPicture.vue";
import ContributorCard from "@/components/User/ContributorCard.vue";
import Pagination from "@/components/Pagination.vue";
import * as User from "@/api/user";
import router from "@/app/routes";

export default defineComponent({
    props: {
    userName: String,
    projectName: String,
    branchName: String,
    project: Object as () => Project.FetchResult,
    page: String,
  },
  components: {
    UserPicture,
    ContributorCard,
    Pagination,
  },
  setup(props) {
    const ready = ref(false);
    const users = ref<User.AllResult[]>([]);
    const pages = ref(0);

    watch(() => props.page, load);

    async function load() {
      ready.value = false;
      const page = parseInt(props.page!);
      const result = await User.all(page);
      users.value = result;
      if (result.length === 0 && page != 1) {
        router.replace({ query: { page: "1" } });
      }
      ready.value = true;
    }

    async function init() {
      const count = await User.count();
      pages.value = Math.ceil(count / User.perPage);
    }

    init().then(load);
    return { ready, users, pages };
  },
});
</script>

<style lang="scss" scoped>
.b1 {
    border: none;
    background: none;
    cursor: pointer;
    margin: 0;
    padding: 0;
  }
</style>