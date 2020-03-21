import { getMenus, save, del } from "@/api/menu";
const menu = {
  namespaced: true,
  state: {
    total_rows: 0,
    menus: []
  },
  mutations: {
    SET_MENUS: (state, menus) => {
      state.menus = menus;
    },
    UPDATE_MENU: (state, menu) => {
      state.menus = state.menus.map(item => {
        return item.id == menu.id ? menu : item;
      });
    },
    DEL_MENU: (state, id) => {
      state.menus = state.menus.filter(item => {
        return item.id != id;
      });
    },
    SET_TOTAL_ROWS: (state, total_rows) => {
      state.total_rows = total_rows;
    },
    ADD_MENU: (state, menu) => {
      state.menus.push(menu);
    }
  },
  actions: {
    getMenus({ commit }) {
      return new Promise((resolve, reject) => {
        getMenus()
          .then(res => {
            const { data } = res;
            commit("SET_MENUS", data.menus);
            resolve();
          })
          .catch(error => {
            reject();
          });
      });
    },
    save({ commit }, params) {
      const menu = params;

      return new Promise((resolve, reject) => {
        save(menu)
          .then(res => {
            const { data } = res;
            if (menu.id != data.menu.id) {
              commit("ADD_MENU", data.menu);
            } else {
              commit("UPDATE_MENU", data.menu);
            }
            resolve();
          })
          .catch(error => {
            reject();
          });
      });
    },
    del({ commit }, params) {
      const { id } = params;
      return new Promise((resolve, reject) => {
        del(id)
          .then(res => {
            const { data } = res;
            commit("DEL_MENU", data.id);
            resolve();
          })
          .catch(error => {
            reject();
          });
      });
    }
  },
  getters: {
    menus: (state, getters, rootState) => {
      let temp = state.menus.slice();
      temp.sort((a, b) => {
        return a.sort - b.sort;
      });
      let menus = [];

      let generate = function(menus, menu, pid, pname) {
        for (let i = 0, len = menus.length; i < len; i++) {
          let a = menus[i];
          if (a.parent_id === pid) {
            let m = {
              ...a,
              children: [],
              parent_name: pname
            };
            menu.push(m);
            generate(menus, m.children, a.id, a.name);
          }
        }
      };
      generate(temp, menus, "0", "");

      return menus;
    }
  }
};

export default menu;
