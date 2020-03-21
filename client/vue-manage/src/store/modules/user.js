import { getToken, setToken, removeToken } from '@/utils/auth'
import {login} from '@/api/user'
const user = {
    state: {
        token: getToken(),
        user: ''
    },
    mutations: {
        SET_TOKEN: (state, token) => {
            state.token = token
        },
        SET_USER: (state, user) => {
            state.user = user
        }
    },
    actions: {
        login ({ commit }, userInfo) {
            const {username,password}= userInfo
            return new Promise((resolve, reject) => {
                login(username,password).then(res=>{
                    const {data}  = res;
                    commit('SET_TOKEN',data.token);
                    setToken(data.token);
                    resolve();
                }).catch(error=>{
                    reject(error);
                })
            })
        },
        logout ({ commit }) {
            return new Promise((resolve, reject) => {
                
            })
        },
        getUser ({ commit, state }) {
            return new Promise((resolve, reject) => {
              
            })
        }
    }
}
export default user