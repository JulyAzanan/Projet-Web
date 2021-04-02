<template>
  <Navbar/>
  <div id="main" class="uk-container">
    <router-view />
  </div>
  <Footer/>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import Footer from "@/components/App/Footer.vue";
import Navbar from "@/components/App/Navbar.vue";

export default defineComponent({
  components: {
    Footer,
    Navbar,
  }
});
</script>

<style lang="scss">
@import "@/styles/variables.scss";

#app {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  #main {
    flex-grow: 1;
  }
}
</style>
