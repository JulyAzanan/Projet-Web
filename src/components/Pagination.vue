<template>
  <ul class="uk-pagination uk-position-bottom-center" uk-margin>
    <li :class="{ 'uk-disabled': page <= 1 }">
      <router-link :to="{ query: { page: page - 1 + '' } }">
        <span uk-pagination-previous></span>
      </router-link>
    </li>
    <li v-for="i in pages" :key="i" :class="{ 'uk-active': i + '' == page }">
      <router-link :to="{ query: { page: i + '' } }">
        <span> {{ i }}</span>
      </router-link>
    </li>
    <li :class="{ 'uk-disabled': page >= pages }">
      <router-link :to="{ query: { page: page + 1 + '' } }">
        <span uk-pagination-next></span>
      </router-link>
    </li>
  </ul>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    page: Number,
    pages: Number,
  }
});
</script>

<style lang="scss" scoped>
</style>
