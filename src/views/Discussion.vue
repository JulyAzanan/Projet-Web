<template>
  The project is {{ userName }} / {{ projectName }} / {{ discussion }}
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
    discussion: String,
  },
});
</script>
