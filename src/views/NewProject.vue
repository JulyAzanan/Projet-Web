<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container uk-margin-large-bottom">
      <h2 class="uk-text-center">Créer un nouveau projet</h2>
      <p class="uk-text-center">
        Un projet contient pour chacune de ses branches, l'historique de tous
        les ajouts, modifications et suppressions de chaque partitions.
      </p>
      <hr class="uk-tab" />
      <form class="uk-display-block uk-form-stacked">
        <div class="uk-container uk-container-xsmall">
          <div
            class="uk-margin-small uk-grid-small uk-child-width-1-2 uk-grid"
            uk-grid
          >
            <div class="uk-first-column">
              <div class="uk-margin">
                <label class="uk-form-label">Auteur*</label>
                <input
                  class="uk-input"
                  type="text"
                  v-model="userName"
                  disabled
                  required
                />
              </div>
            </div>
            <div
              class="uk-grid-small uk-child-width-1-2 uk-grid uk-width-expand"
            >
              <div class="uk-first-column uk-margin-small-top uk-width-auto">
                <p class="uk-h2 uk-margin-small-top">/</p>
              </div>
              <div class="uk-width-expand">
                <label class="uk-form-label">Projet*</label>
                <input
                  class="uk-input"
                  type="text"
                  v-model="project"
                  @input="checkProject"
                  :class="{ 'uk-form-danger': invalidProject }"
                  required
                />
              </div>
            </div>
          </div>
          <div>
            <label class="uk-form-label">Description (Optionnel)</label>
            <input class="uk-input" type="text" v-model="description" />
          </div>
          <hr />
          <div>
            <input
              class="uk-radio"
              type="radio"
              name="public/private"
              v-model="isPrivate"
              :value="false"
            />
            <span class="uk-icon" uk-icon="ratio:2; icon: users"> </span>
            <label class="uk-text-bold">Public : </label>
            <label>
              Tout le monde peut voir le projet et en télécharger le contenu
            </label>
          </div>
          <div>
            <input
              class="uk-radio"
              type="radio"
              name="public/private"
              v-model="isPrivate"
              :value="true"
            />
            <span class="uk-icon" uk-icon="ratio:2; icon: lock"> </span>
            <label class="uk-text-bold">Privé : </label>
            <label>
              Seuls vous et les contributeurs pouvez voir le projet et en
              télécharger le contenu
            </label>
          </div>
          <hr />
          <p>
            Le projet sera initialisé avec une branche principale "main". Il
            sera possible de la changer à tout moment.
          </p>
          <div class="uk-width-auto">
            <button
              type="submit"
              class="uk-button uk-button-primary"
              :disabled="invalidProject"
              @click="createProject"
            >
              Créer le projet
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, watch, ref } from "vue";
import store from "@/app/store";
import router from "@/app/routes";
import debounce from "@/utils/debounce";
import { notifyWarning } from "@/utils/notification";
import * as Project from "@/api/project";

export default defineComponent({
  beforeRouteEnter(to, _from, next) {
    if (store.state.loggedIn) next();
    else next({ name: "Login", query: { redirect: to.fullPath } });
  },
  setup() {
    const userName = ref(store.state.user);
    const project = ref("");
    const description = ref("");
    const isPrivate = ref(false);
    const invalidProject = ref(false);

    watch(
      () => store.state.loggedIn,
      (loggedIn) => loggedIn || router.replace({ name: "Login" })
    );

    async function createProject(event: Event) {
      if (userName.value === "" || project.value === "") return;
      event.preventDefault();
      const success = await Project.add(
        userName.value,
        project.value,
        isPrivate.value,
        description.value
      );
      if (success) {
        return router.push({
          name: "Branch-default",
          params: { userName: userName.value, projectName: project.value },
        });
      }
      project.value = "";
      notifyWarning("Nom de projet indisponible.");
    }

    async function projectExists() {
      invalidProject.value = await Project.find(userName.value, project.value);
    }

    const checkProject = debounce(projectExists, 500);

    return {
      userName,
      project,
      description,
      isPrivate,
      checkProject,
      invalidProject,
      createProject,
    };
  },
});
</script>

<style lang="scss" scoped>
</style>