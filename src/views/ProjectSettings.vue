<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container">
      <div uk-grid class="uk-margin-medium-bottom">
        <div>
          <div v-if="ready">
            <h2 class="uk-text-center">Paramètres du projet</h2>
            <form class="uk-form-horizontal">
              <div class="uk-margin">
                <label class="uk-form-label" for="form-h-text"
                  >Branche principale</label
                >
                <div class="uk-form-controls">
                  <!-- v-model="description" -->
                  <input
                    class="uk-input uk-form-width-large"
                    id="form-h-text"
                    type="text"
                    autofocus
                  />
                </div>
              </div>

              <div class="uk-margin">
                <label class="uk-form-label" for="form-h-textarea"
                  >Description</label
                >
                <div class="uk-form-controls">
                  <!-- v-model="description" -->
                  <textarea
                    class="uk-input uk-form-width-large"
                    id="form-h-textarea"
                    type="text"
                  >
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
                        :checked="isPrivate"
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
                        :checked="!isPrivate"
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
                    >
                      Mettre à jour
                    </button>
                  </div>
                </div>

                <div class="uk-margin">
                  <div class="uk-form-controls">
                    <a
                      class="uk-button uk-button-danger uk-width-1-1"
                      href="#delete-account"
                      uk-toggle
                      >Supprimer le projet</a
                    >
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div v-else uk-spinner></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    description: String,
    isPrivate: Boolean,
    mainBranch: String,
  },
  setup() {
    const ready = true;
    return { ready };
  },
});
</script>
