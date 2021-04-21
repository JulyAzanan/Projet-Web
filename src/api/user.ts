import sleep from "./sleep";
import * as Project from "./project"

interface BaseResult {
  email?: string,
  age?: number,
  bio?: string,
  latestCommit?: Date,
}

export interface FetchResult extends BaseResult {
  projectCount: number,
  followers: number,
  projects: Project.AllOfResult[],
}

export const projectPerPage = 6;
export const userPerPage = 15;

export async function fetch(user: string | null, page: number): Promise<FetchResult | null> {
  if (user == null) return null;
  await sleep(500); // TODO
  return {
    email: "turfu@pm.me",
    age: 56,
    followers: 4,
    bio: "Je suis une fleur",
    projectCount: 42,
    projects: await Project.allOf(user, projectPerPage, page * projectPerPage ),
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
  },{
    name: "July",
    followers: 3
  },{
    name: "Foo",
    followers: 15
  },{
    name: "Annie",
    followers: 36
  },{
    name: "Elaim",
    followers: 1
  },{
    name: "G perdu",
    followers: 36
  },{
    name: "Salut",
    followers: 365
  },{
    name: "Toi",
    followers: 315
  },{
    name: "Keur",
    followers: 3615
  }]
}

export async function count(): Promise<number> {
  await sleep(500); // TODO
  return 43;
}