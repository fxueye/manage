<template>
  <el-button
    :size="size"
    :type="type"
    :icon="icon"
    :loading="loading"
    :disabled="disabled"
    @click="handleClick"
  >
    <slot></slot>
  </el-button>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "PermsButton",
  props: {
    icon: {
      // 按钮显示图标
      type: String,
      default: ""
    },
    size: {
      // 按钮尺寸
      type: String,
      default: "mini"
    },
    type: {
      // 按钮类型
      type: String,
      default: null
    },
    loading: {
      // 按钮加载标识
      type: Boolean,
      default: false
    },
    disabled: {
      // 按钮是否禁用
      type: Boolean,
      default: false
    },
    perms: {
      // 按钮权限标识，外部使用者传入
      type: String,
      default: null
    }
  },
  data() {
    return {};
  },
  computed: {
    ...mapGetters("auth", { permission: "permission" })
  },
  watch: {
    permission: function(val) {
      this.disabled = this.hasPermission(this.perms);
    }
  },
  methods: {
    handleClick: function() {
      // 按钮操作处理函数
      if (this.hasPermission(this.perms)) {
        this.$emit("click", {});
      } else {
        console.log("无权操作");
      }
    },

    hasPermission(perms) {
      for (let p of this.permission) {
        if (p.perms == perms) {
          return true;
        }
      }
      return false;
    }
  },
  mounted() {
    this.disabled = this.hasPermission(this.perms);
  }
};
</script>

<style scoped>
</style>