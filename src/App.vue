<template>
  <nav uk-sticky class="uk-navbar-container uk-margin">
    <div class="uk-container">
      <div class="uk-navbar">
        <div class="uk-navbar-left">
          <a class="uk-navbar-item uk-logo" href="#">Logo</a>

          <ul class="uk-navbar-nav">
            <li class="uk-active"><a href="#">Active</a></li>
            <li><a href="#">Parent</a></li>
            <li><a href="#">Item</a></li>
            <li><a href="#">Item</a></li>
          </ul>
        </div>
        <div class="uk-navbar-right"></div>
        <ul class="uk-navbar-nav">
          <li>
            <a href="#">Right</a>
            <div class="uk-navbar-dropdown">
              <ul class="uk-nav uk-navbar-dropdown-nav">
                <li class="uk-active"><a href="#">Active</a></li>
                <li class="uk-parent">
                  <a href="#">Parent</a>
                  <ul class="uk-nav-sub">
                    <li><a href="#">Sub item</a></li>
                    <li><a href="#">Sub item</a></li>
                  </ul>
                </li>
                <li class="uk-nav-header">Header</li>
                <li>
                  <a href="#"
                    ><span
                      class="uk-margin-small-right"
                      uk-icon="icon: table"
                    ></span>
                    Item</a
                  >
                </li>
                <li>
                  <a href="#"
                    ><span
                      class="uk-margin-small-right"
                      uk-icon="icon: thumbnails"
                    ></span>
                    Item</a
                  >
                </li>
                <li class="uk-nav-divider"></li>
                <li>
                  <a href="#"
                    ><span
                      class="uk-margin-small-right"
                      uk-icon="icon: trash"
                    ></span>
                    Item</a
                  >
                </li>
              </ul>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="uk-container">
    <router-view />
  </div>
  <div id="nav">
    <router-link to="/">Home</router-link> |
    <router-link to="/about">About</router-link>
  </div>
</template>

<script>
import UIkit from "uikit";
import Icons from "uikit/dist/js/uikit-icons";
import { defineComponent } from "vue";

UIkit.use(Icons);

export default defineComponent({});
</script>

<style lang="less">
@import "../node_modules/uikit/src/less/uikit.less";
</style>
