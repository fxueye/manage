<template>
  <div>
    <div class="crumbs">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item>
          <i class="el-icon-lx-cascades"></i> 菜单管理
        </el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="container">
      <div class="handle-box">
        <el-button type="primary" class="mr10" @click="handleAdd">添加菜单</el-button>

        <!-- <el-input v-model="query.name" placeholder="名称" class="handle-input mr10"></el-input>
        <el-button type="primary" icon="el-icon-search" @click="handleSearch">搜索</el-button>-->
      </div>
      <el-table
        row-key="id"
        :tree-props="{ children: 'children', hasChildren: 'hasChildren' }"
        :data="menus"
        border
        @selection-change="handleSelectionChange"
      >
        <el-table-column prop="name" header-align="center" width="120" align="center" label="名称"></el-table-column>
        <el-table-column label="图标" header-align="center" align="center">
          <template slot-scope="scope">
            <i :class="scope.row.icon || ''"></i>
          </template>
        </el-table-column>
        <el-table-column prop="type" label="类型" header-align="center" align="center">
          <template slot-scope="scope">
            <el-tag v-if="scope.row.type === '0'" size="small">目录</el-tag>
            <el-tag v-else-if="scope.row.type === '1'" size="small" type="success">菜单</el-tag>
            <el-tag v-else-if="scope.row.type === '2'" size="small" type="info">按钮</el-tag>
          </template>
        </el-table-column>
        <el-table-column
          prop="parent_name"
          header-align="center"
          align="center"
          width="120"
          label="上级菜单"
        ></el-table-column>
        <el-table-column
          prop="url"
          header-align="center"
          align="center"
          width="150"
          show-overflow-tooltip
          label="菜单路由"
        ></el-table-column>
        <el-table-column
          prop="perms"
          header-align="center"
          align="center"
          width="150"
          :show-overflow-tooltip="true"
          label="授权标识"
        ></el-table-column>
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
    </div>

    <!-- 编辑弹出框 -->
    <el-dialog :title="operation ? '新增' : '编辑'" :visible.sync="editVisible" width="30%">
      <el-form ref="dataForm" :rules="formRules" :model="dataForm" label-width="100px">
        <el-form-item label="菜单类型" prop="type">
          <el-radio-group v-model="dataForm.type">
            <el-radio
              v-for="(type, index) in menuTypeList"
              :label="'' + index"
              :key="'' + index"
            >{{ type }}</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item :label="menuTypeList[dataForm.type] + '名称'" prop="name">
          <el-input v-model="dataForm.name"></el-input>
        </el-form-item>

        <el-form-item label="菜单图标" v-if="dataForm.type === '0'" prop="icon">
          <el-input v-model="dataForm.icon"></el-input>
        </el-form-item>
        <el-form-item label="排序编号" v-if="dataForm.type !== '2'" prop="sort">
          <el-input v-model="dataForm.sort" :min="0"></el-input>
        </el-form-item>
        <el-form-item v-if="dataForm.type == '1'" label="菜单路由" prop="url">
          <el-input v-model="dataForm.url"></el-input>
        </el-form-item>
        <el-form-item
          v-if="dataForm.type === '2'"
          label="权限标识"
          prop="perms"
          placeholder="如: sys:user:add, sys:user:edit, sys:user:delete"
        >
          <el-input v-model="dataForm.perms"></el-input>
        </el-form-item>
        <el-form-item label="上级菜单" prop="parent_name">
          <popup-tree-input
            :data="popupTreeData"
            :props="popupTreeProps"
            :prop="
              dataForm.parent_name == null || dataForm.parent_name == ''
                ? '顶级菜单'
                : dataForm.parent_name
            "
            :nodeKey="dataForm.parent_id"
            :currentChangeHandle="handleTreeSelectChange"
          ></popup-tree-input>
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
import PopupTreeInput from "@/components/PopupTreeInput";
export default {
  // name: "",
  components: {
    PopupTreeInput: PopupTreeInput
  },
  data() {
    return {
      query: {
        page: 1,
        size: 10
      },
      menuTypeList: ["目录", "菜单", "按钮"],
      operation: false, // true:新增, false:编辑
      editVisible: false,
      dataForm: {},
      formRules: {
        name: [
          { required: true, message: "请输入名称", trigger: "blur" },
          { min: 2, max: 20, message: "长度在 2 到 20 个字符", trigger: "blur" }
        ]
      },

      multipleSelection: [],
      popupTreeData: [],
      popupTreeProps: {
        label: "name",
        children: "children"
      }
    };
  },
  mounted() {},
  computed: {
    ...mapGetters("menu", { menus: "menus" })
  },
  watch: {
    menus: function(val) {
      this.generatePopupTree();
    }
  },
  created() {
    this.getData();
  },
  methods: {
    getData() {
      this.$store
        .dispatch("menu/getMenus")
        .then(res => {})
        .catch(err => {});
    },
    generatePopupTree() {
      this.popupTreeData = [
        {
          parent_id: "0",
          name: "顶级菜单",
          children: this.menus
        }
      ];
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
          return this.$store.dispatch("menu/del", { id: row.id });
        })
        .then(res => {
          this.$message.success("删除成功");
          this.users.splice(index, 1);
        })
        .catch(() => {});
    },
    handleAdd() {
      this.dataForm = {
        id: "",
        icon: "",
        name: "",
        parent_id: "0",
        perms: "",
        sort: "0",
        type: "0",
        url: ""
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
    handleTreeSelectChange(data, node) {
      this.dataForm.parent_id = data.id;
      this.dataForm.parent_name = data.name;
    },
    submitForm() {
      this.editVisible = false;
      this.$refs.dataForm.validate(valid => {
        if (valid) {
          let data = { ...this.dataForm };
          delete data["parent_name"];
          this.$store
            .dispatch("menu/save", data)
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
