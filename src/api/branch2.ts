import sleep from "./sleep"

interface BaseResult {
  updatedAt: Date,
  createdAt: Date,
}

export async function add(_author: string, _project: string, _branch: string): Promise<void> {
  // TODO
  return;
}

export async function remove(_author: string, _project: string, _branch: string): Promise<void> {
  // TODO
  return;
}

export async function rename(_author: string, _project: string, _branch: string, _newBranchName: string): Promise<void> {
  // TODO
  return;
}

export interface FindResult extends BaseResult {
  lastCommit?: string
}

export async function find(user?: string, project?: string, branch?: string): Promise<FindResult | null> {
  if (user == null || project == null || branch == null) return null;
  await sleep(500);
  return {
    lastCommit: "8f5e91",
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
  }
}

export interface FetchAllFromResult extends BaseResult {
  branch: string
}

export async function fetchAllFrom(first: number, after: number, author?: string, project?: string): Promise<FetchAllFromResult[]> {
  if (author == null || project == null) return [];
  await sleep(500); // TODO
  return [{
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    branch: "main",
  },
  {
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    branch: "dev",
  },
  {
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    branch: "prod",
  }];
}

export async function all(author?: string, project?: string): Promise<string[]> {
  if (author == null || project == null) return [];
  await sleep(500); // TODO
  return [
    "main",
    "dev",
    "prod",
  ]
}

export async function countFrom(author?: string, project?: string): Promise<number> {
  if (author == null || project == null) return 0;
  await sleep(500); // TODO
  return 5;
}
