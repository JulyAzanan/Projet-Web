import sleep from "./sleep";
import * as User from "./user"
import * as Request from "../utils/request"
import * as Score from "./score"

interface BaseResult {
  id: string,
  message: string,
  createdAt: Date,
  publisher: string,
}

export interface FetchResult extends Omit<BaseResult, "publisher"> {
  publisher: User.BaseResult,
  files: Score.AllResult[],
}

export async function fetch(user: string, project: string, branch: string, commit?: string | null): Promise<FetchResult | null> {
  if (commit == null) return null;
  await sleep(500);
  return {
    id: "bar",
    createdAt: new Date(),
    publisher: {
      name: "Steel",
    },
    message: "Hello world uwu",
    files: [{
      name: "file_3615",
      id: "dqzd48",
      message: "Pour tout n dans la vie",
      createdAt: new Date(new Date().getTime() - 34 * 60 * 1011),
    }, {
      name: "fack",
      id: "2qz56d",
      message: "Parfois la raison elle s'appelle pas q !",
      createdAt: new Date(new Date().getTime() - 35 * 60 * 1011),
    }, {
      name: "matez",
      id: "6zerfez",
      message: "Qu'est ce que je dou ?",
      createdAt: new Date(new Date().getTime() - 34 * 64 * 1011),
    }, {
      name: "hein",
      id: "dqzd48",
      message: "Pour tout n dans la vie",
      createdAt: new Date(new Date().getTime() - 34 * 60 * 1011),
    }, {
      name: "suff",
      id: "16z68c",
      message: "La dem est triv'!",
      createdAt: new Date(new Date().getTime() - 34 * 60 * 10151),
    }],
  };
  //
  return Request.json("api/commit.php", {
    q: "getCommit",
    user,
    project,
    branch,
    commit,
  })
}

export interface ScoreInput {
  name: string,
  content: string,
}

export interface ContentInput {
  message: string,
  scores: ScoreInput[]
}

export async function add(user: string, project: string, branch: string, content: ContentInput): Promise<void> {
  await sleep(500);
  return;
  //
  const response = await Request.post("api/commit.php", {
    user,
    project,
    branch,
    ...content
  });
  if (response.ok) return;
  return Request.exception(response);
}

export async function count(user: string, project: string, branch: string): Promise<number> {
  await sleep(500);
  return 52;
  // 
  return Request.json("api/commit.php", {
    q: "count",
    user,
    project,
    branch,
  });
}

export async function find(user: string, project: string, branch: string, commit: string): Promise<boolean> {
  await sleep(500);
  return false;
  //
  return Request.json("api/commit.php", {
    q: "find",
    user,
    project,
    branch,
    commit,
  })
}

export async function all(user: string, project: string, branch: string): Promise<BaseResult[]> {
  await sleep(500);
  return [{
    id: "dqzdeq",
    createdAt: new Date(),
    message: "Pour tout n dans la vie",
    publisher: "Steel",
  }, {
    id: "jiuyo",
    createdAt: new Date(),
    message: "Qu'est ce que je dou ?",
    publisher: "July",
  }, {
    id: "azerr",
    createdAt: new Date(),
    message: "Qu'est ce que je dou ?",
    publisher: "Steel",
  }, {
    id: "bguytu",
    createdAt: new Date(),
    message: "Pour tout n dans la vie",
    publisher: "Steel",
  }]
  //
  return Request.json("api/commit.php", {
    q: "fetchAll",
    user,
    project,
    branch,
  });
}

export async function download(user: string, project: string, branch: string, commit: string): Promise<Score.DownloadResult[]> {
  await sleep(500);
  return [{
    name: "file1",
    content: "dqsqsfgfdrg"
  }, {
    name: "file2",
    content: "dqsgfretsrtdrg"
  }, {
    name: "file3",
    content: "dqsgfdezrtttttttttrg"
  },{
    name: "file4",
    content: "dqsgfzerterttttttttttttdrg"
  },]
  //
  return Request.json("api/commit.php", {
    q: "download",
    user,
    project,
    branch,
    commit,
  });
}
