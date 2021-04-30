<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container uk-margin-large-bottom">
      <div class="uk-container uk-container-xsmall">
        <div
          class="uk-margin-small uk-grid-small uk-child-width-1-2 uk-grid"
          uk-grid
        >
          <div class="uk-first-column">
            <h1 class="uk-text-italic">"Work Better, Compose Together"</h1>
            <p class="uk-text-lead">
              Rejoignez la plateforme permettant aux utilisateurs du monde
              entier de partager, et collaborer sur des partitions de musique.
            </p>
          </div>
          <div>
            <img src="@/assets/logo_simple.png" width="200" alt="" />
            <img src="@/assets/logo_text.png" width="400" alt="" />
          </div>
        </div>
        <router-link :to="{ name: 'Login' }">
          <p class="uk-margin-xlarge-left uk-text uk-button uk-button-default">
            Se connecter ou créer un compte
          </p>
        </router-link>
        <hr class="uk-tab" />
        <div
          class="uk-grid-small uk-child-width-1-3 uk-grid uk-flex-center"
          uk-grid
        >
          <div class="uk-first-column uk-child-width-1-2 uk-grid-small" uk-grid>
            <div class="uk-first-column uk-width-auto">
              <span class="uk-icon" uk-icon="ratio:2; icon: users"></span>
            </div>
            <div class="uk-width-expand">
              <p>
                <span v-if="count.user > 0">{{count.user}}</span>
                <span v-else>...</span>
                <br />
                <span class="uk-text-meta"> Compositeurs </span>
              </p>
            </div>
          </div>
          <div
            class="uk-child-width-1-2 uk-grid-small uk-margin-remove-top"
            uk-grid
          >
            <div class="uk-first-column uk-width-auto">
              <span class="uk-icon" uk-icon="ratio:2; icon: folder"></span>
            </div>
            <div class="uk-width-expand">
              <p>
                <span v-if="count.project > 0">{{count.project}}</span>
                <span v-else>...</span>
                <br />
                <span class="uk-text-meta"> Projets </span>
              </p>
            </div>
          </div>
          <div
            class="uk-child-width-1-2 uk-grid-small uk-margin-remove-top"
            uk-grid
          >
            <div class="uk-first-column uk-width-auto">
              <span class="uk-icon" uk-icon="ratio:2; icon: code"></span>
            </div>
            <div class="uk-width-expand">
              <p>
                3
                <br />
                <span class="uk-text-meta"> Développeurs </span>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, reactive } from "vue";
import * as User from "@/api/user";
import * as Project from "@/api/project";

export default defineComponent({
  setup() {
    const count = reactive({
      user: 0,
      project: 0,
    });

    async function init() {
      const result = await Promise.all([User.count(), Project.count()]);
      count.user = result[0];
      count.project = result[1];
    }

    init();
    return { count };
  },
});
</script>
