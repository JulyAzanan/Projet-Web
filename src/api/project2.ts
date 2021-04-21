import sleep from "./sleep"

interface BaseResult {
  private: boolean,
  updatedAt: Date,
  createdAt: Date,
  description: string,
}

export async function add(_author: string, _project: string, _isPrivate: boolean, _description: string): Promise<void> {
  // TODO
  return;
}

export async function remove(_author: string, _project: string): Promise<void> {
  // TODO
  return;
}

export interface FindResult extends BaseResult {
  contributors: string[],
  mainBranch: string,
}

export async function find(user?: string, project?: string): Promise<FindResult | null> {
  if (user == null || project == null) return null;
  await sleep(500); // TODO
  return {
    private: false,
    contributors: ["Steel", "July", "Michel"],
    mainBranch: "main",
    description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam pellentesque turpis eu libero elementum, mattis egestas nisi scelerisque. Nulla facilisi. Pellentesque lacinia felis non leo scelerisque tristique. Suspendisse bibendum quam quis ullamcorper lacinia. Ut nec semper enim, vitae efficitur felis. Curabitur scelerisque dignissim metus, at sagittis tortor scelerisque sit amet. Donec eu tincidunt justo.",
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
  };
}

export interface FetchAllResult extends BaseResult {
  author: string,
  project: string,
}

export async function fetchAll(first: number, after: number): Promise<FetchAllResult[]> {
  await sleep(500); // TODO
  return [{
    private: true,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    author: "July",
    project: "Nier: Automata",
    description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
  },
  {
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    author: "Steel",
    project: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }, {
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    author: "Steel",
    project: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }, {
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    author: "Steel",
    project: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }, {
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    author: "Steel",
    project: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }]
}

export interface FetchAllFromResult extends BaseResult {
  project: string,
}

export async function fetchAllFrom(first: number, after: number, user?: string): Promise<FetchAllFromResult[]> {
  if (user == null) return [];
  await sleep(500); // TODO
  return [{
    private: true,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    project: "Nier: Automata",
    description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
  },
  {
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    project: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }, {
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    project: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }, {
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    project: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }, {
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    project: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }];
}

export async function count(): Promise<number> {
  await sleep(500); // TODO
  return 5;
}

export async function countFrom(user?: string): Promise<number> {
  if (user == null) return 0;
  await sleep(500); // TODO
  return 5;
}
