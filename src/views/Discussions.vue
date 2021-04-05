<template>
  Discussions: {{ username }} / {{ project }} / discussions
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    username: String,
    project: String,
  },
});
</script>
