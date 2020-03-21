const getters = {
  token: state => {
    return state.auth.token;
  },
  auth: state => {
    return state.auth;
  },
  menu: state => {
    let temp = state.auth.permission.slice();
    temp.sort((a, b) => {
      return a.sort - b.sort;
    });
    let menu = [];

    let generate = function(menus, menu, pid) {
      for (let i = 0, len = menus.length; i < len; i++) {
        let a = menus[i];
        if (a.parent_id === pid && a.type != "2") {
          let m = {
            icon: a.icon,
            title: a.name,
            index: a.url == "" ? a.id : a.url,
            subs: []
          };
          menu.push(m);
          generate(menus, m.subs, a.id);
        }
      }
    };
    generate(temp, menu, "0");

    return menu;
  }
};
export default getters;
