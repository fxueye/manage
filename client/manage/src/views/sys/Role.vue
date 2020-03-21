<template>
  <div>
    <div class="crumbs">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item>
          <i class="el-icon-lx-cascades"></i> 角色管理
        </el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="container">
      <div class="handle-box">
        <el-button type="primary" class="mr10" @click="handleAdd">添加角色</el-button>

        <el-input v-model="query.name" placeholder="角色名" class="handle-input mr10"></el-input>
        <el-button type="primary" icon="el-icon-search" @click="handleSearch">搜索</el-button>
      </div>
      <el-table
        :data="roles"
        border
        highlight-current-row
        @current-change="handleCurrentChange"
        @selection-change="handleSelectionChange"
      >
        <el-table-column prop="id" label="ID" width="55" align="center"></el-table-column>
        <el-table-column prop="name" label="角色名"></el-table-column>
        <el-table-column prop="remark" label="备注"></el-table-column>
        <el-table-column prop="create_by" label="创建人"></el-table-column>
        <el-table-column
          show-overflow-tooltip
          prop="create_time"
          :formatter="timestampFormat"
          label="创建时间"
        ></el-table-column>
        <el-table-column label="操作" width="180" fixed="right" align="center">
          <template slot-scope="scope">
            <el-button
              type="text"
              icon="el-icon-edit"
              @click="handleEdit(scope.$index, scope.row)"
            >编辑</el-button>
            <el-button
              type="text"
              icon="el-icon-delete"
              class="red"
              @click="handleDelete(scope.$index, scope.row)"
            >删除</el-button>
          </template>
        </el-table-column>
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

      <div>
        <el-tabs value="first">
          <el-tab-pane label="角色权限" name="first"></el-tab-pane>
        </el-tabs>
        <el-tree
          :data="menus"
          size="mini"
          show-checkbox
          node-key="id"
          :props="defaultProps"
          ref="menuTree"
          :render-content="renderContent"
          :check-strictly="true"
          @check-change="handleMenuCheckChange"
        ></el-tree>
        <el-tabs>
          <el-tab-pane></el-tab-pane>
        </el-tabs>
        <div class="font-box">
          <div style>
            <el-checkbox
              v-model="checkAll"
              @change="handleCheckAll"
              :disabled="selectRole.id == null"
            >
              <b>全选</b>
            </el-checkbox>
          </div>
          <div style>
            <el-button
              type="primary"
              class="mr10"
              @click="resetSelection"
              :disabled="selectRole.id == null"
            >重置</el-button>

            <el-button
              type="primary"
              class="mr10"
              @click="submitAuthForm"
              :loading="authLoading"
              :disabled="selectRole.id == null"
            >提交</el-button>
          </div>
        </div>
      </div>
    </div>

    <!-- 编辑弹出框 -->
    <el-dialog :title="operation ? '新增': '编辑'" :visible.sync="editVisible" width="30%">
      <el-form ref="dataForm" :rules="formRules" :model="dataForm" label-width="70px">
        <el-form-item label="角色" prop="name">
          <el-input v-model="dataForm.name"></el-input>
        </el-form-item>
        <el-form-item label="备注" prop="remark">
          <el-input v-model="dataForm.remark"></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="editVisible = false">取 消</el-button>
        <el-button type="primary" @click="submitForm">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import utils from "@/utils/utils";
import { menuIds, saveRoleMenus } from "@/api/role";
export default {
  // name: "role",
  data() {
    return {
      query: {
        page: 1,
        size: 10
      },
      operation: false, // true:新增, false:编辑
      editVisible: false,
      dataForm: {},
      formRules: {
        name: [
          { required: true, message: "请输入角色名", trigger: "blur" },
          { min: 3, max: 20, message: "长度在 3 到 20 个字符", trigger: "blur" }
        ]
      },
      authLoading: false,
      selectRole: {},
      checkAll: false,
      currentRoleMenus: [],
      defaultProps: {
        children: "children",
        label: "name"
      },
      multipleSelection: []
    };
  },
  computed: {
    ...mapGetters("role", { roles: "roles", totalRows: "total_rows" }),
    ...mapGetters("menu", { menus: "menus" })
  },
  created() {
    this.getData();
  },
  methods: {
    getData() {
      this.$store
        .dispatch("role/getRoles", this.query)
        .then(res => {})
        .catch(err => {});
      this.$store
        .dispatch("menu/getMenus")
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
    // 多选操作
    handleSelectionChange(val) {
      this.multipleSelection = val;
    },
    handleCurrentChange(val) {
      this.selectRole = val;
      menuIds(this.selectRole.id)
        .then(res => {
          const { data } = res;
          this.currentRoleMenus = data.menu_ids;
          this.$refs.menuTree.setCheckedKeys(this.currentRoleMenus);
        })
        .catch(error => {});
    },

    // 角色菜单授权提交
    submitAuthForm() {
      let roleId = this.selectRole.id;
      // if ("1" == this.selectRole.id) {
      //   this.$message({
      //     message: "超级管理员拥有所有菜单权限，不允许修改！",
      //     type: "error"
      //   });
      //   return;
      // }
      this.authLoading = true;
      let checkedNodes = this.$refs.menuTree.getCheckedKeys(false, true);
      let data = {
        role_id: roleId,
        menu_ids: checkedNodes
      };
      saveRoleMenus(data)
        .then(res => {
          this.$message.success("操作成功！");
          this.authLoading = false;
          this.currentRoleMenus = checkedNodes;
          this.$store
            .dispatch("auth/permission")
            .then(res => {})
            .catch(error => {});
        })
        .catch(error => {
          this.$message.error("操作失败！");
          this.authLoading = false;
        });
    },

    // 重置选择
    resetSelection() {
      this.checkAll = false;
      this.$refs.menuTree.setCheckedKeys(this.currentRoleMenus);
    },
    // 全选操作
    handleCheckAll() {
      if (this.checkAll) {
        let allMenus = [];
        this.checkAllMenu(this.menus, allMenus);
        this.$refs.menuTree.setCheckedNodes(allMenus);
      } else {
        this.$refs.menuTree.setCheckedNodes([]);
      }
    },
    // 递归全选
    checkAllMenu(menuData, allMenus) {
      menuData.forEach(menu => {
        allMenus.push(menu);
        if (menu.children) {
          this.checkAllMenu(menu.children, allMenus);
        }
      });
    },
    // 树节点选择监听
    handleMenuCheckChange(data, check, subCheck) {
      if (check) {
        // 节点选中时同步选中父节点
        let parent_id = data.parent_id;
        if (parent_id != "") {
          this.$refs.menuTree.setChecked(parent_id, true, false);
        }
      } else {
        // 节点取消选中时同步取消选中子节点
        if (data.children && data.children.length > 0) {
          data.children.forEach(element => {
            this.$refs.menuTree.setChecked(element.id, false, false);
          });
        }
      }
    },
    submitForm() {
      this.editVisible = false;
      this.$refs.dataForm.validate(valid => {
        if (valid) {
          this.$store
            .dispatch("role/save", this.dataForm)
            .then(res => {
              this.$message.success("操作成功！");
            })
            .catch(error => {
              this.$message.error("操作失败！");
            });
        } else {
          return false;
        }
      });
    },
    renderContent(h, { node, data, store }) {
      return (
        <div>
          <span style="text-algin:center;margin-right:80px;">{data.name}</span>
          <span style="text-algin:center;margin-right:80px;">
            <el-tag
              type={
                data.type === "0" ? "" : data.type === "1" ? "success" : "info"
              }
              size="small"
            >
              {data.type === "0" ? "目录" : data.type === "1" ? "菜单" : "按钮"}
            </el-tag>
          </span>
          <span style="text-algin:center;margin-right:80px;">
            {" "}
            <i class={data.icon}></i>
          </span>
          <span style="text-algin:center;margin-right:80px;">
            {data.parent_name ? data.parent_name : "顶级菜单"}
          </span>
          <span style="text-algin:center;margin-right:80px;">
            {data.url ? data.url : "\t"}
          </span>
        </div>
      );
    },
    handlePageChange(val) {
      this.$set(this.query, "page", val);
      this.getData();
    },
    timestampFormat(row, column, cellValue, index) {
      return utils.timestampFormat(cellValue, "YYYY-mm-dd HH:MM:SS");
    },
    statusFormat(row, column, cellValue, index) {
      return cellValue == "0" ? "正常" : "禁用";
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
