import sleep from "./sleep";
import store from "../app/store";
import * as Project from "./project"
import * as Request from "../utils/request"

export const perPage = 15;
export const projectsPerPage = 15;

export interface BaseResult {
  name: string,
  picture?: string,
  email?: string,
  age?: number,
  bio?: string,
  latestCommit?: Date,
}

export async function login(user: string, password: string): Promise<boolean> {
  /* await sleep(500);
  store.commit("login", [user, password]);
  return true; */
  //
  store.commit("signIn", [user, password])
  const response = await Request.get("api/user.php?q=login")
  if (response.status === 401) return false;
  if (response.ok) {
    store.commit("login", [user, password]);
    return true;
  }
  Request.exception(response);
  return false;
}

export function logout(): void {
  store.commit("logout");
}

export async function register(user: string, password: string, email: string | null, age: number | null): Promise<boolean> {
  /* await sleep(500);
  store.commit("login", [user, password]);
  return true; */
  //
  const response = await Request.post("api/user.php", {
    user, password, email, age
  });
  if (response.ok) {
    store.commit("login", [user, password]);
    return true;
  }
  if (response.status === 400) return false;
  Request.exception(response);
  return false;
}

export async function remove(user: string): Promise<void> {
  /* await sleep(500);
  return; */
  //
  const response = await Request.delete_("api/user.php", { user });
  if (response.ok) {
    if (store.state.user === user) logout();
    return;
  }
  return Request.exception(response);
}

export interface UpdateInput {
  password?: string | null,
  email?: string | null,
  age?: number | null,
  bio?: string | null,
  picture?: string | null,
}

export async function edit(user: string, content: UpdateInput): Promise<boolean> {
  /* await sleep(500);
  return true; */
  //
  const response = await Request.patch("api/user.php", {
    user,
    password: null,
    email: null,
    age: null,
    bio: null,
    picture: null,
    ...content,
  });
  if (response.ok) return true;
  if (response.status === 401) return false;
  return Request.exception(response);
}

export async function find(user: string): Promise<boolean> {
  /* await sleep(500);
  return false; */
  //
  return Request.json("api/user.php", {
    q: "find",
    user,
  })
}

export async function findByEmail(email: string): Promise<boolean> {
  /* await sleep(500);
  return false; */
  //
  return Request.json("api/user.php", {
    q: "findByEmail",
    email,
  })
}

export interface ProfileResult extends BaseResult {
  following: AllResult[],
  followingCount: number,
}

export async function profile(page: number): Promise<ProfileResult | null> {
  /* await sleep(500);
  return {
    followingCount: 42,
    name: "Steel",
    email: "turfu@pm.me",
    age: 56,
    bio: "Je suis une fleur",
    following: [{
      name: "Foo",
      followers: 15
    }, {
      name: "Annie",
      followers: 36
    }, {
      name: "Elaim",
      followers: 1
    }, {
      name: "G perdu",
      followers: 36
    }, {
      name: "Salut",
      followers: 365
    }, {
      name: "Toi",
      followers: 315
    }, {
      name: "Keur",
      followers: 3615
    }]
  } */
  //
  return Request.json("api/user.php", {
    q: "getProfile",
    first: perPage,
    after: perPage * (page - 1)
  })
}

export interface FetchResult extends BaseResult {
  projectCount: number,
  followers: number,
  projects: Project.BaseResult[],
}

export async function fetch(user: string | undefined, page: number): Promise<FetchResult | null> {
  if (user === undefined) return null;
  // 
  /* await sleep(500);
  return {
    name: "Steel",
    email: "turfu@pm.me",
    age: 56,
    followers: 4,
    bio: "Je suis une fleur",
    projectCount: 42,
    projects: await Project.allOf(user, page),
  } */
  //
  return Request.json("api/user.php", {
    q: "getUser",
    user,
    first: projectsPerPage,
    after: projectsPerPage * (page - 1),
  })
}

export interface AllResult extends BaseResult {
  followers: number,
}

export async function all(page: number): Promise<AllResult[]> {
  /* await sleep(500);
  return [{
    name: "Bar",
    followers: 3615
  }, {
    name: "July",
    followers: 3,
  }, {
    name: "Foo",
    followers: 15
  }, {
    name: "Annie",
    followers: 36
  }, {
    name: "Elaim",
    followers: 1
  }, {
    name: "G perdu",
    followers: 36
  }, {
    name: "Salut",
    followers: 365
  }, {
    name: "Toi",
    followers: 315
  }, {
    name: "Keur",
    followers: 3615
  }] */
  //
  return Request.json("api/user.php", {
    q: "fetchAll",
    first: perPage,
    after: perPage * (page - 1),
  });
}

export interface SearchResult {
  results: AllResult[],
  count: number
}

export async function search(user: string, project: string, page: number): Promise<SearchResult> {
  /* await sleep(500);
  return {
    count: 42,
    results: [{
      name: "Annie",
      followers: 36
    }, {
      name: "Elaim",
      followers: 1
    }, {
      name: "G perdu",
      followers: 36
    }, {
      name: "Salut",
      followers: 365
    }, {
      name: "Toi",
      followers: 315
    }, {
      name: "Keur",
      followers: 3615
    }]
  }; */
  //
  return Request.json("api/user.php", {
    q: "seek",
    user,
    project,
    first: perPage,
    after: perPage * (page - 1),
  })
}

export async function count(): Promise<number> {
  /* await sleep(500);
  return 43; */
  //
  return Request.json("api/user.php", { q: "count" });
}
