<template>
  Commit: {{ username }} / {{ project }} / {{ branch }} / {{ commit }}
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    username: String,
    project: String,
    branch: String,
    commit: String,
  },
});
</script>
