import { createRouter, createWebHashHistory, RouteRecordRaw } from 'vue-router'

function load(name: string, prefix?: string) {
  return {
    name: name + (prefix ? `-${prefix}` : ""),
    component: () => import(/* webpackChunkName: "[request]" */ `@/views/${name}.vue`),
  }
}

const routes: RouteRecordRaw[] = [
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
    path: "/-/:userName", // liste des projets d'un utilisateur
    props: route => ({ page: route.query.page ?? "1", ...route.params }),
  },
  {
    ...load("Project"),
    path: "/-/:userName/:projectName", // page d'un projet
    props: true,
    children: [
      {
        path: "-",
        redirect: to => ({ name: "Project", params: { userName: to.params.userName, projectName: to.params.projectName } })
      },
      {
        ...load("Branch", "default"),
        path: "", // affiche la branche main par défaut
        props: route => ({ branchName: null, ...route.params }),
      },
      {
        ...load("Branch"),
        path: "-/:branchName", // affiche les partitions d'une branche
        props: true,
        children: [
          {
            ...load("Commit", "default"),
            path: "", // affiche le dernier commit par défaut
            props: route => ({ commitID: null, ...route.params }),
          },
          {
            ...load("Commit"),
            path: ":commitID", // affiche le commit en question
            props: true,
            children: [
              {
                ...load("Files"),
                path: "", // affiche l'ensemble des partitions
                props: true,
              },
              {
                ...load("File"),
                path: ":filePath", // affiche la partition en question
                props: true,
              },
            ],
          },
        ],
      },
      {
        ...load("Pulls"),
        path: "pulls", // pull request
        props: true,
      },
      {
        ...load("Pull"),
        path: "pulls/:pull", // pull request
        props: true,
      },
      {
        ...load("Discussions"),
        path: "discussions", // discussions sur le projet
        props: true,
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
    props: route => ({ redirect: route.query.redirect ?? "/" }),
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
    path: "/:catchAll(.*)*",
  },
]

const router = createRouter({
  history: createWebHashHistory(),
  routes
})

export async function notFound(): Promise<void> {
  const route = router.currentRoute.value
  await router.replace({
    name: "NotFound",
    params: { catchAll: route.path.substring(1).split('/') },
    query: route.query,
    hash: route.hash,
  })
}

export default router
