import sleep from "./sleep";
import { Nil } from "./types"
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
  store.commit("signIn", [user, password])
  const response = await Request.get("api/user.php?q=login")
  if (response.status === 401) return false;
  if (response.ok) {
    store.commit("login", [user, password]);
    return true;
  }
  return Request.exception(response);
}

export function logout(): void {
  store.commit("logout");
}

export async function register(user: string, password: string, email?: string, age?: number): Promise<boolean> {
  const response = await Request.post("api/user.php", JSON.stringify({
    user, password, email, age
  }), "application/json");
  if (response.ok) {
    store.commit("login", [user, password]);
    return true;
  }
  if (response.status === 513) return false;
  return Request.exception(response);
}

export interface FetchResult extends BaseResult {
  projectCount: number,
  followers: number,
  projects: Project.AllOfResult[],
}

export async function fetch(user: Nil<string>, page: number): Promise<FetchResult | null> {
  if (user == null) return null;
  await sleep(500); // TODO
  return {
    email: "turfu@pm.me",
    age: 56,
    followers: 4,
    bio: "Je suis une fleur",
    projectCount: 42,
    projects: await Project.allOf(user, projectPerPage, page * projectPerPage),
  }
}

export interface AllResult extends BaseResult {
  followers: number, // nécessite une jointure
  name: string,
}

export async function all(_first: number, _after: number): Promise<AllResult[]> {
  await sleep(500); // TODO
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
}

export async function count(): Promise<number> {
  await sleep(500); // TODO
  return 43;
}