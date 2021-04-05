<template>Projects page {{ page }}</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    page: String,
  },
});
</script>

<style lang="scss" scoped>
</style>