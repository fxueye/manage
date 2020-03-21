import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import ElementUI from "element-ui";
import "element-ui/lib/theme-chalk/index.css";
// import './assets/css/theme-green/index.css' // 浅绿色主题
import "./assets/css/icon.css";
import "babel-polyfill";
import { Plugin } from "vue-fragment";

Vue.use(Plugin);
Vue.config.productionTip = false;
Vue.use(ElementUI, {
  size: "small"
});

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount("#app");
