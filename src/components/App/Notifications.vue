<template>
  <li>
    <a href="#" uk-tooltip="title: Notifications; pos: bottom; delay: 200">
      <span class="uk-margin-small-right" uk-icon="icon: bell"></span>
      <slot></slot>
      <span v-if="count > 0" class="uk-badge">{{ count }}</span>
    </a>
  </li>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  setup() {
    const count = 2;
    return {
      count,
    };
  },
});
</script>

