import request from "../utils/request";
export function getUsers(page = "", size = "", name = "") {
  return request({
    url: "/admin/user/list",
    method: "post",
    data: {
      page: page,
      size: size,
      name: name
    }
  });
}
export function save(user) {
  return request({
    url: "/admin/user/save",
    method: "post",
    data: user
  });
}
export function del(id) {
  return request({
    url: "/admin/user/del",
    method: "post",
    data: {
      id: id
    }
  });
}

export function batchDel(ids) {
  return request({
    url: "/admin/user/batch_del",
    method: "post",
    data: ids
  });
}
