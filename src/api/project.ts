import sleep from "./sleep";
import { Nil } from "./types"
import * as User from "./user"
import * as Branch from "./branch"
import * as Request from "../utils/request"

export const perPage = 15;

export interface BaseResult {
  name: string,
  author: string
  private: boolean,
  updatedAt: Date,
  createdAt: Date,
  description: string,
  mainBranch: string,
}

export interface FetchResult extends BaseResult {
  contributors: User.BaseResult[],
  branches: Branch.BaseResult[],
}

export async function fetch(user: string, project?: string | null): Promise<FetchResult | null> {
  if (project == null) return null;
  await sleep(500); // TODO
  return {
    name: "foo",
    author: "July",
    private: false,
    contributors: [{
      name: "Steel"
    }, {
      name: "July"
    }, { name: "Michel" }],
    branches: [{
      name: "main",
      updatedAt: new Date(),
    }, {
      name: "dev",
      updatedAt: new Date(),
    }, {
      name: "prod1",
      updatedAt: new Date(),
    }],
    mainBranch: "main",
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam pellentesque turpis eu libero elementum, mattis egestas nisi scelerisque. Nulla facilisi. Pellentesque lacinia felis non leo scelerisque tristique. Suspendisse bibendum quam quis ullamcorper lacinia. Ut nec semper enim, vitae efficitur felis. Curabitur scelerisque dignissim metus, at sagittis tortor scelerisque sit amet. Donec eu tincidunt justo.",
  };
  //
  return Request.json("api/project.php", {
    q: "getProject",
    user,
    project,
  })
}

export async function add(user: string, project: string, isPrivate: boolean, description: string | null): Promise<boolean> {
  await sleep(500);
  return true;
  //
  const response = await Request.post("api/project.php", {
    user,
    project,
    private:isPrivate,
    description: description ? null : description
  });
  if (response.ok) return true;
  if (response.status === 400) return false;
  return Request.exception(response);
}

export async function remove(user: string, project: string): Promise<void> {
  await sleep(500);
  //
  const response = await Request.delete_("api/project.php", {
    user, project
  });
  if (response.ok) return;
  return Request.exception(response);
}

export interface UpdateInput {
  private: boolean
  description?: string | null
  mainBranch: string
}

export async function edit(user: string, project: string, content: UpdateInput): Promise<boolean> {
  await sleep(500);
  return true;
  //
  const response = await Request.patch("api/project.php", {
    user,
    project,
    description: null,
    ...content,
  });
  if (response.ok) return true;
  if (response.status === 401) return false;
  return Request.exception(response);
}

export async function allOf(user: string, page: number): Promise<BaseResult[]> {
  await sleep(500);
  return [{
    author: "July",
    mainBranch: "main",
    private: true,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    name: "Nier: Automata",
    description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
  }, {
    author: "July",
    mainBranch: "main",
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    name: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }, {
    author: "July",
    mainBranch: "main",
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    name: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }, {
    author: "July",
    mainBranch: "main",
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    name: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }, {
    author: "July",
    mainBranch: "main",
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    name: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }]
  //
  return Request.json("api/project.php", {
    q: "fetchAllFromUser",
    user,
    first: perPage,
    after: perPage * page,
  });
}

export async function all(page: number): Promise<BaseResult[]> {
  await sleep(500);
  return [{
    mainBranch: "main",
    author: "July",
    private: true,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    name: "Nier: Automata",
    description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
  }, {
    mainBranch: "main",
    author: "Steel",
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    name: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }, {
    mainBranch: "main",
    author: "Michel",
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    name: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }, {
    mainBranch: "main",
    author: "Annie",
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    name: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }, {
    mainBranch: "main",
    author: "Bernard",
    private: false,
    updatedAt: new Date(),
    createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
    name: "Daft Punk",
    description: "Lorem ipsum dolor sit amet."
  }]
  //
  return Request.json("api/project.php", {
    q: "fetchAll",
    first: perPage,
    after: perPage * page,
  });
}

export async function find(user: string, project: string): Promise<boolean> {
  await sleep(500);
  return false;
  //
  return Request.json("api/project.php", {
    q: "find",
    user,
    project,
  })
}

export interface SearchResult {
  results: BaseResult[],
  count: number
}

export async function search(user: string, project: string, page: number): Promise<SearchResult> {
  await sleep(500);
  return {
    count: 42,
    results: [{
      mainBranch: "main",
      author: "July",
      private: true,
      updatedAt: new Date(),
      createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
      name: "Nier: Automata",
      description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit."
    }, {
      mainBranch: "main",
      author: "Steel",
      private: false,
      updatedAt: new Date(),
      createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
      name: "Daft Punk",
      description: "Lorem ipsum dolor sit amet."
    }, {
      mainBranch: "main",
      author: "Michel",
      private: false,
      updatedAt: new Date(),
      createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
      name: "Daft Punk",
      description: "Lorem ipsum dolor sit amet."
    }, {
      mainBranch: "main",
      author: "Annie",
      private: false,
      updatedAt: new Date(),
      createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
      name: "Daft Punk",
      description: "Lorem ipsum dolor sit amet."
    }, {
      mainBranch: "main",
      author: "Bernard",
      private: false,
      updatedAt: new Date(),
      createdAt: new Date(new Date().getTime() - 34 * 24 * 60 * 60 * 1011),
      name: "Daft Punk",
      description: "Lorem ipsum dolor sit amet."
    }]
  };
  //
  return Request.json("api/project.php", {
    q: "seek",
    user,
    project,
    first: perPage,
    after: perPage * page,
  })
}

export async function count(): Promise<number> {
  await sleep(500);
  return 52;
  // 
  return Request.json("api/project.php", { q: "count" });
}

export async function countOf(user: string): Promise<number> {
  await sleep(500);
  return 5;
  // 
  return Request.json("api/project.php", { q: "countFromUser" });
}
