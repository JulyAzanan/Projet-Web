<template>
  <div class="uk-container">
    <div class="uk-grid-divider" uk-grid>
      <div class="uk-width-2-3@s">
        <select class="uk-select uk-form-width-small">
          <option>Option 01</option>
          <option>Option 02</option>
        </select>
      </div>
      <div class="uk-width-1-3@s">
        <div v-if="ready">
          <h4>
            <span class="uk-margin-small-right" uk-icon="icon: info"></span>
            À propos
          </h4>
          <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam
            pellentesque turpis eu libero elementum, mattis egestas nisi
            scelerisque. Nulla facilisi. Pellentesque lacinia felis non leo
            scelerisque tristique. Suspendisse bibendum quam quis ullamcorper
            lacinia. Ut nec semper enim, vitae efficitur felis. Curabitur
            scelerisque dignissim metus, at sagittis tortor scelerisque sit
            amet. Donec eu tincidunt justo.
          </p>

          <hr class="uk-divider-small" />

          <h4>
            <span class="uk-margin-small-right" uk-icon="icon: users"></span>
            Contributeurs
          </h4>
          <ul class="uk-grid-small uk-flex-middle" uk-grid>
            <li v-for="name in contributors" :key="name">
              <router-link :to="{ name: 'User', params: { username: name } }">
                <img
                  :src="`https://picsum.photos/seed/${name}/200/300`"
                  :alt="name"
                  :uk-tooltip="`title: ${name}; pos: bottom`"
                  class="rounded"
                />
              </router-link>
            </li>
          </ul>
        </div>
        <div v-else uk-spinner></div>
      </div>
    </div>
  </div>
  <br />
  <router-view :branch="branch"></router-view>
</template>

<script lang="ts">
import { defineComponent, ref } from "vue";
import * as Project from "@/api/project";
import * as Branch from "@/api/branch";
import router, { notFound } from "@/routes";

export default defineComponent({
  props: {
    username: String,
    project: String,
    branch: String,
  },
  setup(props) {
    const ready = ref(true);
    if (props.branch === null) {
      Project.metadata(props.username, props.project).then(
        ({ meta, username, project }) => {
          router.push({
            name: "Branch",
            params: { username, project, branch: meta.mainBranch },
          });
        }
      );
    } else {
      Branch.exists(props.username, props.project, props.branch).then(
        (exists) => {
          if (exists) ready.value = true;
          else notFound();
          console.log("exists")
        }
      );
    }
    return { ready, contributors: ["Steel", "July", "Michel"] };
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
