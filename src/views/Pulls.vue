<template>
  Pulls: {{ userName }} / {{ projectName }} / pulls
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
