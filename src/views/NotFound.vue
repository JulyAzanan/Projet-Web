<template>
  <div class="uk-container">
  <div class="uk-background-cover uk-position-relative">
    <img src="@/assets/404.png" alt="ballon dans les airs" class="background-image" />
    <div class="uk-position-cover uk-flex uk-flex-center uk-flex-middle">
      <h1 class="uk-heading-xlarge">404</h1>
    </div>
  </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({});
</script>

<style lang="scss" scoped>
.background-image {
  display: block;
  margin-left: auto;
  margin-right: auto;
}
</style>