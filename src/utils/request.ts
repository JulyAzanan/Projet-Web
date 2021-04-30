import { notifyError } from "./notification";
import store from "@/app/store"

const connection = process.env.NODE_ENV === 'production'
  ? "/~mael.acier/musegit/"
  : "http://localhost:8888/"

function getUser(): string {
  return localStorage.getItem("user") ?? ""
}

function getPassword(): string {
  return localStorage.getItem("password") ?? ""
}

function getHeaders(url: string): Headers {
  const headers = new Headers();
  if (store.state.loggedIn || url.split("?").pop() === "q=login") headers.set('Authorization', 'Basic ' + btoa(`${getUser()}:${getPassword()}`));
  return headers;
}

function createRequest(method: string) {
  // eslint-disable-next-line
  return async (url: string, body: any): Promise<Response> => {
    const headers = getHeaders(url);
    headers.set('Content-Type', "application/json");
    return fetch(connection + url, {
      method,
      headers,
      body: JSON.stringify(body),
    });
  }
}

export async function exception(response: Response): Promise<never> {
  const text = await response.text();
  notifyError("Erreur lors de l'accès à la base de données", text)
  throw new Error(`${response.statusText}: ${text}`)
}

export async function get(url: string): Promise<Response> {
  return fetch(connection + url, {
    method: "GET",
    headers: getHeaders(url),
  });
}

export const post = createRequest("POST");
export const delete_ = createRequest("DELETE");
export const patch = createRequest("PATCH");

// eslint-disable-next-line
export async function json(url: string, params?: Record<string, any>): Promise<any> {
  const response = await get(url + (params ? `?${new URLSearchParams(params)}` : ""));
  if (response.ok) return response.json();
  return exception(response);
}
