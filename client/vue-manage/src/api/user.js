import request from '../utils/request';

export function login(name,password){
    return request({
        url:'/login',
        method:'post',
        data:{
            name:name,
            password,password
        }
    })
}