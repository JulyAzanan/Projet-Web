<template>
  <p>
      Discussions: {{ userName }} / {{ projectName }} / discussions
  </p>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
  },
});
</script>
