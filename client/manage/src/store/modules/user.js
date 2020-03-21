import { getUsers, save, del, batchDel } from "@/api/user";

const user = {
  namespaced: true,
  state: {
    total_rows: 0,
    users: []
  },
  mutations: {
    SET_USERS: (state, users) => {
      state.users = users;
    },
    UPDATE_USER: (state, user) => {
      state.users = state.users.map(item => {
        return item.id == user.id ? user : item;
      });
    },
    DEL_USER: (state, id) => {
      state.users = state.users.filter(item => {
        return item.id != id;
      });
    },
    SET_TOTAL_ROWS: (state, total_rows) => {
      state.total_rows = total_rows;
    },
    ADD_USER: (state, user) => {
      state.users.push(user);
    }
  },
  actions: {
    getUsers({ commit }, params) {
      const { page, size, name } = params;
      return new Promise((resolve, reject) => {
        getUsers(page, size, name)
          .then(res => {
            const { data } = res;
            commit("SET_TOTAL_ROWS", data.total_rows);
            commit("SET_USERS", data.users);
            resolve();
          })
          .catch(error => {
            reject();
          });
      });
    },
    save({ commit }, params) {
      const user = params;

      return new Promise((resolve, reject) => {
        save(user)
          .then(res => {
            const { data } = res;
            if (user.id != data.user.id) {
              commit("SET_TOTAL_ROWS", data.total_rows);
              commit("ADD_USER", data.user);
            } else {
              commit("UPDATE_USER", data.user);
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
            commit("DEL_USER", data.id);
            commit("SET_TOTAL_ROWS", data.total_rows);
            resolve();
          })
          .catch(error => {
            reject();
          });
      });
    },
    batchDel({ commit }, params) {
      return new Promise((resolve, reject) => {
        batchDel(params)
          .then(res => {
            const { data } = res;
            let ids = data.ids;
            for (let id of ids) {
              commit("DEL_USER", id);
            }
            resolve();
          })
          .catch(error => {
            reject();
          });
      });
    }
  },
  getters: {
    users: (state, getters, rootState) => {
      return state.users;
    },
    total_rows: (state, getters, rootState) => {
      return state.total_rows;
    }
  }
};
export default user;
