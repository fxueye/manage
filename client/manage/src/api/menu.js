import request from "../utils/request";
export function getMenus() {
  return request({
    url: "/admin/menu/list",
    method: "post",
    data: {}
  });
}
export function save(user) {
  return request({
    url: "/admin/menu/save",
    method: "post",
    data: user
  });
}
export function del(id) {
  return request({
    url: "/admin/menu/del",
    method: "post",
    data: {
      id: id
    }
  });
}
