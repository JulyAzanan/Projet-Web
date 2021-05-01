<template>
  <div>
    <div>
      <div class="uk-card uk-card-default uk-card-hover">
        <span
          uk-icon="icon: star"
          :uk-tooltip="` ${user.followers} amis`"
          class="uk-position-top-right uk-margin-small-top uk-margin-small-right"
        >
        </span>
        <UserPicture :user="user" :size="7" class="uk-margin-medium-top" />
        <div class="uk-card-body">
          <button
            uk-margin
            class="uk-button uk-button-default"
            @click="$emit('click')"
          >
            Ajouter {{ user.name }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import * as User from "@/api/user"
import UserPicture from "./UserPicture.vue"

export default defineComponent({
  props: {
    user: Object as () => User.AllResult
  },
  components: {
    UserPicture
  },
  emits: ['click'],
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