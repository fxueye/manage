import request from "../utils/request";

export function login(name, password) {
  return request({
    url: "/admin/auth/login",
    method: "post",
    data: {
      name: name,
      password: password
    }
  });
}

export function getUser() {
  return request({
    url: "/admin/auth/user",
    method: "post",
    data: {}
  });
}

export function permission() {
  return request({
    url: "/admin/auth/permission",
    method: "post",
    data: {}
  });
}
