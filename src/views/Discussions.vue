<template>
  Discussions: {{ userName }} / {{ projectName }} / discussions
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
