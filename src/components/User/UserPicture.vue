<template>
  <img
    :src="user.picture || require('@/assets/profile.jpg')"
    :alt="user.name"
    :class="`rounded-${size}`"
  />
</template>

<script lang="ts">
import { defineComponent } from "vue";
import * as User from "@/api/user";

export default defineComponent({
  props: {
    user: Object as () => User.AllResult,
    size: Number,
  },
});
</script>

<style lang="scss" scoped>
img {
  @for $i from 1 through 100 {
    &.rounded-#{$i} {
      object-fit: cover;
      border-radius: 50%;
      height: #{$i}em;
      width: #{$i}em;
    }
  }
}
</style>