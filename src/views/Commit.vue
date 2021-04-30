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
          <div uk-form-custom>
            <select
              class="uk-select uk-form-width-small uk-margin-small-right"
              v-model="selectedCommit"
            >
              <option
                v-for="commit in branch.commits"
                :key="commit.id"
                :value="commit.id"
              >
                {{ commit.id }}
              </option>
            </select>
            <code> {{ commitID }} </code>
          </div>
          {{ commit.createdAt.toLocaleString() }}
        </div>
      </div>
    </div>
    <router-view v-if="page.ready" :commit="commit"></router-view>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive, WatchStopHandle, watch } from "vue";
import { onBeforeRouteUpdate } from "vue-router";
import UserPicture from "@/components/User/UserPicture.vue";
import * as Branch from "@/api/branch";
import * as Commit from "@/api/commit";
import router, { notFound } from "@/app/routes";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
    branchName: String,
    commitID: String,
    branch: Object as () => Branch.FetchResult,
  },
  components: {
    UserPicture,
  },
  setup(props) {
    const selectedCommit = ref(props.commitID);
    const page = reactive({
      ready: false,
    });
    const commit = ref<Commit.FetchResult>({
      id: "",
      createdAt: new Date(),
      publisher: {
        name: "",
      },
      message: "",
      files: [],
    });

    let stopCommitWatcher: WatchStopHandle = () => {};

    async function init(commitID?: string) {
      const result = await Commit.fetch(
        props.userName!,
        props.projectName!,
        props.branchName!,
        commitID ?? props.commitID
      );
      if (result === null) return notFound();
      commit.value = result;
      stopCommitWatcher();
      selectedCommit.value = props.commitID;
      stopCommitWatcher = watch(selectedCommit, async () => {
        await router.replace({
          name: "Files",
          params: {
            userName: props.userName!,
            projectName: props.projectName!,
            branchName: props.branchName!,
            commitID: selectedCommit.value!,
          },
        });
      });
      page.ready = true;
    }

    onBeforeRouteUpdate((to) => {
      if (
        to.name === "Commit" ||
        to.name === "Files" ||
        to.params.commitID !== props.commitID
      ) {
        page.ready = false;
        init(to.params.commitID as string);
      }
    });

    init();
    return { page, commit, selectedCommit };
  },
});
</script>
