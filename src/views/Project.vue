<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container">
      <h3 v-if="projectExists">
        <router-link :to="{ name: 'User', params: { username } }">
          {{ username }}
        </router-link>
        /
        <router-link
          :to="{ name: 'Branch-default', params: { username, project } }"
        >
          {{ project }}
        </router-link>
      </h3>
      <div v-else uk-spinner></div>
      <div>
        <ul class="uk-tab">
          <li>
            <router-link
              :to="{ name: 'Branch-default', params: { username, project } }"
              >Partitions</router-link
            >
          </li>
          <li>
            <router-link :to="{ name: 'Pulls', params: { username, project } }"
              >Changements
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'Discussions', params: { username, project } }"
              >Discussions
            </router-link>
          </li>
        </ul>

        <router-view
          v-if="ready"
          :mainBranch="mainBranch"
          v-slot="{ Component }"
        >
          <component :is="Component">
            <template v-slot:sidebar>
              <h4>
                <span class="uk-margin-small-right" uk-icon="icon: info"></span>
                À propos
              </h4>
              <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam
                pellentesque turpis eu libero elementum, mattis egestas nisi
                scelerisque. Nulla facilisi. Pellentesque lacinia felis non leo
                scelerisque tristique. Suspendisse bibendum quam quis
                ullamcorper lacinia. Ut nec semper enim, vitae efficitur felis.
                Curabitur scelerisque dignissim metus, at sagittis tortor
                scelerisque sit amet. Donec eu tincidunt justo.
              </p>

              <hr class="uk-divider-small" />

              <h4>
                <span
                  class="uk-margin-small-right"
                  uk-icon="icon: users"
                ></span>
                Contributeurs
              </h4>
              <ul class="uk-grid-small uk-flex-middle" uk-grid>
                <li v-for="name in contributors" :key="name">
                  <router-link
                    :to="{ name: 'User', params: { username: name } }"
                  >
                    <img
                      :src="`https://picsum.photos/seed/${name}/200/300`"
                      :alt="name"
                      :uk-tooltip="`title: ${name}; pos: bottom`"
                      class="rounded"
                    />
                  </router-link>
                </li>
              </ul>
            </template>
          </component>
        </router-view>
        <div v-else uk-spinner></div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from "vue";
import { onBeforeRouteUpdate } from "vue-router";
import { notFound } from "@/routes";
import * as Project from "@/api/project";

export default defineComponent({
  props: {
    username: String,
    project: String,
  },
  setup(props) {
    const ready = ref(false);
    const projectExists = ref(false);
    const mainBranch = ref("");
    const contributors = ref<string[]>([]);

    async function init() {
      const project = await Project.find(props.username, props.project);
      if (project === null) return notFound();
      mainBranch.value = project.mainBranch;
      contributors.value = project.contributors;
      projectExists.value = true;
      ready.value = true;
    }

    onBeforeRouteUpdate(async (to, from) => {
      if (to.name === "Branch-default") {
        ready.value = false;
        init();
      }
    });

    init();
    return { ready, projectExists, mainBranch, contributors };
  },
});
</script>

<style lang="scss" scoped>
img {
  &.rounded {
    object-fit: cover;
    border-radius: 50%;
    height: 50px;
    width: 50px;
  }
}
</style>
