<template>
  <div class="uk-overflow-auto">
    <div class="uk-alert-primary" uk-alert>
      <div v-if="page.ready" class="uk-child-width-auto" uk-grid>
        <div class="uk-width-expand">
          <router-link
            :to="{ name: 'User', params: { userName: commit.publisher.name } }"
            class="uk-margin-small-right"
            :uk-tooltip="`title: ${commit.publisher.name}; pos: bottom`"
          >
            <UserPicture :user="commit.publisher" :size="2" />
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
import UserPicture from "@/components/User/UserPicture.vue"
import * as Branch from "@/api/branch";
import * as Commit from "@/api/commit";
import { notFound } from "@/app/routes";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
    branchName: String,
    commitID: String,
    branch: Object as () => Branch.FetchResult,
  },
  components: {
    UserPicture
  },
  setup(props) {
    const page = reactive({
      ready: false,
    });
    const commit = ref<Commit.FetchResult>({
      id: "",
      createdAt: new Date(),
      publisher: {
        name: ""
      },
      message: "",
      files: [],
    });

    async function init() {
      const result = await Commit.fetch(
        props.userName!,
        props.projectName!,
        props.branchName!,
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
