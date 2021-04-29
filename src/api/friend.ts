import sleep from "./sleep";
import * as User from "./user"
import * as Request from "../utils/request"

export const friendPerPage = 6;

export async function add(follower: string, following: string): Promise<boolean> {
 /*  await sleep(500);
  return true; */
  // 
  const response = await Request.post("api/friend.php", {
    follower, following
  });
  if (response.ok) return true;
  if (response.status === 400) return false;
  Request.exception(response);
  return false;
}

export async function remove(follower: string, following: string): Promise<boolean> {
  /* await sleep(500);
  return true; */
  // 
  const response = await Request.delete_("api/friend.php", {
    follower, following
  });
  if (response.ok) return true;
  Request.exception(response);
  return false;
}

export async function all(page: number, user: string): Promise<User.AllResult[]> {
  /* return User.all(page); */
  // 
  return Request.json("api/friend.php", {
    q: "fetchAll",
    first: friendPerPage,
    after: friendPerPage * (page - 1),
    user,
  });
}

export async function count(user: string): Promise<number> {
  /* await sleep(500);
  return 43; */
  // 
  return Request.json("api/friend.php", { q: "count", user });
}

export async function isFriend(follower: string, following: string): Promise<boolean> {
  /* await sleep(500);
  return true; */
  // 
  return Request.json("api/friend.php", {
    q: "find",
    follower,
    following
  });
}
