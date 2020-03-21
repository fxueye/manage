
  
  <template>
  <fragment>
    <template v-for="item in items">
      <template v-if="item.subs && item.subs.length > 0">
        <el-submenu :index="item.index" :key="item.index">
          <template slot="title">
            <i v-if="item.icon !=''" :class="item.icon"></i>
            <span slot="title">{{ item.title }}</span>
          </template>
          <MenuTree :items="item.subs"></MenuTree>
        </el-submenu>
      </template>
      <template v-else>
        <el-menu-item :index="item.index" :key="item.index">
          <i v-if="item.icon !=''" :class="item.icon"></i>
          <span slot="title">{{ item.title }}</span>
        </el-menu-item>
      </template>
    </template>
  </fragment>
</template>

<script>
export default {
  mounted() {
    console.log(this.items);
  },
  props: ["items"],
  name: "MenuTree"
};
</script>