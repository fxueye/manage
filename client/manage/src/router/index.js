import Vue from "vue";
import VueRouter from "vue-router";
import store from "@/store";
import { getToken } from "@/utils/auth";

Vue.use(VueRouter);

const routes = [
  {
    path: "/",
    redirect: "/dashboard"
  },
  {
    path: "/",
    component: () => import("@/common/Home"),
    meta: { title: "首页" },
    children: [
      {
        path: "/dashboard",
        component: () => import("@/views/Dashboard"),
        meta: { title: "系统首页" }
      }
      // {
      //   path: "/sys/user",
      //   component: () => import("@/views/sys/User"),
      //   meta: { title: "用户管理" }
      // }
    ]
  },
  {
    path: "/login",
    component: () => import("@/views/Login"),
    meta: { title: "登录" }
  }
];

const router = new VueRouter({
  routes
});

const WhiteList = ["/login"];

router.beforeEach(async (to, from, next) => {
  document.title = `${to.meta.title} | manage`;
  const token = getToken();
  if (token) {
    if (to.path === "/login") {
      next({ path: "/" });
    } else {
      const userLoaded = store.getters.auth.loaded;
      if (userLoaded) {
        next();
      } else {
        let ret = await store.dispatch("auth/getUser");
        ret = await store.dispatch("auth/permission");
        addRouters(ret);
        console.log(ret);
        if (!ret) {
          await store.dispatch("auth/resetToken");
          next(`login?redirect=${to.path}`);
        } else {
          next();
        }
      }
    }
  } else {
    if (WhiteList.indexOf(to.path) !== -1) {
      next();
    } else {
      next(`login?redirect=${to.path}`);
    }
  }
});

function addRouters(permission) {
  for (let i = 0, len = permission.length; i < len; i++) {
    let p = permission[i];
    if (p.type === "1") {
      let path = p.url;
      let arr = path.split("/");
      let vuePath = "";
      for (let j = 0, l = arr.length; j < l; j++) {
        if (j == l - 1) {
          vuePath += arr[j].slice(0, 1).toUpperCase() + arr[j].slice(1);
        } else {
          vuePath += arr[j] + "/";
        }
      }
      console.log(vuePath);
      console.log(arr);

      let route = {
        path: path,
        component: () => import(`@/views${vuePath}`),
        meta: { title: `${p.name}` }
      };
      console.log(route);
      routes[1].children.push(route);
    }
  }

  router.addRoutes(routes);
}

export default router;
