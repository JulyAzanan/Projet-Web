<template>
  <div class="uk-section-default">
    <div class="uk-section uk-section-xlarge uk-background-cover">
      <div class="uk-container">
        <div class="uk-child-width-1-3@m uk-grid-match" uk-grid>
          <div />
          <div>
            <div class="uk-card uk-card-default uk-card-hover uk-card-body">
              <h3 class="uk-card-title">Connexion</h3>
              <form class="uk-form-stacked">
                <div class="uk-margin">
                  <span class="uk-form-label">Work in progress</span>

                  <div class="uk-inline">
                    <span class="uk-form-icon" uk-icon="icon: user"></span>
                    <input class="uk-input uk-form-width-medium" type="text" />
                  </div>
                </div>

                <div class="uk-margin">
                  <div class="uk-inline">
                    <span
                      class="uk-form-icon uk-form-icon-flip"
                      uk-icon="icon: lock"
                    ></span>
                    <input class="uk-input uk-form-width-medium" type="text" />
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({});
</script>

<style lang="scss" scoped>
.uk-background-cover {
  background-image: url("../assets/login.jpg");
}
// Photo by <a href="https://unsplash.com/@john_matychuk?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">John Matychuk</a> on <a href="https://unsplash.com/s/photos/music?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Unsplash</a>
</style>
