<template>
  <div>
    <div>
      <div class="uk-card uk-card-default uk-card-hover">
          <span
            uk-icon="icon: star"
            :uk-tooltip="` ${followers} Abonnés`"
            class="uk-position-top-right uk-margin-small-top uk-margin-small-right"
          >
          </span>

        <div class="uk-card-body">
          <router-link
            :to="{ name: 'User', params: { userName } }"
            uk-margin
            class="uk-button uk-button-default"
            uk-tooltip="Voir le profil"
          >
            {{userName}}
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    userName: String,
    followers: Number,
  },
});
</script>

<style lang="scss" scoped>
</style>