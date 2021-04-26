import sleep from "./sleep";
import * as User from "./user"

interface BaseResult {
  message: string,
  createdAt: Date,
}

export interface FetchResult extends BaseResult {
  publisher: User.BaseResult,
  files: {
    path: string,
    id: string,
    message: string,
    createdAt: Date,
  }[],
}

export async function fetch(user?: string, project?: string, branch?: string, commit?: string): Promise<FetchResult | null> {
  if (user == null || project == null|| branch == null || commit == null) return null;
  await sleep(500); // TODO
  return {
    createdAt: new Date(),
    publisher: {
      name: "Steel",
    },
    message: "Hello world uwu",
    files: [{
      path: "file_3615",
      id: "dqzd48",
      message: "Pour tout n dans la vie",
      createdAt: new Date(new Date().getTime() - 34 * 60 * 1011),
    },{
      path: "fack",
      id: "2qz56d",
      message: "Parfois la raison elle s'appelle pas q !",
      createdAt: new Date(new Date().getTime() - 35 * 60 * 1011),
    },{
      path: "matez",
      id: "6zerfez",
      message: "Qu'est ce que je dou ?",
      createdAt: new Date(new Date().getTime() - 34 * 64 * 1011),
    },{
      path: "hein",
      id: "dqzd48",
      message: "Pour tout n dans la vie",
      createdAt: new Date(new Date().getTime() - 34 * 60 * 1011),
    },{
      path: "suff",
      id: "16z68c",
      message: "La dem est triv'!",
      createdAt: new Date(new Date().getTime() - 34 * 60 * 10151),
    }],
   };
}