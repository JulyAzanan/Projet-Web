import sleep from "./sleep";
import { Nil } from "./types"

interface BaseResult {
  private: boolean,
  updatedAt: Date,
  createdAt: Date,
  description: string,
}

export interface FetchResult extends BaseResult {
  contributors: string[],
  branches: string[],
  mainBranch: string,
}

export async function fetch(user: Nil<string>, project: Nil<string>): Promise<FetchResult | null> {
  if (user == null || project == null) return null;
  await sleep(500); // TODO
  return {
    private: false,
    contributors: ["Steel", "July", "Michel"],
    branches: ["main", "dev", "prod"],
    mainBranch: "main",
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam pellentesque turpis eu libero elementum, mattis egestas nisi scelerisque. Nulla facilisi. Pellentesque lacinia felis non leo scelerisque tristique. Suspendisse bibendum quam quis ullamcorper lacinia. Ut nec semper enim, vitae efficitur felis. Curabitur scelerisque dignissim metus, at sagittis tortor scelerisque sit amet. Donec eu tincidunt justo.",
  };
}

export async function add(_author: string, _project: string, _isPrivate: boolean, _description: string): Promise<void> {
  // TODO
  return;
}

export async function remove(_author: string, _project: string): Promise<void> {
  // TODO
  return;
}

export interface AllOfResult extends BaseResult {
  name: string
}

export async function allOf(user: Nil<string>, _first: number, _after: number): Promise<AllOfResult[]> {
  if (user == null) return [];
  await sleep(500); // TODO
  return [{
    private: true,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    name: "Nier: Automata",
    description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
  }, {
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    name: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }, {
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    name: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }, {
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    name: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }, {
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    name: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }]
}

export interface AllResult extends AllOfResult {
  author: string
}

export async function all(_first: number, _after: number): Promise<AllOfResult[] | null> {
  await sleep(500); // TODO
  return []
}

export async function count(): Promise<number> {
  await sleep(500); // TODO
  return 5;
}

export async function countOf(user: Nil<string>): Promise<number> {
  if (user == null) return 0;
  await sleep(500); // TODO
  return 5;
}