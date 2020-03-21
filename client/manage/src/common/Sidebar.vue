<template>
  <div class="sidebar">
    <el-menu
      class="sidebar-el-menu"
      :default-active="onRoutes"
      :collapse="collapse"
      background-color="#324157"
      text-color="#bfcbd9"
      active-text-color="#20a0ff"
      unique-opened
      router
    >
      <MenuTree :items="items"></MenuTree>
    </el-menu>
  </div>
</template>

<script>
import bus from "../common/bus";
import { mapGetters } from "vuex";
import MenuTree from "@/components/MenuTree";
export default {
  data() {
    return {
      collapse: false,
      items: [
        {
          icon: "el-icon-lx-home",
          index: "/dashboard",
          title: "系统首页",
          subs: []
        }
      ]
    };
  },
  components: {
    MenuTree: MenuTree
  },
  computed: {
    ...mapGetters(["menu"]),
    onRoutes() {
      return this.$route.path.replace("/", "");
    }
  },
  mounted() {
    this.items.push(...this.menu);
    console.log(this.items);
  },
  created() {
    // 通过 Event Bus 进行组件间通信，来折叠侧边栏
    bus.$on("collapse", msg => {
      this.collapse = msg;
      bus.$emit("collapse-content", msg);
    });
  }
};
</script>

<style scoped>
.sidebar {
  display: block;
  position: absolute;
  left: 0;
  top: 70px;
  bottom: 0;
  overflow-y: scroll;
}
.sidebar::-webkit-scrollbar {
  width: 0;
}
.sidebar-el-menu:not(.el-menu--collapse) {
  width: 250px;
}
.sidebar > ul {
  height: 100%;
}
</style>
