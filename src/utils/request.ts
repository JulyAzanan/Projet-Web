import { notifyError } from "../utils/error";

const connection = process.env.NODE_ENV === 'development'
  ? "http://localhost:8888/"
  : "/"

function getUser(): string {
  return localStorage.getItem("user") ?? ""
}

function getPassword(): string {
  return localStorage.getItem("password") ?? ""
}

function getHeaders(): Headers {
  const headers = new Headers();
  headers.set('Authorization', 'Basic ' + btoa(`${getUser()}:${getPassword()}`));
  return headers;
}

function createRequest(method: string) {
  return async (url: string, body: BodyInit, contentType = "application/x-www-form-urlencoded") => {
    const headers = getHeaders();
    headers.set('Content-Type', contentType);
    return fetch(connection + url, {
      method,
      headers,
      body,
    });
  }
}

export async function exception(response: Response): Promise<never> {
  const text = await response.text();
  notifyError("Erreur lors de l'accès à la base de données", text)
  throw new Error(`${response.statusText}: ${text}`)
}

export async function get(url: string) {
  return fetch(connection + url, {
    method: "GET",
    headers: getHeaders(),
  });
}

export const post = createRequest("POST");
export const delete_ = createRequest("DELETE");
export const patch = createRequest("PATCH");
