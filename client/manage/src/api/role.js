import request from "../utils/request";
export function getRoles(page, size, name) {
  return request({
    url: "/admin/role/list",
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
    url: "/admin/role/save",
    method: "post",
    data: user
  });
}
export function del(id) {
  return request({
    url: "/admin/role/del",
    method: "post",
    data: {
      id: id
    }
  });
}

export function menuIds(roleId) {
  return request({
    url: "/admin/role/menu_ids",
    method: "post",
    data: {
      role_id: roleId
    }
  });
}

export function saveRoleMenus(data) {
  return request({
    url: "/admin/role/save_role_menus",
    method: "post",
    data: data
  });
}
