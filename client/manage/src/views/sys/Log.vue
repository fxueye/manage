<template>
  <div>
    <div class="crumbs">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item>
          <i class="el-icon-lx-cascades"></i> 系统日志
        </el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="container">
      <div class="handle-box">
        <el-input v-model="query.name" placeholder="创建人" class="handle-input mr10"></el-input>
        <perms-button
          type="primary"
          perms="sys:role:select"
          icon="el-icon-search"
          @click="handleSearch"
        >搜索</perms-button>
      </div>
      <el-table :data="logs" border>
        <el-table-column prop="id" label="ID" width="55" align="center"></el-table-column>
        <el-table-column prop="method" label="请求方法"></el-table-column>
        <el-table-column show-overflow-tooltip prop="params" label="请求参数"></el-table-column>
        <el-table-column prop="ip" label="ip"></el-table-column>
        <el-table-column prop="time" label="执行时间"></el-table-column>
        <el-table-column prop="create_by" label="创建人"></el-table-column>

        <el-table-column
          show-overflow-tooltip
          prop="create_time"
          :formatter="timestampFormat"
          label="创建时间"
        ></el-table-column>
      </el-table>
      <div class="pagination">
        <el-pagination
          background
          layout="total, prev, pager, next"
          :current-page="query.page"
          :page-size="query.size"
          :total="totalRows"
          @current-change="handlePageChange"
        ></el-pagination>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import utils from "@/utils/utils";
import PermsButton from "@/components/PermsButton";
export default {
  // name: "role",
  data() {
    return {
      query: {
        page: 1,
        size: 20
      }
    };
  },
  components: {
    PermsButton: PermsButton
  },
  computed: {
    ...mapGetters("log", { logs: "logs", totalRows: "total_rows" })
  },
  created() {
    this.getData();
  },
  methods: {
    getData() {
      this.$store
        .dispatch("log/getLogs", this.query)
        .then(res => {})
        .catch(err => {});
    },
    // 触发搜索按钮
    handleSearch() {
      console.log(this.query);
      this.$set(this.query, "page", 1);
      this.getData();
    },
    // 删除操作
    handleDelete(index, row) {
      // 二次确认删除
      this.$confirm("确定要删除吗？", "提示", {
        type: "warning"
      })
        .then(() => {
          return this.$store.dispatch("role/del", { id: row.id });
        })
        .then(res => {
          this.$message.success("删除成功");
          this.currentRoleMenus = [];
          this.$refs.menuTree.setCheckedKeys(this.currentRoleMenus);
          this.selectRole = {};
        })
        .catch(() => {});
    },
    handleAdd() {
      this.dataForm = {
        id: "",
        name: "",
        remark: ""
      };
      this.editVisible = true;
      this.operation = true;
    },
    handleEdit(index, row) {
      this.idx = index;
      this.dataForm = { ...row };
      this.editVisible = true;
      this.operation = false;
    },

    handlePageChange(val) {
      this.$set(this.query, "page", val);
      this.getData();
    },
    timestampFormat(row, column, cellValue, index) {
      return utils.timestampFormat(cellValue, "YYYY-mm-dd HH:MM:SS");
    }
  }
};
</script>

<style scoped>
.handle-box {
  margin-bottom: 20px;
}

.handle-select {
  width: 120px;
}

.handle-input {
  width: 300px;
  display: inline-block;
}

.red {
  color: #ff0000;
}
.mr10 {
  margin-right: 10px;
}
.font-box {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
}
</style>
