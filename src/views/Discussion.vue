<template>
  The project is {{ user }} / {{ project }} / {{ discussion }}
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    user: String,
    project: String,
    discussion: String,
  },
});
</script>
