<template>
  <div>
    <HomeNotLogged v-if="!logged"/>
    <HomeLogged v-else/>
  </div>
</template>

<script lang="ts">
import { defineComponent, computed } from 'vue';
import store from "@/app/store";
import HomeNotLogged from '@/components/Home/HomeNotLogged.vue'; // @ is an alias to /src
import HomeLogged from '@/components/Home/HomeLogged.vue';

export default defineComponent({
  components: {
    HomeNotLogged,
    HomeLogged
  },
  setup() {
    return {
      logged: computed(() => store.state.loggedIn),
    };
  },
});
</script>
