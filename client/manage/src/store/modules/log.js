import { getLogs } from "@/api/log";

const user = {
  namespaced: true,
  state: {
    total_rows: 0,
    logs: []
  },
  mutations: {
    SET_LOGS: (state, logs) => {
      state.logs = logs;
    },
    SET_TOTAL_ROWS: (state, total_rows) => {
      state.total_rows = total_rows;
    }
  },
  actions: {
    getLogs({ commit }, params) {
      const { page, size, name } = params;
      return new Promise((resolve, reject) => {
        getLogs(page, size, name)
          .then(res => {
            const { data } = res;
            commit("SET_TOTAL_ROWS", data.total_rows);
            commit("SET_LOGS", data.logs);
            resolve();
          })
          .catch(error => {
            reject();
          });
      });
    }
  },
  getters: {
    logs: (state, getters, rootState) => {
      return state.logs;
    },
    total_rows: (state, getters, rootState) => {
      return state.total_rows;
    }
  }
};
export default user;
