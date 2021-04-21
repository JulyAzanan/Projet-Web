<template>
  <div>
    <div>
      <div class="uk-card uk-card-default uk-card-hover">
        <span
          uk-icon="icon: star"
          :uk-tooltip="` ${followers} amis`"
          class="uk-position-top-right uk-margin-small-top uk-margin-small-right"
        >
        </span>
        <img
          :src="`https://picsum.photos/seed/${userName}/100/100`"
          :alt="name"
          class="rounded uk-margin-medium-top"
        />
        <div class="uk-card-body">
          <router-link
            :to="{ name: 'User', params: { userName } }"
            uk-margin
            class="uk-button uk-button-default"
            uk-tooltip="Voir le profil"
          >
            {{ userName }}
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
img {
  &.rounded {
    object-fit: cover;
    border-radius: 50%;
    height: 100px;
    width: 100px;
  }
}
</style>