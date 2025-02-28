<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container uk-margin-medium-bottom">
      <h2>Liste des utilisateurs</h2>
      <div uk-grid class="uk-display-block uk-inline">
        <div>
          <div class="uk-position-top-center">
            <div class="uk-inline">
              <button class="uk-form-icon" uk-icon="icon: search"></button>
              <input
                v-model="userQuery"
                @input="searchUser"
                class="uk-input uk-form-width-large"
                type="text"
                placeholder="Rechercher un utilisateur..."
              />
            </div>
          </div>
          <div class="uk-container uk-margin-large-top uk-inline" uk-grid>
            <div v-if="ready">
              <div
                class="uk-grid-column-small uk-child-width-1-6@s uk-text-center uk-margin-medium-bottom"
                uk-grid
              >
                <UserCard
                  v-for="user in users"
                  :key="user.name"
                  :user="user"
                  class="uk-width-auto"
                />
              </div>
            </div>
            <div v-else uk-spinner></div>
          </div>
          <Pagination v-if="ready" :page="parseInt(page)" :pages="pages" />
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, watch } from "vue";
import UserCard from "@/components/User/UserCard.vue";
import Pagination from "@/components/Pagination.vue";
import * as User from "@/api/user";
import router from "@/app/routes";
import debounce from "@/utils/debounce";

export default defineComponent({
  props: {
    page: String,
  },
  components: {
    UserCard,
    Pagination,
  },
  setup(props) {
    const ready = ref(false);
    const users = ref<User.AllResult[]>([]);
    const pages = ref(0);
    const userQuery = ref("");

    watch(() => props.page, search);

    async function search() {
      ready.value = false;
      const page = parseInt(props.page!);
      const result = await User.search(userQuery.value, page);
      users.value = result.results;
      pages.value = Math.ceil(result.count / User.perPage);
      if (result.results.length === 0 && page != 1) {
        router.replace({ query: { page: "1" } });
      }
      ready.value = true;
    }

    const searchUser = debounce(search, 500);

    async function init() {
      const count = await User.count();
      pages.value = Math.ceil(count / User.perPage);
    }

    init().then(search);
    return { ready, users, pages, userQuery, searchUser };
  },
});
</script>

<style lang="scss" scoped>
</style>