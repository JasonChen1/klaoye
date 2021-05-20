<template>
  <div class="container err-wp">
    <div class="error-page">
      <div class="content-con">
        <!-- <img :src="src" alt="404"> -->
        <div class="text-con">
          <h4>{{ code }}</h4>
          <h5>{{ desc }}</h5>
        </div>
        <back-btn-group class="back-btn-group"></back-btn-group>
      </div>
    </div>
  </div>
</template>

<script>
import backBtnGroup from '@errors/back-btn-group.vue'
export default {
  name: 'error_content',
  components: {
    backBtnGroup
  },
  props: {
    code: String,
    desc: String,
    // src: String
  }
}
</script>
