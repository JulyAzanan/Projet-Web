<template>
  <div>
    <div>
      <div class="uk-card uk-card-default uk-card-hover">
        <div class="uk-card-badge">
          <span
            v-if="!priv"
            class="uk-margin-small-right"
            uk-icon="icon: users"
            uk-tooltip="Public"
          ></span>
          <span
            v-else
            class="uk-margin-small-right"
            uk-icon="icon: lock"
            uk-tooltip="Privé"
          ></span>
        </div>
        <div class="uk-card-body">
          <p></p>
          <h3 class="uk-card-title">{{projectName}}</h3>
          <router-link
            :to="{ name: 'Branch-default', params: { userName, projectName } }"
            uk-margin
            class="uk-button uk-button-default"
          >
            Voir le projet
          </router-link>
          <p>Dernière mise à jour le : {{ updatedAt }}</p>
          <p v-if="userName != author">Créé par : {{ author }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    priv: Boolean,
    updatedAt: String,
    author: String,
    userName: String,
    projectName: String,
    branchName: String,
  },
});
</script>

<style lang="scss" scoped>
</style>