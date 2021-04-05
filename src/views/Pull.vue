<template>
  The project is {{ username }} / {{ project }} / {{ pull }}
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    username: String,
    project: String,
    pull: String
  },
});
</script>
