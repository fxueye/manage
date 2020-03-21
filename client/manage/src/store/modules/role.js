import { getRoles, save, del } from "@/api/role";
const role = {
  namespaced: true,
  state: {
    total_rows: 0,
    roles: []
  },
  mutations: {
    SET_ROLES: (state, roles) => {
      state.roles = roles;
    },
    UPDATE_ROLE: (state, role) => {
      state.roles = state.roles.map(item => {
        return item.id == role.id ? role : item;
      });
    },
    DEL_ROLE: (state, id) => {
      state.roles = state.roles.filter(item => {
        return item.id != id;
      });
    },
    SET_TOTAL_ROWS: (state, total_rows) => {
      state.total_rows = total_rows;
    },
    ADD_ROLE: (state, role) => {
      state.roles.push(role);
    }
  },
  actions: {
    getRoles({ commit }, params) {
      const { page, size, name } = params;
      return new Promise((resolve, reject) => {
        getRoles(page, size, name)
          .then(res => {
            const { data } = res;
            commit("SET_TOTAL_ROWS", data.total_rows);
            commit("SET_ROLES", data.roles);
            resolve();
          })
          .catch(error => {
            reject();
          });
      });
    },
    save({ commit }, params) {
      const role = params;

      return new Promise((resolve, reject) => {
        save(role)
          .then(res => {
            const { data } = res;
            if (role.id != data.role.id) {
              commit("SET_TOTAL_ROWS", data.total_rows);
              commit("ADD_ROLE", data.role);
            } else {
              commit("UPDATE_ROLE", data.role);
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
            commit("DEL_ROLE", data.id);
            commit("SET_TOTAL_ROWS", data.total_rows);
            resolve();
          })
          .catch(error => {
            reject();
          });
      });
    }
  },
  getters: {
    roles: (state, getters, rootState) => {
      return state.roles;
    },
    total_rows: (state, getters, rootState) => {
      return state.total_rows;
    }
  }
};

export default role;
