<template>
  <div>
    <div class="crumbs">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item>
          <i class="el-icon-lx-cascades"></i> 用户管理
        </el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="container">
      <div class="handle-box">
        <el-button type="primary" icon="el-icon-delete" class="mr10" @click="handleBatchDel">批量删除</el-button>
        <perms-button type="primary" perms="sys:user:add" class="mr10" @click="handleAdd">添加用户</perms-button>

        <el-input v-model="query.name" placeholder="用户名" class="handle-input mr10"></el-input>
        <perms-button
          type="primary"
          perms="sys:user:select"
          icon="el-icon-search"
          @click="handleSearch"
        >搜索</perms-button>
      </div>
      <el-table :data="users" border @selection-change="handleSelectionChange">
        <el-table-column type="selection" width="55" align="center"></el-table-column>
        <el-table-column prop="id" label="ID" width="55" align="center"></el-table-column>
        <el-table-column prop="name" label="用户名"></el-table-column>
        <el-table-column show-overflow-tooltip prop="email" label="邮箱"></el-table-column>
        <el-table-column show-overflow-tooltip prop="mobile" label="手机"></el-table-column>
        <el-table-column prop="status" :formatter="statusFormat" label="状态"></el-table-column>
        <el-table-column prop="create_by" label="创建者"></el-table-column>
        <el-table-column
          show-overflow-tooltip
          prop="create_time"
          :formatter="timestampFormat"
          label="创建时间"
        ></el-table-column>
        <el-table-column
          prop="last_login_time"
          show-overflow-tooltip
          :formatter="timestampFormat"
          label="登录时间"
        ></el-table-column>
        <el-table-column prop="last_login_ip" show-overflow-tooltip label="登录IP"></el-table-column>
        <el-table-column label="操作" width="180" fixed="right" align="center">
          <template slot-scope="scope">
            <perms-button
              type="text"
              icon="el-icon-edit"
              perms="sys:user:edit"
              @click="handleEdit(scope.$index, scope.row)"
            >编辑</perms-button>
            <perms-button
              type="text"
              icon="el-icon-delete"
              perms="sys:user:del"
              @click="handleDelete(scope.$index, scope.row)"
            >删除</perms-button>
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
    </div>

    <!-- 编辑弹出框 -->
    <el-dialog :title="operation ? '新增' : '编辑'" :visible.sync="editVisible" width="30%">
      <el-form ref="dataForm" :rules="formRules" :model="dataForm" label-width="70px">
        <el-form-item label="用户名" prop="name">
          <el-input v-model="dataForm.name"></el-input>
        </el-form-item>
        <el-form-item label="密码" prop="password">
          <el-input v-model="dataForm.password"></el-input>
        </el-form-item>
        <el-form-item label="邮箱" prop="email">
          <el-input v-model="dataForm.email"></el-input>
        </el-form-item>
        <el-form-item label="手机" prop="mobile">
          <el-input v-model="dataForm.mobile"></el-input>
        </el-form-item>
        <el-form-item label="状态">
          <el-select v-model="dataForm.status" placeholder="请选择" class="handle-select mr10">
            <el-option key="1" label="正常" value="0"></el-option>
            <el-option key="2" label="禁用" value="1"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="角色" prop="userRoles">
          <el-select v-model="dataForm.role_ids" multiple placeholder="请选择" style="width: 100%;">
            <el-option v-for="item in roles" :key="item.id" :label="item.remark" :value="item.id"></el-option>
          </el-select>
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
import { getRoles } from "@/api/role";
import PermsButton from "@/components/PermsButton";
export default {
  // name: "user",
  data() {
    return {
      query: {
        page: 1,
        size: 10
      },
      operation: false, // true:新增, false:编辑
      editVisible: false,
      dataForm: {},
      roles: {},
      formRules: {
        name: [
          { required: true, message: "请输入用户名", trigger: "blur" },
          { min: 3, max: 20, message: "长度在 3 到 20 个字符", trigger: "blur" }
        ],
        password: [
          { required: false, message: "请输入密码", trigger: "blur" },
          {
            min: 6,
            max: 11,
            message: "请输入密码",
            trigger: "blur"
          }
        ],
        email: [
          { required: true, message: "请输入邮箱地址", trigger: "blur" },
          {
            type: "email",
            message: "请输入正确的邮箱地址",
            trigger: ["blur", "change"]
          }
        ],
        mobile: [
          {
            required: true,
            message: "请输入手机号",
            trigger: "blur"
          },
          {
            min: 11,
            max: 11,
            message: "请输入正确的手机号",
            trigger: "blur"
          },
          {
            pattern: /^(13[0-9]|14[579]|15[0-3,5-9]|16[6]|17[0135678]|18[0-9]|19[89])\d{8}$/,
            message: "请输入正确的手机号码"
          }
        ]
      },

      multipleSelection: []
    };
  },
  components: {
    PermsButton: PermsButton
  },
  computed: {
    ...mapGetters("user", { users: "users", totalRows: "total_rows" })
  },
  created() {
    this.getData();
  },
  methods: {
    getData() {
      this.$store
        .dispatch("user/getUsers", this.query)
        .then(res => {})
        .catch(err => {});
      getRoles()
        .then(res => {
          const { data } = res;
          this.roles = data.roles;
        })
        .catch(error => {});
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
          return this.$store.dispatch("user/del", { id: row.id });
        })
        .then(res => {
          this.$message.success("删除成功");
          // this.users.splice(index, 1);
        })
        .catch(() => {});
    },
    handleAdd() {
      this.dataForm = {
        id: "",
        name: "",
        password: "",
        email: "",
        mobile: "",
        status: "0",
        role_ids: []
      };
      this.editVisible = true;
      this.operation = true;
    },
    handleEdit(index, row) {
      this.dataForm = { ...row };
      this.editVisible = true;
      this.operation = false;
    },
    // 多选操作
    handleSelectionChange(val) {
      this.multipleSelection = val;
    },
    handleBatchDel() {
      const length = this.multipleSelection.length;
      let delIds = [];

      for (let i = 0; i < length; i++) {
        let id = this.multipleSelection[i].id;
        delIds.push(id);
      }

      this.multipleSelection = [];
      this.$store
        .dispatch("user/batchDel", delIds)
        .then(res => {
          this.$message.success("操作成功！");
        })
        .catch(error => {
          this.$message.error("操作失败！");
        });
    },
    submitForm() {
      this.editVisible = false;
      this.$refs.dataForm.validate(valid => {
        if (valid) {
          this.$store
            .dispatch("user/save", this.dataForm)
            .then(res => {
              this.dataForm.password = "";
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
</style>
