<template>
  <div class="uk-section uk-section-default uk-section-small">
    <div class="uk-container">
      <h3 v-if="page.valid">
        <router-link :to="{ name: 'User', params: { userName } }">
          {{ userName }}
        </router-link>
        /
        <router-link
          :to="{ name: 'Branch-default', params: { userName, projectName } }"
        >
          {{ projectName }}
        </router-link>
      </h3>
      <div v-else uk-spinner></div>
      <div>
        <ul class="uk-tab">
          <li>
            <router-link
              :to="{ name: 'Branch-default', params: { userName, projectName } }"
              >Partitions</router-link
            >
          </li>
          <li>
            <router-link :to="{ name: 'Pulls', params: { userName, projectName } }"
              >Changements
            </router-link>
          </li>
          <li>
            <router-link
              :to="{ name: 'Discussions', params: { userName, projectName } }"
              >Discussions
            </router-link>
          </li>
        </ul>

        <router-view
          v-if="page.ready"
          :mainBranch="project.mainBranch"
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
                    :to="{ name: 'User', params: { userName: name } }"
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
import { defineComponent, ref, reactive } from "vue";
import { onBeforeRouteUpdate } from "vue-router";
import { notFound } from "@/routes";
import * as Project from "@/api/project";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
  },
  setup(props) {
    const page = reactive({
      ready: false,
      valid: false, 
    });
    const project = ref<Project.Result>({
      contributors: [],
      mainBranch: "",
      description: "",
      updatedAt: new Date(),
      createdAt: new Date(),
    });

    async function init() {
      const result = await Project.find(props.userName, props.projectName);
      if (result === null) return notFound();
      project.value = result;
      page.valid = true;
      page.ready = true;
    }

    onBeforeRouteUpdate((to) => {
      if (to.name === "Branch-default") {
        page.ready = false;
        init();
      }
    });

    init();
    return { page, project };
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
