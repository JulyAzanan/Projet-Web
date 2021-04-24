import sleep from "./sleep";
import { Nil, isNil } from "./types"
import store from "../app/store";
import * as Project from "./project"
import * as Request from "../utils/request"

export const projectPerPage = 6;
export const userPerPage = 15;

interface BaseResult {
  email?: string,
  age?: number,
  bio?: string,
  latestCommit?: Date,
}

export async function login(user: string, password: string): Promise<boolean> {
  await sleep(500);
  store.commit("login", [user, password]);
  return true;
  /* store.commit("signIn", [user, password])
  const response = await Request.get("api/user.php?q=login")
  if (response.status === 401) return false;
  if (response.ok) {
    store.commit("login", [user, password]);
    return true;
  }
  return Request.exception(response); */
}

export function logout(): void {
  store.commit("logout");
}

export async function register(user: string, password: string, email: Nil<string>, age?: Nil<number>): Promise<boolean> {
  await sleep(500);
  store.commit("login", [user, password]);
  return true;
  /* const response = await Request.post("api/user.php", {
    user, password, email, age
  });
  if (response.ok) {
    store.commit("login", [user, password]);
    return true;
  }
  if (response.status === 400) return false;
  return Request.exception(response); */
}

// TODO: remove

export async function find(user: Nil<string>): Promise<BaseResult | null> {
  if (user == null) return null;
  // 
  await sleep(500);
  return {
    email: "turfu@pm.me",
    age: 56,
    bio: "Je suis une fleur",
  }
  /* return Request.json("api/user.php", {
    q: "find",
    user,
  }) */
}

export interface FindByEmailResult extends Omit<BaseResult, "email"> {
  name: string
}

export async function findByEmail(email: Nil<string>): Promise<FindByEmailResult | null> {
  if (isNil(email)) return null;
  // 
  await sleep(500);
  return {
    name: "Steel",
    age: 56,
    bio: "Je suis une fleur",
  }
  /* return Request.json("api/user.php", {
    q: "findByEmail",
    email,
  })  */
}

export interface FetchResult extends BaseResult {
  projectCount: number,
  followers: number,
  projects: Project.AllOfResult[],
}

export async function fetch(user: Nil<string>, page: number): Promise<FetchResult | null> {
  if (isNil(user)) return null;
  // 
  await sleep(500);
  return {
    email: "turfu@pm.me",
    age: 56,
    followers: 4,
    bio: "Je suis une fleur",
    projectCount: 42,
    projects: await Project.allOf(user, projectPerPage, page * projectPerPage),
  }
  /* return Request.json("api/user.php", {
    q: "fetch",
    user,
    page,
  }) */
}

export interface AllResult extends BaseResult {
  followers: number,
  name: string,
}

export async function all(first: number, after: number): Promise<AllResult[]> {
  await sleep(500);
  return [{
    name: "Bar",
    followers: 3615
  }, {
    name: "July",
    followers: 3
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
  }]
  /* return Request.json("api/user.php", {
    q: "fetchAll",
    first,
    after,
  }); */
}

export async function count(): Promise<number> {
  await sleep(500);
  return 43;
  // return Request.json("api/user.php", { q: "count" });
}