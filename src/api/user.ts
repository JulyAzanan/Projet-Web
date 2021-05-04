import store from "../app/store";
import * as Project from "./project"
import * as Request from "../utils/request"

export const perPage = 10;
export const projectsPerPage = 6;

export interface BaseResult {
  name: string,
  picture?: string,
  email?: string,
  age?: number,
  bio?: string,
  latestCommit?: Date,
}

export async function login(user: string, password: string): Promise<boolean> {
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
  Request.exception(response);
  return false;
}

export async function find(user: string): Promise<boolean> {
  return Request.json("api/user.php", {
    q: "find",
    user,
  })
}

export async function findByEmail(email: string): Promise<boolean> {
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

export async function search(user: string, page: number): Promise<SearchResult> {
  return Request.json("api/user.php", {
    q: "seek",
    user,
    first: perPage,
    after: perPage * (page - 1),
  })
}

export async function count(): Promise<number> {
  return Request.json("api/user.php", { q: "count" });
}
