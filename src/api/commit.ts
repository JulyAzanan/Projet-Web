import sleep from "./sleep";

interface BaseResult {
  author: string,
  message: string,
  createdAt: Date,
}

export interface FetchResult extends BaseResult {
  files: {
    path: string,
    commitID: string,
    commitMessage: string,
    createdAt: Date,
  }[],
}

export async function fetch(user?: string, project?: string, branch?: string, commit?: string): Promise<FetchResult | null> {
  if (user == null || project == null|| branch == null || commit == null) return null;
  await sleep(500); // TODO
  return {
    createdAt: new Date(),
    author: "Steel",
    message: "Hello world uwu",
    files: [{
      path: "file_3615",
      commitID: "dqzd48",
      commitMessage: "Pour tout n dans la vie",
      createdAt: new Date(new Date().getTime() - 34 * 60 * 1011),
    },{
      path: "fack",
      commitID: "2qz56d",
      commitMessage: "Parfois la raison elle s'appelle pas q !",
      createdAt: new Date(new Date().getTime() - 35 * 60 * 1011),
    },{
      path: "matez",
      commitID: "6zerfez",
      commitMessage: "Qu'est ce que je dou ?",
      createdAt: new Date(new Date().getTime() - 34 * 64 * 1011),
    },{
      path: "hein",
      commitID: "dqzd48",
      commitMessage: "Pour tout n dans la vie",
      createdAt: new Date(new Date().getTime() - 34 * 60 * 1011),
    },{
      path: "suff",
      commitID: "16z68c",
      commitMessage: "La dem est triv'!",
      createdAt: new Date(new Date().getTime() - 34 * 60 * 10151),
    }],
   };
}