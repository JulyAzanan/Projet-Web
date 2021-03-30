<template>
  The project is {{ user }} / {{ project }} / discussions
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    user: String,
    project: String,
  },
});
</script>
