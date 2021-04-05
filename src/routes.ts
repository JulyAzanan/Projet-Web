import { createRouter, createWebHashHistory, RouteRecordRaw } from 'vue-router'

function load(name: string) {
  return {
    name,
    component: () => import(/* webpackChunkName: "[request]" */ `@/views/${name}.vue`),
  }
}

const routes: Array<RouteRecordRaw> = [
  {
    ...load("Home"),
    path: "/", // page d'accueil
  },
  {
    ...load("Users"),
    path: "/users", // liste de tous les utilisateurs
    props: route => ({ page: route.query.page ?? "1" }),
  },
  {
    ...load("Projects"),
    path: "/projects", // liste de tous les projets
    props: route => ({ page: route.query.page ?? "1" }),
  },
  {
    path: "/-",
    redirect: "/"
  },
  {
    ...load("User"),
    path: "/-/:username", // liste des projets d'un utilisateur
    props: route => ({ page: route.query.page ?? "1", username: route.params.username }),
  },
  {
    ...load("Project"),
    path: "/-/:username/:project", // affiche la branche main par défaut
    props: true,
    children: [
      {
        ...load("Branch"),
        path: "blob/:branch", // affiche le commit le plus récent par défaut
        props: true,
        children: [
          {
            ...load("Commit"),
            path: ":commit", // affiche l'ensemble des partitions ainsi que des détails sur le projet
            props: true,
          },
          {
            ...load("File"),
            path: ":filepath", // affiche la partition en question
            props: true,
          },
        ]
      },
      {
        ...load("Pulls"),
        path: "pulls", // pull request
      },
      {
        ...load("Pull"),
        path: "pulls/:pull", // pull request
        props: true,
      },
      {
        ...load("Discussions"),
        path: "discussions", // discussions sur le projet
      },
      {
        ...load("Discussion"),
        path: "discussions/:discussion", // discussions sur le projet
        props: true,
      },
    ]
  },
  {
    ...load("Settings"),
    path: '/settings', // paramètres utilisateur // à voir
  },
  {
    ...load("Profile"),
    path: '/profile', // profil utilisateur
  },
  {
    ...load("Login"),
    path: '/login', // page de connexion
  },
  {
    ...load("Register"),
    path: '/register', // page d'inscription
  },
  {
    ...load("About"),
    path: '/about', // page à propos
  },
  {
    ...load("NotFound"),
    path: "/:catchAll(.*)",
  },
]

const router = createRouter({
  history: createWebHashHistory(),
  routes
})

export default router
