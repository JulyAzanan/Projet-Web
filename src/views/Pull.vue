<template>
  <p>The project is {{ userName }} / {{ projectName }} / {{ pull }}</p>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
    pull: String,
  },
});
</script>
