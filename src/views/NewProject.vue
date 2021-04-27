<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container uk-margin-large-bottom">
      <h2 class="uk-text-center">Créer un nouveau projet</h2>
      <p class="uk-text-center">
        Un projet contient pour chacune de ses branches, l'historique de tous
        les ajouts, modifications et suppressions de chaque partitions.
      </p>
      <hr class="uk-tab" />
      <form uk-grid class="uk-display-block uk-inline uk-form-stacked">
        <div>
          <div class="uk-position-top-center">
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
                    :value="userName"
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
                  <input class="uk-input" type="text" required />
                </div>
              </div>
            </div>
            <div>
              <label class="uk-form-label">Description (Optionnel)</label>
              <input class="uk-input" type="text" />
            </div>
            <hr />
            <div>
              <input
                class="uk-radio"
                type="radio"
                name="public/private"
                checked
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
              />
              <span class="uk-icon" uk-icon="ratio:2; icon: lock"> </span>
              <label class="uk-text-bold">Privé : </label>
              <label>
                Seuls vous et les contributeurs pouvez voir le projet et en télécharger le contenu
              </label>
            </div>
            <hr />
            <p>Le projet sera initialisé avec une branche principale "main". Il sera possible de la changer à tout moment.</p>
            <input class="uk-button uk-button-primary" type="submit" value="Créer le projet"/>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, computed } from "vue";
import store from "@/app/store";

export default defineComponent({
  setup() {
    return {
      logged: computed(() => store.state.loggedIn),
      userName: computed(() => store.state.user),
    };
  },
});
</script>

<style lang="scss" scoped>
</style>