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
        <UserPicture :user="user" :size="7" />
        <div class="uk-card-body">
          <router-link
            :to="{ name: 'User', params: { userName: user.name } }"
            uk-margin
            class="uk-button uk-button-default"
            uk-tooltip="Voir le profil"
          >
            {{ user.name }}
          </router-link>
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
  }
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