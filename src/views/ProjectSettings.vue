<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container">
      <div uk-grid class="uk-margin-medium-bottom">
        <div>
          <h2 class="uk-text-center">Paramètres du projet</h2>
          <form class="uk-form-horizontal">
            <div class="uk-margin">
              <label class="uk-form-label" for="form-h-text"
                >Branche principale</label
              >
              <div class="uk-form-controls">
                <select class="uk-select" v-model="mainBranch">
                  <option disabled value="">Branche:</option>
                  <option
                    v-for="branch in project.branches"
                    :key="branch.name"
                    :value="branch.name"
                  >
                    {{ branch.name }}
                  </option>
                </select>
              </div>
            </div>

            <div class="uk-margin">
              <label class="uk-form-label" for="form-h-textarea"
                >Description</label
              >
              <div class="uk-form-controls">
                <textarea class="uk-input" type="text" v-model="description">
                </textarea>
              </div>
            </div>

            <div class="uk-margin">
              <label class="uk-form-label" for="form-h-text"
                >Projet privé</label
              >
              <div class="uk-margin uk-grid-small uk-grid" uk-grid>
                <div class="uk-form-controls">
                  <label>
                    <input
                      class="uk-radio"
                      type="radio"
                      name="privateornot"
                      v-model="isPrivate"
                      :value="true"
                    />
                    Oui
                  </label>
                </div>
                <div class="uk-form-controls">
                  <label>
                    <input
                      class="uk-radio"
                      type="radio"
                      name="privateornot"
                      v-model="isPrivate"
                      :value="false"
                    />
                    Non
                  </label>
                </div>
              </div>

              <div class="uk-margin">
                <div class="uk-form-controls">
                  <button
                    type="button"
                    class="uk-button uk-button-primary uk-width-1-1"
                    @click="updateProject"
                  >
                    Mettre à jour
                  </button>
                </div>
              </div>

              <div class="uk-margin">
                <div class="uk-form-controls">
                  <a
                    class="uk-button uk-button-danger uk-width-1-1"
                    href="#delete-project"
                    uk-toggle
                    >Supprimer le projet</a
                  >
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div id="delete-project" uk-modal>
      <div class="uk-modal-dialog uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <h2 class="uk-modal-title">Attention</h2>
        <p>
          Vous êtes sur le point de supprimer ce projet. Cette action est
          irréversible, êtes vous sûr de vouloir poursuivre ?
        </p>
        <p class="uk-text-right">
          <button
            class="uk-button uk-button-default uk-modal-close uk-margin-small-right"
            type="button"
          >
            Annuler
          </button>
          <button
            class="uk-button uk-button-danger uk-modal-close"
            type="button"
            @click="deleteProject"
          >
            Supprimer le projet
          </button>
        </p>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from "vue";
import * as Project from "@/api/project";
import router from "@/app/routes";
import { notifySuccess, notifyWarning } from "@/utils/notification";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
    project: Object as () => Project.FetchResult,
  },
  setup(props) {
    const description = ref(props.project?.description);
    const isPrivate = ref(props.project?.private);
    const mainBranch = ref(props.project?.mainBranch);

    async function deleteProject() {
      await Project.remove(props.userName!, props.projectName!);
      await router.push({ name: "Home" });
    }

    async function updateProject() {
      const success = Project.edit(props.userName!, props.projectName!, {
        private: isPrivate.value ?? false,
        description: description.value,
        mainBranch: mainBranch.value ?? null,
      });
      const project = props.project;
      if (project) {
        project.private = isPrivate.value ?? false;
      }
      if (success) notifySuccess("Projet mis à jour");
      else notifyWarning("Erreur lors de la mise à jour du projet");
    }

    return { description, isPrivate, mainBranch, deleteProject, updateProject };
  },
});
</script>
