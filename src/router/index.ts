import { createRouter, createWebHashHistory, RouteRecordRaw } from 'vue-router'
import Home from '@/views/Home.vue'

const routes: Array<RouteRecordRaw> = [
  {
    path: '/', // page d'accueil
    name: 'Home',
    component: Home
  },
  {
    path: "/-/:user", // profil utilisateur et liste des projets
    name: "User",
    component: () => import(/* webpackChunkName: "user" */ '@/views/User.vue'),
    props: true,
  },
  {
    path: "/-/:user/:project", // affiche la branche main par défaut
    name: "Project",
    component: () => () => import(/* webpackChunkName: "project" */ '@/views/Project.vue'),
    props: true,
  },
  {
    path: "/-/:user/:project/blob/:branch", // affiche le commit le plus récent par défaut
    name: "Branch",
    component: () => import(/* webpackChunkName: "branch" */ '@/views/Branch.vue'),
    props: true,
  },
  {
    path: "/-/:user/:project/blob/:branch/:commit", // affiche l'ensemble des partitions ainsi que des détails sur le projet
    name: "Commit",
    component: () => import(/* webpackChunkName: "commit" */ '@/views/Commit.vue'),
    props: true,
  },
  {
    path: "/-/:user/:project/blob/:branch/:commit/:filepath", // affiche la partition en question
    name: "File",
    component: () => import(/* webpackChunkName: "file" */ '@/views/File.vue'),
    props: true,
  },
  {
    path: "/-/:user/:project/pulls", // pull request
    name: "Pulls",
    component: () => import(/* webpackChunkName: "pulls" */ '@/views/Pulls.vue'),
    props: true,
  },
  {
    path: "/-/:user/:project/pulls/:pull", // pull request
    name: "Pull",
    component: () => import(/* webpackChunkName: "pull" */ '@/views/Pull.vue'),
    props: true,
  },
  {
    path: "/-/:user/:project/discussions", // discussions sur le projet
    name: "Discussions",
    component: () => import(/* webpackChunkName: "discussions" */ '@/views/Discussions.vue'),
    props: true,
  },
  {
    path: "/-/:user/:project/discussions/:discussion", // discussions sur le projet
    name: "Discussion",
    component: () => import(/* webpackChunkName: "discussion" */ '@/views/Discussion.vue'),
    props: true,
  },
  {
    path: '/settings', // paramètres utilisateur
    name: 'Settings',
    component: () => import(/* webpackChunkName: "settings" */ '@/views/Settings.vue')
  },
  {
    path: '/login', // page de connexion / d'inscription
    name: 'Login',
    component: () => import(/* webpackChunkName: "login" */ '@/views/Login.vue')
  },
  {
    path: '/about', // page à propos
    name: 'About',
    component: () => import(/* webpackChunkName: "about" */ '@/views/About.vue')
  },
  {
    path: "/:catchAll(.*)",
    component: () => import(/* webpackChunkName: "notFound" */ '@/views/NotFound.vue'),
  },
]

const router = createRouter({
  history: createWebHashHistory(),
  routes
})

export default router
