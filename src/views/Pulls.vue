<template>
  The project is {{ username }} / {{ project }} / pulls
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
