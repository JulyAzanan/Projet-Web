<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container uk-margin-large-bottom">
      <div class="uk-container uk-container-xsmall">
        <h3>Liste des contributeurs du projet</h3>
        <ul class="uk-grid-small uk-flex-middle" uk-grid>
          <li v-for="user in project.contributors" :key="user.name">
            <a
              class="uk-inline-clip uk-transition-toggle uk-light"
              :uk-tooltip="`title: Retirer ${user.name}; pos: bottom`"
              @click="removeContributor(user.name)"
            >
              <UserPicture :user="user" :size="3" />
              <div class="uk-position-center">
                <span
                  class="uk-transition-fade"
                  uk-icon="icon: close; ratio: 3"
                ></span>
              </div>
            </a>
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
                  v-model="contributorQuery"
                  @input="searchContributor"
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
                    v-for="user in contributors"
                    v-on:click="addContributor(user)"
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
import * as Contributor from "@/api/contributor";
import router from "@/app/routes";
import { notifySuccess, notifyWarning } from "@/utils/notification";
import debounce from "@/utils/debounce";

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
    const contributors = ref<User.AllResult[]>([]);
    const pages = ref(0);
    const contributorQuery = ref("");

    function removeContributor(contributor: string) {
      const success = Contributor.remove(
        props.userName!,
        props.projectName!,
        contributor
      );
      if (success) {
        for (let i = 0; i < props.project!.contributors.length; i++) {
          if (props.project!.contributors[i].name === contributor) {
            props.project!.contributors.splice(i, 1);
          }
        }
        notifySuccess("Contributeur retiré");
      } else notifyWarning("Erreur lors du retrait du contributeur");
    }

    async function addContributor(contributor: User.BaseResult) {
      const success = Contributor.add(
        props.userName!,
        props.projectName!,
        contributor.name,
      );
      if (success) {
        props.project!.contributors.push(contributor);
        notifySuccess("Contributeur ajouté");
      } else notifyWarning("Erreur lors de l'ajout du contributeur");
    }

    watch(() => props.page, search);

    async function search() {
      ready.value = false;
      const page = parseInt(props.page!);
      const result = await User.search(contributorQuery.value, page);
      contributors.value = result.results;
      pages.value = Math.ceil(result.count / User.perPage);
      if (result.results.length === 0 && page != 1) {
        router.replace({ query: { page: "1" } });
      }
      ready.value = true;
    }

    const searchContributor = debounce(search, 500);

    async function init() {
      const count = await User.count();
      pages.value = Math.ceil(count / User.perPage);
    }

    init().then(search);
    return {
      ready,
      contributors,
      pages,
      removeContributor,
      contributorQuery,
      searchContributor,
      addContributor,
    };
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