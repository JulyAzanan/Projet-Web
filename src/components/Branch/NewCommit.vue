<template>
  <div id="new-commit" class="uk-modal-container" uk-modal>
    <div class="uk-modal-dialog">
      <button
        ref="closeButton"
        class="uk-modal-close-default"
        type="button"
        uk-close
      ></button>

      <div class="uk-modal-header">
        <h2 class="uk-modal-title">Nouvelles partitions / modifications</h2>
      </div>

      <div
        @drop.prevent="dragFiles"
        @dragover.prevent
        class="uk-modal-body"
        uk-overflow-auto
      >
        <progress
          v-if="showProgress"
          class="uk-progress"
          :value="progress"
          :max="files.length"
        ></progress>
        <div v-if="files.length === 0" class="uk-text-center">
          <span
            class="uk-margin-small-right uk-text-muted"
            uk-icon="icon: cloud-upload"
          ></span>
          <span class="uk-text-middle"
            >Ajoutez des partitions en glissant des fichiers ici ou en
          </span>
          <div uk-form-custom>
            <input
              type="file"
              @change="selectFiles"
              multiple
              accept="image/x-png,image/gif,image/jpeg"
            />
            <span class="uk-link">sélectionnant des fichiers</span>
          </div>
        </div>
        <div v-for="file in files" :key="file.name" uk-alert>
          <a @click="removeFile(file)" class="uk-alert-close" uk-close></a>
          <p>
            <strong>{{ file.name }}</strong> ({{ prettyBytes(file.size) }})
          </p>
        </div>
      </div>

      <div class="uk-modal-footer uk-text-right">
        <form class="uk-display-block uk-form-stacked">
          <div class="uk-child-width-auto" uk-grid>
            <div class="uk-width-expand uk-margin-small-right">
              <input
                v-model="message"
                class="uk-input"
                id="form-h-text"
                type="text"
                placeholder="Message de commit"
                required
              />
            </div>
            <button
              class="uk-button uk-button-primary"
              type="submit"
              @click="createCommit"
            >
              Créer un nouveau commit
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from "vue";
import prettyBytes from "pretty-bytes";
import * as Commit from "@/api/commit";

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
    branchName: String,
  },
  setup(props) {
    const files = ref<File[]>([]);
    const message = ref("");
    const progress = ref(0);
    const showProgress = ref(false);
    const closeButton = ref<HTMLElement>();

    function addFiles(fileList?: FileList | null) {
      if (fileList == null || fileList?.length === 0) return;
      for (const file of fileList) {
        if (!files.value.some((f) => f.name === file.name))
          files.value.push(file);
      }
    }
    function dragFiles(event: DragEvent) {
      const element = event.dataTransfer;
      addFiles(element?.files);
    }
    function selectFiles(event: InputEvent) {
      const element = event.currentTarget as HTMLInputElement | null;
      addFiles(element?.files);
    }

    function removeFile(file: File) {
      for (let i = 0; i < files.value.length; i++) {
        if (files.value[i] === file) {
          files.value.splice(i, 1);
        }
      }
    }

    function loadFile(file: File): Promise<Commit.ScoreInput | null> {
      return new Promise((resolve) => {
        const reader = new FileReader();
        reader.onloadend = () => {
          const content = reader.result as string;
          progress.value++;
          resolve({
            name: file.name,
            content: btoa(content),
          });
        };
        reader.onerror = () => {
          progress.value++;
          resolve(null);
        };
        reader.readAsBinaryString(file);
      });
    }

    async function createCommit(event: Event) {
      if (message.value === "") return;
      event.preventDefault();
      const content: Commit.ContentInput = {
        message: message.value,
        scores: [],
      };
      showProgress.value = true;
      progress.value = 0;
      const scores = await Promise.all(
        files.value.map((file) => loadFile(file))
      );
      content.scores.push(
        ...(scores.filter((s) => s !== null) as Commit.ScoreInput[])
      );
      await Commit.add(
        props.userName!,
        props.projectName!,
        props.branchName!,
        content
      );
      console.log(closeButton);
      closeButton.value?.click();
      showProgress.value = false;
      files.value.splice(0, files.value.length);
    }

    return {
      dragFiles,
      selectFiles,
      files,
      prettyBytes,
      removeFile,
      message,
      createCommit,
      progress,
      showProgress,
      closeButton,
    };
  },
});
</script>

<style lang="scss" scoped>
</style>