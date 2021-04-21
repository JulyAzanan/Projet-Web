<template>
  <div class="uk-overflow-auto">
    <div class="uk-alert-primary" uk-alert>
      <div v-if="page.ready" class="uk-child-width-auto" uk-grid>
        <div class="uk-width-expand">
          <router-link
            :to="{ name: 'User', params: { userName: commit.author } }"
            class="uk-margin-small-right"
          >
            <img
              :src="`https://picsum.photos/seed/${commit.author}/200/300`"
              :alt="commit.author"
              :uk-tooltip="`title: ${commit.author}; pos: bottom`"
              class="rounded"
            />
          </router-link>
          <strong>{{ commit.author }}</strong> {{ commit.message }}
        </div>
        <div>
          <code> {{ commitID }} </code>
          {{ commit.createdAt.toLocaleString() }}
        </div>
      </div>
    </div>
    <router-view v-if="page.ready" :commit="commit"></router-view>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive } from "vue";
import { onBeforeRouteUpdate } from "vue-router";
import * as Branch from "@/api/branch";
import * as Commit from "@/api/commit";
import { notFound } from "@/routes";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
    branchName: String,
    commitID: String,
    branch: Object as () => Branch.FetchResult,
  },
  setup(props) {
    const page = reactive({
      ready: false,
    });
    const commit = ref<Commit.FetchResult>({
      createdAt: new Date(),
      author: "",
      message: "",
      files: [],
    });

    async function init() {
      const result = await Commit.fetch(
        props.userName,
        props.projectName,
        props.branchName,
        props.commitID
      );
      if (result === null) return notFound();
      commit.value = result;
      page.ready = true;
    }

     onBeforeRouteUpdate((to) => {
      if (to.name === "Commit" || to.name === "Files") {
        page.ready = false;
        init();
      }
    });

    init();
    return { page, commit };
  },
});
</script>

<style lang="scss" scoped>
img {
  &.rounded {
    object-fit: cover;
    border-radius: 50%;
    height: 30px;
    width: 30px;
  }
}
</style>
