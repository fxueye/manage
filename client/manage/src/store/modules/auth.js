import { getToken, setToken, removeToken } from "@/utils/auth";
import { login, getUser, permission } from "@/api/auth";
const auth = {
  namespaced: true,
  state: {
    token: getToken(),
    user: {},
    permission: {},
    loaded: false
  },
  mutations: {
    SET_TOKEN: (state, token) => {
      state.token = token;
    },
    SET_SUER: (state, user) => {
      state.user = user;
      state.loaded = user.id ? true : false;
    },
    SET_PRERMISSION: (state, permission) => {
      state.permission = permission;
    }
  },
  actions: {
    login({ commit }, userInfo) {
      const { username, password } = userInfo;
      return new Promise((resolve, reject) => {
        login(username, password)
          .then(res => {
            const { data } = res;
            commit("SET_TOKEN", data.token);
            setToken(data.token);
            resolve();
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    logout({ commit }) {
      return new Promise((resolve, reject) => {
        removeToken();
        commit("SET_SUER", {});
        resolve();
      });
    },
    getUser({ commit }) {
      return new Promise((resolve, reject) => {
        getUser()
          .then(res => {
            const { data } = res;
            commit("SET_SUER", data.user);
            resolve(data.user);
          })
          .catch(() => {
            reject(false);
          });
      });
    },
    resetToken({ commit }) {
      return new Promise((resolve, reject) => {
        removeToken();
        commit("SET_SUER", {});
        resolve();
      });
    },
    permission({ commit }) {
      return new Promise((resolve, reject) => {
        permission()
          .then(res => {
            const { data } = res;
            commit("SET_PRERMISSION", data.permission);
            resolve(data.permission);
          })
          .catch(() => {
            reject(false);
          });
      });
    }
  },
  getters: {
    permission: (state, getters, rootState) => {
      return state.permission.filter(item => {
        return item.type == "2";
      });
    }
  }
};
export default auth;
